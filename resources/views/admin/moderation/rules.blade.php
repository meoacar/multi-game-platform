@extends('admin.layout')

@section('title', $pageTitle)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
        <p class="text-gray-600 mt-2">Otomatik moderasyon kurallarını yönetin</p>
    </div>

    <!-- Kurallar -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Aktif Kurallar</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($rules as $rule)
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-medium text-gray-900">{{ $rule['name'] }}</h3>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($rule['action'] === 'reject') bg-red-100 text-red-800
                                    @elseif($rule['action'] === 'ban') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($rule['action']) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">{{ $rule['description'] }}</p>
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span>Tip: <strong>{{ $rule['type'] }}</strong></span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       class="sr-only peer" 
                                       {{ $rule['is_active'] ? 'checked' : '' }}
                                       onchange="toggleRule({{ $rule['id'] }}, this.checked)">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Küfür Kelime Listesi -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Küfür/Hakaret Kelime Listesi</h2>
        </div>
        <div class="p-6">
            <!-- Kelime Ekleme Formu -->
            <form method="POST" action="{{ route('admin.moderation.rules.bad-words.add') }}" class="mb-6">
                @csrf
                <div class="flex space-x-2">
                    <input type="text" 
                           name="word" 
                           placeholder="Kelime ekle..." 
                           required
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        Ekle
                    </button>
                </div>
            </form>

            <!-- Kelime Listesi -->
            <div class="space-y-2">
                @foreach($badWords as $word)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-900">{{ $word }}</span>
                        <form method="POST" action="{{ route('admin.moderation.rules.bad-words.remove') }}" class="inline">
                            @csrf
                            <input type="hidden" name="word" value="{{ $word }}">
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                Kaldır
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Dikkat</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Bu liste örnek amaçlıdır. Gerçek implementasyonda daha kapsamlı bir liste kullanılmalı ve veritabanında saklanmalıdır.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- İçerik Test Aracı -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">İçerik Test Aracı</h2>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">Bir içeriği otomatik moderasyon kurallarına göre test edin</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test İçeriği</label>
                    <textarea id="test-content" 
                              rows="4" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Test etmek istediğiniz içeriği buraya yazın..."></textarea>
                </div>
                
                <button onclick="testContent()" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                    Test Et
                </button>
                
                <div id="test-result" class="hidden mt-4 p-4 rounded-lg"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleRule(ruleId, isActive) {
    fetch(`/admin/moderation/rules/${ruleId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ is_active: isActive })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Kural durumu güncellendi');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu');
    });
}

function testContent() {
    const content = document.getElementById('test-content').value;
    const resultDiv = document.getElementById('test-result');
    
    if (!content.trim()) {
        alert('Lütfen test edilecek içeriği girin');
        return;
    }
    
    fetch('{{ route('admin.moderation.test-content') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ content: content })
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.classList.remove('hidden');
        
        if (data.passed) {
            resultDiv.className = 'mt-4 p-4 rounded-lg bg-green-50 border border-green-200';
            resultDiv.innerHTML = `
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">İçerik Uygun</h3>
                        <p class="mt-1 text-sm text-green-700">Bu içerik moderasyon kurallarını geçti.</p>
                        <p class="mt-1 text-sm text-green-700">Önerilen İşlem: <strong>${data.action}</strong></p>
                    </div>
                </div>
            `;
        } else {
            resultDiv.className = 'mt-4 p-4 rounded-lg bg-red-50 border border-red-200';
            let issuesHtml = data.issues.map(issue => `
                <li class="text-sm text-red-700">
                    <strong>${issue.type}</strong> (${issue.severity}): ${issue.message}
                </li>
            `).join('');
            
            resultDiv.innerHTML = `
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">İçerik Uygun Değil</h3>
                        <p class="mt-1 text-sm text-red-700">Bu içerik aşağıdaki sorunları içeriyor:</p>
                        <ul class="mt-2 list-disc list-inside">
                            ${issuesHtml}
                        </ul>
                        <p class="mt-2 text-sm text-red-700">Önerilen İşlem: <strong>${data.action}</strong></p>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu');
    });
}
</script>
@endpush
@endsection
