@extends('admin.layout')

@section('title', 'Klan Düzenle')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.clans.index') }}" class="hover:text-orange-600">Klanlar</a>
            <span>/</span>
            <span>Düzenle</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Klan Düzenle</h1>
        <p class="text-gray-600 mt-1">{{ $clan->leader->name }} - {{ $clan->leader->email }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.clans.update', $clan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Klan Adı <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $clan->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Açıklama <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="4" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('description') border-red-500 @enderror">{{ old('description', $clan->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Requirements -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Gereksinimler
                </label>
                <textarea name="requirements" rows="3"
                    placeholder="Rank, yaş, aktiflik vb. gereksinimler..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('requirements') border-red-500 @enderror">{{ old('requirements', $clan->requirements) }}</textarea>
                @error('requirements')
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
                            <option value="{{ $game->id }}" {{ old('game_id', $clan->game_id) == $game->id ? 'selected' : '' }}>
                                {{ $game->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('game_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şehir</label>
                    <input type="text" name="city" value="{{ old('city', $clan->city) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <!-- Min Rank -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Rank</label>
                    <select name="min_rank" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Seçiniz</option>
                        @foreach(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror'] as $rank)
                            <option value="{{ $rank }}" {{ old('min_rank', $clan->min_rank) == $rank ? 'selected' : '' }}>
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
                            <option value="{{ $rank }}" {{ old('max_rank', $clan->max_rank) == $rank ? 'selected' : '' }}>
                                {{ $rank }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Min Age Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Yaş Aralığı</label>
                    <select name="min_age_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Seçiniz</option>
                        @foreach(['13-17', '18-24', '25-30', '31-40', '40+'] as $range)
                            <option value="{{ $range }}" {{ old('min_age_range', $clan->min_age_range) == $range ? 'selected' : '' }}>
                                {{ $range }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Max Age Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Yaş Aralığı</label>
                    <select name="max_age_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Seçiniz</option>
                        @foreach(['13-17', '18-24', '25-30', '31-40', '40+'] as $range)
                            <option value="{{ $range }}" {{ old('max_age_range', $clan->max_age_range) == $range ? 'selected' : '' }}>
                                {{ $range }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Max Members -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Üye Sayısı</label>
                    <input type="number" name="max_members" value="{{ old('max_members', $clan->max_members ?? 50) }}" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <!-- Discord Invite -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discord Davet Linki</label>
                    <input type="text" name="discord_invite" value="{{ old('discord_invite', $clan->discord_invite) }}"
                        placeholder="https://discord.gg/..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                    Kaydet
                </button>
                <a href="{{ route('admin.clans.show', $clan->id) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    İptal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
