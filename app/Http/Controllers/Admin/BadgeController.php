<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    public function __construct(
        private BadgeService $badgeService
    ) {}

    /**
     * Rozet listesi
     */
    public function index(Request $request)
    {
        $query = Badge::query();

        // Kategori filtresi
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Nadirlik filtresi
        if ($request->filled('rarity')) {
            $query->where('rarity', $request->rarity);
        }

        // Aktiflik filtresi
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Arama
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $badges = $query->orderBy('sort_order')->paginate(20);

        return view('admin.badges.index', compact('badges'));
    }

    /**
     * Yeni rozet formu
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Rozet kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:badges,slug',
            'description' => 'required|string|max:500',
            'icon' => 'required|string|max:100',
            'category' => 'required|in:gameplay,social,content,special,activity,moderation',
            'rarity' => 'required|in:common,rare,epic,legendary',
            'is_hidden' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'xp_required' => 'nullable|integer|min:0',
        ]);

        // Slug otomatik oluştur
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Badge::create($validated);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Rozet başarıyla oluşturuldu!');
    }

    /**
     * Rozet düzenleme formu
     */
    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    /**
     * Rozet güncelle
     */
    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:badges,slug,' . $badge->id,
            'description' => 'required|string|max:500',
            'icon' => 'required|string|max:100',
            'category' => 'required|in:gameplay,social,content,special,activity,moderation',
            'rarity' => 'required|in:common,rare,epic,legendary',
            'is_hidden' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'xp_required' => 'nullable|integer|min:0',
        ]);

        $badge->update($validated);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Rozet başarıyla güncellendi!');
    }

    /**
     * Rozet sil
     */
    public function destroy(Badge $badge)
    {
        $badge->delete();

        return redirect()->route('admin.badges.index')
            ->with('success', 'Rozet başarıyla silindi!');
    }

    /**
     * Rozet aktif/pasif
     */
    public function toggleActive(Badge $badge)
    {
        $badge->update(['is_active' => !$badge->is_active]);

        return back()->with('success', 'Rozet durumu güncellendi!');
    }

    /**
     * Kullanıcıya rozet ver
     */
    public function assignToUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'badge_slug' => 'required|exists:badges,slug',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $badge = $this->badgeService->unlockBadge($user, $validated['badge_slug']);

        if ($badge) {
            return back()->with('success', "Rozet '{$badge->name}' kullanıcıya verildi!");
        }

        return back()->with('error', 'Rozet zaten kullanıcıda veya bulunamadı!');
    }

    /**
     * Kullanıcıdan rozet al
     */
    public function removeFromUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'badge_id' => 'required|exists:badges,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->badges()->detach($validated['badge_id']);

        return back()->with('success', 'Rozet kullanıcıdan alındı!');
    }

    /**
     * Rozet istatistikleri
     */
    public function stats()
    {
        $totalBadges = Badge::count();
        $activeBadges = Badge::where('is_active', true)->count();
        $hiddenBadges = Badge::where('is_hidden', true)->count();

        $byCategory = Badge::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        $byRarity = Badge::selectRaw('rarity, COUNT(*) as count')
            ->groupBy('rarity')
            ->get();

        $mostUnlocked = Badge::withCount(['users' => function($query) {
            $query->wherePivot('unlocked_at', '!=', null);
        }])
        ->orderBy('users_count', 'desc')
        ->limit(10)
        ->get();

        $leastUnlocked = Badge::withCount(['users' => function($query) {
            $query->wherePivot('unlocked_at', '!=', null);
        }])
        ->where('is_active', true)
        ->orderBy('users_count', 'asc')
        ->limit(10)
        ->get();

        return view('admin.badges.stats', compact(
            'totalBadges',
            'activeBadges',
            'hiddenBadges',
            'byCategory',
            'byRarity',
            'mostUnlocked',
            'leastUnlocked'
        ));
    }
}
