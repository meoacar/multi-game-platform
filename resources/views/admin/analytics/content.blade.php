@extends('admin.layout')

@section('title', $pageTitle)

@section('content')
<div class="container mx-auto" x-data="contentAnalytics()">
    <!-- Başlık ve Filtreler -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
            <p class="text-gray-600 mt-1">İçerik oluşturma, etkileşim ve kalite analitiği</p>
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
                    <a href="{{ route('admin.analytics.export.pdf', ['type' => 'content', 'date_range' => $dateRange]) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <span class="text-red-500">📄</span> PDF
                    </a>
                    <a href="{{ route('admin.analytics.export.excel', ['type' => 'content', 'date_range' => $dateRange]) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <span class="text-green-500">📊</span> Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Özet Kartlar -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- LFG İlanları -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">LFG İlanları</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($analytics['popular_types']['lfg_posts']) }}</h3>
                </div>
                <div class="text-blue-500 text-4xl">🎮</div>
            </div>
        </div>

        <!-- Klanlar -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Klanlar</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($analytics['popular_types']['clans']) }}</h3>
                </div>
                <div class="text-purple-500 text-4xl">🛡️</div>
            </div>
        </div>

        <!-- Rehberler -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Rehberler</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($analytics['popular_types']['guides']) }}</h3>
                </div>
                <div class="text-green-500 text-4xl">📚</div>
            </div>
        </div>

        <!-- Topluluk Gönderileri -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Topluluk Gönderileri</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($analytics['popular_types']['community_posts']) }}</h3>
                </div>
                <div class="text-orange-500 text-4xl">💬</div>
            </div>
        </div>
    </div>

    <!-- İçerik Oluşturma Trendi -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="mb-4">
            <h5 class="text-lg font-semibold text-gray-900">İçerik Oluşturma Trendi</h5>
            <p class="text-sm text-gray-600">Günlük içerik oluşturma istatistikleri</p>
        </div>
        <div class="h-80">
            <canvas id="contentCreationTrendChart"></canvas>
        </div>
    </div>

    <!-- İçerik Türü Dağılımı ve Etkileşim -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- İçerik Türü Dağılımı -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h5 class="text-lg font-semibold text-gray-900">İçerik Türü Dağılımı</h5>
                <p class="text-sm text-gray-600">Toplam içerik sayılarına göre</p>
            </div>
            <div class="h-80 flex items-center justify-center">
                <canvas id="contentTypeDistributionChart"></canvas>
            </div>
        </div>

        <!-- Etkileşim Oranı -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h5 class="text-lg font-semibold text-gray-900">Etkileşim Metrikleri</h5>
                <p class="text-sm text-gray-600">İçerik başına ortalama etkileşim</p>
            </div>
            
            <!-- Engagement Rate -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-700 font-medium">Genel Etkileşim Oranı</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $analytics['engagement_rate']['rate'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-500 h-3 rounded-full" 
                         style="width: {{ min($analytics['engagement_rate']['rate'] * 10, 100) }}%">
                    </div>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mt-2">
                    <span>{{ number_format($analytics['engagement_rate']['total_content']) }} içerik</span>
                    <span>{{ number_format($analytics['engagement_rate']['total_comments']) }} yorum</span>
                </div>
            </div>

            <!-- İçerik Başına Ortalama -->
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">👁️</span>
                            <div>
                                <p class="text-sm text-gray-600">Ortalama Görüntülenme</p>
                                <p class="text-xl font-bold text-gray-900">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">💬</span>
                            <div>
                                <p class="text-sm text-gray-600">Ortalama Yorum</p>
                                <p class="text-xl font-bold text-gray-900">{{ $analytics['engagement_rate']['rate'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-purple-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">❤️</span>
                            <div>
                                <p class="text-sm text-gray-600">Ortalama Beğeni</p>
                                <p class="text-xl font-bold text-gray-900">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- İçerik Kalitesi ve En İyi İçerik Üreticileri -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- İçerik Kalitesi -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h5 class="text-lg font-semibold text-gray-900">En Kaliteli Rehberler</h5>
                <p class="text-sm text-gray-600">Etkileşim skoruna göre sıralı</p>
            </div>
            
            <div class="space-y-3">
                @forelse($analytics['quality_score']['top_guides'] as $guide)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 truncate">{{ $guide['title'] }}</p>
                        <p class="text-sm text-gray-600">Skor: {{ $guide['score'] }}</p>
                    </div>
                    <a href="{{ route('admin.guides.show', $guide['id']) }}" 
                       class="ml-3 text-blue-600 hover:text-blue-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>Henüz rehber bulunmuyor</p>
                </div>
                @endforelse
            </div>

            @if(count($analytics['quality_score']['top_guides']) > 0)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Ortalama Kalite Skoru</span>
                    <span class="text-lg font-bold text-blue-600">{{ $analytics['quality_score']['average_score'] }}</span>
                </div>
            </div>
            @endif
        </div>

        <!-- En İyi İçerik Üreticileri -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h5 class="text-lg font-semibold text-gray-900">En Aktif İçerik Üreticileri</h5>
                <p class="text-sm text-gray-600">Dönem içinde en çok içerik üreten kullanıcılar</p>
            </div>
            
            <div class="space-y-3">
                @forelse($analytics['top_creators'] as $index => $creator)
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                            {{ $index + 1 }}
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $creator['name'] }}</p>
                        <p class="text-sm text-gray-600">{{ $creator['total_content'] }} içerik</p>
                    </div>
                    <a href="{{ route('admin.users.show', $creator['id']) }}" 
                       class="text-blue-600 hover:text-blue-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>Henüz içerik üreticisi bulunmuyor</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Oyuna Göre İçerik Dağılımı -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="mb-4">
            <h5 class="text-lg font-semibold text-gray-900">Oyuna Göre İçerik Dağılımı</h5>
            <p class="text-sm text-gray-600">Hangi oyunlar için daha çok içerik üretiliyor</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- LFG İlanları -->
            <div>
                <h6 class="text-md font-medium text-gray-700 mb-3">LFG İlanları</h6>
                <div class="space-y-2">
                    @forelse($analytics['content_by_game']['lfg_posts'] as $item)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">{{ $item->game->name ?? 'Bilinmeyen' }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" 
                                     style="width: {{ ($item->count / $analytics['content_by_game']['lfg_posts']->max('count')) * 100 }}%">
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 w-12 text-right">{{ number_format($item->count) }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">Veri yok</p>
                    @endforelse
                </div>
            </div>

            <!-- Klanlar -->
            <div>
                <h6 class="text-md font-medium text-gray-700 mb-3">Klanlar</h6>
                <div class="space-y-2">
                    @forelse($analytics['content_by_game']['clans'] as $item)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">{{ $item->game->name ?? 'Bilinmeyen' }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" 
                                     style="width: {{ ($item->count / $analytics['content_by_game']['clans']->max('count')) * 100 }}%">
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 w-12 text-right">{{ number_format($item->count) }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">Veri yok</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function contentAnalytics() {
    return {
        dateRange: '{{ $dateRange }}',
        customStartDate: '{{ $customDates['start'] ?? '' }}',
        customEndDate: '{{ $customDates['end'] ?? '' }}',
        
        init() {
            this.initCharts();
        },
        
        loadData() {
            let url = '{{ route('admin.analytics.content') }}?date_range=' + this.dateRange;
            
            if (this.dateRange === 'custom') {
                url += '&start_date=' + this.customStartDate + '&end_date=' + this.customEndDate;
            }
            
            window.location.href = url;
        },
        
        initCharts() {
            // İçerik Oluşturma Trendi Grafiği
            const creationTrend = @json($analytics['creation_trend']);
            
            // Tüm tarihleri topla
            const allDates = new Set();
            Object.values(creationTrend).forEach(data => {
                Object.keys(data).forEach(date => allDates.add(date));
            });
            const dates = Array.from(allDates).sort();
            
            // Her içerik türü için veri hazırla
            const datasets = [
                {
                    label: 'LFG İlanları',
                    data: dates.map(date => creationTrend.lfg_posts[date] || 0),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Klanlar',
                    data: dates.map(date => creationTrend.clans[date] || 0),
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Rehberler',
                    data: dates.map(date => creationTrend.guides[date] || 0),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Topluluk',
                    data: dates.map(date => creationTrend.community_posts[date] || 0),
                    borderColor: 'rgb(249, 115, 22)',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ];
            
            new Chart(document.getElementById('contentCreationTrendChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
            
            // İçerik Türü Dağılımı (Pie Chart)
            const popularTypes = @json($analytics['popular_types']);
            
            new Chart(document.getElementById('contentTypeDistributionChart'), {
                type: 'pie',
                data: {
                    labels: ['LFG İlanları', 'Klanlar', 'Rehberler', 'Topluluk'],
                    datasets: [{
                        data: [
                            popularTypes.lfg_posts,
                            popularTypes.clans,
                            popularTypes.guides,
                            popularTypes.community_posts
                        ],
                        backgroundColor: [
                            'rgb(59, 130, 246)',
                            'rgb(147, 51, 234)',
                            'rgb(34, 197, 94)',
                            'rgb(249, 115, 22)'
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
</script>
@endpush
@endsection
