@extends('layouts.app')

@section('title', 'Call of Duty Mobile Türkiye')

{{-- DEBUG: Bu COD view'ı yüklendi! --}}

@section('content')
<div class="min-h-screen bg-[#0f0f0f]">
    <!-- HERO - Discord Style -->
    <div class="relative overflow-hidden">
        <!-- Animated Gradient Blobs -->
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-green-500/20 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-emerald-500/20 rounded-full blur-[100px] animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 w-[300px] h-[300px] bg-lime-500/10 rounded-full blur-[80px] animate-pulse" style="animation-delay: 2s;"></div>

        <div class="relative max-w-6xl mx-auto px-6 pt-20 pb-32">
            <div class="text-center">
                <!-- Animated Badge -->
                <div class="inline-flex items-center gap-3 px-5 py-2.5 bg-white/5 backdrop-blur-xl rounded-full border border-white/10 mb-10 hover:bg-white/10 transition-all cursor-default">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-white/80 text-sm font-medium">{{ number_format($stats['active_users']) }} oyuncu şu an aktif</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-6xl md:text-8xl font-black text-white mb-8 tracking-tight leading-none">
                    Takımını bul,<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 via-emerald-400 to-lime-400 animate-gradient">zaferi kazan.</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-xl md:text-2xl text-[#b5bac1] max-w-2xl mx-auto mb-12 leading-relaxed">
                    Call of Duty Mobile Türkiye'nin en büyük topluluğu. Takım arkadaşlarını bul, klanına katıl, turnuvalarda yarış.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    @auth
                        <a href="{{ route('lfg.create') }}" class="group relative px-8 py-4 bg-[#248046] hover:bg-[#1a6334] text-white font-semibold rounded-full transition-all hover:shadow-[0_0_40px_rgba(34,197,94,0.4)] hover:scale-105">
                            <span class="flex items-center gap-2">
                                Takım Ara
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="group relative px-8 py-4 bg-[#248046] hover:bg-[#1a6334] text-white font-semibold rounded-full transition-all hover:shadow-[0_0_40px_rgba(34,197,94,0.4)] hover:scale-105">
                            <span class="flex items-center gap-2">
                                Hemen Başla — ücretsiz
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </span>
                        </a>
                    @endauth
                    <a href="{{ route('lfg.index') }}" class="px-8 py-4 text-white/80 hover:text-white font-medium transition-colors">
                        İlanları Gör →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- STATS BAR -->
    <div class="border-y border-white/5 bg-white/[0.02]">
        <div class="max-w-6xl mx-auto px-6 py-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center group cursor-default">
                    <div class="text-4xl font-black text-white group-hover:text-green-400 transition-colors">{{ number_format($stats['total_users']) }}</div>
                    <div class="text-sm text-[#b5bac1] mt-1">Toplam Oyuncu</div>
                </div>
                <div class="text-center group cursor-default">
                    <div class="text-4xl font-black text-green-400">{{ number_format($stats['active_users']) }}</div>
                    <div class="text-sm text-[#b5bac1] mt-1">Aktif Oyuncu</div>
                </div>
                <div class="text-center group cursor-default">
                    <div class="text-4xl font-black text-white group-hover:text-green-400 transition-colors">{{ number_format($stats['active_lfg']) }}</div>
                    <div class="text-sm text-[#b5bac1] mt-1">Açık İlan</div>
                </div>
                <div class="text-center group cursor-default">
                    <div class="text-4xl font-black text-white group-hover:text-green-400 transition-colors">{{ number_format($stats['total_clans']) }}</div>
                    <div class="text-sm text-[#b5bac1] mt-1">Aktif Klan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- FEATURES -->
    <div class="max-w-6xl mx-auto px-6 py-24">
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="group p-8 bg-[#1a1a1a] hover:bg-[#222] rounded-2xl border border-white/5 hover:border-green-500/30 transition-all duration-300 hover:-translate-y-1">
                <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-green-500/20 group-hover:scale-110 transition-all">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Takım Bul</h3>
                <p class="text-[#b5bac1] leading-relaxed">Ranked, casual veya turnuva. Seninle uyumlu takım arkadaşlarını saniyeler içinde bul.</p>
            </div>

            <!-- Card 2 -->
            <div class="group p-8 bg-[#1a1a1a] hover:bg-[#222] rounded-2xl border border-white/5 hover:border-green-500/30 transition-all duration-300 hover:-translate-y-1">
                <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-green-500/20 group-hover:scale-110 transition-all">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Klan Kur</h3>
                <p class="text-[#b5bac1] leading-relaxed">Kendi klanını oluştur, üyeleri yönet. Birlikte daha güçlüsünüz.</p>
            </div>

            <!-- Card 3 -->
            <div class="group p-8 bg-[#1a1a1a] hover:bg-[#222] rounded-2xl border border-white/5 hover:border-green-500/30 transition-all duration-300 hover:-translate-y-1">
                <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-green-500/20 group-hover:scale-110 transition-all">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Turnuva</h3>
                <p class="text-[#b5bac1] leading-relaxed">Turnuvalara katıl, yeteneklerini göster, ödüller kazan.</p>
            </div>
        </div>
    </div>

    <!-- RECENT POSTS -->
    @if($recentLfg->count() > 0)
    <div class="border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6 py-24">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2">Son İlanlar</h2>
                    <p class="text-[#b5bac1]">Takım arayan oyuncular</p>
                </div>
                <a href="{{ route('lfg.index') }}" class="text-green-400 hover:text-green-300 font-medium flex items-center gap-2 transition-colors">
                    Tümü
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentLfg as $lfg)
                <a href="{{ route('lfg.show', $lfg->id) }}" class="group block p-5 bg-[#1a1a1a] hover:bg-[#222] rounded-xl border border-white/5 hover:border-green-500/20 transition-all">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($lfg->user->name) }}&background=248046&color=fff&size=40" class="w-10 h-10 rounded-full">
                        <div class="flex-1 min-w-0">
                            <div class="text-white font-medium truncate">{{ $lfg->user->name }}</div>
                            <div class="text-xs text-[#b5bac1]">{{ $lfg->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="px-2.5 py-1 bg-green-500/10 text-green-400 text-xs font-medium rounded-full">{{ $lfg->mode ?? 'Ranked' }}</span>
                    </div>
                    <h3 class="text-white font-semibold mb-2 group-hover:text-green-400 transition-colors line-clamp-1">{{ $lfg->title }}</h3>
                    <p class="text-sm text-[#b5bac1] line-clamp-2">{{ Str::limit($lfg->description, 100) }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- CTA -->
    <div class="border-t border-white/5 bg-gradient-to-b from-[#0f0f0f] to-[#1a1a1a]">
        <div class="max-w-4xl mx-auto px-6 py-24 text-center">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-6">Hazır mısın?</h2>
            <p class="text-xl text-[#b5bac1] mb-10 max-w-xl mx-auto">
                Binlerce oyuncu seni bekliyor. Hemen katıl.
            </p>
            @guest
            <a href="{{ route('register') }}" class="inline-flex items-center gap-3 px-10 py-5 bg-[#248046] hover:bg-[#1a6334] text-white font-semibold text-lg rounded-full transition-all hover:shadow-[0_0_60px_rgba(34,197,94,0.3)] hover:scale-105">
                Ücretsiz Kayıt Ol
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            @else
            <a href="{{ route('lfg.create') }}" class="inline-flex items-center gap-3 px-10 py-5 bg-[#248046] hover:bg-[#1a6334] text-white font-semibold text-lg rounded-full transition-all hover:shadow-[0_0_60px_rgba(34,197,94,0.3)] hover:scale-105">
                İlan Oluştur
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            @endguest
        </div>
    </div>
</div>

<style>
@keyframes gradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}
.animate-gradient {
    background-size: 200% auto;
    animation: gradient 3s ease infinite;
}
</style>
@endsection
