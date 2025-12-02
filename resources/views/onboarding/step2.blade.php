@extends('onboarding.layout')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">
            Oyun Tercihlerin 🎮
        </h2>
        <p class="text-gray-300">
            Nasıl oynamayı sevdiğini anlat
        </p>
    </div>

    <form action="{{ route('onboarding.step2') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Favorite Mode -->
        <div>
            <label class="block text-sm font-semibold text-gray-200 mb-3">
                Favori Mod <span class="text-game-danger">*</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative cursor-pointer touch-feedback">
                    <input type="radio" name="favorite_mode" value="TPP" 
                           {{ old('favorite_mode', $user->favorite_mode) == 'TPP' ? 'checked' : '' }}
                           class="peer sr-only" required>
                    <div class="btn-touch p-4 bg-gray-700/50 border-2 border-gray-600 rounded-xl text-center transition-smooth peer-checked:border-game-primary peer-checked:bg-game-primary/20 hover:border-gray-500 hover:bg-gray-700/70">
                        <div class="text-2xl mb-1">👁️</div>
                        <div class="font-semibold text-white">TPP</div>
                        <div class="text-xs text-gray-400">Third Person</div>
                    </div>
                </label>
                <label class="relative cursor-pointer touch-feedback">
                    <input type="radio" name="favorite_mode" value="FPP" 
                           {{ old('favorite_mode', $user->favorite_mode) == 'FPP' ? 'checked' : '' }}
                           class="peer sr-only">
                    <div class="btn-touch p-4 bg-gray-700/50 border-2 border-gray-600 rounded-xl text-center transition-smooth peer-checked:border-game-primary peer-checked:bg-game-primary/20 hover:border-gray-500 hover:bg-gray-700/70">
                        <div class="text-2xl mb-1">🎯</div>
                        <div class="font-semibold text-white">FPP</div>
                        <div class="text-xs text-gray-400">First Person</div>
                    </div>
                </label>
            </div>
            @error('favorite_mode')
                <p class="mt-1 text-sm text-game-danger flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Favorite Type -->
        <div>
            <label class="block text-sm font-semibold text-gray-200 mb-3">
                Favori Oyun Tipi <span class="text-game-danger">*</span>
            </label>
            <div class="grid grid-cols-3 gap-3">
                @php
                    $types = [
                        'Solo' => ['icon' => '🧍', 'desc' => 'Tek Başına'],
                        'Duo' => ['icon' => '👥', 'desc' => 'İkili'],
                        'Squad' => ['icon' => '👨‍👩‍👧‍👦', 'desc' => 'Takım']
                    ];
                @endphp
                @foreach($types as $type => $data)
                    <label class="relative cursor-pointer touch-feedback">
                        <input type="radio" name="favorite_type" value="{{ $type }}" 
                               {{ old('favorite_type', $user->favorite_type) == $type ? 'checked' : '' }}
                               class="peer sr-only" required>
                        <div class="btn-touch p-4 bg-gray-700/50 border-2 border-gray-600 rounded-xl text-center transition-smooth peer-checked:border-game-secondary peer-checked:bg-game-secondary/20 hover:border-gray-500 hover:bg-gray-700/70">
                            <div class="text-2xl mb-1">{{ $data['icon'] }}</div>
                            <div class="font-semibold text-white text-sm">{{ $type }}</div>
                            <div class="text-xs text-gray-400">{{ $data['desc'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('favorite_type')
                <p class="mt-1 text-sm text-game-danger flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Active Hours -->
        <div>
            <label class="block text-sm font-semibold text-gray-200 mb-3">
                Aktif Oyun Saatlerin <span class="text-game-danger">*</span>
                <span class="text-xs text-gray-400 font-normal">(Birden fazla seçebilirsin)</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
                @php
                    $hours = [
                        'morning' => ['icon' => '🌅', 'label' => 'Sabah', 'time' => '06:00-12:00'],
                        'afternoon' => ['icon' => '☀️', 'label' => 'Öğleden Sonra', 'time' => '12:00-18:00'],
                        'evening' => ['icon' => '🌆', 'label' => 'Akşam', 'time' => '18:00-00:00'],
                        'night' => ['icon' => '🌙', 'label' => 'Gece', 'time' => '00:00-06:00']
                    ];
                    $oldHours = old('active_hours', $user->active_hours ?? []);
                @endphp
                @foreach($hours as $key => $data)
                    <label class="relative cursor-pointer touch-feedback">
                        <input type="checkbox" name="active_hours[]" value="{{ $key }}" 
                               {{ in_array($key, $oldHours) ? 'checked' : '' }}
                               class="peer sr-only">
                        <div class="btn-touch p-3 bg-gray-700/50 border-2 border-gray-600 rounded-xl transition-smooth peer-checked:border-game-success peer-checked:bg-game-success/20 hover:border-gray-500 hover:bg-gray-700/70">
                            <div class="flex items-center">
                                <div class="text-2xl mr-3">{{ $data['icon'] }}</div>
                                <div class="flex-1">
                                    <div class="font-semibold text-white text-sm">{{ $data['label'] }}</div>
                                    <div class="text-xs text-gray-400">{{ $data['time'] }}</div>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('active_hours')
                <p class="mt-1 text-sm text-game-danger flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

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
