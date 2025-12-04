@extends('layouts.app')

@section('title', 'PUBG Mobile - Türkiye\'nin En Büyük Battle Royale Topluluğu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- PUBG Hero Section - Savaş Alanı -->
    <div class="relative rounded-3xl overflow-hidden mb-12 shadow-2xl" style="background: linear-gradient(135deg, #1a0f00 0%, #2d1a00 50%, #1a0f00 100%);">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23FF6B00&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        <div class="relative px-8 py-20 md:px-16 md:py-32">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center shadow-2xl animate-pulse-slow">
                    <span class="text-4xl">🔥</span>
                </div>
                <div>
                    <div class="text-orange-400 font-bold text-lg mb-1">BATTLE ROYALE</div>
                    <h1 class="text-5xl md:text-7xl font-black" style="background: linear-gradient(135deg, #FFB800 0%, #FF6B00 50%, #FF4500 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        PUBG MOBILE
                    </h1>
                </div>
            </div>
            <p class="text-2xl md:text-3xl mb-8 text-orange-200 max-w-3xl font-bold">
                100 Oyuncu. 1 Ada. Tek Kazanan!<br>
                <span class="text-orange-400">Türkiye'nin en büyük PUBG Mobile topluluğuna katıl!</span>
            </p>
            <div class="flex flex-wrap gap-4">
                @auth
                    <a href="{{ route('lfg.create') }}" class="px-10 py-5 bg-gradient-to-r from-orange-500 to-red-600 text-white font-black rounded-2xl hover:shadow-2xl transition-all transform hover:scale-105 text-lg shadow-lg" style="box-shadow: 0 0 30px rgba(255, 107, 0, 0.6);">
                        🎯 TAKIM BUL
                    </a>
                    <a href="{{ route('clans.create') }}" class="px-10 py-5 bg-black/50 backdrop-blur-md text-orange-400 font-black rounded-2xl hover:bg-black/70 transition-all border-2 border-orange-500 text-lg">
                        🛡️ KLAN KUR
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-10 py-5 bg-gradient-to-r from-orange-500 to-red-600 text-white font-black rounded-2xl hover:shadow-2xl transition-all transform hover:scale-105 text-lg shadow-lg" style="box-shadow: 0 0 30px rgba(255, 107, 0, 0.6);">
                        🚀 HEMEN KATIL
                    </a>
                    <a href="{{ route('login') }}" class="px-10 py-5 bg-black/50 backdrop-blur-md text-orange-400 font-black rounded-2xl hover:bg-black/70 transition-all border-2 border-orange-500 text-lg">
                        GİRİŞ YAP
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- PUBG Stats - Savaş İstatistikleri -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(26, 15, 0, 0.8) 0%, rgba(45, 26, 0, 0.6) 100%); border: 1px solid rgba(255, 107, 0, 0.3);">
            <div class="text-5xl font-black text-orange-400 mb-2">{{ number_format($stats['total_users']) }}</div>
            <div class="text-orange-200 font-bold">SAVAŞÇI</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(26, 15, 0, 0.8) 0%, rgba(45, 26, 0, 0.6) 100%); border: 1px solid rgba(255, 107, 0, 0.3);">
            <div class="text-5xl font-black text-orange-400 mb-2">{{ number_format($stats['active_users']) }}</div>
            <div class="text-orange-200 font-bold">AKTİF OYUNCU</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(26, 15, 0, 0.8) 0%, rgba(45, 26, 0, 0.6) 100%); border: 1px solid rgba(255, 107, 0, 0.3);">
            <div class="text-5xl font-black text-orange-400 mb-2">{{ number_format($stats['active_lfg']) }}</div>
            <div class="text-orange-200 font-bold">TAKIM ARANIYOR</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(26, 15, 0, 0.8) 0%, rgba(45, 26, 0, 0.6) 100%); border: 1px solid rgba(255, 107, 0, 0.3);">
            <div class="text-5xl font-black text-orange-400 mb-2">{{ number_format($stats['total_clans']) }}</div>
            <div class="text-orange-200 font-bold">KLAN</div>
        </div>
    </div>

    <!-- PUBG Özellikler - Savaş Modları -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(255, 107, 0, 0.2) 0%, rgba(255, 69, 0, 0.1) 100%); border: 2px solid rgba(255, 107, 0, 0.4);">
            <div class="text-6xl mb-4">🎯</div>
            <h3 class="text-2xl font-black text-orange-400 mb-3">SQUAD MATCH</h3>
            <p class="text-orange-200 mb-4">4 kişilik takımlar kur, stratejiler geliştir ve Chicken Dinner kazan!</p>
            <a href="{{ route('lfg.index') }}" class="inline-flex items-center text-orange-400 font-bold hover:text-orange-300">
                TAKIMINI BUL →
            </a>
        </div>

        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(255, 107, 0, 0.2) 0%, rgba(255, 69, 0, 0.1) 100%); border: 2px solid rgba(255, 107, 0, 0.4);">
            <div class="text-6xl mb-4">🛡️</div>
            <h3 class="text-2xl font-black text-orange-400 mb-3">CLAN WARS</h3>
            <p class="text-orange-200 mb-4">Klanını kur, üyelerini topla ve klan savaşlarında rakiplerini ez!</p>
            <a href="{{ route('clans.index') }}" class="inline-flex items-center text-orange-400 font-bold hover:text-orange-300">
                KLANLARI KEŞFET →
            </a>
        </div>

        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(255, 107, 0, 0.2) 0%, rgba(255, 69, 0, 0.1) 100%); border: 2px solid rgba(255, 107, 0, 0.4);">
            <div class="text-6xl mb-4">🏆</div>
            <h3 class="text-2xl font-black text-orange-400 mb-3">TOURNAMENTS</h3>
            <p class="text-orange-200 mb-4">Turnuvalara katıl, yeteneklerini göster ve ödülleri kazan!</p>
            <a href="{{ route('tournaments.index') }}" class="inline-flex items-center text-orange-400 font-bold hover:text-orange-300">
                TURNUVALARA KATIL →
            </a>
        </div>
    </div>

    <!-- Son İlanlar -->
    @if($recentLfg->count() > 0)
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-4xl font-black text-orange-400">🔥 AKTİF İLANLAR</h2>
            <a href="{{ route('lfg.index') }}" class="text-orange-400 hover:text-orange-300 font-bold flex items-center">
                TÜMÜNÜ GÖR →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($recentLfg as $lfg)
                <a href="{{ route('lfg.show', $lfg->id) }}" class="glass-card overflow-hidden hover:shadow-2xl transition-all transform hover:scale-105" style="background: linear-gradient(135deg, rgba(26, 15, 0, 0.9) 0%, rgba(45, 26, 0, 0.7) 100%); border: 1px solid rgba(255, 107, 0, 0.3);">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-orange-500/30 text-orange-400 rounded-full text-sm font-bold border border-orange-500">
                                {{ $lfg->game_mode ?? 'SQUAD' }}
                            </span>
                            <span class="text-orange-300 text-sm">{{ $lfg->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-orange-400 mb-2">{{ Str::limit($lfg->title, 50) }}</h3>
                        <p class="text-orange-200 mb-4">{{ Str::limit($lfg->description, 80) }}</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-full mr-3 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($lfg->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-orange-400">{{ $lfg->user->name }}</div>
                                <div class="text-sm text-orange-300">{{ $lfg->city ?? 'Türkiye' }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
