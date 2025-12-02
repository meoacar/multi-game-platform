@extends('admin.layout')

@section('title', 'Profil Düzenle')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
        <a href="{{ route('admin.profiles.index') }}" class="hover:text-orange-600">Profiller</a>
        <span>/</span>
        <span>Düzenle</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Profil Düzenle</h1>
    <p class="text-gray-600 mt-1">{{ $profile->user->name }} - {{ $profile->user->email }}</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.profiles.update', $profile->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nickname -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    PUBG Nickname
                </label>
                <input type="text" name="nickname" value="{{ old('nickname', $profile->nickname) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('nickname') border-red-500 @enderror">
                @error('nickname')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- PUBG ID -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    PUBG ID
                </label>
                <input type="text" name="pubg_id" value="{{ old('pubg_id', $profile->pubg_id) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('pubg_id') border-red-500 @enderror">
                @error('pubg_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rank -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Rank
                </label>
                <select name="rank" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Seçiniz</option>
                    @foreach(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror'] as $rank)
                        <option value="{{ $rank }}" {{ old('rank', $profile->rank) == $rank ? 'selected' : '' }}>
                            {{ $rank }}
                        </option>
                    @endforeach
                </select>
                @error('rank')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Server Region -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Sunucu Bölgesi
                </label>
                <select name="server_region" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Seçiniz</option>
                    @foreach(['EU', 'MENA', 'ASIA', 'NA', 'SA'] as $region)
                        <option value="{{ $region }}" {{ old('server_region', $profile->server_region) == $region ? 'selected' : '' }}>
                            {{ $region }}
                        </option>
                    @endforeach
                </select>
                @error('server_region')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- City -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Şehir
                </label>
                <input type="text" name="city" value="{{ old('city', $profile->city) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('city') border-red-500 @enderror">
                @error('city')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Age Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Yaş Aralığı
                </label>
                <select name="age_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Seçiniz</option>
                    @foreach(['13-17', '18-24', '25-30', '31-40', '40+'] as $range)
                        <option value="{{ $range }}" {{ old('age_range', $profile->age_range) == $range ? 'selected' : '' }}>
                            {{ $range }}
                        </option>
                    @endforeach
                </select>
                @error('age_range')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gender -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Cinsiyet
                </label>
                <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Seçiniz</option>
                    @foreach(['male' => 'Erkek', 'female' => 'Kadın', 'other' => 'Diğer'] as $value => $label)
                        <option value="{{ $value }}" {{ old('gender', $profile->gender) == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('gender')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Play Style -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Oyun Stili
                </label>
                <select name="play_style" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Seçiniz</option>
                    @foreach(['try-hard', 'chill', 'fun-first', 'competitive'] as $style)
                        <option value="{{ $style }}" {{ old('play_style', $profile->play_style) == $style ? 'selected' : '' }}>
                            {{ ucfirst($style) }}
                        </option>
                    @endforeach
                </select>
                @error('play_style')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Bio -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Biyografi
            </label>
            <textarea name="bio" rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('bio') border-red-500 @enderror">{{ old('bio', $profile->bio) }}</textarea>
            @error('bio')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                Kaydet
            </button>
            <a href="{{ route('admin.profiles.statistics', $profile->id) }}" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg hover:from-blue-600 hover:to-purple-600">
                📊 Oyun İstatistikleri
            </a>
            <a href="{{ route('admin.profiles.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                İptal
            </a>
        </div>
    </form>
</div>
@endsection
