@extends('layouts.app')

@section('title', 'XP Geçmişi')

@section('content')
<!-- PUBG Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" 
     style="background-image: url('/arkaplan/16.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    
    <!-- Karartma Katmanı -->
    <div class="absolute inset-0 bg-black/70"></div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-purple-900/50 via-transparent to-black/70"></div>
    
    <!-- Animasyonlu Işık Efektleri -->
    <div class="absolute inset-0">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-duration: 4s;"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse" style="animation-duration: 6s; animation-delay: 1s;"></div>
    </div>
</div>

<!-- Hero Banner - Yeni Tasarım -->
<div class="relative z-10 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Üst Bilgi Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Level Kartı -->
            <div class="group relative bg-gradient-to-br from-yellow-500/20 via-orange-500/20 to-red-500/20 backdrop-blur-xl rounded-3xl border-2 border-yellow-500/30 p-8 hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-yellow-500/50">
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-orange-500/10 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                <div class="relative text-center">
                    <div class="text-6xl mb-3 animate-pulse">⭐</div>
                    <div class="text-5xl font-black text-yellow-400 mb-2">{{ $level }}</div>
                    <div class="text-sm font-bold text-yellow-300 uppercase tracking-wider">Seviye</div>
                </div>
            </div>

            <!-- Toplam XP Kartı -->
            <div class="group relative bg-gradient-to-br from-purple-500/20 via-blue-500/20 to-cyan-500/20 backdrop-blur-xl rounded-3xl border-2 border-purple-500/30 p-8 hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/50">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-blue-500/10 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                <div class="relative text-center">
                    <div class="text-6xl mb-3">💎</div>
                    <div class="text-4xl font-black text-purple-400 mb-2">{{ number_format(auth()->user()->xp_total ?? 0) }}</div>
                    <div class="text-sm font-bold text-purple-300 uppercase tracking-wider">Toplam XP</div>
                </div>
            </div>

            <!-- İlerleme Kartı -->
            <div class="group relative bg-gradient-to-br from-green-500/20 via-emerald-500/20 to-teal-500/20 backdrop-blur-xl rounded-3xl border-2 border-green-500/30 p-8 hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-green-500/50">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                <div class="relative text-center">
                    <div class="text-6xl mb-3">🚀</div>
                    <div class="text-4xl font-black text-green-400 mb-2">{{ $progress['percentage'] }}%</div>
                    <div class="text-sm font-bold text-green-300 uppercase tracking-wider">Sonraki Level</div>
                </div>
            </div>
        </div>

        <!-- Başlık -->
        <div class="text-center mb-8">
            <h1 class="text-7xl font-black mb-4 bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400 bg-clip-text text-transparent drop-shadow-2xl animate-pulse">
                XP GEÇMİŞİN
            </h1>
            <p class="text-2xl text-white/90 font-bold">Her başarın burada kayıtlı! 🎯</p>
        </div>

        <!-- İlerleme Çubuğu -->
        <div class="max-w-4xl mx-auto mb-8">
            <div class="bg-black/50 backdrop-blur-xl rounded-full h-8 border-2 border-purple-500/30 overflow-hidden shadow-2xl">
                <div class="relative h-full bg-gradient-to-r from-purple-500 via-pink-500 to-blue-500 rounded-full transition-all duration-1000 ease-out shadow-lg"
                     style="width: {{ $progress['percentage'] }}%">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent animate-pulse"></div>
                </div>
            </div>
            <div class="flex justify-between mt-3 text-sm font-bold">
                <span class="text-purple-400">{{ number_format($progress['current_xp']) }} XP</span>
                <span class="text-pink-400">{{ number_format($progress['needed_xp']) }} XP'ye {{ $level + 1 }}. Level</span>
            </div>
        </div>
    </div>
</div>

