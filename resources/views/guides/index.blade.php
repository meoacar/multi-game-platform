@extends('layouts.app')

@section('title', 'Rehber - PUBG Mobile Topluluk')

@section('content')
<div class="min-h-screen bg-cover bg-center bg-fixed" style="background-image: url('{{ asset('arkaplan/14.jpg') }}');">
    <div class="min-h-screen bg-black/60 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black bg-gradient-to-r from-orange-500 to-red-600 bg-clip-text text-transparent mb-2">📚 PUBG Mobile Rehber</h1>
            <p class="text-gray-400 text-lg">Oyun taktikleri, ipuçları ve stratejiler</p>
        </div>
        @auth
            <a href="{{ route('guide.create') }}" class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all transform hover:scale-105 whitespace-nowrap">
                + Rehber Yaz
            </a>
        @endauth
    </div>

    <!-- Search & Filter -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-6 mb-8">
        <form method="GET" action="{{ route('guide.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-300 mb-2">🔍 Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rehber başlığı ara..." class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-2 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all">
            </div>

            <!-- Game Filter -->
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">🎮 Oyun</label>
                <select name="game_id" class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-2 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all">
                    <option value="">Tümü</option>
                    @foreach($games as $game)
                        <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>
                            {{ $game->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <button type="submit" class="w-full md:w-auto bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-orange-500/30 transition-all transform hover:scale-105">
                    Ara
                </button>
            </div>
        </form>
    </div>

    <!-- Guides Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($guides as $guide)
            <a href="{{ route('guide.show', $guide->id) }}" class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl hover:shadow-orange-500/20 transition-all duration-300 overflow-hidden border border-white/10 hover:border-orange-500/50 transform hover:-translate-y-2">
                <!-- Image Placeholder -->
                <div class="h-48 bg-gradient-to-br from-orange-500/20 to-red-600/20 flex items-center justify-center">
                    <span class="text-6xl">📚</span>
                </div>
                
                <!-- Content -->
                <div class="p-6">
                    <h3 class="text-xl font-black text-white mb-2 line-clamp-2">
                        {{ $guide->title }}
                    </h3>
                    
                    <p class="text-gray-400 text-sm mb-4 line-clamp-3">
                        {{ Str::limit(strip_tags($guide->content), 120) }}
                    </p>
                    
                    <!-- Meta -->
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <div class="flex items-center space-x-2">
                            <span>👤 {{ $guide->user->name }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span>👁️ {{ $guide->views_count }}</span>
                            <span>{{ $guide->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-12 text-center">
                <div class="text-6xl mb-4">📚</div>
                <h3 class="text-2xl font-black text-white mb-2">Henüz Rehber Bulunmuyor</h3>
                <p class="text-gray-400 text-lg mb-6">İlk rehberi sen yaz ve topluluğa katkıda bulun!</p>
                @auth
                    <a href="{{ route('guide.create') }}" class="inline-block bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all transform hover:scale-105">
                        İlk Rehberi Yaz!
                    </a>
                @endauth
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($guides->hasPages())
        <div class="mt-8">
            {{ $guides->links() }}
        </div>
    @endif
        </div>
    </div>
</div>
@endsection
