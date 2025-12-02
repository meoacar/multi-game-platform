@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Takımı Düzenle</h1>

        <form action="{{ route('squads.update', $squad->slug) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Takım Adı -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Takım Adı *
                </label>
                <input type="text" name="name" id="name" required
                    class="w-full border-gray-300 rounded-lg @error('name') border-red-500 @enderror"
                    value="{{ old('name', $squad->name) }}">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Açıklama -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Açıklama
                </label>
                <textarea name="description" id="description" rows="4"
                    class="w-full border-gray-300 rounded-lg @error('description') border-red-500 @enderror">{{ old('description', $squad->description) }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Maksimum Üye Sayısı -->
            <div class="mb-6">
                <label for="max_members" class="block text-sm font-medium text-gray-700 mb-2">
                    Maksimum Üye Sayısı
                </label>
                <select name="max_members" id="max_members"
                    class="w-full border-gray-300 rounded-lg @error('max_members') border-red-500 @enderror">
                    <option value="4" {{ old('max_members', $squad->max_members) == 4 ? 'selected' : '' }}>4 Kişi</option>
                    <option value="5" {{ old('max_members', $squad->max_members) == 5 ? 'selected' : '' }}>5 Kişi</option>
                    <option value="6" {{ old('max_members', $squad->max_members) == 6 ? 'selected' : '' }}>6 Kişi</option>
                    <option value="8" {{ old('max_members', $squad->max_members) == 8 ? 'selected' : '' }}>8 Kişi</option>
                    <option value="10" {{ old('max_members', $squad->max_members) == 10 ? 'selected' : '' }}>10 Kişi</option>
                </select>
                @error('max_members')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Mevcut üye sayısı: {{ $squad->members->count() }}</p>
            </div>

            <!-- Durum -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                        {{ old('is_active', $squad->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600">
                    <span class="ml-2 text-sm text-gray-700">Takım aktif</span>
                </label>
                <p class="mt-1 text-sm text-gray-500">Pasif takımlar listede görünmez</p>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                    Değişiklikleri Kaydet
                </button>
                <a href="{{ route('squads.show', $squad->slug) }}" class="flex-1 text-center bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 font-medium">
                    İptal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
