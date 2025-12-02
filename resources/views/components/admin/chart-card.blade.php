@props([
    'title',
    'subtitle' => null,
    'chartId',
    'height' => 'h-64',
])

<div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">{{ $title }}</h2>
        @if($subtitle)
            <span class="text-sm text-gray-500">{{ $subtitle }}</span>
        @endif
    </div>
    
    <div class="{{ $height }}">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
    
    {{ $slot }}
</div>
