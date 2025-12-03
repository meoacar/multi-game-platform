{{--
    Game Switcher Component
    
    Oyunlar arası geçiş yapmak için dropdown component
    
    Özellikler:
    - Aktif oyun gösterimi
    - Oyun listesi dropdown
    - Oyun değiştirme logic'i
    - Responsive tasarım
    
    Requirements: 7.1, 7.5
--}}

@php
    use App\Services\GameService;
    
    $gameService = app(GameService::class);
    $currentGame = $gameService->getCurrentGame();
    $activeGames = $gameService->getActiveGames();
@endphp

@if($activeGames->count() > 1)
<div class="relative" x-data="{ open: false }">
    <!-- Aktif Oyun Butonu -->
    <button 
        @click="open = !open" 
        class="flex items-center space-x-2 px-4 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition-all font-semibold group"
        aria-label="Oyun Değiştir"
        aria-expanded="false"
        x-bind:aria-expanded="open.toString()"
    >
        @if($currentGame)
            <!-- Mevcut Oyun Logosu -->
            @if($currentGame->logo)
                <img 
                    src="{{ asset($currentGame->logo) }}" 
                    alt="{{ $currentGame->name }}" 
                    class="w-6 h-6 rounded object-cover"
                >
            @else
                <div class="w-6 h-6 bg-gradient-to-br from-orange-500 to-red-600 rounded flex items-center justify-center text-xs">
                    {{ strtoupper(substr($currentGame->slug, 0, 2)) }}
                </div>
            @endif
            
            <!-- Oyun Adı -->
            <span class="hidden sm:inline">{{ $currentGame->name }}</span>
            
            <!-- Dropdown İkonu -->
            <svg 
                class="w-4 h-4 transition-transform duration-200" 
                :class="{ 'rotate-180': open }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        @else
            <!-- Oyun Seçilmemiş -->
            <span class="text-sm">🎮 Oyun Seç</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        @endif
    </button>
    
    <!-- Dropdown Menü -->
    <div 
        x-show="open" 
        @click.away="open = false" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-72 bg-gray-800/95 backdrop-blur-xl rounded-xl shadow-2xl border border-white/10 py-2 z-50"
        style="display: none;"
    >
        <!-- Başlık -->
        <div class="px-4 py-2 border-b border-white/10">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Oyun Seç</p>
        </div>
        
        <!-- Oyun Listesi -->
        <div class="py-2 max-h-96 overflow-y-auto">
            @foreach($activeGames as $game)
                <a 
                    href="{{ $gameService->getGameUrl($game) }}"
                    class="flex items-center space-x-3 px-4 py-3 hover:bg-white/5 transition-colors group {{ $currentGame && $currentGame->id === $game->id ? 'bg-white/5' : '' }}"
                    @click="open = false"
                >
                    <!-- Oyun Logosu -->
                    @if($game->logo)
                        <img 
                            src="{{ asset($game->logo) }}" 
                            alt="{{ $game->name }}" 
                            class="w-10 h-10 rounded-lg object-cover ring-2 ring-white/10 group-hover:ring-orange-500/50 transition-all"
                        >
                    @else
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center text-sm font-bold ring-2 ring-white/10 group-hover:ring-orange-500/50 transition-all">
                            {{ strtoupper(substr($game->slug, 0, 2)) }}
                        </div>
                    @endif
                    
                    <!-- Oyun Bilgileri -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <p class="text-sm font-semibold text-white group-hover:text-orange-400 transition-colors truncate">
                                {{ $game->name }}
                            </p>
                            
                            @if($currentGame && $currentGame->id === $game->id)
                                <!-- Aktif Oyun İşareti -->
                                <span class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            @endif
                        </div>
                        
                        @if($game->description)
                            <p class="text-xs text-gray-400 truncate">
                                {{ Str::limit($game->description, 50) }}
                            </p>
                        @endif
                    </div>
                    
                    <!-- Sağ Ok İkonu -->
                    <svg 
                        class="w-5 h-5 text-gray-600 group-hover:text-orange-400 transition-colors flex-shrink-0" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @endforeach
        </div>
        
        <!-- Alt Bilgi -->
        @if($activeGames->count() > 3)
            <div class="px-4 py-2 border-t border-white/10">
                <p class="text-xs text-gray-500 text-center">
                    {{ $activeGames->count() }} oyun mevcut
                </p>
            </div>
        @endif
    </div>
</div>
@endif

{{-- Eğer sadece 1 oyun varsa, switcher gösterme --}}
@if($activeGames->count() === 1 && $currentGame)
<div class="flex items-center space-x-2 px-4 py-2 text-gray-300">
    @if($currentGame->logo)
        <img 
            src="{{ asset($currentGame->logo) }}" 
            alt="{{ $currentGame->name }}" 
            class="w-6 h-6 rounded object-cover"
        >
    @else
        <div class="w-6 h-6 bg-gradient-to-br from-orange-500 to-red-600 rounded flex items-center justify-center text-xs">
            {{ strtoupper(substr($currentGame->slug, 0, 2)) }}
        </div>
    @endif
    <span class="hidden sm:inline font-semibold">{{ $currentGame->name }}</span>
</div>
@endif
