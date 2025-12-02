@extends('layouts.app')

@section('title', $device->device_name . ' - Cihaz Detayı')

@section('content')
<!-- PUBG Mobile Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden bg-black">
    <div class="absolute inset-0" 
         style="background-image: url('/arkaplan/fFetCZ0H0O_x8QSHv6LGz.png'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.12;">
    </div>
    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/15 via-cyan-900/15 to-teal-900/15"></div>
</div>

<div class="relative z-10 min-h-screen py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center gap-2 text-sm">
                <li>
                    <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Ana Sayfa</a>
                </li>
                <li class="text-gray-600">/</li>
                <li>
                    <a href="{{ route('devices.index') }}" class="text-gray-400 hover:text-white transition-colors">Cihazlar</a>
                </li>
                <li class="text-gray-600">/</li>
                <li class="text-white font-medium">{{ $device->device_name }}</li>
            </ol>
        </nav>

        <!-- Ana Kart -->
        <div class="bg-gradient-to-br from-gray-900/90 via-gray-800/90 to-gray-900/90 backdrop-blur-xl rounded-3xl border border-gray-700/50 shadow-2xl overflow-hidden">
            
            <!-- Header -->
            <div class="relative p-8 border-b border-gray-700/50">
                <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
                    <div class="absolute top-0 left-1/4 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl"></div>
                </div>
                
                <div class="relative">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="text-5xl flex-shrink-0">📱</div>
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight">
                                    <span class="bg-gradient-to-r from-cyan-400 via-blue-400 to-teal-400 bg-clip-text text-transparent">
                                        {{ $device->device_name }}
                                    </span>
                                </h1>
                            </div>
                            <div class="flex items-center gap-4 text-sm">
                                <a href="{{ route('profile.show', $device->user->id) }}" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors group">
                                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold group-hover:scale-110 transition-transform">
                                        {{ substr($device->user->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium">{{ $device->user->name }}</span>
                                </a>
                                <span class="text-gray-600">•</span>
                                <span class="text-gray-400">{{ $device->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        
                        <!-- Gyro Badge -->
                        <div class="flex-shrink-0">
                            @if($device->gyro_enabled)
                                <div class="px-4 py-2 bg-green-500/20 border border-green-500/30 rounded-xl text-green-400 font-bold">
                                    ✅ Gyro Açık
                                </div>
                            @else
                                <div class="px-4 py-2 bg-gray-500/20 border border-gray-500/30 rounded-xl text-gray-400 font-bold">
                                    ❌ Gyro Kapalı
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- İçerik -->
            <div class="p-8 space-y-8">
                
                <!-- Temel Ayarlar -->
                <div>
                    <h3 class="text-2xl font-black text-white mb-6 flex items-center gap-2">
                        <span class="text-3xl">⚙️</span>
                        Temel Ayarlar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($device->graphics_settings)
                        <div class="bg-gradient-to-br from-blue-500/10 to-cyan-500/10 backdrop-blur-sm rounded-2xl p-6 border border-blue-500/20 hover:border-blue-500/40 transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Grafik Ayarları</p>
                                    <p class="text-2xl font-black text-white">{{ $device->graphics_settings }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($device->fps_setting)
                        <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 backdrop-blur-sm rounded-2xl p-6 border border-purple-500/20 hover:border-purple-500/40 transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">FPS Ayarı</p>
                                    <p class="text-2xl font-black text-white">{{ $device->fps_setting }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="bg-gradient-to-br from-teal-500/10 to-green-500/10 backdrop-blur-sm rounded-2xl p-6 border border-teal-500/20 hover:border-teal-500/40 transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-teal-500/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Gyro Durumu</p>
                                    <p class="text-2xl font-black text-white">
                                        {{ $device->gyro_enabled ? 'Açık' : 'Kapalı' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-orange-500/10 to-red-500/10 backdrop-blur-sm rounded-2xl p-6 border border-orange-500/20 hover:border-orange-500/40 transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Eklenme Tarihi</p>
                                    <p class="text-lg font-bold text-white">{{ $device->created_at->format('d.m.Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hassasiyet Ayarları -->
                @if($device->sensitivity_settings)
                <div>
                    <h3 class="text-2xl font-black text-white mb-6 flex items-center gap-2">
                        <span class="text-3xl">🎯</span>
                        Hassasiyet Ayarları
                    </h3>
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
                        <div class="overflow-x-auto">
                            <pre class="text-sm text-cyan-400 font-mono">{{ json_encode($device->sensitivity_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notlar -->
                @if($device->notes)
                <div>
                    <h3 class="text-2xl font-black text-white mb-6 flex items-center gap-2">
                        <span class="text-3xl">📝</span>
                        Notlar
                    </h3>
                    <div class="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 backdrop-blur-sm rounded-2xl p-6 border border-yellow-500/20">
                        <p class="text-gray-300 leading-relaxed whitespace-pre-line">{{ $device->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="p-8 border-t border-gray-700/50 bg-gray-900/50">
                <div class="flex items-center justify-between">
                    <a href="{{ route('devices.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold rounded-xl transition-all transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cihazlar Listesine Dön
                    </a>
                    
                    <div class="text-sm text-gray-400">
                        <span class="font-semibold text-white">{{ $device->user->name }}</span> tarafından paylaşıldı
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
