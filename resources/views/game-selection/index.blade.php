@extends('layouts.app')

@section('title', 'Oyun Seç - SquadBul')

@section('content')
<!-- Hero Section -->
<div class="relative min-h-screen overflow-hidden bg-black">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{ asset('images/analogolar/anasayfaarka.jpg') }}" 
             alt="Background" 
             class="w-full h-full object-cover opacity-30">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/80 via-purple-900/50 to-black/80"></div>
    </div>
    
    <!-- Video Background Effect -->
    <div class="absolute inset-0">
        <!-- Floating Orbs -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-purple-600/20 rounded-full mix-blend-screen filter blur-3xl animate-float"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-pink-600/20 rounded-full mix-blend-screen filter blur-3xl animate-float-delayed"></div>
        <div class="absolute bottom-20 left-1/3 w-80 h-80 bg-blue-600/20 rounded-full mix-blend-screen filter blur-3xl animate-float-slow"></div>
        
        <!-- Grid Pattern -->
        <div class="absolute inset-0 bg-grid-white opacity-5"></div>
        
        <!-- Scanline Effect -->
        <div class="absolute inset-0 bg-scanline opacity-5 animate-scan"></div>
    </div>

    <div class="relative z-10 container mx-auto px-4 py-16">
        <!-- Header -->
        <div class="text-center mb-20 space-y-6">
            <!-- Badge -->
            <div class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-purple-500/10 via-pink-500/10 to-blue-500/10 border border-purple-500/30 rounded-full backdrop-blur-xl animate-fade-in">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-purple-500"></span>
                </span>
                <span class="text-purple-300 font-bold text-sm tracking-wider">MULTI-GAME PLATFORM</span>
            </div>

            <!-- Main Title -->
            <h1 class="text-6xl md:text-8xl font-black leading-tight animate-fade-in-up">
                <span class="block text-white mb-2">Hangi Oyunda</span>
                <span class="block bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400 bg-clip-text text-transparent animate-gradient-x">
                    Efsane Olacaksın?
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-gray-400 max-w-3xl mx-auto font-light animate-fade-in-up animation-delay-200">
                Oyununu seç, <span class="text-purple-400 font-bold">takımını kur</span>, 
                <span class="text-pink-400 font-bold">rakiplerini ez</span>, 
                <span class="text-blue-400 font-bold">zirveye çık!</span>
            </p>

            <!-- Stats -->
            <div class="flex flex-wrap justify-center gap-8 pt-8 animate-fade-in-up animation-delay-400">
                <div class="text-center">
                    <div class="text-4xl font-black text-white mb-1">5+</div>
                    <div class="text-sm text-gray-500 uppercase tracking-wider">Oyun</div>
                </div>
                <div class="w-px h-16 bg-gradient-to-b from-transparent via-purple-500/50 to-transparent"></div>
                <div class="text-center">
                    <div class="text-4xl font-black text-white mb-1">10K+</div>
                    <div class="text-sm text-gray-500 uppercase tracking-wider">Oyuncu</div>
                </div>
                <div class="w-px h-16 bg-gradient-to-b from-transparent via-purple-500/50 to-transparent"></div>
                <div class="text-center">
                    <div class="text-4xl font-black text-white mb-1">24/7</div>
                    <div class="text-sm text-gray-500 uppercase tracking-wider">Aktif</div>
                </div>
            </div>
        </div>

        <!-- Games Grid -->
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $gameLogos = [
                        'pubg' => 'pubgmoile.jpg',
                        'valorant' => 'valo.webp',
                        'cod' => 'callofduty.jpg',
                        'csgo' => 'csgo.png',
                        'lol' => 'lol.jpg',
                    ];
                @endphp

                @foreach($games as $game)
                <div class="group relative animate-fade-in-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                    <a href="{{ route('game.select', $game->slug) }}" class="block">
                        <!-- Card -->
                        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-xl border border-white/10 transition-all duration-500 hover:border-purple-500/50 hover:scale-105 hover:-translate-y-2">
                            
                            <!-- Glow Effect -->
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-600/0 via-pink-600/0 to-blue-600/0 group-hover:from-purple-600/20 group-hover:via-pink-600/20 group-hover:to-blue-600/20 transition-all duration-500"></div>
                            
                            <!-- Shine Effect -->
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            </div>

                            <!-- Image Container -->
                            <div class="relative aspect-video overflow-hidden">
                                @if(isset($gameLogos[$game->slug]))
                                    <img src="{{ asset('images/analogolar/' . $gameLogos[$game->slug]) }}" 
                                         alt="{{ $game->name }}"
                                         class="w-full h-full object-cover transition-all duration-700 group-hover:scale-125 group-hover:rotate-2">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-purple-600 via-pink-600 to-blue-600 flex items-center justify-center">
                                        <span class="text-8xl">🎮</span>
                                    </div>
                                @endif
                                
                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
                                
                                <!-- Status Badge -->
                                @auth
                                    @if($game->has_profile ?? false)
                                        <div class="absolute top-4 right-4 z-10">
                                            <div class="flex items-center gap-2 px-4 py-2 bg-green-500/90 backdrop-blur-xl rounded-full shadow-2xl shadow-green-500/50 animate-pulse-slow">
                                                <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
                                                <span class="text-white text-sm font-black">AKTİF</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="absolute top-4 right-4 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <div class="px-4 py-2 bg-purple-500/90 backdrop-blur-xl rounded-full shadow-2xl">
                                                <span class="text-white text-sm font-black">BAŞLA</span>
                                            </div>
                                        </div>
                                    @endif
                                @endauth
                            </div>

                            <!-- Content -->
                            <div class="relative p-6 space-y-4">
                                <!-- Game Name -->
                                <div class="flex items-center justify-between">
                                    <h3 class="text-3xl font-black text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-purple-400 group-hover:to-pink-400 group-hover:bg-clip-text transition-all">
                                        {{ $game->name }}
                                    </h3>
                                    
                                    <!-- Arrow -->
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center transform group-hover:translate-x-2 group-hover:scale-110 transition-all shadow-lg shadow-purple-500/50">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Description -->
                                @if($game->description)
                                    <p class="text-gray-400 text-sm line-clamp-2 group-hover:text-gray-300 transition-colors">
                                        {{ $game->description }}
                                    </p>
                                @endif

                                <!-- Features -->
                                <div class="flex items-center gap-4 pt-2">
                                    <div class="flex items-center gap-2 text-purple-400">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                        </svg>
                                        <span class="text-sm font-bold">Topluluk</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-pink-400">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm font-bold">Hızlı Eşleşme</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Animated Border -->
                            <div class="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                                <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-purple-500 via-pink-500 to-blue-500 opacity-50 blur-xl"></div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- CTA Section -->
        @guest
        <div class="text-center mt-24 animate-fade-in-up animation-delay-600">
            <div class="relative inline-block">
                <!-- Glow -->
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 via-pink-600 to-blue-600 rounded-3xl blur-3xl opacity-40 animate-pulse-slow"></div>
                
                <div class="relative bg-gradient-to-br from-gray-900/95 to-gray-800/95 backdrop-blur-2xl rounded-3xl p-12 border border-purple-500/30 shadow-2xl">
                    <!-- Icon -->
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl mb-6 animate-bounce-slow shadow-2xl shadow-purple-500/50">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>

                    <!-- Title -->
                    <h3 class="text-4xl font-black text-white mb-4">
                        Topluluğa Katıl!
                    </h3>
                    
                    <!-- Description -->
                    <p class="text-gray-300 text-lg max-w-md mb-8">
                        <span class="text-purple-400 font-bold">Ücretsiz</span> hesap oluştur, 
                        <span class="text-pink-400 font-bold">takım arkadaşlarını bul</span>, 
                        <span class="text-blue-400 font-bold">turnuvalara katıl!</span>
                    </p>
                    
                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" 
                           class="group relative px-8 py-4 bg-gradient-to-r from-purple-600 via-pink-600 to-blue-600 hover:from-purple-700 hover:via-pink-700 hover:to-blue-700 text-white rounded-xl font-black text-lg transition-all transform hover:scale-105 shadow-2xl shadow-purple-500/50 hover:shadow-purple-500/80 overflow-hidden">
                            <!-- Shine -->
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative z-10 flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Hemen Başla
                            </span>
                        </a>
                        
                        <a href="{{ route('login') }}" 
                           class="group px-8 py-4 bg-white/5 hover:bg-white/10 border-2 border-white/20 hover:border-white/40 text-white rounded-xl font-black text-lg transition-all backdrop-blur-xl">
                            <span class="flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Giriş Yap
                            </span>
                        </a>
                    </div>

                    <!-- Trust Badges -->
                    <div class="flex items-center justify-center gap-6 mt-8 pt-8 border-t border-white/10">
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Ücretsiz</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Güvenli</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Hızlı</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endguest
    </div>
