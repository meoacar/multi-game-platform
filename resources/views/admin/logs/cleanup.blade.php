@extends('admin.layout')

@section('title', 'Log Temizleme')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-red-400 via-pink-400 to-purple-400 bg-clip-text text-transparent mb-2">
                        🗑️ Log Temizleme
                    </h1>
                    <p class="text-gray-400">Eski log kayıtlarını temizleyin ve veritabanı boyutunu optimize edin</p>
                </div>
                <a href="{{ route('admin.logs.index') }}" 
                   class="px-5 py-3 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl font-semibold transition-all border border-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Geri
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500/50 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-300 font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500/50 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-300 font-semibold">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Veritabanı Boyutları -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-600/20 to-blue-800/20 backdrop-blur-xl rounded-2xl p-6 border border-blue-500/30">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Admin Logları</h3>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-blue-400 mb-2">{{ $sizes['admin_logs'] }} MB</p>
                <p class="text-sm text-gray-400">Veritabanı boyutu</p>
            </div>

            <div class="bg-gradient-to-br from-purple-600/20 to-purple-800/20 backdrop-blur-xl rounded-2xl p-6 border border-purple-500/30">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Sistem Logları</h3>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-purple-400 mb-2">{{ $sizes['system_logs'] }} MB</p>
                <p class="text-sm text-gray-400">Veritabanı boyutu</p>
            </div>
        </div>

        <!-- Log Sayıları -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Admin Logları -->
            <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Admin Logları
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg">
                        <span class="text-gray-300">Toplam</span>
                        <span class="text-white font-bold">{{ number_format($counts['admin']['total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg">
                        <span class="text-gray-300">Son 7 gün</span>
                        <span class="text-green-400 font-bold">{{ number_format($counts['admin']['last_7_days']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg">
                        <span class="text-gray-300">Son 30 gün</span>
                        <span class="text-blue-400 font-bold">{{ number_format($counts['admin']['last_30_days']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg">
                        <span class="text-gray-300">Son 90 gün</span>
                        <span class="text-yellow-400 font-bold">{{ number_format($counts['admin']['last_90_days']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-900/20 rounded-lg border border-red-500/30">
                        <span class="text-gray-300">90 günden eski</span>
                        <span class="text-red-400 font-bold">{{ number_format($counts['admin']['older']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Sistem Logları -->
            <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Sistem Logları
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg">
                        <span class="text-gray-300">Toplam</span>
                        <span class="text-white font-bold">{{ number_format($counts['system']['total']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg">
                        <span class="text-gray-300">Son 7 gün</span>
                        <span class="text-green-400 font-bold">{{ number_format($counts['system']['last_7_days']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg">
                        <span class="text-gray-300">Son 30 gün</span>
                        <span class="text-blue-400 font-bold">{{ number_format($counts['system']['last_30_days']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-lg">
                        <span class="text-gray-300">Son 90 gün</span>
                        <span class="text-yellow-400 font-bold">{{ number_format($counts['system']['last_90_days']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-900/20 rounded-lg border border-red-500/30">
                        <span class="text-gray-300">90 günden eski</span>
                        <span class="text-red-400 font-bold">{{ number_format($counts['system']['older']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Temizleme Seçenekleri -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Manuel Temizleme -->
            <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    Manuel Temizleme
                </h3>
                <p class="text-gray-400 text-sm mb-6">Belirli bir tarihten eski logları temizleyin</p>

                <form method="POST" action="{{ route('admin.logs.cleanup') }}" onsubmit="return confirm('Bu işlem geri alınamaz! Devam etmek istediğinizden emin misiniz?');">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Log Türü</label>
                        <select name="log_type" required class="w-full px-4 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="all">Tüm Loglar</option>
                            <option value="admin">Sadece Admin Logları</option>
                            <option value="system">Sadece Sistem Logları</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Kaç günden eski loglar silinsin?</label>
                        <input type="number" 
                               name="days" 
                               value="90" 
                               min="1" 
                               max="365"
                               required
                               class="w-full px-4 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <p class="text-xs text-gray-400 mt-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Örnek: 90 gün = Son 90 günden eski tüm loglar silinir
                        </p>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" 
                                   name="confirm" 
                                   value="1"
                                   required
                                   class="w-5 h-5 rounded bg-gray-900/50 border-gray-700 text-orange-600 focus:ring-2 focus:ring-orange-500">
                            <span class="text-sm font-medium text-gray-300">Bu işlemin geri alınamayacağını anlıyorum</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-xl font-bold shadow-lg shadow-orange-500/30 transition-all">
                        Manuel Temizleme Başlat
                    </button>
                </form>
            </div>

            <!-- Akıllı Temizleme -->
            <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Akıllı Toplu Temizleme
                </h3>
                <p class="text-gray-400 text-sm mb-6">Önerilen kurallara göre otomatik temizleme</p>

                <div class="bg-gray-900/50 rounded-xl p-4 mb-6 space-y-2">
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-300">90+ günlük admin logları</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-300">30+ günlük info/performance logları</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-300">60+ günlük warning logları</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span class="text-gray-300">Kritik loglar korunur</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logs.cleanup.bulk') }}" onsubmit="return confirm('Akıllı temizleme başlatılacak. Devam etmek istediğinizden emin misiniz?');">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" 
                                   name="confirm" 
                                   value="1"
                                   required
                                   class="w-5 h-5 rounded bg-gray-900/50 border-gray-700 text-purple-600 focus:ring-2 focus:ring-purple-500">
                            <span class="text-sm font-medium text-gray-300">Akıllı temizleme kurallarını onaylıyorum</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-xl font-bold shadow-lg shadow-purple-500/30 transition-all">
                        Akıllı Temizleme Başlat
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
