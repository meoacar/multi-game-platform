@extends('admin.layout')

@section('title', $pageTitle)

@section('content')
<div class="container mx-auto" x-data="userAnalytics()">
    <!-- Başlık ve Filtreler -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
            <p class="text-gray-600 mt-1">Kullanıcı kayıt, aktivite ve segmentasyon analitiği</p>
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
                    <a href="{{ route('admin.analytics.export.pdf', ['type' => 'users', 'date_range' => $dateRange]) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <span class="text-red-500">📄</span> PDF
                    </a>
                    <a href="{{ route('admin.analytics.export.excel', ['type' => 'users', 'date_range' => $dateRange]) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <span class="text-green-500">📊</span> Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Özet Kartlar -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Toplam Kullanıcı -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Toplam Kullanıcı</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($analytics['registration_trend']['total']) }}</h3>
                    @if($analytics['registration_trend']['change_percentage'] != 0)
                        <p class="text-sm mt-2 {{ $analytics['registration_trend']['change_percentage'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="{{ $analytics['registration_trend']['change_percentage'] > 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"></path>
                            </svg>
                            {{ abs($analytics['registration_trend']['change_percentage']) }}%
                        </p>
                    @endif
                </div>
                <div class="text-blue-500 text-4xl">👥</div>
            </div>
        </div>

        <!-- Aktif Kullanıcı -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Aktif Kullanıcı (7 Gün)</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($analytics['active_users_trend']['total']) }}</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        Günlük Ort: {{ number_format($analytics['active_users_trend']['daily_average']) }}
                    </p>
                </div>
                <div class="text-green-500 text-4xl">✅</div>
            </div>
        </div>

        <!-- Churn Rate -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Kayıp Oranı (Churn)</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $analytics['churn_rate']['rate'] }}%</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        {{ number_format($analytics['churn_rate']['churned_users']) }} kullanıcı
                    </p>
                </div>
                <div class="text-red-500 text-4xl">❌</div>
            </div>
        </div>

        <!-- Retention Rate -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm mb-1">Elde Tutma (Retention)</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $analytics['retention_rate']['rate'] }}%</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        {{ number_format($analytics['retention_rate']['retained_users']) }}/{{ number_format($analytics['retention_rate']['new_users']) }}
                    </p>
                </div>
                <div class="text-indigo-500 text-4xl">🛡️</div>
            </div>
        </div>
    </div>

    <!-- Grafikler -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Kayıt Trendi Grafiği -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h5 class="text-lg font-semibold text-gray-900">Kayıt Trendi</h5>
                <p class="text-sm text-gray-600">Günlük yeni kullanıcı kayıtları</p>
            </div>
            <div class="h-80">
                <canvas id="registrationTrendChart"></canvas>
            </div>
        </div>

        <!-- Kullanıcı Segmentasyonu -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h5 class="text-lg font-semibold text-gray-900">Kullanıcı Segmentasyonu</h5>
                <p class="text-sm text-gray-600">Aktivite durumuna göre</p>
            </div>
            <div class="h-80 flex items-center justify-center">
                <canvas id="segmentationChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Aktif Kullanıcı Trendi -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="mb-4">
            <h5 class="text-lg font-semibold text-gray-900">Aktif Kullanıcı Trendi</h5>
            <p class="text-sm text-gray-600">Son 7 gün içinde aktivite gösteren kullanıcılar</p>
        </div>
        <div class="h-64">
            <canvas id="activeUsersTrendChart"></canvas>
        </div>
    </div>

    <!-- Detaylı İstatistikler -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Demografik Bilgiler -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h5 class="text-lg font-semibold text-gray-900 mb-4">Demografik Dağılım</h5>
            
            <!-- Şehir Dağılımı -->
            <h6 class="text-md font-medium text-gray-700 mb-3">En Popüler Şehirler</h6>
            <div class="space-y-2 mb-6">
                @foreach($analytics['demographics']['by_city'] as $city)
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">{{ $city->city }}</span>
                    <div class="flex items-center gap-2">
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" 
                                 style="width: {{ ($city->count / $analytics['demographics']['by_city']->first()->count) * 100 }}%">
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-900 w-12 text-right">{{ number_format($city->count) }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Cinsiyet Dağılımı -->
            <h6 class="text-md font-medium text-gray-700 mb-3">Cinsiyet Dağılımı</h6>
            <div class="space-y-2 mb-6">
                @foreach($analytics['demographics']['by_gender'] as $gender)
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">{{ ucfirst($gender->gender) }}</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        {{ number_format($gender->count) }}
                    </span>
                </div>
                @endforeach
            </div>

            <!-- Yaş Grubu Dağılımı -->
            <h6 class="text-md font-medium text-gray-700 mb-3">Yaş Grubu Dağılımı</h6>
            <div class="space-y-2">
                @foreach($analytics['demographics']['by_age_group'] as $ageGroup => $count)
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">{{ $ageGroup }} yaş</span>
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                        {{ number_format($count) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Etkileşim Metrikleri -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h5 class="text-lg font-semibold text-gray-900 mb-4">Kullanıcı Etkileşimi</h5>
            
            <!-- Segmentasyon Detayları -->
            <h6 class="text-md font-medium text-gray-700 mb-3">Kullanıcı Segmentleri</h6>
            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center p-4 bg-yellow-50 rounded-lg">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">⭐</span>
                            <strong class="text-gray-900">Yeni Kullanıcılar</strong>
                        </div>
                        <p class="text-sm text-gray-600 ml-9">Son 7 gün içinde kayıt olan</p>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-900">{{ number_format($analytics['segmentation']['new']) }}</h4>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">🔥</span>
                            <strong class="text-gray-900">Aktif Kullanıcılar</strong>
                        </div>
                        <p class="text-sm text-gray-600 ml-9">Son 7 gün içinde aktif</p>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-900">{{ number_format($analytics['segmentation']['active']) }}</h4>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-orange-50 rounded-lg">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">🌙</span>
                            <strong class="text-gray-900">Pasif Kullanıcılar</strong>
                        </div>
                        <p class="text-sm text-gray-600 ml-9">30-90 gün arası aktivite yok</p>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-900">{{ number_format($analytics['segmentation']['inactive']) }}</h4>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">💤</span>
                            <strong class="text-gray-900">Kayıp Kullanıcılar</strong>
                        </div>
                        <p class="text-sm text-gray-600 ml-9">90+ gün aktivite yok</p>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-900">{{ number_format($analytics['segmentation']['churned']) }}</h4>
                </div>
            </div>

            <!-- Etkileşim Ortalamaları -->
            <h6 class="text-md font-medium text-gray-700 mb-3">Ortalama Etkileşim</h6>
            <div class="space-y-2">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-700">
                        <span class="text-blue-500">✏️</span> Kullanıcı başına gönderi
                    </span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        {{ $analytics['engagement']['posts_per_user'] }}
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-700">
                        <span class="text-green-500">💬</span> Kullanıcı başına yorum
                    </span>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        {{ $analytics['engagement']['comments_per_user'] }}
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-700">
                        <span class="text-indigo-500">📅</span> Kullanıcı başına aktif gün
                    </span>
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                        {{ $analytics['engagement']['active_days_per_user'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function userAnalytics() {
    return {
        dateRange: '{{ $dateRange }}',
        customStartDate: '{{ $customDates['start'] ?? '' }}',
        customEndDate: '{{ $customDates['end'] ?? '' }}',
        
        init() {
            this.initCharts();
        },
        
        loadData() {
            let url = '{{ route('admin.analytics.users') }}?date_range=' + this.dateRange;
            
            if (this.dateRange === 'custom') {
                url += '&start_date=' + this.customStartDate + '&end_date=' + this.customEndDate;
            }
            
            window.location.href = url;
        },
        
        initCharts() {
            // Kayıt Trendi Grafiği
            const registrationData = @json($analytics['registration_trend']['data']);
            const registrationLabels = registrationData.map(item => item.date);
            const registrationCounts = registrationData.map(item => item.count);
            
            new Chart(document.getElementById('registrationTrendChart'), {
                type: 'line',
                data: {
                    labels: registrationLabels,
                    datasets: [{
                        label: 'Yeni Kayıtlar',
                        data: registrationCounts,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
            
            // Segmentasyon Grafiği (Doughnut)
            const segmentation = @json($analytics['segmentation']);
            
            new Chart(document.getElementById('segmentationChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Yeni', 'Aktif', 'Pasif', 'Kayıp'],
                    datasets: [{
                        data: [
                            segmentation.new,
                            segmentation.active,
                            segmentation.inactive,
                            segmentation.churned
                        ],
                        backgroundColor: [
                            'rgb(251, 191, 36)',
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
            
            // Aktif Kullanıcı Trendi Grafiği
            const activeUsersData = @json($analytics['active_users_trend']['data']);
            const activeUsersLabels = activeUsersData.map(item => item.date);
            const activeUsersCounts = activeUsersData.map(item => item.count);
            
            new Chart(document.getElementById('activeUsersTrendChart'), {
                type: 'bar',
                data: {
                    labels: activeUsersLabels,
                    datasets: [{
                        label: 'Aktif Kullanıcılar',
                        data: activeUsersCounts,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
        }
    }
}
</script>
@endpush
@endsection
