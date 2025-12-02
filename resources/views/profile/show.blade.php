@extends('layouts.app')

@section('title', $user->name . ' - Profil')

@section('content')
<style>
    /* PUBG Dark Theme */
    body {
        background: #0a0e27;
    }
    
    .profile-hero {
        background: linear-gradient(135deg, #1a1f3a 0%, #0a0e27 100%);
        position: relative;
        overflow: hidden;
    }
    
    .profile-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 50%, rgba(255, 107, 0, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255, 165, 0, 0.1) 0%, transparent 50%);
    }
    
    .profile-hero::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ff6b00" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
        background-size: cover;
    }

    /* Dark Glass Card */
    .glass-card {
        background: rgba(26, 31, 58, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 107, 0, 0.2);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
    }

    /* Avatar Glow Effect - Orange */
    .avatar-glow {
        box-shadow: 0 0 30px rgba(255, 107, 0, 0.6), 0 0 60px rgba(255, 165, 0, 0.4);
        animation: pulse-glow 2s ease-in-out infinite;
    }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 30px rgba(255, 107, 0, 0.6), 0 0 60px rgba(255, 165, 0, 0.4); }
        50% { box-shadow: 0 0 40px rgba(255, 107, 0, 0.8), 0 0 80px rgba(255, 165, 0, 0.6); }
    }

    /* Stat Card Hover Effect - Dark */
    .stat-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        background: rgba(20, 25, 45, 0.6);
        border: 1px solid rgba(255, 107, 0, 0.2);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 107, 0, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .stat-card:hover::before {
        left: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(255, 107, 0, 0.3);
        border-color: rgba(255, 107, 0, 0.5);
    }

    /* Badge Animation */
    .badge-item {
        transition: all 0.3s ease;
    }

    .badge-item:hover {
        transform: scale(1.2) rotate(5deg);
    }

    /* Button Gradient - Orange */
    .btn-gradient {
        background: linear-gradient(135deg, #ff6b00 0%, #ff8c00 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .btn-gradient:hover::before {
        left: 100%;
    }

    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(255, 107, 0, 0.6);
    }

    /* Level Badge - Orange */
    .level-badge {
        background: linear-gradient(135deg, #ff6b00 0%, #ffa500 100%);
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    /* Activity Card - Dark */
    .activity-card {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        background: rgba(20, 25, 45, 0.4);
    }

    .activity-card:hover {
        border-left-color: #ff6b00;
        background: linear-gradient(90deg, rgba(255, 107, 0, 0.1) 0%, rgba(20, 25, 45, 0.4) 100%);
        transform: translateX(5px);
    }

    /* Progress Bar - Orange */
    .xp-progress {
        height: 8px;
        background: linear-gradient(90deg, #ff6b00 0%, #ffa500 100%);
        border-radius: 10px;
        position: relative;
        overflow: hidden;
    }

    .xp-progress::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* Social Icons */
    .social-icon {
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .social-icon:hover {
        transform: translateY(-3px) scale(1.1);
    }

    .social-twitch { background: linear-gradient(135deg, #9146FF 0%, #6441A5 100%); }
    .social-youtube { background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%); }
    .social-discord { background: linear-gradient(135deg, #7289DA 0%, #5865F2 100%); }
</style>

<!-- Hero Section with Gradient Background -->
<div class="profile-hero py-16 mb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <!-- Avatar & Info -->
            <div class="flex items-center space-x-6 mb-6 md:mb-0">
                <div class="relative">
                    <div class="w-32 h-32 rounded-full overflow-hidden avatar-glow border-4 border-gray-900">
                        @if($user->profile && $user->profile->avatar_path)
                            <img src="{{ $user->profile->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white text-5xl font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    @php
                        $settings = $user->settings ?? [];
                        $showOnlineStatus = $settings['show_online_status'] ?? true;
                    @endphp
                    @if($user->status === 'active' && $showOnlineStatus)
                    <div class="absolute bottom-2 right-2 w-6 h-6 bg-green-400 rounded-full border-4 border-gray-900"></div>
                    @endif
                </div>
                
                <div class="text-white">
                    <div class="flex items-center space-x-3 mb-2">
                        <h1 class="text-4xl md:text-5xl font-bold drop-shadow-lg">{{ $user->name }}</h1>
                        <span class="level-badge px-4 py-2 rounded-full text-white text-sm font-bold shadow-lg">
                            ⭐ Level {{ $user->getLevel() }}
                        </span>
                    </div>
                    @if($user->profile && $user->profile->nickname)
                        <p class="text-orange-200 text-lg mb-1">{{ $user->profile->nickname }}</p>
                    @endif
                    <div class="flex items-center space-x-4 text-gray-300">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ number_format($user->xp_total ?? 0) }} XP
                        </span>
                        <span>•</span>
                        <span>{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                    
                    <!-- XP Progress Bar -->
                    <div class="mt-3 w-64">
                        <div class="bg-gray-800 bg-opacity-60 rounded-full h-3 overflow-hidden border border-orange-900">
                            @php
                                $currentLevel = $user->getLevel();
                                $xpForCurrentLevel = ($currentLevel - 1) * 1000;
                                $xpForNextLevel = $currentLevel * 1000;
                                $currentXp = $user->xp_total ?? 0;
                                $progress = (($currentXp - $xpForCurrentLevel) / ($xpForNextLevel - $xpForCurrentLevel)) * 100;
                            @endphp
                            <div class="xp-progress h-full" style="width: {{ min($progress, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-orange-300 mt-1">{{ number_format($xpForNextLevel - $currentXp) }} XP sonraki seviyeye</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="glass-card rounded-xl p-4 border-orange-500">
                    <div class="text-3xl font-bold text-orange-400">{{ $user->lfgPosts()->count() }}</div>
                    <div class="text-xs text-gray-400 mt-1">İlan</div>
                </div>
                <div class="glass-card rounded-xl p-4 border-orange-500">
                    <div class="text-3xl font-bold text-orange-400">{{ $user->guidePosts()->count() }}</div>
                    <div class="text-xs text-gray-400 mt-1">Rehber</div>
                </div>
                <div class="glass-card rounded-xl p-4 border-orange-500">
                    <div class="text-3xl font-bold text-orange-400">{{ $user->profile->views_count ?? 0 }}</div>
                    <div class="text-xs text-gray-400 mt-1">Görüntülenme</div>
                </div>
            </div>
        </div>

        <!-- Profil Tamamlama Durumu (Sadece kendi profilinde göster) -->
        @if(auth()->check() && auth()->id() === $user->id && $user->profile_completion < 100)
        <div class="mt-6 max-w-md mx-auto">
            <div class="glass-card rounded-2xl p-6 border-2 border-orange-500/50">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-white font-bold text-lg">Profil Tamamlanma</h4>
                    <span class="text-orange-400 font-black text-2xl">{{ $user->profile_completion }}%</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-4 overflow-hidden mb-4">
                    <div class="bg-gradient-to-r from-orange-500 to-red-500 h-full rounded-full transition-all duration-500" 
                         style="width: {{ $user->profile_completion }}%"></div>
                </div>
                <p class="text-gray-300 text-sm mb-4">
                    💡 Profilini tamamlayarak daha fazla özelliğe erişebilirsin!
                </p>
                <a href="{{ route('profile.edit') }}" 
                   class="btn-gradient w-full py-3 rounded-xl font-bold text-white text-center block shadow-lg">
                    ✨ Profili Tamamla
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ana İçerik -->
        <div class="lg:col-span-2 space-y-6">
            <!-- PUBG Bilgileri -->
            @if($user->profile)
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl mr-3 shadow-lg shadow-orange-500/50">
                        🎮
                    </div>
                    <h3 class="text-2xl font-bold text-orange-400">PUBG Mobile Bilgileri</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($user->profile->pubg_id)
                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">PUBG ID</p>
                                <p class="text-xl font-bold text-white">{{ $user->profile->pubg_id }}</p>
                            </div>
                            <div class="text-3xl opacity-20">🎮</div>
                        </div>
                    </div>
                    @endif

                    @if($user->profile->rank)
                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">Rütbe</p>
                                <p class="text-xl font-bold text-white">{{ $user->profile->rank }}</p>
                            </div>
                            <div class="text-3xl opacity-20">👑</div>
                        </div>
                    </div>
                    @endif

                    @if($user->profile->server_region)
                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">Sunucu</p>
                                <p class="text-xl font-bold text-white">{{ $user->profile->server_region }}</p>
                            </div>
                            <div class="text-3xl opacity-20">🌍</div>
                        </div>
                    </div>
                    @endif

                    @if($user->profile->play_style)
                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">Oyun Tarzı</p>
                                <p class="text-xl font-bold text-white">{{ ucfirst($user->profile->play_style) }}</p>
                            </div>
                            <div class="text-3xl opacity-20">⚔️</div>
                        </div>
                    </div>
                    @endif

                    @php
                        $settings = $user->settings ?? [];
                        $showCity = $settings['show_city'] ?? true;
                        $showAge = $settings['show_age'] ?? true;
                        $showEmail = $settings['show_email'] ?? false;
                    @endphp

                    @if($user->profile->city && $showCity)
                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">Şehir</p>
                                <p class="text-xl font-bold text-white">{{ $user->profile->city }}</p>
                            </div>
                            <div class="text-3xl opacity-20">📍</div>
                        </div>
                    </div>
                    @endif

                    @if($user->profile->age_range && $showAge)
                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">Yaş</p>
                                <p class="text-xl font-bold text-white">{{ $user->profile->age_range }}</p>
                            </div>
                            <div class="text-3xl opacity-20">🎂</div>
                        </div>
                    </div>
                    @endif

                    @if($showEmail && auth()->check() && auth()->id() !== $user->id)
                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">Email</p>
                                <p class="text-xl font-bold text-white">{{ $user->email }}</p>
                            </div>
                            <div class="text-3xl opacity-20">📧</div>
                        </div>
                    </div>
                    @endif
                </div>

                @if($user->profile->bio)
                <div class="mt-6 stat-card p-6 rounded-xl">
                    <div class="flex items-start space-x-3">
                        <div class="text-2xl opacity-20">💬</div>
                        <div class="flex-1">
                            <p class="text-xs text-orange-400 font-semibold mb-3 uppercase tracking-wider">Hakkında</p>
                            <p class="text-gray-300 leading-relaxed text-base">{{ $user->profile->bio }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Oyun İstatistikleri -->
            @if($user->profile && ($user->profile->matches_played > 0 || $user->profile->kills > 0))
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl mr-3 shadow-lg shadow-orange-500/50">
                        📊
                    </div>
                    <h3 class="text-2xl font-bold text-orange-400">Oyun İstatistikleri</h3>
                </div>
                
                <!-- Ana İstatistikler (Büyük Kartlar) -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="stat-card p-6 rounded-xl text-center">
                        <div class="text-4xl mb-2">⚔️</div>
                        <p class="text-3xl font-bold text-white mb-1">{{ number_format($user->profile->kd_ratio, 2) }}</p>
                        <p class="text-xs text-orange-400 font-semibold uppercase tracking-wider">K/D Oranı</p>
                    </div>
                    
                    <div class="stat-card p-6 rounded-xl text-center">
                        <div class="text-4xl mb-2">🏆</div>
                        <p class="text-3xl font-bold text-white mb-1">{{ number_format($user->profile->win_rate, 1) }}%</p>
                        <p class="text-xs text-orange-400 font-semibold uppercase tracking-wider">Win Rate</p>
                    </div>
                    
                    <div class="stat-card p-6 rounded-xl text-center">
                        <div class="text-4xl mb-2">🎯</div>
                        <p class="text-3xl font-bold text-white mb-1">{{ number_format($user->profile->headshot_rate, 1) }}%</p>
                        <p class="text-xs text-orange-400 font-semibold uppercase tracking-wider">Headshot</p>
                    </div>
                    
                    <div class="stat-card p-6 rounded-xl text-center">
                        <div class="text-4xl mb-2">🎮</div>
                        <p class="text-3xl font-bold text-white mb-1">{{ number_format($user->profile->matches_played) }}</p>
                        <p class="text-xs text-orange-400 font-semibold uppercase tracking-wider">Maç</p>
                    </div>
                </div>

                <!-- Detaylı İstatistikler -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-1 uppercase tracking-wider">Kazanılan</p>
                                <p class="text-2xl font-bold text-white">{{ number_format($user->profile->wins) }}</p>
                            </div>
                            <div class="text-2xl opacity-20">🥇</div>
                        </div>
                    </div>

                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-1 uppercase tracking-wider">Öldürme</p>
                                <p class="text-2xl font-bold text-white">{{ number_format($user->profile->kills) }}</p>
                            </div>
                            <div class="text-2xl opacity-20">💀</div>
                        </div>
                    </div>

                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-1 uppercase tracking-wider">Ölüm</p>
                                <p class="text-2xl font-bold text-white">{{ number_format($user->profile->deaths) }}</p>
                            </div>
                            <div class="text-2xl opacity-20">☠️</div>
                        </div>
                    </div>

                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-1 uppercase tracking-wider">Headshot</p>
                                <p class="text-2xl font-bold text-white">{{ number_format($user->profile->headshots) }}</p>
                            </div>
                            <div class="text-2xl opacity-20">🎯</div>
                        </div>
                    </div>

                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-1 uppercase tracking-wider">Top 10</p>
                                <p class="text-2xl font-bold text-white">{{ number_format($user->profile->top_10_finishes) }}</p>
                            </div>
                            <div class="text-2xl opacity-20">🔟</div>
                        </div>
                    </div>

                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-1 uppercase tracking-wider">Hasar</p>
                                <p class="text-2xl font-bold text-white">{{ number_format($user->profile->damage_dealt) }}</p>
                            </div>
                            <div class="text-2xl opacity-20">💥</div>
                        </div>
                    </div>

                    @if($user->profile->survival_time > 0)
                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-1 uppercase tracking-wider">Hayatta Kalma</p>
                                <p class="text-2xl font-bold text-white">{{ number_format($user->profile->survival_time) }}dk</p>
                            </div>
                            <div class="text-2xl opacity-20">⏱️</div>
                        </div>
                    </div>
                    @endif

                    @if($user->profile->longest_kill > 0)
                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-1 uppercase tracking-wider">En Uzak Kill</p>
                                <p class="text-2xl font-bold text-white">{{ number_format($user->profile->longest_kill) }}m</p>
                            </div>
                            <div class="text-2xl opacity-20">🎯</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Sosyal Medya -->
            @if($user->profile && ($user->profile->twitch_username || $user->profile->youtube_channel || $user->profile->discord_username))
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl mr-3 shadow-lg shadow-orange-500/50">
                        🔗
                    </div>
                    <h3 class="text-2xl font-bold text-orange-400">Sosyal Medya</h3>
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                    @if($user->profile->twitch_username)
                    <a href="https://twitch.tv/{{ $user->profile->twitch_username }}" target="_blank" 
                       class="stat-card p-5 rounded-xl hover:shadow-lg transition-all hover:-translate-y-1 block">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-2xl shadow-lg shadow-purple-500/30">
                                    📺
                                </div>
                                <div>
                                    <div class="text-xs text-orange-400 font-semibold uppercase tracking-wider">Twitch</div>
                                    <div class="text-lg font-bold text-white">{{ $user->profile->twitch_username }}</div>
                                </div>
                            </div>
                            <div class="text-gray-500">→</div>
                        </div>
                    </a>
                    @endif

                    @if($user->profile->youtube_channel)
                    <a href="https://youtube.com/{{ $user->profile->youtube_channel }}" target="_blank"
                       class="stat-card p-5 rounded-xl hover:shadow-lg transition-all hover:-translate-y-1 block">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-2xl shadow-lg shadow-red-500/30">
                                    ▶️
                                </div>
                                <div>
                                    <div class="text-xs text-orange-400 font-semibold uppercase tracking-wider">YouTube</div>
                                    <div class="text-lg font-bold text-white">{{ $user->profile->youtube_channel }}</div>
                                </div>
                            </div>
                            <div class="text-gray-500">→</div>
                        </div>
                    </a>
                    @endif

                    @if($user->profile->discord_username)
                    <div class="stat-card p-5 rounded-xl">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center text-2xl shadow-lg shadow-indigo-500/30">
                                💬
                            </div>
                            <div>
                                <div class="text-xs text-orange-400 font-semibold uppercase tracking-wider">Discord</div>
                                <div class="text-lg font-bold text-white">{{ $user->profile->discord_username }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Cihaz Bilgileri -->
            @if($user->device)
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl mr-3 shadow-lg shadow-orange-500/50">
                        📱
                    </div>
                    <h3 class="text-2xl font-bold text-orange-400">Cihaz Bilgileri</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">Cihaz</p>
                                <p class="text-xl font-bold text-white">{{ $user->device->device_name }}</p>
                            </div>
                            <div class="text-3xl opacity-20">📱</div>
                        </div>
                    </div>

                    @if($user->device->graphics_settings)
                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">Grafik</p>
                                <p class="text-xl font-bold text-white">{{ $user->device->graphics_settings }}</p>
                            </div>
                            <div class="text-3xl opacity-20">🎨</div>
                        </div>
                    </div>
                    @endif

                    @if($user->device->fps_setting)
                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">FPS</p>
                                <p class="text-xl font-bold text-white">{{ $user->device->fps_setting }}</p>
                            </div>
                            <div class="text-3xl opacity-20">⚡</div>
                        </div>
                    </div>
                    @endif

                    <div class="stat-card p-6 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-orange-400 font-semibold mb-2 uppercase tracking-wider">Gyro</p>
                                <p class="text-xl font-bold text-white">{{ $user->device->gyro_enabled ? '✅ Açık' : '❌ Kapalı' }}</p>
                            </div>
                            <div class="text-3xl opacity-20">🎯</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Rozetler -->
            @if($user->badges()->count() > 0)
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl mr-3 shadow-lg shadow-orange-500/50">
                        🏆
                    </div>
                    <h3 class="text-2xl font-bold text-orange-400">Rozetler</h3>
                </div>
                
                <div class="grid grid-cols-4 md:grid-cols-6 gap-4">
                    @foreach($user->badges as $badge)
                    <div class="badge-item text-center p-4 stat-card rounded-xl cursor-pointer">
                        <div class="text-5xl mb-2">{{ $badge->icon }}</div>
                        <p class="text-xs text-gray-300 font-medium">{{ $badge->name }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Son Aktiviteler -->
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white text-2xl mr-3 shadow-lg shadow-orange-500/50">
                        📝
                    </div>
                    <h3 class="text-2xl font-bold text-orange-400">Son Aktiviteler</h3>
                </div>
                
                <div class="space-y-3">
                    @php
                        $activities = collect();
                        
                        if($user->lfgPosts()->exists()) {
                            $activities = $activities->merge(
                                $user->lfgPosts()->latest()->take(3)->get()->map(function($post) {
                                    return [
                                        'type' => 'lfg',
                                        'title' => $post->title,
                                        'date' => $post->created_at,
                                        'url' => route('lfg.show', $post->id)
                                    ];
                                })
                            );
                        }
                        
                        if($user->guidePosts()->exists()) {
                            $activities = $activities->merge(
                                $user->guidePosts()->latest()->take(3)->get()->map(function($post) {
                                    return [
                                        'type' => 'guide',
                                        'title' => $post->title,
                                        'date' => $post->created_at,
                                        'url' => route('guides.show', $post->id)
                                    ];
                                })
                            );
                        }
                        
                        if($user->communityPosts()->exists()) {
                            $activities = $activities->merge(
                                $user->communityPosts()->latest()->take(3)->get()->map(function($post) {
                                    return [
                                        'type' => 'community',
                                        'title' => $post->title,
                                        'date' => $post->created_at,
                                        'url' => route('community.show', $post->id)
                                    ];
                                })
                            );
                        }
                        
                        $activities = $activities->sortByDesc('date')->take(5);
                    @endphp

                    @forelse($activities as $activity)
                    <a href="{{ $activity['url'] }}" class="activity-card block p-4 rounded-xl hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl bg-gradient-to-br from-orange-500 to-red-600 shadow-lg shadow-orange-500/30">
                                    @if($activity['type'] === 'lfg') 🎮
                                    @elseif($activity['type'] === 'guide') 📖
                                    @else 💬
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-orange-400 font-medium">
                                        @if($activity['type'] === 'lfg') İlan
                                        @elseif($activity['type'] === 'guide') Rehber
                                        @else Topluluk
                                        @endif
                                    </p>
                                    <p class="font-semibold text-white">{{ Str::limit($activity['title'], 40) }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $activity['date']->diffForHumans() }}</span>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">📭</div>
                        <p class="text-gray-400">Henüz aktivite yok</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- İletişim Butonları -->
            @auth
                @if(auth()->id() !== $user->id)
                @php
                    $settings = $user->settings ?? [];
                    $allowMessages = $settings['allow_messages'] ?? 'everyone';
                    $allowFriendRequests = $settings['allow_friend_requests'] ?? true;
                    
                    // Arkadaş kontrolü
                    $isFriend = \App\Models\Friendship::where(function($query) use ($user) {
                        $query->where('user_id', auth()->id())
                              ->where('friend_id', $user->id);
                    })->orWhere(function($query) use ($user) {
                        $query->where('user_id', $user->id)
                              ->where('friend_id', auth()->id());
                    })->where('status', 'accepted')->exists();
                    
                    // Mesaj gönderme izni kontrolü
                    $canSendMessage = $allowMessages === 'everyone' || 
                                     ($allowMessages === 'friends' && $isFriend);
                @endphp
                <div class="glass-card rounded-2xl p-6 shadow-xl space-y-3">
                    @if($canSendMessage)
                    <a href="{{ route('messages.show', $user->id) }}" 
                        class="btn-gradient block w-full text-white px-6 py-4 rounded-xl text-center font-bold shadow-lg">
                        <span class="text-xl mr-2">💬</span>
                        Mesaj Gönder
                    </a>
                    @else
                    <div class="block w-full bg-gray-700 text-gray-400 px-6 py-4 rounded-xl text-center font-bold cursor-not-allowed">
                        <span class="text-xl mr-2">🔒</span>
                        Mesaj Kapalı
                    </div>
                    @endif
                    
                    @if($allowFriendRequests && !$isFriend)
                    <form action="{{ route('friends.send-request') }}" method="POST">
                        @csrf
                        <input type="hidden" name="friend_id" value="{{ $user->id }}">
                        <button type="submit" 
                                class="block w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl text-center font-bold shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                            <span class="text-xl mr-2">👥</span>
                            Arkadaş Ekle
                        </button>
                    </form>
                    @elseif(!$allowFriendRequests)
                    <div class="block w-full bg-gray-700 text-gray-400 px-6 py-4 rounded-xl text-center font-bold cursor-not-allowed">
                        <span class="text-xl mr-2">🔒</span>
                        Arkadaşlık İstekleri Kapalı
                    </div>
                    @elseif($isFriend)
                    <div class="block w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-xl text-center font-bold">
                        <span class="text-xl mr-2">✅</span>
                        Arkadaşsınız
                    </div>
                    @endif
                </div>
                @endif
            @endauth
            
            @guest
            <div class="glass-card rounded-2xl p-6 shadow-xl text-center">
                <div class="text-6xl mb-4">🔒</div>
                <p class="text-gray-300 mb-6">Bu kullanıcıyla iletişime geçmek için giriş yapmalısın.</p>
                <a href="{{ route('login') }}" 
                    class="btn-gradient block w-full text-white px-6 py-4 rounded-xl text-center font-bold shadow-lg">
                    Giriş Yap
                </a>
            </div>
            @endguest

            <!-- İstatistikler -->
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <h3 class="text-xl font-bold mb-6 text-orange-400">📊 İstatistikler</h3>
                <div class="space-y-4">
                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-xs text-orange-400 font-semibold uppercase tracking-wider mb-1">Toplam XP</div>
                                <div class="font-bold text-white text-2xl">{{ number_format($user->xp_total ?? 0) }}</div>
                            </div>
                            <div class="text-3xl opacity-20">⭐</div>
                        </div>
                    </div>
                    @if($user->profile)
                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-xs text-orange-400 font-semibold uppercase tracking-wider mb-1">Görüntülenme</div>
                                <div class="font-bold text-white text-2xl">{{ number_format($user->profile->views_count ?? 0) }}</div>
                            </div>
                            <div class="text-3xl opacity-20">👁️</div>
                        </div>
                    </div>
                    @endif
                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-xs text-orange-400 font-semibold uppercase tracking-wider mb-1">Üyelik</div>
                                <div class="font-bold text-white text-lg">{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="text-3xl opacity-20">📅</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hızlı Bilgiler -->
            <div class="glass-card rounded-2xl p-6 shadow-xl">
                <h3 class="text-xl font-bold mb-6 text-orange-400">⚡ Hızlı Bilgiler</h3>
                <div class="space-y-3">
                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="text-3xl">🎯</div>
                                <div>
                                    <div class="text-xs text-orange-400 font-semibold uppercase tracking-wider">Toplam İçerik</div>
                                    <div class="font-bold text-white text-xl">{{ $user->lfgPosts()->count() + $user->guidePosts()->count() + $user->communityPosts()->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="text-3xl">🏅</div>
                                <div>
                                    <div class="text-xs text-orange-400 font-semibold uppercase tracking-wider">Rozet Sayısı</div>
                                    <div class="font-bold text-white text-xl">{{ $user->badges()->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card p-4 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="text-3xl">⭐</div>
                                <div>
                                    <div class="text-xs text-orange-400 font-semibold uppercase tracking-wider">Seviye</div>
                                    <div class="font-bold text-white text-xl">{{ $user->getLevel() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
