{{--
    Game Theme Component
    
    Bu component, mevcut oyun bağlamına göre dinamik olarak tema CSS'ini yükler.
    DetectGame middleware tarafından paylaşılan $currentGame değişkenini kullanır.
    
    Kullanım:
    <x-game-theme />
--}}

@if(isset($currentGame) && $currentGame)
    {{-- Oyuna özel tema CSS'ini yükle --}}
    <link rel="stylesheet" href="{{ asset('css/themes/' . $currentGame->slug . '.css') }}">
    
    {{-- Oyun meta bilgilerini ekle --}}
    <meta name="game-context" content="{{ $currentGame->slug }}">
    <meta name="game-id" content="{{ $currentGame->id }}">
    
    {{-- Oyuna özel theme color --}}
    @php
        $themeColor = $currentGame->settings['theme_color'] ?? '#FF6B00';
    @endphp
    <meta name="theme-color" content="{{ $themeColor }}">
    <meta name="msapplication-TileColor" content="{{ $themeColor }}">
    
    {{-- Dinamik CSS Variables --}}
    <style>
        :root {
            --current-game-primary: {{ $currentGame->settings['theme_color'] ?? '#FF6B00' }};
            --current-game-secondary: {{ $currentGame->settings['secondary_color'] ?? '#FFB800' }};
        }
    </style>
    
    {{-- Oyun bilgilerini JavaScript'e aktar --}}
    <script>
        window.currentGame = {
            id: {{ $currentGame->id }},
            name: '{{ $currentGame->name }}',
            slug: '{{ $currentGame->slug }}',
            themeColor: '{{ $themeColor }}',
            settings: @json($currentGame->settings)
        };
    </script>
@else
    {{-- Ana domain için default tema --}}
    <meta name="theme-color" content="#3B82F6">
    <meta name="msapplication-TileColor" content="#3B82F6">
    
    <script>
        window.currentGame = null;
    </script>
@endif
