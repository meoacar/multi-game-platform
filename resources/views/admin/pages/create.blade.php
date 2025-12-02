@extends('admin.layout')

@section('title', 'Yeni Sayfa Oluştur')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.pages.index') }}" 
                    class="w-12 h-12 bg-gray-800 hover:bg-gray-700 rounded-xl flex items-center justify-center transition-colors border border-gray-700">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                        ✨ Yeni Sayfa Oluştur
                    </h1>
                    <p class="text-gray-400 mt-1">Statik sayfa oluşturun (Hakkımızda, İletişim, vb.)</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Ana Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('admin.pages.store') }}" method="POST" 
                    class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-2xl p-8 border border-gray-700/50">
                    @csrf

                    <!-- Başlık -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-bold text-gray-300 mb-2">
                            Sayfa Başlığı <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('title') border-red-500 @enderror"
                               placeholder="Örn: Hakkımızda, Gizlilik Politikası">
                        @error('title')
                            <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="mb-6">
                        <label for="slug" class="block text-sm font-bold text-gray-300 mb-2">
                            URL Slug (Opsiyonel)
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500 bg-gray-900 px-3 py-3 rounded-xl border border-gray-700">{{ url('/sayfa/') }}/</span>
                            <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                                   class="flex-1 px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('slug') border-red-500 @enderror"
                                   placeholder="Boş bırakılırsa otomatik oluşturulur">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Örn: hakkimizda, gizlilik-politikasi (Türkçe karakter kullanmayın)</p>
                        @error('slug')
                            <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Özet (Excerpt) -->
                    <div class="mb-6">
                        <label for="excerpt" class="block text-sm font-bold text-gray-300 mb-2">
                            Kısa Özet (Opsiyonel)
                        </label>
                        <textarea id="excerpt" name="excerpt" rows="3"
                                  class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('excerpt') border-red-500 @enderror"
                                  placeholder="Sayfanın kısa özeti...">{{ old('excerpt') }}</textarea>
                        <p class="mt-2 text-xs text-gray-500">Sayfa önizlemelerinde kullanılır.</p>
                        @error('excerpt')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- İçerik -->
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-bold text-gray-300 mb-2">
                            Sayfa İçeriği <span class="text-red-400">*</span>
                        </label>
                        <textarea id="content" name="content" rows="20" required
                                  class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-mono text-sm @error('content') border-red-500 @enderror"
                                  placeholder="HTML veya düz metin yazabilirsiniz...">{{ old('content') }}</textarea>
                        <p class="mt-2 text-xs text-gray-500">HTML etiketleri desteklenir. Markdown desteği için gelecekte eklenecek.</p>
                        @error('content')
                            <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Şablon Seçimi -->
                    <div class="mb-6">
                        <label for="template" class="block text-sm font-bold text-gray-300 mb-2">
                            Sayfa Şablonu <span class="text-red-400">*</span>
                        </label>
                        <select id="template" name="template" required
                                class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('template') border-red-500 @enderror">
                            @foreach($templates as $key => $label)
                                <option value="{{ $key }}" {{ old('template', 'default') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-500">Sayfanın görünüm şablonunu seçin.</p>
                        @error('template')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SEO Ayarları -->
                    <div class="mb-6 p-6 bg-gray-900/50 rounded-xl border border-gray-700">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            SEO Ayarları
                        </h3>

                        <!-- Meta Title -->
                        <div class="mb-4">
                            <label for="meta_title" class="block text-sm font-bold text-gray-300 mb-2">
                                SEO Başlık (Meta Title)
                            </label>
                            <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                                   maxlength="255"
                                   class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('meta_title') border-red-500 @enderror"
                                   placeholder="Boş bırakılırsa sayfa başlığı kullanılır">
                            <p class="mt-2 text-xs text-gray-500">Arama motorlarında görünecek başlık (max 255 karakter)</p>
                            @error('meta_title')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Description -->
                        <div class="mb-4">
                            <label for="meta_description" class="block text-sm font-bold text-gray-300 mb-2">
                                SEO Açıklama (Meta Description)
                            </label>
                            <textarea id="meta_description" name="meta_description" rows="3"
                                      maxlength="500"
                                      class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('meta_description') border-red-500 @enderror"
                                      placeholder="Sayfanın kısa açıklaması...">{{ old('meta_description') }}</textarea>
                            <p class="mt-2 text-xs text-gray-500">Arama motorlarında görünecek açıklama (max 500 karakter)</p>
                            @error('meta_description')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Keywords -->
                        <div>
                            <label for="meta_keywords" class="block text-sm font-bold text-gray-300 mb-2">
                                Anahtar Kelimeler (Meta Keywords)
                            </label>
                            <input type="text" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}"
                                   maxlength="255"
                                   class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('meta_keywords') border-red-500 @enderror"
                                   placeholder="pubg, mobile, topluluk, oyun">
                            <p class="mt-2 text-xs text-gray-500">Virgülle ayırarak yazın (max 255 karakter)</p>
                            @error('meta_keywords')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Yayın Durumu -->
                    <div class="mb-8">
                        <label class="flex items-center gap-3 p-4 bg-gray-900/50 rounded-xl border border-gray-700 cursor-pointer hover:bg-gray-900/70 transition-colors">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                                   class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-2 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-bold text-gray-300">Hemen yayınla</span>
                                <p class="text-xs text-gray-500 mt-0.5">İşaretlenmezse sayfa taslak olarak kaydedilir.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Butonlar -->
                    <div class="flex items-center gap-4 pt-6 border-t border-gray-700">
                        <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sayfayı Oluştur
                        </button>
                        <a href="{{ route('admin.pages.index') }}" 
                            class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-bold transition-colors">
                            İptal
                        </a>
                    </div>
                </form>
            </div>



            <!-- Yardım Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- İpuçları -->
                <div class="bg-gradient-to-br from-blue-600/20 to-purple-600/20 backdrop-blur-sm rounded-2xl p-6 border border-blue-500/30">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">💡</span>
                        </div>
                        <h3 class="font-bold text-white text-lg">İpuçları</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-300">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Başlık SEO için önemlidir</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Slug URL'de görünür</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>HTML etiketleri kullanabilirsiniz</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Taslak olarak kaydedip sonra yayınlayabilirsiniz</span>
                        </li>
                    </ul>
                </div>

                <!-- Örnek Sayfalar -->
                <div class="bg-gradient-to-br from-purple-600/20 to-pink-600/20 backdrop-blur-sm rounded-2xl p-6 border border-purple-500/30">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">📝</span>
                        </div>
                        <h3 class="font-bold text-white text-lg">Örnek Sayfalar</h3>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li class="flex items-center gap-2 p-2 bg-gray-900/30 rounded-lg">
                            <span class="text-purple-400">•</span>
                            <span>Hakkımızda</span>
                        </li>
                        <li class="flex items-center gap-2 p-2 bg-gray-900/30 rounded-lg">
                            <span class="text-purple-400">•</span>
                            <span>İletişim</span>
                        </li>
                        <li class="flex items-center gap-2 p-2 bg-gray-900/30 rounded-lg">
                            <span class="text-purple-400">•</span>
                            <span>Gizlilik Politikası</span>
                        </li>
                        <li class="flex items-center gap-2 p-2 bg-gray-900/30 rounded-lg">
                            <span class="text-purple-400">•</span>
                            <span>Kullanım Şartları</span>
                        </li>
                        <li class="flex items-center gap-2 p-2 bg-gray-900/30 rounded-lg">
                            <span class="text-purple-400">•</span>
                            <span>SSS (Sık Sorulan Sorular)</span>
                        </li>
                        <li class="flex items-center gap-2 p-2 bg-gray-900/30 rounded-lg">
                            <span class="text-purple-400">•</span>
                            <span>Topluluk Kuralları</span>
                        </li>
                    </ul>
                </div>

                <!-- Klavye Kısayolları -->
                <div class="bg-gradient-to-br from-green-600/20 to-emerald-600/20 backdrop-blur-sm rounded-2xl p-6 border border-green-500/30">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">⌨️</span>
                        </div>
                        <h3 class="font-bold text-white text-lg">Kısayollar</h3>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li class="flex items-center justify-between p-2 bg-gray-900/30 rounded-lg">
                            <span>Kaydet</span>
                            <kbd class="px-2 py-1 bg-gray-800 rounded text-xs border border-gray-700">Ctrl + S</kbd>
                        </li>
                        <li class="flex items-center justify-between p-2 bg-gray-900/30 rounded-lg">
                            <span>Geri</span>
                            <kbd class="px-2 py-1 bg-gray-800 rounded text-xs border border-gray-700">Esc</kbd>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Başlıktan otomatik slug oluştur
document.getElementById('title').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value) {
        const slug = this.value
            .toLowerCase()
            .replace(/ğ/g, 'g')
            .replace(/ü/g, 'u')
            .replace(/ş/g, 's')
            .replace(/ı/g, 'i')
            .replace(/ö/g, 'o')
            .replace(/ç/g, 'c')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    }
});

// Ctrl+S ile kaydet
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.querySelector('form').submit();
    }
});

// Esc ile geri dön
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.location.href = '{{ route("admin.pages.index") }}';
    }
});
</script>
@endsection
