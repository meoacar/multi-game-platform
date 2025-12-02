@extends('admin.layout')

@section('title', $pageTitle)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Başlık -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
        <p class="text-gray-600 mt-2">Bekleyen raporlar, içerikler ve şüpheli aktiviteleri yönetin</p>
    </div>

    <!-- Özet Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Bekleyen Raporlar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Bekleyen Raporlar</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $queue['summary']['total_pending_reports'] }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.moderation.reports') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Tümünü Görüntüle →
                </a>
            </div>
        </div>

        <!-- Yüksek Öncelikli -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Yüksek Öncelikli</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $queue['summary']['high_priority_reports'] }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.moderation.reports', ['priority' => 'high']) }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Görüntüle →
                </a>
            </div>
        </div>

        <!-- Bekleyen Başvurular -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Bekleyen Başvurular</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $queue['summary']['total_pending_applications'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.clans.applications') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Görüntüle →
                </a>
            </div>
        </div>

        <!-- Şüpheli Kullanıcılar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Şüpheli Aktivite</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $queue['summary']['suspicious_users'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.moderation.suspicious') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    İncele →
                </a>
            </div>
        </div>
    </div>

    <!-- Son Raporlar -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Son Raporlar</h2>
        </div>
        <div class="p-6">
            @if($queue['pending_reports']->count() > 0)
                <div class="space-y-4">
                    @foreach($queue['pending_reports']->take(10) as $report)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($report['priority'] === 'high') bg-red-100 text-red-800
                                            @elseif($report['priority'] === 'medium') bg-orange-100 text-orange-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst($report['priority']) }}
                                        </span>
                                        <span class="text-sm text-gray-600">{{ $report['type'] }}</span>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">{{ $report['reason'] }}</p>
                                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($report['description'], 100) }}</p>
                                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                                        <span>Raporlayan: {{ $report['reporter'] }}</span>
                                        <span>{{ $report['created_at'] }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ $report['url'] }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        İncele
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('admin.moderation.reports') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Tüm Raporları Görüntüle →
                    </a>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Bekleyen rapor bulunmuyor</p>
            @endif
        </div>
    </div>

    <!-- Hızlı Erişim -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admin.moderation.reports') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center space-x-4">
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Raporları Yönet</h3>
                    <p class="text-sm text-gray-600">Bekleyen raporları incele ve işle</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.moderation.suspicious') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center space-x-4">
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Şüpheli Aktiviteler</h3>
                    <p class="text-sm text-gray-600">Spam ve şüpheli kullanıcıları incele</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.moderation.rules') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Moderasyon Kuralları</h3>
                    <p class="text-sm text-gray-600">Otomatik moderasyon ayarları</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
