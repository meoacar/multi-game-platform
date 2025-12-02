@props([
    'action' => '',
    'method' => 'GET',
])

<div class="bg-white rounded-xl shadow-lg p-6 mb-6" x-data="{ expanded: true }">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Filtreler
        </h3>
        <button @click="expanded = !expanded" class="text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': !expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    </div>
    
    <form action="{{ $action }}" method="{{ $method }}" x-show="expanded" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{ $slot }}
        </div>
        
        <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-200">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                🔍 Filtrele
            </button>
            <a href="{{ $action }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                🔄 Temizle
            </a>
        </div>
    </form>
</div>
