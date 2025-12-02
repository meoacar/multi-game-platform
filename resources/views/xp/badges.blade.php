@extends('layouts.app')

@section('title', 'Rozetlerim')

@section('content')
<!-- PUBG Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" 
     style="background-image: url('/arkaplan/6.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <!-- Karartma Katmanı -->
    <div class="absolute inset-0 bg-black/70"></div>
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-purple-900/40 via-transparent to-black/60"></div>
    <!-- Animasyonlu Işık Efektleri -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-pink-500/10 rounded-full blur-3xl animate-float-delayed"></div>
        <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-yellow-500/5 rounded-full blur-3xl animate-pulse-slow"></div>
    </div>
</div>

<div class="relative z-10 min-h-screen py-12" x-data="{ activeTab: 'unlocked' }">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Modern Hero Header -->
        <div class="text-center mb-12 animate-fade-in">
            <!-- Animated Badge Icon -->
            <div class="relative inline-block mb-6">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full blur-2xl opacity-50 animate-pulse-slow"></div>
                <div class="relative inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-purple-600 via-pink-600 to-orange-500 rounded-3xl shadow-2xl shadow-purple-500/50 transform hover:scale-110 transition-transform duration-300">
                    <svg class="w-14 h-14 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-5xl md:text-6xl font-black mb-4">
                <span class="bg-gradient-to-r from-purple-400 via-pink-400 to-orange-400 bg-clip-text text-transparent">
                    Rozet Koleksiyonum
                </span>
            </h1>
            <p class="text-gray-300 text-xl max-w-2xl mx-auto">
                Başarılarını sergile, yeni rozetler kazan ve topluluğun en iyisi ol! 🏆
            </p>
        </div>

        <!-- Modern Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 animate-slide-up">
            <!-- Kazanılan Rozetler -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-emerald-500 rounded-3xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity"></div>
                <div class="relative bg-gradient-to-br from-green-600 to-emerald-600 rounded-3xl shadow-2xl shadow-green-500/30 p-8 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-white/80 text-sm font-medium mb-1">Kazanılan</p>
                            <p class="text-5xl font-black">{{ $unlockedBadges->count() }}</p>
                        </div>
                    </div>
                    <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full bg-white rounded-full" style="width: {{ $unlockedBadges->count() > 0 ? round(($unlockedBadges->count() / ($unlockedBadges->count() + $lockedBadges->count())) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Kilitli Rozetler -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-gray-500 to-gray-600 rounded-3xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity"></div>
                <div class="relative bg-gradient-to-br from-gray-700 to-gray-800 rounded-3xl shadow-2xl shadow-gray-500/30 p-8 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-white/80 text-sm font-medium mb-1">Kilitli</p>
                            <p class="text-5xl font-black">{{ $lockedBadges->count() }}</p>
                        </div>
                    </div>
                    <p class="text-white/60 text-sm">Daha fazla XP kazan!</p>
                </div>
            </div>

            <!-- Tamamlanma Yüzdesi -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 rounded-3xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity"></div>
                <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 rounded-3xl shadow-2xl shadow-purple-500/30 p-8 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-white/80 text-sm font-medium mb-1">Tamamlanma</p>
                            <p class="text-5xl font-black">{{ $unlockedBadges->count() > 0 ? round(($unlockedBadges->count() / ($unlockedBadges->count() + $lockedBadges->count())) * 100) : 0 }}%</p>
                        </div>
                    </div>
                    <p class="text-white/60 text-sm">Koleksiyonunu tamamla!</p>
                </div>
            </div>
        </div>

        <!-- Modern Tabs -->
        <div class="relative mb-10">
            <div class="bg-gray-900/60 backdrop-blur-xl rounded-3xl shadow-2xl border border-purple-500/20 p-3">
                <div class="grid grid-cols-2 gap-3">
                    <button @click="activeTab = 'unlocked'" 
                        :class="activeTab === 'unlocked' ? 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-2xl shadow-green-500/50 scale-105' : 'text-gray-400 hover:text-white hover:bg-gray-800/50'"
                        class="relative px-8 py-5 rounded-2xl font-black text-lg transition-all duration-300 flex items-center justify-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="activeTab === 'unlocked' ? 'bg-white/20' : 'bg-gray-700/50'">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="text-sm opacity-80">Kazanılan</div>
                            <div class="text-2xl font-black">{{ $unlockedBadges->count() }}</div>
                        </div>
                    </button>
                    <button @click="activeTab = 'locked'"
                        :class="activeTab === 'locked' ? 'bg-gradient-to-r from-gray-600 to-gray-700 text-white shadow-2xl shadow-gray-500/50 scale-105' : 'text-gray-400 hover:text-white hover:bg-gray-800/50'"
                        class="relative px-8 py-5 rounded-2xl font-black text-lg transition-all duration-300 flex items-center justify-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="activeTab === 'locked' ? 'bg-white/20' : 'bg-gray-700/50'">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="text-sm opacity-80">Kilitli</div>
                            <div class="text-2xl font-black">{{ $lockedBadges->count() }}</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Kazanılan Rozetler -->
        <div x-show="activeTab === 'unlocked'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            @if($unlockedBadges->isEmpty())
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-green-500/10 p-12 border-2 border-gray-700 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-700/50 rounded-2xl mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Henüz rozet kazanmadınız</h3>
                    <p class="text-gray-400 mb-6">XP kazanarak ilk rozetinizi açın!</p>
                    <a href="{{ route('xp.leaderboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-bold shadow-lg shadow-green-500/30 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        XP Kazanmaya Başla
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($unlockedBadges as $badge)
                    @php
                        $badgeIcons = [
                            'trophy' => '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>',
                            'shield' => '<path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
                            'fire' => '<path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>',
                            'lightning' => '<path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>',
                            'heart' => '<path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>',
                            'crown' => '<path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>',
                        ];
                        $iconKey = strtolower(str_replace(' ', '', $badge->name));
                        $iconPath = $badgeIcons['trophy']; // default
                    @endphp
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-green-500/10 border-2 border-green-500/50 overflow-hidden group hover:scale-105 transition-transform duration-300">
                        <!-- Badge Header -->
                        <div class="bg-gradient-to-br from-green-600 to-emerald-600 p-8 text-center relative">
                            <div class="absolute top-3 right-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Badge Icon -->
                            <div class="inline-flex items-center justify-center w-24 h-24 bg-white/20 rounded-3xl mb-4 shadow-lg backdrop-blur-sm">
                                <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    {!! $iconPath !!}
                                </svg>
                            </div>
                            
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-full text-white text-sm font-bold backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Kazanıldı
                            </div>
                        </div>

                        <!-- Badge Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-white mb-2">{{ $badge->name }}</h3>
                            <p class="text-sm text-gray-400 mb-4">{{ $badge->description }}</p>
                            <div class="flex items-center gap-2 text-xs text-green-400 bg-green-600/20 px-3 py-2 rounded-lg border border-green-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ \Carbon\Carbon::parse($badge->pivot->unlocked_at)->format('d.m.Y') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Kilitli Rozetler -->
        <div x-show="activeTab === 'locked'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            @if($lockedBadges->isEmpty())
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-purple-500/10 p-12 border-2 border-gray-700 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl mb-6 shadow-lg shadow-yellow-500/50">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">🎉 Tüm rozetleri kazandınız!</h3>
                    <p class="text-gray-400">Harika iş! Tüm başarıları tamamladınız.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($lockedBadges as $badge)
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-gray-500/10 border-2 border-gray-700 overflow-hidden group hover:border-yellow-500/50 transition-all duration-300">
                        <!-- Badge Header -->
                        <div class="bg-gradient-to-br from-gray-700 to-gray-800 p-8 text-center relative">
                            <!-- Lock Overlay -->
                            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-10">
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-gray-600/80 rounded-2xl flex items-center justify-center mx-auto shadow-2xl">
                                        <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden Badge Icon (blurred background) -->
                            <div class="inline-flex items-center justify-center w-24 h-24 bg-white/10 rounded-3xl mb-4 blur-sm opacity-30">
                                <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            
                            <div class="inline-flex items-center gap-1 px-3 py-1 bg-gray-600/50 rounded-full text-gray-400 text-xs font-bold">
                                Kilitli
                            </div>
                        </div>

                        <!-- Badge Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-white mb-2">{{ $badge->name }}</h3>
                            <p class="text-sm text-gray-400 mb-4">{{ $badge->description }}</p>
                            
                            @if($badge->xp_required)
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg border border-gray-600">
                                    <span class="text-xs text-gray-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Hedef
                                    </span>
                                    <span class="font-bold text-yellow-400">{{ number_format($badge->xp_required) }} XP</span>
                                </div>
                                
                                @php
                                    $userXp = auth()->user()->xp_total ?? 0;
                                    $percentage = min(100, ($userXp / $badge->xp_required) * 100);
                                    $remaining = $badge->xp_required - $userXp;
                                @endphp
                                
                                <!-- Progress Bar -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-400">İlerleme</span>
                                        <span class="font-bold text-white">{{ round($percentage) }}%</span>
                                    </div>
                                    <div class="relative w-full bg-gray-700 rounded-full h-4 overflow-hidden border border-gray-600">
                                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-cyan-500/20"></div>
                                        <div class="relative bg-gradient-to-r from-blue-500 to-cyan-500 h-4 rounded-full transition-all duration-500 flex items-center justify-center" style="width: {{ $percentage }}%">
                                            @if($percentage > 15)
                                            <span class="text-xs font-bold text-white drop-shadow">{{ round($percentage) }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Stats -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="p-2 bg-blue-600/20 rounded-lg border border-blue-500/30 text-center">
                                        <p class="text-xs text-blue-300 mb-1">Mevcut</p>
                                        <p class="text-sm font-bold text-white">{{ number_format($userXp) }}</p>
                                    </div>
                                    <div class="p-2 bg-orange-600/20 rounded-lg border border-orange-500/30 text-center">
                                        <p class="text-xs text-orange-300 mb-1">Kalan</p>
                                        <p class="text-sm font-bold text-white">{{ number_format($remaining > 0 ? $remaining : 0) }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Modern Bilgi Kartı -->
        <div class="mt-12 relative group">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-3xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity"></div>
            <div class="relative bg-gradient-to-r from-blue-600 via-cyan-600 to-teal-600 rounded-3xl shadow-2xl shadow-blue-500/30 p-8 text-white">
                <div class="flex items-start gap-6">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 transform group-hover:scale-110 transition-transform">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-black mb-3 flex items-center gap-2">
                            💡 Rozetler Nasıl Kazanılır?
                        </h3>
                        <p class="text-white/90 text-lg leading-relaxed mb-4">
                            Rozetler, belirli XP seviyelerine ulaştığınızda otomatik olarak kazanılır. 
                            Daha fazla XP kazanmak için aktivitelere katılın, içerik oluşturun ve topluluğa katkıda bulunun!
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <div class="text-3xl mb-2">🎯</div>
                                <div class="font-bold mb-1">İlan Oluştur</div>
                                <div class="text-sm text-white/70">+30 XP</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <div class="text-3xl mb-2">🛡️</div>
                                <div class="font-bold mb-1">Klan Kur</div>
                                <div class="text-sm text-white/70">+100 XP</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <div class="text-3xl mb-2">📚</div>
                                <div class="font-bold mb-1">Rehber Yaz</div>
                                <div class="text-sm text-white/70">+40 XP</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Animations -->
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    @keyframes float-delayed {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    @keyframes pulse-slow {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 0.8; }
    }
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-float { animation: float 6s ease-in-out infinite; }
    .animate-float-delayed { animation: float-delayed 6s ease-in-out infinite 3s; }
    .animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
    .animate-fade-in { animation: fade-in 0.6s ease-out; }
    .animate-slide-up { animation: slide-up 0.8s ease-out; }
</style>
@endsection
