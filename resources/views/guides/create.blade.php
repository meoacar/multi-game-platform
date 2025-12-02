@extends('layouts.app')

@section('title', 'Yeni Rehber Oluştur')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ 
    title: '{{ old('title') }}',
    content: '{{ old('content') }}',
    category: '{{ old('category') }}',
    difficulty: '{{ old('difficulty_level', 'beginner') }}'
}">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-5xl font-black bg-gradient-to-r from-blue-500 to-cyan-600 bg-clip-text text-transparent mb-3">
            📚 Yeni Rehber Oluştur
        </h1>
        <p class="text-gray-400 text-lg">Bilgini paylaş, topluluğa katkıda bulun! +15 XP kazanacaksın 🎉</p>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-full flex items-center justify-center text-white font-bold">1</div>
                <span class="ml-2 text-white font-semibold">Başlık</span>
            </div>
            <div class="w-16 h-1 bg-white/20"></div>
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white font-bold">2</div>
                <span class="ml-2 text-gray-400 font-semibold">İçerik</span>
            </div>
            <div class="w-16 h-1 bg-white/20"></div>
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white font-bold">3</div>
                <span class="ml-2 text-gray-400 font-semibold">Yayınla</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('guide.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Başlık -->
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
                    <label class="block text-lg font-bold text-white mb-4">
                        📝 Rehber Başlığı <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" x-model="title" value="{{ old('title') }}" required
                        class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-gray-500 @error('title') border-red-500 @enderror"
                        placeholder="Örn: PUBG Mobile'da Recoil Kontrolü Nasıl Yapılır?"
                        maxlength="255">
                    <div class="flex justify-between mt-2">
                        <span class="text-gray-400 text-sm">Açıklayıcı ve SEO dostu bir başlık seç</span>
                        <span class="text-gray-500 text-sm" x-text="title.length + '/255'">0/255</span>
                    </div>
                    @error('title')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori ve Zorluk -->
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
                    <label class="block text-lg font-bold text-white mb-4">
                        🎯 Kategori ve Zorluk Seviyesi
                    </label>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Kategori <span class="text-red-500">*</span></label>
                            <select name="category" x-model="category" required
                                class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition-all [&>option]:bg-gray-800 [&>option]:text-white">
                                <option value="" class="bg-gray-800 text-gray-400">Seçin...</option>
                                <option value="weapons" {{ old('category') == 'weapons' ? 'selected' : '' }} class="bg-gray-800 text-white">🔫 Silahlar</option>
                                <option value="tactics" {{ old('category') == 'tactics' ? 'selected' : '' }} class="bg-gray-800 text-white">🎯 Taktikler</option>
                                <option value="maps" {{ old('category') == 'maps' ? 'selected' : '' }} class="bg-gray-800 text-white">🗺️ Haritalar</option>
                                <option value="settings" {{ old('category') == 'settings' ? 'selected' : '' }} class="bg-gray-800 text-white">⚙️ Ayarlar</option>
                                <option value="tips" {{ old('category') == 'tips' ? 'selected' : '' }} class="bg-gray-800 text-white">💡 İpuçları</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }} class="bg-gray-800 text-white">📦 Diğer</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Zorluk Seviyesi</label>
                            <select name="difficulty_level" x-model="difficulty"
                                class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition-all [&>option]:bg-gray-800 [&>option]:text-white">
                                <option value="beginner" {{ old('difficulty_level') == 'beginner' ? 'selected' : '' }} class="bg-gray-800 text-white">🟢 Başlangıç</option>
                                <option value="intermediate" {{ old('difficulty_level') == 'intermediate' ? 'selected' : '' }} class="bg-gray-800 text-white">🟡 Orta</option>
                                <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }} class="bg-gray-800 text-white">🔴 İleri</option>
                            </select>
                        </div>
                    </div>
                    @error('category')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- İçerik -->
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
                    <label class="block text-lg font-bold text-white mb-4">
                        ✍️ Rehber İçeriği <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" x-model="content" rows="15" required
                        class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-gray-500 font-mono text-sm @error('content') border-red-500 @enderror"
                        placeholder="Rehber içeriğini buraya yazın...

