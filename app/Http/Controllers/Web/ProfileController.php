<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Web Profil Controller
 */
class ProfileController extends Controller
{
    /**
     * Profil sayfası
     * GET /profilim
     */
    public function index(Request $request)
    {
        $user = $request->user()->load([
            'profile', 
            'device',
            'badges' => function($query) {
                $query->orderBy('user_badges.unlocked_at', 'desc')->take(6);
            },
            'lfgPosts' => function($query) {
                $query->latest()->take(3);
            },
            'guidePosts' => function($query) {
                $query->latest()->take(3);
            },
            'communityPosts' => function($query) {
                $query->latest()->take(3);
            },
            'xpEvents' => function($query) {
                $query->latest()->take(5);
            },
            'matchmakingHistory' => function($query) {
                $query->latest()->take(5);
            },
            'clans',
            'squads'
        ]);

        return view('profile.index', compact('user'));
    }

    /**
     * Multi-game activity dashboard
     * GET /profilim/tum-oyunlar
     */
    public function multiGameDashboard(Request $request)
    {
        $user = $request->user();
        
        // Tüm aktif oyunları al
        $games = \App\Models\Game::active()->get();
        
        // Her oyun için kullanıcının aktivitelerini topla
        $gameActivities = [];
        
        foreach ($games as $game) {
            // Global scope'u devre dışı bırakarak her oyun için veri çek
            $activities = [
                'game' => $game,
                'tournaments' => \App\Models\Tournament::withoutGlobalScope(\App\Models\Scopes\GameScope::class)
                    ->where('game_id', $game->id)
                    ->where('organizer_id', $user->id)
                    ->count(),
                'clans' => \App\Models\Clan::withoutGlobalScope(\App\Models\Scopes\GameScope::class)
                    ->where('game_id', $game->id)
                    ->whereHas('members', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->count(),
                'lfg_posts' => \App\Models\LfgPost::withoutGlobalScope(\App\Models\Scopes\GameScope::class)
                    ->where('game_id', $game->id)
                    ->where('user_id', $user->id)
                    ->count(),
                'guide_posts' => \App\Models\GuidePost::withoutGlobalScope(\App\Models\Scopes\GameScope::class)
                    ->where('game_id', $game->id)
                    ->where('user_id', $user->id)
                    ->count(),
                'community_posts' => \App\Models\CommunityPost::withoutGlobalScope(\App\Models\Scopes\GameScope::class)
                    ->where('game_id', $game->id)
                    ->where('user_id', $user->id)
                    ->count(),
                'badges' => \App\Models\Badge::withoutGlobalScope(\App\Models\Scopes\GameScope::class)
                    ->where('game_id', $game->id)
                    ->whereHas('users', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->count(),
            ];
            
            // Toplam aktivite sayısı
            $activities['total'] = $activities['tournaments'] + 
                                  $activities['clans'] + 
                                  $activities['lfg_posts'] + 
                                  $activities['guide_posts'] + 
                                  $activities['community_posts'];
            
            $gameActivities[] = $activities;
        }
        
        // Cross-game aktiviteler (game_id olmayan)
        $crossGameActivities = [
            'messages' => \App\Models\Message::where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id)
                ->count(),
            'friendships' => \App\Models\Friendship::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('friend_id', $user->id);
            })->where('status', 'accepted')->count(),
            'notifications' => $user->notifications()->count(),
        ];
        
        // Toplam istatistikler
        $totalStats = [
            'total_games' => $games->count(),
            'active_games' => collect($gameActivities)->filter(fn($a) => $a['total'] > 0)->count(),
            'total_activities' => collect($gameActivities)->sum('total'),
            'total_badges' => collect($gameActivities)->sum('badges'),
        ];
        
        // Son aktiviteler (tüm oyunlardan)
        $recentActivities = $this->getRecentCrossGameActivities($user);
        
