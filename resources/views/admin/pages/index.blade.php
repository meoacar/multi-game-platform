@extends('admin.layout')

@section('title', 'Sayfa Yönetimi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">
                        📄 Sayfa Yönetimi
                    </h1>
                    <p class="text-gray-400">Statik sayfaları yönetin (Hakkımızda, Gizlilik Politikası, vb.)</p>
                </div>
                <a href="{{ route('admin.pages.create') }}" 
                    class="px-5 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Yeni Sayfa
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-xl shadow-blue-500/20 p-6 text-white border border-blue-500/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">📄</div>
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold mb-1">{{ $pages->total() }}</p>
                <p class="text-blue-200 text-sm">Toplam Sayfa</p>
            </div>

            <div class="bg-gradient-to-br from-green-600 to-emerald-700 rounded-2xl shadow-xl shadow-green-500/20 p-6 text-white border border-green-500/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">✅</div>
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold mb-1">{{ $pages->where('is_published', true)->count() }}</p>
                <p class="text-green-200 text-sm">Yayında</p>
            </div>

            <div class="bg-gradient-to-br from-gray-600 to-gray-700 rounded-2xl shadow-xl shadow-gray-500/20 p-6 text-white border border-gray-500/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">📝</div>
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold mb-1">{{ $pages->where('is_published', false)->count() }}</p>
                <p class="text-gray-200 text-sm">Taslak</p>
            </div>
        </div>


        <!-- Filtreler -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-xl p-6 mb-6 border border-gray-700/50">
            <form method="GET" action="{{ route('admin.pages.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="🔍 Başlık, slug veya içerik ara..." 
                           class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                </div>
                
                <select name="is_published" class="px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    <option value="">Tüm Durumlar</option>
                    <option value="published" {{ request('is_published') === 'published' ? 'selected' : '' }}>Yayında</option>
                    <option value="draft" {{ request('is_published') === 'draft' ? 'selected' : '' }}>Taslak</option>
                </select>

                <select name="sort_by" class="px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Oluşturma Tarihi</option>
                    <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Güncelleme Tarihi</option>
                    <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Başlık</option>
                </select>

                <select name="sort_order" class="px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Azalan</option>
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Artan</option>
                </select>

                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all">
                    Filtrele
                </button>
                
                @if(request()->hasAny(['search', 'is_published', 'sort_by', 'sort_order']))
                    <a href="{{ route('admin.pages.index') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-bold transition-all">
                        Temizle
                    </a>
                @endif
            </form>
        </div>

        <!-- Toplu İşlemler -->
        <form id="bulkActionForm" method="POST" class="mb-6">
            @csrf
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-xl p-4 border border-gray-700/50 flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="selectAll" class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="selectAll" class="text-sm font-medium text-gray-300">Tümünü Seç</label>
                </div>
                
                <select id="bulkAction" class="px-4 py-2 bg-gray-900 border-2 border-gray-700 rounded-xl text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    <option value="">Toplu İşlem Seç</option>
                    <option value="publish">Yayınla</option>
                    <option value="unpublish">Yayından Kaldır</option>
                    <option value="delete">Sil</option>
                </select>
                
                <button type="button" onclick="executeBulkAction()" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-bold transition-all">
                    Uygula
                </button>
                
                <span id="selectedCount" class="text-sm text-gray-400 ml-auto">0 sayfa seçildi</span>
            </div>
        </form>

        <!-- Sayfa Listesi -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden border border-gray-700/50">
            @if($pages->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    <input type="checkbox" class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-2 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Başlık</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Slug</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Oluşturma</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Güncelleme</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @foreach($pages as $page)
                            <tr class="hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="page_ids[]" value="{{ $page->id }}" class="page-checkbox w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-white">{{ $page->title }}</div>
                                    <div class="text-xs text-gray-400 mt-1">{{ Str::limit(strip_tags($page->content), 60) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs bg-gray-900 text-blue-400 px-3 py-1.5 rounded-lg border border-gray-700">{{ $page->slug }}</code>
                                        <a href="{{ route('pages.show', $page->slug) }}" target="_blank" 
                                            class="w-8 h-8 bg-blue-600/20 hover:bg-blue-600/30 rounded-lg flex items-center justify-center transition-colors border border-blue-500/30">
                                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($page->is_published)
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg bg-green-500/20 text-green-400 border border-green-500/30">
                                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                            Yayında
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                            Taslak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-300">{{ $page->created_at->format('d.m.Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $page->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-300">{{ $page->updated_at->format('d.m.Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $page->updated_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.pages.toggle-publish', $page) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-2 bg-{{ $page->is_published ? 'yellow' : 'green' }}-600/20 hover:bg-{{ $page->is_published ? 'yellow' : 'green' }}-600/30 text-{{ $page->is_published ? 'yellow' : 'green' }}-400 rounded-lg font-semibold transition-colors border border-{{ $page->is_published ? 'yellow' : 'green' }}-500/30 text-xs">
                                                {{ $page->is_published ? '📤 Kaldır' : '📢 Yayınla' }}
                                            </button>
                                        </form>
                                        
                                        <a href="{{ route('admin.pages.edit', $page) }}" 
                                            class="px-3 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 rounded-lg font-semibold transition-colors border border-blue-500/30 text-xs">
                                            ✏️ Düzenle
                                        </a>
                                        
                                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Bu sayfayı silmek istediğinizden emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-lg font-semibold transition-colors border border-red-500/30 text-xs">
                                                🗑️ Sil
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-700/50 bg-gray-900/30">
                    {{ $pages->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-3xl mb-6 border-2 border-blue-500/30">
                        <div class="text-6xl">📄</div>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Sayfa bulunamadı</h3>
                    <p class="text-gray-400 mb-6">İlk sayfanızı oluşturarak başlayın.</p>
                    <a href="{{ route('admin.pages.create') }}" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all">
                        Yeni Sayfa Oluştur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Tümünü seç/kaldır
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.page-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

// Seçili sayfa sayısını güncelle
document.querySelectorAll('.page-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const count = document.querySelectorAll('.page-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count + ' sayfa seçildi';
}

// Toplu işlem uygula
function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const selectedIds = Array.from(document.querySelectorAll('.page-checkbox:checked')).map(cb => cb.value);
    
    if (!action) {
        alert('Lütfen bir işlem seçin.');
        return;
    }
    
    if (selectedIds.length === 0) {
        alert('Lütfen en az bir sayfa seçin.');
        return;
    }
    
    const form = document.getElementById('bulkActionForm');
    
    // Action'a göre route belirle
    let route = '';
    let confirmMessage = '';
    
    switch(action) {
        case 'publish':
            route = '{{ route("admin.pages.bulk-publish") }}';
            confirmMessage = selectedIds.length + ' sayfayı yayınlamak istediğinizden emin misiniz?';
            break;
        case 'unpublish':
            route = '{{ route("admin.pages.bulk-unpublish") }}';
            confirmMessage = selectedIds.length + ' sayfayı yayından kaldırmak istediğinizden emin misiniz?';
            break;
        case 'delete':
            route = '{{ route("admin.pages.bulk-delete") }}';
            confirmMessage = selectedIds.length + ' sayfayı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!';
            break;
    }
    
    if (confirm(confirmMessage)) {
        form.action = route;
        form.submit();
    }
}
</script>
@endsection
