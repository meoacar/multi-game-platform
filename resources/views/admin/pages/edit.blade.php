@extends('admin.layout')

@section('title', 'Sayfa Düzenle')

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
                        ✏️ Sayfa Düzenle
                    </h1>
                    <p class="text-gray-400 mt-1">{{ $page->title }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Ana Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('admin.pages.update', $page) }}" method="POST" 
                    class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-2xl p-8 border border-gray-700/50">
                    @csrf
                    @method('PUT')

                    <!-- Başlık -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-bold text-gray-300 mb-2">
                            Sayfa Başlığı <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title', $page->title) }}" required
                               class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('title') border-red-500 @enderror">
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
                            URL Slug
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500 bg-gray-900 px-3 py-3 rounded-xl border border-gray-700">{{ url('/sayfa/') }}/</span>
                            <input type="text" id="slug" name="slug" value="{{ old('slug', $page->slug) }}"
                                   class="flex-1 px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('slug') border-red-500 @enderror">
                        </div>
                        <p class="mt-2 text-xs text-yellow-500">⚠️ Slug değiştirirseniz eski linkler çalışmayacaktır!</p>
                        @error('slug')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Özet (Excerpt) -->
                    <div class="mb-6">
                        <label for="excerpt" class="block text-sm font-bold text-gray-300 mb-2">
                            Kısa Özet (Opsiyonel)
                        </label>
                        <textarea id="excerpt" name="excerpt" rows="3"
                                  class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all @error('excerpt') border-red-500 @enderror"
                                  placeholder="Sayfanın kısa özeti...">{{ old('excerpt', $page->excerpt) }}</textarea>
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
                                  class="w-full px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-mono text-sm @error('content') border-red-500 @enderror">{{ old('content', $page->content) }}</textarea>
                        @error('content')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
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
                                <option value="{{ $key }}" {{ old('template', $page->template) == $key ? 'selected' : '' }}>
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
                            <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
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
                                      placeholder="Sayfanın kısa açıklaması...">{{ old('meta_description', $page->meta_description) }}</textarea>
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
                            <input type="text" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}"
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
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }}
                                   class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-2 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-bold text-gray-300">Yayında</span>
                                <p class="text-xs text-gray-500 mt-0.5">Sayfayı yayınlamak için işaretleyin.</p>
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
                            Değişiklikleri Kaydet
                        </button>
                        <a href="{{ route('admin.pages.preview', $page) }}" target="_blank"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Önizle
                        </a>
                        <a href="{{ route('admin.pages.index') }}" 
                            class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-bold transition-colors">
                            İptal
                        </a>
                    </div>
                </form>

                <!-- Tehlikeli Bölge -->
                <div class="bg-gradient-to-br from-red-600/20 to-red-700/20 backdrop-blur-sm rounded-2xl p-6 mt-6 border border-red-500/30">
                    <h3 class="text-lg font-bold text-red-400 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Tehlikeli Bölge
                    </h3>
                    <p class="text-sm text-red-300 mb-4">Bu sayfayı silmek istiyorsanız aşağıdaki butona tıklayın. Bu işlem geri alınamaz!</p>
                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Bu sayfayı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Sayfayı Sil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bilgi Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Sayfa Bilgileri -->
                <div class="bg-gradient-to-br from-blue-600/20 to-purple-600/20 backdrop-blur-sm rounded-2xl p-6 border border-blue-500/30">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">ℹ️</span>
                        </div>
                        <h3 class="font-bold text-white text-lg">Sayfa Bilgileri</h3>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center p-3 bg-gray-900/30 rounded-lg">
                            <span class="text-gray-400">Durum:</span>
                            <span class="px-3 py-1 rounded-lg {{ $page->is_published ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                                {{ $page->is_published ? 'Yayında' : 'Taslak' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-900/30 rounded-lg">
                            <span class="text-gray-400">Şablon:</span>
                            <span class="text-white">{{ $templates[$page->template] ?? 'Varsayılan' }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-900/30 rounded-lg">
                            <span class="text-gray-400">Oluşturma:</span>
                            <span class="text-white">{{ $page->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-900/30 rounded-lg">
                            <span class="text-gray-400">Güncelleme:</span>
                            <span class="text-white">{{ $page->updated_at->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Hızlı Linkler -->
                <div class="bg-gradient-to-br from-green-600/20 to-emerald-600/20 backdrop-blur-sm rounded-2xl p-6 border border-green-500/30">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">🔗</span>
                        </div>
                        <h3 class="font-bold text-white text-lg">Hızlı Linkler</h3>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('pages.show', $page->slug) }}" target="_blank"
                           class="flex items-center gap-2 p-3 bg-gray-900/30 hover:bg-gray-900/50 rounded-lg text-white transition-colors">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            <span class="text-sm">Sayfayı Görüntüle</span>
                        </a>
                        <a href="{{ route('admin.pages.preview', $page) }}" target="_blank"
                           class="flex items-center gap-2 p-3 bg-gray-900/30 hover:bg-gray-900/50 rounded-lg text-white transition-colors">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span class="text-sm">Önizleme</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
