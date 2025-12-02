@extends('layouts.app')

@section('title', 'Klanlar - PUBG Mobile Topluluk | ' . $clans->total() . '+ Aktif Klan')
@section('description', 'PUBG Mobile için ' . $clans->total() . '+ aktif klan. Klan bul, başvur veya kendi klanını oluştur. Doğrulanmış klanlar, güçlü takımlar ve rekabetçi oyuncular seni bekliyor!')
@section('keywords', 'pubg mobile klan, pubg klan ara, pubg klan kur, pubg mobile türkiye klan, pubg klan başvuru, pubg mobile clan')
@section('canonical', route('clans.index'))

@section('og_title', 'PUBG Mobile Klanlar - ' . $clans->total() . '+ Aktif Klan')
@section('og_description', 'Türkiye\'nin en büyük PUBG Mobile klan platformu. ' . $clans->total() . '+ klan arasından senin için en uygun olanı bul!')
@section('og_type', 'website')

@push('structured-data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "PUBG Mobile Klanlar",
    "description": "{{ $clans->total() }}+ aktif PUBG Mobile klanı",
    "url": "{{ route('clans.index') }}",
    "numberOfItems": {{ $clans->total() }},
    "itemListElement": [
        @foreach($clans->take(10) as $index => $clan)
        {
            "@type": "Organization",
            "position": {{ $index + 1 }},
            "name": "{{ $clan->name }}",
            "description": "{{ Str::limit($clan->description, 100) }}",
            "url": "{{ route('clans.show', $clan->slug) }}",
            "memberOf": {
                "@type": "Game",
                "name": "{{ $clan->game->name }}"
            }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endpush

@section('content')
<!-- PUBG Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none bg-black">
    <div class="absolute inset-0" 
         style="background-image: url('/arkaplan/1.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.12;">
    </div>
    
    <!-- Gradient -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-transparent to-black/40"></div>
    
    <!-- Animasyonlu Işık Efektleri -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-1/3 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl animate-pulse" style="animation-duration: 5s;"></div>
        <div class="absolute bottom-0 right-1/3 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl animate-pulse" style="animation-duration: 7s; animation-delay: 1s;"></div>
    </div>
</div>

<div class="relative z-10 min-h-screen">
    <!-- Hero Section - Yeni Tasarım -->
    <div class="relative overflow-hidden py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Animated Badge -->
            <div class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-500/20 to-purple-500/20 backdrop-blur-xl border-2 border-blue-500/30 rounded-full px-6 py-3 mb-8 animate-bounce">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-400"></span>
                </span>
                <span class="text-lg font-black text-white drop-shadow-lg">{{ $clans->total() }} Aktif Klan</span>
            </div>
            
            <!-- Main Title -->
            <h1 class="text-7xl sm:text-8xl font-black mb-6 drop-shadow-2xl">
                <span class="bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent animate-pulse">
                    KLANINI BUL
                </span>
            </h1>
            <h2 class="text-4xl sm:text-5xl font-black text-white mb-8 drop-shadow-xl">
                Güçlen, Kazan! 🛡️
            </h2>
            
            <!-- Description -->
            <p class="text-2xl text-white/90 max-w-3xl mx-auto mb-12 font-bold drop-shadow-lg leading-relaxed">
                Türkiye'nin en güçlü PUBG Mobile klanları burada! Hemen katıl veya kendi klanını kur.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    <a href="{{ route('clans.create') }}" class="group relative inline-flex items-center px-10 py-5 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-2xl font-black text-xl shadow-2xl transition-all duration-300 transform hover:scale-110 hover:shadow-blue-500/50">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-2xl blur-xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
                        <svg class="relative w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="relative">Klan Oluştur</span>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="group relative inline-flex items-center px-10 py-5 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-2xl font-black text-xl shadow-2xl transition-all duration-300 transform hover:scale-110 hover:shadow-blue-500/50">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-2xl blur-xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
                        <span class="relative">Kayıt Ol ve Başla</span>
                    </a>
                @endauth
                
                <a href="#klanlar" class="inline-flex items-center px-10 py-5 bg-white/10 hover:bg-white/20 backdrop-blur-xl border-2 border-white/30 hover:border-white/50 text-white rounded-2xl font-black text-xl shadow-2xl transition-all duration-300 transform hover:scale-110">
                    <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Klan Ara
                </a>
            </div>
        </div>
    </div>

    <div id="klanlar" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Stats Cards - Yeni Tasarım -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            <!-- Toplam Klan -->
            <div class="group relative bg-gradient-to-br from-blue-500/20 via-cyan-500/20 to-blue-600/20 backdrop-blur-xl rounded-3xl border-2 border-blue-500/30 p-8 text-center hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/50">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                <div class="relative">
                    <div class="text-5xl mb-3">🛡️</div>
                    <div class="text-5xl font-black text-blue-400 mb-2">{{ $clans->total() }}</div>
                    <div class="text-sm font-bold text-blue-300 uppercase tracking-wider">Toplam Klan</div>
                </div>
            </div>

            <!-- Doğrulanmış -->
            <div class="group relative bg-gradient-to-br from-green-500/20 via-emerald-500/20 to-green-600/20 backdrop-blur-xl rounded-3xl border-2 border-green-500/30 p-8 text-center hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-green-500/50">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                <div class="relative">
                    <div class="text-5xl mb-3">✓</div>
                    <div class="text-5xl font-black text-green-400 mb-2">{{ $clans->where('is_verified', true)->count() }}</div>
                    <div class="text-sm font-bold text-green-300 uppercase tracking-wider">Doğrulanmış</div>
                </div>
            </div>

            <!-- Toplam Üye -->
            <div class="group relative bg-gradient-to-br from-purple-500/20 via-pink-500/20 to-purple-600/20 backdrop-blur-xl rounded-3xl border-2 border-purple-500/30 p-8 text-center hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/50">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-500/10 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                <div class="relative">
                    <div class="text-5xl mb-3">👥</div>
                    <div class="text-5xl font-black text-purple-400 mb-2">{{ $clans->sum('member_count') }}</div>
                    <div class="text-sm font-bold text-purple-300 uppercase tracking-wider">Toplam Üye</div>
                </div>
            </div>

            <!-- Üye Alıyor -->
            <div class="group relative bg-gradient-to-br from-orange-500/20 via-red-500/20 to-orange-600/20 backdrop-blur-xl rounded-3xl border-2 border-orange-500/30 p-8 text-center hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-orange-500/50">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-red-500/10 rounded-3xl blur-xl group-hover:blur-2xl transition-all"></div>
                <div class="relative">
                    <div class="text-5xl mb-3">🔥</div>
                    <div class="text-5xl font-black text-orange-400 mb-2">{{ $clans->where('member_count', '<', 'max_members')->count() }}</div>
                    <div class="text-sm font-bold text-orange-300 uppercase tracking-wider">Üye Alıyor</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-6 mb-8" x-data="{ showFilters: false }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtreler
                </h2>
                <button @click="showFilters = !showFilters" class="text-gray-400 hover:text-white transition-colors">
                    <svg x-show="!showFilters" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    <svg x-show="showFilters" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
            </div>

            <form method="GET" action="{{ route('clans.index') }}" x-show="showFilters" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Oyun -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">🎮 Oyun</label>
                    <select name="game_id" class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tüm Oyunlar</option>
                        @foreach($games as $game)
                            <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }} class="bg-gray-800">
                                {{ $game->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Şehir -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">📍 Şehir</label>
                    <select name="city" class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tüm Şehirler</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }} class="bg-gray-800">
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sıralama -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">🔄 Sıralama</label>
                    <select name="sort" class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }} class="bg-gray-800">En Yeni</option>
                        <option value="members" {{ request('sort') == 'members' ? 'selected' : '' }} class="bg-gray-800">En Kalabalık</option>
                        <option value="verified" {{ request('sort') == 'verified' ? 'selected' : '' }} class="bg-gray-800">Doğrulanmış</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-xl font-bold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                        Filtrele
                    </button>
                    <a href="{{ route('clans.index') }}" class="px-4 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                </div>

                <!-- Doğrulanmış Toggle -->
                <div class="md:col-span-2 lg:col-span-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }} class="sr-only peer">
                        <div class="relative w-11 h-6 bg-white/10 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-300">✓ Sadece Doğrulanmış Klanlar</span>
                    </label>
                </div>
            </form>
        </div>

        <!-- View Toggle -->
        <div class="flex items-center justify-between mb-6" x-data="{ view: 'grid' }">
            <div class="text-gray-400">
                <span class="font-semibold text-white">{{ $clans->total() }}</span> klan bulundu
            </div>
            <div class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-1">
                <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'" class="px-4 py-2 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button @click="view = 'list'" :class="view === 'list' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'" class="px-4 py-2 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Clans Grid -->
        <div x-data="{ view: 'grid' }">
            <!-- Grid View -->
            <div x-show="view === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @forelse($clans as $clan)
                    <div class="group relative bg-gradient-to-br from-white/5 to-white/0 border border-white/10 rounded-3xl overflow-hidden hover:border-blue-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-blue-500/20 transform hover:-translate-y-2">
                        <!-- Clan Header -->
                        <div class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-6">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="relative">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-2xl font-black text-white mb-2 group-hover:scale-105 transition-transform">
                                            {{ $clan->name }}
                                        </h3>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold text-white">
                                                {{ $clan->game->name }}
                                            </span>
                                            @if($clan->is_verified)
                                                <span class="px-3 py-1 bg-green-500/20 backdrop-blur-sm border border-green-500/30 rounded-full text-xs font-semibold text-green-300 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Doğrulanmış
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($clan->member_count >= $clan->max_members)
                                        <span class="px-3 py-1 bg-red-500/20 backdrop-blur-sm border border-red-500/30 rounded-full text-xs font-semibold text-red-300">
                                            Dolu
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-green-500/20 backdrop-blur-sm border border-green-500/30 rounded-full text-xs font-semibold text-green-300">
                                            Üye Alıyor
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Clan Body -->
                        <div class="p-6">
                            <!-- Description -->
                            <p class="text-gray-400 mb-4 line-clamp-3 leading-relaxed">
                                {{ Str::limit($clan->description, 120) }}
                            </p>

                            <!-- Stats -->
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="text-center bg-blue-500/10 border border-blue-500/20 rounded-xl p-3">
                                    <div class="text-2xl font-black text-blue-400">{{ $clan->member_count }}</div>
                                    <div class="text-xs text-gray-500">Üye</div>
                                </div>
                                <div class="text-center bg-purple-500/10 border border-purple-500/20 rounded-xl p-3">
                                    <div class="text-2xl font-black text-purple-400">{{ $clan->max_members }}</div>
                                    <div class="text-xs text-gray-500">Kapasite</div>
                                </div>
                                <div class="text-center bg-pink-500/10 border border-pink-500/20 rounded-xl p-3">
                                    <div class="text-2xl font-black text-pink-400">{{ $clan->level ?? 1 }}</div>
                                    <div class="text-xs text-gray-500">Level</div>
                                </div>
                            </div>

                            <!-- Meta Info -->
                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex items-center text-gray-400">
                                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                    </svg>
                                    <span class="text-white font-semibold">{{ $clan->leader->name }}</span>
                                </div>
                                @if($clan->city)
                                    <div class="flex items-center text-gray-400">
                                        <svg class="w-4 h-4 mr-2 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $clan->city }}
                                    </div>
                                @endif
                                @if($clan->min_rank || $clan->max_rank)
                                    <div class="flex items-center text-gray-400">
                                        <svg class="w-4 h-4 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        {{ $clan->min_rank ?? '?' }} - {{ $clan->max_rank ?? '?' }}
                                    </div>
                                @endif
                                <div class="flex items-center text-gray-400">
                                    <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $clan->created_at->diffForHumans() }}
                                </div>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('clans.show', $clan->slug) }}" class="block w-full text-center bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-blue-500/50">
                                Detayları Gör →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-12 text-center">
                        <div class="text-6xl mb-4">👥</div>
                        <p class="text-gray-400 text-lg mb-4">Henüz klan bulunmuyor</p>
                        @auth
                            <a href="{{ route('clans.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-bold hover:from-blue-600 hover:to-purple-700 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                İlk Klanı Sen Oluştur!
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-bold hover:from-blue-600 hover:to-purple-700 transition-all">
                                Kayıt Ol ve Klan Kur
                            </a>
                        @endauth
                    </div>
                @endforelse
            </div>

            <!-- List View -->
            <div x-show="view === 'list'" class="space-y-4 mb-8">
                @forelse($clans as $clan)
                    <div class="group bg-gradient-to-r from-white/5 to-white/0 border border-white/10 rounded-2xl p-6 hover:border-blue-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-6 flex-1">
                                <!-- Clan Avatar -->
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center text-3xl font-black text-white shadow-lg">
                                    {{ strtoupper(substr($clan->name, 0, 2)) }}
                                </div>

                                <!-- Clan Info -->
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-2xl font-bold text-white">{{ $clan->name }}</h3>
                                        @if($clan->is_verified)
                                            <span class="px-2 py-1 bg-green-500/20 border border-green-500/30 rounded-full text-xs font-semibold text-green-300">
                                                ✓
                                            </span>
                                        @endif
                                        @if($clan->member_count >= $clan->max_members)
                                            <span class="px-2 py-1 bg-red-500/20 border border-red-500/30 rounded-full text-xs font-semibold text-red-300">
                                                Dolu
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-green-500/20 border border-green-500/30 rounded-full text-xs font-semibold text-green-300">
                                                Üye Alıyor
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-400 mb-2 line-clamp-2">{{ Str::limit($clan->description, 150) }}</p>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                            </svg>
                                            {{ $clan->leader->name }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ $clan->game->name }}</span>
                                        @if($clan->city)
                                            <span>•</span>
                                            <span>📍 {{ $clan->city }}</span>
                                        @endif
                                        <span>•</span>
                                        <span>{{ $clan->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="hidden lg:flex items-center space-x-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-black text-blue-400">{{ $clan->member_count }}</div>
                                        <div class="text-xs text-gray-500">Üye</div>
                                    </div>
                                    <div class="text-gray-600">/</div>
                                    <div class="text-center">
                                        <div class="text-2xl font-black text-purple-400">{{ $clan->max_members }}</div>
                                        <div class="text-xs text-gray-500">Kapasite</div>
                                    </div>
                                </div>

                                <!-- Action -->
                                <a href="{{ route('clans.show', $clan->slug) }}" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                                    Detay →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-12 text-center">
                        <div class="text-6xl mb-4">👥</div>
                        <p class="text-gray-400 text-lg mb-4">Henüz klan bulunmuyor</p>
                        @auth
                            <a href="{{ route('clans.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-bold hover:from-blue-600 hover:to-purple-700 transition-all">
                                İlk Klanı Sen Oluştur!
                            </a>
                        @endauth
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($clans->hasPages())
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
                {{ $clans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