<div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Ana İçerik - XP Geçmişi -->
        <div class="lg:col-span-3">
            @if($xpHistory->isEmpty())
                <!-- Boş Durum -->
                <div class="relative group bg-gradient-to-br from-purple-900/30 via-pink-900/30 to-blue-900/30 backdrop-blur-xl rounded-3xl border-2 border-purple-500/30 p-12 text-center hover:scale-105 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-500/10 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                    <div class="relative">
                        <div class="text-9xl mb-6 animate-bounce">🎮</div>
                        <h3 class="text-4xl font-black text-white mb-4">Maceraya Başla!</h3>
                        <p class="text-xl text-gray-300 mb-8">İlk XP'ni kazanmak için aktivitelere katıl</p>
                        <div class="flex gap-4 justify-center flex-wrap">
                            <a href="{{ route('profile.edit') }}" 
                                class="group relative inline-block bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-8 py-4 rounded-2xl font-black text-lg shadow-2xl transition-all transform hover:scale-110">
                                <span class="relative z-10">👤 Profil Oluştur</span>
                                <span class="absolute top-0 right-0 bg-yellow-400 text-black px-3 py-1 rounded-full text-xs font-black -mt-2 -mr-2">+50 XP</span>
                            </a>
                            <a href="{{ route('lfg.index') }}" 
                                class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-8 py-4 rounded-2xl font-black text-lg shadow-2xl transition-all transform hover:scale-110">
                                🎯 İlanlara Göz At
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- XP Timeline -->
                <div class="space-y-4">
                    @foreach($xpHistory as $event)
                    <div class="group relative bg-gradient-to-r from-purple-900/30 via-black/30 to-blue-900/30 backdrop-blur-xl rounded-2xl border-2 border-purple-500/20 hover:border-purple-500/60 transition-all duration-300 overflow-hidden hover:scale-102 hover:shadow-2xl hover:shadow-purple-500/30">
                        <!-- Glow Efekti -->
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-500/0 via-purple-500/10 to-purple-500/0 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="relative flex items-center p-6">
                            <!-- İkon -->
                            <div class="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-purple-500/30 to-blue-500/30 rounded-2xl flex items-center justify-center text-4xl border-2 border-purple-500/30 group-hover:scale-110 transition-transform">
                                @if(str_contains($event->type, 'profile'))
                                    👤
                                @elseif(str_contains($event->type, 'post') || str_contains($event->type, 'guide'))
                                    📝
                                @elseif(str_contains($event->type, 'clan'))
                                    👥
                                @elseif(str_contains($event->type, 'comment'))
                                    💬
                                @elseif(str_contains($event->type, 'like'))
                                    ❤️
                                @elseif(str_contains($event->type, 'device'))
                                    📱
                                @else
                                    ⭐
                                @endif
                            </div>

                            <!-- İçerik -->
                            <div class="flex-1 ml-6">
                                <h3 class="text-2xl font-black text-white mb-1 group-hover:text-purple-300 transition-colors">
                                    {{ app(\App\Services\XpService::class)->getXpTypeDescription($event->type) }}
                                </h3>
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="text-gray-400 font-bold">🕐 {{ $event->created_at->diffForHumans() }}</span>
                                    <span class="text-gray-600">•</span>
                                    <span class="text-purple-400 font-bold">{{ $event->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                            </div>

                            <!-- XP Badge -->
                            <div class="flex-shrink-0 ml-6">
                                <div class="relative group/badge">
                                    <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl blur-lg opacity-50 group-hover/badge:opacity-100 transition-opacity"></div>
                                    <div class="relative bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-3 rounded-2xl shadow-2xl transform group-hover/badge:scale-110 transition-transform">
                                        <div class="text-center">
                                            <div class="text-3xl font-black text-white">+{{ $event->points }}</div>
                                            <div class="text-xs font-bold text-green-200 uppercase tracking-wider">XP</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Sidebar - Kompakt -->
        <div class="space-y-6">
            <!-- Hızlı Linkler -->
            <div class="group relative bg-gradient-to-br from-blue-900/30 via-purple-900/30 to-pink-900/30 backdrop-blur-xl rounded-3xl border-2 border-blue-500/30 overflow-hidden hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                <div class="relative p-6 space-y-3">
                    <h3 class="text-2xl font-black text-white mb-4 flex items-center gap-2">
                        🚀 <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Hızlı Erişim</span>
                    </h3>
                    
                    <a href="{{ route('xp.leaderboard') }}" 
                        class="group/btn relative block bg-gradient-to-r from-blue-600/50 to-blue-700/50 hover:from-blue-600 hover:to-blue-700 backdrop-blur-sm text-white px-5 py-4 rounded-2xl font-black text-center transition-all shadow-lg hover:shadow-2xl hover:shadow-blue-500/50 border-2 border-blue-500/30 hover:border-blue-400">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <span class="text-2xl">📊</span>
                            <span>Liderlik Tablosu</span>
                        </span>
                    </a>
                    
                    <a href="{{ route('xp.badges') }}" 
                        class="group/btn relative block bg-gradient-to-r from-purple-600/50 to-purple-700/50 hover:from-purple-600 hover:to-purple-700 backdrop-blur-sm text-white px-5 py-4 rounded-2xl font-black text-center transition-all shadow-lg hover:shadow-2xl hover:shadow-purple-500/50 border-2 border-purple-500/30 hover:border-purple-400">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <span class="text-2xl">🎖️</span>
                            <span>Rozetlerim</span>
                        </span>
                    </a>
                    
                    <a href="{{ route('profile.index') }}" 
                        class="group/btn relative block bg-gradient-to-r from-green-600/50 to-green-700/50 hover:from-green-600 hover:to-green-700 backdrop-blur-sm text-white px-5 py-4 rounded-2xl font-black text-center transition-all shadow-lg hover:shadow-2xl hover:shadow-green-500/50 border-2 border-green-500/30 hover:border-green-400">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <span class="text-2xl">👤</span>
                            <span>Profilim</span>
                        </span>
                    </a>
                </div>
            </div>

            <!-- XP Kazanma Rehberi -->
            <div class="group relative bg-gradient-to-br from-pink-900/30 via-purple-900/30 to-orange-900/30 backdrop-blur-xl rounded-3xl border-2 border-pink-500/30 overflow-hidden hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-pink-500/10 to-orange-500/10 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                <div class="relative p-6">
                    <h3 class="text-2xl font-black text-white mb-4 flex items-center gap-2 drop-shadow-lg">
                        💡 <span class="text-white">XP Kazan</span>
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between bg-black/30 backdrop-blur-sm p-3 rounded-xl border border-green-500/20 hover:border-green-500/50 transition-all">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">👤</span>
                                <span class="text-white font-bold text-sm">Profil</span>
                            </div>
                            <span class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-3 py-1 rounded-full text-xs font-black">+50 XP</span>
                        </div>
                        <div class="flex items-center justify-between bg-black/30 backdrop-blur-sm p-3 rounded-xl border border-green-500/20 hover:border-green-500/50 transition-all">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">👥</span>
                                <span class="text-white font-bold text-sm">Klan</span>
                            </div>
                            <span class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-3 py-1 rounded-full text-xs font-black">+30 XP</span>
                        </div>
                        <div class="flex items-center justify-between bg-black/30 backdrop-blur-sm p-3 rounded-xl border border-green-500/20 hover:border-green-500/50 transition-all">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">📱</span>
                                <span class="text-white font-bold text-sm">Cihaz</span>
                            </div>
                            <span class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-3 py-1 rounded-full text-xs font-black">+20 XP</span>
                        </div>
                        <div class="flex items-center justify-between bg-black/30 backdrop-blur-sm p-3 rounded-xl border border-green-500/20 hover:border-green-500/50 transition-all">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">📝</span>
                                <span class="text-white font-bold text-sm">Rehber</span>
                            </div>
                            <span class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-3 py-1 rounded-full text-xs font-black">+15 XP</span>
                        </div>
                        <div class="flex items-center justify-between bg-black/30 backdrop-blur-sm p-3 rounded-xl border border-green-500/20 hover:border-green-500/50 transition-all">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">💬</span>
                                <span class="text-white font-bold text-sm">Yorum</span>
                            </div>
                            <span class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-3 py-1 rounded-full text-xs font-black">+5 XP</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
