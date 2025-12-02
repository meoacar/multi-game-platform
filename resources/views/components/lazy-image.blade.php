@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'placeholder' => '/images/placeholder.jpg',
    'blur' => false,
])

<div class="lazy-container {{ $class }}" 
     @if($width) style="width: {{ $width }}px;" @endif
     @if($height) style="height: {{ $height }}px;" @endif>
    
    <!-- Placeholder (yüklenirken gösterilecek) -->
    <div class="lazy-placeholder"></div>
    
    <!-- Asıl resim (lazy load ile yüklenecek) -->
    <img 
        class="lazy optimized-img {{ $blur ? 'lazy-blur' : '' }}"
        data-src="{{ $src }}"
        src="{{ $placeholder }}"
        alt="{{ $alt }}"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        loading="lazy"
        decoding="async"
    />
</div>
