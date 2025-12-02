@props([
    'headers' => [],
    'rows' => [],
    'actions' => true,
    'selectable' => false,
])

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @if($selectable)
                        <th scope="col" class="px-6 py-3 text-left">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   @click="selectAll = !selectAll; $dispatch('select-all', selectAll)">
                        </th>
                    @endif
                    
                    @foreach($headers as $header)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                    
                    @if($actions)
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
