@extends('layouts.app')

@section('title', 'Valorant - Taktiksel 5v5 FPS Topluluğu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Valorant Hero Section - Cyberpunk -->
    <div class="relative rounded-3xl overflow-hidden mb-12 shadow-2xl" style="background: #0f1923; border: 2px solid rgba(255, 70, 85, 0.4);">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: linear-gradient(rgba(255, 70, 85, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 70, 85, 0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
        </div>
        <div class="relative px-8 py-20 md:px-16 md:py-32">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-2xl animate-pulse" style="background: linear-gradient(135deg, #FF4655 0%, #FD4556 100%); box-shadow: 0 0 40px rgba(255, 70, 85, 0.6);">
                    <span class="text-4xl">◆</span>
                </div>
                <div>
                    <div class="text-red-400 font-bold text-lg mb-1 tracking-widest">TACTICAL FPS</div>
                    <h1 class="text-5xl md:text-7xl font-black tracking-wider" style="background: linear-gradient(135deg, #ffffff 0%, #FF4655 50%, #FD4556 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 0 0 30px rgba(255, 70, 85, 0.6);">
                        VALORANT
                    </h1>
                </div>
            </div>
            <p class="text-2xl md:text-3xl mb-8 text-red-200 max-w-3xl font-bold">
                5v5 Taktiksel Savaş. Karakterler. Yetenekler.<br>
                <span class="text-red-400">Türkiye'nin en aktif Valorant topluluğu!</span>
            </p>
            <div class="flex flex-wrap gap-4">
                @auth
                    <a href="{{ route('lfg.create') }}" class="px-10 py-5 font-black rounded-xl hover:shadow-2xl transition-all transform hover:scale-105 text-lg" style="background: linear-gradient(135deg, #FF4655 0%, #FF1744 100%); color: white; box-shadow: 0 0 40px rgba(255, 70, 85, 0.6); clip-path: polygon(0 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%);">
                        ▸ TAKIM BUL
                    </a>
                    <a href="{{ route('clans.create') }}" class="px-10 py-5 bg-black/50 backdrop-blur-md text-red-400 font-black rounded-xl hover:bg-black/70 transition-all border-2 border-red-500 text-lg" style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%);">
                        ▸ KLAN KUR
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-10 py-5 font-black rounded-xl hover:shadow-2xl transition-all transform hover:scale-105 text-lg" style="background: linear-gradient(135deg, #FF4655 0%, #FF1744 100%); color: white; box-shadow: 0 0 40px rgba(255, 70, 85, 0.6); clip-path: polygon(0 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%);">
                        ▸ HEMEN KATIL
                    </a>
                    <a href="{{ route('login') }}" class="px-10 py-5 bg-black/50 backdrop-blur-md text-red-400 font-black rounded-xl hover:bg-black/70 transition-all border-2 border-red-500 text-lg" style="clip-path: polygon(0 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%);">
                        ▸ GİRİŞ YAP
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Valorant Stats - Cyber Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(15, 25, 35, 0.9) 0%, rgba(25, 35, 45, 0.7) 100%); border: 2px solid rgba(255, 70, 85, 0.4); clip-path: polygon(0 0, 100% 0, 100% calc(100% - 15px), calc(100% - 15px) 100%, 0 100%);">
            <div class="text-5xl font-black text-red-400 mb-2">{{ number_format($stats['total_users']) }}</div>
            <div class="text-red-200 font-bold tracking-wider">AGENTS</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(15, 25, 35, 0.9) 0%, rgba(25, 35, 45, 0.7) 100%); border: 2px solid rgba(255, 70, 85, 0.4); clip-path: polygon(0 0, 100% 0, 100% calc(100% - 15px), calc(100% - 15px) 100%, 0 100%);">
            <div class="text-5xl font-black text-red-400 mb-2">{{ number_format($stats['active_users']) }}</div>
            <div class="text-red-200 font-bold tracking-wider">ONLINE</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(15, 25, 35, 0.9) 0%, rgba(25, 35, 45, 0.7) 100%); border: 2px solid rgba(255, 70, 85, 0.4); clip-path: polygon(0 0, 100% 0, 100% calc(100% - 15px), calc(100% - 15px) 100%, 0 100%);">
            <div class="text-5xl font-black text-red-400 mb-2">{{ number_format($stats['active_lfg']) }}</div>
            <div class="text-red-200 font-bold tracking-wider">LFG</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(15, 25, 35, 0.9) 0%, rgba(25, 35, 45, 0.7) 100%); border: 2px solid rgba(255, 70, 85, 0.4); clip-path: polygon(0 0, 100% 0, 100% calc(100% - 15px), calc(100% - 15px) 100%, 0 100%);">
            <div class="text-5xl font-black text-red-400 mb-2">{{ number_format($stats['total_clans']) }}</div>
            <div class="text-red-200 font-bold tracking-wider">TEAMS</div>
        </div>
    </div>

    <!-- Valorant Özellikler - Agent Abilities -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(255, 70, 85, 0.2) 0%, rgba(253, 69, 86, 0.1) 100%); border: 2px solid rgba(255, 70, 85, 0.4); clip-path: polygon(5px 0, 100% 0, 100% calc(100% - 5px), calc(100% - 5px) 100%, 0 100%, 0 5px);">
            <div class="text-6xl mb-4">⚡</div>
            <h3 class="text-2xl font-black text-red-400 mb-3 tracking-wider">COMPETITIVE</h3>
            <p class="text-red-200 mb-4">Ranked maçlar için takım kur. Radiant'a birlikte tırman!</p>
            <a href="{{ route('lfg.index') }}" class="inline-flex items-center text-red-400 font-bold hover:text-red-300">
                ▸ TAKIMINI BUL
            </a>
        </div>

        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(255, 70, 85, 0.2) 0%, rgba(253, 69, 86, 0.1) 100%); border: 2px solid rgba(255, 70, 85, 0.4); clip-path: polygon(5px 0, 100% 0, 100% calc(100% - 5px), calc(100% - 5px) 100%, 0 100%, 0 5px);">
            <div class="text-6xl mb-4">🎯</div>
            <h3 class="text-2xl font-black text-red-400 mb-3 tracking-wider">SCRIMS</h3>
            <p class="text-red-200 mb-4">Klanını kur, antrenman maçları yap ve stratejiler geliştir!</p>
            <a href="{{ route('clans.index') }}" class="inline-flex items-center text-red-400 font-bold hover:text-red-300">
                ▸ KLANLARI KEŞFET
            </a>
        </div>

        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(255, 70, 85, 0.2) 0%, rgba(253, 69, 86, 0.1) 100%); border: 2px solid rgba(255, 70, 85, 0.4); clip-path: polygon(5px 0, 100% 0, 100% calc(100% - 5px), calc(100% - 5px) 100%, 0 100%, 0 5px);">
            <div class="text-6xl mb-4">🏆</div>
            <h3 class="text-2xl font-black text-red-400 mb-3 tracking-wider">TOURNAMENTS</h3>
            <p class="text-red-200 mb-4">Turnuvalara katıl, rakiplerini yen ve şampiyonluğu kazan!</p>
            <a href="{{ route('tournaments.index') }}" class="inline-flex items-center text-red-400 font-bold hover:text-red-300">
                ▸ TURNUVALARA KATIL
            </a>
        </div>
    </div>

    <!-- Son İlanlar -->
    @if($recentLfg->count() > 0)
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-4xl font-black text-red-400 tracking-wider">◆ AKTİF İLANLAR</h2>
            <a href="{{ route('lfg.index') }}" class="text-red-400 hover:text-red-300 font-bold flex items-center tracking-wider">
                TÜMÜNÜ GÖR ▸
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($recentLfg as $lfg)
                <a href="{{ route('lfg.show', $lfg->id) }}" class="glass-card overflow-hidden hover:shadow-2xl transition-all transform hover:scale-105" style="background: linear-gradient(135deg, rgba(15, 25, 35, 0.9) 0%, rgba(25, 35, 45, 0.7) 100%); border: 2px solid rgba(255, 70, 85, 0.4); clip-path: polygon(0 0, 100% 0, 100% calc(100% - 15px), calc(100% - 15px) 100%, 0 100%);">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-red-500/30 text-red-400 rounded text-sm font-bold border border-red-500" style="clip-path: polygon(5px 0, 100% 0, 100% calc(100% - 5px), calc(100% - 5px) 100%, 0 100%, 0 5px);">
                                {{ $lfg->game_mode ?? 'COMPETITIVE' }}
                            </span>
                            <span class="text-red-300 text-sm">{{ $lfg->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-red-400 mb-2">{{ Str::limit($lfg->title, 50) }}</h3>
                        <p class="text-red-200 mb-4">{{ Str::limit($lfg->description, 80) }}</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full mr-3 flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #FF4655 0%, #FD4556 100%);">
                                {{ strtoupper(substr($lfg->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-red-400">{{ $lfg->user->name }}</div>
                                <div class="text-sm text-red-300">{{ $lfg->city ?? 'Türkiye' }}</div>
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
