@extends('admin.layout')

@section('title', 'Cihaz Yönetimi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Cihaz Yönetimi</h1>
    <p class="text-gray-600 mt-1">Kullanıcı cihaz ve hassasiyet ayarlarını görüntüle ve düzenle</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('admin.devices.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cihaz adı, kullanıcı..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>

            <!-- FPS Setting -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">FPS Ayarı</label>
                <select name="fps_setting" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Tümü</option>
                    @foreach($fpsSettings as $fps)
                        <option value="{{ $fps }}" {{ request('fps_setting') == $fps ? 'selected' : '' }}>
                            {{ $fps }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Gyro -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gyro</label>
                <select name="gyro_enabled" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Tümü</option>
                    <option value="yes" {{ request('gyro_enabled') == 'yes' ? 'selected' : '' }}>Açık</option>
                    <option value="no" {{ request('gyro_enabled') == 'no' ? 'selected' : '' }}>Kapalı</option>
                </select>
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                Filtrele
            </button>
            <a href="{{ route('admin.devices.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Temizle
            </a>
        </div>
    </form>
</div>

<!-- Devices List -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cihaz</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grafik</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">FPS</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gyro</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($devices as $device)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $device->user->name }}</div>
                    <div class="text-sm text-gray-500">{{ $device->user->profile->nickname ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    {{ $device->device_name ?? '-' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    {{ $device->graphics_settings ?? '-' }}
                </td>
                <td class="px-6 py-4 text-sm">
                    @if($device->fps_setting)
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                            {{ $device->fps_setting }}
                        </span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm">
                    @if($device->gyro_enabled)
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Açık</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Kapalı</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm space-x-2">
                    <a href="{{ route('admin.devices.edit', $device->id) }}" 
                        class="text-orange-600 hover:text-orange-800">
                        Düzenle
                    </a>
                    <form action="{{ route('admin.devices.destroy', $device->id) }}" 
                        method="POST" class="inline"
                        onsubmit="return confirm('Cihaz kaydı silinecek. Emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            Sil
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    Cihaz kaydı bulunamadı
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($devices->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $devices->links() }}
    </div>
    @endif
</div>
@endsection
