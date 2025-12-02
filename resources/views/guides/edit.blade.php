@extends('layouts.app')

@section('title', 'Rehberi Düzenle')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold mb-6">Rehberi Düzenle</h1>

        <form action="{{ route('guides.update', $guide->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Oyun Seçimi -->
            <div class="mb-6">
                <label for="game_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Oyun <span class="text-red-500">*</span>
                </label>
                <select name="game_id" id="game_id" 
                        class="w-full border rounded-lg px-4 py-2 @error('game_id') border-red-500 @enderror">
                    <option value="">Oyun Seçin</option>
                    @foreach($games as $game)
                        <option value="{{ $game->id }}" 
                                {{ old('game_id', $guide->game_id) == $game->id ? 'selected' : '' }}>
                            {{ $game->name }}
                        </option>
                    @endforeach
                </select>
                @error('game_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Başlık -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Başlık <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" 
                       value="{{ old('title', $guide->title) }}"
                       class="w-full border rounded-lg px-4 py-2 @error('title') border-red-500 @enderror"
                       placeholder="Örn: PUBG Mobile'da Recoil Kontrolü Nasıl Yapılır?">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- İçerik -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    İçerik <span class="text-red-500">*</span>
                </label>
                <textarea name="content" id="content" rows="15"
                          class="w-full border rounded-lg px-4 py-2 @error('content') border-red-500 @enderror"
                          placeholder="Rehberinizi detaylı bir şekilde yazın...">{{ old('content', $guide->content) }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">
                    Minimum 100 karakter gereklidir
                </p>
            </div>

            <!-- Yayınlama Durumu -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_published" value="1" 
                           {{ old('is_published', $guide->is_published) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 mr-2">
                    <span class="text-sm font-medium text-gray-700">Yayında</span>
                </label>
            </div>

            <!-- Butonlar -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                    Değişiklikleri Kaydet
                </button>
                <a href="{{ route('guides.show', $guide->id) }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 font-medium">
                    İptal
                </a>
            </div>
        </form>
    </div>

    <!-- Rehber Bilgileri -->
    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h3 class="font-bold text-gray-900 mb-3">📊 Rehber İstatistikleri</h3>
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-600">Görüntülenme</p>
                <p class="text-2xl font-bold text-gray-900">{{ $guide->views_count }}</p>
            </div>
            <div>
                <p class="text-gray-600">Beğeni</p>
                <p class="text-2xl font-bold text-gray-900">{{ $guide->likes_count }}</p>
            </div>
            <div>
                <p class="text-gray-600">Yorum</p>
                <p class="text-2xl font-bold text-gray-900">{{ $guide->comments->count() }}</p>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-4">
            Oluşturulma: {{ $guide->created_at->format('d.m.Y H:i') }}
        </p>
    </div>
</div>
@endsection
