@extends('admin.layout')

@section('title', 'Toplu Bildirim Yönetimi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                📢 Toplu Bildirim Yönetimi
            </h1>
            <p class="text-gray-600 mt-2">Kullanıcılara toplu bildirim gönder ve yönet</p>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.notifications.templates') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Email Şablonları
            </a>
            <a href="{{ route('admin.notifications.create') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Yeni Bildirim
            </a>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">📊 Toplam</p>
            <p class="text-2xl font-bold">{{ number_format($statistics['total']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">📝 Taslak</p>
            <p class="text-2xl font-bold">{{ number_format($statistics['draft']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">⏰ Zamanlanmış</p>
            <p class="text-2xl font-bold">{{ number_format($statistics['scheduled']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">✅ Gönderildi</p>
            <p class="text-2xl font-bold">{{ number_format($statistics['sent']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">👥 Toplam Alıcı</p>
            <p class="text-2xl font-bold">{{ number_format($statistics['total_recipients']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">📤 Gönderilen</p>
            <p class="text-2xl font-bold">{{ number_format($statistics['total_sent']) }}</p>
        </div>
    </div>

    <!-- Bildirim Listesi -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bildirim
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tip
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hedef
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Durum
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İstatistik
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tarih
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notifications as $notification)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $notification->title }}
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ Str::limit($notification->message, 60) }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        Oluşturan: {{ $notification->creator->username ?? 'Bilinmiyor' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $typeColors = [
                                    'email' => 'bg-blue-100 text-blue-800',
                                    'sms' => 'bg-green-100 text-green-800',
                                    'push' => 'bg-purple-100 text-purple-800',
                                    'site' => 'bg-yellow-100 text-yellow-800',
                                ];
                                $typeIcons = [
                                    'email' => '📧',
                                    'sms' => '📱',
                                    'push' => '🔔',
                                    'site' => '🌐',
                                ];
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeColors[$notification->type] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $typeIcons[$notification->type] ?? '' }} {{ ucfirst($notification->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($notification->target_type === 'all')
                                    👥 Tüm Kullanıcılar
                                @elseif($notification->target_type === 'segment')
                                    🎯 Segment
                                @else
                                    📋 Özel Liste
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'scheduled' => 'bg-yellow-100 text-yellow-800',
                                    'sending' => 'bg-blue-100 text-blue-800',
                                    'sent' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'draft' => '📝 Taslak',
                                    'scheduled' => '⏰ Zamanlanmış',
                                    'sending' => '📤 Gönderiliyor',
                                    'sent' => '✅ Gönderildi',
                                    'failed' => '❌ Başarısız',
                                ];
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$notification->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$notification->status] ?? ucfirst($notification->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div>Hedef: {{ number_format($notification->total_recipients) }}</div>
                                @if($notification->status === 'sent')
                                    <div class="text-green-600">Gönderilen: {{ number_format($notification->sent_count) }}</div>
                                    @if($notification->failed_count > 0)
                                        <div class="text-red-600">Başarısız: {{ number_format($notification->failed_count) }}</div>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $notification->created_at->format('d.m.Y H:i') }}
                            </div>
                            @if($notification->scheduled_at)
                                <div class="text-xs text-yellow-600 mt-1">
                                    ⏰ {{ $notification->scheduled_at->format('d.m.Y H:i') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.notifications.show', $notification->id) }}" 
                                    class="text-blue-600 hover:text-blue-900" title="Görüntüle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                
                                @if(in_array($notification->status, ['draft', 'scheduled']))
                                    <a href="{{ route('admin.notifications.edit', $notification->id) }}" 
                                        class="text-yellow-600 hover:text-yellow-900" title="Düzenle">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                @endif
                                
                                @if($notification->status !== 'sending')
                                    <form action="{{ route('admin.notifications.destroy', $notification->id) }}" 
                                        method="POST" 
                                        onsubmit="return confirm('Bu bildirimi silmek istediğinize emin misiniz?')"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Sil">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Henüz bildirim yok</p>
                                <p class="text-gray-400 mt-1">İlk toplu bildirimi oluşturmak için yukarıdaki butonu kullanın</p>
                                <a href="{{ route('admin.notifications.create') }}" 
                                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Yeni Bildirim Oluştur
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
