@extends('admin.layout')

@section('title', 'Admin Log Detayı')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Admin Log Detayı</h1>
                    <p class="text-gray-400">Log ID: #{{ $log->id }}</p>
                </div>
                <a href="{{ route('admin.logs.admin') }}" 
                   class="px-5 py-3 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl font-semibold transition-all border border-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Geri
                </a>
            </div>
        </div>

        <!-- Log Bilgileri -->
        <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-8 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Admin -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Admin</label>
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold">
                                {{ strtoupper(substr($log->admin->name, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-white">{{ $log->admin->name }}</p>
                            <p class="text-sm text-gray-400">{{ $log->admin->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tarih -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Tarih</label>
                    <p class="text-lg font-semibold text-white">{{ $log->created_at->format('d.m.Y H:i:s') }}</p>
                    <p class="text-sm text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                </div>

                <!-- Aksiyon -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Aksiyon</label>
                    <span class="inline-block px-4 py-2 text-sm font-bold rounded-lg 
                        {{ str_contains($log->action, 'delete') || str_contains($log->action, 'ban') ? 'bg-red-500/20 text-red-400 border border-red-500/30' : '' }}
                        {{ str_contains($log->action, 'create') || str_contains($log->action, 'accept') ? 'bg-green-500/20 text-green-400 border border-green-500/30' : '' }}
                        {{ str_contains($log->action, 'update') || str_contains($log->action, 'edit') ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : '' }}
                        {{ !str_contains($log->action, 'delete') && !str_contains($log->action, 'ban') && !str_contains($log->action, 'create') && !str_contains($log->action, 'accept') && !str_contains($log->action, 'update') && !str_contains($log->action, 'edit') ? 'bg-gray-500/20 text-gray-400 border border-gray-500/30' : '' }}">
                        {{ $log->action }}
                    </span>
                </div>

                <!-- IP Adresi -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">IP Adresi</label>
                    <p class="text-lg font-mono text-white">{{ $log->ip_address }}</p>
                </div>

                <!-- Hedef Tipi -->
                @if($log->target_type)
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Hedef Tipi</label>
                    <p class="text-lg font-semibold text-white">{{ class_basename($log->target_type) }}</p>
                </div>
                @endif

                <!-- Hedef ID -->
                @if($log->target_id)
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Hedef ID</label>
                    <p class="text-lg font-mono text-white">#{{ $log->target_id }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- User Agent -->
        @if($log->user_agent)
        <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6 mb-6">
            <h3 class="text-lg font-bold text-white mb-3">User Agent</h3>
            <p class="text-sm text-gray-300 font-mono break-all">{{ $log->user_agent }}</p>
        </div>
        @endif

        <!-- Detaylar -->
        @if($log->details)
        <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-bold text-white mb-3">Detaylar</h3>
            <pre class="bg-gray-900/50 rounded-xl p-4 text-sm text-gray-300 overflow-x-auto border border-gray-700/30"><code>{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
        </div>
        @endif
    </div>
</div>
@endsection