</div>

<!-- Custom Styles -->
<style>
    @keyframes float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -30px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
    }
    
    @keyframes float-delayed {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(-30px, 30px) scale(1.1); }
        66% { transform: translate(20px, -20px) scale(0.9); }
    }
    
    @keyframes float-slow {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(0, 30px) scale(1.05); }
    }
    
    @keyframes scan {
        0% { transform: translateY(-100%); }
        100% { transform: translateY(100%); }
    }
    
    @keyframes gradient-x {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .animate-float {
        animation: float 20s ease-in-out infinite;
    }
    
    .animate-float-delayed {
        animation: float-delayed 25s ease-in-out infinite;
    }
    
    .animate-float-slow {
        animation: float-slow 30s ease-in-out infinite;
    }
    
    .animate-scan {
        animation: scan 8s linear infinite;
    }
    
    .animate-gradient-x {
        background-size: 200% 200%;
        animation: gradient-x 3s ease infinite;
    }
    
    .bg-grid-white {
        background-image: 
            linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        background-size: 50px 50px;
    }
    
    .bg-scanline {
        background: linear-gradient(
            to bottom,
            transparent 50%,
            rgba(255, 255, 255, 0.05) 50%
        );
        background-size: 100% 4px;
    }
    
    .animation-delay-200 {
        animation-delay: 0.2s;
    }
    
    .animation-delay-400 {
        animation-delay: 0.4s;
    }
    
    .animation-delay-600 {
        animation-delay: 0.6s;
    }
</style>
@endsection
