@props([
    'title',
    'value',
    'icon' => '📊',
    'color' => 'blue',
    'subtitle' => null,
    'trend' => null,
    'trendUp' => true,
])

@php
    $colorClasses = [
        'blue' => 'from-blue-500 to-blue-600',
        'green' => 'from-green-500 to-green-600',
        'orange' => 'from-orange-500 to-orange-600',
        'red' => 'from-red-500 to-red-600',
        'purple' => 'from-purple-500 to-purple-600',
        'yellow' => 'from-yellow-500 to-yellow-600',
    ];
    
    $gradientClass = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="bg-gradient-to-br {{ $gradientClass }} rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
    <div class="flex items-center justify-between mb-4">
        <div class="text-4xl">{{ $icon }}</div>
        @if($subtitle)
            <div class="bg-white/20 rounded-lg px-3 py-1 text-sm">{{ $subtitle }}</div>
        @endif
    </div>
    
    <p class="text-3xl font-bold mb-1">{{ $value }}</p>
    <p class="text-white/80 text-sm">{{ $title }}</p>
    
    @if($trend)
        <div class="mt-3 pt-3 border-t border-white/20 flex items-center justify-between text-xs">
            <span>{{ $trend }}</span>
            @if($trendUp)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
            @endif
        </div>
    @endif
    
    {{ $slot }}
</div>
