@extends('layouts.app')

@section('title', 'CS:GO - Counter-Strike Topluluğu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- CS:GO Hero Section - Industrial -->
    <div class="relative rounded-3xl overflow-hidden mb-12 shadow-2xl" style="background: #0d1117; border: 2px solid transparent; border-image: linear-gradient(90deg, #F7931E 0%, #00A8E8 50%, #F7931E 100%) 1;">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: linear-gradient(rgba(247, 147, 30, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 168, 232, 0.05) 1px, transparent 1px); background-size: 30px 30px;"></div>
        </div>
        <div class="relative px-8 py-20 md:px-16 md:py-32">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-20 h-20 rounded-xl flex items-center justify-center shadow-2xl animate-pulse" style="background: linear-gradient(135deg, #F7931E 0%, #00A8E8 100%); box-shadow: 0 0 40px rgba(247, 147, 30, 0.6);">
                    <span class="text-4xl">🔫</span>
                </div>
                <div>
                    <div class="text-orange-400 font-bold text-lg mb-1 tracking-widest font-mono">TACTICAL FPS</div>
                    <h1 class="text-5xl md:text-7xl font-black tracking-wide font-mono" style="background: linear-gradient(135deg, #F7931E 0%, #00A8E8 50%, #F7931E 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 0 0 30px rgba(247, 147, 30, 0.5);">
                        CS:GO
                    </h1>
                </div>
            </div>
            <p class="text-2xl md:text-3xl mb-8 text-orange-200 max-w-3xl font-bold font-mono">
                5v5 Competitive. Bomb Defusal. Tactical Gameplay.<br>
                <span class="text-orange-400">Türkiye'nin en rekabetçi CS:GO topluluğu!</span>
            </p>
            <div class="flex flex-wrap gap-4">
                @auth
                    <a href="{{ route('lfg.create') }}" class="px-10 py-5 font-black rounded-xl hover:shadow-2xl transition-all transform hover:scale-105 text-lg font-mono" style="background: linear-gradient(135deg, #F7931E 0%, #FF6B35 50%, #00A8E8 100%); color: white; box-shadow: 0 0 40px rgba(247, 147, 30, 0.6); border: 2px solid #F7931E; border-right: 2px solid #00A8E8;">
                        ▸ FIND TEAM
                    </a>
                    <a href="{{ route('clans.create') }}" class="px-10 py-5 bg-black/50 backdrop-blur-md text-orange-400 font-black rounded-xl hover:bg-black/70 transition-all border-2 text-lg font-mono" style="border-image: linear-gradient(90deg, #F7931E 0%, #00A8E8 100%) 1;">
                        ▸ CREATE CLAN
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-10 py-5 font-black rounded-xl hover:shadow-2xl transition-all transform hover:scale-105 text-lg font-mono" style="background: linear-gradient(135deg, #F7931E 0%, #FF6B35 50%, #00A8E8 100%); color: white; box-shadow: 0 0 40px rgba(247, 147, 30, 0.6); border: 2px solid #F7931E; border-right: 2px solid #00A8E8;">
                        ▸ JOIN NOW
                    </a>
                    <a href="{{ route('login') }}" class="px-10 py-5 bg-black/50 backdrop-blur-md text-orange-400 font-black rounded-xl hover:bg-black/70 transition-all border-2 text-lg font-mono" style="border-image: linear-gradient(90deg, #F7931E 0%, #00A8E8 100%) 1;">
                        ▸ LOGIN
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- CS:GO Stats - Competitive Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(13, 17, 23, 0.9) 0%, rgba(23, 27, 33, 0.7) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #F7931E 0%, #00A8E8 100%) 1; border-left: 3px solid #F7931E;">
            <div class="text-5xl font-black text-orange-400 mb-2 font-mono">{{ number_format($stats['total_users']) }}</div>
            <div class="text-orange-200 font-bold tracking-wider font-mono">PLAYERS</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(13, 17, 23, 0.9) 0%, rgba(23, 27, 33, 0.7) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #F7931E 0%, #00A8E8 100%) 1; border-left: 3px solid #F7931E;">
            <div class="text-5xl font-black text-orange-400 mb-2 font-mono">{{ number_format($stats['active_users']) }}</div>
            <div class="text-orange-200 font-bold tracking-wider font-mono">ONLINE</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(13, 17, 23, 0.9) 0%, rgba(23, 27, 33, 0.7) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #F7931E 0%, #00A8E8 100%) 1; border-left: 3px solid #F7931E;">
            <div class="text-5xl font-black text-orange-400 mb-2 font-mono">{{ number_format($stats['active_lfg']) }}</div>
            <div class="text-orange-200 font-bold tracking-wider font-mono">LFG</div>
        </div>
        <div class="glass-card p-6 text-center transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(13, 17, 23, 0.9) 0%, rgba(23, 27, 33, 0.7) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #F7931E 0%, #00A8E8 100%) 1; border-left: 3px solid #F7931E;">
            <div class="text-5xl font-black text-orange-400 mb-2 font-mono">{{ number_format($stats['total_clans']) }}</div>
            <div class="text-orange-200 font-bold tracking-wider font-mono">TEAMS</div>
        </div>
    </div>

    <!-- CS:GO Özellikler - Game Modes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(247, 147, 30, 0.2) 0%, rgba(0, 168, 232, 0.1) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #F7931E 0%, #00A8E8 100%) 1; border-right: 2px solid #00A8E8;">
            <div class="text-6xl mb-4">⚔️</div>
            <h3 class="text-2xl font-black text-orange-400 mb-3 tracking-wider font-mono">COMPETITIVE</h3>
            <p class="text-orange-200 mb-4 font-mono">5v5 ranked maçlar. Global Elite'e birlikte ulaş!</p>
            <a href="{{ route('lfg.index') }}" class="inline-flex items-center text-orange-400 font-bold hover:text-orange-300 font-mono">
                ▸ FIND TEAM
            </a>
        </div>

        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(247, 147, 30, 0.2) 0%, rgba(0, 168, 232, 0.1) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #F7931E 0%, #00A8E8 100%) 1; border-right: 2px solid #00A8E8;">
            <div class="text-6xl mb-4">🎯</div>
            <h3 class="text-2xl font-black text-orange-400 mb-3 tracking-wider font-mono">SCRIMS</h3>
            <p class="text-orange-200 mb-4 font-mono">Klanını kur, antrenman maçları yap ve taktikler geliştir!</p>
            <a href="{{ route('clans.index') }}" class="inline-flex items-center text-orange-400 font-bold hover:text-orange-300 font-mono">
                ▸ JOIN CLAN
            </a>
        </div>

        <div class="glass-card p-8 transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, rgba(247, 147, 30, 0.2) 0%, rgba(0, 168, 232, 0.1) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #F7931E 0%, #00A8E8 100%) 1; border-right: 2px solid #00A8E8;">
            <div class="text-6xl mb-4">🏆</div>
            <h3 class="text-2xl font-black text-orange-400 mb-3 tracking-wider font-mono">TOURNAMENTS</h3>
            <p class="text-orange-200 mb-4 font-mono">Turnuvalara katıl, ödülleri kazan ve şöhrete ulaş!</p>
            <a href="{{ route('tournaments.index') }}" class="inline-flex items-center text-orange-400 font-bold hover:text-orange-300 font-mono">
                ▸ COMPETE
            </a>
        </div>
    </div>

    <!-- Son İlanlar -->
    @if($recentLfg->count() > 0)
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-4xl font-black text-orange-400 tracking-wider font-mono">▸ ACTIVE LFG</h2>
            <a href="{{ route('lfg.index') }}" class="text-orange-400 hover:text-orange-300 font-bold flex items-center tracking-wider font-mono">
                VIEW ALL ▸
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($recentLfg as $lfg)
                <a href="{{ route('lfg.show', $lfg->id) }}" class="glass-card overflow-hidden hover:shadow-2xl transition-all transform hover:scale-105" style="background: linear-gradient(135deg, rgba(13, 17, 23, 0.9) 0%, rgba(23, 27, 33, 0.7) 100%); border: 2px solid transparent; border-image: linear-gradient(135deg, #F7931E 0%, #00A8E8 100%) 1; border-right: 2px solid #00A8E8;">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-orange-500/30 text-orange-400 rounded text-sm font-bold border border-orange-500 font-mono">
                                {{ $lfg->game_mode ?? 'COMPETITIVE' }}
                            </span>
                            <span class="text-orange-300 text-sm font-mono">{{ $lfg->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-orange-400 mb-2 font-mono">{{ Str::limit($lfg->title, 50) }}</h3>
                        <p class="text-orange-200 mb-4">{{ Str::limit($lfg->description, 80) }}</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded mr-3 flex items-center justify-center text-white font-bold font-mono" style="background: linear-gradient(135deg, #F7931E 0%, #00A8E8 100%);">
                                {{ strtoupper(substr($lfg->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-orange-400 font-mono">{{ $lfg->user->name }}</div>
                                <div class="text-sm text-orange-300 font-mono">{{ $lfg->city ?? 'TR' }}</div>
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
