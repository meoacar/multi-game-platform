@extends('admin.layout')

@section('title', 'Widget Düzenle')

@section('content')
<div class="container mx-auto px-4" x-data="widgetForm()">
    <!-- Başlık -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Widget Düzenle</h1>
            <p class="text-gray-600 mt-1">{{ $widget->title }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.widgets.preview', $widget) }}" target="_blank"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Önizle
            </a>
            <a href="{{ route('admin.widgets.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Geri Dön
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

    <form action="{{ route('admin.widgets.update', $widget) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sol Kolon (2/3) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Temel Bilgiler -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Temel Bilgiler</h2>
                    
                    <!-- Başlık -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Başlık <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror" 
                               id="title" name="title" value="{{ old('title', $widget->title) }}" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="mb-6">
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror" 
                               id="slug" name="slug" value="{{ old('slug', $widget->slug) }}" required>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tür -->
                    <div class="mb-6">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Widget Türü <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror" 
                                id="type" name="type" x-model="widgetType" required>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ old('type', $widget->type) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- İçerik -->
                <div class="bg-white rounded-lg shadow-sm p-6" x-show="showContentField">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">İçerik</h2>
                    
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">HTML İçerik</label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm @error('content') border-red-500 @enderror" 
                                  id="content" name="content" rows="12">{{ old('content', $widget->content) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">HTML kod veya metin girebilirsiniz</p>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Widget Ayarları -->
                <div class="bg-white rounded-lg shadow-sm p-6" x-show="showSettingsField">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Widget Ayarları</h2>
                    
                    <!-- Son İçerikler için -->
                    <div x-show="widgetType === 'recent_content'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">İçerik Sayısı</label>
                            <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   name="settings[limit]" value="{{ old('settings.limit', $widget->settings['limit'] ?? 5) }}" min="1" max="20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">İçerik Türü</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                    name="settings[content_type]">
                                <option value="all" {{ old('settings.content_type', $widget->settings['content_type'] ?? 'all') == 'all' ? 'selected' : '' }}>Tümü</option>
                                <option value="guides" {{ old('settings.content_type', $widget->settings['content_type'] ?? '') == 'guides' ? 'selected' : '' }}>Rehberler</option>
                                <option value="community" {{ old('settings.content_type', $widget->settings['content_type'] ?? '') == 'community' ? 'selected' : '' }}>Topluluk Gönderileri</option>
                            </select>
                        </div>
                    </div>

                    <!-- Popüler İçerikler için -->
                    <div x-show="widgetType === 'popular'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">İçerik Sayısı</label>
                            <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   name="settings[limit]" value="{{ old('settings.limit', $widget->settings['limit'] ?? 5) }}" min="1" max="20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">İçerik Türü</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                    name="settings[content_type]">
                                <option value="all" {{ old('settings.content_type', $widget->settings['content_type'] ?? 'all') == 'all' ? 'selected' : '' }}>Tümü</option>
                                <option value="guides" {{ old('settings.content_type', $widget->settings['content_type'] ?? '') == 'guides' ? 'selected' : '' }}>Rehberler</option>
                            </select>
                        </div>
                    </div>

                    <!-- Özel Widget için -->
                    <div x-show="widgetType === 'custom'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">View Adı</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   name="settings[view]" value="{{ old('settings.view', $widget->settings['view'] ?? '') }}" placeholder="widgets.custom.my-widget">
                            <p class="mt-1 text-sm text-gray-500">Örnek: widgets.custom.my-widget</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ Kolon (1/3) -->
            <div class="space-y-6">
                <!-- Yayınlama -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Yayınlama</h2>
                    
                    <!-- Aktif/Pasif -->
                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $widget->is_active) ? 'checked' : '' }}
                                   class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-3 text-sm font-medium text-gray-700">Widget Aktif</span>
                        </label>
                    </div>

                    <!-- Konum -->
                    <div class="mb-6">
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            Konum <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror" 
                                id="location" name="location" required>
                            @foreach($locations as $key => $label)
                                <option value="{{ $key }}" {{ old('location', $widget->location) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sıra -->
                    <div class="mb-6">
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Sıra</label>
                        <input type="number" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('order') border-red-500 @enderror" 
                               id="order" name="order" value="{{ old('order', $widget->order) }}" min="0">
                        <p class="mt-1 text-sm text-gray-500">Küçük sayı önce gösterilir</p>
                        @error('order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kaydet Butonu -->
                    <button type="submit" 
                            class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 font-medium mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Değişiklikleri Kaydet
                    </button>

                    <!-- Sil Butonu -->
                    <button type="button" @click="deleteWidget()" 
                            class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center gap-2 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Widget'ı Sil
                    </button>
                </div>

                <!-- Bilgi -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-900">Widget Bilgileri</h3>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-600">Widget ID:</span>
                            <span class="font-semibold text-gray-900 ml-2">#{{ $widget->id }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Oluşturma:</span>
                            <span class="font-semibold text-gray-900 ml-2">{{ $widget->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Son Güncelleme:</span>
                            <span class="font-semibold text-gray-900 ml-2">{{ $widget->updated_at->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Silme Formu -->
<form id="delete-form" action="{{ route('admin.widgets.destroy', $widget) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function widgetForm() {
    return {
        widgetType: '{{ old('type', $widget->type) }}',
        
        get showContentField() {
            return ['html', 'ad', 'custom'].includes(this.widgetType);
        },
        
        get showSettingsField() {
            return ['recent_content', 'popular', 'custom'].includes(this.widgetType);
        },
        
        deleteWidget() {
            if (confirm('Bu widget\'ı silmek istediğinizden emin misiniz?')) {
                document.getElementById('delete-form').submit();
            }
        }
    }
}
</script>
@endpush
@endsection
