@extends('admin.layout')

@section('title', 'Widget Yönetimi')

@section('content')
<div class="container mx-auto px-4" x-data="widgetManager()">
    <!-- Başlık ve Butonlar -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Widget Yönetimi</h1>
            <p class="text-gray-600 mt-1">Sidebar ve footer widget'larını yönetin</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.widgets.manage') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                </svg>
                Konum Bazlı Yönet
            </a>
            <a href="{{ route('admin.widgets.create') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Yeni Widget
            </a>
        </div>
    </div>

    <!-- Başarı Mesajı -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-lg" x-data="{ show: true }" x-show="show">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Filtreler -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.widgets.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Arama</label>
                <input type="text" name="search" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       value="{{ request('search') }}" placeholder="Widget ara...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Konum</label>
                <select name="location" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    @foreach($locations as $key => $label)
                        <option value="{{ $key }}" {{ request('location') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tür</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                <select name="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tümü</option>
                    <option value="active" {{ request('is_active') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('is_active') == 'inactive' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Filtrele
                </button>
                <a href="{{ route('admin.widgets.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Temizle
                </a>
            </div>
        </form>
    </div>

    <!-- Widget Listesi -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-900">Widget Listesi ({{ $widgets->total() }})</h2>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="bulkAction('activate')" 
                        class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Toplu Aktif Et
                </button>
                <button type="button" @click="bulkAction('deactivate')" 
                        class="px-3 py-1.5 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Toplu Pasif Et
                </button>
                <button type="button" @click="bulkAction('delete')" 
                        class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Toplu Sil
                </button>
            </div>
        </div>
        <div class="p-6">
            @if($widgets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" @change="toggleAll($event)" 
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sıra</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oluşturma</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($widgets as $widget)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" class="widget-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                                               value="{{ $widget->id }}" x-model="selectedWidgets">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $widget->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $widget->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $types[$widget->type] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $locations[$widget->location] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $widget->order }}</td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('admin.widgets.toggle', $widget) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1 text-xs font-semibold rounded-full {{ $widget->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} hover:opacity-80 transition">
                                                {{ $widget->is_active ? '✓ Aktif' : '✗ Pasif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $widget->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.widgets.preview', $widget) }}" target="_blank"
                                               class="text-blue-600 hover:text-blue-800" title="Önizle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.widgets.edit', $widget) }}" 
                                               class="text-yellow-600 hover:text-yellow-800" title="Düzenle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <button @click="deleteWidget({{ $widget->id }})" 
                                                    class="text-red-600 hover:text-red-800" title="Sil">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $widgets->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz widget bulunmuyor</h3>
                    <p class="text-gray-600 mb-6">İlk widget'ınızı oluşturarak başlayın</p>
                    <a href="{{ route('admin.widgets.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        İlk Widget'ı Oluştur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Silme Formu -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Toplu İşlem Formu -->
<form id="bulk-form" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="widget_ids" id="bulk-widget-ids">
</form>

@push('scripts')
<script>
function widgetManager() {
    return {
        selectedWidgets: [],
        
        toggleAll(event) {
            const checkboxes = document.querySelectorAll('.widget-checkbox');
            checkboxes.forEach(cb => cb.checked = event.target.checked);
            
            if (event.target.checked) {
                this.selectedWidgets = Array.from(checkboxes).map(cb => cb.value);
            } else {
                this.selectedWidgets = [];
            }
        },
        
        deleteWidget(id) {
            if (confirm('Bu widget\'ı silmek istediğinizden emin misiniz?')) {
                const form = document.getElementById('delete-form');
                form.action = `/admin/widgets/${id}`;
                form.submit();
            }
        },
        
        bulkAction(action) {
            if (this.selectedWidgets.length === 0) {
                alert('Lütfen en az bir widget seçin.');
                return;
            }

            let confirmMsg = '';
            let actionUrl = '';

            switch(action) {
                case 'delete':
                    confirmMsg = `${this.selectedWidgets.length} widget'ı silmek istediğinizden emin misiniz?`;
                    actionUrl = '{{ route("admin.widgets.bulk-delete") }}';
                    break;
                case 'activate':
                    confirmMsg = `${this.selectedWidgets.length} widget'ı aktif etmek istediğinizden emin misiniz?`;
                    actionUrl = '{{ route("admin.widgets.bulk-activate") }}';
                    break;
                case 'deactivate':
                    confirmMsg = `${this.selectedWidgets.length} widget'ı pasif etmek istediğinizden emin misiniz?`;
                    actionUrl = '{{ route("admin.widgets.bulk-deactivate") }}';
                    break;
            }

            if (confirm(confirmMsg)) {
                const form = document.getElementById('bulk-form');
                document.getElementById('bulk-widget-ids').value = JSON.stringify(this.selectedWidgets);
                form.action = actionUrl;
                form.submit();
            }
        }
    }
}
</script>
@endpush
@endsection