Markdown formatını kullanabilirsiniz:
# Başlık
## Alt Başlık
**Kalın yazı**
*İtalik yazı*
- Liste öğesi
1. Numaralı liste">{{ old('content') }}</textarea>
                    <div class="flex justify-between mt-2">
                        <span class="text-gray-400 text-sm">Markdown formatı desteklenir</span>
                        <span class="text-gray-500 text-sm" x-text="content.length + ' karakter'">0 karakter</span>
                    </div>
                    @error('content')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Etiketler -->
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
                    <label class="block text-lg font-bold text-white mb-4">
                        🏷️ Etiketler
                    </label>
                    <input type="text" name="tags" value="{{ old('tags') }}"
                        class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-gray-500"
                        placeholder="Örn: recoil, spray, m416, akm">
                    <p class="text-gray-400 text-xs mt-2">Virgülle ayırarak birden fazla etiket ekleyebilirsiniz</p>
                </div>

                <!-- Butonlar -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all transform hover:scale-105 flex items-center justify-center">
                        <span class="mr-2">📚</span> Rehberi Yayınla (+15 XP)
                    </button>
                    <a href="{{ route('guide.index') }}" 
                        class="flex-1 bg-white/10 hover:bg-white/20 text-white font-bold py-4 px-8 rounded-xl border-2 border-white/20 transition-all text-center flex items-center justify-center">
                        <span class="mr-2">❌</span> İptal
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar - Önizleme ve İpuçları -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Canlı Önizleme -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6 sticky top-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="mr-2">👁️</span> Canlı Önizleme
                </h3>
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="text-white font-bold text-lg mb-1 line-clamp-2" x-text="title || 'Rehber Başlığı'">Rehber Başlığı</h4>
                            <p class="text-gray-400 text-sm">{{ Auth::user()->name }} • Az önce</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs" x-show="category" x-text="category">Kategori</span>
                        <span class="px-2 py-1 rounded text-xs" 
                            :class="{
                                'bg-green-500/20 text-green-400': difficulty === 'beginner',
                                'bg-yellow-500/20 text-yellow-400': difficulty === 'intermediate',
                                'bg-red-500/20 text-red-400': difficulty === 'advanced'
                            }"
                            x-text="difficulty === 'beginner' ? '🟢 Başlangıç' : difficulty === 'intermediate' ? '🟡 Orta' : '🔴 İleri'">
                            🟢 Başlangıç
                        </span>
                    </div>
                    <p class="text-gray-300 text-sm line-clamp-3" x-text="content || 'Rehber içeriği buraya gelecek...'">Rehber içeriği buraya gelecek...</p>
                </div>
            </div>

            <!-- İpuçları -->
            <div class="bg-gradient-to-br from-blue-500/10 to-cyan-500/10 backdrop-blur-sm rounded-2xl border border-blue-500/20 p-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="mr-2">💡</span> İpuçları
                </h3>
                <ul class="space-y-3 text-sm text-gray-300">
                    <li class="flex items-start">
                        <span class="text-green-400 mr-2">✓</span>
                        <span>Başlığı açık ve anlaşılır yaz</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-400 mr-2">✓</span>
                        <span>Adım adım anlatım yap</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-400 mr-2">✓</span>
                        <span>Görsel eklemek için Markdown kullan</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-400 mr-2">✓</span>
                        <span>Doğru kategori ve zorluk seç</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-400 mr-2">⚡</span>
                        <span>Rehber oluşturarak <strong>+15 XP</strong> kazanırsın!</span>
                    </li>
                </ul>
            </div>

            <!-- Yazar Bilgileri -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
                <h3 class="text-lg font-bold text-white mb-4">✍️ Yazar Bilgileri</h3>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-white font-bold">{{ Auth::user()->name }}</p>
                        <p class="text-gray-400 text-sm">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Toplam XP</span>
                        <span class="text-orange-400 font-bold">{{ Auth::user()->xp_total ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Rehber Sayısı</span>
                        <span class="text-white font-bold">{{ Auth::user()->guidePosts()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
