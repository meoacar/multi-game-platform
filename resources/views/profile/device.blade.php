@extends('layouts.app')

@section('title', 'Cihaz Bilgileri')

@section('content')
<!-- Arka Plan -->
<div class="fixed inset-0 z-0">
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
         style="background-image: url('{{ asset('arkaplan/14.jpg') }}');">
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/95 via-gray-900/90 to-gray-900/95"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-purple-600/10 via-transparent to-pink-600/10"></div>
</div>

<div class="relative z-10 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Header -->
        <div class="text-center mb-12 animate-fade-in">
            <div class="inline-block mb-6">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500 via-pink-500 to-red-600 rounded-3xl blur-xl opacity-70 animate-pulse"></div>
                    <div class="relative w-24 h-24 bg-gradient-to-br from-purple-500 via-pink-500 to-red-600 rounded-3xl flex items-center justify-center shadow-2xl shadow-purple-500/50">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="inline-block bg-black/60 backdrop-blur-xl rounded-3xl p-8 border-2 border-purple-500/40 shadow-2xl">
                <h1 class="text-6xl font-black text-purple-300 mb-4 drop-shadow-[0_0_30px_rgba(216,180,254,0.8)]" style="text-shadow: 0 0 20px rgba(216,180,254,0.8), 0 0 40px rgba(240,171,252,0.6), 0 4px 8px rgba(0,0,0,0.8);">
                    📱 CİHAZ BİLGİLERİ
                </h1>
                <p class="text-white text-xl font-bold drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)]">Cihazını ve hassasiyet ayarlarını paylaş!</p>
            </div>
        </div>

        <div class="bg-black/70 backdrop-blur-xl rounded-3xl border-2 border-purple-500/40 shadow-2xl overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-purple-600/30 via-pink-600/30 to-red-600/30 px-8 py-6 border-b-2 border-purple-500/40">
                <h2 class="text-2xl font-black text-white drop-shadow-lg flex items-center gap-3">
                    <span class="text-3xl">⚙️</span>
                    Cihaz Ayarları
                </h2>
                <p class="text-purple-200 text-sm mt-1 font-semibold">Tüm detayları doldur ve topluluğa katkı sağla</p>
            </div>

            <form action="{{ route('profile.device.update') }}" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <!-- Cihaz Adı -->
                <div class="group">
                    <label class="flex items-center gap-2 text-sm font-bold text-white mb-3">
                        <span class="text-2xl">📱</span>
                        <span>Cihaz Adı</span>
                        <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="device_name" value="{{ old('device_name', $device->device_name ?? '') }}" required
                        class="w-full px-5 py-4 bg-white/5 border-2 border-purple-500/30 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all group-hover:border-purple-500/50 text-lg font-semibold @error('device_name') border-red-500 @enderror"
                        placeholder="Örn: iPhone 13 Pro, Samsung Galaxy S21, Poco X6 Pro">
                    @error('device_name')
                        <p class="text-red-400 text-sm mt-2 font-semibold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Grafik Ayarları -->
                    <div class="group">
                        <label class="flex items-center gap-2 text-sm font-bold text-white mb-3">
                            <span class="text-2xl">🎨</span>
                            <span>Grafik Ayarları</span>
                        </label>
                        <select name="graphics_settings" class="w-full px-5 py-4 bg-gray-900/90 border-2 border-purple-500/50 rounded-xl text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all group-hover:border-purple-500/70 text-lg font-bold appearance-none cursor-pointer" style="color-scheme: dark;">
                            <option value="" class="bg-gray-900 text-gray-400">Seçiniz...</option>
                            @foreach(['Smooth', 'Balanced', 'HD', 'HDR', 'Ultra HD', 'Extreme'] as $graphics)
                                <option value="{{ $graphics }}" class="bg-gray-900 text-white font-bold" {{ old('graphics_settings', $device->graphics_settings ?? '') == $graphics ? 'selected' : '' }}>
                                    {{ $graphics }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- FPS Ayarı -->
                    <div class="group">
                        <label class="flex items-center gap-2 text-sm font-bold text-white mb-3">
                            <span class="text-2xl">⚡</span>
                            <span>FPS Ayarı</span>
                        </label>
                        <select name="fps_setting" class="w-full px-5 py-4 bg-gray-900/90 border-2 border-purple-500/50 rounded-xl text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all group-hover:border-purple-500/70 text-lg font-bold appearance-none cursor-pointer" style="color-scheme: dark;">
                            <option value="" class="bg-gray-900 text-gray-400">Seçiniz...</option>
                            @foreach(['30 FPS', '40 FPS', '60 FPS', '90 FPS', '120 FPS'] as $fps)
                                <option value="{{ $fps }}" class="bg-gray-900 text-white font-bold" {{ old('fps_setting', $device->fps_setting ?? '') == $fps ? 'selected' : '' }}>
                                    {{ $fps }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Gyro Toggle -->
                <div class="bg-gradient-to-r from-purple-500/10 to-pink-500/10 border-2 border-purple-500/30 rounded-xl p-6 hover:border-purple-500/50 transition-all">
                    <label class="flex items-center justify-between cursor-pointer group">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">🎯</span>
                            <div>
                                <p class="text-lg font-black text-white">Gyro Aktif</p>
                                <p class="text-sm text-purple-200">Gyroscope hassasiyet kontrolü</p>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="checkbox" name="gyro_enabled" value="1" 
                                {{ old('gyro_enabled', $device->gyro_enabled ?? false) ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-16 h-8 bg-gray-700 rounded-full peer-checked:bg-gradient-to-r peer-checked:from-green-500 peer-checked:to-emerald-600 transition-all duration-300 shadow-inner"></div>
                            <div class="absolute left-1 top-1 w-6 h-6 bg-white rounded-full transition-all duration-300 peer-checked:translate-x-8 shadow-lg"></div>
                        </div>
                    </label>
                </div>

                <!-- Hassasiyet Ayarları -->
                <div class="group">
                    <label class="flex items-center gap-2 text-sm font-bold text-white mb-3">
                        <span class="text-2xl">🎚️</span>
                        <span>Hassasiyet Ayarları (JSON)</span>
                    </label>
                    <textarea name="sensitivity_settings" rows="8"
                        class="w-full px-5 py-4 bg-gray-900/50 border-2 border-purple-500/30 rounded-xl text-green-400 placeholder-gray-500 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all group-hover:border-purple-500/50 font-mono text-sm"
                        placeholder='{"camera": 100, "ads": 80, "scope": 60, "gyro": 300}'>{{ old('sensitivity_settings', $device->sensitivity_settings ? json_encode($device->sensitivity_settings, JSON_PRETTY_PRINT) : '') }}</textarea>
                    <p class="text-xs text-purple-300 mt-2 flex items-center gap-1 font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        JSON formatında hassasiyet ayarlarınızı girebilirsiniz
                    </p>
                </div>

                <!-- Notlar -->
                <div class="group">
                    <label class="flex items-center gap-2 text-sm font-bold text-white mb-3">
                        <span class="text-2xl">📝</span>
                        <span>Notlar & İpuçları</span>
                    </label>
                    <textarea name="notes" rows="5"
                        class="w-full px-5 py-4 bg-white/5 border-2 border-purple-500/30 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all group-hover:border-purple-500/50 resize-none"
                        placeholder="Cihaz ve ayarlarınız hakkında notlar, ipuçları veya öneriler...">{{ old('notes', $device->notes ?? '') }}</textarea>
                </div>

                <!-- Butonlar -->
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button type="submit" 
                        class="flex-1 group relative overflow-hidden bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 text-white px-8 py-5 rounded-2xl font-black text-xl shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 hover:scale-105">
                        <span class="relative z-10 flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Kaydet</span>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-pink-600 via-red-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                    
                    <a href="{{ route('profile.index') }}" 
                        class="flex-1 group relative overflow-hidden bg-white/5 hover:bg-white/10 text-white px-8 py-5 rounded-2xl font-black text-xl border-2 border-white/20 hover:border-white/40 transition-all duration-300 hover:scale-105 flex items-center justify-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>İptal</span>
                    </a>
                </div>
            </form>

            <!-- İpuçları Kartı -->
            <div class="bg-gradient-to-r from-cyan-600/20 to-blue-600/20 border-t-2 border-cyan-500/40 p-8">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-cyan-500/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white mb-3">💡 Pro İpuçları</h3>
                        <ul class="space-y-2 text-cyan-100">
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-1">•</span>
                                <span>Cihaz modelini tam olarak yazın (örn: iPhone 13 Pro Max)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-1">•</span>
                                <span>Hassasiyet ayarlarını JSON formatında paylaşın</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-1">•</span>
                                <span>Notlar bölümüne özel ipuçlarınızı ekleyin</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
