@extends('admin.layout')

@section('title', 'Log Yönetimi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">
                📋 Log Yönetimi
            </h1>
            <p class="text-gray-400">Sistem, admin ve güvenlik loglarını yönetin</p>
        </div>

        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <!-- Admin Logları -->
            <div class="bg-gradient-to-br from-blue-600/20 to-blue-800/20 backdrop-blur-xl rounded-2xl p-6 border border-blue-500/30">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">{{ number_format($stats['admin_logs']) }}</h3>
                <p class="text-blue-300 text-sm">Admin Logları</p>
                <p class="text-xs text-gray-400 mt-1">Son 24 saat</p>
            </div>

            <!-- Sistem Logları -->
            <div class="bg-gradient-to-br from-purple-600/20 to-purple-800/20 backdrop-blur-xl rounded-2xl p-6 border border-purple-500/30">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">{{ number_format($stats['system_logs']) }}</h3>
                <p class="text-purple-300 text-sm">Sistem Logları</p>
                <p class="text-xs text-gray-400 mt-1">Son 24 saat</p>
            </div>

            <!-- Hata Logları -->
            <div class="bg-gradient-to-br from-red-600/20 to-red-800/20 backdrop-blur-xl rounded-2xl p-6 border border-red-500/30">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">{{ number_format($stats['error_logs']) }}</h3>
                <p class="text-red-300 text-sm">Hata Logları</p>
                <p class="text-xs text-gray-400 mt-1">Son 24 saat</p>
            </div>

            <!-- Güvenlik Logları -->
            <div class="bg-gradient-to-br from-yellow-600/20 to-yellow-800/20 backdrop-blur-xl rounded-2xl p-6 border border-yellow-500/30">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">{{ number_format($stats['security_logs']) }}</h3>
                <p class="text-yellow-300 text-sm">Güvenlik Logları</p>
                <p class="text-xs text-gray-400 mt-1">Son 24 saat</p>
            </div>

            <!-- Kritik Loglar -->
            <div class="bg-gradient-to-br from-pink-600/20 to-pink-800/20 backdrop-blur-xl rounded-2xl p-6 border border-pink-500/30">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">{{ number_format($stats['critical_logs']) }}</h3>
                <p class="text-pink-300 text-sm">Kritik Loglar</p>
                <p class="text-xs text-gray-400 mt-1">Son 24 saat</p>
            </div>
        </div>

        <!-- Hızlı Erişim Butonları -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('admin.logs.admin') }}" class="bg-gradient-to-br from-blue-600/20 to-blue-800/20 backdrop-blur-xl rounded-xl p-6 border border-blue-500/30 hover:border-blue-400/50 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Admin Logları</h3>
                        <p class="text-sm text-gray-400">Tüm admin işlemleri</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.logs.system') }}" class="bg-gradient-to-br from-purple-600/20 to-purple-800/20 backdrop-blur-xl rounded-xl p-6 border border-purple-500/30 hover:border-purple-400/50 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Sistem Logları</h3>
                        <p class="text-sm text-gray-400">Hata ve uyarılar</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.logs.security') }}" class="bg-gradient-to-br from-yellow-600/20 to-yellow-800/20 backdrop-blur-xl rounded-xl p-6 border border-yellow-500/30 hover:border-yellow-400/50 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Güvenlik Logları</h3>
                        <p class="text-sm text-gray-400">Şüpheli aktiviteler</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.logs.cleanup.page') }}" class="bg-gradient-to-br from-red-600/20 to-red-800/20 backdrop-blur-xl rounded-xl p-6 border border-red-500/30 hover:border-red-400/50 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Log Temizleme</h3>
                        <p class="text-sm text-gray-400">Eski logları sil</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Son Admin Aktiviteleri -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Son Admin Aktiviteleri
                </h2>
                <div class="space-y-3">
                    @forelse($recentAdminLogs as $log)
                        <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700/30">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-semibold text-white">{{ $log->admin->name }}</span>
                                        <span class="text-xs text-gray-500">•</span>
                                        <span class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-300">{{ $log->action }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Henüz aktivite yok</p>
                    @endforelse
                </div>
            </div>

            <!-- Kritik Sistem Logları -->
            <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Kritik Loglar
                </h2>
                <div class="space-y-3">
                    @forelse($criticalLogs as $log)
                        <div class="bg-red-900/20 rounded-xl p-4 border border-red-500/30">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-1 text-xs font-bold rounded bg-{{ $log->level_color }}-500/20 text-{{ $log->level_color }}-400 border border-{{ $log->level_color }}-500/30">
                                            {{ $log->level_name }}
                                        </span>
                                        <span class="text-xs text-gray-500">•</span>
                                        <span class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-300">{{ Str::limit($log->message, 100) }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Kritik log yok</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
