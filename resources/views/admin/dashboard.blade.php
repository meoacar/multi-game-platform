@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="dashboard()">
    <!-- Header with Date Range Filter -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                🚀 Admin Dashboard
            </h1>
            <p class="text-gray-600 mt-2">Hoş geldin, {{ auth()->user()->name }}! Platform istatistiklerini buradan takip edebilirsin.</p>
        </div>
        
        <!-- Date Range Filter -->
        <div class="flex items-center gap-2">
            <select x-model="dateRange" @change="loadStats()" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="today">Bugün</option>
                <option value="yesterday">Dün</option>
                <option value="week">Bu Hafta</option>
                <option value="month" selected>Bu Ay</option>
            </select>
            <button @click="refreshStats()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                🔄 Yenile
            </button>
        </div>
    </div>

    <!-- 4 Ana İstatistik Kartı -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Kullanıcı İstatistikleri -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="text-4xl">👥</div>
                <div class="bg-white/20 rounded-lg px-3 py-1 text-sm">Kullanıcılar</div>
            </div>
            <p class="text-3xl font-bold mb-1" x-text="stats.users?.total || '{{ $statistics['users']['total'] ?? 0 }}'">{{ $statistics['users']['total'] ?? 0 }}</p>
            <p class="text-blue-100 text-sm">Toplam Kullanıcı</p>
            <div class="mt-3 pt-3 border-t border-white/20 space-y-1">
                <div class="flex justify-between text-xs">
                    <span>Aktif:</span>
                    <span x-text="stats.users?.active || '{{ $statistics['users']['active'] ?? 0 }}'">{{ $statistics['users']['active'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>Banlı:</span>
                    <span x-text="stats.users?.banned || '{{ $statistics['users']['banned'] ?? 0 }}'">{{ $statistics['users']['banned'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>Yeni (Bugün):</span>
                    <span x-text="stats.users?.new_today || '{{ $statistics['users']['new_today'] ?? 0 }}'">{{ $statistics['users']['new_today'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- İçerik İstatistikleri -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="text-4xl">📝</div>
                <div class="bg-white/20 rounded-lg px-3 py-1 text-sm">İçerik</div>
            </div>
            <p class="text-3xl font-bold mb-1" x-text="contentTotal">
                {{ ($statistics['content']['lfg_posts']['total'] ?? 0) + ($statistics['content']['clans']['total'] ?? 0) + ($statistics['content']['guides']['total'] ?? 0) }}
            </p>
            <p class="text-green-100 text-sm">Toplam İçerik</p>
            <div class="mt-3 pt-3 border-t border-white/20 space-y-1">
                <div class="flex justify-between text-xs">
                    <span>İlanlar:</span>
                    <span x-text="stats.content?.lfg_posts?.total || '{{ $statistics['content']['lfg_posts']['total'] ?? 0 }}'">{{ $statistics['content']['lfg_posts']['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>Klanlar:</span>
                    <span x-text="stats.content?.clans?.total || '{{ $statistics['content']['clans']['total'] ?? 0 }}'">{{ $statistics['content']['clans']['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>Rehberler:</span>
                    <span x-text="stats.content?.guides?.total || '{{ $statistics['content']['guides']['total'] ?? 0 }}'">{{ $statistics['content']['guides']['total'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Moderasyon İstatistikleri -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="text-4xl">⚠️</div>
                <div class="bg-white/20 rounded-lg px-3 py-1 text-sm">Moderasyon</div>
            </div>
            <p class="text-3xl font-bold mb-1" x-text="stats.moderation?.reports?.pending || '{{ $statistics['moderation']['reports']['pending'] ?? 0 }}'">{{ $statistics['moderation']['reports']['pending'] ?? 0 }}</p>
            <p class="text-orange-100 text-sm">Bekleyen Rapor</p>
            <div class="mt-3 pt-3 border-t border-white/20 space-y-1">
                <div class="flex justify-between text-xs">
                    <span>Çözüldü:</span>
                    <span x-text="stats.moderation?.reports?.resolved || '{{ $statistics['moderation']['reports']['resolved'] ?? 0 }}'">{{ $statistics['moderation']['reports']['resolved'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>Başvurular:</span>
                    <span x-text="stats.moderation?.applications?.total_pending || '{{ $statistics['moderation']['applications']['total_pending'] ?? 0 }}'">{{ $statistics['moderation']['applications']['total_pending'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Sistem İstatistikleri -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-4">
                <div class="text-4xl">⚙️</div>
                <div class="bg-white/20 rounded-lg px-3 py-1 text-sm">Sistem</div>
            </div>
            <p class="text-3xl font-bold mb-1">{{ $statistics['system']['disk_usage']['percentage'] ?? 0 }}%</p>
            <p class="text-purple-100 text-sm">Disk Kullanımı</p>
            <div class="mt-3 pt-3 border-t border-white/20 space-y-1">
                <div class="flex justify-between text-xs">
                    <span>Kullanılan:</span>
                    <span>{{ $statistics['system']['disk_usage']['used'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>Toplam:</span>
                    <span>{{ $statistics['system']['disk_usage']['total'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span>PHP:</span>
                    <span>{{ $statistics['system']['php_version'] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Grafik -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- 1. Kayıt Trendi Grafiği -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">📈 Kullanıcı Kayıt Trendi</h2>
                <span class="text-sm text-gray-500">Son 30 Gün</span>
            </div>
            <div class="h-64">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>

        <!-- 2. İçerik Dağılımı Grafiği -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">📊 İçerik Dağılımı</h2>
                <span class="text-sm text-gray-500">Toplam</span>
            </div>
            <div class="h-64">
                <canvas id="contentDistributionChart"></canvas>
            </div>
        </div>

        <!-- 3. Aktif Kullanıcı Grafiği -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">👥 Aktif Kullanıcı Trendi</h2>
                <span class="text-sm text-gray-500">Son 30 Gün</span>
            </div>
            <div class="h-64">
                <canvas id="activeUsersChart"></canvas>
            </div>
        </div>

        <!-- 4. Popüler Saatler Grafiği -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">🕐 Popüler Saatler</h2>
                <span class="text-sm text-gray-500">Son 7 Gün</span>
            </div>
            <div class="h-64">
                <canvas id="popularHoursChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Hızlı Erişim Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('admin.users.index') }}" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Kullanıcı Yönetimi</p>
                    <p class="text-lg font-bold text-gray-900">{{ $statistics['users']['total'] ?? 0 }} Kullanıcı</p>
                </div>
                <div class="text-4xl">👥</div>
            </div>
            <div class="mt-3 text-sm text-blue-600 font-semibold">Yönet →</div>
        </a>

        <a href="{{ route('admin.reports.index') }}" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Moderasyon Kuyruğu</p>
                    <p class="text-lg font-bold text-gray-900">{{ $statistics['moderation']['reports']['pending'] ?? 0 }} Bekliyor</p>
                </div>
                <div class="text-4xl">⚠️</div>
            </div>
            <div class="mt-3 text-sm text-orange-600 font-semibold">İncele →</div>
        </a>

        <a href="{{ route('admin.lfg-posts.index') }}" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">LFG İlanları</p>
                    <p class="text-lg font-bold text-gray-900">{{ $statistics['content']['lfg_posts']['open'] ?? 0 }} Açık</p>
                </div>
                <div class="text-4xl">📢</div>
            </div>
            <div class="mt-3 text-sm text-green-600 font-semibold">Görüntüle →</div>
        </a>

        <a href="{{ route('admin.settings.index') }}" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Sistem Ayarları</p>
                    <p class="text-lg font-bold text-gray-900">{{ $statistics['system']['disk_usage']['percentage'] ?? 0 }}% Disk</p>
                </div>
                <div class="text-4xl">⚙️</div>
            </div>
            <div class="mt-3 text-sm text-purple-600 font-semibold">Ayarla →</div>
        </a>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Aktif Kullanıcılar</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['users']['active'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Toplam: {{ $statistics['users']['total'] ?? 0 }} kullanıcı</p>
                </div>
                <div class="text-5xl">✅</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Yayınlanan Rehberler</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['content']['guides']['published'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Toplam: {{ $statistics['content']['guides']['total'] ?? 0 }}</p>
                </div>
                <div class="text-5xl">📚</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Topluluk Gönderileri</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['content']['community_posts']['total'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Tüm kategoriler</p>
                </div>
                <div class="text-5xl">💬</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- En Aktif Kullanıcılar -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <span>🏆</span>
                <span>En Aktif Kullanıcılar</span>
            </h2>
            <div class="space-y-3">
                @foreach($top_users as $index => $user)
                <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        @if($index === 0)
                            <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                🥇
                            </div>
                        @elseif($index === 1)
                            <div class="w-10 h-10 bg-gradient-to-br from-gray-300 to-gray-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                🥈
                            </div>
                        @elseif($index === 2)
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                🥉
                            </div>
                        @else
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">Level {{ $user->level }} • {{ number_format($user->xp_total) }} XP</p>
                    </div>
                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-800">
                        →
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Popüler Klanlar -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <span>👑</span>
                <span>Popüler Klanlar</span>
            </h2>
            <div class="space-y-3">
                @foreach($popular_clans as $clan)
                <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                        {{ substr($clan->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $clan->name }}</p>
                        <p class="text-sm text-gray-500">{{ $clan->members_count }} üye</p>
                    </div>
                    @if($clan->is_verified)
                        <span class="text-blue-500" title="Doğrulanmış">✓</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Popüler Rehberler -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <span>📚</span>
                <span>Popüler Rehberler</span>
            </h2>
            <div class="space-y-3">
                @foreach($popular_guides as $guide)
                <div class="p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <p class="font-semibold text-gray-900 truncate mb-1">{{ $guide->title }}</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ $guide->user->name }}</span>
                        <span class="text-blue-600">{{ number_format($guide->views_count) }} görüntülenme</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Son Kullanıcılar -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold">👥 Son Kullanıcılar</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Tümünü Gör →
                </a>
            </div>
            <div class="space-y-3">
                @foreach($recent_users as $user)
                <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                        @if($user->status === 'active')
                            <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Aktif</span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Banlı</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Son Admin Aktiviteleri -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold">📋 Son Aktiviteler</h2>
                <div class="flex items-center gap-2">
                    <button @click="loadActivities()" class="text-sm text-gray-600 hover:text-gray-800">
                        🔄
                    </button>
                    <a href="{{ route('admin.logs.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Tümünü Gör →
                    </a>
                </div>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto" x-data="{ activities: @js($recent_activities) }">
                <template x-for="activity in activities" :key="activity.id">
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            <span x-text="activity.admin.charAt(0).toUpperCase()"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-semibold" x-text="activity.admin"></span>
                                <span class="text-gray-600" x-text="activity.description"></span>
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <p class="text-xs text-gray-500" x-text="activity.created_at"></p>
                                <span class="text-xs text-gray-400">•</span>
                                <p class="text-xs text-gray-400" x-text="activity.ip_address"></p>
                            </div>
                        </div>
                    </div>
                </template>
                
                @if(count($recent_activities) === 0)
                <div class="text-center py-8 text-gray-500">
                    <p class="text-4xl mb-2">📋</p>
                    <p class="text-sm">Henüz aktivite yok</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bekleyen Raporlar -->
    @if($recent_reports->isNotEmpty())
    <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-red-600">⚠️ Bekleyen Raporlar</h2>
            <a href="{{ route('admin.reports.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Tümünü Gör →
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($recent_reports as $report)
            <div class="border border-red-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-2">
                    <span class="inline-block px-3 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                        {{ $report->reason }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-700 mb-3 line-clamp-2">{{ $report->description }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">{{ $report->reporter->name }}</span>
                    <a href="{{ route('admin.reports.show', $report->id) }}" 
                        class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                        İncele →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function dashboard() {
    return {
        dateRange: 'month',
        stats: @json($statistics),
        charts: @json($charts),
        loading: false,
        
        init() {
            this.initCharts();
        },
        
        get contentTotal() {
            if (!this.stats.content) return 0;
            return (this.stats.content.lfg_posts?.total || 0) + 
                   (this.stats.content.clans?.total || 0) + 
                   (this.stats.content.guides?.total || 0);
        },
        
        async loadStats() {
            this.loading = true;
            try {
                const response = await fetch(`/admin/api/dashboard/stats?date_range=${this.dateRange}`);
                const data = await response.json();
                if (data.success) {
                    this.stats = data.data;
                    await this.refreshCharts();
                }
            } catch (error) {
                console.error('İstatistikler yüklenirken hata:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async refreshStats() {
            await this.loadStats();
        },
        
        async loadActivities() {
            try {
                const response = await fetch('/admin/api/dashboard/activities?limit=10');
                const data = await response.json();
                if (data.success) {
                    // Alpine.js component'ine aktiviteleri güncelle
                    const activitiesComponent = document.querySelector('[x-data*="activities"]');
                    if (activitiesComponent) {
                        Alpine.$data(activitiesComponent).activities = data.data;
                    }
                }
            } catch (error) {
                console.error('Aktiviteler yüklenirken hata:', error);
            }
        },
        
        async refreshCharts() {
            // Grafikleri yeniden yükle
            const types = ['registration', 'content', 'active_users', 'popular_hours'];
            for (const type of types) {
                try {
                    const response = await fetch(`/admin/api/dashboard/chart/${type}?date_range=${this.dateRange}`);
                    const data = await response.json();
                    if (data.success) {
                        this.updateChart(type, data.data);
                    }
                } catch (error) {
                    console.error(`${type} grafiği yüklenirken hata:`, error);
                }
            }
        },
        
        initCharts() {
            this.initRegistrationChart();
            this.initContentDistributionChart();
            this.initActiveUsersChart();
            this.initPopularHoursChart();
        },
        
        initRegistrationChart() {
            const ctx = document.getElementById('registrationChart');
            const chartData = this.charts.registration || { labels: [], datasets: [] };
            
            window.registrationChart = new Chart(ctx, {
                type: 'line',
                data: chartData,
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
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        },
        
        initContentDistributionChart() {
            const ctx = document.getElementById('contentDistributionChart');
            const chartData = this.charts.content || { labels: [], datasets: [] };
            
            window.contentDistributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
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
        },
        
        initActiveUsersChart() {
            const ctx = document.getElementById('activeUsersChart');
            const chartData = this.charts.active_users || { labels: [], datasets: [] };
            
            window.activeUsersChart = new Chart(ctx, {
                type: 'line',
                data: chartData,
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
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        },
        
        initPopularHoursChart() {
            const ctx = document.getElementById('popularHoursChart');
            const chartData = this.charts.popular_hours || { labels: [], datasets: [] };
            
            window.popularHoursChart = new Chart(ctx, {
                type: 'bar',
                data: chartData,
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
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        },
        
        updateChart(type, data) {
            const chartMap = {
                'registration': window.registrationChart,
                'content': window.contentDistributionChart,
                'active_users': window.activeUsersChart,
                'popular_hours': window.popularHoursChart
            };
            
            const chart = chartMap[type];
            if (chart) {
                chart.data = data;
                chart.update();
            }
        }
    }
}
</script>
@endsection
