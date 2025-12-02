@extends('admin.layout')

@section('title', 'LFG İlan Yönetimi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ selected: [], view: 'grid' }">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-400 to-pink-600 bg-clip-text text-transparent">
                📢 LFG İlan Yönetimi
            </h1>
            <p class="text-gray-400 mt-2">Takım arama ilanlarını görüntüle, düzenle ve yönet</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 shadow-xl">
                <p class="text-orange-100 text-sm mb-1">Toplam İlan</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['total']) }}</p>
                <p class="text-orange-100 text-xs mt-1">Tüm zamanlar</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 shadow-xl">
                <p class="text-green-100 text-sm mb-1">Açık İlanlar</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['open']) }}</p>
                <p class="text-green-100 text-xs mt-1">Aktif</p>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 shadow-xl">
                <p class="text-red-100 text-sm mb-1">Kapalı İlanlar</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['closed']) }}</p>
                <p class="text-red-100 text-xs mt-1">Tamamlandı</p>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 shadow-xl">
                <p class="text-yellow-100 text-sm mb-1">Öne Çıkan</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['featured']) }}</p>
                <p class="text-yellow-100 text-xs mt-1">Featured</p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 shadow-xl">
                <p class="text-purple-100 text-sm mb-1">Bugün</p>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['today']) }}</p>
                <p class="text-purple-100 text-xs mt-1">Yeni ilan</p>
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
                <p class="text-gray-400 text-sm mb-1">Toplam Görüntülenme</p>
                <p class="text-2xl font-bold text-white">{{ number_format($stats['total_views']) }}</p>
            </div>
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 shadow-xl border border-gray-700">
                <p class="text-gray-400 text-sm mb-1">Toplam Başvuru</p>
                <p class="text-2xl font-bold text-white">{{ number_format($stats['total_applications']) }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl p-6 mb-6 border border-gray-700">
            <form method="GET" action="{{ route('admin.lfg-posts.index') }}" class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">🔍 Filtreler</h3>
                    <div class="flex gap-2">
                        <button type="button" @click="view = 'grid'" 
                            :class="view === 'grid' ? 'bg-orange-600 text-white' : 'bg-gray-700 text-gray-300'"
                            class="px-3 py-2 rounded-lg hover:opacity-80 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button type="button" @click="view = 'list'" 
                            :class="view === 'list' ? 'bg-orange-600 text-white' : 'bg-gray-700 text-gray-300'"
                            class="px-3 py-2 rounded-lg hover:opacity-80 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">🔎 Arama</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Başlık, açıklama, kullanıcı..."
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>

                    <!-- Game -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">🎮 Oyun</label>
                        <select name="game_id" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-orange-500">
                            <option value="">Tümü</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>
                                    {{ $game->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">📊 Durum</label>
                        <select name="status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-orange-500">
                            <option value="">Tümü</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>✅ Açık</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>🔒 Kapalı</option>
                        </select>
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">📍 Şehir</label>
                        <select name="city" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-orange-500">
                            <option value="">Tümü</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Play Style -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">🎯 Oyun Stili</label>
                        <select name="play_style_tag" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-orange-500">
                            <option value="">Tümü</option>
                            @foreach($playStyles as $style)
                                <option value="{{ $style }}" {{ request('play_style_tag') == $style ? 'selected' : '' }}>
                                    {{ $style }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Featured -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">⭐ Öne Çıkan</label>
                        <select name="is_featured" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-orange-500">
                            <option value="">Tümü</option>
                            <option value="yes" {{ request('is_featured') == 'yes' ? 'selected' : '' }}>Evet</option>
                            <option value="no" {{ request('is_featured') == 'no' ? 'selected' : '' }}>Hayır</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">📅 Başlangıç</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-orange-500 to-pink-600 text-white rounded-lg hover:from-orange-600 hover:to-pink-700 transition-all shadow-lg">
                        Filtrele
                    </button>
                    <a href="{{ route('admin.lfg-posts.index') }}" class="px-6 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors">
                        Temizle
                    </a>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl p-4 mb-6 border border-gray-700" x-show="selected.length > 0">
            <div class="flex items-center justify-between">
                <div class="text-gray-300">
                    <span class="font-bold text-orange-400" x-text="selected.length"></span> ilan seçildi
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.lfg-posts.bulk-close') }}" class="inline"
                        @submit.prevent="if(confirm('Seçili ilanlar kapatılacak. Emin misiniz?')) { $el.querySelector('input[name=ids]').value = JSON.stringify(selected); $el.submit(); }">
                        @csrf
                        <input type="hidden" name="ids">
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            🔒 Toplu Kapat
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.lfg-posts.bulk-delete') }}" class="inline"
                        @submit.prevent="if(confirm('Seçili ilanlar silinecek. Emin misiniz?')) { $el.querySelector('input[name=ids]').value = JSON.stringify(selected); $el.submit(); }">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="ids">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            🗑️ Toplu Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($posts as $post)
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden border border-gray-700 hover:border-orange-500 transition-all hover:shadow-orange-500/20">
                <!-- Header -->
                <div class="bg-gradient-to-r from-orange-500 to-pink-600 p-4">
                    <div class="flex items-start justify-between mb-2">
                        <input type="checkbox" value="{{ $post->id }}" 
                            @change="$event.target.checked ? selected.push({{ $post->id }}) : selected = selected.filter(id => id !== {{ $post->id }})"
                            class="rounded border-white/30 bg-white/10 text-orange-600 focus:ring-orange-500">
                        <div class="flex gap-2">
                            @if($post->is_featured)
                                <span class="px-2 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full">⭐ Öne Çıkan</span>
                            @endif
                            @if($post->status === 'open')
                                <span class="px-2 py-1 bg-green-400 text-green-900 text-xs font-bold rounded-full">✅ Açık</span>
                            @else
                                <span class="px-2 py-1 bg-gray-400 text-gray-900 text-xs font-bold rounded-full">🔒 Kapalı</span>
                            @endif
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-1">{{ Str::limit($post->title, 50) }}</h3>
                    <p class="text-orange-100 text-sm">{{ $post->game->name }}</p>
                </div>

                <!-- Body -->
                <div class="p-4 space-y-3">
                    <!-- User Info -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($post->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-white font-semibold">{{ $post->user->name }}</p>
                            <p class="text-gray-400 text-sm">{{ $post->user->profile->nickname ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        @if($post->city)
                            <div class="bg-gray-700/50 rounded-lg p-2">
                                <p class="text-gray-400 text-xs">Şehir</p>
                                <p class="text-white font-semibold">{{ $post->city }}</p>
                            </div>
                        @endif
                        @if($post->play_style_tag)
                            <div class="bg-gray-700/50 rounded-lg p-2">
                                <p class="text-gray-400 text-xs">Oyun Stili</p>
                                <p class="text-white font-semibold">{{ $post->play_style_tag }}</p>
                            </div>
                        @endif
                        @if($post->min_rank)
                            <div class="bg-gray-700/50 rounded-lg p-2">
                                <p class="text-gray-400 text-xs">Min Rank</p>
                                <p class="text-white font-semibold">{{ $post->min_rank }}</p>
                            </div>
                        @endif
                        @if($post->microphone_required)
                            <div class="bg-gray-700/50 rounded-lg p-2">
                                <p class="text-gray-400 text-xs">Mikrofon</p>
                                <p class="text-green-400 font-semibold">✓ Gerekli</p>
                            </div>
                        @endif
                    </div>

                    <!-- Date -->
                    <div class="text-xs text-gray-400 pt-2 border-t border-gray-700">
                        📅 {{ $post->created_at->format('d.m.Y H:i') }}
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.lfg-posts.show', $post->id) }}" 
                            class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Detay
                        </a>
                        <a href="{{ route('admin.lfg-posts.edit', $post->id) }}" 
                            class="flex-1 text-center px-3 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm">
                            Düzenle
                        </a>
                        <form action="{{ route('admin.lfg-posts.toggle-featured', $post->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                                {{ $post->is_featured ? '★' : '☆' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-400 text-lg">İlan bulunamadı</p>
            </div>
            @endforelse
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <input type="checkbox" @change="selected = $event.target.checked ? [...document.querySelectorAll('.post-checkbox')].map(cb => parseInt(cb.value)) : []"
                                class="rounded border-gray-600 bg-gray-700 text-orange-600 focus:ring-orange-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">İlan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Kullanıcı</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Oyun</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Şehir</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-300 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($posts as $post)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $post->id }}" class="post-checkbox rounded border-gray-600 bg-gray-700 text-orange-600 focus:ring-orange-500"
                                @change="$event.target.checked ? selected.push({{ $post->id }}) : selected = selected.filter(id => id !== {{ $post->id }})">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @if($post->is_featured)
                                    <span class="text-yellow-400 text-lg">⭐</span>
                                @endif
                                <div>
                                    <div class="text-sm font-semibold text-white">{{ Str::limit($post->title, 40) }}</div>
                                    @if($post->play_style_tag)
                                        <span class="text-xs px-2 py-1 bg-blue-600 text-blue-100 rounded-full">
                                            {{ $post->play_style_tag }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-white">{{ $post->user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $post->user->profile->nickname ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $post->game->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $post->city ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($post->status === 'open')
                                <span class="px-3 py-1 bg-green-600 text-white rounded-full text-xs font-semibold">✅ Açık</span>
                            @else
                                <span class="px-3 py-1 bg-gray-600 text-gray-300 rounded-full text-xs font-semibold">🔒 Kapalı</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $post->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.lfg-posts.show', $post->id) }}" 
                                    class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    Detay
                                </a>
                                <a href="{{ route('admin.lfg-posts.edit', $post->id) }}" 
                                    class="px-3 py-1 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm">
                                    Düzenle
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            İlan bulunamadı
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
        <div class="mt-6 bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl px-6 py-4 border border-gray-700">
            {{ $posts->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
