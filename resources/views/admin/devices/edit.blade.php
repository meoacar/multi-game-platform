@extends('admin.layout')

@section('title', 'Cihaz Düzenle')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
        <a href="{{ route('admin.devices.index') }}" class="hover:text-orange-600">Cihazlar</a>
        <span>/</span>
        <span>Düzenle</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Cihaz Düzenle</h1>
    <p class="text-gray-600 mt-1">{{ $device->user->name }} - {{ $device->user->email }}</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.devices.update', $device->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Device Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Cihaz Adı
                </label>
                <input type="text" name="device_name" value="{{ old('device_name', $device->device_name) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('device_name') border-red-500 @enderror">
                @error('device_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Graphics Settings -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Grafik Ayarları
                </label>
                <input type="text" name="graphics_settings" value="{{ old('graphics_settings', $device->graphics_settings) }}"
                    placeholder="Örn: HDR + Extreme"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('graphics_settings') border-red-500 @enderror">
                @error('graphics_settings')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- FPS Setting -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    FPS Ayarı
                </label>
                <select name="fps_setting" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Seçiniz</option>
                    @foreach(['30 FPS', '40 FPS', '60 FPS', '90 FPS', '120 FPS'] as $fps)
                        <option value="{{ $fps }}" {{ old('fps_setting', $device->fps_setting) == $fps ? 'selected' : '' }}>
                            {{ $fps }}
                        </option>
                    @endforeach
                </select>
                @error('fps_setting')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gyro Enabled -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Gyro
                </label>
                <select name="gyro_enabled" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="0" {{ old('gyro_enabled', $device->gyro_enabled) == 0 ? 'selected' : '' }}>Kapalı</option>
                    <option value="1" {{ old('gyro_enabled', $device->gyro_enabled) == 1 ? 'selected' : '' }}>Açık</option>
                </select>
                @error('gyro_enabled')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Sensitivity Settings (JSON) -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Hassasiyet Ayarları (JSON)
            </label>
            <textarea name="sensitivity_settings" rows="4"
                placeholder='{"general": 80, "ads": 60, "gyro": 300}'
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 font-mono text-sm @error('sensitivity_settings') border-red-500 @enderror">{{ old('sensitivity_settings', $device->sensitivity_settings) }}</textarea>
            @error('sensitivity_settings')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Notes -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Notlar
            </label>
            <textarea name="notes" rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 @error('notes') border-red-500 @enderror">{{ old('notes', $device->notes) }}</textarea>
            @error('notes')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                Kaydet
            </button>
            <a href="{{ route('admin.devices.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                İptal
            </a>
        </div>
    </form>
</div>
@endsection
