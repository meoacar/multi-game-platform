@extends('layouts.app')

@section('title', 'Takımlar')

@section('content')
<div class="min-h-screen bg-cover bg-center bg-fixed" style="background-image: url('{{ asset('arkaplan/7.jpg') }}');">
    <div class="min-h-screen bg-black/60 backdrop-blur-sm">
<!-- Hero Banner -->
<div class="relative bg-gradient-to-r from-green-600 via-emerald-600 to-green-700 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.05) 35px, rgba(255,255,255,.05) 70px);"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-5xl font-black mb-2">👥 TAKIMLAR</h1>
                <p class="text-xl text-white/90">Takıma katıl veya kendi takımını oluştur</p>
            </div>
            @auth
                <a href="{{ route('squads.create') }}" 
                    class="bg-white text-green-600 hover:bg-green-50 px-8 py-4 rounded-xl font-black text-lg shadow-xl transition-all transform hover:scale-105">
                    + YENİ TAKIM OLUŞTUR
                </a>
            @endauth
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Filtreler -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Takım ara..."
                    class="w-full bg-black/30 border-2 border-green-500/30 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:border-green-500 focus:outline-none">
            </div>
            <div>
                <select name="game_mode" 
                    class="w-full bg-black/30 border-2 border-green-500/30 rounded-xl px-4 py-3 text-white focus:border-green-500 focus:outline-none">
                    <option value="">Tüm Modlar</option>
                    <option value="TPP" {{ request('game_mode') == 'TPP' ? 'selected' : '' }}>TPP</option>
                    <option value="FPP" {{ request('game_mode') == 'FPP' ? 'selected' : '' }}>FPP</option>
                </select>
            </div>
            <div>
                <button type="submit" 
                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 rounded-xl transition-all">
                    🔍 Ara
                </button>
            </div>
        </form>
    </div>

    <!-- Takımlar Grid -->
    @if($squads->isEmpty())
        <div class="text-center py-16">
            <div class="text-8xl mb-6">👥</div>
            <h3 class="text-3xl font-bold text-white mb-3">Henüz Takım Yok</h3>
            <p class="text-gray-400 mb-6 text-lg">İlk takımı sen oluştur!</p>
            @auth
                <a href="{{ route('squads.create') }}" 
                    class="inline-block bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-4 rounded-xl font-black text-lg shadow-xl transition-all transform hover:scale-105">
                    + YENİ TAKIM OLUŞTUR
                </a>
            @endauth
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($squads as $squad)
            <a href="{{ route('squads.show', $squad->slug) }}" 
                class="bg-gradient-to-br from-gray-900/40 to-black/40 backdrop-blur-sm rounded-2xl border-2 border-green-500/30 hover:border-green-500/60 overflow-hidden shadow-xl transition-all transform hover:-translate-y-2">
                
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-4 py-3 border-b-2 border-green-500/50">
                    <h3 class="text-xl font-black text-white truncate">{{ $squad->name }}</h3>
                </div>

                <div class="p-6">
                    <!-- Lider -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center text-white font-bold">
                            {{ substr($squad->leader->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Lider</p>
                            <p class="text-white font-bold">{{ $squad->leader->name }}</p>
                        </div>
                    </div>

                    <!-- Bilgiler -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Üye Sayısı</span>
                            <span class="text-white font-bold">{{ $squad->members->count() }}/{{ $squad->max_members }}</span>
                        </div>
                        @if($squad->game_mode)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Mod</span>
                            <span class="text-green-400 font-bold">{{ $squad->game_mode }}</span>
                        </div>
                        @endif
                        @if($squad->rank_requirement)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Rütbe</span>
                            <span class="text-yellow-400 font-bold">{{ $squad->rank_requirement }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Durum -->
                    @if($squad->isFull())
                        <div class="bg-red-500/20 border border-red-500/30 rounded-lg px-3 py-2 text-center">
                            <span class="text-red-400 font-bold text-sm">❌ DOLU</span>
                        </div>
                    @else
                        <div class="bg-green-500/20 border border-green-500/30 rounded-lg px-3 py-2 text-center">
                            <span class="text-green-400 font-bold text-sm">✅ YER VAR</span>
                        </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $squads->links() }}
        </div>
    @endif
</div>
    </div>
</div>
@endsection
