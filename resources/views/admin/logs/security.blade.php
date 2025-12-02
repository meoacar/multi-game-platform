@extends('admin.layout')

@section('title', 'Güvenlik Logları')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-yellow-400 via-orange-400 to-red-400 bg-clip-text text-transparent mb-2">
                        🔒 Güvenlik Logları
                    </h1>
                    <p class="text-gray-400">Güvenlik olayları ve şüpheli aktiviteler</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.logs.export', 'system') }}?type=security" 
                       class="px-5 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-bold shadow-lg shadow-green-500/30 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export CSV
                    </a>
                    <a href="{{ route('admin.logs.index') }}" 
                       class="px-5 py-3 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl font-semibold transition-all border border-gray-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Geri
                    </a>
                </div>
            </div>
        </div>

        <!-- İstatistik -->
        <div class="bg-gradient-to-br from-yellow-600/20 to-orange-800/20 backdrop-blur-xl rounded-2xl p-6 border border-yellow-500/30 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white mb-1">Son 24 Saatteki Güvenlik Olayları</h3>
                    <p class="text-3xl font-bold text-yellow-400">{{ number_format($recentCount) }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Şüpheli IP'ler -->
        @if($suspiciousIps->count() > 0)
        <div class="bg-gradient-to-br from-red-600/20 to-red-800/20 backdrop-blur-xl rounded-2xl p-6 border border-red-500/30 mb-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Şüpheli IP Adresleri (5+ güvenlik logu)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($suspiciousIps as $ip)
                    <div class="bg-gray-900/50 rounded-xl p-4 border border-red-500/30">
                        <p class="text-sm font-mono text-white mb-1">{{ $ip->ip_address }}</p>
                        <p class="text-xs text-red-400">{{ $ip->count }} olay</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Filtreler -->
        <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Seviye -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Seviye</label>
                    <select name="level" class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        <option value="">Tümü</option>
                        <option value="emergency" {{ request('level') == 'emergency' ? 'selected' : '' }}>Acil</option>
                        <option value="alert" {{ request('level') == 'alert' ? 'selected' : '' }}>Alarm</option>
                        <option value="critical" {{ request('level') == 'critical' ? 'selected' : '' }}>Kritik</option>
                        <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Hata</option>
                        <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Uyarı</option>
                    </select>
                </div>

                <!-- Mesaj -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Mesaj</label>
                    <input type="text" 
                           name="message" 
                           value="{{ request('message') }}"
                           placeholder="Mesaj ara..."
                           class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                </div>

                <!-- IP -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">IP Adresi</label>
                    <input type="text" 
                           name="ip_address" 
                           value="{{ request('ip_address') }}"
                           placeholder="IP ara..."
                           class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                </div>

                <!-- Tarih Başlangıç -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Başlangıç</label>
                    <input type="date" 
                           name="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                </div>

                <!-- Tarih Bitiş -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Bitiş</label>
                    <input type="date" 
                           name="date_to" 
                           value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                </div>

                <!-- Butonlar -->
                <div class="md:col-span-5 flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white rounded-xl font-semibold shadow-lg shadow-yellow-500/30 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtrele
                    </button>
                    <a href="{{ route('admin.logs.security') }}" class="px-6 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl font-semibold transition-all border border-gray-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Temizle
                    </a>
                </div>
            </form>
        </div>

        <!-- Log Listesi -->
        <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700/50">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Tarih</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Seviye</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Mesaj</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">IP</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/30">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $log->created_at->format('d.m.Y H:i:s') }}
                                    <div class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg bg-{{ $log->level_color }}-500/20 text-{{ $log->level_color }}-400 border border-{{ $log->level_color }}-500/30">
                                        {{ $log->level_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    {{ Str::limit($log->message, 100) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.logs.show', ['type' => 'system', 'id' => $log->id]) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600/20 hover:bg-yellow-600/30 text-yellow-400 rounded-lg border border-yellow-500/30 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Detay
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-400 text-lg font-semibold">Henüz güvenlik logu bulunmuyor</p>
                                        <p class="text-gray-600 text-sm mt-1">Güvenlik olayları burada görünecek</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-700/50 bg-gray-900/30">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
