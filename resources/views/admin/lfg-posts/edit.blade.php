@extends('admin.layout')

@section('title', 'İlan Düzenle')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.lfg-posts.index') }}" class="hover:text-orange-600">İlanlar</a>
            <span>/</span>
            <span>Düzenle</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">İlan Düzenle</h1>
        <p class="text-gray-600 mt-1">{{ $post->user->name }} - {{ $post->user->email }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.lfg-posts.update', $post->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Başlık <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Açıklama <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="4" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('description') border-red-500 @enderror">{{ old('description', $post->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Game -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Oyun <span class="text-red-500">*</span>
                    </label>
                    <select name="game_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        @foreach($games as $game)
                            <option value="{{ $game->id }}" {{ old('game_id', $post->game_id) == $game->id ? 'selected' : '' }}>
                                {{ $game->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('game_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mod</label>
                    <input type="text" name="mode" value="{{ old('mode', $post->mode) }}"
                        placeholder="Örn: Squad TPP"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <!-- Min Rank -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Rank</label>
                    <select name="min_rank" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Seçiniz</option>
                        @foreach(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror'] as $rank)
                            <option value="{{ $rank }}" {{ old('min_rank', $post->min_rank) == $rank ? 'selected' : '' }}>
                                {{ $rank }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Max Rank -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Rank</label>
                    <select name="max_rank" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Seçiniz</option>
                        @foreach(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror'] as $rank)
                            <option value="{{ $rank }}" {{ old('max_rank', $post->max_rank) == $rank ? 'selected' : '' }}>
                                {{ $rank }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şehir</label>
                    <input type="text" name="city" value="{{ old('city', $post->city) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <!-- Play Style -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Oyun Stili</label>
                    <select name="play_style_tag" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Seçiniz</option>
                        @foreach(['try-hard', 'chill', 'fun-first', 'competitive'] as $style)
                            <option value="{{ $style }}" {{ old('play_style_tag', $post->play_style_tag) == $style ? 'selected' : '' }}>
                                {{ ucfirst($style) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Microphone Required -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mikrofon</label>
                    <select name="microphone_required" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="0" {{ old('microphone_required', $post->microphone_required) == 0 ? 'selected' : '' }}>Opsiyonel</option>
                        <option value="1" {{ old('microphone_required', $post->microphone_required) == 1 ? 'selected' : '' }}>Gerekli</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Durum <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="open" {{ old('status', $post->status) == 'open' ? 'selected' : '' }}>Açık</option>
                        <option value="closed" {{ old('status', $post->status) == 'closed' ? 'selected' : '' }}>Kapalı</option>
                    </select>
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Admin Notları
                </label>
                <textarea name="admin_notes" rows="3"
                    placeholder="Sadece adminler görebilir..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('admin_notes') border-red-500 @enderror">{{ old('admin_notes', $post->admin_notes) }}</textarea>
                @error('admin_notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                    Kaydet
                </button>
                <a href="{{ route('admin.lfg-posts.show', $post->id) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    İptal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
