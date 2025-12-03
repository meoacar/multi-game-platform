@extends('onboarding.layout')

@section('content')
<div class="space-y-6">
    <!-- Başlık -->
    <div class="text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">
            Hangi Oyunu Oynuyorsun? 🎮
        </h2>
        <p class="text-gray-300">
            Ana oyununu seç ve topluluğa katıl
        </p>
    </div>

    <!-- Form -->
    <form action="{{ route('onboarding.step0') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Oyun Seçimi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($games as $game)
                <label class="relative cursor-pointer touch-feedback group">
                    <input type="radio" 
                           name="game_id" 
                           value="{{ $game->id }}" 
                           {{ old('game_id', $user->game_id) == $game->id ? 'checked' : '' }}
                           class="peer sr-only" 
                           required>
                    
                    <div class="btn-touch p-6 bg-gray-700/50 border-2 border-gray-600 rounded-xl transition-smooth peer-checked:border-game-primary peer-checked:bg-game-primary/20 hover:border-gray-500 hover:bg-gray-700/70 group-hover:scale-105">
                        <!-- Oyun Logosu -->
                        <div class="flex flex-col items-center text-center space-y-3">
                            @if($game->logo)
                                <img src="{{ $game->logo_url }}" 
                                     alt="{{ $game->name }}" 
                                     class="w-20 h-20 object-contain rounded-lg">
                            @else
                                <div class="w-20 h-20 bg-gradient-to-br from-game-primary to-game-blue rounded-lg flex items-center justify-center">
                                    <span class="text-white font-black text-3xl">
                                        {{ substr($game->name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Oyun Adı -->
                            <div>
                                <div class="font-bold text-white text-lg">
                                    {{ $game->name }}
                                </div>
                                @if($game->description)
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ Str::limit($game->description, 50) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Seçili İşareti -->
                            <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                <div class="bg-game-primary rounded-full p-1">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        @error('game_id')
            <p class="mt-1 text-sm text-game-danger flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $message }}
            </p>
        @enderror

        <!-- Bilgilendirme -->
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-200">
                    <p class="font-semibold mb-1">💡 Bilgi</p>
                    <p>Seçtiğin oyun ana oyunun olacak. Daha sonra profil ayarlarından değiştirebilirsin.</p>
                </div>
            </div>
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
