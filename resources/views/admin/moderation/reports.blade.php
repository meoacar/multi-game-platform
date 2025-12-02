@extends('admin.layout')

@section('title', $pageTitle)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Başlık ve Filtreler -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $pageTitle }}</h1>
        
        <!-- Filtre Formu -->
        <form method="GET" action="{{ route('admin.moderation.reports') }}" class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Öncelik -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Öncelik</label>
                    <select name="priority" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tümü</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Yüksek</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Orta</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Düşük</option>
                    </select>
                </div>

                <!-- Tip -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">İçerik Tipi</label>
                    <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tümü</option>
                        <option value="App\Models\User" {{ request('type') === 'App\Models\User' ? 'selected' : '' }}>Kullanıcı</option>
                        <option value="App\Models\LfgPost" {{ request('type') === 'App\Models\LfgPost' ? 'selected' : '' }}>LFG İlanı</option>
                        <option value="App\Models\Clan" {{ request('type') === 'App\Models\Clan' ? 'selected' : '' }}>Klan</option>
                        <option value="App\Models\GuidePost" {{ request('type') === 'App\Models\GuidePost' ? 'selected' : '' }}>Rehber</option>
                        <option value="App\Models\CommunityPost" {{ request('type') === 'App\Models\CommunityPost' ? 'selected' : '' }}>Gönderi</option>
                        <option value="App\Models\Comment" {{ request('type') === 'App\Models\Comment' ? 'selected' : '' }}>Yorum</option>
                    </select>
                </div>

                <!-- Tarih Başlangıç -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Tarih Bitiş -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-4 flex justify-end space-x-2">
                <a href="{{ route('admin.moderation.reports') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Temizle
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- Raporlar Listesi -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Raporlar ({{ $reports->total() }})</h2>
            
            <!-- Toplu İşlemler -->
            <div class="flex items-center space-x-2">
                <button onclick="bulkProcess('approve')" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                    Seçilenleri Onayla
                </button>
                <button onclick="bulkProcess('reject')" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">
                    Seçilenleri Reddet
                </button>
            </div>
        </div>

        @if($reports->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($reports as $report)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start">
                            <!-- Checkbox -->
                            <input type="checkbox" name="report_ids[]" value="{{ $report->id }}" class="report-checkbox mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            
                            <!-- İçerik -->
                            <div class="ml-4 flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($report->priority === 'high') bg-red-100 text-red-800
                                        @elseif($report->priority === 'medium') bg-orange-100 text-orange-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($report->priority ?? 'medium') }}
                                    </span>
                                    <span class="text-sm text-gray-600">{{ class_basename($report->reportable_type) }}</span>
                                    <span class="text-sm text-gray-400">•</span>
                                    <span class="text-sm text-gray-600">{{ $report->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $report->reason }}</h3>
                                <p class="text-sm text-gray-600 mb-3">{{ $report->description }}</p>
                                
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span>Raporlayan: <strong>{{ $report->reporter->name ?? 'Anonim' }}</strong></span>
                                </div>
                            </div>

                            <!-- İşlemler -->
                            <div class="ml-4 flex flex-col space-y-2">
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Detay
                                </a>
                                <button onclick="processReport({{ $report->id }}, 'approve')" class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Onayla
                                </button>
                                <button onclick="processReport({{ $report->id }}, 'reject')" class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                    Reddet
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reports->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Rapor bulunamadı</h3>
                <p class="mt-1 text-sm text-gray-500">Filtrelere uygun bekleyen rapor bulunmuyor.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function processReport(reportId, action) {
    if (!confirm(`Bu raporu ${action === 'approve' ? 'onaylamak' : 'reddetmek'} istediğinize emin misiniz?`)) {
        return;
    }

    const reason = prompt('Sebep (opsiyonel):');
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/moderation/reports/${reportId}/process`;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    form.appendChild(actionInput);
    
    if (reason) {
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}

function bulkProcess(action) {
    const checkboxes = document.querySelectorAll('.report-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Lütfen en az bir rapor seçin');
        return;
    }
    
    if (!confirm(`${checkboxes.length} raporu ${action === 'approve' ? 'onaylamak' : 'reddetmek'} istediğinize emin misiniz?`)) {
        return;
    }
    
    const reason = prompt('Sebep (opsiyonel):');
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.moderation.reports.bulk-process') }}';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    form.appendChild(actionInput);
    
    checkboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'report_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });
    
    if (reason) {
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection
