@extends('admin.layout')

@section('title', 'Güvenlik Taraması')

@section('content')
<div x-data="securityScanner()" x-init="init()">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">🔒 Güvenlik Taraması</h1>
        <p class="text-gray-600">SQL Injection, XSS ve CSRF güvenlik açıklarını tespit edin</p>
    </div>

    <!-- Tarama Kontrolleri -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Tarama Seçenekleri</h2>
            <button 
                @click="runScan('all')"
                :disabled="scanning"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <svg x-show="!scanning" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <svg x-show="scanning" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="scanning ? 'Taranıyor...' : 'Tam Tarama Başlat'"></span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- SQL Injection -->
            <button 
                @click="runScan('sql')"
                :disabled="scanning"
                class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-left">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">SQL Injection</h3>
                        <p class="text-sm text-gray-600">Veritabanı güvenliği</p>
                    </div>
                </div>
            </button>

            <!-- XSS -->
            <button 
                @click="runScan('xss')"
                :disabled="scanning"
                class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-left">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">XSS</h3>
                        <p class="text-sm text-gray-600">Script injection</p>
                    </div>
                </div>
            </button>

            <!-- CSRF -->
            <button 
                @click="runScan('csrf')"
                :disabled="scanning"
                class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-left">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">CSRF</h3>
                        <p class="text-sm text-gray-600">Token koruması</p>
                    </div>
                </div>
            </button>
        </div>
    </div>

    <!-- Sonuç Özeti -->
    <div x-show="results" class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Tarama Özeti</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Durum -->
            <div class="p-4 rounded-lg" :class="{
                'bg-green-50 border-2 border-green-200': results?.summary?.status === 'safe',
                'bg-yellow-50 border-2 border-yellow-200': results?.summary?.status === 'warning',
                'bg-red-50 border-2 border-red-200': results?.summary?.status === 'critical'
            }">
                <div class="text-sm font-medium mb-1" :class="{
                    'text-green-600': results?.summary?.status === 'safe',
                    'text-yellow-600': results?.summary?.status === 'warning',
                    'text-red-600': results?.summary?.status === 'critical'
                }">Durum</div>
                <div class="text-2xl font-bold" :class="{
                    'text-green-700': results?.summary?.status === 'safe',
                    'text-yellow-700': results?.summary?.status === 'warning',
                    'text-red-700': results?.summary?.status === 'critical'
                }" x-text="getStatusText()"></div>
            </div>

            <!-- Toplam -->
            <div class="p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                <div class="text-sm text-gray-600 font-medium mb-1">Toplam Açık</div>
                <div class="text-2xl font-bold text-gray-900" x-text="results?.summary?.total_vulnerabilities || 0"></div>
            </div>

            <!-- Kritik -->
            <div class="p-4 bg-red-50 rounded-lg border-2 border-red-200">
                <div class="text-sm text-red-600 font-medium mb-1">Kritik</div>
                <div class="text-2xl font-bold text-red-700" x-text="results?.summary?.critical || 0"></div>
            </div>

            <!-- Uyarı -->
            <div class="p-4 bg-yellow-50 rounded-lg border-2 border-yellow-200">
                <div class="text-sm text-yellow-600 font-medium mb-1">Uyarı</div>
                <div class="text-2xl font-bold text-yellow-700" x-text="results?.summary?.warning || 0"></div>
            </div>
        </div>
    </div>

    <!-- Detaylı Sonuçlar -->
    <div x-show="results">
        <!-- SQL Injection Sonuçları -->
        <div x-show="results?.sql_injection" class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 SQL Injection Taraması</h3>
            <template x-if="results?.sql_injection?.total > 0">
                <div>
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-800 font-medium">
                            <span x-text="results.sql_injection.total"></span> güvenlik açığı bulundu
                        </p>
                    </div>
                    <div class="space-y-4">
                        <template x-for="(vuln, index) in results.sql_injection.vulnerabilities" :key="index">
                            <div class="border-l-4 pl-4 py-2" :class="vuln.severity === 'critical' ? 'border-red-500' : 'border-yellow-500'">
                                <div class="flex items-start justify-between mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded" 
                                          :class="vuln.severity === 'critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'"
                                          x-text="vuln.severity === 'critical' ? 'KRİTİK' : 'UYARI'"></span>
                                </div>
                                <p class="text-sm text-gray-700 mb-1" x-text="vuln.file + ':' + vuln.line"></p>
                                <p class="text-sm text-gray-900 font-medium mb-1" x-text="vuln.description"></p>
                                <p class="text-sm text-blue-600" x-text="'💡 ' + vuln.recommendation"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <template x-if="results?.sql_injection?.total === 0">
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                    ✅ SQL Injection açığı bulunamadı
                </div>
            </template>
        </div>

        <!-- XSS Sonuçları -->
        <div x-show="results?.xss" class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 XSS Taraması</h3>
            <template x-if="results?.xss?.total > 0">
                <div>
                    <div class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                        <p class="text-orange-800 font-medium">
                            <span x-text="results.xss.total"></span> güvenlik açığı bulundu
                        </p>
                    </div>
                    <div class="space-y-4">
                        <template x-for="(vuln, index) in results.xss.vulnerabilities" :key="index">
                            <div class="border-l-4 pl-4 py-2" :class="vuln.severity === 'critical' ? 'border-red-500' : 'border-yellow-500'">
                                <div class="flex items-start justify-between mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded" 
                                          :class="vuln.severity === 'critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'"
                                          x-text="vuln.severity === 'critical' ? 'KRİTİK' : 'UYARI'"></span>
                                </div>
                                <p class="text-sm text-gray-700 mb-1" x-text="vuln.file + ':' + vuln.line"></p>
                                <p class="text-sm text-gray-900 font-medium mb-1" x-text="vuln.description"></p>
                                <p class="text-sm text-blue-600" x-text="'💡 ' + vuln.recommendation"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <template x-if="results?.xss?.total === 0">
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                    ✅ XSS açığı bulunamadı
                </div>
            </template>
        </div>

        <!-- CSRF Sonuçları -->
        <div x-show="results?.csrf" class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 CSRF Taraması</h3>
            <template x-if="results?.csrf?.total > 0">
                <div>
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-yellow-800 font-medium">
                            <span x-text="results.csrf.total"></span> güvenlik açığı bulundu
                        </p>
                    </div>
                    <div class="space-y-4">
                        <template x-for="(vuln, index) in results.csrf.vulnerabilities" :key="index">
                            <div class="border-l-4 pl-4 py-2" :class="vuln.severity === 'critical' ? 'border-red-500' : 'border-yellow-500'">
                                <div class="flex items-start justify-between mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded" 
                                          :class="vuln.severity === 'critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'"
                                          x-text="vuln.severity === 'critical' ? 'KRİTİK' : 'UYARI'"></span>
                                </div>
                                <p class="text-sm text-gray-700 mb-1" x-text="vuln.file ? (vuln.file + ':' + vuln.line) : (vuln.route + ' [' + vuln.methods + ']')"></p>
                                <p class="text-sm text-gray-900 font-medium mb-1" x-text="vuln.description"></p>
                                <p class="text-sm text-blue-600" x-text="'💡 ' + vuln.recommendation"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <template x-if="results?.csrf?.total === 0">
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                    ✅ CSRF açığı bulunamadı
                </div>
            </template>
        </div>

        <!-- Güvenlik Önerileri -->
        <div x-show="recommendations" class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Güvenlik Önerileri</h3>
            <div class="space-y-6">
                <template x-for="(category, key) in recommendations" :key="key">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2" x-text="category.title"></h4>
                        <ul class="space-y-2">
                            <template x-for="(rec, index) in category.recommendations" :key="index">
                                <li class="flex items-start gap-2 text-sm text-gray-700">
                                    <span class="text-blue-600 mt-1">•</span>
                                    <span x-text="rec"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function securityScanner() {
    return {
        scanning: false,
        results: null,
        recommendations: null,

        init() {
            // Sayfa yüklendiğinde önerileri getir
            this.loadRecommendations();
        },

        async runScan(type) {
            this.scanning = true;
            this.results = null;

            try {
                const response = await fetch('/admin/security/scan?type=' + type, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.results = data.results;
                    this.recommendations = data.recommendations;
                }
            } catch (error) {
                console.error('Tarama hatası:', error);
                alert('Tarama sırasında bir hata oluştu');
            } finally {
                this.scanning = false;
            }
        },

        async loadRecommendations() {
            try {
                const response = await fetch('/admin/security/recommendations');
                const data = await response.json();
                
                if (data.success) {
                    this.recommendations = data.recommendations;
                }
            } catch (error) {
                console.error('Öneriler yüklenemedi:', error);
            }
        },

        getStatusText() {
            if (!this.results?.summary) return '';
            
            switch(this.results.summary.status) {
                case 'safe': return '✅ Güvenli';
                case 'warning': return '⚠️ Uyarı';
                case 'critical': return '🚨 Kritik';
                default: return 'Bilinmiyor';
            }
        }
    }
}
</script>
@endsection
