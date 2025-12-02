@extends('admin.layout')

@section('title', 'Widget Yerleştirme')

@section('content')
<div class="container mx-auto px-4" x-data="widgetManager()">
    <!-- Başlık -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Widget Yerleştirme</h1>
            <p class="text-gray-600 mt-1">Sürükle-bırak ile widget'ları yönetin</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.widgets.create') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Yeni Widget
            </a>
            <a href="{{ route('admin.widgets.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                Liste Görünümü
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

    <!-- Konum Seçimi -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Konum Seç</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($locations as $key => $label)
                <a href="{{ route('admin.widgets.manage', ['location' => $key]) }}" 
                   class="px-4 py-2 rounded-lg font-medium transition {{ $location == $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Widget Listesi (2/3) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $locations[$location] }}</h2>
                        <p class="text-sm text-gray-600">{{ $widgets->count() }} widget</p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">
                        {{ $widgets->count() }} Widget
                    </span>
                </div>
                
                <div class="p-6">
                    @if($widgets->count() > 0)
                        <div id="widget-list" class="space-y-3">
                            @foreach($widgets as $widget)
                                <div class="widget-item bg-gray-50 rounded-lg p-4 border-2 border-gray-200 hover:border-blue-400 transition cursor-move" 
                                     data-id="{{ $widget->id }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4 flex-1">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h4m0 0V4m0 4l-4-4m16 0h-4m0 0V4m0 4l4-4M4 16h4m0 0v4m0-4l-4 4m16-4h-4m0 0v4m0-4l4 4"></path>
                                            </svg>
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-900">{{ $widget->title }}</h3>
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                                        {{ $types[$widget->type] }}
                                                    </span>
                                                    <span class="px-2 py-1 {{ $widget->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} text-xs font-semibold rounded-full">
                                                        {{ $widget->is_active ? '✓ Aktif' : '✗ Pasif' }}
                                                    </span>
                                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">
                                                        Sıra: {{ $widget->order }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.widgets.preview', $widget) }}" target="_blank"
                                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Önizle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.widgets.edit', $widget) }}" 
                                               class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Düzenle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-blue-800">Widget'ları sürükleyerek sıralamayı değiştirebilirsiniz. Değişiklikler otomatik kaydedilir.</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16">
                            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Bu konumda widget yok</h3>
                            <p class="text-gray-600 mb-6">İlk widget'ınızı ekleyerek başlayın</p>
                            <a href="{{ route('admin.widgets.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Widget Ekle
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sağ Kolon (1/3) -->
        <div class="space-y-6">
            <!-- Önizleme -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Canlı Önizleme</h2>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-lg p-4 border-2 border-dashed border-gray-300">
                        <p class="text-xs text-gray-600 mb-3 font-semibold">{{ $locations[$location] }}</p>
                        <div class="space-y-2">
                            @if($widgets->count() > 0)
                                @foreach($widgets as $widget)
                                    <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $widget->title }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">{{ $types[$widget->type] }}</p>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">Widget yok</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yardım -->
            <div class="bg-gradient-to-br from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-900">Widget Türleri</h3>
                </div>
                <ul class="space-y-2 text-xs text-gray-700">
                    <li class="flex items-start gap-2">
                        <span class="text-purple-600">•</span>
                        <div><strong>HTML İçerik:</strong> Özel HTML kodu</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-purple-600">•</span>
                        <div><strong>Son İçerikler:</strong> En son eklenen içerikler</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-purple-600">•</span>
                        <div><strong>Popüler:</strong> En çok görüntülenen</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-purple-600">•</span>
                        <div><strong>Reklam:</strong> Reklam banner'ı</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-purple-600">•</span>
                        <div><strong>Özel:</strong> Özel view dosyası</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
function widgetManager() {
    return {
        init() {
            this.initSortable();
        },
        
        initSortable() {
            const widgetList = document.getElementById('widget-list');
            if (!widgetList) return;
            
            new Sortable(widgetList, {
                animation: 200,
                ghostClass: 'opacity-50',
                dragClass: 'shadow-2xl',
                onEnd: () => this.saveOrder()
            });
        },
        
        saveOrder() {
            const items = document.querySelectorAll('#widget-list .widget-item');
            const widgets = [];
            
            items.forEach((item, index) => {
                widgets.push({
                    id: parseInt(item.dataset.id),
                    location: '{{ $location }}',
                    order: index
                });
            });

            fetch('{{ route("admin.widgets.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ widgets: widgets })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showToast('success', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showToast('error', 'Sıralama kaydedilemedi!');
            });
        },
        
        showToast(type, message) {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-up`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.remove(), 3000);
        }
    }
}
</script>
@endpush
@endsection
