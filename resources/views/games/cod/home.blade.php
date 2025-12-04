@extends('layouts.app')

@section('title', 'Call of Duty Mobile - Askeri Taktik Topluluğu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- COD Hero Section - Military -->
    <div class="relative rounded-3xl overflow-hidden mb-12 shadow-2xl" style="background: #0a1208; border: 2px solid rgba(92, 135, 39, 0.4);">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(92, 135, 39, 0.1) 10px, rgba(92, 135, 39, 0.1) 20px);"></div>
        </div>
        <div class="relative px-8 py-20 md:px-16 md:py-32">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-2xl animate-pulse" style="background: linear-gradient(135deg, #5C8727 0%, #8BC34A 100%); box-shadow: 0 0 30px rgba(92, 135, 39, 0.6); clip-path: polygon(8px 0, 100% 0, 100% calc(100% - 8px), calc(100% - 8px) 100%, 0 100%, 0 8px);">
                    <span class="text-4xl">🎖️</span>
                </div>
                <div>
                    <div class="text-green-400 font-bold text-lg mb-1 tracking-widest font-mono">MILITARY FPS</div>
                    <h1 class="text-5xl md:text-7xl font-black tracking-wider font-mono" style="background: linear-gradient(135deg, #8BC34A 0%, #5C8727 50%, #4CAF50 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        CALL OF DUTY
                    </h1>
                </div>
            </div>
            <p class="text-2xl md:text-3xl mb-8 text-green-200 max-w-3xl font-bold font-mono">
                Multiplayer. Battle Royale. Tactical Ops.<br>
                <span class="text-green-400">Türkiye'nin en disiplinli COD topluluğu!</span>
            </p>
            <div class="flex flex-wrap gap-4">
                @auth
                    <a href="{{ route('lfg.create') }}" class="px-10 py-5 font-black rounded-xl hover:shadow-2xl transition-all transform hover:scale-105 text-lg font-mono" style="background: linear-gradient(135deg, #5C8727 0%, #4CAF50 100%); color: white; box-shadow: 0 0 30px rgba(92, 135, 39, 0.6); clip-path: polygon(8px 0, 100% 0, 100% calc(100% - 8px), calc(100% - 8px) 100%, 0 100%, 0 8px);">
                        ▮ SQUAD UP
                    </a>
                    <a href="{{ route('clans.create') }}" class="px-10 py-5 bg-black/50 backdrop-blur-md text-green-400 font-black rounded-xl hover:bg-black/70 transition-all border-2 border-green-500 text-lg font-mono" style="clip-path: polygon(8px 0, 100% 0, 100% calc(100% - 8px), calc(100% - 8px) 100%, 0 100%, 0 8px);">
                        ▮ CREATE CLAN
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-10 py-5 font-black rounded-xl hover:shadow-2xl transition-all transform hover:scale-105 text-lg font-mono" style="background: linear-gradient(135deg, #5C8727 0%, #4CAF50 100%); color: white; box-shadow: 0 0 30px rgba(92, 135, 39, 0.6); clip-path: polygon(8px 0, 100% 0, 100% calc(100% - 8px), calc(100% - 8px) 100%, 0 100%, 0 8px);">
                        ▮ ENLIST NOW
                    </a>
                    <a href="{{ route('login') }}" class="px-10 py-5 bg-black/50 backdrop-blur-md text-green-400 font-black rounded-xl hover:bg-black/70 transition-all border-2 border-green-500 text-lg font-mono" style="clip-path: polygon(8px 0, 100% 0, 100% calc(100% - 8px), calc(100% - 8px) 100%, 0 100%, 0 8px);">
                        ▮ LOGIN
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- COD Stats - Military Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(10, 18, 8, 0.9) 0%, rgba(20, 28, 18, 0.7) 100%); border: 2px solid rgba(92, 135, 39, 0.4); border-left: 4px solid #5C8727; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);">
            <div class="text-5xl font-black text-green-400 mb-2 font-mono">{{ number_format($stats['total_users']) }}</div>
            <div class="text-green-200 font-bold tracking-wider font-mono">SOLDIERS</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(10, 18, 8, 0.9) 0%, rgba(20, 28, 18, 0.7) 100%); border: 2px solid rgba(92, 135, 39, 0.4); border-left: 4px solid #5C8727; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);">
            <div class="text-5xl font-black text-green-400 mb-2 font-mono">{{ number_format($stats['active_users']) }}</div>
            <div class="text-green-200 font-bold tracking-wider font-mono">ACTIVE</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(10, 18, 8, 0.9) 0%, rgba(20, 28, 18, 0.7) 100%); border: 2px solid rgba(92, 135, 39, 0.4); border-left: 4px solid #5C8727; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);">
            <div class="text-5xl font-black text-green-400 mb-2 font-mono">{{ number_format($stats['active_lfg']) }}</div>
            <div class="text-green-200 font-bold tracking-wider font-mono">MISSIONS</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(10, 18, 8, 0.9) 0%, rgba(20, 28, 18, 0.7) 100%); border: 2px solid rgba(92, 135, 39, 0.4); border-left: 4px solid #5C8727; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);">
            <div class="text-5xl font-black text-green-400 mb-2 font-mono">{{ number_format($stats['total_clans']) }}</div>
            <div class="text-green-200 font-bold tracking-wider font-mono">UNITS</div>
        </div>
    </div>

    <!-- COD Özellikler - Game Modes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(92, 135, 39, 0.2) 0%, rgba(139, 195, 74, 0.1) 100%); border: 2px solid rgba(92, 135, 39, 0.4); border-left: 4px solid #8BC34A; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);">
            <div class="text-6xl mb-4">⚔️</div>
            <h3 class="text-2xl font-black text-green-400 mb-3 tracking-wider font-mono">MULTIPLAYER</h3>
            <p class="text-green-200 mb-4 font-mono">TDM, Domination, S&D. Takım kur ve düşmanı yok et!</p>
            <a href="{{ route('lfg.index') }}" class="inline-flex items-center text-green-400 font-bold hover:text-green-300 font-mono">
                ▮ FIND SQUAD
            </a>
        </div>

        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(92, 135, 39, 0.2) 0%, rgba(139, 195, 74, 0.1) 100%); border: 2px solid rgba(92, 135, 39, 0.4); border-left: 4px solid #8BC34A; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);">
            <div class="text-6xl mb-4">🎯</div>
            <h3 class="text-2xl font-black text-green-400 mb-3 tracking-wider font-mono">BATTLE ROYALE</h3>
            <p class="text-green-200 mb-4 font-mono">100 oyuncu, tek kazanan. Klanınla hayatta kal!</p>
            <a href="{{ route('clans.index') }}" class="inline-flex items-center text-green-400 font-bold hover:text-green-300 font-mono">
                ▮ JOIN CLAN
            </a>
        </div>

        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(92, 135, 39, 0.2) 0%, rgba(139, 195, 74, 0.1) 100%); border: 2px solid rgba(92, 135, 39, 0.4); border-left: 4px solid #8BC34A; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);">
            <div class="text-6xl mb-4">🏆</div>
            <h3 class="text-2xl font-black text-green-400 mb-3 tracking-wider font-mono">TOURNAMENTS</h3>
            <p class="text-green-200 mb-4 font-mono">Turnuvalara katıl, ödülleri kazan, şöhrete ulaş!</p>
            <a href="{{ route('tournaments.index') }}" class="inline-flex items-center text-green-400 font-bold hover:text-green-300 font-mono">
                ▮ COMPETE
            </a>
        </div>
    </div>

    <!-- Son İlanlar -->
    @if($recentLfg->count() > 0)
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-4xl font-black text-green-400 tracking-wider font-mono">▮ ACTIVE MISSIONS</h2>
            <a href="{{ route('lfg.index') }}" class="text-green-400 hover:text-green-300 font-bold flex items-center tracking-wider font-mono">
                VIEW ALL ▮
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($recentLfg as $lfg)
                <a href="{{ route('lfg.show', $lfg->id) }}" class="glass-card overflow-hidden hover:shadow-2xl transition-all transform hover:scale-105" style="background: linear-gradient(135deg, rgba(10, 18, 8, 0.9) 0%, rgba(20, 28, 18, 0.7) 100%); border: 2px solid rgba(92, 135, 39, 0.4); border-left: 4px solid #5C8727; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-green-500/30 text-green-400 rounded text-sm font-bold border border-green-500 font-mono" style="clip-path: polygon(5px 0, 100% 0, 100% calc(100% - 5px), calc(100% - 5px) 100%, 0 100%, 0 5px);">
                                {{ $lfg->game_mode ?? 'MULTIPLAYER' }}
                            </span>
                            <span class="text-green-300 text-sm font-mono">{{ $lfg->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-green-400 mb-2 font-mono">{{ Str::limit($lfg->title, 50) }}</h3>
                        <p class="text-green-200 mb-4">{{ Str::limit($lfg->description, 80) }}</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded mr-3 flex items-center justify-center text-white font-bold font-mono" style="background: linear-gradient(135deg, #5C8727 0%, #8BC34A 100%); clip-path: polygon(3px 0, 100% 0, 100% calc(100% - 3px), calc(100% - 3px) 100%, 0 100%, 0 3px);">
                                {{ strtoupper(substr($lfg->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-green-400 font-mono">{{ $lfg->user->name }}</div>
                                <div class="text-sm text-green-300 font-mono">{{ $lfg->city ?? 'TR' }}</div>
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
