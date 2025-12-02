@extends('admin.layout')

@section('title', $pageTitle)

@section('content')
<div class="container mx-auto" x-data="platformAnalytics()">
    <!-- Başlık ve Filtreler -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
            <p class="text-gray-600 mt-1">Sayfa görüntülenme, trafik kaynakları ve cihaz analitiği</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <!-- Tarih Aralığı Seçimi -->
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    x-model="dateRange" @change="loadData()">
                <option value="week" {{ $dateRange === 'week' ? 'selected' : '' }}>Bu Hafta</option>
                <option value="month" {{ $dateRange === 'month' ? 'selected' : '' }}>Bu Ay</option>
                <option value="quarter" {{ $dateRange === 'quarter' ? 'selected' : '' }}>Bu Çeyrek</option>
                <option value="year" {{ $dateRange === 'year' ? 'selected' : '' }}>Bu Yıl</option>
                <option value="custom" {{ $dateRange === 'custom' ? 'selected' : '' }}>Özel Tarih</option>
            </select>
            
            <!-- Özel Tarih Aralığı -->
            <template x-if="dateRange === 'custom'">
                <div class="flex gap-2">
                    <input type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           x-model="customStartDate" @change="loadData()">
                    <input type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           x-model="customEndDate" @change="loadData()">
                </div>
            </template>
            
            <!-- Export Butonları -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" 
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export
                </button>
                <div x-show="open" @click.away="open = false" 
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-10">
                    <a href="{{ route('admin.analytics.export.pdf', ['type' => 'platform', 'date_range' => $dateRange]) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <span class="text-red-500">📄</span> PDF
                    </a>
                    <a href="{{ route('admin.analytics.export.excel', ['type' => 'platform', 'date_range' => $dateRange]) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <span class="text-green-500">📊</span> Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Analytics Entegrasyon Uyarısı -->
    @if(isset($analytics['page_views']['note']))
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Google Analytics Entegrasyonu Gerekli</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Platform analitiği verileri için Google Analytics entegrasyonu yapılmalıdır. Şu anda sadece cihaz dağılımı verileri mevcut.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Özet Kartlar -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Sayfa Görüntülenme -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Toplam Görüntülenme</p>
                    <h3 class="text-3xl font-bold text-gray-900">
                        {{ $analytics['page_views']['total'] > 0 ? number_format($analytics['page_views']['total']) : '-' }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $analytics['page_views']['note'] ?? '' }}</p>
                </div>
                <div class="text-blue-500 text-4xl">👁️</div>
            </div>
        </div>

        <!-- Benzersiz Ziyaretçi -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Benzersiz Ziyaretçi</p>
                    <h3 class="text-3xl font-bold text-gray-900">
                        {{ $analytics['page_views']['unique'] > 0 ? number_format($analytics['page_views']['unique']) : '-' }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $analytics['page_views']['note'] ?? '' }}</p>
                </div>
                <div class="text-green-500 text-4xl">👤</div>
            </div>
        </div>

        <!-- Bounce Rate -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Hemen Çıkma Oranı</p>
                    <h3 class="text-3xl font-bold text-gray-900">
                        {{ $analytics['bounce_rate']['rate'] > 0 ? $analytics['bounce_rate']['rate'] . '%' : '-' }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $analytics['bounce_rate']['note'] ?? '' }}</p>
                </div>
                <div class="text-orange-500 text-4xl">📉</div>
            </div>
        </div>

        <!-- Ortalama Oturum Süresi -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Ort. Oturum Süresi</p>
                    <h3 class="text-3xl font-bold text-gray-900">
                        {{ $analytics['session_duration']['duration'] > 0 ? gmdate('i:s', $analytics['session_duration']['duration']) : '-' }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $analytics['session_duration']['note'] ?? 'dakika:saniye' }}</p>
                </div>
                <div class="text-purple-500 text-4xl">⏱️</div>
            </div>
        </div>
    </div>

    <!-- Cihaz Dağılımı ve Trafik Kaynakları -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Cihaz Dağılımı -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h5 class="text-lg font-semibold text-gray-900">Cihaz Dağılımı</h5>
                <p class="text-sm text-gray-600">Kullanıcıların tercih ettiği cihazlar</p>
            </div>
            
            @php
                $deviceData = array_filter($analytics['device_distribution'], function($key) {
                    return $key !== 'note';
                }, ARRAY_FILTER_USE_KEY);
            @endphp
            
            @if(count($deviceData) > 0)
            <div class="h-80 flex items-center justify-center">
                <canvas id="deviceDistributionChart"></canvas>
            </div>
            
            <div class="mt-4 space-y-2">
                @foreach($deviceData as $device => $count)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">
                            @if($device === 'mobile')
                                📱
                            @elseif($device === 'tablet')
                                📲
                            @else
                                💻
                            @endif
                        </span>
                        <span class="font-medium text-gray-900">{{ ucfirst($device) }}</span>
                    </div>
                    <span class="text-lg font-bold text-gray-900">{{ is_numeric($count) ? number_format($count) : 0 }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <p>Henüz cihaz verisi bulunmuyor</p>
            </div>
            @endif
        </div>

        <!-- Trafik Kaynakları -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h5 class="text-lg font-semibold text-gray-900">Trafik Kaynakları</h5>
                <p class="text-sm text-gray-600">Ziyaretçiler nereden geliyor</p>
            </div>
            
            @if(isset($analytics['traffic_sources']['note']))
            <div class="text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="font-medium text-gray-900 mb-2">Google Analytics Gerekli</p>
                <p class="text-sm">{{ $analytics['traffic_sources']['note'] }}</p>
            </div>
            @else
            <div class="space-y-3">
                @forelse($analytics['traffic_sources']['sources'] as $source)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                            {{ substr($source['name'], 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $source['name'] }}</p>
                            <p class="text-sm text-gray-600">{{ $source['percentage'] }}%</p>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-gray-900">{{ number_format($source['visits']) }}</span>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>Veri yok</p>
                </div>
                @endforelse
            </div>
            @endif
        </div>
    </div>

    <!-- Popüler Sayfalar -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="mb-4">
            <h5 class="text-lg font-semibold text-gray-900">Popüler Sayfalar</h5>
            <p class="text-sm text-gray-600">En çok ziyaret edilen sayfalar</p>
        </div>
        
        @if(isset($analytics['popular_pages']['note']))
        <div class="text-center py-12 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="font-medium text-gray-900 mb-2">Google Analytics Gerekli</p>
            <p class="text-sm">{{ $analytics['popular_pages']['note'] }}</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sayfa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Görüntülenme</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Benzersiz</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ort. Süre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bounce Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($analytics['popular_pages']['pages'] as $index => $page)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $page['path'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($page['views']) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ number_format($page['unique']) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $page['avg_time'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $page['bounce_rate'] }}%</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Veri yok
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Platform Performans Metrikleri -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Sayfa Yükleme Hızı -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <span class="text-2xl">⚡</span>
                </div>
                <div>
                    <h6 class="text-sm font-medium text-gray-600">Sayfa Yükleme Hızı</h6>
                    <p class="text-2xl font-bold text-gray-900">-</p>
                </div>
            </div>
            <p class="text-xs text-gray-500">Google Analytics gerekli</p>
        </div>

        <!-- Sunucu Yanıt Süresi -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <span class="text-2xl">🖥️</span>
                </div>
                <div>
                    <h6 class="text-sm font-medium text-gray-600">Sunucu Yanıt Süresi</h6>
                    <p class="text-2xl font-bold text-gray-900">-</p>
                </div>
            </div>
            <p class="text-xs text-gray-500">Google Analytics gerekli</p>
        </div>

        <!-- Etkileşim Oranı -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <span class="text-2xl">🎯</span>
                </div>
                <div>
                    <h6 class="text-sm font-medium text-gray-600">Etkileşim Oranı</h6>
                    <p class="text-2xl font-bold text-gray-900">-</p>
                </div>
            </div>
            <p class="text-xs text-gray-500">Google Analytics gerekli</p>
        </div>
    </div>

    <!-- Google Analytics Entegrasyon Bilgisi -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Google Analytics Entegrasyonu</h3>
                <p class="text-blue-800 mb-4">
                    Platform analitiği verilerinin tam olarak görüntülenebilmesi için Google Analytics entegrasyonu yapılmalıdır. 
                    Entegrasyon sonrası aşağıdaki veriler otomatik olarak toplanacaktır:
                </p>
                <ul class="list-disc list-inside text-blue-800 space-y-1 mb-4">
                    <li>Sayfa görüntülenme ve benzersiz ziyaretçi sayıları</li>
                    <li>Bounce rate (hemen çıkma oranı)</li>
                    <li>Ortalama oturum süresi</li>
                    <li>Trafik kaynakları (organik, sosyal medya, direkt vb.)</li>
                    <li>Popüler sayfalar ve kullanıcı akışı</li>
                    <li>Sayfa yükleme hızı ve performans metrikleri</li>
                </ul>
                <a href="{{ route('admin.settings.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Ayarlara Git
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function platformAnalytics() {
    return {
        dateRange: '{{ $dateRange }}',
        customStartDate: '{{ $customDates['start'] ?? '' }}',
        customEndDate: '{{ $customDates['end'] ?? '' }}',
        
        init() {
            this.initCharts();
        },
        
        loadData() {
            let url = '{{ route('admin.analytics.platform') }}?date_range=' + this.dateRange;
            
            if (this.dateRange === 'custom') {
                url += '&start_date=' + this.customStartDate + '&end_date=' + this.customEndDate;
            }
            
            window.location.href = url;
        },
        
        initCharts() {
            const deviceDistribution = @json($analytics['device_distribution']);
            
            // 'note' key'ini filtrele
            const filteredData = Object.keys(deviceDistribution)
                .filter(key => key !== 'note')
                .reduce((obj, key) => {
                    obj[key] = deviceDistribution[key];
                    return obj;
                }, {});
            
            // Sadece veri varsa grafik oluştur
            if (Object.keys(filteredData).length > 0) {
                const labels = Object.keys(filteredData).map(key => {
                    return key.charAt(0).toUpperCase() + key.slice(1);
                });
                const data = Object.values(filteredData);
                
                new Chart(document.getElementById('deviceDistributionChart'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                'rgb(59, 130, 246)',
                                'rgb(147, 51, 234)',
                                'rgb(34, 197, 94)',
                                'rgb(249, 115, 22)',
                                'rgb(239, 68, 68)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }
    }
}
</script>
@endpush
@endsection
