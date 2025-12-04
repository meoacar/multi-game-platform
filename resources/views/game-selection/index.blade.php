@extends('layouts.app')

@section('title', 'Oyun Seç - SquadBul')

@section('content')
<!-- Hero Section with Animated Background -->
<div class="relative min-h-screen overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-black via-purple-950 to-black">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-0 left-1/3 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
        </div>
        <!-- Grid Pattern -->
        <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
    </div>

    <div class="relative container mx-auto px-4 py-20">
        <!-- Header -->
        <div class="text-center mb-20 animate-fade-in">
            <div class="inline-block mb-6">
                <span class="px-6 py-2 bg-gradient-to-r from-purple-500/20 to-pink-500/20 border border-purple-500/30 rounded-full text-purple-300 text-sm font-bold backdrop-blur-xl">
                    🎮 Multi-Game Platform
                </span>
            </div>
            <h1 class="text-7xl md:text-8xl font-black text-white mb-6 leading-tight">
                <span class="bg-gradient-to-r from-purple-400 via-pink-400 to-red-400 bg-clip-text text-transparent animate-gradient">
                    Hangi Oyunu
                </span>
                <br>
                <span class="text-white">Oynuyorsun?</span>
            </h1>
            <p class="text-2xl text-gray-300 max-w-2xl mx-auto font-light">
                Oyununu seç, <span class="text-purple-400 font-bold">takım arkadaşlarını bul</span>, 
                <span class="text-pink-400 font-bold">efsane ol!</span>
            </p>
        </div>

        <!-- Oyun Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            @foreach($games as $game)
            <a href="{{ route('game.select', $game->slug) }}" 
               class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-xl border border-purple-500/20 hover:border-purple-500/60 transition-all duration-500 transform hover:scale-105 hover:-translate-y-2 animate-fade-in"
               style="animation-delay: {{ $loop->index * 0.1 }}s;">
                
                <!-- Glow Effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/0 via-pink-500/0 to-red-500/0 group-hover:from-purple-500/20 group-hover:via-pink-500/20 group-hover:to-red-500/20 transition-all duration-500 rounded-3xl"></div>
                
                <!-- Oyun Görseli -->
                <div class="aspect-video relative overflow-hidden">
                    @if($game->banner_image)
                        <img src="{{ asset('storage/' . $game->banner_image) }}" 
                             alt="{{ $game->name }}"
                             class="w-full h-full object-cover group-hover:scale-125 group-hover:rotate-2 transition-all duration-700">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-purple-600 via-pink-600 to-red-600 flex items-center justify-center relative overflow-hidden">
                            <!-- Animated Background Pattern -->
                            <div class="absolute inset-0 opacity-20">
                                <div class="absolute top-0 left-0 w-full h-full bg-grid-pattern animate-pulse-slow"></div>
                            </div>
                            <span class="text-8xl animate-bounce-slow relative z-10">🎮</span>
                        </div>
                    @endif
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-80 group-hover:opacity-60 transition-opacity"></div>
                    
                    <!-- Badge -->
                    @auth
                        @if($game->has_profile ?? false)
                            <div class="absolute top-4 right-4 z-10">
                                <span class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-bold rounded-full shadow-2xl shadow-green-500/50 flex items-center gap-2 animate-pulse-slow">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Aktif
                                </span>
                            </div>
                        @endif
                    @endauth
                </div>

                <!-- Oyun Bilgileri -->
                <div class="relative p-8">
                    <!-- Shine Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    
                    <div class="relative">
                        <h3 class="text-3xl font-black text-white mb-3 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-purple-400 group-hover:to-pink-400 group-hover:bg-clip-text transition-all">
                            {{ $game->name }}
                        </h3>

                        @if($game->description)
                            <p class="text-gray-400 text-sm mb-6 line-clamp-2 group-hover:text-gray-300 transition-colors">
                                {{ $game->description }}
                            </p>
                        @endif

                        <!-- Stats -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-purple-400 font-semibold">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                <span class="text-sm">Topluluğa Katıl</span>
                            </div>
                            
                            <!-- Arrow Icon -->
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center transform group-hover:translate-x-2 transition-transform shadow-lg shadow-purple-500/50">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Animated Border -->
                <div class="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 opacity-50 blur-xl"></div>
                </div>
            </a>
            @endforeach
        </div>

        @guest
        <!-- Giriş Yap Çağrısı -->
        <div class="text-center mt-20 animate-fade-in" style="animation-delay: 0.5s;">
            <div class="relative inline-block">
                <!-- Glow Effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 rounded-3xl blur-2xl opacity-30 animate-pulse-slow"></div>
                
                <div class="relative bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-xl rounded-3xl p-12 border border-purple-500/30 shadow-2xl">
                    <div class="mb-6">
                        <div class="inline-block p-4 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl mb-4 animate-bounce-slow">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-black text-white mb-3">
                            Topluluğa Katıl!
                        </h3>
                        <p class="text-gray-300 text-lg max-w-md">
                            Profilini oluştur, <span class="text-purple-400 font-bold">takım arkadaşlarını bul</span>, 
                            <span class="text-pink-400 font-bold">turnuvalara katıl!</span>
                        </p>
                    </div>
                    
                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('login') }}" 
                           class="group relative px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-bold transition-all transform hover:scale-105 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50">
                            <span class="relative z-10 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Giriş Yap
                            </span>
                        </a>
                        <a href="{{ route('register') }}" 
                           class="group relative px-8 py-4 bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 hover:from-purple-700 hover:via-pink-700 hover:to-red-700 text-white rounded-xl font-bold transition-all transform hover:scale-105 shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 overflow-hidden">
                            <!-- Shine Effect -->
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <span class="relative z-10 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                Kayıt Ol
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endguest
    </div>
</div>

<!-- Custom Animations -->
<style>
    @keyframes blob {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(20px, -50px) scale(1.1); }
        50% { transform: translate(-20px, 20px) scale(0.9); }
        75% { transform: translate(50px, 50px) scale(1.05); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }
    .bg-grid-pattern {
        background-image: 
            linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        background-size: 50px 50px;
    }
</style>
@endsection
