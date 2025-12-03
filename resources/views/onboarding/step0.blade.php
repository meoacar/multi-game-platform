@extends('onboarding.layout')

@section('content')
<div class="space-y-8">
    <!-- Başlık - Animated -->
    <div class="text-center animate-fade-in">
        <div class="inline-block mb-4">
            <div class="text-6xl animate-bounce-slow">🎮</div>
        </div>
        <h2 class="text-3xl sm:text-4xl font-black text-white mb-3 bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
            Hangi Oyunu Oynuyorsun?
        </h2>
        <p class="text-lg text-gray-300">
            Ana oyununu seç ve maceraya başla! 🚀
        </p>
    </div>

    <!-- Form -->
    <form action="{{ route('onboarding.step0') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Oyun Seçimi - Grid Layout -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($games as $index => $game)
                <label class="relative cursor-pointer group" style="animation: slideInUp 0.5s ease-out {{ $index * 0.1 }}s both;">
                    <input type="radio" 
                           name="game_id" 
                           value="{{ $game->id }}" 
                           {{ old('game_id', $user->game_id) == $game->id ? 'checked' : '' }}
                           class="peer sr-only" 
                           required>
                    
                    <!-- Oyun Kartı -->
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-800 to-gray-900 border-2 border-gray-700 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/20 peer-checked:border-purple-500 peer-checked:shadow-2xl peer-checked:shadow-purple-500/50 peer-checked:scale-105">
                        
                        <!-- Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/0 via-blue-500/0 to-pink-500/0 peer-checked:from-purple-500/20 peer-checked:via-blue-500/20 peer-checked:to-pink-500/20 transition-all duration-500"></div>
                        
                        <!-- İçerik -->
                        <div class="relative p-6 flex flex-col items-center text-center space-y-4">
                            
                            <!-- Oyun Logosu/İkonu -->
                            <div class="relative">
                                @if($game->logo)
                                    <div class="w-24 h-24 rounded-xl overflow-hidden ring-4 ring-gray-700 group-hover:ring-purple-500 transition-all duration-300 bg-white/10 backdrop-blur-sm">
                                        <img src="{{ asset($game->logo) }}" 
                                             alt="{{ $game->name }}" 
                                             class="w-full h-full object-contain p-2 transform group-hover:scale-110 transition-transform duration-500"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center\'><span class=\'text-white font-black text-4xl\'>{{ substr($game->name, 0, 1) }}</span></div>';">
                                    </div>
                                @else
                                    @php
                                        $gameIcons = [
                                            'lol' => ['emoji' => '⚔️', 'gradient' => 'from-purple-500 to-indigo-500'],
                                        ];
                                        $icon = $gameIcons[$game->slug] ?? ['emoji' => '🎮', 'gradient' => 'from-purple-500 to-pink-500'];
                                    @endphp
                                    <div class="w-24 h-24 bg-gradient-to-br {{ $icon['gradient'] }} rounded-xl flex items-center justify-center ring-4 ring-gray-700 group-hover:ring-purple-500 transition-all duration-300 shadow-lg group-hover:shadow-2xl">
                                        <span class="text-6xl transform group-hover:scale-110 transition-transform duration-300">
                                            {{ $icon['emoji'] }}
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- Seçili Badge -->
                                <div class="absolute -top-2 -right-2 opacity-0 peer-checked:opacity-100 transition-all duration-300 transform scale-0 peer-checked:scale-100">
                                    <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-full p-2 shadow-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Oyun Bilgileri -->
                            <div class="space-y-2">
                                <h3 class="font-black text-xl text-white group-hover:text-purple-400 transition-colors">
                                    {{ $game->name }}
                                </h3>
                                @if($game->description)
                                    <p class="text-sm text-gray-400 line-clamp-2">
                                        {{ $game->description }}
                                    </p>
                                @endif
                            </div>

                            <!-- Hover Effect - Play Icon -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                <div class="bg-purple-500/20 backdrop-blur-sm rounded-full p-4">
                                    <svg class="w-8 h-8 text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Gradient -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 via-blue-500 to-pink-500 transform scale-x-0 peer-checked:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </label>
            @endforeach
        </div>

        @error('game_id')
            <div class="bg-red-500/10 border-2 border-red-500 rounded-xl p-4 flex items-center animate-shake">
                <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="text-red-200 font-semibold">{{ $message }}</span>
            </div>
        @enderror

        <!-- Bilgilendirme -->
        <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/30 rounded-xl p-5 backdrop-blur-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-blue-300 mb-1">💡 İpucu</p>
                    <p class="text-sm text-blue-200/80">
                        Seçtiğin oyun ana oyunun olacak. Merak etme, daha sonra profil ayarlarından istediğin zaman değiştirebilirsin!
                    </p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="relative w-full bg-gradient-to-r from-purple-600 via-blue-600 to-pink-600 hover:from-purple-500 hover:via-blue-500 hover:to-pink-500 text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 overflow-hidden group">
            
            <!-- Animated Background -->
            <div class="absolute inset-0 bg-gradient-to-r from-pink-600 via-purple-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            
            <!-- Button Content -->
            <span class="relative flex items-center justify-center text-lg">
                <span>Hadi Başlayalım!</span>
                <svg class="w-6 h-6 ml-3 transform group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </span>
        </button>
    </form>
</div>

<!-- Custom Animations -->
<style>
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
    
    .animate-bounce-slow {
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-20px);
        }
    }
</style>
@endsection
