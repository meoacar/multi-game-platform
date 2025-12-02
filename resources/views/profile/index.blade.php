@extends('layouts.app')

@section('title', 'Profilim')

@section('content')
<!-- PUBG Mobile Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" 
     style="background-image: url('/arkaplan/16.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    
    <!-- Karartma Katmanı -->
    <div class="absolute inset-0 bg-black/60"></div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-black/60"></div>
    
    <!-- Animasyonlu Işık Efektleri -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-float-delayed"></div>
        <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-yellow-500/5 rounded-full blur-3xl animate-pulse-slow"></div>
    </div>
</div>

<div class="relative z-10 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Hero Profile Card -->
        <div class="relative mb-8 animate-fade-in">
            <div class="relative bg-gradient-to-br from-orange-900/20 via-red-900/20 to-pink-900/20 backdrop-blur-xl rounded-3xl border border-orange-500/30 shadow-2xl overflow-hidden">
                <!-- Animated Background -->
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 via-red-500/10 to-pink-500/10"></div>
                
                <!-- Glow Effect -->
                <div class="absolute top-0 left-0 w-full h-full">
                    <div class="absolute top-0 left-1/4 w-96 h-96 bg-orange-500/20 rounded-full blur-3xl animate-pulse-slow"></div>
                    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
                </div>

                <!-- Pattern Overlay -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 32px 32px;"></div>
                </div>

                <div class="relative p-8">
                    <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="relative group">
                                <div class="w-48 h-48 rounded-3xl overflow-hidden shadow-2xl border-4 border-orange-500/50 transform hover:scale-105 transition-all duration-300">
                                    @if($user->profile && $user->profile->avatar_path)
                                        <img src="{{ $user->profile->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-orange-500 via-red-500 to-pink-500 flex items-center justify-center text-white text-7xl font-black">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 text-center md:text-left">
                            <!-- Online Badge - İsmin Üstünde -->
                            <div class="inline-flex items-center bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg border-2 border-green-600 animate-pulse mb-2">
                                🟢 Çevrimiçi
                            </div>
                            
                            <h1 class="text-5xl font-black mb-3">
                                <span class="bg-gradient-to-r from-orange-400 via-red-400 to-pink-400 bg-clip-text text-transparent">
                                    {{ $user->name }}
                                </span>
                            </h1>
                            
                            @if($user->profile && $user->profile->nickname)
                                <p class="text-2xl text-orange-400 font-bold mb-2">🎮 {{ $user->profile->nickname }}</p>
                            @endif
                            
                            <div class="flex flex-wrap gap-3 justify-center md:justify-start mb-4">
                                <span class="px-4 py-2 bg-gradient-to-r from-blue-500/20 to-purple-500/20 border border-blue-500/30 rounded-xl text-blue-300 text-sm font-semibold">
                                    ⭐ Level {{ $user->getLevel() }}
                                </span>
                                <span class="px-4 py-2 bg-gradient-to-r from-orange-500/20 to-red-500/20 border border-orange-500/30 rounded-xl text-orange-300 text-sm font-semibold">
                                    🔥 {{ number_format($user->xp_total ?? 0) }} XP
                                </span>
                                @if($user->profile && $user->profile->rank)
                                    <span class="px-4 py-2 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 rounded-xl text-yellow-300 text-sm font-semibold">
                                        👑 {{ $user->profile->rank }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-gray-400 mb-4">📧 {{ $user->email }}</p>
                            <p class="text-gray-500 text-sm">📅 Üyelik: {{ $user->created_at->format('d.m.Y') }}</p>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('profile.edit') }}" 
                               class="px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-bold hover:from-orange-600 hover:to-red-600 transition-all transform hover:scale-105 shadow-lg text-center">
                                ✏️ Düzenle
                            </a>
                            <a href="{{ route('profile.statistics') }}" 
                               class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-bold hover:from-blue-600 hover:to-purple-600 transition-all transform hover:scale-105 shadow-lg text-center">
                                📊 İstatistikler
                            </a>
                            <a href="{{ route('settings.index') }}" 
                               class="px-6 py-3 bg-white/5 border border-white/10 text-white rounded-xl font-bold hover:bg-white/10 transition-all text-center">
                                ⚙️ Ayarlar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PUBG Mobile Bilgileri -->
        @if($user->profile)
        <div class="mb-8 bg-gradient-to-br from-orange-900/20 via-red-900/20 to-pink-900/20 backdrop-blur-xl rounded-2xl border border-orange-500/30 shadow-xl p-6 animate-fade-in">
            <h2 class="text-3xl font-black text-white mb-6 flex items-center">
                <span class="text-4xl mr-3">🎮</span>
                PUBG Mobile Bilgileri
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @if($user->profile->pubg_id)
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="text-sm text-gray-400 mb-1">🆔 PUBG ID</div>
                    <div class="text-lg font-bold text-white">{{ $user->profile->pubg_id }}</div>
                </div>
                @endif

                @if($user->profile->rank)
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="text-sm text-gray-400 mb-1">👑 Rank</div>
                    <div class="text-lg font-bold text-white">{{ $user->profile->rank }}</div>
                </div>
                @endif

                @if($user->profile->server_region)
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="text-sm text-gray-400 mb-1">🌍 Sunucu</div>
                    <div class="text-lg font-bold text-white">{{ $user->profile->server_region }}</div>
                </div>
                @endif

                @if($user->profile->play_style)
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="text-sm text-gray-400 mb-1">🎯 Oyun Tarzı</div>
                    <div class="text-lg font-bold text-white">{{ ucfirst($user->profile->play_style) }}</div>
                </div>
                @endif
            </div>

            @if($user->profile->bio)
            <div class="mt-6 bg-white/5 rounded-xl p-4 border border-white/10">
                <div class="text-sm text-gray-400 mb-2">📝 Hakkında</div>
                <p class="text-white leading-relaxed">{{ $user->profile->bio }}</p>
            </div>
            @endif
        </div>
        @endif

        <!-- Stats Grid -->
        @if($user->profile && ($user->profile->matches_played > 0 || $user->profile->kills > 0))
        <div class="mb-8 animate-scale-in">
            <h2 class="text-3xl font-black text-white mb-6 flex items-center">
                <span class="text-4xl mr-3">📊</span>
                Oyun İstatistikleri
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-br from-blue-600/20 to-blue-800/20 backdrop-blur-xl rounded-2xl p-6 border border-blue-500/20 hover:border-blue-500/40 transition-all hover:scale-105">
                    <div class="text-4xl mb-2">⚔️</div>
                    <div class="text-4xl font-black text-white mb-1">{{ number_format($user->profile->kd_ratio, 2) }}</div>
                    <div class="text-sm text-blue-300 font-semibold">K/D Oranı</div>
                </div>
                
                <div class="bg-gradient-to-br from-green-600/20 to-green-800/20 backdrop-blur-xl rounded-2xl p-6 border border-green-500/20 hover:border-green-500/40 transition-all hover:scale-105">
                    <div class="text-4xl mb-2">🏆</div>
                    <div class="text-4xl font-black text-white mb-1">{{ number_format($user->profile->win_rate, 1) }}%</div>
                    <div class="text-sm text-green-300 font-semibold">Win Rate</div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-600/20 to-purple-800/20 backdrop-blur-xl rounded-2xl p-6 border border-purple-500/20 hover:border-purple-500/40 transition-all hover:scale-105">
                    <div class="text-4xl mb-2">🎯</div>
                    <div class="text-4xl font-black text-white mb-1">{{ number_format($user->profile->headshot_rate, 1) }}%</div>
                    <div class="text-sm text-purple-300 font-semibold">Headshot</div>
                </div>
                
                <div class="bg-gradient-to-br from-orange-600/20 to-red-800/20 backdrop-blur-xl rounded-2xl p-6 border border-orange-500/20 hover:border-orange-500/40 transition-all hover:scale-105">
                    <div class="text-4xl mb-2">🎮</div>
                    <div class="text-4xl font-black text-white mb-1">{{ number_format($user->profile->matches_played) }}</div>
                    <div class="text-sm text-orange-300 font-semibold">Toplam Maç</div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white/5 backdrop-blur-xl rounded-xl p-4 border border-white/10">
                    <div class="text-2xl mb-1">💀</div>
                    <div class="text-2xl font-bold text-white">{{ number_format($user->profile->kills) }}</div>
                    <div class="text-xs text-gray-400">Öldürme</div>
                </div>
                
                <div class="bg-white/5 backdrop-blur-xl rounded-xl p-4 border border-white/10">
                    <div class="text-2xl mb-1">🥇</div>
                    <div class="text-2xl font-bold text-white">{{ number_format($user->profile->wins) }}</div>
                    <div class="text-xs text-gray-400">Kazanılan</div>
                </div>
                
                <div class="bg-white/5 backdrop-blur-xl rounded-xl p-4 border border-white/10">
                    <div class="text-2xl mb-1">🔟</div>
                    <div class="text-2xl font-bold text-white">{{ number_format($user->profile->top_10_finishes) }}</div>
                    <div class="text-xs text-gray-400">Top 10</div>
                </div>
                
                <div class="bg-white/5 backdrop-blur-xl rounded-xl p-4 border border-white/10">
                    <div class="text-2xl mb-1">💥</div>
                    <div class="text-2xl font-bold text-white">{{ number_format($user->profile->damage_dealt) }}</div>
                    <div class="text-xs text-gray-400">Hasar</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Rozetler Showcase -->
        @if($user->badges()->count() > 0)
        <div class="mb-8 animate-fade-in">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-3xl font-black text-white flex items-center">
                    <span class="text-4xl mr-3">🏅</span>
                    Başarımlar & Rozetler
                </h2>
                <a href="{{ route('xp.badges') }}" class="text-orange-400 hover:text-orange-300 font-semibold">
                    Tümünü Gör →
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($user->badges()->take(6)->get() as $badge)
                @php
                    $iconMap = [
                        'star' => '⭐',
                        'badge-check' => '✅',
                        'gem' => '💎',
                        'award' => '🏆',
                        'heart' => '❤️',
                        'trending-up' => '📈',
                        'crown' => '👑',
                        'eye' => '👁️',
                        'crosshair' => '🎯',
                        'user' => '👤',
                        'users' => '👥',
                        'user-plus' => '➕',
                        'message-circle' => '💬',
                        'message-square' => '💭',
                        'thumbs-up' => '👍',
                        'megaphone' => '📢',
                        'file-text' => '📄',
                        'shield' => '🛡️',
                        'shield-check' => '✅',
                        'book' => '📚',
                        'book-open' => '📖',
                        'smartphone' => '📱',
                        'calendar' => '📅',
                        'calendar-check' => '✔️',
                        'zap' => '⚡',
                        'sunrise' => '🌅',
                        'moon' => '🌙',
                        'coffee' => '☕',
                        'fire' => '🔥',
                        'trophy' => '🏆',
                        'flag' => '🚩',
                        'life-buoy' => '🆘',
                    ];
                    $emoji = $iconMap[$badge->icon] ?? '🏅';
                @endphp
                <div class="bg-gradient-to-br from-yellow-900/20 via-orange-900/20 to-red-900/20 backdrop-blur-xl rounded-2xl border border-yellow-500/20 p-4 hover:border-yellow-500/40 transition-all hover:scale-105 group">
                    <div class="text-5xl mb-2 text-center group-hover:scale-110 transition-transform">{{ $emoji }}</div>
                    <div class="text-center">
                        <div class="text-sm font-bold text-white mb-1">{{ $badge->name }}</div>
                        <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($badge->pivot->unlocked_at)->diffForHumans() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Sosyal Medya & İletişim -->
        @if($user->profile && ($user->profile->twitch_username || $user->profile->youtube_channel || $user->profile->discord_username))
        <div class="mb-8 bg-gradient-to-br from-purple-900/20 via-pink-900/20 to-red-900/20 backdrop-blur-xl rounded-2xl border border-purple-500/30 shadow-xl p-6 animate-fade-in">
            <h3 class="text-2xl font-bold text-white mb-4 flex items-center">
                <span class="text-3xl mr-3">🔗</span>
                Sosyal Medya
            </h3>
            
            <div class="flex flex-wrap gap-3">
                @if($user->profile->twitch_username)
                <a href="https://twitch.tv/{{ $user->profile->twitch_username }}" target="_blank" 
                   class="flex items-center gap-2 px-4 py-3 bg-purple-600/30 border border-purple-500/50 rounded-xl text-purple-300 hover:bg-purple-600/50 transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/></svg>
                    Twitch
                </a>
                @endif

                @if($user->profile->youtube_channel)
                <a href="https://youtube.com/{{ $user->profile->youtube_channel }}" target="_blank" 
                   class="flex items-center gap-2 px-4 py-3 bg-red-600/30 border border-red-500/50 rounded-xl text-red-300 hover:bg-red-600/50 transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    YouTube
                </a>
                @endif

                @if($user->profile->discord_username)
                <div class="flex items-center gap-2 px-4 py-3 bg-indigo-600/30 border border-indigo-500/50 rounded-xl text-indigo-300">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515a.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0a12.64 12.64 0 0 0-.617-1.25a.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057a19.9 19.9 0 0 0 5.993 3.03a.078.078 0 0 0 .084-.028a14.09 14.09 0 0 0 1.226-1.994a.076.076 0 0 0-.041-.106a13.107 13.107 0 0 1-1.872-.892a.077.077 0 0 1-.008-.128a10.2 10.2 0 0 0 .372-.292a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127a12.299 12.299 0 0 1-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028a19.839 19.839 0 0 0 6.002-3.03a.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.956-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.946 2.418-2.157 2.418z"/></svg>
                    {{ $user->profile->discord_username }}
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Favori Haritalar -->
        @if($user->profile && $user->profile->favorite_maps && count($user->profile->favorite_maps) > 0)
        <div class="mb-8 animate-fade-in">
            <h2 class="text-3xl font-black text-white mb-6 flex items-center">
                <span class="text-4xl mr-3">🗺️</span>
                Favori Haritalar
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($user->profile->favorite_maps as $map)
                @php
                    // Harita bilgileri
                    $mapData = [
                        'Erangel' => ['emoji' => '🏞️', 'image' => 'erangel.jpg', 'gradient' => 'from-green-800 to-blue-900'],
                        'Miramar' => ['emoji' => '🏜️', 'image' => 'miramar.jpg', 'gradient' => 'from-yellow-800 to-orange-900'],
                        'Sanhok' => ['emoji' => '🌴', 'image' => 'sanhok.jpg', 'gradient' => 'from-green-700 to-emerald-900'],
                        'Vikendi' => ['emoji' => '❄️', 'image' => 'vikendi.jpg', 'gradient' => 'from-blue-700 to-cyan-900'],
                        'Livik' => ['emoji' => '🏝️', 'image' => 'livik.jpg', 'gradient' => 'from-teal-700 to-blue-900'],
                        'Karakin' => ['emoji' => '🏔️', 'image' => 'karakin.jpg', 'gradient' => 'from-stone-700 to-gray-900'],
                        'Nusa' => ['emoji' => '🌊', 'image' => 'nusa.jpg', 'gradient' => 'from-blue-600 to-indigo-900'],
                    ];
                    
                    $currentMap = $mapData[$map] ?? ['emoji' => '🗺️', 'image' => null, 'gradient' => 'from-gray-800 to-gray-900'];
                    $imagePath = '/images/maps/' . $currentMap['image'];
                    $imageExists = $currentMap['image'] && file_exists(public_path($imagePath));
                @endphp
                <div class="relative group overflow-hidden rounded-2xl border-2 border-orange-500/30 hover:border-orange-500/60 transition-all transform hover:scale-105 duration-300">
                    <div class="aspect-video relative">
                        @if($imageExists)
                            <!-- Gerçek Harita Resmi -->
                            <img src="{{ asset($imagePath) }}" 
                                 alt="{{ $map }}" 
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            <!-- Karartma Overlay -->
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-all"></div>
                        @else
                            <!-- Placeholder Gradient -->
                            <div class="w-full h-full bg-gradient-to-br {{ $currentMap['gradient'] }} flex items-center justify-center">
                                <span class="text-5xl group-hover:scale-125 transition-transform duration-300">{{ $currentMap['emoji'] }}</span>
                            </div>
                        @endif
                        
                        <!-- Glow Effect -->
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-orange-500/30 via-transparent to-transparent"></div>
                        </div>
                    </div>
                    
                    <!-- Harita İsmi -->
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/70 to-transparent p-4">
                        <div class="text-white font-bold text-center text-lg group-hover:text-orange-400 transition-colors">
                            {{ $map }}
                        </div>
                    </div>
                    
                    <!-- Hover Border Glow -->
                    <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                         style="box-shadow: inset 0 0 20px rgba(249, 115, 22, 0.3);"></div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Arkadaşlar -->
        <div class="mb-8 animate-fade-in">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-3xl font-black text-white flex items-center">
                    <span class="text-4xl mr-3">👥</span>
                    Arkadaşlar
                </h2>
                <a href="{{ route('friends.index') }}" class="text-orange-400 hover:text-orange-300 font-semibold">
                    Tümünü Gör →
                </a>
            </div>
            
            @php
                $friends = \App\Models\Friendship::where(function($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhere('friend_id', $user->id);
                })->where('status', 'accepted')->with(['user', 'friend'])->take(8)->get();
            @endphp

            @if($friends->count() > 0)
            <div class="grid grid-cols-4 md:grid-cols-8 gap-4">
                @foreach($friends as $friendship)
                    @php
                        $friend = $friendship->user_id === $user->id ? $friendship->friend : $friendship->user;
                    @endphp
                    <a href="{{ route('profile.show', $friend->id) }}" class="group">
                        <div class="relative">
                            <div class="w-full aspect-square rounded-2xl overflow-hidden border-2 border-white/10 group-hover:border-orange-500/50 transition-all">
                                @if($friend->profile && $friend->profile->avatar_path)
                                    <img src="{{ $friend->profile->avatar_url }}" alt="{{ $friend->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center text-white text-2xl font-black">
                                        {{ substr($friend->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-gray-900"></div>
                        </div>
                        <div class="text-center mt-2 text-xs text-gray-300 truncate">{{ $friend->name }}</div>
                    </a>
                @endforeach
            </div>
            @else
            <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-8 text-center">
                <div class="text-6xl mb-4">😢</div>
                <p class="text-gray-400">Henüz arkadaşın yok. Hemen arkadaş ekle!</p>
                <a href="{{ route('friends.index') }}" class="inline-block mt-4 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold hover:from-orange-600 hover:to-red-600 transition-all">
                    Arkadaş Ekle
                </a>
            </div>
            @endif
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- İlanlarım -->
            <div class="bg-gradient-to-br from-gray-800/50 via-gray-900/50 to-black/50 backdrop-blur-xl rounded-2xl border border-white/10 shadow-xl p-6 animate-fade-in">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <span class="text-2xl mr-2">📢</span>
                        İlanlarım
                    </h3>
                    <span class="px-3 py-1 bg-orange-500/20 border border-orange-500/30 rounded-full text-orange-300 text-sm font-bold">
                        {{ $user->lfgPosts()->count() }}
                    </span>
                </div>
                <a href="{{ route('lfg.index') }}" class="block w-full px-4 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold hover:from-orange-600 hover:to-red-600 transition-all text-center">
                    Tümünü Gör →
                </a>
            </div>

            <!-- Klanlarım -->
            <div class="bg-gradient-to-br from-gray-800/50 via-gray-900/50 to-black/50 backdrop-blur-xl rounded-2xl border border-white/10 shadow-xl p-6 animate-fade-in" style="animation-delay: 0.1s;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <span class="text-2xl mr-2">🛡️</span>
                        Klanlarım
                    </h3>
                    <span class="px-3 py-1 bg-blue-500/20 border border-blue-500/30 rounded-full text-blue-300 text-sm font-bold">
                        {{ $user->clans()->count() }}
                    </span>
                </div>
                <a href="{{ route('clans.index') }}" class="block w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-purple-600 transition-all text-center">
                    Tümünü Gör →
                </a>
            </div>

            <!-- Takımlarım -->
            <div class="bg-gradient-to-br from-gray-800/50 via-gray-900/50 to-black/50 backdrop-blur-xl rounded-2xl border border-white/10 shadow-xl p-6 animate-fade-in" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <span class="text-2xl mr-2">⚔️</span>
                        Takımlarım
                    </h3>
                    <span class="px-3 py-1 bg-green-500/20 border border-green-500/30 rounded-full text-green-300 text-sm font-bold">
                        {{ $user->squads()->count() }}
                    </span>
                </div>
                <a href="{{ route('squads.index') }}" class="block w-full px-4 py-3 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-xl font-semibold hover:from-green-600 hover:to-teal-600 transition-all text-center">
                    Tümünü Gör →
                </a>
            </div>
        </div>

        <!-- Son Aktiviteler -->
        <div class="mb-8 animate-fade-in">
            <h2 class="text-3xl font-black text-white mb-6 flex items-center">
                <span class="text-4xl mr-3">⚡</span>
                Son Aktiviteler
            </h2>
            
            <div class="bg-gradient-to-br from-gray-800/50 via-gray-900/50 to-black/50 backdrop-blur-xl rounded-2xl border border-white/10 shadow-xl p-6">
                @php
                    $recentActivities = collect();
                    
                    // LFG İlanları
                    $user->lfgPosts()->latest()->take(3)->get()->each(function($post) use (&$recentActivities) {
                        $recentActivities->push([
                            'type' => 'lfg',
                            'icon' => '📢',
                            'title' => 'Yeni ilan oluşturdu',
                            'description' => $post->title,
                            'url' => route('lfg.show', $post->id),
                            'time' => $post->created_at
                        ]);
                    });
                    
                    // Topluluk Postları
                    $user->communityPosts()->latest()->take(3)->get()->each(function($post) use (&$recentActivities) {
                        $recentActivities->push([
                            'type' => 'community',
                            'icon' => '💬',
                            'title' => 'Toplulukta paylaşım yaptı',
                            'description' => \Str::limit($post->content, 50),
                            'url' => route('community.show', $post->id),
                            'time' => $post->created_at
                        ]);
                    });
                    
                    // Rehber Yazıları
                    $user->guidePosts()->latest()->take(3)->get()->each(function($guide) use (&$recentActivities) {
                        $recentActivities->push([
                            'type' => 'guide',
                            'icon' => '📚',
                            'title' => 'Yeni rehber yazdı',
                            'description' => $guide->title,
                            'url' => route('guide.show', $guide->id),
                            'time' => $guide->created_at
                        ]);
                    });
                    
                    // XP Kazanımları
                    $user->xpEvents()->latest()->take(5)->get()->each(function($event) use (&$recentActivities) {
                        $recentActivities->push([
                            'type' => 'xp',
                            'icon' => '⭐',
                            'title' => 'XP kazandı',
                            'description' => "+{$event->xp_amount} XP - {$event->type}",
                            'url' => route('xp.history'),
                            'time' => $event->created_at
                        ]);
                    });
                    
                    $recentActivities = $recentActivities->sortByDesc('time')->take(10);
                @endphp

                @if($recentActivities->count() > 0)
                <div class="space-y-4">
                    @foreach($recentActivities as $activity)
                    <a href="{{ $activity['url'] }}" class="block group">
                        <div class="flex items-start gap-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all">
                            <div class="text-3xl">{{ $activity['icon'] }}</div>
                            <div class="flex-1">
                                <div class="text-white font-semibold group-hover:text-orange-400 transition-colors">{{ $activity['title'] }}</div>
                                <div class="text-gray-400 text-sm mt-1">{{ $activity['description'] }}</div>
                                <div class="text-gray-500 text-xs mt-2">{{ $activity['time']->diffForHumans() }}</div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="text-6xl mb-4">🌟</div>
                    <p class="text-gray-400">Henüz aktivite yok. Hemen başla!</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Son Maçlar (Matchmaking Geçmişi) -->
        @php
            $recentMatches = $user->matchmakingHistory()->latest()->take(5)->get();
        @endphp
        @if($recentMatches->count() > 0)
        <div class="mb-8 animate-fade-in">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-3xl font-black text-white flex items-center">
                    <span class="text-4xl mr-3">🎯</span>
                    Son Maçlar
                </h2>
                <a href="{{ route('matchmaking.history') }}" class="text-orange-400 hover:text-orange-300 font-semibold">
                    Tümünü Gör →
                </a>
            </div>
            
            <div class="grid gap-4">
                @foreach($recentMatches as $match)
                <div class="bg-gradient-to-br from-gray-800/50 via-gray-900/50 to-black/50 backdrop-blur-xl rounded-2xl border border-white/10 shadow-xl p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="text-4xl">
                                @if($match->status === 'completed') ✅
                                @elseif($match->status === 'cancelled') ❌
                                @else ⏳
                                @endif
                            </div>
                            <div>
                                <div class="text-white font-bold">{{ ucfirst($match->game_mode) }} - {{ ucfirst($match->match_type) }}</div>
                                <div class="text-gray-400 text-sm">{{ $match->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-orange-400 font-bold">{{ ucfirst($match->status) }}</div>
                            <div class="text-gray-500 text-sm">{{ $match->duration ?? 0 }} dk</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Profil Tamamlanma Çubuğu -->
        @if($user->profile && !$user->profile->is_complete)
        <div class="mb-8 bg-gradient-to-br from-yellow-900/30 via-orange-900/30 to-red-900/30 backdrop-blur-xl rounded-2xl border border-yellow-500/30 shadow-xl p-6 animate-fade-in">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <span class="text-2xl mr-2">📋</span>
                    Profil Tamamlanma
                </h3>
                <span class="text-2xl font-black text-yellow-400">{{ $user->profile_completion ?? 0 }}%</span>
            </div>
            <div class="w-full bg-gray-700/50 rounded-full h-4 mb-3 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-500 via-orange-500 to-red-500 h-4 rounded-full transition-all duration-500" 
                     style="width: {{ $user->profile_completion ?? 0 }}%"></div>
            </div>
            <p class="text-gray-300 text-sm">Profilini tamamla ve daha fazla özelliğe erişim kazan! 🎯</p>
        </div>
        @endif



    </div>
</div>

<style>
/* Temel Animasyonlar */
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scale-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.1); }
}

/* Arka Plan Animasyonları */
@keyframes float {
    0%, 100% { 
        transform: translate(0, 0) scale(1);
        opacity: 0.3;
    }
    33% { 
        transform: translate(30px, -30px) scale(1.1);
        opacity: 0.4;
    }
    66% { 
        transform: translate(-20px, 20px) scale(0.9);
        opacity: 0.35;
    }
}

@keyframes float-delayed {
    0%, 100% { 
        transform: translate(0, 0) scale(1);
        opacity: 0.3;
    }
    33% { 
        transform: translate(-30px, 30px) scale(1.1);
        opacity: 0.4;
    }
    66% { 
        transform: translate(20px, -20px) scale(0.9);
        opacity: 0.35;
    }
}

@keyframes draw {
    0% { 
        stroke-dasharray: 0, 1000;
        opacity: 0;
    }
    50% {
        opacity: 0.5;
    }
    100% { 
        stroke-dasharray: 1000, 0;
        opacity: 0;
    }
}

@keyframes draw-delayed {
    0% { 
        stroke-dasharray: 0, 1000;
        opacity: 0;
    }
    50% {
        opacity: 0.5;
    }
    100% { 
        stroke-dasharray: 1000, 0;
        opacity: 0;
    }
}

/* Animasyon Sınıfları */
.animate-fade-in {
    animation: fade-in 0.6s ease-out forwards;
}

.animate-scale-in {
    animation: scale-in 0.6s ease-out forwards;
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 10s ease-in-out infinite;
    animation-delay: 2s;
}

.animate-draw {
    animation: draw 15s linear infinite;
}

.animate-draw-delayed {
    animation: draw-delayed 15s linear infinite;
    animation-delay: 7.5s;
}

/* Ek Görsel Efektler */
.backdrop-blur-xl {
    backdrop-filter: blur(16px);
}
</style>
@endsection
