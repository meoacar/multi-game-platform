@extends('layouts.app')

@section('title', 'Bildirimler - PUBG Mobile Topluluk')

@section('content')
<!-- Hero Section with Background -->
<div class="relative min-h-screen overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-black"></div>
        <div class="absolute inset-0 opacity-20">
            <img src="{{ asset('arkaplan/14.jpg') }}" alt="Background" class="w-full h-full object-cover">
        </div>
        <!-- Animated Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 via-red-500/10 to-purple-500/10 animate-pulse"></div>
        
        <!-- Floating Particles -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-float-delayed"></div>
            <div class="absolute bottom-1/4 left-1/3 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl animate-float-slow"></div>
        </div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-orange-500/20 to-red-500/20 rounded-full border border-orange-500/30 mb-6 animate-float">
                <span class="text-3xl">🔔</span>
                <span class="text-orange-400 font-bold">Bildirim Merkezi</span>
            </div>
            
            <h1 class="text-5xl md:text-6xl font-black mb-4">
                <span class="bg-gradient-to-r from-orange-400 via-red-500 to-purple-500 bg-clip-text text-transparent">
                    Bildirimleriniz
                </span>
            </h1>
            
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Tüm aktivitelerinizi ve güncellemelerinizi buradan takip edin
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <!-- Toplam Bildirim -->
            <div class="glass-card p-6 rounded-2xl border border-white/10 hover:border-orange-500/50 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Toplam Bildirim</p>
                        <p class="text-3xl font-black text-white">{{ $notifications->total() }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500/20 to-red-500/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="text-3xl">📬</span>
                    </div>
                </div>
            </div>

            <!-- Okunmamış -->
            <div class="glass-card p-6 rounded-2xl border border-white/10 hover:border-red-500/50 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Okunmamış</p>
                        <p class="text-3xl font-black text-red-400">{{ $unreadCount }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500/20 to-pink-500/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="text-3xl">🔴</span>
                    </div>
                </div>
            </div>

            <!-- Bugün -->
            <div class="glass-card p-6 rounded-2xl border border-white/10 hover:border-purple-500/50 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm mb-1">Bugün</p>
                        <p class="text-3xl font-black text-purple-400">{{ $notifications->where('created_at', '>=', now()->startOfDay())->count() }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500/20 to-blue-500/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="text-3xl">📅</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-3 mb-8" x-data="{ filter: '{{ request('filter', 'all') }}' }">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
               class="px-6 py-3 rounded-xl font-bold transition-all duration-300"
               :class="filter === 'all' ? 'bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg shadow-orange-500/50' : 'bg-white/5 text-gray-400 hover:bg-white/10'">
                🔔 Tümü
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
               class="px-6 py-3 rounded-xl font-bold transition-all duration-300"
               :class="filter === 'unread' ? 'bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg shadow-orange-500/50' : 'bg-white/5 text-gray-400 hover:bg-white/10'">
                🔴 Okunmamış
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'read']) }}" 
               class="px-6 py-3 rounded-xl font-bold transition-all duration-300"
               :class="filter === 'read' ? 'bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg shadow-orange-500/50' : 'bg-white/5 text-gray-400 hover:bg-white/10'">
                ✅ Okundu
            </a>
        </div>

        <!-- Notifications List -->
        @if($notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                        $type = $notification->data['type'] ?? 'default';
                        $icon = match($type) {
                            'lfg_application' => '👥',
                            'clan_application' => '🏰',
                            'application_accepted' => '✅',
                            'application_rejected' => '❌',
                            'friend_request' => '🤝',
                            'message' => '💬',
                            'xp_earned' => '⭐',
                            'badge_unlocked' => '🏆',
                            default => '🔔'
                        };
                        $color = match($type) {
                            'lfg_application' => 'from-blue-500/20 to-cyan-500/20 border-blue-500/30',
                            'clan_application' => 'from-purple-500/20 to-pink-500/20 border-purple-500/30',
                            'application_accepted' => 'from-green-500/20 to-emerald-500/20 border-green-500/30',
                            'application_rejected' => 'from-red-500/20 to-orange-500/20 border-red-500/30',
                            'friend_request' => 'from-yellow-500/20 to-orange-500/20 border-yellow-500/30',
                            'message' => 'from-indigo-500/20 to-blue-500/20 border-indigo-500/30',
                            'xp_earned' => 'from-amber-500/20 to-yellow-500/20 border-amber-500/30',
                            'badge_unlocked' => 'from-orange-500/20 to-red-500/20 border-orange-500/30',
                            default => 'from-gray-500/20 to-slate-500/20 border-gray-500/30'
                        };
                    @endphp
                    
                    <div class="glass-card rounded-2xl border {{ $color }} p-6 hover:scale-[1.02] transition-all duration-300 group {{ $isUnread ? 'shadow-lg' : '' }}">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 bg-gradient-to-br {{ $color }} rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                    {{ $icon }}
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <h3 class="text-lg font-bold text-white">
                                        {{ $notification->data['title'] ?? 'Bildirim' }}
                                    </h3>
                                    @if($isUnread)
                                        <span class="flex-shrink-0 w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                                    @endif
                                </div>
                                
                                <p class="text-gray-300 mb-3">
                                    {{ $notification->data['message'] ?? 'Yeni bir bildiriminiz var' }}
                                </p>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 text-sm text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        @if(!$isUnread)
                                            <span class="flex items-center gap-1 text-green-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Okundu
                                            </span>
                                        @endif
                                    </div>

                                    @if(isset($notification->data['url']))
                                        <a href="{{ $notification->data['url'] }}" 
                                           class="px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-bold hover:shadow-lg hover:shadow-orange-500/50 transition-all duration-300">
                                            Görüntüle →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="glass-card rounded-3xl border border-white/10 p-16 text-center">
                <div class="w-32 h-32 bg-gradient-to-br from-orange-500/20 to-red-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-6xl">📭</span>
                </div>
                <h3 class="text-2xl font-black text-white mb-3">Henüz Bildiriminiz Yok</h3>
                <p class="text-gray-400 mb-8 max-w-md mx-auto">
                    Platformda aktivite göstermeye başladığınızda bildirimleriniz burada görünecek
                </p>
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-bold hover:shadow-lg hover:shadow-orange-500/50 transition-all duration-300">
                    <span>Ana Sayfaya Dön</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px) translateX(0px); }
    50% { transform: translateY(-30px) translateX(10px); }
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0px) translateX(0px); }
    50% { transform: translateY(-15px) translateX(-10px); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 8s ease-in-out infinite;
}

.animate-float-slow {
    animation: float-slow 10s ease-in-out infinite;
}

.glass-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
</style>
@endsection
