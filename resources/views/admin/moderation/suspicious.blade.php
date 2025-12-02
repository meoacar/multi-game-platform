@extends('admin.layout')

@section('title', $pageTitle)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
        <p class="text-gray-600 mt-2">Spam, bot ve şüpheli kullanıcı aktivitelerini inceleyin</p>
    </div>

    <!-- Şüpheli Aktiviteler -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Tespit Edilen Aktiviteler ({{ $activities->count() }})</h2>
        </div>

        @if($activities->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($activities as $activity)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($activity['severity'] === 'high') bg-red-100 text-red-800
                                        @elseif($activity['severity'] === 'medium') bg-orange-100 text-orange-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($activity['severity']) }}
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        @if($activity['type'] === 'spammer')
                                            Spam Kullanıcı
                                        @elseif($activity['type'] === 'highly_reported')
                                            Çok Raporlanan
                                        @elseif($activity['type'] === 'bad_content')
                                            Uygunsuz İçerik
                                        @else
                                            {{ $activity['type'] }}
                                        @endif
                                    </span>
                                </div>
                                
                                @if(isset($activity['user_name']))
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">
                                        Kullanıcı: {{ $activity['user_name'] }}
                                    </h3>
                                @elseif(isset($activity['content_type']))
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">
                                        İçerik: {{ ucfirst($activity['content_type']) }} #{{ $activity['content_id'] }}
                                    </h3>
                                @endif
                                
                                <p class="text-sm text-gray-600 mb-3">{{ $activity['description'] }}</p>
                            </div>

                            <!-- İşlemler -->
                            <div class="ml-4 flex flex-col space-y-2">
                                @if(isset($activity['user_id']))
                                    <a href="{{ route('admin.users.show', $activity['user_id']) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Kullanıcıyı Görüntüle
                                    </a>
                                    <a href="{{ route('admin.users.edit', $activity['user_id']) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Düzenle
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.ban', $activity['user_id']) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Bu kullanıcıyı banlamak istediğinize emin misiniz?')"
                                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                            Banla
                                        </button>
                                    </form>
                                @elseif(isset($activity['content_id']))
                                    <button onclick="viewContent('{{ $activity['content_type'] }}', {{ $activity['content_id'] }})"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        İçeriği Görüntüle
                                    </button>
                                    <button onclick="deleteContent('{{ $activity['content_type'] }}', {{ $activity['content_id'] }})"
                                            class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                        Sil
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Şüpheli aktivite bulunamadı</h3>
                <p class="mt-1 text-sm text-gray-500">Harika! Şu anda tespit edilen şüpheli aktivite yok.</p>
            </div>
        @endif
    </div>

    <!-- Bilgilendirme -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Otomatik Tespit Sistemi</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Bu sayfa otomatik olarak şüpheli aktiviteleri tespit eder:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li><strong>Spam Kullanıcılar:</strong> Son 1 saatte 10+ içerik paylaşan kullanıcılar</li>
                        <li><strong>Çok Raporlanan:</strong> 3 veya daha fazla bekleyen raporu olan kullanıcılar</li>
                        <li><strong>Uygunsuz İçerik:</strong> Küfür/hakaret içeren içerikler</li>
                    </ul>
                    <p class="mt-2">Bu liste her 5 dakikada bir otomatik olarak güncellenir.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewContent(type, id) {
    // İçerik tipine göre doğru sayfaya yönlendir
    let route = '';
    
    switch(type) {
        case 'comment':
            route = `/admin/comments/${id}`;
            break;
        case 'community_post':
            route = `/admin/community-posts/${id}`;
            break;
        case 'guide':
            route = `/admin/guides/${id}`;
            break;
        case 'lfg_post':
            route = `/admin/lfg-posts/${id}`;
            break;
        default:
            alert('Bilinmeyen içerik tipi');
            return;
    }
    
    window.location.href = route;
}

function deleteContent(type, id) {
    if (!confirm('Bu içeriği silmek istediğinize emin misiniz?')) {
        return;
    }
    
    let route = '';
    
    switch(type) {
        case 'comment':
            route = `/admin/comments/${id}`;
            break;
        case 'community_post':
            route = `/admin/community-posts/${id}`;
            break;
        case 'guide':
            route = `/admin/guides/${id}`;
            break;
        case 'lfg_post':
            route = `/admin/lfg-posts/${id}`;
            break;
        default:
            alert('Bilinmeyen içerik tipi');
            return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection
