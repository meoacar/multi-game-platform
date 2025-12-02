@extends('admin.layout')

@section('title', 'Yeni Widget Oluştur')

@section('content')
<div class="container mx-auto px-4" x-data="widgetForm()">
    <!-- Başlık -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Yeni Widget Oluştur</h1>
            <p class="text-gray-600 mt-1">Sidebar veya footer için yeni widget ekleyin</p>
        </div>
        <a href="{{ route('admin.widgets.index') }}" 
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Geri Dön
        </a>
    </div>

    <form action="{{ route('admin.widgets.store') }}" method="POST">
        @csrf

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
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="mb-6">
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                        <input type="text" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror" 
                               id="slug" name="slug" value="{{ old('slug') }}">
                        <p class="mt-1 text-sm text-gray-500">Boş bırakılırsa otomatik oluşturulur</p>
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
                            <option value="">Seçiniz...</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- İçerik (HTML türü için) -->
                <div class="bg-white rounded-lg shadow-sm p-6" x-show="showContentField">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">İçerik</h2>
                    
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">HTML İçerik</label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm @error('content') border-red-500 @enderror" 
                                  id="content" name="content" rows="12">{{ old('content') }}</textarea>
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
                                   name="settings[limit]" value="5" min="1" max="20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">İçerik Türü</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                    name="settings[content_type]">
                                <option value="all">Tümü</option>
                                <option value="guides">Rehberler</option>
                                <option value="community">Topluluk Gönderileri</option>
                            </select>
                        </div>
                    </div>

                    <!-- Popüler İçerikler için -->
                    <div x-show="widgetType === 'popular'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">İçerik Sayısı</label>
                            <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   name="settings[limit]" value="5" min="1" max="20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">İçerik Türü</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                    name="settings[content_type]">
                                <option value="all">Tümü</option>
                                <option value="guides">Rehberler</option>
                            </select>
                        </div>
                    </div>

                    <!-- Özel Widget için -->
                    <div x-show="widgetType === 'custom'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">View Adı</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   name="settings[view]" placeholder="widgets.custom.my-widget">
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
                                   {{ old('is_active', true) ? 'checked' : '' }}
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
                            <option value="">Seçiniz...</option>
                            @foreach($locations as $key => $label)
                                <option value="{{ $key }}" {{ old('location') == $key ? 'selected' : '' }}>
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
                               id="order" name="order" value="{{ old('order', 0) }}" min="0">
                        <p class="mt-1 text-sm text-gray-500">Küçük sayı önce gösterilir</p>
                        @error('order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kaydet Butonu -->
                    <button type="submit" 
                            class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Widget Oluştur
                    </button>
                </div>

                <!-- Yardım -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-blue-900 mb-1">Widget Türleri</h3>
                            <ul class="text-xs text-blue-800 space-y-1">
                                <li><strong>HTML İçerik:</strong> Özel HTML kodu</li>
                                <li><strong>Son İçerikler:</strong> En yeni gönderiler</li>
                                <li><strong>Popüler:</strong> En çok görüntülenen</li>
                                <li><strong>Reklam:</strong> Banner veya reklam</li>
                                <li><strong>Özel:</strong> Custom view dosyası</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function widgetForm() {
    return {
        widgetType: '{{ old('type') }}',
        
        get showContentField() {
            return ['html', 'ad', 'custom'].includes(this.widgetType);
        },
        
        get showSettingsField() {
            return ['recent_content', 'popular', 'custom'].includes(this.widgetType);
        }
    }
}
</script>
@endpush
@endsection
