@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'lazy' => true,
    'aspectRatio' => null, // '16-9', '4-3', '1-1'
])

@php
    $aspectClass = $aspectRatio ? "aspect-{$aspectRatio}" : '';
    $lazyClass = $lazy ? 'lazy lazy-blur' : '';
    $placeholder = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect fill="%23f3f4f6" width="400" height="300"/%3E%3Ctext fill="%239ca3af" font-family="sans-serif" font-size="24" dy="10.5" font-weight="bold" x="50%25" y="50%25" text-anchor="middle"%3EYükleniyor...%3C/text%3E%3C/svg%3E';
@endphp

<div class="lazy-container {{ $aspectClass }} {{ $class }}">
    @if($lazy)
        <!-- Lazy loading ile -->
        <img 
            class="{{ $lazyClass }} optimized-img w-full h-full object-cover"
            data-src="{{ $src }}"
            src="{{ $placeholder }}"
            alt="{{ $alt }}"
            @if($width) width="{{ $width }}" @endif
            @if($height) height="{{ $height }}" @endif
            loading="lazy"
            decoding="async"
        />
    @else
        <!-- Normal yükleme -->
        <img 
            class="optimized-img w-full h-full object-cover"
            src="{{ $src }}"
            alt="{{ $alt }}"
            @if($width) width="{{ $width }}" @endif
            @if($height) height="{{ $height }}" @endif
            decoding="async"
        />
    @endif
</div>
