@extends('layouts.app')

@section('title', 'Klan Düzenle')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">✏️ Klan Düzenle</h1>

        <form action="{{ route('clans.update', $clan->slug) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Klan Adı -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Klan Adı <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $clan->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Açıklama -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Açıklama <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="5" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $clan->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gereksinimler -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Gereksinimler
                </label>
                <textarea name="requirements" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('requirements', $clan->requirements) }}</textarea>
            </div>

            <!-- Rütbe Aralığı -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Rütbe</label>
                    <input type="text" name="min_rank" value="{{ old('min_rank', $clan->min_rank) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Rütbe</label>
                    <input type="text" name="max_rank" value="{{ old('max_rank', $clan->max_rank) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Şehir -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Şehir</label>
                <input type="text" name="city" value="{{ old('city', $clan->city) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Maksimum Üye Sayısı -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Maksimum Üye Sayısı</label>
                <input type="number" name="max_members" value="{{ old('max_members', $clan->max_members) }}" min="5" max="100"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Discord Davet Linki -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Discord Davet Linki</label>
                <input type="url" name="discord_invite" value="{{ old('discord_invite', $clan->discord_invite) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Butonlar -->
            <div class="flex space-x-4">
                <button type="submit" 
                    class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    💾 Değişiklikleri Kaydet
                </button>
                <a href="{{ route('clans.show', $clan->slug) }}" 
                    class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 text-center font-semibold">
                    ❌ İptal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
