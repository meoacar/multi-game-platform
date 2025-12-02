@props([
    'actions' => [],
])

<div class="bg-white rounded-xl shadow-lg p-4 mb-6" x-data="{ selectedCount: 0 }" x-show="selectedCount > 0" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="text-sm font-medium text-gray-700">
                <span x-text="selectedCount"></span> öğe seçildi
            </span>
            
            <div class="flex items-center gap-2">
                {{ $slot }}
                
                @foreach($actions as $action)
                    <button type="button" 
                            @click="$dispatch('bulk-action', '{{ $action['name'] }}')"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                                   {{ $action['color'] === 'red' ? 'bg-red-100 text-red-700 hover:bg-red-200' : '' }}
                                   {{ $action['color'] === 'green' ? 'bg-green-100 text-green-700 hover:bg-green-200' : '' }}
                                   {{ $action['color'] === 'blue' ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' : '' }}
                                   {{ $action['color'] === 'gray' ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : '' }}">
                        {{ $action['icon'] ?? '' }} {{ $action['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
        
        <button @click="selectedCount = 0; $dispatch('deselect-all')" 
                class="text-sm text-gray-500 hover:text-gray-700">
            Seçimi Kaldır
        </button>
    </div>
</div>
