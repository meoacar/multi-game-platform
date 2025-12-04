@extends('layouts.app')

@section('title', 'Oyun Seç - SquadBul')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="container mx-auto px-4 py-16">
        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-white mb-4">
                Hangi Oyunu Oynuyorsun?
            </h1>
            <p class="text-xl text-gray-400">
                Oyununu seç, takım arkadaşlarını bul!
            </p>
        </div>

        <!-- Oyun Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            @foreach($games as $game)
            <a href="{{ route('game.select', $game->slug) }}" 
               class="group relative overflow-hidden rounded-2xl bg-gray-800 hover:bg-gray-750 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                
                <!-- Oyun Görseli -->
                <div class="aspect-video relative overflow-hidden">
                    @if($game->banner_image)
                        <img src="{{ asset('storage/' . $game->banner_image) }}" 
                             alt="{{ $game->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-{{ $game->primary_color }}-600 to-{{ $game->primary_color }}-800 flex items-center justify-center">
                            <span class="text-6xl">🎮</span>
                        </div>
                    @endif
                    
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                </div>

                <!-- Oyun Bilgileri -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-2xl font-bold text-white group-hover:text-{{ $game->primary_color }}-400 transition-colors">
                            {{ $game->name }}
                        </h3>
                        
                        @auth
                            @if($game->has_profile ?? false)
                                <span class="px-3 py-1 bg-green-500/20 text-green-400 text-sm rounded-full border border-green-500/30">
                                    ✓ Profilin Var
                                </span>
                            @endif
                        @endauth
                    </div>

                    @if($game->description)
                        <p class="text-gray-400 text-sm mb-4 line-clamp-2">
                            {{ $game->description }}
                        </p>
                    @endif

                    <!-- Oyuncu Sayısı -->
                    <div class="flex items-center text-gray-500 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                        <span>Topluluğa Katıl</span>
                    </div>
                </div>

                <!-- Hover Effect Border -->
                <div class="absolute inset-0 border-2 border-transparent group-hover:border-{{ $game->primary_color }}-500 rounded-2xl transition-colors pointer-events-none"></div>
            </a>
            @endforeach
        </div>

        @guest
        <!-- Giriş Yap Çağrısı -->
        <div class="text-center mt-16">
            <div class="inline-block bg-gray-800 rounded-xl p-8 border border-gray-700">
                <p class="text-gray-300 mb-4">
                    Profilini oluştur, takım arkadaşlarını bul!
                </p>
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('login') }}" 
                       class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                        Giriş Yap
                    </a>
                    <a href="{{ route('register') }}" 
                       class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors">
                        Kayıt Ol
                    </a>
                </div>
            </div>
        </div>
        @endguest
    </div>
</div>
@endsection
