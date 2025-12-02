@extends('admin.layout')

@section('title', 'Klan Yönetimi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ view: 'grid' }">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-400 to-pink-600 bg-clip-text text-transparent">
                    👑 Klan Yönetimi
                </h1>
                <p class="text-gray-400 mt-2">Klanları görüntüle, düzenle ve yönet</p>
            </div>
            <a href="{{ route('admin.clans.applications') }}" 
                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg">
                📋 Başvuruları Görüntüle
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 shadow-xl">
                <p class="text-purple-100 text-sm mb-1">Toplam Klan</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['total']) }}</p>
                <p class="text-purple-100 text-xs mt-1">Tüm zamanlar</p>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 shadow-xl">
                <p class="text-blue-100 text-sm mb-1">Doğrulanmış</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['verified']) }}</p>
                <p class="text-blue-100 text-xs mt-1">Verified</p>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 shadow-xl">
                <p class="text-yellow-100 text-sm mb-1">Bekliyor</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['unverified']) }}</p>
                <p class="text-yellow-100 text-xs mt-1">Doğrulanmamış</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 shadow-xl">
                <p class="text-green-100 text-sm mb-1">Toplam Üye</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['total_members']) }}</p>
                <p class="text-green-100 text-xs mt-1">Aktif üyeler</p>
            </div>
            <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-4 shadow-xl">
                <p class="text-pink-100 text-sm mb-1">Bugün</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['today']) }}</p>
                <p class="text-pink-100 text-xs mt-1">Yeni klan</p>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 shadow-xl border border-gray-700">
                <p class="text-gray-400 text-sm mb-1">Bu Hafta</p>
                <p class="text-2xl font-bold text-white">{{ number_format($stats['this_week']) }}</p>
            </div>
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 shadow-xl border border-gray-700">
                <p class="text-gray-400 text-sm mb-1">Bu Ay</p>
                <p class="text-2xl font-bold text-white">{{ number_format($stats['this_month']) }}</p>
            </div>
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 shadow-xl border border-gray-700">
                <p class="text-gray-400 text-sm mb-1">Ortalama Üye</p>
                <p class="text-2xl font-bold text-white">{{ number_format($stats['avg_members'], 1) }}</p>
            </div>
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 shadow-xl border border-gray-700">
                <p class="text-gray-400 text-sm mb-1">Bekleyen Başvuru</p>
                <p class="text-2xl font-bold text-white">{{ number_format($stats['pending_applications']) }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl p-6 mb-6 border border-gray-700">
            <form method="GET" action="{{ route('admin.clans.index') }}" class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">🔍 Filtreler</h3>
                    <div class="flex gap-2">
                        <button type="button" @click="view = 'grid'" 
                            :class="view === 'grid' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300'"
                            class="px-3 py-2 rounded-lg hover:opacity-80 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button type="button" @click="view = 'list'" 
                            :class="view === 'list' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300'"
                            class="px-3 py-2 rounded-lg hover:opacity-80 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">🔎 Arama</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Klan adı, lider..."
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <!-- Game -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">🎮 Oyun</label>
                        <select name="game_id" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500">
                            <option value="">Tümü</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>
                                    {{ $game->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">📍 Şehir</label>
                        <select name="city" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500">
                            <option value="">Tümü</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Verified -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">✓ Doğrulanmış</label>
                        <select name="is_verified" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500">
                            <option value="">Tümü</option>
                            <option value="yes" {{ request('is_verified') == 'yes' ? 'selected' : '' }}>Evet</option>
                            <option value="no" {{ request('is_verified') == 'no' ? 'selected' : '' }}>Hayır</option>
                        </select>
                    </div>

                    <!-- Min Rank -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">🏆 Min Rank</label>
                        <select name="min_rank" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500">
                            <option value="">Tümü</option>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank }}" {{ request('min_rank') == $rank ? 'selected' : '' }}>
                                    {{ $rank }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-lg hover:from-purple-600 hover:to-pink-700 transition-all shadow-lg">
                        Filtrele
                    </button>
                    <a href="{{ route('admin.clans.index') }}" class="px-6 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors">
                        Temizle
                    </a>
                </div>
            </form>
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($clans as $clan)
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden border border-gray-700 hover:border-purple-500 transition-all hover:shadow-purple-500/20">
                <!-- Header -->
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-3xl font-bold text-white">
                            {{ substr($clan->name, 0, 1) }}
                        </div>
                        <div class="flex flex-col gap-2">
                            @if($clan->is_verified)
                                <span class="px-3 py-1 bg-blue-400 text-blue-900 text-xs font-bold rounded-full flex items-center gap-1">
                                    ✓ Doğrulanmış
                                </span>
                            @endif
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-1">{{ $clan->name }}</h3>
                    <p class="text-purple-100 text-sm">{{ $clan->game->name }}</p>
                </div>

                <!-- Body -->
                <div class="p-4 space-y-3">
                    <!-- Leader Info -->
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-700">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($clan->leader->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Lider</p>
                            <p class="text-white font-semibold">{{ $clan->leader->name }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($clan->description)
                    <div class="bg-gray-700/50 rounded-lg p-3">
                        <p class="text-gray-300 text-sm line-clamp-2">{{ $clan->description }}</p>
                    </div>
                    @endif

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-gray-700/50 rounded-lg p-2 text-center">
                            <p class="text-xs text-gray-400">Üye Sayısı</p>
                            <p class="text-lg font-bold text-purple-400">{{ $clan->member_count ?? 1 }}/{{ $clan->max_members ?? 50 }}</p>
                        </div>
                        @if($clan->city)
                        <div class="bg-gray-700/50 rounded-lg p-2 text-center">
                            <p class="text-xs text-gray-400">Şehir</p>
                            <p class="text-lg font-bold text-pink-400">{{ $clan->city }}</p>
                        </div>
                        @endif
                        @if($clan->min_rank)
                        <div class="bg-gray-700/50 rounded-lg p-2 text-center">
                            <p class="text-xs text-gray-400">Min Rank</p>
                            <p class="text-sm font-bold text-orange-400">{{ $clan->min_rank }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Date -->
                    <div class="text-xs text-gray-400 pt-2 border-t border-gray-700">
                        📅 {{ $clan->created_at->format('d.m.Y H:i') }}
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.clans.show', $clan->id) }}" 
                            class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Detay
                        </a>
                        <a href="{{ route('admin.clans.edit', $clan->id) }}" 
                            class="flex-1 text-center px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm">
                            Düzenle
                        </a>
                        <form action="{{ route('admin.clans.toggle-verified', $clan->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 {{ $clan->is_verified ? 'bg-blue-600' : 'bg-gray-600' }} text-white rounded-lg hover:opacity-80 transition-opacity text-sm">
                                {{ $clan->is_verified ? '✓' : '○' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-400 text-lg">Klan bulunamadı</p>
            </div>
            @endforelse
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Klan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Lider</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Oyun</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Şehir</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Üye</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-300 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($clans as $clan)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                    {{ substr($clan->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-white">{{ $clan->name }}</p>
                                        @if($clan->is_verified)
                                            <span class="text-blue-400 text-lg" title="Doğrulanmış">✓</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400 truncate">{{ Str::limit($clan->description, 40) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-white">{{ $clan->leader->name }}</div>
                            <div class="text-xs text-gray-400">{{ $clan->leader->profile->nickname ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $clan->game->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $clan->city ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-purple-600 text-white rounded-full text-xs font-semibold">
                                {{ $clan->member_count ?? 1 }} / {{ $clan->max_members ?? 50 }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($clan->is_verified)
                                <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-xs font-semibold">✓ Doğrulanmış</span>
                            @else
                                <span class="px-3 py-1 bg-gray-600 text-gray-300 rounded-full text-xs font-semibold">Normal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $clan->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.clans.show', $clan->id) }}" 
                                    class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    Detay
                                </a>
                                <a href="{{ route('admin.clans.edit', $clan->id) }}" 
                                    class="px-3 py-1 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm">
                                    Düzenle
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            Klan bulunamadı
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($clans->hasPages())
        <div class="mt-6 bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl px-6 py-4 border border-gray-700">
            {{ $clans->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
