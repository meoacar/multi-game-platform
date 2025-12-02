@extends('admin.layout')

@section('title', 'FCM Token Listesi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📋 FCM Token Listesi</h1>
            <p class="text-gray-600 mt-1">Tüm kullanıcıların FCM token bilgileri</p>
        </div>
        <a href="{{ route('admin.push-notifications.index') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            ← Geri Dön
        </a>
    </div>

    <!-- Filtreler -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Arama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Arama</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="İsim veya email..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Cihaz Tipi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cihaz Tipi</label>
                <select name="device_type" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tümü</option>
                    <option value="android" {{ request('device_type') === 'android' ? 'selected' : '' }}>Android</option>
                    <option value="ios" {{ request('device_type') === 'ios' ? 'selected' : '' }}>iOS</option>
                    <option value="web" {{ request('device_type') === 'web' ? 'selected' : '' }}>Web</option>
                </select>
            </div>

            <!-- Aktif Kullanıcılar -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                <select name="active_only" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tümü</option>
                    <option value="1" {{ request('active_only') === '1' ? 'selected' : '' }}>Sadece Aktif (7 gün)</option>
                </select>
            </div>

            <!-- Butonlar -->
            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    🔍 Filtrele
                </button>
                <a href="{{ route('admin.push-notifications.tokens') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    ✕
                </a>
            </div>
        </form>
    </div>

    <!-- Token Listesi -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kullanıcı
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cihaz Tipi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Token Güncellenme
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Son Giriş
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $user->device_type === 'android' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $user->device_type === 'ios' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $user->device_type === 'web' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ $user->device_type === 'android' ? '🤖' : '' }}
                                    {{ $user->device_type === 'ios' ? '🍎' : '' }}
                                    {{ $user->device_type === 'web' ? '🌐' : '' }}
                                    {{ ucfirst($user->device_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->fcm_token_updated_at ? $user->fcm_token_updated_at->diffForHumans() : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($user->last_login_at)
                                    <span class="{{ $user->last_login_at->gt(now()->subDays(7)) ? 'text-green-600 font-medium' : '' }}">
                                        {{ $user->last_login_at->diffForHumans() }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('admin.push-notifications.delete-token', $user->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Bu kullanıcının FCM token\'ını silmek istediğinden emin misin?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900">
                                        🗑️ Token Sil
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="text-4xl">📭</span>
                                    <p class="text-lg font-medium">FCM token bulunamadı</p>
                                    <p class="text-sm">Henüz hiçbir kullanıcı FCM token kaydetmemiş</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Toplu İşlemler -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-900 mb-4">🧹 Toplu İşlemler</h3>
        <form action="{{ route('admin.push-notifications.cleanup-tokens') }}" 
              method="POST" 
              onsubmit="return confirm('Eski token\'ları temizlemek istediğinden emin misin?')"
              class="flex items-end gap-4">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kaç gün önce güncellenmeyen token'ları temizle?
                </label>
                <select name="days" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="30">30 gün</option>
                    <option value="60">60 gün</option>
                    <option value="90" selected>90 gün</option>
                    <option value="180">180 gün</option>
                </select>
            </div>
            <button type="submit" 
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                🗑️ Eski Token'ları Temizle
            </button>
        </form>
        <p class="text-sm text-gray-500 mt-2">
            ⚠️ Bu işlem geri alınamaz. Kullanıcılar yeniden token kaydetmek zorunda kalacak.
        </p>
    </div>
</div>
@endsection
