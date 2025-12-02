@extends('admin.layout')

@section('title', 'Şablon Önizleme')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">👁️ Şablon Önizleme</h1>
                <p class="text-gray-600 mt-1">{{ $template->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.notifications.templates.edit', $template->id) }}" 
                   class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition">
                    ✏️ Düzenle
                </a>
                <a href="{{ route('admin.notifications.templates') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    ← Geri Dön
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Taraf: Önizleme -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Email Önizleme -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Email Header -->
                <div class="border-b border-gray-200 p-6 bg-gray-50">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            {{ substr(config('app.name'), 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ config('app.name') }}</div>
                            <div class="text-sm text-gray-500">noreply@{{ parse_url(config('app.url'), PHP_URL_HOST) }}</div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-start">
                            <span class="text-sm text-gray-600 w-16">Kime:</span>
                            <span class="text-sm text-gray-900">{{ $preview['sample_data']['email'] ?? 'user@example.com' }}</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-sm text-gray-600 w-16">Konu:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $preview['subject'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Email Body -->
                <div class="p-8">
                    <div class="prose prose-sm max-w-none">
                        {!! nl2br(e($preview['body'])) !!}
                    </div>
                </div>

                <!-- Email Footer -->
                <div class="border-t border-gray-200 p-6 bg-gray-50">
                    <div class="text-xs text-gray-500 text-center space-y-1">
                        <p>Bu email {{ config('app.name') }} tarafından gönderilmiştir.</p>
                        <p>© {{ date('Y') }} {{ config('app.name') }}. Tüm hakları saklıdır.</p>
                    </div>
                </div>
            </div>

            <!-- HTML Kaynak Kodu -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">💻 HTML Kaynak Kodu</h3>
                    <button 
                        onclick="copyToClipboard()"
                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                        📋 Kopyala
                    </button>
                </div>
                
                <pre id="htmlSource" class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-xs"><code>{{ $preview['body'] }}</code></pre>
            </div>
        </div>

        <!-- Sağ Taraf: Bilgiler ve Ayarlar -->
        <div class="space-y-6">
            <!-- Şablon Bilgileri -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">ℹ️ Şablon Bilgileri</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Şablon Adı:</span>
                        <span class="font-medium text-gray-900">{{ $template->name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Slug:</span>
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $template->slug }}</code>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Durum:</span>
                        @if($template->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✅ Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                ⏸️ Pasif
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Oluşturulma:</span>
                        <span class="font-medium text-gray-900">{{ $template->created_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-gray-600">Son Güncelleme:</span>
                        <span class="font-medium text-gray-900">{{ $template->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Kullanılan Değişkenler -->
            @if($template->variables && count($template->variables) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">🔧 Kullanılan Değişkenler</h3>
                
                <div class="space-y-2">
                    @foreach($template->variables as $variable)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <code class="text-sm text-blue-600">{{ '{{' . $variable . '}}' }}</code>
                            <span class="text-xs text-gray-500">{{ $preview['sample_data'][$variable] ?? 'N/A' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Örnek Veri -->
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                <h3 class="font-semibold text-blue-900 mb-3">📊 Örnek Veri</h3>
                
                <div class="space-y-2 text-sm">
                    @foreach($preview['sample_data'] as $key => $value)
                        <div class="flex items-start justify-between">
                            <code class="text-xs text-blue-700">{{ $key }}:</code>
                            <span class="text-xs text-blue-900 font-medium">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
                
                <p class="text-xs text-blue-700 mt-3">
                    💡 Bu veriler sadece önizleme içindir. Gerçek gönderimde kullanıcı verileri kullanılacaktır.
                </p>
            </div>

            <!-- Test Email Gönder -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">📧 Test Email Gönder</h3>
                
                <form id="testEmailForm" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Adresi</label>
                        <input 
                            type="email" 
                            id="testEmail"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="test@example.com"
                            required>
                    </div>
                    
                    <button 
                        type="submit"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        📨 Test Email Gönder
                    </button>
                </form>
                
                <div id="testEmailResult" class="mt-3 hidden"></div>
            </div>

            <!-- İşlemler -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">⚙️ İşlemler</h3>
                
                <div class="space-y-2">
                    <a href="{{ route('admin.notifications.templates.edit', $template->id) }}" 
                       class="w-full px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition text-sm flex items-center justify-center space-x-2">
                        <span>✏️</span>
                        <span>Şablonu Düzenle</span>
                    </a>
                    
                    <form action="{{ route('admin.notifications.templates.duplicate', $template->id) }}" 
                          method="POST"
                          onsubmit="return confirm('Bu şablonu kopyalamak istediğinize emin misiniz?')">
                        @csrf
                        <button 
                            type="submit"
                            class="w-full px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition text-sm flex items-center justify-center space-x-2">
                            <span>📋</span>
                            <span>Şablonu Kopyala</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard() {
    const source = document.getElementById('htmlSource').textContent;
    navigator.clipboard.writeText(source).then(() => {
        alert('HTML kaynak kodu kopyalandı! 📋');
    }).catch(err => {
        console.error('Kopyalama hatası:', err);
    });
}

// Test email gönderme
document.getElementById('testEmailForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('testEmail').value;
    const resultDiv = document.getElementById('testEmailResult');
    
    try {
        const response = await fetch('{{ route("admin.notifications.send-test-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email: email,
                subject: @json($preview['subject']),
                body: @json($preview['body'])
            })
        });
        
        const data = await response.json();
        
        resultDiv.classList.remove('hidden');
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
                    ✅ ${data.message}
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
                    ❌ ${data.message}
                </div>
            `;
        }
    } catch (error) {
        resultDiv.classList.remove('hidden');
        resultDiv.innerHTML = `
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
                ❌ Bir hata oluştu: ${error.message}
            </div>
        `;
    }
});
</script>
@endpush
@endsection
