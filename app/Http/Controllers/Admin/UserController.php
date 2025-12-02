<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

/**
 * Admin User Controller
 * Kullanıcı yönetimi
 */
class UserController extends Controller
{
    /**
     * Kullanıcı listesi
     * GET /admin/users
     */
    public function index(Request $request)
    {
        $query = User::with(['profile', 'adminRoles']);

        // ============================================
        // GELİŞMİŞ FİLTRELEME
        // ============================================

        // Durum filtresi (aktif, banlı, dondurulmuş, silinmiş)
        if ($request->filled('status')) {
            if ($request->status === 'deleted') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Admin/Rol filtresi
        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'user') {
                $query->where('is_admin', false);
            } else {
                // Belirli bir admin rolü
                $query->whereHas('adminRoles', function($q) use ($request) {
                    $q->where('slug', $request->role);
                });
            }
        }

        // Email doğrulama durumu
        if ($request->filled('email_verified')) {
            if ($request->email_verified === 'verified') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Kayıt tarihi aralığı
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        // Son giriş tarihi aralığı
        if ($request->filled('last_login_from')) {
            $query->whereDate('last_login_at', '>=', $request->last_login_from);
        }
        if ($request->filled('last_login_to')) {
            $query->whereDate('last_login_at', '<=', $request->last_login_to);
        }

        // XP aralığı
        if ($request->filled('xp_min')) {
            $query->where('xp_total', '>=', $request->xp_min);
        }
        if ($request->filled('xp_max')) {
            $query->where('xp_total', '<=', $request->xp_max);
        }

        // Şehir filtresi
        if ($request->filled('city')) {
            $query->whereHas('profile', function($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        // Oyun tercihi filtresi
        if ($request->filled('game_id')) {
            $query->whereHas('profile', function($q) use ($request) {
                $q->where('game_id', $request->game_id);
            });
        }

        // Arama (isim, email, PUBG ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function($subQ) use ($search) {
                      $subQ->where('pubg_id', 'like', "%{$search}%");
                  });
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'xp') {
            $query->orderBy('xp_total', $sortOrder);
        } elseif ($sortBy === 'last_login') {
            $query->orderBy('last_login_at', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Sayfalama
        $perPage = $request->get('per_page', 20);
        $users = $query->paginate($perPage)->withQueryString();

        // ============================================
        // GELİŞMİŞ İSTATİSTİKLER
        // ============================================
        $stats = $this->calculateStatistics($request);

        // Filtre seçenekleri için veri
        $games = Game::orderBy('name')->get();
        $cities = DB::table('profiles')
            ->select('city')
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('admin.users.index', compact('users', 'stats', 'games', 'cities'));
    }

    /**
     * İstatistikleri hesapla
     * 
     * @param Request $request
     * @return array
     */
    private function calculateStatistics(Request $request): array
    {
        // Temel istatistikler
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'banned' => User::where('status', 'banned')->count(),
            'frozen' => User::where('status', 'frozen')->count(),
            'deleted' => User::onlyTrashed()->count(),
            'admins' => User::where('is_admin', true)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
        ];

        // Zaman bazlı istatistikler
        $stats['today'] = User::whereDate('created_at', today())->count();
        $stats['yesterday'] = User::whereDate('created_at', today()->subDay())->count();
        $stats['this_week'] = User::whereBetween('created_at', [
            now()->startOfWeek(), 
            now()->endOfWeek()
        ])->count();
        $stats['last_week'] = User::whereBetween('created_at', [
            now()->subWeek()->startOfWeek(), 
            now()->subWeek()->endOfWeek()
        ])->count();
        $stats['this_month'] = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $stats['last_month'] = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        // Aktivite istatistikleri
        $stats['active_today'] = User::whereDate('last_login_at', today())->count();
        $stats['active_this_week'] = User::whereBetween('last_login_at', [
            now()->startOfWeek(), 
            now()->endOfWeek()
        ])->count();
        $stats['active_this_month'] = User::whereMonth('last_login_at', now()->month)
            ->whereYear('last_login_at', now()->year)
            ->count();

        // XP istatistikleri
        $stats['avg_xp'] = round(User::avg('xp_total') ?? 0);
        $stats['total_xp'] = User::sum('xp_total') ?? 0;
        $stats['max_xp'] = User::max('xp_total') ?? 0;

        // Büyüme oranları
        if ($stats['last_week'] > 0) {
            $stats['weekly_growth'] = round((($stats['this_week'] - $stats['last_week']) / $stats['last_week']) * 100, 2);
        } else {
            $stats['weekly_growth'] = $stats['this_week'] > 0 ? 100 : 0;
        }

        if ($stats['last_month'] > 0) {
            $stats['monthly_growth'] = round((($stats['this_month'] - $stats['last_month']) / $stats['last_month']) * 100, 2);
        } else {
            $stats['monthly_growth'] = $stats['this_month'] > 0 ? 100 : 0;
        }

        return $stats;
    }

    /**
     * Kullanıcı detayı
     * GET /admin/users/{id}
     */
    public function show($id)
    {
        $user = User::with(['profile', 'device', 'lfgPosts', 'ownedClans', 'guidePosts', 'communityPosts'])
            ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Kullanıcı düzenleme formu
     * GET /admin/users/{id}/edit
     */
    public function edit($id)
    {
        $user = User::with(['profile', 'badges', 'adminNotes.admin', 'adminRoles'])->findOrFail($id);
        
        // Tüm rolleri getir
        $roles = \App\Models\AdminRole::orderBy('name')->get();
        
        // Tüm rozetleri getir
        $allBadges = \App\Models\Badge::orderBy('name')->get();
        
        // Kullanıcının sahip olduğu rozet ID'lerini al
        $userBadgeIds = $user->badges->pluck('id')->toArray();
        
        return view('admin.users.edit', compact('user', 'roles', 'allBadges', 'userBadgeIds'));
    }

    /**
     * Kullanıcı güncelleme
     * PUT /admin/users/{id}
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'status' => 'required|in:active,banned,frozen',
            'is_admin' => 'boolean',
            'xp_total' => 'nullable|integer|min:0',
            'password' => 'nullable|string|min:8',
            // Admin rolleri
            'roles' => 'nullable|array',
            'roles.*' => 'exists:admin_roles,id',
            // Profile fields
            'pubg_id' => 'nullable|string|max:255',
            'rank' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            // XP değişikliği
            'xp_change_type' => 'nullable|in:add,remove',
            'xp_change_amount' => 'nullable|integer|min:1',
            'xp_change_reason' => 'nullable|string|max:500',
            // Rozetler
            'badges' => 'nullable|array',
            'badges.*' => 'exists:badges,id',
            // Admin notu
            'admin_note' => 'nullable|string|max:1000',
            'note_type' => 'nullable|in:info,warning,important',
        ]);

        DB::beginTransaction();
        try {
            $oldXp = $user->xp_total;

            // Kullanıcı bilgilerini güncelle
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'status' => $validated['status'],
                'is_admin' => $request->boolean('is_admin'),
                'xp_total' => $validated['xp_total'] ?? $user->xp_total,
            ];
            
            // Şifre varsa ekle
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($validated['password']);
            }
            
            $user->update($updateData);
            
            // Admin rolleri güncelle
            if ($request->boolean('is_admin') && $request->has('roles')) {
                $user->adminRoles()->sync($validated['roles'] ?? []);
            } elseif (!$request->boolean('is_admin')) {
                // Admin değilse tüm rolleri kaldır
                $user->adminRoles()->detach();
            }

            // Profil bilgilerini güncelle
            if ($user->profile) {
                $user->profile->update([
                    'pubg_id' => $validated['pubg_id'] ?? $user->profile->pubg_id,
                    'rank' => $validated['rank'] ?? $user->profile->rank,
                    'bio' => $validated['bio'] ?? $user->profile->bio,
                ]);
            }

            // XP değişikliği
            if ($request->filled('xp_change_type') && $request->filled('xp_change_amount')) {
                $this->handleXpChange(
                    $user, 
                    $validated['xp_change_type'], 
                    $validated['xp_change_amount'], 
                    $validated['xp_change_reason'] ?? null
                );
            }

            // Şifre sıfırlama
            if ($request->filled('new_password')) {
                $user->update([
                    'password' => bcrypt($validated['new_password'])
                ]);

                \App\Models\AdminActivityLog::create([
                    'admin_id' => auth()->id(),
                    'action' => 'reset_password',
                    'target_type' => 'User',
                    'target_id' => $user->id,
                    'details' => "Kullanıcının şifresi sıfırlandı: {$user->name}",
                ]);
            }

            // Rozetleri güncelle
            if ($request->has('badges')) {
                $this->updateUserBadges($user, $validated['badges'] ?? []);
            }

            // Admin notu ekle
            if ($request->filled('admin_note')) {
                \App\Models\AdminNote::create([
                    'user_id' => $user->id,
                    'admin_id' => auth()->id(),
                    'note' => $validated['admin_note'],
                    'type' => $validated['note_type'] ?? 'info',
                ]);
            }

            // Admin activity log
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'update_user',
                'target_type' => 'User',
                'target_id' => $user->id,
                'details' => "Kullanıcı güncellendi: {$user->name}",
            ]);

            DB::commit();

            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'Kullanıcı başarıyla güncellendi');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Güncelleme sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * XP değişikliğini işle
     */
    private function handleXpChange(User $user, string $type, int $amount, ?string $reason): void
    {
        if ($type === 'add') {
            $user->increment('xp_total', $amount);
            
            \App\Models\XpEvent::create([
                'user_id' => $user->id,
                'type' => 'admin_bonus',
                'amount' => $amount,
                'description' => $reason ?? 'Admin tarafından manuel XP eklendi',
            ]);

            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'add_xp',
                'target_type' => 'User',
                'target_id' => $user->id,
                'details' => "+{$amount} XP eklendi" . ($reason ? ": {$reason}" : ''),
            ]);
        } else {
            $newXp = max(0, $user->xp_total - $amount);
            $actualRemoved = $user->xp_total - $newXp;
            
            $user->update(['xp_total' => $newXp]);

            \App\Models\XpEvent::create([
                'user_id' => $user->id,
                'type' => 'admin_penalty',
                'amount' => -$actualRemoved,
                'description' => $reason ?? 'Admin tarafından manuel XP çıkarıldı',
            ]);

            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'remove_xp',
                'target_type' => 'User',
                'target_id' => $user->id,
                'details' => "-{$actualRemoved} XP çıkarıldı" . ($reason ? ": {$reason}" : ''),
            ]);
        }
    }

    /**
     * Kullanıcının rozetlerini güncelle
     */
    private function updateUserBadges(User $user, array $badgeIds): void
    {
        $oldBadgeIds = $user->badges->pluck('id')->toArray();
        
        // Yeni eklenen rozetler
        $addedBadges = array_diff($badgeIds, $oldBadgeIds);
        
        // Kaldırılan rozetler
        $removedBadges = array_diff($oldBadgeIds, $badgeIds);

        // Rozetleri senkronize et
        $syncData = [];
        foreach ($badgeIds as $badgeId) {
            $syncData[$badgeId] = ['unlocked_at' => now()];
        }
        $user->badges()->sync($syncData);

        // Eklenen rozetleri logla
        if (!empty($addedBadges)) {
            $badgeNames = \App\Models\Badge::whereIn('id', $addedBadges)->pluck('name')->implode(', ');
            
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'add_badges',
                'target_type' => 'User',
                'target_id' => $user->id,
                'details' => "Rozetler eklendi: {$badgeNames}",
            ]);
        }

        // Kaldırılan rozetleri logla
        if (!empty($removedBadges)) {
            $badgeNames = \App\Models\Badge::whereIn('id', $removedBadges)->pluck('name')->implode(', ');
            
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'remove_badges',
                'target_type' => 'User',
                'target_id' => $user->id,
                'details' => "Rozetler kaldırıldı: {$badgeNames}",
            ]);
        }
    }

    /**
     * Kullanıcıyı banla
     * POST /admin/users/{id}/ban
     */
    public function ban(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Kendini banlayamaz
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kendinizi banlayamazsınız');
        }

        $user->update(['status' => 'banned']);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'ban_user',
            'target_type' => 'User',
            'target_id' => $user->id,
            'details' => "Kullanıcı banlandı: {$user->name}",
        ]);

        return back()->with('success', 'Kullanıcı başarıyla banlandı');
    }

    /**
     * Kullanıcının banını kaldır
     * POST /admin/users/{id}/unban
     */
    public function unban($id)
    {
        $user = User::findOrFail($id);

        $user->update(['status' => 'active']);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'unban_user',
            'target_type' => 'User',
            'target_id' => $user->id,
            'details' => "Kullanıcının banı kaldırıldı: {$user->name}",
        ]);

        return back()->with('success', 'Kullanıcının banı kaldırıldı');
    }

    /**
     * Kullanıcıyı admin yap
     * POST /admin/users/{id}/make-admin
     */
    public function makeAdmin($id)
    {
        $user = User::findOrFail($id);

        $user->update(['is_admin' => true]);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'make_admin',
            'target_type' => 'User',
            'target_id' => $user->id,
            'details' => "Kullanıcı admin yapıldı: {$user->name}",
        ]);

        return back()->with('success', 'Kullanıcı admin yapıldı');
    }

    /**
     * Kullanıcının admin yetkisini kaldır
     * POST /admin/users/{id}/remove-admin
     */
    public function removeAdmin($id)
    {
        $user = User::findOrFail($id);

        // Kendinin admin yetkisini kaldıramaz
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kendi admin yetkinizi kaldıramazsınız');
        }

        $user->update(['is_admin' => false]);

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'remove_admin',
            'target_type' => 'User',
            'target_id' => $user->id,
            'details' => "Kullanıcının admin yetkisi kaldırıldı: {$user->name}",
        ]);

        return back()->with('success', 'Kullanıcının admin yetkisi kaldırıldı');
    }

    /**
     * Kullanıcıyı sil
     * DELETE /admin/users/{id}
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Kendini silemez
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kendinizi silemezsiniz');
        }

        $userName = $user->name;
        $user->delete();

        // Admin activity log
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_user',
            'target_type' => 'User',
            'target_id' => $id,
            'details' => "Kullanıcı silindi: {$userName}",
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı silindi');
    }

    // ============================================
    // TOPLU İŞLEMLER (BULK ACTIONS)
    // ============================================

    /**
     * Toplu işlem
     * POST /admin/users/bulk-action
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:ban,unban,delete,add_xp,remove_xp,send_email',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'value' => 'nullable|integer', // XP miktarı için
            'reason' => 'nullable|string|max:500', // Ban/XP değişikliği sebebi
            'email_subject' => 'nullable|required_if:action,send_email|string|max:255',
            'email_message' => 'nullable|required_if:action,send_email|string',
        ]);

        $userIds = $validated['user_ids'];
        $action = $validated['action'];

        // Kendini toplu işleme dahil edemez
        if (in_array(auth()->id(), $userIds)) {
            return back()->with('error', 'Kendinize toplu işlem uygulayamazsınız');
        }

        $affectedCount = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            switch ($action) {
                case 'ban':
                    $affectedCount = $this->bulkBan($userIds, $validated['reason'] ?? null);
                    break;

                case 'unban':
                    $affectedCount = $this->bulkUnban($userIds);
                    break;

                case 'delete':
                    $affectedCount = $this->bulkDelete($userIds);
                    break;

                case 'add_xp':
                    $affectedCount = $this->bulkAddXp($userIds, $validated['value'] ?? 0, $validated['reason'] ?? null);
                    break;

                case 'remove_xp':
                    $affectedCount = $this->bulkRemoveXp($userIds, $validated['value'] ?? 0, $validated['reason'] ?? null);
                    break;

                case 'send_email':
                    $affectedCount = $this->bulkSendEmail(
                        $userIds, 
                        $validated['email_subject'], 
                        $validated['email_message']
                    );
                    break;
            }

            DB::commit();

            return back()->with('success', "{$affectedCount} kullanıcıya işlem uygulandı");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Toplu işlem sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Toplu ban işlemi
     */
    private function bulkBan(array $userIds, ?string $reason): int
    {
        $count = User::whereIn('id', $userIds)
            ->where('status', '!=', 'banned')
            ->update(['status' => 'banned']);

        // Log kaydet
        foreach ($userIds as $userId) {
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'bulk_ban_user',
                'target_type' => 'User',
                'target_id' => $userId,
                'details' => "Toplu ban işlemi" . ($reason ? ": {$reason}" : ''),
            ]);
        }

        return $count;
    }

    /**
     * Toplu ban kaldırma işlemi
     */
    private function bulkUnban(array $userIds): int
    {
        $count = User::whereIn('id', $userIds)
            ->where('status', 'banned')
            ->update(['status' => 'active']);

        // Log kaydet
        foreach ($userIds as $userId) {
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'bulk_unban_user',
                'target_type' => 'User',
                'target_id' => $userId,
                'details' => 'Toplu ban kaldırma işlemi',
            ]);
        }

        return $count;
    }

    /**
     * Toplu silme işlemi
     */
    private function bulkDelete(array $userIds): int
    {
        $users = User::whereIn('id', $userIds)->get();
        $count = 0;

        foreach ($users as $user) {
            $user->delete();
            $count++;

            // Log kaydet
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'bulk_delete_user',
                'target_type' => 'User',
                'target_id' => $user->id,
                'details' => "Toplu silme işlemi: {$user->name}",
            ]);
        }

        return $count;
    }

    /**
     * Toplu XP ekleme işlemi
     */
    private function bulkAddXp(array $userIds, int $amount, ?string $reason): int
    {
        if ($amount <= 0) {
            return 0;
        }

        $users = User::whereIn('id', $userIds)->get();
        $count = 0;

        foreach ($users as $user) {
            $user->increment('xp_total', $amount);
            $count++;

            // XP event kaydet
            \App\Models\XpEvent::create([
                'user_id' => $user->id,
                'type' => 'admin_bonus',
                'amount' => $amount,
                'description' => $reason ?? 'Admin tarafından manuel XP eklendi',
            ]);

            // Log kaydet
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'bulk_add_xp',
                'target_type' => 'User',
                'target_id' => $user->id,
                'details' => "Toplu XP ekleme: +{$amount} XP" . ($reason ? " - {$reason}" : ''),
            ]);
        }

        return $count;
    }

    /**
     * Toplu XP çıkarma işlemi
     */
    private function bulkRemoveXp(array $userIds, int $amount, ?string $reason): int
    {
        if ($amount <= 0) {
            return 0;
        }

        $users = User::whereIn('id', $userIds)->get();
        $count = 0;

        foreach ($users as $user) {
            $newXp = max(0, $user->xp_total - $amount);
            $actualRemoved = $user->xp_total - $newXp;
            
            $user->update(['xp_total' => $newXp]);
            $count++;

            // XP event kaydet
            \App\Models\XpEvent::create([
                'user_id' => $user->id,
                'type' => 'admin_penalty',
                'amount' => -$actualRemoved,
                'description' => $reason ?? 'Admin tarafından manuel XP çıkarıldı',
            ]);

            // Log kaydet
            \App\Models\AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'bulk_remove_xp',
                'target_type' => 'User',
                'target_id' => $user->id,
                'details' => "Toplu XP çıkarma: -{$actualRemoved} XP" . ($reason ? " - {$reason}" : ''),
            ]);
        }

        return $count;
    }

    /**
     * Toplu email gönderme işlemi
     */
    private function bulkSendEmail(array $userIds, string $subject, string $message): int
    {
        $users = User::whereIn('id', $userIds)->get();
        $count = 0;

        foreach ($users as $user) {
            try {
                // Email gönder (Laravel Mail kullanarak)
                \Illuminate\Support\Facades\Mail::raw($message, function ($mail) use ($user, $subject) {
                    $mail->to($user->email)
                         ->subject($subject);
                });

                $count++;

                // Log kaydet
                \App\Models\AdminActivityLog::create([
                    'admin_id' => auth()->id(),
                    'action' => 'bulk_send_email',
                    'target_type' => 'User',
                    'target_id' => $user->id,
                    'details' => "Toplu email gönderimi: {$subject}",
                ]);
            } catch (\Exception $e) {
                // Email gönderimi başarısız, devam et
                continue;
            }
        }

        return $count;
    }

    // ============================================
    // EXPORT İŞLEMLERİ
    // ============================================

    /**
     * Kullanıcıları CSV olarak export et
     * GET /admin/users/export/csv
     */
    public function exportCsv(Request $request)
    {
        // Aynı filtreleri uygula
        $query = $this->applyFilters(User::with(['profile', 'adminRoles']), $request);
        
        $users = $query->get();

        $filename = 'kullanicilar_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM ekle (Excel için)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Başlıklar
            fputcsv($file, [
                'ID',
                'İsim',
                'Email',
                'Durum',
                'Admin',
                'Email Doğrulandı',
                'PUBG ID',
                'Rank',
                'Şehir',
                'XP',
                'Kayıt Tarihi',
                'Son Giriş',
            ]);

            // Veriler
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->status,
                    $user->is_admin ? 'Evet' : 'Hayır',
                    $user->email_verified_at ? 'Evet' : 'Hayır',
                    $user->profile->pubg_id ?? '',
                    $user->profile->rank ?? '',
                    $user->profile->city ?? '',
                    $user->xp_total ?? 0,
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        // Log kaydet
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'export_users_csv',
            'target_type' => 'User',
            'details' => "Kullanıcılar CSV olarak export edildi ({$users->count()} kayıt)",
        ]);

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Kullanıcıları Excel olarak export et
     * GET /admin/users/export/excel
     */
    public function exportExcel(Request $request)
    {
        // Aynı filtreleri uygula
        $query = $this->applyFilters(User::with(['profile', 'adminRoles']), $request);
        
        $users = $query->get();

        $filename = 'kullanicilar_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Excel export için basit bir CSV oluştur (gerçek Excel için PhpSpreadsheet kullanılabilir)
        // Şimdilik CSV formatında döndürüyoruz
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM ekle
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Başlıklar
            fputcsv($file, [
                'ID',
                'İsim',
                'Email',
                'Durum',
                'Admin',
                'Roller',
                'Email Doğrulandı',
                'PUBG ID',
                'Rank',
                'Şehir',
                'Bio',
                'XP',
                'Kayıt Tarihi',
                'Son Giriş',
                'Silindi',
            ]);

            // Veriler
            foreach ($users as $user) {
                $roles = $user->adminRoles->pluck('name')->implode(', ');
                
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->status,
                    $user->is_admin ? 'Evet' : 'Hayır',
                    $roles ?: 'Yok',
                    $user->email_verified_at ? 'Evet' : 'Hayır',
                    $user->profile->pubg_id ?? '',
                    $user->profile->rank ?? '',
                    $user->profile->city ?? '',
                    $user->profile->bio ?? '',
                    $user->xp_total ?? 0,
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : '',
                    $user->deleted_at ? 'Evet' : 'Hayır',
                ]);
            }

            fclose($file);
        };

        // Log kaydet
        \App\Models\AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'export_users_excel',
            'target_type' => 'User',
            'details' => "Kullanıcılar Excel olarak export edildi ({$users->count()} kayıt)",
        ]);

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Filtreleri query'ye uygula (export için)
     */
    private function applyFilters($query, Request $request)
    {
        // Durum filtresi
        if ($request->filled('status')) {
            if ($request->status === 'deleted') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Admin/Rol filtresi
        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'user') {
                $query->where('is_admin', false);
            } else {
                $query->whereHas('adminRoles', function($q) use ($request) {
                    $q->where('slug', $request->role);
                });
            }
        }

        // Email doğrulama durumu
        if ($request->filled('email_verified')) {
            if ($request->email_verified === 'verified') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Tarih aralıkları
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }
        if ($request->filled('last_login_from')) {
            $query->whereDate('last_login_at', '>=', $request->last_login_from);
        }
        if ($request->filled('last_login_to')) {
            $query->whereDate('last_login_at', '<=', $request->last_login_to);
        }

        // XP aralığı
        if ($request->filled('xp_min')) {
            $query->where('xp_total', '>=', $request->xp_min);
        }
        if ($request->filled('xp_max')) {
            $query->where('xp_total', '<=', $request->xp_max);
        }

        // Şehir ve oyun
        if ($request->filled('city')) {
            $query->whereHas('profile', function($q) use ($request) {
                $q->where('city', $request->city);
            });
        }
        if ($request->filled('game_id')) {
            $query->whereHas('profile', function($q) use ($request) {
                $q->where('game_id', $request->game_id);
            });
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function($subQ) use ($search) {
                      $subQ->where('pubg_id', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    // ============================================
    // API ENDPOINT'LERİ (AJAX için)
    // ============================================

    /**
     * Toplu ban API endpoint
     * POST /admin/api/users/bulk-ban
     */
    public function apiBulkBan(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        // Kendini banlayamaz
        if (in_array(auth()->id(), $validated['user_ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Kendinizi banlayamazsınız'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            $count = $this->bulkBan($validated['user_ids'], $validated['reason'] ?? null);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$count} kullanıcı banlandı",
                'affected_count' => $count
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toplu email gönderimi API endpoint
     * POST /admin/api/users/bulk-email
     */
    public function apiBulkEmail(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $count = $this->bulkSendEmail(
                $validated['user_ids'],
                $validated['subject'],
                $validated['message']
            );

            return response()->json([
                'success' => true,
                'message' => "{$count} kullanıcıya email gönderildi",
                'sent_count' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email gönderimi sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toplu XP işlemi API endpoint
     * POST /admin/api/users/bulk-xp
     */
    public function apiBulkXp(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'type' => 'required|in:add,remove',
            'amount' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            if ($validated['type'] === 'add') {
                $count = $this->bulkAddXp(
                    $validated['user_ids'],
                    $validated['amount'],
                    $validated['reason'] ?? null
                );
                $message = "{$count} kullanıcıya {$validated['amount']} XP eklendi";
            } else {
                $count = $this->bulkRemoveXp(
                    $validated['user_ids'],
                    $validated['amount'],
                    $validated['reason'] ?? null
                );
                $message = "{$count} kullanıcıdan {$validated['amount']} XP çıkarıldı";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'affected_count' => $count
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'XP işlemi sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toplu silme API endpoint
     * POST /admin/api/users/bulk-delete
     */
    public function apiBulkDelete(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Kendini silemez
        if (in_array(auth()->id(), $validated['user_ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Kendinizi silemezsiniz'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            $count = $this->bulkDelete($validated['user_ids']);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$count} kullanıcı silindi",
                'deleted_count' => $count
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Silme işlemi sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export API endpoint (format parametresi ile)
     * GET /admin/api/users/export?format=csv|excel
     */
    public function apiExport(Request $request)
    {
        $format = $request->get('format', 'csv');

        if ($format === 'excel') {
            return $this->exportExcel($request);
        }

        return $this->exportCsv($request);
    }
}
