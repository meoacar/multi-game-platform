@extends('admin.layout')

@section('title', 'Push Notification Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🔔 Push Notification Yönetimi</h1>
            <p class="text-gray-600 mt-1">Firebase Cloud Messaging (FCM) istatistikleri ve yönetimi</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.push-notifications.test') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                🧪 Test Gönder
            </a>
            <a href="{{ route('admin.push-notifications.tokens') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                📋 Token Listesi
            </a>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Toplam Kullanıcı -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Toplam Kullanıcı</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($statistics['total_users']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">👥</span>
                </div>
            </div>
        </div>

        <!-- Token Olan Kullanıcılar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">FCM Token Olan</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($statistics['users_with_token']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        %{{ $statistics['total_users'] > 0 ? round(($statistics['users_with_token'] / $statistics['total_users']) * 100, 1) : 0 }} kapsama
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">🔔</span>
                </div>
            </div>
        </div>

        <!-- Aktif Kullanıcılar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Aktif (7 gün)</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($statistics['active_users_with_token']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Token olan aktif</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">⚡</span>
                </div>
            </div>
        </div>

        <!-- Son 7 Gün Token -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Yeni Token (7 gün)</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ number_format($statistics['recent_tokens']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Son güncellenen</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">🆕</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cihaz Dağılımı -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Cihaz Tipi İstatistikleri -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">📱 Cihaz Dağılımı</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Android -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <span class="text-xl">🤖</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Android</p>
                                <p class="text-sm text-gray-500">{{ number_format($statistics['android_users']) }} kullanıcı</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600">
                                {{ $statistics['users_with_token'] > 0 ? round(($statistics['android_users'] / $statistics['users_with_token']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    </div>

                    <!-- iOS -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <span class="text-xl">🍎</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">iOS</p>
                                <p class="text-sm text-gray-500">{{ number_format($statistics['ios_users']) }} kullanıcı</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-600">
                                {{ $statistics['users_with_token'] > 0 ? round(($statistics['ios_users'] / $statistics['users_with_token']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    </div>

                    <!-- Web -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="text-xl">🌐</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Web</p>
                                <p class="text-sm text-gray-500">{{ number_format($statistics['web_users']) }} kullanıcı</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-blue-600">
                                {{ $statistics['users_with_token'] > 0 ? round(($statistics['web_users'] / $statistics['users_with_token']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Token Güncellemeleri -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">🕐 Son Token Güncellemeleri</h2>
            </div>
            <div class="p-6">
                @if($recentTokens->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentTokens as $user)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                        {{ $user->device_type === 'android' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $user->device_type === 'ios' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $user->device_type === 'web' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ ucfirst($user->device_type) }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">{{ $user->fcm_token_updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Henüz token güncellemesi yok</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Hızlı İşlemler -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">⚡ Hızlı İşlemler</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Test Gönder -->
                <a href="{{ route('admin.push-notifications.test') }}" 
                   class="p-4 border-2 border-blue-200 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                            <span class="text-2xl">🧪</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Test Gönder</p>
                            <p class="text-sm text-gray-600">Tek kullanıcıya test push</p>
                        </div>
                    </div>
                </a>

                <!-- Token Listesi -->
                <a href="{{ route('admin.push-notifications.tokens') }}" 
                   class="p-4 border-2 border-gray-200 rounded-lg hover:border-gray-400 hover:bg-gray-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                            <span class="text-2xl">📋</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Token Listesi</p>
                            <p class="text-sm text-gray-600">Tüm FCM token'ları</p>
                        </div>
                    </div>
                </a>

                <!-- Toplu Bildirim -->
                <a href="{{ route('admin.notifications.create') }}" 
                   class="p-4 border-2 border-green-200 rounded-lg hover:border-green-400 hover:bg-green-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                            <span class="text-2xl">📢</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Toplu Bildirim</p>
                            <p class="text-sm text-gray-600">Segment'e push gönder</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Bilgi Kutusu -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <span class="text-3xl">💡</span>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-blue-900 mb-2">Push Notification Nasıl Çalışır?</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Kullanıcılar mobil uygulamada FCM token'larını kaydeder</li>
                    <li>• Admin panelden test veya toplu bildirim gönderebilirsin</li>
                    <li>• Bildirimler queue sistemi ile asenkron gönderilir</li>
                    <li>• Firebase Console'dan detaylı istatistikleri görebilirsin</li>
                </ul>
                <div class="mt-4">
                    <a href="/PUSH-NOTIFICATION-KURULUM.md" target="_blank" 
                       class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                        📖 Kurulum Kılavuzunu Oku →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
