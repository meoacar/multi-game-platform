@extends('layouts.app')

@section('title', 'Yeni İlan Oluştur - PUBG Mobile Topluluk')

@section('content')
<style>
    /* Select option'ları için stil */
    select option {
        background-color: #1f2937 !important;
        color: white !important;
        padding: 10px;
    }
</style>
<!-- PUBG Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" 
     style="background-image: url('/arkaplan/3.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    
    <!-- Karartma Katmanı -->
    <div class="absolute inset-0 bg-black/60"></div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-orange-900/30 via-transparent to-black/60"></div>
    
    <!-- Animasyonlu Işık Efektleri -->
    <div class="absolute inset-0">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl animate-pulse" style="animation-duration: 4s;"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse" style="animation-duration: 6s; animation-delay: 1s;"></div>
    </div>
</div>

<div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-black bg-gradient-to-r from-orange-500 to-red-600 bg-clip-text text-transparent mb-2">🎯 Yeni Takım Arama İlanı</h1>
        <p class="text-gray-400 text-lg">Takım arkadaşlarını bul ve zafere ulaş!</p>
    </div>

    <!-- Form -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-8">
        <form action="{{ route('lfg.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Oyun Seçimi -->
            <div>
                <label for="game_id" class="block text-sm font-bold text-gray-300 mb-2">
                    🎮 Oyun <span class="text-red-500">*</span>
                </label>
                <select name="game_id" id="game_id" required
                    class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all @error('game_id') border-red-500 @enderror">
                    <option value="">Oyun Seçin</option>
                    @foreach($games as $game)
                        <option value="{{ $game->id }}" {{ old('game_id') == $game->id ? 'selected' : '' }}>
                            {{ $game->name }}
                        </option>
                    @endforeach
                </select>
                @error('game_id')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Başlık -->
            <div>
                <label for="title" class="block text-sm font-bold text-gray-300 mb-2">
                    📝 İlan Başlığı <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required maxlength="255"
                    value="{{ old('title') }}"
                    placeholder="Örn: Ranked için takım arkadaşı arıyorum"
                    class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all placeholder-gray-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Açıklama -->
            <div>
                <label for="description" class="block text-sm font-bold text-gray-300 mb-2">
                    💬 Açıklama <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" required rows="5"
                    placeholder="İlan detaylarını yazın... (Hangi modda oynuyorsun, ne tür oyuncular arıyorsun, vs.)"
                    class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all placeholder-gray-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Oyun Modu -->
            <div>
                <label for="mode" class="block text-sm font-bold text-gray-300 mb-2">
                    🎯 Oyun Modu
                </label>
                <input type="text" name="mode" id="mode" maxlength="100"
                    value="{{ old('mode') }}"
                    placeholder="Örn: Squad, Duo, Solo"
                    class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all placeholder-gray-500 @error('mode') border-red-500 @enderror">
                @error('mode')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rütbe Aralığı -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="min_rank" class="block text-sm font-bold text-gray-300 mb-2">
                        🏆 Minimum Rütbe
                    </label>
                    <input type="text" name="min_rank" id="min_rank"
                        value="{{ old('min_rank') }}"
                        placeholder="Örn: Platinum"
                        class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all placeholder-gray-500 @error('min_rank') border-red-500 @enderror">
                    @error('min_rank')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_rank" class="block text-sm font-bold text-gray-300 mb-2">
                        💎 Maksimum Rütbe
                    </label>
                    <input type="text" name="max_rank" id="max_rank"
                        value="{{ old('max_rank') }}"
                        placeholder="Örn: Diamond"
                        class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all placeholder-gray-500 @error('max_rank') border-red-500 @enderror">
                    @error('max_rank')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Yaş Aralığı -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="min_age_range" class="block text-sm font-bold text-gray-300 mb-2">
                        👤 Minimum Yaş
                    </label>
                    <input type="text" name="min_age_range" id="min_age_range"
                        value="{{ old('min_age_range') }}"
                        placeholder="Örn: 18"
                        class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all placeholder-gray-500 @error('min_age_range') border-red-500 @enderror">
                    @error('min_age_range')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_age_range" class="block text-sm font-bold text-gray-300 mb-2">
                        👥 Maksimum Yaş
                    </label>
                    <input type="text" name="max_age_range" id="max_age_range"
                        value="{{ old('max_age_range') }}"
                        placeholder="Örn: 30"
                        class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all placeholder-gray-500 @error('max_age_range') border-red-500 @enderror">
                    @error('max_age_range')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Şehir -->
            <div>
                <label for="city" class="block text-sm font-bold text-gray-300 mb-2">
                    📍 Şehir
                </label>
                <input type="text" name="city" id="city" maxlength="255"
                    value="{{ old('city') }}"
                    placeholder="Örn: İstanbul"
                    class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all placeholder-gray-500 @error('city') border-red-500 @enderror">
                @error('city')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Oyun Tarzı -->
            <div>
                <label for="play_style_tag" class="block text-sm font-bold text-gray-300 mb-2">
                    ⚡ Oyun Tarzı
                </label>
                <select name="play_style_tag" id="play_style_tag"
                    class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all @error('play_style_tag') border-red-500 @enderror">
                    <option value="">Seçiniz</option>
                    <option value="try-hard" {{ old('play_style_tag') == 'try-hard' ? 'selected' : '' }}>🔥 Try-Hard (Kazanmak için oynuyorum)</option>
                    <option value="competitive" {{ old('play_style_tag') == 'competitive' ? 'selected' : '' }}>⚔️ Competitive (Rekabetçi)</option>
                    <option value="chill" {{ old('play_style_tag') == 'chill' ? 'selected' : '' }}>😎 Chill (Rahat)</option>
                    <option value="fun-first" {{ old('play_style_tag') == 'fun-first' ? 'selected' : '' }}>🎉 Fun First (Eğlence öncelikli)</option>
                    <option value="casual" {{ old('play_style_tag') == 'casual' ? 'selected' : '' }}>🎮 Casual (Gündelik)</option>
                </select>
                @error('play_style_tag')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mikrofon Gerekli mi? -->
            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="microphone_required" id="microphone_required" value="1"
                        {{ old('microphone_required') ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-2 border-white/20 bg-white/10 text-orange-600 focus:ring-orange-500">
                    <span class="ml-3 text-sm font-bold text-gray-300">🎤 Mikrofon zorunlu</span>
                </label>
            </div>

            <!-- Son Başvuru Tarihi -->
            <div>
                <label for="expires_at" class="block text-sm font-bold text-gray-300 mb-2">
                    ⏰ İlan Bitiş Tarihi (Opsiyonel)
                </label>
                <input type="datetime-local" name="expires_at" id="expires_at"
                    value="{{ old('expires_at') }}"
                    class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all @error('expires_at') border-red-500 @enderror">
                @error('expires_at')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-400">💡 Belirtmezseniz ilan süresiz açık kalır</p>
            </div>

            <!-- Butonlar -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-white/10">
                <a href="{{ route('lfg.index') }}" 
                    class="px-8 py-3 border-2 border-white/20 rounded-xl text-gray-300 hover:bg-white/5 hover:border-white/40 transition-all font-bold">
                    ❌ İptal
                </a>
                <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white rounded-xl font-bold shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all transform hover:scale-105">
                    🚀 İlanı Yayınla
                </button>
            </div>
        </form>
    </div>

    <!-- Bilgilendirme Kutusu -->
    <div class="mt-6 bg-gradient-to-r from-blue-500/10 to-purple-500/10 backdrop-blur-sm rounded-2xl border border-blue-500/20 p-6">
        <div class="flex items-start">
            <div class="text-3xl mr-4">💡</div>
            <div>
                <h3 class="text-lg font-bold text-white mb-2">İpuçları</h3>
                <ul class="text-sm text-gray-300 space-y-1">
                    <li>• Başlığı açık ve net yazın</li>
                    <li>• Aradığınız oyuncu tipini detaylı açıklayın</li>
                    <li>• Rütbe ve yaş aralığı belirterek daha uygun oyuncular bulun</li>
                    <li>• İlan oluşturarak <span class="text-orange-500 font-bold">+10 XP</span> kazanırsınız!</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
