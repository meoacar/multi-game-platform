@extends('admin.layout')

@section('title', 'Rehber Düzenle')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.guides.index') }}" class="hover:text-orange-600">Rehberler</a>
            <span>/</span>
            <span>Düzenle</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Rehber Düzenle</h1>
        <p class="text-gray-600 mt-1">{{ $guide->user->name }} - {{ $guide->user->email }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.guides.update', $guide->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Başlık <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $guide->title) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Game -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Oyun
                </label>
                <select name="game_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Genel (Tüm Oyunlar)</option>
                    @foreach($games as $game)
                        <option value="{{ $game->id }}" {{ old('game_id', $guide->game_id) == $game->id ? 'selected' : '' }}>
                            {{ $game->name }}
                        </option>
                    @endforeach
                </select>
                @error('game_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    İçerik <span class="text-red-500">*</span>
                </label>
                <textarea name="content" rows="20" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 font-mono text-sm @error('content') border-red-500 @enderror">{{ old('content', $guide->content) }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Markdown veya düz metin kullanabilirsiniz</p>
            </div>

            <!-- Published -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_published" value="1" 
                        {{ old('is_published', $guide->is_published) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                    <span class="ml-2 text-sm text-gray-700">Yayınla</span>
                </label>
                <p class="text-sm text-gray-500 mt-1">İşaretlenirse rehber hemen yayınlanır</p>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                    Kaydet
                </button>
                <a href="{{ route('admin.guides.show', $guide->id) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    İptal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
