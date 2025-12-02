@extends('layouts.admin')

@section('title', 'Yeni Rozet Oluştur')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Yeni Rozet Oluştur</h1>
            <p class="text-gray-600 mt-1">Yeni bir başarı rozeti ekleyin</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.badges.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
            @csrf

            <!-- İsim -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rozet İsmi *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug (Boş bırakılırsa otomatik oluşturulur)</label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('slug') border-red-500 @enderror">
                @error('slug')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Açıklama -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama *</label>
                <textarea name="description" rows="3" required
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- İkon -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">İkon (emoji veya icon class) *</label>
                <input type="text" name="icon" value="{{ old('icon') }}" required
                       placeholder="🏆 veya trophy"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('icon') border-red-500 @enderror">
                @error('icon')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                <select name="category" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror">
                    <option value="">Seçiniz</option>
                    <option value="gameplay" {{ old('category') == 'gameplay' ? 'selected' : '' }}>Oyun</option>
                    <option value="social" {{ old('category') == 'social' ? 'selected' : '' }}>Sosyal</option>
                    <option value="content" {{ old('category') == 'content' ? 'selected' : '' }}>İçerik</option>
                    <option value="special" {{ old('category') == 'special' ? 'selected' : '' }}>Özel</option>
                    <option value="activity" {{ old('category') == 'activity' ? 'selected' : '' }}>Aktivite</option>
                    <option value="moderation" {{ old('category') == 'moderation' ? 'selected' : '' }}>Moderasyon</option>
                </select>
                @error('category')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nadirlik -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nadirlik *</label>
                <select name="rarity" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('rarity') border-red-500 @enderror">
                    <option value="">Seçiniz</option>
                    <option value="common" {{ old('rarity') == 'common' ? 'selected' : '' }}>Yaygın</option>
                    <option value="rare" {{ old('rarity') == 'rare' ? 'selected' : '' }}>Nadir</option>
                    <option value="epic" {{ old('rarity') == 'epic' ? 'selected' : '' }}>Epik</option>
                    <option value="legendary" {{ old('rarity') == 'legendary' ? 'selected' : '' }}>Efsanevi</option>
                </select>
                @error('rarity')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- XP Gereksinimi -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Gereken XP</label>
                <input type="number" name="xp_required" value="{{ old('xp_required', 0) }}" min="0"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('xp_required') border-red-500 @enderror">
                @error('xp_required')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sıra -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Sıra</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('sort_order') border-red-500 @enderror">
                @error('sort_order')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Checkbox'lar -->
            <div class="mb-4 space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_hidden" value="1" {{ old('is_hidden') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Gizli rozet (kullanıcılar göremez)</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
            </div>

            <!-- Butonlar -->
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('admin.badges.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    İptal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Rozet Oluştur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
