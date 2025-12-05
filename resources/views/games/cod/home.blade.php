@extends('layouts.app')

@section('title', 'Call of Duty Mobile Türkiye Topluluğu')

@section('content')
<div class="min-h-screen">
    <!-- HERO SECTION - Modern & Clean -->
    <div class="relative overflow-hidden">
        <!-- Gradient Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-zinc-900 via-emerald-950 to-zinc-900"></div>
        
        <!-- Subtle Pattern -->
        <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 border border-emerald-500/30 rounded-full mb-8 backdrop-blur-sm">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span class="text-emerald-400 text-sm font-medium">Türkiye'nin #1 COD Mobile Topluluğu</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-white mb-6 tracking-tight">
                    Call of Duty
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-green-400 to-emerald-400">Mobile</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-xl md:text-2xl text-zinc-400 max-w-2xl mx-auto mb-12 leading-relaxed">
                    Takım arkadaşlarını bul, klanına katıl, turnuvalarda yarış. 
                    <span class="text-white font-medium">Savaş meydanında buluşalım.</span>
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('lfg.create') }}" class="group px-8 py-4 bg-gradient-to-r from-emerald-500 to-green-500 text-white font-bold rounded-2xl hover:from-emerald-400 hover:to-green-400 transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                            <span class="flex items-center justify-center gap-2">
                                Takım Bul
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                        </a>
                        <a href="{{ route('clans.index') }}" class="px-8 py-4 bg-white/5 backdrop-blur-sm text-white font-bold rounded-2xl border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all hover:scale-105">
                            Klanları Keşfet
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="group px-8 py-4 bg-gradient-to-r from-emerald-500 to-green-500 text-white font-bold rounded-2xl hover:from-emerald-400 hover:to-green-400 transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-105">
                            <span class="flex items-center justify-center gap-2">
                                Hemen Başla
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-white/5 backdrop-blur-sm text-white font-bold rounded-2xl border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all hover:scale-105">
                            Giriş Yap
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Bottom Fade -->
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-zinc-900 to-transparent"></div>
    </div>

    <!-- STATS SECTION -->
    <div class="bg-zinc-900 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-black text-white mb-2">{{ number_format($stats['total_users']) }}</div>
                    <div class="text-zinc-500 font-medium">Oyuncu</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-black text-emerald-400 mb-2">{{ number_format($stats['active_users']) }}</div>
                    <div class="text-zinc-500 font-medium">Aktif</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-black text-white mb-2">{{ number_format($stats['active_lfg']) }}</div>
                    <div class="text-zinc-500 font-medium">Açık İlan</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-black text-emerald-400 mb-2">{{ number_format($stats['total_clans']) }}</div>
                    <div class="text-zinc-500 font-medium">Klan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- FEATURES SECTION -->
    <div class="bg-zinc-900 py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Neler Yapabilirsin?</h2>
                <p class="text-zinc-400 text-lg max-w-2xl mx-auto">Topluluğumuzda seni bekleyen özellikler</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group p-8 bg-zinc-800/50 rounded-3xl border border-zinc-700/50 hover:border-emerald-500/50 transition-all hover:bg-zinc-800">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-500/20 transition-colors">
                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Takım Bul</h3>
                    <p class="text-zinc-400 leading-relaxed">Ranked veya casual, istediğin modu seç ve seninle uyumlu takım arkadaşlarını bul.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 bg-zinc-800/50 rounded-3xl border border-zinc-700/50 hover:border-emerald-500/50 transition-all hover:bg-zinc-800">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-500/20 transition-colors">
                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Klan Kur</h3>
                    <p class="text-zinc-400 leading-relaxed">Kendi klanını oluştur veya mevcut klanlara katıl. Birlikte daha güçlüsünüz.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 bg-zinc-800/50 rounded-3xl border border-zinc-700/50 hover:border-emerald-500/50 transition-all hover:bg-zinc-800">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-500/20 transition-colors">
                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Turnuvalara Katıl</h3>
                    <p class="text-zinc-400 leading-relaxed">Düzenlenen turnuvalarda yeteneklerini göster ve ödüller kazan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT LFG POSTS -->
    @if($recentLfg->count() > 0)
    <div class="bg-zinc-900 py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-black text-white mb-2">Son İlanlar</h2>
                    <p class="text-zinc-400">Takım arayan oyuncular</p>
                </div>
                <a href="{{ route('lfg.index') }}" class="hidden md:flex items-center gap-2 text-emerald-400 hover:text-emerald-300 font-medium transition-colors">
                    Tümünü Gör
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentLfg as $lfg)
                <a href="{{ route('lfg.show', $lfg->id) }}" class="group block p-6 bg-zinc-800/50 rounded-2xl border border-zinc-700/50 hover:border-emerald-500/30 hover:bg-zinc-800 transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-sm font-medium rounded-lg">
                            {{ $lfg->mode ?? 'Ranked' }}
                        </span>
                        <span class="text-zinc-500 text-sm">{{ $lfg->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-emerald-400 transition-colors">{{ Str::limit($lfg->title, 50) }}</h3>
                    <p class="text-zinc-400 text-sm mb-4 line-clamp-2">{{ Str::limit($lfg->description, 100) }}</p>
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($lfg->user->name) }}&background=10b981&color=fff&size=40" 
                             class="w-10 h-10 rounded-full">
                        <div>
                            <div class="text-white font-medium text-sm">{{ $lfg->user->name }}</div>
                            <div class="text-zinc-500 text-xs">{{ $lfg->city ?? 'Türkiye' }}</div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <div class="mt-8 text-center md:hidden">
                <a href="{{ route('lfg.index') }}" class="inline-flex items-center gap-2 text-emerald-400 hover:text-emerald-300 font-medium">
                    Tümünü Gör
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- CTA SECTION -->
    <div class="bg-gradient-to-br from-emerald-600 to-green-600 py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Hazır mısın?</h2>
            <p class="text-xl text-emerald-100 mb-10 max-w-2xl mx-auto">
                Binlerce oyuncu seni bekliyor. Hemen katıl ve savaş meydanında yerini al.
            </p>
            @guest
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-10 py-5 bg-white text-emerald-600 font-bold text-lg rounded-2xl hover:bg-emerald-50 transition-all shadow-xl hover:shadow-2xl hover:scale-105">
                Ücretsiz Kayıt Ol
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            @else
            <a href="{{ route('lfg.create') }}" class="inline-flex items-center gap-2 px-10 py-5 bg-white text-emerald-600 font-bold text-lg rounded-2xl hover:bg-emerald-50 transition-all shadow-xl hover:shadow-2xl hover:scale-105">
                İlan Oluştur
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            @endguest
        </div>
    </div>
</div>
@endsection
