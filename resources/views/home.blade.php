@extends('layouts.app')

@section('title', 'Ana Sayfa - ' . ($currentGame->name ?? 'Oyun') . ' Topluluk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-game-primary via-game-secondary to-game-primary rounded-3xl shadow-2xl overflow-hidden mb-12">
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.1) 35px, rgba(255,255,255,.1) 70px);"></div>
        </div>
        
        <div class="relative px-8 py-16 md:px-16 md:py-24 text-white">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-4xl">
                    {{ $currentGame->icon ?? '🎮' }}
                </div>
                <div>
                    <div class="text-sm font-bold text-white/80 uppercase tracking-wider">Türkiye'nin En Büyük</div>
                    <h1 class="text-4xl md:text-6xl font-black animate-fade-in">
                        <span class="text-yellow-300">{{ $currentGame->name ?? 'Oyun' }}</span> Topluluğu
                    </h1>
                </div>
            </div>
            <p class="text-xl md:text-2xl mb-8 text-white/90 max-w-2xl">
                {{ $currentGame->description ?? 'Binlerce oyuncu ile takım kur, klan oluştur, turnuvalara katıl ve topluluğun bir parçası ol!' }}
            </p>
            <div class="flex flex-wrap gap-4">
                @auth
                    <a href="{{ route('lfg.create') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-gray-100 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                        🎯 İlan Oluştur
                    </a>
                    <a href="{{ route('clans.create') }}" class="px-8 py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-xl hover:bg-white/20 transition-all border-2 border-white/30">
                        🛡️ Klan Kur
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-gray-100 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                        🚀 Hemen Katıl
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-xl hover:bg-white/20 transition-all border-2 border-white/30">
                        Giriş Yap
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition-all">
            <div class="text-4xl font-black text-blue-600 mb-2">{{ number_format($stats['total_users']) }}</div>
            <div class="text-gray-600 font-medium">Toplam Oyuncu</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition-all">
            <div class="text-4xl font-black text-green-600 mb-2">{{ number_format($stats['active_users']) }}</div>
            <div class="text-gray-600 font-medium">Aktif Oyuncu</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition-all">
            <div class="text-4xl font-black text-purple-600 mb-2">{{ number_format($stats['active_lfg']) }}</div>
            <div class="text-gray-600 font-medium">Aktif İlan</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition-all">
            <div class="text-4xl font-black text-orange-600 mb-2">{{ number_format($stats['total_clans']) }}</div>
            <div class="text-gray-600 font-medium">Toplam Klan</div>
        </div>
    </div>

    <!-- Recent LFG Posts -->
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-black text-gray-900">🎯 Son İlanlar</h2>
            <a href="{{ route('lfg.index') }}" class="text-blue-600 hover:text-blue-700 font-bold flex items-center">
                Tümünü Gör
                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if($recentLfg->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($recentLfg as $lfg)
                    <a href="{{ route('lfg.show', $lfg->id) }}" class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all transform hover:scale-105">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold">
                                    {{ $lfg->game->name ?? 'PUBG Mobile' }}
                                </span>
                                <span class="text-gray-500 text-sm">{{ $lfg->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ Str::limit($lfg->title, 50) }}</h3>
                            <p class="text-gray-600 mb-4">{{ Str::limit($lfg->description, 80) }}</p>
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($lfg->user->name) }}&background=3B82F6&color=fff" 
                                     class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <div class="font-bold text-gray-900">{{ $lfg->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $lfg->city ?? 'Türkiye' }}</div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div class="text-6xl mb-4">🎯</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Henüz İlan Yok</h3>
                <p class="text-gray-600 mb-6">İlk ilanı sen oluştur ve takım arkadaşlarını bul!</p>
                @auth
                    <a href="{{ route('lfg.create') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all">
                        İlan Oluştur
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all">
                        Kayıt Ol
                    </a>
                @endauth
            </div>
        @endif
    </div>

    <!-- Popular Clans -->
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-black text-gray-900">🛡️ Popüler Klanlar</h2>
            <a href="{{ route('clans.index') }}" class="text-purple-600 hover:text-purple-700 font-bold flex items-center">
                Tümünü Gör
                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if($popularClans->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($popularClans as $clan)
                    <a href="{{ route('clans.show', $clan->slug) }}" class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all transform hover:scale-105">
                        <div class="h-32 bg-gradient-to-br from-purple-600 to-pink-600"></div>
                        <div class="p-6 -mt-16">
                            <div class="w-24 h-24 bg-white rounded-2xl shadow-xl flex items-center justify-center mb-4 border-4 border-white">
                                <span class="text-4xl font-black text-purple-600">{{ strtoupper(substr($clan->name, 0, 2)) }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $clan->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ Str::limit($clan->description, 80) }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span class="font-bold">{{ $clan->members_count }} Üye</span>
                                </div>
                                @if($clan->is_verified)
                                    <span class="text-blue-600" title="Doğrulanmış Klan">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div class="text-6xl mb-4">🛡️</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Henüz Klan Yok</h3>
                <p class="text-gray-600 mb-6">İlk klanı sen kur ve liderliği ele al!</p>
                @auth
                    <a href="{{ route('clans.create') }}" class="inline-block px-6 py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition-all">
                        Klan Kur
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-block px-6 py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition-all">
                        Kayıt Ol
                    </a>
                @endauth
            </div>
        @endif
    </div>

    <!-- Features -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-8 text-white">
            <div class="text-5xl mb-4">🎯</div>
            <h3 class="text-2xl font-bold mb-3">Takım Bul</h3>
            <p class="text-blue-100 mb-4">İlanlar oluştur, başvuruları değerlendir ve ideal takım arkadaşlarını bul.</p>
            <a href="{{ route('lfg.index') }}" class="inline-flex items-center text-white font-bold hover:text-blue-100">
                Keşfet
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white">
            <div class="text-5xl mb-4">🛡️</div>
            <h3 class="text-2xl font-bold mb-3">Klan Kur</h3>
            <p class="text-purple-100 mb-4">Kendi klanını oluştur, üyelerini yönet ve turnuvalara katıl.</p>
            <a href="{{ route('clans.index') }}" class="inline-flex items-center text-white font-bold hover:text-purple-100">
                Keşfet
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
