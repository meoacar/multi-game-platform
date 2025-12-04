@extends('layouts.app')

@section('title', 'League of Legends - MOBA Topluluğu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- LOL Hero Section - Magical -->
    <div class="relative rounded-3xl overflow-hidden mb-12 shadow-2xl" style="background: radial-gradient(ellipse at center, #010a13 0%, #000000 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1;">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: radial-gradient(2px 2px at 20% 30%, rgba(200, 155, 60, 0.8), transparent), radial-gradient(2px 2px at 60% 70%, rgba(10, 200, 185, 0.8), transparent), radial-gradient(1px 1px at 50% 50%, rgba(200, 155, 60, 0.6), transparent); background-size: 200% 200%; animation: stars-float 20s ease-in-out infinite;"></div>
        </div>
        <div class="relative px-8 py-20 md:px-16 md:py-32">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-2xl animate-pulse" style="background: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%); box-shadow: 0 0 40px rgba(200, 155, 60, 0.6); clip-path: polygon(10% 0%, 90% 0%, 100% 10%, 100% 90%, 90% 100%, 10% 100%, 0% 90%, 0% 10%);">
                    <span class="text-4xl">⚔️</span>
                </div>
                <div>
                    <div class="text-yellow-400 font-bold text-lg mb-1 tracking-wider">MOBA LEGEND</div>
                    <h1 class="text-5xl md:text-7xl font-black tracking-wide" style="background: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 50%, #C89B3C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 0 0 40px rgba(200, 155, 60, 0.6);">
                        LEAGUE OF LEGENDS
                    </h1>
                </div>
            </div>
            <p class="text-2xl md:text-3xl mb-8 text-yellow-200 max-w-3xl font-bold">
                5v5 Stratejik Savaş. 160+ Şampiyon. Sonsuz Strateji.<br>
                <span class="text-yellow-400">Türkiye'nin en büyük LOL topluluğu!</span>
            </p>
            <div class="flex flex-wrap gap-4">
                @auth
                    <a href="{{ route('lfg.create') }}" class="px-10 py-5 font-black rounded-xl hover:shadow-2xl transition-all transform hover:scale-105 text-lg" style="background: linear-gradient(135deg, #C89B3C 0%, #785A28 50%, #0AC8B9 100%); color: white; box-shadow: 0 0 40px rgba(200, 155, 60, 0.6); clip-path: polygon(10% 0%, 90% 0%, 100% 10%, 100% 90%, 90% 100%, 10% 100%, 0% 90%, 0% 10%);">
                        ✦ TAKIM BUL
                    </a>
                    <a href="{{ route('clans.create') }}" class="px-10 py-5 bg-black/50 backdrop-blur-md text-yellow-400 font-black rounded-xl hover:bg-black/70 transition-all border-2 text-lg" style="border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1; clip-path: polygon(10% 0%, 90% 0%, 100% 10%, 100% 90%, 90% 100%, 10% 100%, 0% 90%, 0% 10%);">
                        ✦ KLAN KUR
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-10 py-5 font-black rounded-xl hover:shadow-2xl transition-all transform hover:scale-105 text-lg" style="background: linear-gradient(135deg, #C89B3C 0%, #785A28 50%, #0AC8B9 100%); color: white; box-shadow: 0 0 40px rgba(200, 155, 60, 0.6); clip-path: polygon(10% 0%, 90% 0%, 100% 10%, 100% 90%, 90% 100%, 10% 100%, 0% 90%, 0% 10%);">
                        ✦ HEMEN KATIL
                    </a>
                    <a href="{{ route('login') }}" class="px-10 py-5 bg-black/50 backdrop-blur-md text-yellow-400 font-black rounded-xl hover:bg-black/70 transition-all border-2 text-lg" style="border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1; clip-path: polygon(10% 0%, 90% 0%, 100% 10%, 100% 90%, 90% 100%, 10% 100%, 0% 90%, 0% 10%);">
                        ✦ GİRİŞ YAP
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- LOL Stats - Magical Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(1, 10, 19, 0.9) 0%, rgba(20, 30, 40, 0.7) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1; clip-path: polygon(5% 0%, 95% 0%, 100% 5%, 100% 95%, 95% 100%, 5% 100%, 0% 95%, 0% 5%);">
            <div class="text-5xl font-black text-yellow-400 mb-2">{{ number_format($stats['total_users']) }}</div>
            <div class="text-yellow-200 font-bold tracking-wider">SUMMONERS</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(1, 10, 19, 0.9) 0%, rgba(20, 30, 40, 0.7) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1; clip-path: polygon(5% 0%, 95% 0%, 100% 5%, 100% 95%, 95% 100%, 5% 100%, 0% 95%, 0% 5%);">
            <div class="text-5xl font-black text-yellow-400 mb-2">{{ number_format($stats['active_users']) }}</div>
            <div class="text-yellow-200 font-bold tracking-wider">ONLINE</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(1, 10, 19, 0.9) 0%, rgba(20, 30, 40, 0.7) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1; clip-path: polygon(5% 0%, 95% 0%, 100% 5%, 100% 95%, 95% 100%, 5% 100%, 0% 95%, 0% 5%);">
            <div class="text-5xl font-black text-yellow-400 mb-2">{{ number_format($stats['active_lfg']) }}</div>
            <div class="text-yellow-200 font-bold tracking-wider">LFG</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(1, 10, 19, 0.9) 0%, rgba(20, 30, 40, 0.7) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1; clip-path: polygon(5% 0%, 95% 0%, 100% 5%, 100% 95%, 95% 100%, 5% 100%, 0% 95%, 0% 5%);">
            <div class="text-5xl font-black text-yellow-400 mb-2">{{ number_format($stats['total_clans']) }}</div>
            <div class="text-yellow-200 font-bold tracking-wider">GUILDS</div>
        </div>
    </div>

    <!-- LOL Özellikler - Game Modes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(200, 155, 60, 0.2) 0%, rgba(10, 200, 185, 0.1) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1; clip-path: polygon(8% 0%, 92% 0%, 100% 8%, 100% 92%, 92% 100%, 8% 100%, 0% 92%, 0% 8%);">
            <div class="text-6xl mb-4">⚡</div>
            <h3 class="text-2xl font-black text-yellow-400 mb-3 tracking-wider">RANKED</h3>
            <p class="text-yellow-200 mb-4">Ranked maçlar için takım kur. Challenger'a birlikte tırman!</p>
            <a href="{{ route('lfg.index') }}" class="inline-flex items-center text-yellow-400 font-bold hover:text-yellow-300">
                ✦ TAKIMINI BUL
            </a>
        </div>

        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(200, 155, 60, 0.2) 0%, rgba(10, 200, 185, 0.1) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1; clip-path: polygon(8% 0%, 92% 0%, 100% 8%, 100% 92%, 92% 100%, 8% 100%, 0% 92%, 0% 8%);">
            <div class="text-6xl mb-4">🎯</div>
            <h3 class="text-2xl font-black text-yellow-400 mb-3 tracking-wider">CLASH</h3>
            <p class="text-yellow-200 mb-4">Klanını kur, Clash turnuvalarına katıl ve ödülleri kazan!</p>
            <a href="{{ route('clans.index') }}" class="inline-flex items-center text-yellow-400 font-bold hover:text-yellow-300">
                ✦ KLANLARI KEŞFET
            </a>
        </div>

        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(200, 155, 60, 0.2) 0%, rgba(10, 200, 185, 0.1) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1; clip-path: polygon(8% 0%, 92% 0%, 100% 8%, 100% 92%, 92% 100%, 8% 100%, 0% 92%, 0% 8%);">
            <div class="text-6xl mb-4">🏆</div>
            <h3 class="text-2xl font-black text-yellow-400 mb-3 tracking-wider">TOURNAMENTS</h3>
            <p class="text-yellow-200 mb-4">Turnuvalara katıl, rakiplerini yen ve şampiyonluğu kazan!</p>
            <a href="{{ route('tournaments.index') }}" class="inline-flex items-center text-yellow-400 font-bold hover:text-yellow-300">
                ✦ TURNUVALARA KATIL
            </a>
        </div>
    </div>

    <!-- Son İlanlar -->
    @if($recentLfg->count() > 0)
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-4xl font-black text-yellow-400 tracking-wider">✦ AKTİF İLANLAR</h2>
            <a href="{{ route('lfg.index') }}" class="text-yellow-400 hover:text-yellow-300 font-bold flex items-center tracking-wider">
                TÜMÜNÜ GÖR ✦
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($recentLfg as $lfg)
                <a href="{{ route('lfg.show', $lfg->id) }}" class="glass-card overflow-hidden hover:shadow-2xl transition-all transform hover:scale-105" style="background: linear-gradient(135deg, rgba(1, 10, 19, 0.9) 0%, rgba(20, 30, 40, 0.7) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%) 1; clip-path: polygon(5% 0%, 95% 0%, 100% 5%, 100% 95%, 95% 100%, 5% 100%, 0% 95%, 0% 5%);">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-yellow-500/30 text-yellow-400 rounded text-sm font-bold border border-yellow-500" style="clip-path: polygon(8% 0%, 92% 0%, 100% 8%, 100% 92%, 92% 100%, 8% 100%, 0% 92%, 0% 8%);">
                                {{ $lfg->game_mode ?? 'RANKED' }}
                            </span>
                            <span class="text-yellow-300 text-sm">{{ $lfg->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-yellow-400 mb-2">{{ Str::limit($lfg->title, 50) }}</h3>
                        <p class="text-yellow-200 mb-4">{{ Str::limit($lfg->description, 80) }}</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full mr-3 flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #C89B3C 0%, #0AC8B9 100%);">
                                {{ strtoupper(substr($lfg->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-yellow-400">{{ $lfg->user->name }}</div>
                                <div class="text-sm text-yellow-300">{{ $lfg->city ?? 'Türkiye' }}</div>
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
