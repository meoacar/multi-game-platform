@extends('layouts.app')

@section('title', 'Yeni Gönderi Oluştur - Topluluk')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-black bg-gradient-to-r from-purple-500 to-pink-600 bg-clip-text text-transparent mb-2">✍️ Yeni Gönderi Oluştur</h1>
        <p class="text-gray-400 text-lg">Düşüncelerini paylaş, topluluğa katıl</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-8">
                <form action="{{ route('community.store') }}" method="POST" x-data="{ charCount: {{ old('content') ? strlen(old('content')) : 0 }} }">
                    @csrf

                    <!-- Gönderi Tipi -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-300 mb-3">
                            Gönderi Kategorisi <span class="text-red-400">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="intro" 
                                       {{ old('type') == 'intro' ? 'checked' : '' }}
                                       class="peer sr-only">
                                <div class="px-4 py-3 rounded-xl border-2 border-white/20 bg-white/5 peer-checked:border-green-500 peer-checked:bg-green-500/20 transition-all text-center">
                                    <span class="text-2xl block mb-1">👋</span>
                                    <span class="font-bold text-white">Tanışma</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="general" 
                                       {{ old('type', 'general') == 'general' ? 'checked' : '' }}
                                       class="peer sr-only">
                                <div class="px-4 py-3 rounded-xl border-2 border-white/20 bg-white/5 peer-checked:border-blue-500 peer-checked:bg-blue-500/20 transition-all text-center">
                                    <span class="text-2xl block mb-1">💬</span>
                                    <span class="font-bold text-white">Genel</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="question" 
                                       {{ old('type') == 'question' ? 'checked' : '' }}
                                       class="peer sr-only">
                                <div class="px-4 py-3 rounded-xl border-2 border-white/20 bg-white/5 peer-checked:border-orange-500 peer-checked:bg-orange-500/20 transition-all text-center">
                                    <span class="text-2xl block mb-1">❓</span>
                                    <span class="font-bold text-white">Soru</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="achievement" 
                                       {{ old('type') == 'achievement' ? 'checked' : '' }}
                                       class="peer sr-only">
                                <div class="px-4 py-3 rounded-xl border-2 border-white/20 bg-white/5 peer-checked:border-yellow-500 peer-checked:bg-yellow-500/20 transition-all text-center">
                                    <span class="text-2xl block mb-1">🏆</span>
                                    <span class="font-bold text-white">Başarı</span>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- İçerik -->
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-bold text-gray-300 mb-3">
                            İçerik <span class="text-red-400">*</span>
                        </label>
                        <textarea name="content" id="content" rows="10"
                                  x-on:input="charCount = $event.target.value.length"
                                  class="w-full bg-white/10 border-2 border-white/20 text-white placeholder-gray-500 rounded-xl px-4 py-3 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-all resize-none @error('content') border-red-500 @enderror"
                                  placeholder="Ne düşünüyorsun? Deneyimlerini, sorularını veya başarılarını paylaş...">{{ old('content') }}</textarea>
                        
                        <div class="flex justify-between items-center mt-2">
                            @error('content')
                                <p class="text-red-400 text-sm">{{ $message }}</p>
                            @else
                                <p class="text-gray-500 text-sm">Minimum 10, maksimum 1000 karakter</p>
                            @enderror
                            <p class="text-sm font-bold" :class="charCount > 1000 ? 'text-red-400' : (charCount < 10 ? 'text-gray-500' : 'text-purple-400')">
                                <span x-text="charCount"></span> / 1000
                            </p>
                        </div>
                    </div>

                    <!-- Butonlar -->
                    <div class="flex gap-4">
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all transform hover:scale-105">
                            📤 Gönder
                        </button>
                        <a href="{{ route('community.index') }}" 
                           class="px-8 py-3 rounded-xl bg-white/5 text-gray-300 hover:bg-white/10 hover:text-white font-bold transition-all border-2 border-white/20">
                            İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar - İpuçları -->
        <div class="lg:col-span-1 space-y-6">
            <!-- İpuçları -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-2">💡</span>
                    İpuçları
                </h3>
                <div class="space-y-3 text-sm text-gray-300">
                    <div class="flex items-start space-x-2">
                        <span class="text-green-400 mt-0.5">✓</span>
                        <p><strong class="text-white">Tanışma:</strong> Kendini tanıt, oyun deneyimlerini paylaş</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="text-green-400 mt-0.5">✓</span>
                        <p><strong class="text-white">Genel:</strong> Oyun hakkında düşüncelerini, taktiklerini paylaş</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="text-green-400 mt-0.5">✓</span>
                        <p><strong class="text-white">Soru:</strong> Merak ettiklerini sor, yardım iste</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="text-green-400 mt-0.5">✓</span>
                        <p><strong class="text-white">Başarı:</strong> Kazandığın başarıları, rekorları paylaş</p>
                    </div>
                </div>
            </div>

            <!-- Kurallar -->
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-2">📋</span>
                    Topluluk Kuralları
                </h3>
                <div class="space-y-3 text-sm text-gray-300">
                    <div class="flex items-start space-x-2">
                        <span class="text-red-400 mt-0.5">✗</span>
                        <p>Spam yapma</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="text-red-400 mt-0.5">✗</span>
                        <p>Hakaret etme</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="text-red-400 mt-0.5">✗</span>
                        <p>Kişisel bilgi paylaşma</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="text-green-400 mt-0.5">✓</span>
                        <p>Saygılı ve nazik ol</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="text-green-400 mt-0.5">✓</span>
                        <p>Yapıcı eleştiri yap</p>
                    </div>
                </div>
            </div>

            <!-- İstatistik -->
            <div class="bg-gradient-to-br from-purple-500/20 to-pink-600/20 backdrop-blur-sm rounded-2xl shadow-xl border border-purple-500/30 p-6">
                <h3 class="text-lg font-bold text-white mb-3 flex items-center">
                    <span class="text-2xl mr-2">🎯</span>
                    XP Kazan!
                </h3>
                <p class="text-sm text-gray-300 mb-3">
                    Her gönderi için <strong class="text-purple-400">+10 XP</strong> kazan!
                </p>
                <div class="bg-white/10 rounded-lg p-3 text-xs text-gray-400">
                    Aktif ol, seviye atla ve özel rozetler kazan!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
