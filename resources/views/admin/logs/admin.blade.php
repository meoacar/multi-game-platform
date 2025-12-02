@extends('admin.layout')

@section('title', 'Admin Aktivite Logları')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">
                        📋 Admin Aktivite Logları
                    </h1>
                    <p class="text-gray-400">Tüm admin işlemlerinin kaydı</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.logs.export', 'admin') }}" 
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

        <!-- Filtreler -->
        <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Admin -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Admin</label>
                    <select name="admin_id" class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tümü</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Aksiyon -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Aksiyon</label>
                    <input type="text" 
                           name="action" 
                           value="{{ request('action') }}"
                           placeholder="Aksiyon ara..."
                           class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Target Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Hedef Tipi</label>
                    <select name="target_type" class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tümü</option>
                        @foreach($targetTypes as $type)
                            <option value="{{ $type }}" {{ request('target_type') == $type ? 'selected' : '' }}>
                                {{ class_basename($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- IP Adresi -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">IP Adresi</label>
                    <input type="text" 
                           name="ip_address" 
                           value="{{ request('ip_address') }}"
                           placeholder="IP ara..."
                           class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Tarih Başlangıç -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Başlangıç</label>
                    <input type="date" 
                           name="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Tarih Bitiş -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Bitiş</label>
                    <input type="date" 
                           name="date_to" 
                           value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 bg-gray-900/50 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Butonlar -->
                <div class="md:col-span-6 flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtrele
                    </button>
                    <a href="{{ route('admin.logs.admin') }}" class="px-6 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl font-semibold transition-all border border-gray-700 flex items-center gap-2">
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
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Admin</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Aksiyon</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Hedef</th>
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
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                                            <span class="text-white font-bold text-sm">
                                                {{ strtoupper(substr($log->admin->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-semibold text-white">{{ $log->admin->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg 
                                        {{ str_contains($log->action, 'delete') || str_contains($log->action, 'ban') ? 'bg-red-500/20 text-red-400 border border-red-500/30' : '' }}
                                        {{ str_contains($log->action, 'create') || str_contains($log->action, 'accept') ? 'bg-green-500/20 text-green-400 border border-green-500/30' : '' }}
                                        {{ str_contains($log->action, 'update') || str_contains($log->action, 'edit') ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : '' }}
                                        {{ !str_contains($log->action, 'delete') && !str_contains($log->action, 'ban') && !str_contains($log->action, 'create') && !str_contains($log->action, 'accept') && !str_contains($log->action, 'update') && !str_contains($log->action, 'edit') ? 'bg-gray-500/20 text-gray-400 border border-gray-500/30' : '' }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">
                                    @if($log->target_type)
                                        <div>
                                            <span class="font-medium text-gray-300">{{ class_basename($log->target_type) }}</span>
                                            @if($log->target_id)
                                                <span class="text-gray-500">#{{ $log->target_id }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                                    {{ $log->ip_address }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.logs.show', ['type' => 'admin', 'id' => $log->id]) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 rounded-lg border border-blue-500/30 transition-all">
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
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-400 text-lg font-semibold">Henüz log kaydı bulunmuyor</p>
                                        <p class="text-gray-600 text-sm mt-1">Admin işlemleri burada görünecek</p>
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
