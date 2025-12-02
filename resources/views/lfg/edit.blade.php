@extends('layouts.app')

@section('title', 'İlan Düzenle')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">✏️ İlan Düzenle</h1>

        <form action="{{ route('lfg.update', $post->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Başlık -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    İlan Başlığı <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Açıklama -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Açıklama <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="5" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $post->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rütbe Aralığı -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Rütbe</label>
                    <input type="text" name="min_rank" value="{{ old('min_rank', $post->min_rank) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Rütbe</label>
                    <input type="text" name="max_rank" value="{{ old('max_rank', $post->max_rank) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Mod -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Oyun Modu</label>
                <input type="text" name="mode" value="{{ old('mode', $post->mode) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Mikrofon -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="microphone_required" value="1" 
                        {{ old('microphone_required', $post->microphone_required) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">🎤 Mikrofon gerekli</span>
                </label>
            </div>

            <!-- Oyun Tarzı -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Oyun Tarzı</label>
                <select name="play_style_tag"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seçin...</option>
                    <option value="try-hard" {{ old('play_style_tag', $post->play_style_tag) == 'try-hard' ? 'selected' : '' }}>Try-Hard</option>
                    <option value="chill" {{ old('play_style_tag', $post->play_style_tag) == 'chill' ? 'selected' : '' }}>Chill</option>
                    <option value="fun-first" {{ old('play_style_tag', $post->play_style_tag) == 'fun-first' ? 'selected' : '' }}>Fun First</option>
                    <option value="competitive" {{ old('play_style_tag', $post->play_style_tag) == 'competitive' ? 'selected' : '' }}>Competitive</option>
                    <option value="casual" {{ old('play_style_tag', $post->play_style_tag) == 'casual' ? 'selected' : '' }}>Casual</option>
                </select>
            </div>

            <!-- Şehir -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Şehir</label>
                <input type="text" name="city" value="{{ old('city', $post->city) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Durum -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">İlan Durumu</label>
                <select name="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="open" {{ old('status', $post->status) == 'open' ? 'selected' : '' }}>Açık</option>
                    <option value="closed" {{ old('status', $post->status) == 'closed' ? 'selected' : '' }}>Kapalı</option>
                </select>
            </div>

            <!-- Butonlar -->
            <div class="flex space-x-4">
                <button type="submit" 
                    class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    💾 Değişiklikleri Kaydet
                </button>
                <a href="{{ route('lfg.show', $post->id) }}" 
                    class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 text-center font-semibold">
                    ❌ İptal
                </a>
            </div>
        </form>

        <!-- İlanı Sil -->
        <form action="{{ route('lfg.destroy', $post->id) }}" method="POST" class="mt-6"
            onsubmit="return confirm('Bu ilanı silmek istediğinizden emin misiniz?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-semibold">
                🗑️ İlanı Sil
            </button>
        </form>
    </div>
</div>
@endsection
