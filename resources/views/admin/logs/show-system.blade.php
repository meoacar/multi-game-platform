@extends('admin.layout')

@section('title', 'Sistem Log Detayı')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Sistem Log Detayı</h1>
                    <p class="text-gray-400">Log ID: #{{ $log->id }}</p>
                </div>
                <a href="{{ route('admin.logs.system') }}" 
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
                <!-- Tür -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Tür</label>
                    <span class="inline-block px-4 py-2 text-sm font-bold rounded-lg bg-{{ $log->type_color }}-500/20 text-{{ $log->type_color }}-400 border border-{{ $log->type_color }}-500/30">
                        {{ $log->type_name }}
                    </span>
                </div>

                <!-- Seviye -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Seviye</label>
                    <span class="inline-block px-4 py-2 text-sm font-bold rounded-lg bg-{{ $log->level_color }}-500/20 text-{{ $log->level_color }}-400 border border-{{ $log->level_color }}-500/30">
                        {{ $log->level_name }}
                    </span>
                </div>

                <!-- Tarih -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Tarih</label>
                    <p class="text-lg font-semibold text-white">{{ $log->created_at->format('d.m.Y H:i:s') }}</p>
                    <p class="text-sm text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                </div>

                <!-- IP Adresi -->
                @if($log->ip_address)
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">IP Adresi</label>
                    <p class="text-lg font-mono text-white">{{ $log->ip_address }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Mesaj -->
        <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6 mb-6">
            <h3 class="text-lg font-bold text-white mb-3">Mesaj</h3>
            <p class="text-gray-300">{{ $log->message }}</p>
        </div>

        <!-- URL -->
        @if($log->url)
        <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6 mb-6">
            <h3 class="text-lg font-bold text-white mb-3">URL</h3>
            <p class="text-sm text-blue-400 font-mono break-all">{{ $log->url }}</p>
        </div>
        @endif

        <!-- User Agent -->
        @if($log->user_agent)
        <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6 mb-6">
            <h3 class="text-lg font-bold text-white mb-3">User Agent</h3>
            <p class="text-sm text-gray-300 font-mono break-all">{{ $log->user_agent }}</p>
        </div>
        @endif

        <!-- Context -->
        @if($log->context)
        <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-6 mb-6">
            <h3 class="text-lg font-bold text-white mb-3">Context</h3>
            <pre class="bg-gray-900/50 rounded-xl p-4 text-sm text-gray-300 overflow-x-auto border border-gray-700/30"><code>{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
        </div>
        @endif

        <!-- Stack Trace -->
        @if($log->stack_trace)
        <div class="bg-gradient-to-br from-red-800/20 to-red-900/20 backdrop-blur-xl rounded-2xl shadow-2xl border border-red-700/50 p-6">
            <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Stack Trace
            </h3>
            <pre class="bg-gray-900/50 rounded-xl p-4 text-xs text-red-300 overflow-x-auto border border-red-700/30"><code>{{ $log->stack_trace }}</code></pre>
        </div>
        @endif
    </div>
</div>
@endsection
