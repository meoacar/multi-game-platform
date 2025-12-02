@extends('admin.layout')

@section('title', 'Zamanlanmış Raporlar')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Zamanlanmış Raporlar</h1>
            <p class="text-gray-600 mt-1">Otomatik olarak oluşturulacak ve gönderilecek raporları yönetin</p>
        </div>
        <button 
            @click="showCreateModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Yeni Rapor Oluştur
        </button>
    </div>

    <!-- Bildirim Mesajları -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
        {{ session('error') }}
    </div>
    @endif

    <!-- Rapor Listesi -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rapor Adı
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tür
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Format
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sıklık
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Son Gönderim
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sonraki Gönderim
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Durum
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($scheduledReports as $report)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $report->name }}</div>
                            <div class="text-sm text-gray-500">{{ $report->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($report->type === 'users') bg-blue-100 text-blue-800
                                @elseif($report->type === 'content') bg-green-100 text-green-800
                                @elseif($report->type === 'platform') bg-yellow-100 text-yellow-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                {{ match($report->type) {
                                    'users' => 'Kullanıcı',
                                    'content' => 'İçerik',
                                    'platform' => 'Platform',
                                    'all' => 'Tümü',
                                    default => $report->type
                                } }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ strtoupper($report->format) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ match($report->frequency) {
                                'daily' => 'Günlük',
                                'weekly' => 'Haftalık',
                                'monthly' => 'Aylık',
                                default => $report->frequency
                            } }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $report->last_sent_at ? $report->last_sent_at->format('d.m.Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $report->next_send_at ? $report->next_send_at->format('d.m.Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($report->is_active)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                Aktif
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                Pasif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.analytics.scheduled-reports.toggle', $report->id) }}">
                                    @csrf
                                    <button 
                                        type="submit"
                                        class="text-gray-600 hover:text-gray-900"
                                        title="{{ $report->is_active ? 'Pasif Yap' : 'Aktif Yap' }}"
                                    >
                                        @if($report->is_active)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        @endif
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('admin.analytics.scheduled-reports.destroy', $report->id) }}" 
                                      onsubmit="return confirm('Bu raporu silmek istediğinizden emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        title="Sil"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2">Henüz zamanlanmış rapor bulunmuyor</p>
                            <button 
                                @click="showCreateModal = true"
                                class="mt-4 text-blue-600 hover:text-blue-700 font-medium"
                            >
                                İlk raporunuzu oluşturun
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($scheduledReports->hasPages())
    <div class="mt-4">
        {{ $scheduledReports->links() }}
    </div>
    @endif
</div>

<!-- Yeni Rapor Oluşturma Modal'ı -->
<div 
    x-data="{ showCreateModal: false }"
    x-show="showCreateModal"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCreateModal = false"></div>
        
        <div class="relative bg-white rounded-lg max-w-2xl w-full p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Yeni Zamanlanmış Rapor</h3>
            
            <form method="POST" action="{{ route('admin.analytics.schedule') }}">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rapor Adı</label>
                        <input 
                            type="text" 
                            name="name" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Örn: Haftalık Kullanıcı Raporu"
                        >
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rapor Türü</label>
                            <select 
                                name="type" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="users">Kullanıcı Analitiği</option>
                                <option value="content">İçerik Analitiği</option>
                                <option value="platform">Platform Analitiği</option>
                                <option value="all">Tüm Analitikler</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                            <select 
                                name="format" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="both">Her İkisi</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gönderim Sıklığı</label>
                        <select 
                            name="frequency" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="daily">Günlük</option>
                            <option value="weekly">Haftalık</option>
                            <option value="monthly">Aylık</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Adresi</label>
                        <input 
                            type="email" 
                            name="email" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="ornek@email.com"
                        >
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ek Alıcılar (Opsiyonel)
                            <span class="text-gray-500 text-xs">Her satıra bir email</span>
                        </label>
                        <textarea 
                            name="recipients[]" 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="email1@example.com&#10;email2@example.com"
                        ></textarea>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-3">
                    <button 
                        type="button"
                        @click="showCreateModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                    >
                        İptal
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
