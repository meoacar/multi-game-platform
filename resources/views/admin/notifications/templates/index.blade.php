@extends('admin.layout')

@section('title', 'Email Şablonları')

@section('content')
<div x-data="templatesManager()">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📧 Email Şablonları</h1>
                <p class="text-gray-600 mt-1">Email şablonlarını yönetin ve düzenleyin</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    ← Bildirimler
                </a>
                <a href="{{ route('admin.notifications.templates.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                    <span>➕</span>
                    <span>Yeni Şablon</span>
                </a>
            </div>
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Toplam Şablon</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $templates->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">📧</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Aktif Şablon</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $templates->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">✅</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pasif Şablon</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1">{{ $templates->where('is_active', false)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">⏸️</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Son Güncelleme</p>
                    <p class="text-sm font-medium text-gray-900 mt-1">
                        {{ $templates->first()?->updated_at?->diffForHumans() ?? 'Yok' }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">🕐</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtreler ve Arama -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Arama -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">🔍 Arama</label>
                <input 
                    type="text" 
                    x-model="filters.search"
                    @input.debounce.500ms="applyFilters()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Şablon adı veya slug ara...">
            </div>

            <!-- Durum Filtresi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                <select 
                    x-model="filters.status"
                    @change="applyFilters()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tümü</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Pasif</option>
                </select>
            </div>

            <!-- Sıralama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sıralama</label>
                <select 
                    x-model="filters.sort"
                    @change="applyFilters()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="name_asc">İsim (A-Z)</option>
                    <option value="name_desc">İsim (Z-A)</option>
                    <option value="created_desc">En Yeni</option>
                    <option value="created_asc">En Eski</option>
                    <option value="updated_desc">Son Güncellenen</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Şablon Listesi -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Şablon
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Slug
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Değişkenler
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Durum
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tarih
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($templates as $template)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-medium text-gray-900">{{ $template->name }}</div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ $template->subject }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $template->slug }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @if($template->variables && count($template->variables) > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($template->variables, 0, 3) as $variable)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $variable }}
                                        </span>
                                    @endforeach
                                    @if(count($template->variables) > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            +{{ count($template->variables) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-gray-400">Yok</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($template->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    ⏸️ Pasif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $template->created_at->format('d.m.Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $template->updated_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <!-- Önizleme -->
                                <a href="{{ route('admin.notifications.templates.preview', $template->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition"
                                   title="Önizle">
                                    👁️
                                </a>

                                <!-- Düzenle -->
                                <a href="{{ route('admin.notifications.templates.edit', $template->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 transition"
                                   title="Düzenle">
                                    ✏️
                                </a>

                                <!-- Kopyala -->
                                <form action="{{ route('admin.notifications.templates.duplicate', $template->id) }}" 
                                      method="POST" 
                                      class="inline"
                                      @submit.prevent="if(confirm('Bu şablonu kopyalamak istediğinize emin misiniz?')) $el.submit()">
                                    @csrf
                                    <button type="submit" 
                                            class="text-purple-600 hover:text-purple-900 transition"
                                            title="Kopyala">
                                        📋
                                    </button>
                                </form>

                                <!-- Sil -->
                                <form action="{{ route('admin.notifications.templates.destroy', $template->id) }}" 
                                      method="POST" 
                                      class="inline"
                                      @submit.prevent="if(confirm('Bu şablonu silmek istediğinize emin misiniz?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 transition"
                                            title="Sil">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <div class="text-4xl mb-2">📧</div>
                                <p class="text-lg font-medium">Henüz email şablonu yok</p>
                                <p class="text-sm mt-1">İlk şablonunuzu oluşturmak için yukarıdaki butona tıklayın</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($templates->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $templates->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function templatesManager() {
    return {
        filters: {
            search: '',
            status: '',
            sort: 'name_asc'
        },

        applyFilters() {
            const params = new URLSearchParams();
            
            if (this.filters.search) params.append('search', this.filters.search);
            if (this.filters.status) params.append('status', this.filters.status);
            if (this.filters.sort) params.append('sort', this.filters.sort);
            
            window.location.href = '{{ route("admin.notifications.templates") }}?' + params.toString();
        }
    }
}
</script>
@endpush
@endsection
