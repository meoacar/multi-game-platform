@extends('admin.layout')

@section('title', 'Log Detayı')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Log Detayı</h1>
            <p class="text-gray-600 mt-1">Admin aktivite log kaydı</p>
        </div>
        <a href="{{ route('admin.logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Geri Dön
        </a>
    </div>

    <!-- Log Detayları -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 space-y-6">
            <!-- Temel Bilgiler -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Tarih & Saat</h3>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $log->created_at->format('d.m.Y H:i:s') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ $log->created_at->diffForHumans() }}
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Admin</h3>
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <span class="text-purple-600 font-semibold">
                                {{ substr($log->admin->name, 0, 2) }}
                            </span>
                        </div>
                        <div class="ml-3">
                            <p class="text-lg font-semibold text-gray-900">{{ $log->admin->name }}</p>
                            <p class="text-sm text-gray-500">{{ $log->admin->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Aksiyon -->
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Aksiyon</h3>
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                    {{ str_contains($log->action, 'delete') || str_contains($log->action, 'ban') ? 'bg-red-100 text-red-800' : '' }}
                    {{ str_contains($log->action, 'create') || str_contains($log->action, 'accept') ? 'bg-green-100 text-green-800' : '' }}
                    {{ str_contains($log->action, 'update') || str_contains($log->action, 'edit') ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ !str_contains($log->action, 'delete') && !str_contains($log->action, 'ban') && !str_contains($log->action, 'create') && !str_contains($log->action, 'accept') && !str_contains($log->action, 'update') && !str_contains($log->action, 'edit') ? 'bg-gray-100 text-gray-800' : '' }}">
                    {{ $log->action }}
                </span>
            </div>

            <!-- Hedef -->
            @if($log->target_type || $log->target_id)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Hedef</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        @if($log->target_type)
                            <p class="text-sm">
                                <span class="font-medium text-gray-700">Tip:</span>
                                <span class="text-gray-900">{{ $log->target_type }}</span>
                            </p>
                        @endif
                        @if($log->target_id)
                            <p class="text-sm mt-1">
                                <span class="font-medium text-gray-700">ID:</span>
                                <span class="text-gray-900">{{ $log->target_id }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Meta Data -->
            @if($log->meta)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Ek Bilgiler (Meta)</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <pre class="text-sm text-gray-900 whitespace-pre-wrap">{{ json_encode($log->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif

            <hr>

            <!-- Teknik Bilgiler -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">IP Adresi</h3>
                    <p class="text-gray-900 font-mono">{{ $log->ip_address }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">User Agent</h3>
                    <p class="text-sm text-gray-900 break-all">{{ $log->user_agent }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- İlgili Loglar -->
    @if($log->target_type && $log->target_id)
        @php
            $relatedLogs = \App\Models\AdminActivityLog::where('target_type', $log->target_type)
                ->where('target_id', $log->target_id)
                ->where('id', '!=', $log->id)
                ->with('admin')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        @endphp

        @if($relatedLogs->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">İlgili Loglar</h3>
                    <p class="text-sm text-gray-600">Aynı hedef üzerindeki diğer işlemler</p>
                </div>
                <div class="divide-y">
                    @foreach($relatedLogs as $relatedLog)
                        <div class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                        <span class="text-purple-600 font-semibold text-xs">
                                            {{ substr($relatedLog->admin->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $relatedLog->admin->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $relatedLog->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ str_contains($relatedLog->action, 'delete') || str_contains($relatedLog->action, 'ban') ? 'bg-red-100 text-red-800' : '' }}
                                        {{ str_contains($relatedLog->action, 'create') || str_contains($relatedLog->action, 'accept') ? 'bg-green-100 text-green-800' : '' }}
                                        {{ str_contains($relatedLog->action, 'update') || str_contains($relatedLog->action, 'edit') ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ !str_contains($relatedLog->action, 'delete') && !str_contains($relatedLog->action, 'ban') && !str_contains($relatedLog->action, 'create') && !str_contains($relatedLog->action, 'accept') && !str_contains($relatedLog->action, 'update') && !str_contains($relatedLog->action, 'edit') ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $relatedLog->action }}
                                    </span>
                                    <a href="{{ route('admin.logs.show', $relatedLog) }}" 
                                       class="text-blue-600 hover:text-blue-900 text-sm">
                                        Detay →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
