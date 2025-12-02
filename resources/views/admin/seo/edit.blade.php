@extends('admin.layout')

@section('title', 'SEO Ayarı Düzenle')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            🔍 SEO Ayarı Düzenle
        </h1>
        <p class="text-gray-600 mt-2">{{ $seoSetting->page_type }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.seo.update', $seoSetting) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Sayfa Tipi</label>
                <input type="text" value="{{ $seoSetting->page_type }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
            </div>

            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Başlık</label>
                    <input type="text" name="title" value="{{ old('title', $seoSetting->title) }}" maxlength="255" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Açıklama</label>
                    <textarea name="description" rows="3" maxlength="500" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $seoSetting->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Anahtar Kelimeler</label>
                    <input type="text" name="keywords" value="{{ old('keywords', $seoSetting->keywords) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('keywords') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Virgülle ayırın</p>
                    @error('keywords')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="border-t pt-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Open Graph (Facebook)</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OG Başlık</label>
                        <input type="text" name="og_title" value="{{ old('og_title', $seoSetting->og_title) }}" maxlength="255" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OG Açıklama</label>
                        <textarea name="og_description" rows="2" maxlength="500" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('og_description', $seoSetting->og_description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OG Görsel URL</label>
                        <input type="url" name="og_image" value="{{ old('og_image', $seoSetting->og_image) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="border-t pt-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Twitter Card</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Başlık</label>
                        <input type="text" name="twitter_title" value="{{ old('twitter_title', $seoSetting->twitter_title) }}" maxlength="255" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Açıklama</label>
                        <textarea name="twitter_description" rows="2" maxlength="500" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('twitter_description', $seoSetting->twitter_description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Twitter Görsel URL</label>
                        <input type="url" name="twitter_image" value="{{ old('twitter_image', $seoSetting->twitter_image) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $seoSetting->is_active) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Güncelle
                </button>
                <a href="{{ route('admin.seo.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    İptal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
