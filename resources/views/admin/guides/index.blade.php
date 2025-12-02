@extends('admin.layout')

@section('title', 'Rehber Yönetimi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="guidesManager()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                    📚 Rehber Yönetimi
                </h1>
                <p class="text-gray-600 mt-2">Kullanıcıların oluşturduğu rehberleri yönet, yayınla ve düzenle</p>
            </div>
            <a href="{{ route('guide.index') }}" 
                class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-200 font-semibold">
                🌐 Siteyi Görüntüle
            </a>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-green-100 text-sm mb-1">Toplam Rehber</p>
            <p class="text-3xl font-bold">{{ number_format($stats['total']) }}</p>
            <p class="text-green-100 text-xs mt-1">Tüm zamanlar</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-blue-100 text-sm mb-1">Yayında</p>
            <p class="text-3xl font-bold">{{ number_format($stats['published']) }}</p>
            <p class="text-blue-100 text-xs mt-1">Published</p>
        </div>

        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-gray-100 text-sm mb-1">Taslak</p>
            <p class="text-3xl font-bold">{{ number_format($stats['draft']) }}</p>
            <p class="text-gray-100 text-xs mt-1">Draft</p>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-yellow-100 text-sm mb-1">Öne Çıkan</p>
            <p class="text-3xl font-bold">{{ number_format($stats['featured']) }}</p>
            <p class="text-yellow-100 text-xs mt-1">Featured</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-purple-100 text-sm mb-1">Bugün</p>
            <p class="text-3xl font-bold">{{ number_format($stats['today']) }}</p>
            <p class="text-purple-100 text-xs mt-1">Yeni rehber</p>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-600 text-sm mb-1">Bu Hafta</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['this_week']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-600 text-sm mb-1">Bu Ay</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['this_month']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-600 text-sm mb-1">Toplam Görüntülenme</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_views']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-600 text-sm mb-1">Toplam Beğeni</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_likes']) }}</p>
        </div>
    </div>

    <!-- Filtreler ve Arama -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.guides.index') }}" class="space-y-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">🔍 Filtreler</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Arama</label>
                    <input type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Başlık veya içerik ara..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Oyun</label>
                    <select name="game_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Tüm Oyunlar</option>
                        @foreach($games as $game)
                            <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>
                                {{ $game->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Yayın Durumu</label>
                    <select name="is_published" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Tümü</option>
                        <option value="yes" {{ request('is_published') === 'yes' ? 'selected' : '' }}>Yayında</option>
                        <option value="no" {{ request('is_published') === 'no' ? 'selected' : '' }}>Taslak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Öne Çıkan</label>
                    <select name="is_featured" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Tümü</option>
                        <option value="yes" {{ request('is_featured') === 'yes' ? 'selected' : '' }}>Öne Çıkan</option>
                        <option value="no" {{ request('is_featured') === 'no' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Başlangıç Tarihi</label>
                    <input type="date" 
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bitiş Tarihi</label>
                    <input type="date" 
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sıralama</label>
                    <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Tarih</option>
                        <option value="views_count" {{ request('sort_by') === 'views_count' ? 'selected' : '' }}>Görüntülenme</option>
                        <option value="likes_count" {{ request('sort_by') === 'likes_count' ? 'selected' : '' }}>Beğeni</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filtrele
                </button>
                <a href="{{ route('admin.guides.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-redo mr-2"></i>Sıfırla
                </a>
            </div>
        </form>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">🔢 Sıralama</label>
                <select x-model="sortBy" 
                    @change="filterGuides"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="newest">En Yeni</option>
                    <option value="oldest">En Eski</option>
                    <option value="views">En Çok Görüntülenen</option>
                    <option value="likes">En Çok Beğenilen</option>
                </select>
            </div>
        </div>

        <!-- Toplu İşlemler -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <input type="checkbox" 
                        x-model="selectAll"
                        @change="toggleSelectAll"
                        class="w-5 h-5 text-green-600 rounded focus:ring-green-500">
                    <span class="text-sm font-semibold text-gray-700">
                        <span x-text="selectedGuides.length"></span> rehber seçildi
                    </span>
                </div>
                
                <div class="flex gap-2" x-show="selectedGuides.length > 0">
                    <button @click="bulkPublish" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-semibold">
                        ✅ Yayınla
                    </button>
                    <button @click="bulkUnpublish" 
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm font-semibold">
                        ⏸️ Yayından Kaldır
                    </button>
                    <button @click="bulkDelete" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-semibold">
                        🗑️ Sil
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rehberler Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($guides as $guide)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <!-- Checkbox -->
            <div class="absolute top-4 left-4 z-10">
                <input type="checkbox" 
                    value="{{ $guide->id }}"
                    x-model="selectedGuides"
                    class="w-5 h-5 text-green-600 rounded focus:ring-green-500 bg-white/90 backdrop-blur">
            </div>

            <!-- Thumbnail -->
            <div class="relative h-48 bg-gradient-to-br from-green-400 via-emerald-500 to-teal-600 overflow-hidden">
                @if($guide->thumbnail)
                    <img src="{{ $guide->thumbnail }}" alt="{{ $guide->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white text-6xl">
                        📚
                    </div>
                @endif
                
                <!-- Durum Badge -->
                <div class="absolute top-4 right-4">
                    @if($guide->is_published)
                        <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-lg">
                            ✅ Yayında
                        </span>
                    @else
                        <span class="px-3 py-1 bg-gray-500 text-white text-xs font-bold rounded-full shadow-lg">
                            📝 Taslak
                        </span>
                    @endif
                </div>

                <!-- Öne Çıkan Badge -->
                @if($guide->is_featured)
                    <div class="absolute bottom-4 left-4">
                        <span class="px-3 py-1 bg-purple-500 text-white text-xs font-bold rounded-full shadow-lg">
                            ⭐ Öne Çıkan
                        </span>
                    </div>
                @endif
            </div>

            <!-- İçerik -->
            <div class="p-6">
                <!-- Başlık -->
                <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 hover:text-green-600 transition-colors">
                    {{ $guide->title }}
                </h3>

                <!-- Oyun -->
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 text-xs font-semibold rounded-full">
                        {{ $guide->game->name ?? 'PUBG Mobile' }}
                    </span>
                </div>

                <!-- Yazar -->
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ substr($guide->user->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $guide->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $guide->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <!-- İstatistikler -->
                <div class="grid grid-cols-3 gap-2 mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900">{{ number_format($guide->views_count) }}</p>
                        <p class="text-xs text-gray-500">👁️ Görüntülenme</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900">{{ number_format($guide->likes_count) }}</p>
                        <p class="text-xs text-gray-500">❤️ Beğeni</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900">{{ $guide->comments_count ?? 0 }}</p>
                        <p class="text-xs text-gray-500">💬 Yorum</p>
                    </div>
                </div>

                <!-- Aksiyonlar -->
                <div class="flex gap-2">
                    <a href="{{ route('admin.guides.show', $guide->id) }}" 
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:shadow-lg transition-all text-center text-sm font-semibold">
                        👁️ Görüntüle
                    </a>
                    <a href="{{ route('admin.guides.edit', $guide->id) }}" 
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all text-center text-sm font-semibold">
                        ✏️ Düzenle
                    </a>
                </div>

                <!-- Hızlı Aksiyonlar -->
                <div class="flex gap-2 mt-2">
                    @if(!$guide->is_published)
                        <form action="{{ route('admin.guides.toggle-published', $guide->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                class="w-full px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-xs font-semibold">
                                ✅ Yayınla
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.guides.toggle-published', $guide->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                class="w-full px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors text-xs font-semibold">
                                ⏸️ Yayından Kaldır
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.guides.toggle-featured', $guide->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" 
                            class="w-full px-3 py-2 {{ $guide->is_featured ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }} rounded-lg hover:bg-purple-200 transition-colors text-xs font-semibold">
                            {{ $guide->is_featured ? '⭐ Öne Çıkan' : '☆ Öne Çıkar' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.guides.destroy', $guide->id) }}" method="POST" 
                        onsubmit="return confirm('Bu rehberi silmek istediğinize emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-xs font-semibold">
                            🗑️
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $guides->links() }}
    </div>
</div>

<script>
function guidesManager() {
    return {
        search: '',
        statusFilter: '',
        gameFilter: '',
        sortBy: 'newest',
        selectedGuides: [],
        selectAll: false,

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedGuides = Array.from(document.querySelectorAll('input[type="checkbox"][value]')).map(cb => cb.value);
            } else {
                this.selectedGuides = [];
            }
        },

        filterGuides() {
            // Filtreleme işlemi - sayfa yenileme ile yapılacak
            const params = new URLSearchParams();
            if (this.search) params.append('search', this.search);
            if (this.statusFilter) params.append('status', this.statusFilter);
            if (this.gameFilter) params.append('game', this.gameFilter);
            if (this.sortBy) params.append('sort', this.sortBy);
            
            window.location.href = '{{ route("admin.guides.index") }}?' + params.toString();
        },

        bulkPublish() {
            if (!confirm(`${this.selectedGuides.length} rehberi yayınlamak istediğinize emin misiniz?`)) return;
            
            fetch('{{ route("admin.guides.bulk-publish") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: this.selectedGuides })
            }).then(() => window.location.reload());
        },

        bulkUnpublish() {
            if (!confirm(`${this.selectedGuides.length} rehberi yayından kaldırmak istediğinize emin misiniz?`)) return;
            
            fetch('{{ route("admin.guides.bulk-unpublish") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: this.selectedGuides })
            }).then(() => window.location.reload());
        },

        bulkDelete() {
            if (!confirm(`${this.selectedGuides.length} rehberi SİLMEK istediğinize emin misiniz? Bu işlem geri alınamaz!`)) return;
            
            fetch('{{ route("admin.guides.bulk-delete") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: this.selectedGuides })
            }).then(() => window.location.reload());
        }
    }
}
</script>
@endsection