        return view('profile.multi-game-dashboard', compact(
            'user',
            'games',
            'gameActivities',
            'crossGameActivities',
            'totalStats',
            'recentActivities'
        ));
    }
    
    /**
     * Tüm oyunlardan son aktiviteleri getir
     */
    private function getRecentCrossGameActivities($user)
    {
        $activities = collect();
        
        // LFG Posts
        $lfgPosts = \App\Models\LfgPost::withoutGlobalScope(\App\Models\Scopes\GameScope::class)
            ->with('game')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function($post) {
                return [
                    'type' => 'lfg_post',
                    'game' => $post->game,
                    'title' => $post->title,
                    'created_at' => $post->created_at,
                    'url' => route('lfg.show', $post->id),
                ];
            });
        
        // Guide Posts
        $guidePosts = \App\Models\GuidePost::withoutGlobalScope(\App\Models\Scopes\GameScope::class)
            ->with('game')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function($post) {
                return [
                    'type' => 'guide_post',
                    'game' => $post->game,
                    'title' => $post->title,
                    'created_at' => $post->created_at,
                    'url' => route('guides.show', $post->id),
                ];
            });
        
        // Community Posts
        $communityPosts = \App\Models\CommunityPost::withoutGlobalScope(\App\Models\Scopes\GameScope::class)
            ->with('game')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function($post) {
                return [
                    'type' => 'community_post',
                    'game' => $post->game,
                    'title' => $post->title,
                    'created_at' => $post->created_at,
                    'url' => route('community.show', $post->id),
                ];
            });
        
        // Tüm aktiviteleri birleştir ve tarihe göre sırala
        return $activities
            ->concat($lfgPosts)
            ->concat($guidePosts)
            ->concat($communityPosts)
            ->sortByDesc('created_at')
            ->take(10);
    }

    /**
     * Profil düzenleme sayfası
     * GET /profilim/duzenle
     */
    public function edit(Request $request)
    {
        $user = $request->user()->load('profile');

        return view('profile.edit', compact('user'));
    }

    /**
     * Profil güncelleme
     * PUT /profilim
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'nullable|string|max:255',
            'pubg_id' => 'nullable|string|max:255',
            'rank' => 'nullable|string|in:Bronz,Gümüş,Altın,Platin,Elmas,Taç,As,Fatih',
            'server_region' => 'nullable|string|in:EU,MENA,ASIA,NA,SA',
            'city' => 'nullable|string|max:255',
            'age_range' => 'nullable|string|max:50',
            'gender' => 'nullable|string|in:male,female,other',
            'play_style' => 'nullable|string|in:agresif,savunmaci,sniper,rusher,takimci',
            'favorite_maps' => 'nullable|array',
            'favorite_maps.*' => 'string|in:Erangel,Miramar,Sanhok,Vikendi,Livik,Karakin,Nusa',
            'bio' => 'nullable|string|max:1000',
            'twitch_username' => 'nullable|string|max:255',
            'youtube_channel' => 'nullable|string|max:255',
            'discord_username' => 'nullable|string|max:255',
            'settings' => 'nullable|array',
            'settings.profile_visibility' => 'nullable|string|in:public,friends,private',
            'settings.show_email' => 'nullable|boolean',
            'settings.show_city' => 'nullable|boolean',
            'settings.show_age' => 'nullable|boolean',
            'settings.show_online_status' => 'nullable|boolean',
            'settings.allow_messages' => 'nullable|string|in:everyone,friends,none',
            'settings.allow_friend_requests' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $profile = $user->profile;
        
        // Profil yoksa oluştur
        if (!$profile) {
            $profile = $user->profile()->create([]);
        }
        
        $wasComplete = $profile->is_complete;
        
        // Profil bilgilerini güncelle
        $profileData = collect($validated)->except('settings')->toArray();
        $profile->update($profileData);
        $profile->checkCompletion();

        // Gizlilik ayarlarını güncelle
        if (isset($validated['settings'])) {
            $settings = $validated['settings'];
            
            // Checkbox'lar için false değerlerini ayarla
            $settings['show_email'] = isset($settings['show_email']) && $settings['show_email'] == '1';
            $settings['show_city'] = isset($settings['show_city']) && $settings['show_city'] == '1';
            $settings['show_age'] = isset($settings['show_age']) && $settings['show_age'] == '1';
            $settings['show_online_status'] = isset($settings['show_online_status']) && $settings['show_online_status'] == '1';
            $settings['allow_friend_requests'] = isset($settings['allow_friend_requests']) && $settings['allow_friend_requests'] == '1';
            
            // Mevcut ayarları al ve yeni ayarlarla birleştir
            $currentSettings = $user->settings ?? [];
            $user->update(['settings' => array_merge($currentSettings, $settings)]);
        }

        // Profil ilk kez tamamlandıysa ve daha önce bu XP verilmemişse kazandır
        if (!$wasComplete && $profile->is_complete) {
            // Daha önce profile_complete XP'si verilmiş mi kontrol et
            $hasProfileXp = $user->xpEvents()
                ->where('type', 'profile_complete')
                ->exists();
            
            if (!$hasProfileXp) {
                $user->addXp('profile_complete');
            }
        }

        return redirect()->route('profile.index')
            ->with('success', 'Profil ve gizlilik ayarları başarıyla güncellendi!' . (!$wasComplete && $profile->is_complete ? ' +50 XP kazandınız!' : ''));
    }

    /**
     * Avatar yükleme
     * POST /profilim/avatar
     */
    public function uploadAvatar(Request $request)
    {
        // Debug: Request'i logla
        \Log::info('=== AVATAR UPLOAD BAŞLADI ===');
        \Log::info('Has file?', ['has' => $request->hasFile('avatar')]);
        \Log::info('All files', ['files' => $request->allFiles()]);
        \Log::info('All input', ['input' => $request->all()]);
        
        try {
            // Dosya var mı kontrol
            if (!$request->hasFile('avatar')) {
                \Log::error('Dosya yok!');
                return redirect()->route('profile.edit')
                    ->with('error', '❌ Dosya bulunamadı! Lütfen tekrar deneyin.');
            }
            
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user = $request->user();
            $profile = $user->profile;

            // Profil yoksa oluştur
            if (!$profile) {
                $profile = $user->profile()->create([]);
                \Log::info('Profil oluşturuldu', ['user_id' => $user->id]);
            }

            // Eski avatar'ı sil
            if ($profile->avatar_path) {
                \Storage::disk('public')->delete($profile->avatar_path);
                \Log::info('Eski avatar silindi', ['path' => $profile->avatar_path]);
            }

            // Yeni avatar'ı kaydet
            $file = $request->file('avatar');
            \Log::info('Dosya bilgileri', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]);
            
            $path = $file->store('avatars', 'public');
            \Log::info('✅ Avatar yüklendi', ['path' => $path]);
            
            // Direkt SQL ile güncelle
            \DB::table('profiles')
                ->where('id', $profile->id)
                ->update(['avatar_path' => $path, 'updated_at' => now()]);
            
            \Log::info('✅ Avatar path güncellendi', [
                'profile_id' => $profile->id,
                'path' => $path
            ]);

            return redirect()->route('profile.edit')
                ->with('success', '✅ Profil fotoğrafı başarıyla yüklendi!');
                
        } catch (\Exception $e) {
            \Log::error('❌ Avatar upload hatası', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('profile.edit')
                ->with('error', '❌ Hata: ' . $e->getMessage());
        }
    }

    /**
     * Avatar silme
     * DELETE /profilim/avatar
     */
    public function deleteAvatar(Request $request)
    {
        $profile = $request->user()->profile;

        if ($profile->avatar_path) {
            \Storage::disk('public')->delete($profile->avatar_path);
            $profile->update(['avatar_path' => null]);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Profil fotoğrafı silindi!');
    }

    /**
     * Cihaz bilgisi düzenleme sayfası
     * GET /profilim/cihaz
     */
    public function device(Request $request)
    {
        $device = $request->user()->device;

        return view('profile.device', compact('device'));
    }

    /**
     * Cihaz bilgisi güncelleme
     * PUT /profilim/cihaz
     */
    public function updateDevice(Request $request)
    {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'graphics_settings' => 'nullable|string|max:255',
            'fps_setting' => 'nullable|string|max:50',
            'gyro_enabled' => 'boolean',
            'sensitivity_settings' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        $device = $request->user()->device;

        if ($device) {
            $device->update($validated);
            $message = 'Cihaz bilgisi güncellendi!';
        } else {
            $request->user()->device()->create($validated);
            $message = 'Cihaz bilgisi oluşturuldu! +20 XP kazandınız!';
            
            // XP kazandır
            $request->user()->addXp('device_add');
        }

        return redirect()->route('profile.device')
            ->with('success', $message);
    }

    /**
     * Public profil görüntüleme
     * GET /kullanici/{id}
     */
    public function show($id)
    {
        $user = \App\Models\User::with([
            'profile', 
            'device', 
            'badges',
            'lfgPosts' => function($query) {
                $query->latest()->take(3);
            },
            'guidePosts' => function($query) {
                $query->latest()->take(3);
            },
            'communityPosts' => function($query) {
                $query->latest()->take(3);
            }
        ])->findOrFail($id);

        // Gizlilik kontrolü
        $settings = $user->settings ?? [];
        $profileVisibility = $settings['profile_visibility'] ?? 'public';
        $currentUser = auth()->user();
        
        // Profil görünürlük kontrolü
        if ($profileVisibility === 'private' && (!$currentUser || $currentUser->id !== $user->id)) {
            abort(403, 'Bu profil özeldir.');
        }
        
        if ($profileVisibility === 'friends' && (!$currentUser || $currentUser->id !== $user->id)) {
            // Arkadaş kontrolü yap
            $isFriend = $currentUser && \App\Models\Friendship::where(function($query) use ($currentUser, $user) {
                $query->where('user_id', $currentUser->id)
                      ->where('friend_id', $user->id);
            })->orWhere(function($query) use ($currentUser, $user) {
                $query->where('user_id', $user->id)
                      ->where('friend_id', $currentUser->id);
            })->where('status', 'accepted')->exists();
            
            if (!$isFriend) {
                abort(403, 'Bu profili sadece arkadaşları görebilir.');
            }
        }

        // Görüntülenme sayısını artır (sadece başka kullanıcılar için)
        if ($user->profile && auth()->id() !== $user->id) {
            $user->profile->incrementViews();
        }

        return view('profile.show', compact('user'));
    }

    /**
     * Oyun istatistikleri düzenleme sayfası
     * GET /profilim/istatistikler
     */
    public function statistics(Request $request)
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return redirect()->route('profile.edit')
                ->with('error', 'Önce profilinizi tamamlayın!');
        }

        return view('profile.statistics', compact('profile'));
    }

    /**
     * Oyun istatistikleri güncelleme
     * PUT /profilim/istatistikler
     */
    public function updateStatistics(Request $request)
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return redirect()->route('profile.edit')
                ->with('error', 'Önce profilinizi tamamlayın!');
        }

        $validated = $request->validate([
            'matches_played' => 'required|integer|min:0',
            'wins' => 'required|integer|min:0',
            'kills' => 'required|integer|min:0',
            'deaths' => 'required|integer|min:0',
            'headshots' => 'required|integer|min:0',
            'top_10_finishes' => 'required|integer|min:0',
            'damage_dealt' => 'required|integer|min:0',
            'survival_time' => 'required|integer|min:0',
            'longest_kill' => 'required|integer|min:0',
        ]);

        // İstatistikleri güncelle ve oranları hesapla
        $profile->updateStatistics($validated);

        return redirect()->route('profile.statistics')
            ->with('success', 'Oyun istatistikleriniz başarıyla güncellendi!');
    }
}
