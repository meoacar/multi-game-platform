@extends('onboarding.layout')

@section('content')
<div class="space-y-6">
    <!-- Başlık -->
    <div class="text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">
            {{ $game->name ?? 'Oyun' }} Profil Bilgilerin 🎯
        </h2>
        <p class="text-gray-300">
            Oyun bilgilerini paylaş, benzer seviyedeki oyuncularla eşleş
        </p>
    </div>

    <!-- Form -->
    <form action="{{ route('onboarding.step1') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Game ID -->
        <div>
            <label for="pubg_id" class="block text-sm font-semibold text-gray-200 mb-2">
                {{ $game->name ?? 'Oyun' }} ID <span class="text-game-danger">*</span>
            </label>
            <input type="text" 
                   id="pubg_id" 
                   name="pubg_id" 
                   value="{{ old('pubg_id', $user->pubg_id) }}"
                   placeholder="Örn: PlayerName#1234"
                   class="input-pubg input-mobile @error('pubg_id') input-pubg-error @enderror"
                   required>
            @error('pubg_id')
                <p class="mt-1 text-sm text-game-danger flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Player Level -->
        <div>
            <label for="player_level" class="block text-sm font-semibold text-gray-200 mb-2">
                Oyuncu Seviyesi <span class="text-game-danger">*</span>
            </label>
            <select id="player_level" 
                    name="player_level" 
                    class="input-pubg input-mobile @error('player_level') input-pubg-error @enderror"
                    required>
                <option value="">Seviye Seç</option>
                @for ($i = 1; $i <= 100; $i++)
                    <option value="{{ $i }}" {{ old('player_level', $user->player_level) == $i ? 'selected' : '' }}>
                        Seviye {{ $i }}
                    </option>
                @endfor
            </select>
            @error('player_level')
                <p class="mt-1 text-sm text-game-danger flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Player Tier -->
        <div>
            <label for="player_tier" class="block text-sm font-semibold text-gray-200 mb-2">
                Tier (Rütbe) <span class="text-game-danger">*</span>
            </label>
            <select id="player_tier" 
                    name="player_tier" 
                    class="input-pubg input-mobile @error('player_tier') input-pubg-error @enderror"
                    required>
                <option value="">Tier Seç</option>
                @php
                    $tiers = ['Bronz', 'Gümüş', 'Altın', 'Platin', 'Elmas', 'Taç', 'As', 'As Ustası', 'As Hakimi', 'Fatih'];
                    $tierIcons = ['🥉', '🥈', '🥇', '💎', '💠', '👑', '🏆', '⭐', '🌟', '⚡'];
                @endphp
                @foreach($tiers as $index => $tier)
                    <option value="{{ $tier }}" {{ old('player_tier', $user->player_tier) == $tier ? 'selected' : '' }}>
                        {{ $tierIcons[$index] }} {{ $tier }}
                    </option>
                @endforeach
            </select>
            @error('player_tier')
                <p class="mt-1 text-sm text-game-danger flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Main Server -->
        <div>
            <label for="main_server" class="block text-sm font-semibold text-gray-200 mb-2">
                Ana Sunucu <span class="text-game-danger">*</span>
            </label>
            <select id="main_server" 
                    name="main_server" 
                    class="input-pubg input-mobile @error('main_server') input-pubg-error @enderror"
                    required>
                <option value="">Sunucu Seç</option>
                <option value="Europe" {{ old('main_server', $user->main_server) == 'Europe' ? 'selected' : '' }}>🇪🇺 Europe</option>
                <option value="Asia" {{ old('main_server', $user->main_server) == 'Asia' ? 'selected' : '' }}>🌏 Asia</option>
                <option value="America" {{ old('main_server', $user->main_server) == 'America' ? 'selected' : '' }}>🌎 America</option>
            </select>
            @error('main_server')
                <p class="mt-1 text-sm text-game-danger flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="btn-pubg-primary btn-touch-lg btn-mobile w-full flex items-center justify-center group touch-feedback">
            <span>Devam Et</span>
            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </form>
</div>
@endsection
