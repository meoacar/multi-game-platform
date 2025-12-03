@extends('layouts.app')

@section('title', 'Tüm Oyunlar - Aktivite Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Başlık -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">🎮 Tüm Oyunlar Dashboard</h1>
        <p class="text-gray-400">Tüm oyunlardaki aktivitelerinizi tek bir yerden görüntüleyin</p>
    </div>

    <!-- Toplam İstatistikler -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Toplam Oyun</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalStats['total_games'] }}</p>
                </div>
                <div class="bg-blue-500/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Aktif Oyun</p>
                    <p class="text-3xl font-bold text-green-500 mt-1">{{ $totalStats['active_games'] }}</p>
                </div>
                <div class="bg-green-500/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Toplam Aktivite</p>
                    <p class="text-3xl font-bold text-purple-500 mt-1">{{ $totalStats['total_activities'] }}</p>
                </div>
                <div class="bg-purple-500/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Toplam Rozet</p>
                    <p class="text-3xl font-bold text-yellow-500 mt-1">{{ $totalStats['total_badges'] }}</p>
                </div>
                <div class="bg-yellow-500/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Oyun Bazlı Aktiviteler -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-4">📊 Oyun Bazlı Aktiviteler</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($gameActivities as $activity)
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden hover:border-gray-600 transition-colors">
                <!-- Oyun Başlığı -->
                <div class="p-4 border-b border-gray-700" style="background: linear-gradient(135deg, {{ $activity['game']->getThemeColor() }}20 0%, transparent 100%);">
                    <div class="flex items-center space-x-3">
                        @if($activity['game']->logo)
                        <img src="{{ asset('storage/' . $activity['game']->logo) }}" alt="{{ $activity['game']->name }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl" style="background-color: {{ $activity['game']->getThemeColor() }}20;">
                            🎮
                        </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $activity['game']->name }}</h3>
                            <p class="text-sm text-gray-400">{{ $activity['total'] }} aktivite</p>
                        </div>
                    </div>
                </div>

                <!-- Aktivite Detayları -->
                <div class="p-4 space-y-3">
                    @if($activity['tournaments'] > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">🏆 Turnuvalar</span>
                        <span class="text-white font-semibold">{{ $activity['tournaments'] }}</span>
                    </div>
                    @endif

                    @if($activity['clans'] > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">⚔️ Klanlar</span>
                        <span class="text-white font-semibold">{{ $activity['clans'] }}</span>
                    </div>
                    @endif

                    @if($activity['lfg_posts'] > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">👥 LFG İlanları</span>
                        <span class="text-white font-semibold">{{ $activity['lfg_posts'] }}</span>
                    </div>
                    @endif

                    @if($activity['guide_posts'] > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">📖 Rehberler</span>
                        <span class="text-white font-semibold">{{ $activity['guide_posts'] }}</span>
                    </div>
                    @endif

                    @if($activity['community_posts'] > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">💬 Gönderiler</span>
                        <span class="text-white font-semibold">{{ $activity['community_posts'] }}</span>
                    </div>
                    @endif

                    @if($activity['badges'] > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">⭐ Rozetler</span>
                        <span class="text-white font-semibold">{{ $activity['badges'] }}</span>
                    </div>
                    @endif

                    @if($activity['total'] == 0)
                    <p class="text-gray-500 text-sm text-center py-2">Henüz aktivite yok</p>
                    @endif
                </div>

                <!-- Oyuna Git Butonu -->
                <div class="p-4 border-t border-gray-700">
                    <a href="http://{{ $activity['game']->slug }}.{{ config('app.domain', 'takimsistemi.com') }}" 
                       class="block w-full text-center py-2 rounded-lg font-semibold transition-colors"
                       style="background-color: {{ $activity['game']->getThemeColor() }}; color: white;">
                        Oyuna Git →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Cross-Game Aktiviteler -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-4">🌐 Platform Geneli Aktiviteler</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-500/20 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Mesajlar</p>
                        <p class="text-2xl font-bold text-white">{{ $crossGameActivities['messages'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center space-x-4">
                    <div class="bg-green-500/20 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Arkadaşlar</p>
                        <p class="text-2xl font-bold text-white">{{ $crossGameActivities['friendships'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex items-center space-x-4">
                    <div class="bg-purple-500/20 p-3 rounded-lg">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Bildirimler</p>
                        <p class="text-2xl font-bold text-white">{{ $crossGameActivities['notifications'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Aktiviteler -->
    @if($recentActivities->count() > 0)
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-4">⏱️ Son Aktiviteler</h2>
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="divide-y divide-gray-700">
                @foreach($recentActivities as $activity)
                <div class="p-4 hover:bg-gray-750 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3 flex-1">
                            <!-- Oyun Logosu -->
                            @if($activity['game']->logo)
                            <img src="{{ asset('storage/' . $activity['game']->logo) }}" alt="{{ $activity['game']->name }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl" style="background-color: {{ $activity['game']->getThemeColor() }}20;">
                                🎮
                            </div>
                            @endif

                            <!-- Aktivite Bilgisi -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-xs px-2 py-1 rounded" style="background-color: {{ $activity['game']->getThemeColor() }}20; color: {{ $activity['game']->getThemeColor() }};">
                                        {{ $activity['game']->name }}
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded bg-gray-700 text-gray-300">
                                        @if($activity['type'] == 'lfg_post')
                                            👥 LFG İlanı
                                        @elseif($activity['type'] == 'guide_post')
                                            📖 Rehber
                                        @elseif($activity['type'] == 'community_post')
                                            💬 Gönderi
                                        @endif
                                    </span>
                                </div>
                                <a href="{{ $activity['url'] }}" class="text-white hover:text-blue-400 transition-colors font-medium">
                                    {{ $activity['title'] }}
                                </a>
                                <p class="text-gray-400 text-sm mt-1">
                                    {{ $activity['created_at']->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <!-- Git Butonu -->
                        <a href="{{ $activity['url'] }}" class="text-blue-400 hover:text-blue-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Geri Dön Butonu -->
    <div class="text-center">
        <a href="{{ route('profile.index') }}" class="inline-flex items-center space-x-2 px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Profilime Dön</span>
        </a>
    </div>
</div>
@endsection
