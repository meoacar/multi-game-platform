@extends('admin.layout')

@section('title', 'Sistem Yönetimi')

@section('content')
<div x-data="systemManager()" x-init="init()">
    <!-- Sayfa Başlığı -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">⚙️ Sistem Yönetimi</h1>
                <p class="text-gray-600 mt-1">Sistem durumu, performans ve cache yönetimi</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.backup.index') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                    <span>💾</span>
                    <span>Yedekleme</span>
                </a>
                <button @click="refreshData()" 
                        :disabled="loading"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors disabled:opacity-50 flex items-center space-x-2">
                    <svg class="w-5 h-5" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Yenile</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Sistem Sağlık Durumu -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">🏥 Sistem Sağlık Durumu</h3>
            <span class="text-sm text-gray-500">Son güncelleme: <span x-text="lastUpdate"></span></span>
        </div>
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-3
                @if($health['overall'] === 'healthy') bg-green-100
                @elseif($health['overall'] === 'degraded') bg-yellow-100
                @else bg-red-100
                @endif">
                <span class="text-4xl">
                    @if($health['overall'] === 'healthy') ✅
                    @elseif($health['overall'] === 'degraded') ⚠️
                    @else ❌
                    @endif
                </span>
            </div>
            <p class="text-2xl font-bold
                @if($health['overall'] === 'healthy') text-green-600
                @elseif($health['overall'] === 'degraded') text-yellow-600
                @else text-red-600
                @endif">
                @if($health['overall'] === 'healthy') Sağlıklı
                @elseif($health['overall'] === 'degraded') Kısmi Sorun
                @else Sorunlu
                @endif
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($health['checks'] as $name => $check)
            <div class="border-2 rounded-lg p-4
                @if($check['status'] === 'up') border-green-200 bg-green-50
                @elseif($check['status'] === 'warning') border-yellow-200 bg-yellow-50
                @else border-red-200 bg-red-50
                @endif">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl">
                            @if($name === 'database') 🗄️
                            @elseif($name === 'cache') 💾
                            @elseif($name === 'disk') 💿
                            @endif
                        </span>
                        <span class="font-semibold text-gray-900">
                            @if($name === 'database') Veritabanı
                            @elseif($name === 'cache') Cache
                            @elseif($name === 'disk') Disk
                            @endif
                        </span>
                    </div>
                    <span class="px-2 py-1 text-xs font-bold rounded
                        @if($check['status'] === 'up') bg-green-200 text-green-800
                        @elseif($check['status'] === 'warning') bg-yellow-200 text-yellow-800
                        @else bg-red-200 text-red-800
                        @endif">
                        {{ $check['status'] === 'up' ? '✓ OK' : ($check['status'] === 'warning' ? '⚠ Uyarı' : '✗ Hata') }}
                    </span>
                </div>
                <p class="text-sm text-gray-700">{{ $check['message'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Performans Metrikleri -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Memory -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2">
                    <span class="text-3xl">🧠</span>
                    <h3 class="text-xl font-bold">Bellek</h3>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-purple-100">Kullanılan:</span>
                    <span class="font-bold">{{ $performance['memory']['current'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-purple-100">Peak:</span>
                    <span class="font-bold">{{ $performance['memory']['peak'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-purple-100">Limit:</span>
                    <span class="font-bold">{{ $performance['memory']['limit'] }}</span>
                </div>
            </div>
        </div>

        <!-- Disk -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2">
                    <span class="text-3xl">💿</span>
                    <h3 class="text-xl font-bold">Disk</h3>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-blue-100">Boş Alan:</span>
                    <span class="font-bold">{{ $performance['disk']['free'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-blue-100">Toplam:</span>
                    <span class="font-bold">{{ $performance['disk']['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-blue-100">Kullanım:</span>
                    <span class="font-bold">{{ $performance['disk']['used_percent'] }}%</span>
                </div>
            </div>
        </div>

        <!-- Cache -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2">
                    <span class="text-3xl">⚡</span>
                    <h3 class="text-xl font-bold">Cache</h3>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-green-100">Driver:</span>
                    <span class="font-bold uppercase">{{ $systemInfo['cache_driver'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-green-100">Hit Rate:</span>
                    <span class="font-bold">
                        @php
                            $hitRate = $cacheStats['hit_rate'] ?? 0;
                            if (is_string($hitRate)) {
                                $hitRate = (float) str_replace('%', '', $hitRate);
                            }
                        @endphp
                        {{ number_format($hitRate, 1) }}%
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-green-100">Toplam Key:</span>
                    <span class="font-bold">{{ number_format($cacheStats['keys_count'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sistem Bilgileri -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Sunucu Bilgileri -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <span>🖥️</span>
                <span>Sunucu Bilgileri</span>
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">PHP Versiyonu:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $systemInfo['php_version'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Laravel Versiyonu:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $systemInfo['laravel_version'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">İşletim Sistemi:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $systemInfo['os'] }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-600">Sunucu:</span>
                    <span class="text-sm font-semibold text-gray-900 text-right">{{ $systemInfo['server_software'] }}</span>
                </div>
            </div>
        </div>

        <!-- Veritabanı Bilgileri -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
                <span>🗄️</span>
                <span>Veritabanı Bilgileri</span>
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Driver:</span>
                    <span class="text-sm font-semibold text-gray-900 uppercase">{{ $systemInfo['database_driver'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Versiyon:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $systemInfo['database_version'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Boyut:</span>
                    <span class="text-sm font-semibold text-blue-600">{{ $performance['database']['size'] }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-600">Tablo Sayısı:</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $performance['database']['table_count'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cache Yönetimi -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center space-x-2">
            <span>🧹</span>
            <span>Cache Yönetimi</span>
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button @click="clearCache('all')" 
                    class="bg-red-600 hover:bg-red-700 text-white rounded-lg p-4 transition-colors flex flex-col items-center space-y-2">
                <span class="text-3xl">🗑️</span>
                <span class="text-sm font-semibold">Tümünü Temizle</span>
            </button>
            
            <button @click="clearCache('dashboard')" 
                    class="bg-orange-600 hover:bg-orange-700 text-white rounded-lg p-4 transition-colors flex flex-col items-center space-y-2">
                <span class="text-3xl">📊</span>
                <span class="text-sm font-semibold">Dashboard</span>
            </button>
            
            <button @click="clearCache('analytics')" 
                    class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-4 transition-colors flex flex-col items-center space-y-2">
                <span class="text-3xl">📈</span>
                <span class="text-sm font-semibold">Analytics</span>
            </button>
            
            <button @click="clearCache('queries')" 
                    class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-4 transition-colors flex flex-col items-center space-y-2">
                <span class="text-3xl">🔍</span>
                <span class="text-sm font-semibold">Queries</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function systemManager() {
    return {
        loading: false,
        lastUpdate: new Date().toLocaleTimeString('tr-TR'),

        init() {
            setInterval(() => {
                this.lastUpdate = new Date().toLocaleTimeString('tr-TR');
            }, 30000);
        },

        async refreshData() {
            this.loading = true;
            try {
                await new Promise(resolve => setTimeout(resolve, 500));
                window.location.reload();
            } finally {
                this.loading = false;
            }
        },

        async clearCache(type) {
            if (!confirm(`${type === 'all' ? 'Tüm cache' : type + ' cache'}'i temizlemek istediğinize emin misiniz?`)) {
                return;
            }

            try {
                const response = await fetch('/admin/system/cache/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ type })
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    await this.refreshData();
                } else {
                    alert('Hata: ' + data.message);
                }
            } catch (error) {
                console.error('Cache temizleme hatası:', error);
                alert('Cache temizlenirken bir hata oluştu');
            }
        }
    }
}
</script>
@endpush
@endsection
