@props(['current', 'total'])

@php
    $percentage = ($current / $total) * 100;
@endphp

<div class="mb-8">
    <!-- Step Indicators -->
    <div class="flex justify-between items-center mb-4">
        @for ($i = 1; $i <= $total; $i++)
            <div class="flex flex-col items-center flex-1">
                <!-- Circle -->
                <div class="relative">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-bold text-sm sm:text-base transition-smooth hw-accelerate
                        @if($i < $current)
                            bg-gradient-to-br from-game-success to-green-600 text-white shadow-lg shadow-game-success/50
                        @elseif($i == $current)
                            bg-gradient-to-br from-game-primary to-game-blue text-white shadow-lg shadow-game-primary/50 ring-4 ring-game-primary/30 animate-pulse-slow
                        @else
                            bg-gray-700 text-gray-400
                        @endif
                    ">
                        @if($i < $current)
                            <!-- Checkmark -->
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $i }}
                        @endif
                    </div>
                </div>
                
                <!-- Label (Hidden on mobile) -->
                <div class="hidden sm:block mt-2 text-xs font-medium text-center
                    @if($i <= $current) text-white @else text-gray-500 @endif
                ">
                    @if($i == 1) PUBG Bilgileri
                    @elseif($i == 2) Tercihler
                    @elseif($i == 3) İlgi Alanları
                    @elseif($i == 4) Bildirimler
                    @endif
                </div>
            </div>

            <!-- Connector Line -->
            @if($i < $total)
                <div class="flex-1 h-1 mx-2 rounded-full transition-smooth
                    @if($i < $current)
                        bg-gradient-to-r from-game-success to-green-600
                    @else
                        bg-gray-700
                    @endif
                "></div>
            @endif
        @endfor
    </div>

    <!-- Progress Bar -->
    <div class="relative">
        <div class="progress-pubg">
            <div class="progress-pubg-bar hw-accelerate"
                 style="width: {{ $percentage }}%">
            </div>
        </div>
        
        <!-- Percentage Text -->
        <div class="mt-2 text-center">
            <span class="text-sm font-semibold text-white">
                Adım {{ $current }} / {{ $total }}
            </span>
            <span class="text-xs text-gray-400 ml-2">
                ({{ round($percentage) }}% tamamlandı)
            </span>
        </div>
    </div>
</div>
