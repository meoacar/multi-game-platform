@extends('layouts.app')

@section('title', 'Takım Arama İlanları - PUBG Mobile Topluluk')

@section('content')
<!-- PUBG Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" 
     style="background-image: url('/arkaplan/fFetCZ0H0O_x8QSHv6LGz.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    
    <!-- Karartma Katmanı -->
    <div class="absolute inset-0 bg-black/60"></div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-orange-900/30 via-transparent to-black/60"></div>
    
    <!-- Animasyonlu Işık Efektleri -->
    <div class="absolute inset-0">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl animate-pulse" style="animation-duration: 4s;"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse" style="animation-duration: 6s; animation-delay: 1s;"></div>
    </div>
</div>

<div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black bg-gradient-to-r from-orange-500 to-red-600 bg-clip-text text-transparent mb-2">🎯 Takım Arama İlanları</h1>
            <p class="text-gray-600 text-lg">Seviyene uygun oyuncularla takım kur ve zafere ulaş</p>
        </div>
        @auth
            <a href="{{ route('lfg.create') }}" class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all transform hover:scale-105 whitespace-nowrap">
                + Yeni İlan Aç
            </a>
        @endauth
    </div>

    <!-- Filters -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-6 mb-8">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Filtrele
        </h3>
        <form method="GET" action="{{ route('lfg.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Oyun -->
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">🎮 Oyun</label>
                <select name="game_id" class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all">
                    <option value="">Tümü</option>
                    @foreach($games as $game)
                        <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>
                            {{ $game->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Şehir -->
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">📍 Şehir</label>
                <select name="city" class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all">
                    <option value="">Tümü</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Oyun Stili -->
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">🎯 Oyun Stili</label>
                <select name="play_style" class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all">
                    <option value="">Tümü</option>
                    <option value="try-hard" {{ request('play_style') == 'try-hard' ? 'selected' : '' }}>Try-Hard</option>
                    <option value="chill" {{ request('play_style') == 'chill' ? 'selected' : '' }}>Chill</option>
                    <option value="fun-first" {{ request('play_style') == 'fun-first' ? 'selected' : '' }}>Fun First</option>
                    <option value="competitive" {{ request('play_style') == 'competitive' ? 'selected' : '' }}>Competitive</option>
                    <option value="casual" {{ request('play_style') == 'casual' ? 'selected' : '' }}>Casual</option>
                </select>
            </div>

            <!-- Mikrofon -->
            <div class="flex items-end">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="microphone_required" value="1" {{ request('microphone_required') ? 'checked' : '' }} class="rounded border-2 border-white/20 bg-white/10 text-orange-600 focus:ring-orange-500">
                    <span class="ml-2 text-sm font-bold text-gray-300">🎤 Mikrofon Şart</span>
                </label>
                <button type="submit" class="ml-auto bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-6 py-2 rounded-xl font-bold shadow-lg shadow-orange-500/30 transition-all transform hover:scale-105">
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Posts List -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($posts as $post)
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl hover:shadow-orange-500/20 transition-all duration-300 p-6 border border-white/10 hover:border-orange-500/50 transform hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <!-- Title -->
                        <h3 class="text-2xl font-black text-white mb-2">
                            <a href="{{ route('lfg.show', $post->id) }}" class="hover:text-orange-500 transition-colors">
                                {{ $post->title }}
                            </a>
                        </h3>

                        <!-- Meta Info -->
                        <div class="flex items-center space-x-4 text-sm text-gray-400 mb-3">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                                {{ $post->user->name }}
                            </span>
                            <span>{{ $post->game->name }}</span>
                            @if($post->city)
                                <span>📍 {{ $post->city }}</span>
                            @endif
                            @if($post->mode)
                                <span>🎮 {{ $post->mode }}</span>
                            @endif
                        </div>

                        <!-- Description -->
                        <p class="text-gray-300 mb-4">
                            {{ Str::limit($post->description, 150) }}
                        </p>

                        <!-- Tags -->
                        <div class="flex flex-wrap gap-2">
                            @if($post->min_rank || $post->max_rank)
                                <span class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-xs font-bold px-3 py-1.5 rounded-full border border-blue-300">
                                    🏆 Rank: {{ $post->min_rank ?? '?' }} - {{ $post->max_rank ?? '?' }}
                                </span>
                            @endif
                            @if($post->play_style_tag)
                                <span class="bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 text-xs font-bold px-3 py-1.5 rounded-full border border-purple-300">
                                    ⚡ {{ ucfirst($post->play_style_tag) }}
                                </span>
                            @endif
                            @if($post->microphone_required)
                                <span class="bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-xs font-bold px-3 py-1.5 rounded-full border border-green-300">
                                    🎤 Mikrofon Şart
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="ml-6 text-right">
                        <div class="text-sm text-gray-400">
                            {{ $post->views_count }} görüntülenme
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $post->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-12 text-center">
                <div class="text-6xl mb-4">🎯</div>
                <h3 class="text-2xl font-black text-white mb-2">Henüz İlan Bulunmuyor</h3>
                <p class="text-gray-400 text-lg mb-6">İlk ilanı sen oluştur ve takım arkadaşlarını bul!</p>
                @auth
                    <a href="{{ route('lfg.create') }}" class="inline-block bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all transform hover:scale-105">
                        İlk İlanı Sen Aç!
                    </a>
                @endauth
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
@endsection
