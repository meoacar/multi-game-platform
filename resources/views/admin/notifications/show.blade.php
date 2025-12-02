@extends('admin.layout')

@section('title', 'Bildirim Detayı')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                📢 Bildirim Detayı
            </h1>
            <p class="text-gray-600 mt-2">{{ $notification->title }}</p>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.notifications.index') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                ← Geri Dön
            </a>
            
            @if(in_array($notification->status, ['draft', 'scheduled']))
                <a href="{{ route('admin.notifications.edit', $notification->id) }}" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Düzenle
                </a>
            @endif
            
            @if($notification->status === 'draft' || $notification->status === 'scheduled')
                <form action="{{ route('admin.notifications.send', $notification->id) }}" 
                    method="POST" 
                    onsubmit="return confirm('Bu bildirimi göndermek istediğinize emin misiniz?')"
                    class="inline">
                    @csrf
                    <button type="submit" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Gönder
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Taraf: Bildirim Detayları -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Temel Bilgiler -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">📝 Temel Bilgiler</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Başlık</label>
                        <p class="text-gray-900 text-lg font-medium">{{ $notification->title }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Mesaj</label>
                        <div class="bg-gray-50 rounded-lg p-4 text-gray-900">
                            {{ $notification->message }}
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Bildirim Tipi</label>
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
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $typeColors[$notification->type] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $typeIcons[$notification->type] ?? '' }} {{ ucfirst($notification->type) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Hedef Tipi</label>
                            <p class="text-gray-900">
                                @if($notification->target_type === 'all')
                                    👥 Tüm Kullanıcılar
                                @elseif($notification->target_type === 'segment')
                                    🎯 Segment
                                @else
                                    📋 Özel Liste
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- İstatistikler -->
            @if($notification->status === 'sent')
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Gönderim İstatistikleri</h2>
                
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-blue-600 mb-1">Hedef Alıcı</p>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($notification->total_recipients) }}</p>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-green-600 mb-1">Başarılı</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($notification->sent_count) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            {{ $notification->total_recipients > 0 ? round(($notification->sent_count / $notification->total_recipients) * 100, 1) : 0 }}%
                        </p>
                    </div>
                    
                    <div class="bg-red-50 rounded-lg p-4">
                        <p class="text-sm text-red-600 mb-1">Başarısız</p>
                        <p class="text-2xl font-bold text-red-900">{{ number_format($notification->failed_count) }}</p>
                        <p class="text-xs text-red-600 mt-1">
                            {{ $notification->total_recipients > 0 ? round(($notification->failed_count / $notification->total_recipients) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sağ Taraf: Durum ve Bilgiler -->
        <div class="space-y-6">
            <!-- Durum Kartı -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">ℹ️ Durum Bilgisi</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Mevcut Durum</label>
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
                        <span class="px-3 py-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$notification->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$notification->status] ?? ucfirst($notification->status) }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Oluşturulma</label>
                        <p class="text-gray-900">{{ $notification->created_at->format('d.m.Y H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    
                    @if($notification->scheduled_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Zamanlanmış Tarih</label>
                        <p class="text-yellow-600 font-medium">{{ $notification->scheduled_at->format('d.m.Y H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ $notification->scheduled_at->diffForHumans() }}</p>
                    </div>
                    @endif
                    
                    @if($notification->sent_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Gönderilme</label>
                        <p class="text-green-600 font-medium">{{ $notification->sent_at->format('d.m.Y H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ $notification->sent_at->diffForHumans() }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Oluşturan</label>
                        <p class="text-gray-900">{{ $notification->creator->username ?? 'Bilinmiyor' }}</p>
                    </div>
                </div>
            </div>

            <!-- Hedef Kitle Bilgisi -->
            @if($notification->target_criteria)
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">🎯 Hedef Kitle</h2>
                
                <div class="space-y-2">
                    @foreach($notification->target_criteria as $key => $value)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ is_bool($value) ? ($value ? 'Evet' : 'Hayır') : $value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Tehlike Bölgesi -->
            @if($notification->status !== 'sending')
            <div class="bg-red-50 rounded-xl border border-red-200 p-6">
                <h2 class="text-lg font-semibold text-red-900 mb-4">⚠️ Tehlike Bölgesi</h2>
                
                <form action="{{ route('admin.notifications.destroy', $notification->id) }}" 
                    method="POST" 
                    onsubmit="return confirm('Bu bildirimi kalıcı olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        🗑️ Bildirimi Sil
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
