@extends('layouts.app')

@section('title', 'Cihazlar ve Hassasiyet Ayarları')

@section('content')
<!-- Arka Plan -->
<div class="fixed inset-0 z-0">
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
         style="background-image: url('{{ asset('arkaplan/13.jpg') }}');">
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/95 via-gray-900/90 to-gray-900/95"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/10 via-transparent to-blue-600/10"></div>
</div>

<div class="relative z-10 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Modern Header with Stats -->
        <div class="relative mb-12 animate-fade-in">
            <!-- Background Decoration -->
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 via-blue-500/20 to-purple-500/20 rounded-3xl blur-3xl"></div>
            
            <div class="relative bg-black/70 backdrop-blur-xl rounded-3xl border-2 border-cyan-500/40 shadow-2xl overflow-hidden">
                <!-- Animated Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.1) 35px, rgba(255,255,255,.1) 70px);"></div>
                </div>
                
                <div class="relative p-8 md:p-12">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                        <!-- Left: Title & Description -->
                        <div class="flex-1 text-center md:text-left">
                            <div class="inline-flex items-center gap-4 mb-4">
                                <div class="relative">
                                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-3xl blur-xl opacity-70 animate-pulse"></div>
                                    <div class="relative w-20 h-20 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-3xl flex items-center justify-center shadow-2xl shadow-cyan-500/50">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h1 class="text-5xl md:text-6xl font-black text-cyan-400 drop-shadow-[0_0_30px_rgba(34,211,238,0.8)]" style="text-shadow: 0 0 20px rgba(34,211,238,0.8), 0 0 40px rgba(59,130,246,0.6), 0 4px 8px rgba(0,0,0,0.8);">
                                        📱 CİHAZLAR & HASSASİYET
                                    </h1>
                                    <p class="text-white text-lg font-bold drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)] mt-2">
                                        Pro oyuncuların ayarlarını keşfet ve kendi ayarlarını paylaş! 🎮
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Quick Stats -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-gradient-to-br from-cyan-500/20 to-cyan-600/20 border-2 border-cyan-500/40 rounded-2xl p-5 text-center shadow-lg shadow-cyan-500/30 hover:scale-105 transition-transform">
                                <div class="text-4xl font-black text-cyan-300 drop-shadow-lg">{{ $devices->total() }}</div>
                                <div class="text-sm text-white font-bold mt-1">Cihaz</div>
                            </div>
                            <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 border-2 border-blue-500/40 rounded-2xl p-5 text-center shadow-lg shadow-blue-500/30 hover:scale-105 transition-transform">
                                <div class="text-4xl font-black text-blue-300 drop-shadow-lg">{{ $deviceNames->count() }}</div>
                                <div class="text-sm text-white font-bold mt-1">Model</div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-500/20 to-purple-600/20 border-2 border-purple-500/40 rounded-2xl p-5 text-center shadow-lg shadow-purple-500/30 hover:scale-105 transition-transform">
                                <div class="text-4xl font-black text-purple-300 drop-shadow-lg">{{ $devices->where('gyro_enabled', true)->count() }}</div>
                                <div class="text-sm text-white font-bold mt-1">Gyro</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="mb-8">
            <div class="bg-black/70 backdrop-blur-xl rounded-2xl border-2 border-cyan-500/40 shadow-2xl overflow-hidden">
                <!-- Filter Header -->
                <div class="bg-gradient-to-r from-cyan-600/30 to-blue-600/30 px-6 py-5 border-b-2 border-cyan-500/40">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/50">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white drop-shadow-lg">Gelişmiş Filtreler</h3>
                                <p class="text-sm text-cyan-300 font-semibold">Aradığın cihazı bul</p>
                            </div>
                        </div>
                        @if(request()->hasAny(['device_name', 'fps_setting', 'gyro_enabled', 'graphics_settings']))
                        <a href="{{ route('devices.index') }}" class="flex items-center gap-2 px-4 py-2 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 text-red-400 rounded-xl text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Filtreleri Temizle
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Filter Form -->
                <form method="GET" action="{{ route('devices.index') }}" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Cihaz Adı -->
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-300">
                                <span class="text-lg">📱</span>
                                Cihaz Adı
                            </label>
                            <div class="relative">
                                <input type="text" name="device_name" value="{{ request('device_name') }}"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-700/50 border border-gray-600 text-white rounded-xl focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition placeholder-gray-500"
                                    placeholder="iPhone, Samsung...">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- FPS -->
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-300">
                                <span class="text-lg">⚡</span>
                                FPS Ayarı
                            </label>
                            <select name="fps_setting" class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 text-white rounded-xl focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition appearance-none cursor-pointer">
                                <option value="">Tüm FPS Değerleri</option>
                                @foreach(['30 FPS', '40 FPS', '60 FPS', '90 FPS', '120 FPS'] as $fps)
                                    <option value="{{ $fps }}" {{ request('fps_setting') == $fps ? 'selected' : '' }}>
                                        {{ $fps }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Gyro -->
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-300">
                                <span class="text-lg">🎯</span>
                                Gyro Durumu
                            </label>
                            <select name="gyro_enabled" class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 text-white rounded-xl focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition appearance-none cursor-pointer">
                                <option value="">Tüm Durumlar</option>
                                <option value="1" {{ request('gyro_enabled') == '1' ? 'selected' : '' }}>✅ Gyro Açık</option>
                                <option value="0" {{ request('gyro_enabled') == '0' ? 'selected' : '' }}>❌ Gyro Kapalı</option>
                            </select>
                        </div>

                        <!-- Grafik Ayarları -->
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-300">
                                <span class="text-lg">🎨</span>
                                Grafik Kalitesi
                            </label>
                            <select name="graphics_settings" class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 text-white rounded-xl focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition appearance-none cursor-pointer">
                                <option value="">Tüm Ayarlar</option>
                                @foreach(['Smooth', 'Balanced', 'HD', 'HDR', 'Ultra HD'] as $graphics)
                                    <option value="{{ $graphics }}" {{ request('graphics_settings') == $graphics ? 'selected' : '' }}>
                                        {{ $graphics }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-cyan-500/30 hover:shadow-cyan-500/50 transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Filtrele
                        </button>
                        
                        @auth
                        <a href="{{ route('profile.device') }}" class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Cihazımı Ekle
                        </a>
                        @endauth
                    </div>
                </form>

                <!-- Popular Devices Quick Filter -->
                @if($deviceNames->isNotEmpty())
                <div class="px-6 pb-6">
                    <div class="bg-gradient-to-r from-gray-700/30 to-gray-600/30 rounded-xl p-4 border border-gray-600/50">
                        <p class="text-sm font-semibold text-gray-300 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Popüler Cihazlar
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($deviceNames->take(8) as $deviceName)
                            <a href="{{ route('devices.index', ['device_name' => $deviceName]) }}" 
                                class="px-4 py-2 bg-gray-700/50 hover:bg-cyan-500/20 border border-gray-600 hover:border-cyan-500/50 text-gray-300 hover:text-cyan-400 rounded-lg text-sm font-medium transition-all duration-300">
                                {{ $deviceName }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Cihaz Listesi -->
        @if($devices->isEmpty())
            <!-- Empty State -->
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/5 via-blue-500/5 to-purple-500/5 rounded-3xl blur-3xl"></div>
                <div class="relative bg-gray-800/80 backdrop-blur-xl rounded-3xl border border-gray-700/50 shadow-2xl p-16 text-center">
                    <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-gray-700 to-gray-800 rounded-3xl mb-8 shadow-2xl">
                        <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-3xl font-black text-white mb-3">Henüz Cihaz Bulunamadı</h3>
                    <p class="text-gray-400 text-lg mb-8">İlk cihaz ayarlarını paylaşan sen ol! 🚀</p>
                    @auth
                    <a href="{{ route('profile.device') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg shadow-cyan-500/30 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Cihazımı Ekle
                    </a>
                    @endauth
                </div>
            </div>
        @else
            <!-- Devices Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($devices as $device)
                <div class="group relative">
                    <!-- Hover Glow Effect -->
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-2xl opacity-0 group-hover:opacity-20 blur transition duration-500"></div>
                    
                    <div class="relative bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-700/50 hover:border-cyan-500/50 shadow-xl hover:shadow-2xl hover:shadow-cyan-500/20 transition-all duration-500 overflow-hidden transform hover:-translate-y-1">
                        <!-- Device Header -->
                        <div class="relative bg-gradient-to-br from-cyan-500 via-blue-600 to-purple-600 p-6 overflow-hidden">
                            <!-- Animated Background -->
                            <div class="absolute inset-0 opacity-20">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            </div>
                            
                            <!-- Badges -->
                            <div class="absolute top-3 right-3 flex gap-2">
                                @if($device->gyro_enabled)
                                <div class="flex items-center gap-1 px-3 py-1.5 bg-green-500/90 backdrop-blur-sm rounded-full text-white text-xs font-bold shadow-lg">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Gyro
                                </div>
                                @endif
                                @if($device->fps_setting && str_contains($device->fps_setting, '90') || str_contains($device->fps_setting, '120'))
                                <div class="flex items-center gap-1 px-3 py-1.5 bg-yellow-500/90 backdrop-blur-sm rounded-full text-white text-xs font-bold shadow-lg">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                                    </svg>
                                    Pro
                                </div>
                                @endif
                            </div>
                            
                            <div class="relative flex items-center gap-4">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xl font-black text-white mb-1 truncate">{{ $device->device_name }}</h3>
                                    <p class="text-sm text-white/90 font-medium">Oyun Ayarları</p>
                                </div>
                            </div>
                        </div>

                        <!-- Device Content -->
                        <div class="p-6 space-y-4">
                            <!-- Settings Grid -->
                            <div class="grid grid-cols-2 gap-3">
                                @if($device->graphics_settings)
                                <div class="bg-gradient-to-br from-gray-700/50 to-gray-700/30 rounded-xl p-3 border border-gray-600/50 hover:border-cyan-500/50 transition-colors">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs">🎨</span>
                                        <p class="text-xs text-gray-400 font-medium">Grafik</p>
                                    </div>
                                    <p class="text-sm font-bold text-white">{{ $device->graphics_settings }}</p>
                                </div>
                                @endif

                                @if($device->fps_setting)
                                <div class="bg-gradient-to-br from-cyan-500/10 to-blue-500/10 rounded-xl p-3 border border-cyan-500/30 hover:border-cyan-500/50 transition-colors">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs">⚡</span>
                                        <p class="text-xs text-gray-400 font-medium">FPS</p>
                                    </div>
                                    <p class="text-sm font-bold text-cyan-400">{{ $device->fps_setting }}</p>
                                </div>
                                @endif

                                <div class="bg-gradient-to-br from-{{ $device->gyro_enabled ? 'green' : 'red' }}-500/10 to-{{ $device->gyro_enabled ? 'green' : 'red' }}-500/5 rounded-xl p-3 border border-{{ $device->gyro_enabled ? 'green' : 'red' }}-500/30 hover:border-{{ $device->gyro_enabled ? 'green' : 'red' }}-500/50 transition-colors">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs">🎯</span>
                                        <p class="text-xs text-gray-400 font-medium">Gyro</p>
                                    </div>
                                    <p class="text-sm font-bold {{ $device->gyro_enabled ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $device->gyro_enabled ? 'Açık' : 'Kapalı' }}
                                    </p>
                                </div>

                                @if($device->sensitivity_settings)
                                <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 rounded-xl p-3 border border-purple-500/30 hover:border-purple-500/50 transition-colors">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs">🎚️</span>
                                        <p class="text-xs text-gray-400 font-medium">Hassasiyet</p>
                                    </div>
                                    <p class="text-sm font-bold text-purple-400">Özel Ayar</p>
                                </div>
                                @endif
                            </div>

                            <!-- User Info & Action -->
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-700/30 to-gray-600/30 rounded-xl border border-gray-600/50 hover:border-gray-500/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl blur opacity-50"></div>
                                        <div class="relative w-11 h-11 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-white font-black text-sm shadow-lg">
                                            {{ strtoupper(substr($device->user->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium">Paylaşan</p>
                                        <p class="text-sm font-bold text-white">{{ $device->user->name }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('devices.show', $device->id) }}" 
                                    class="flex items-center gap-1 px-4 py-2 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/30 hover:border-cyan-500/50 text-cyan-400 hover:text-cyan-300 rounded-lg font-bold text-sm transition-all duration-300 group/btn">
                                    Detay
                                    <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Modern Pagination -->
            <div class="flex justify-center">
                <div class="bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-2">
                    {{ $devices->links() }}
                </div>
            </div>
        @endif

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
            <!-- Cihaz Paylaş Kartı -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl opacity-50 group-hover:opacity-75 blur transition duration-500"></div>
                <div class="relative bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 text-white overflow-hidden">
                    <!-- Animated Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.1) 35px, rgba(255,255,255,.1) 70px);"></div>
                    </div>
                    
                    <div class="relative flex items-start gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-black mb-3 flex items-center gap-2">
                                💡 Cihazını Paylaş
                            </h3>
                            <p class="text-white/95 mb-4 leading-relaxed">
                                Kendi cihaz ve hassasiyet ayarlarını paylaşarak diğer oyunculara yardımcı ol! 
                                Topluluk için değerli bir katkı sağla.
                            </p>
                            @auth
                            <a href="{{ route('profile.device') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-6 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Hemen Ekle
                            </a>
                            @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-6 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Giriş Yap
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- İpuçları Kartı -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-2xl opacity-50 group-hover:opacity-75 blur transition duration-500"></div>
                <div class="relative bg-gradient-to-br from-cyan-600 to-blue-600 rounded-2xl shadow-2xl p-8 text-white overflow-hidden">
                    <!-- Animated Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0" style="background-image: repeating-linear-gradient(-45deg, transparent, transparent 35px, rgba(255,255,255,.1) 35px, rgba(255,255,255,.1) 70px);"></div>
                    </div>
                    
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black">🎯 Pro İpuçları</h3>
                        </div>
                        <ul class="space-y-3 text-white/95">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Yüksek FPS için grafik ayarlarını düşür</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Gyro hassasiyetini kademeli olarak artır</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Farklı cihaz ayarlarını test et ve karşılaştır</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
