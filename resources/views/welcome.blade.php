<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title>PUBG Mobile Topluluk Platformu - Takım Bul, Klan Kur, Paylaş</title>
    <meta name="description" content="Türkiye'nin en büyük PUBG Mobile topluluğu! {{ number_format($stats['total_users'] ?? 0) }}+ oyuncu, {{ number_format($stats['total_clans'] ?? 0) }}+ klan. Takım ara, klan kur, cihaz ayarlarını paylaş, turnuvalara katıl.">
    <meta name="keywords" content="pubg mobile, pubg mobile türkiye, pubg klan, pubg takım, lfg pubg, pubg mobile topluluk, pubg turnuva, pubg mobile ayarları, pubg mobile hassasiyet, pubg mobile cihaz ayarları">
    <meta name="author" content="PUBG Mobile Topluluk">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="PUBG Mobile Topluluk Platformu - Türkiye'nin En Büyük Topluluğu">
    <meta property="og:description" content="{{ number_format($stats['total_users'] ?? 0) }}+ oyuncu, {{ number_format($stats['total_clans'] ?? 0) }}+ klan. Takım ara, klan kur, turnuvalara katıl!">
    <meta property="og:image" content="{{ asset('images/og-home.jpg') }}">
    <meta property="og:site_name" content="PUBG Mobile Topluluk">
    <meta property="og:locale" content="tr_TR">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="PUBG Mobile Topluluk Platformu">
    <meta name="twitter:description" content="{{ number_format($stats['total_users'] ?? 0) }}+ oyuncu ile Türkiye'nin en büyük PUBG Mobile topluluğu!">
    <meta name="twitter:image" content="{{ asset('images/twitter-home.jpg') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="#F97316">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(249, 115, 22, 0.3); }
            50% { box-shadow: 0 0 40px rgba(249, 115, 22, 0.6); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-glow { animation: glow 2s ease-in-out infinite; }
    </style>
    
    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "PUBG Mobile Topluluk Platformu",
        "url": "{{ url('/') }}",
        "description": "Türkiye'nin en büyük PUBG Mobile topluluğu. Takım ara, klan kur, turnuvalara katıl.",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/') }}/arama?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "PUBG Mobile Topluluk",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo.png') }}",
        "description": "Türkiye'nin en büyük PUBG Mobile topluluğu",
        "sameAs": [
            "https://facebook.com/pubgmobiletopluluk",
            "https://twitter.com/pubgmobiletopluluk",
            "https://discord.gg/pubgmobiletopluluk"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "Customer Service",
            "availableLanguage": "Turkish"
        }
    }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white font-['Inter'] antialiased overflow-x-hidden">
    
    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-yellow-500/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Navigation -->
    <x-navbar />

    <!-- Hero Section -->
    <section class="relative z-10 pt-20 pb-32 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center space-y-8">
                <!-- Badge -->
                <div class="inline-flex items-center space-x-2 bg-gradient-to-r from-orange-500/10 to-red-500/10 border border-orange-500/20 rounded-full px-4 py-2 backdrop-blur-sm animate-float">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                    </span>
                    <span class="text-sm font-medium text-orange-300">Türkiye'nin En Büyük PUBG Mobile Topluluğu</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-tight">
                    <span class="block bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Takımını Bul,</span>
                    <span class="block bg-gradient-to-r from-orange-400 via-red-500 to-orange-600 bg-clip-text text-transparent mt-2">Zafere Ulaş!</span>
                </h1>

                <!-- Description -->
                <p class="text-xl text-gray-400 max-w-3xl mx-auto leading-relaxed">
                    PUBG Mobile oyuncuları için özel olarak tasarlanmış topluluk platformu. 
                    Takım ara, klan kur, cihaz ayarlarını paylaş ve topluluğun bir parçası ol.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    @auth
                        <a href="{{ route('lfg.index') }}" class="group relative px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl font-bold text-lg shadow-2xl shadow-orange-500/30 hover:shadow-orange-500/50 transition-all duration-300 transform hover:scale-105 w-full sm:w-auto">
                            <span class="relative z-10">🎯 İlanları Gör</span>
                        </a>
                        <a href="{{ route('clans.index') }}" class="px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl font-bold text-lg backdrop-blur-sm transition-all duration-300 transform hover:scale-105 w-full sm:w-auto">
                            👥 Klanları Keşfet
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="group relative px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl font-bold text-lg shadow-2xl shadow-orange-500/30 hover:shadow-orange-500/50 transition-all duration-300 transform hover:scale-105 w-full sm:w-auto animate-glow">
                            <span class="relative z-10">🚀 Hemen Başla</span>
                        </a>
                        <a href="#features" class="px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl font-bold text-lg backdrop-blur-sm transition-all duration-300 transform hover:scale-105 w-full sm:w-auto">
                            ✨ Özellikleri Keşfet
                        </a>
                    @endauth
                </div>

                <!-- Stats -->
                <div id="stats" class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-12 max-w-4xl mx-auto">
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        <div class="text-3xl font-black bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">{{ number_format($stats['total_users'] ?? 0) }}+</div>
                        <div class="text-sm text-gray-400 mt-1">Toplam Oyuncu</div>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        <div class="text-3xl font-black bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">{{ number_format($stats['total_clans'] ?? 0) }}+</div>
                        <div class="text-sm text-gray-400 mt-1">Aktif Klan</div>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        <div class="text-3xl font-black bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">{{ number_format($stats['active_lfg'] ?? 0) }}+</div>
                        <div class="text-sm text-gray-400 mt-1">LFG İlanı</div>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        <div class="text-3xl font-black bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">24/7</div>
                        <div class="text-sm text-gray-400 mt-1">Aktif Destek</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions Section -->
    <section class="relative z-10 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Quick Action 1 -->
                <a href="{{ route('lfg.index') }}" class="group relative bg-gradient-to-br from-orange-500/10 to-red-500/10 border border-orange-500/20 rounded-3xl p-8 hover:border-orange-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-orange-500/20 transform hover:-translate-y-2">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">🎯</div>
                    <h3 class="text-2xl font-bold mb-2">Takım Ara</h3>
                    <p class="text-gray-400 text-sm">Hemen oyun arkadaşı bul</p>
                    <div class="mt-4 flex items-center text-orange-400 font-semibold">
                        <span>Keşfet</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </div>
                </a>

                <!-- Quick Action 2 -->
                <a href="{{ route('clans.index') }}" class="group relative bg-gradient-to-br from-blue-500/10 to-purple-500/10 border border-blue-500/20 rounded-3xl p-8 hover:border-blue-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-blue-500/20 transform hover:-translate-y-2">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">👥</div>
                    <h3 class="text-2xl font-bold mb-2">Klan Bul</h3>
                    <p class="text-gray-400 text-sm">Güçlü klanlara katıl</p>
                    <div class="mt-4 flex items-center text-blue-400 font-semibold">
                        <span>Keşfet</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </div>
                </a>

                <!-- Quick Action 3 -->
                <a href="{{ route('tournaments.index') }}" class="group relative bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/20 rounded-3xl p-8 hover:border-green-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-green-500/20 transform hover:-translate-y-2">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">🎮</div>
                    <h3 class="text-2xl font-bold mb-2">Turnuvalar</h3>
                    <p class="text-gray-400 text-sm">Yarışmalara katıl</p>
                    <div class="mt-4 flex items-center text-green-400 font-semibold">
                        <span>Keşfet</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </div>
                </a>

                <!-- Quick Action 4 -->
                <a href="{{ route('devices.index') }}" class="group relative bg-gradient-to-br from-yellow-500/10 to-orange-500/10 border border-yellow-500/20 rounded-3xl p-8 hover:border-yellow-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-yellow-500/20 transform hover:-translate-y-2">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform duration-300">📱</div>
                    <h3 class="text-2xl font-bold mb-2">Cihaz Ayarları</h3>
                    <p class="text-gray-400 text-sm">Pro ayarları keşfet</p>
                    <div class="mt-4 flex items-center text-yellow-400 font-semibold">
                        <span>Keşfet</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative z-10 py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="text-4xl sm:text-5xl font-black mb-4">
                    <span class="bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">Güçlü Özellikler</span>
                </h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    PUBG Mobile deneyimini bir üst seviyeye taşıyacak özellikler
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1: LFG -->
                <div class="group relative bg-gradient-to-br from-white/5 to-white/0 border border-white/10 rounded-3xl p-8 hover:border-orange-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-orange-500/20 transform hover:-translate-y-2 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-500/0 to-red-500/0 group-hover:from-orange-500/5 group-hover:to-red-500/5 rounded-3xl transition-all duration-500"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-3xl">🎯</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Takım Arama (LFG)</h3>
                        <p class="text-gray-400 leading-relaxed mb-4">
                            Seviyene, oyun tarzına ve hedeflerine uygun oyuncular bul. Filtreleme seçenekleri ile tam istediğin takımı oluştur.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Gelişmiş filtreleme</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Anlık bildirimler</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Otomatik eşleştirme</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 2: Clan -->
                <div class="group relative bg-gradient-to-br from-white/5 to-white/0 border border-white/10 rounded-3xl p-8 hover:border-blue-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-blue-500/20 transform hover:-translate-y-2 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-purple-500/0 group-hover:from-blue-500/5 group-hover:to-purple-500/5 rounded-3xl transition-all duration-500"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-3xl">👥</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Klan Sistemi</h3>
                        <p class="text-gray-400 leading-relaxed mb-4">
                            Kendi klanını kur veya mevcut klanlara katıl. Klan sıralamaları, turnuvalar ve özel etkinliklerle rekabet et.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Klan yönetim paneli</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Sıralama sistemi</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Turnuva organizasyonu</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 3: Device & Sens -->
                <div class="group relative bg-gradient-to-br from-white/5 to-white/0 border border-white/10 rounded-3xl p-8 hover:border-green-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-green-500/20 transform hover:-translate-y-2 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/0 to-emerald-500/0 group-hover:from-green-500/5 group-hover:to-emerald-500/5 rounded-3xl transition-all duration-500"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-3xl">📱</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Cihaz & Hassasiyet</h3>
                        <p class="text-gray-400 leading-relaxed mb-4">
                            Cihaz özelliklerini ve hassasiyet ayarlarını paylaş. Pro oyuncuların ayarlarını incele ve kendi ayarlarını optimize et.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Detaylı ayar paylaşımı</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Cihaz karşılaştırma</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Pro oyuncu ayarları</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 4: Profile -->
                <div class="group relative bg-gradient-to-br from-white/5 to-white/0 border border-white/10 rounded-3xl p-8 hover:border-yellow-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-yellow-500/20 transform hover:-translate-y-2 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/0 to-orange-500/0 group-hover:from-yellow-500/5 group-hover:to-orange-500/5 rounded-3xl transition-all duration-500"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-yellow-500/30 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-3xl">⭐</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Profil Sistemi</h3>
                        <p class="text-gray-400 leading-relaxed mb-4">
                            Kişiselleştirilmiş profilinle öne çık. İstatistiklerini sergile, rozetler kazan ve topluluktaki yerini al.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Özelleştirilebilir profil</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Başarı rozetleri</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>İstatistik takibi</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 5: Notifications -->
                <div class="group relative bg-gradient-to-br from-white/5 to-white/0 border border-white/10 rounded-3xl p-8 hover:border-pink-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-pink-500/20 transform hover:-translate-y-2 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-pink-500/0 to-red-500/0 group-hover:from-pink-500/5 group-hover:to-red-500/5 rounded-3xl transition-all duration-500"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-red-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-pink-500/30 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-3xl">🔔</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Anlık Bildirimler</h3>
                        <p class="text-gray-400 leading-relaxed mb-4">
                            Önemli gelişmelerden anında haberdar ol. Takım davetleri, klan aktiviteleri ve daha fazlası için bildirim al.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Gerçek zamanlı bildirimler</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Özelleştirilebilir tercihler</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>E-posta & push bildirimleri</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 6: Security -->
                <div class="group relative bg-gradient-to-br from-white/5 to-white/0 border border-white/10 rounded-3xl p-8 hover:border-purple-500/50 transition-all duration-500 hover:shadow-2xl hover:shadow-purple-500/20 transform hover:-translate-y-2 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/0 to-indigo-500/0 group-hover:from-purple-500/5 group-hover:to-indigo-500/5 rounded-3xl transition-all duration-500"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                            <span class="text-3xl">🔒</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Güvenlik & Gizlilik</h3>
                        <p class="text-gray-400 leading-relaxed mb-4">
                            Verileriniz güvende. Modern güvenlik protokolleri ve gizlilik ayarları ile tam kontrol sizde.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>2FA doğrulama</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Şifreli veri iletimi</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Gizlilik kontrolleri</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Activity Section -->
    <section class="relative z-10 py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-transparent via-orange-500/5 to-transparent">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl sm:text-5xl font-black mb-4">
                    <span class="bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">Son Aktiviteler</span>
                </h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    Topluluktaki en son gelişmeleri takip et
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Latest LFG Posts -->
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <span class="text-3xl mr-3">🎯</span>
                            Son İlanlar
                        </h3>
                        <a href="{{ route('lfg.index') }}" class="text-orange-400 hover:text-orange-300 font-semibold text-sm flex items-center">
                            Tümünü Gör
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($latest_lfg ?? [] as $lfg)
                            <a href="{{ route('lfg.show', $lfg) }}" class="block bg-white/5 hover:bg-white/10 rounded-xl p-4 transition-all duration-300 transform hover:scale-105">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center text-sm font-bold">
                                            {{ strtoupper(substr($lfg->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold">{{ $lfg->user->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $lfg->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">Aktif</span>
                                </div>
                                <p class="text-sm text-gray-400 mb-3">{{ Str::limit($lfg->description, 80) }}</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2 py-1 bg-orange-500/20 text-orange-400 rounded text-xs">{{ $lfg->map }}</span>
                                    <span class="px-2 py-1 bg-orange-500/20 text-orange-400 rounded text-xs">{{ $lfg->mode }}</span>
                                    <span class="px-2 py-1 bg-orange-500/20 text-orange-400 rounded text-xs">{{ $lfg->rank }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <div class="text-4xl mb-2">🎯</div>
                                <p>Henüz ilan yok</p>
                                <a href="{{ route('lfg.create') }}" class="text-orange-400 hover:text-orange-300 text-sm mt-2 inline-block">İlk ilanı sen oluştur!</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Latest Clans -->
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <span class="text-3xl mr-3">👥</span>
                            Yeni Klanlar
                        </h3>
                        <a href="{{ route('clans.index') }}" class="text-blue-400 hover:text-blue-300 font-semibold text-sm flex items-center">
                            Tümünü Gör
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($latest_clans ?? [] as $clan)
                            <a href="{{ route('clans.show', $clan) }}" class="block bg-white/5 hover:bg-white/10 rounded-xl p-4 transition-all duration-300 transform hover:scale-105">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-sm font-bold">
                                            {{ strtoupper(substr($clan->tag, 0, 3)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold">[{{ $clan->tag }}] {{ $clan->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $clan->members_count ?? 0 }} üye</div>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-semibold">{{ $clan->level ?? 1 }} Lvl</span>
                                </div>
                                <p class="text-sm text-gray-400">{{ Str::limit($clan->description, 80) }}</p>
                            </a>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <div class="text-4xl mb-2">👥</div>
                                <p>Henüz klan yok</p>
                                <a href="{{ route('clans.create') }}" class="text-blue-400 hover:text-blue-300 text-sm mt-2 inline-block">İlk klanı sen kur!</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leaderboard Section -->
    <section class="relative z-10 py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl sm:text-5xl font-black mb-4">
                    <span class="bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">🏆 Liderlik Tablosu</span>
                </h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    En aktif ve başarılı oyuncular
                </p>
            </div>

            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($top_users ?? [] as $index => $user)
                        <div class="bg-gradient-to-br from-white/5 to-white/0 border border-white/10 rounded-2xl p-6 hover:border-orange-500/50 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="relative">
                                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center text-xl font-bold">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-xs font-bold border-2 border-gray-900">
                                        {{ $index + 1 }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-lg">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-400">Level {{ $user->level ?? 1 }}</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-400">{{ number_format($user->xp ?? 0) }}</div>
                                    <div class="text-xs text-gray-500">XP</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-400">{{ $user->badges_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">Rozet</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-400">{{ $user->posts_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">Gönderi</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-12 text-gray-500">
                            <div class="text-5xl mb-4">🏆</div>
                            <p class="text-lg">Liderlik tablosu yakında doldurulacak!</p>
                        </div>
                    @endforelse
                </div>
                <div class="text-center mt-8">
                    <a href="{{ route('xp.leaderboard') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 rounded-xl font-bold transition-all duration-300 transform hover:scale-105">
                        Tüm Sıralamayı Gör
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative z-10 py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="relative bg-gradient-to-br from-orange-500/20 to-red-500/20 border border-orange-500/30 rounded-3xl p-12 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-red-500/10 backdrop-blur-sm"></div>
                <div class="relative z-10 text-center space-y-6">
                    <h2 class="text-4xl sm:text-5xl font-black">
                        <span class="bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">Hazır mısın?</span>
                    </h2>
                    <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                        Binlerce oyuncu seni bekliyor. Hemen katıl ve maceraya başla!
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        @auth
                            <a href="{{ route('lfg.create') }}" class="px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 rounded-xl font-bold text-lg shadow-2xl shadow-orange-500/30 hover:shadow-orange-500/50 transition-all duration-300 transform hover:scale-105 w-full sm:w-auto">
                                🎯 İlan Oluştur
                            </a>
                            <a href="{{ route('clans.create') }}" class="px-8 py-4 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl font-bold text-lg backdrop-blur-sm transition-all duration-300 transform hover:scale-105 w-full sm:w-auto">
                                👥 Klan Kur
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 rounded-xl font-bold text-lg shadow-2xl shadow-orange-500/30 hover:shadow-orange-500/50 transition-all duration-300 transform hover:scale-105 w-full sm:w-auto">
                                🚀 Ücretsiz Kayıt Ol
                            </a>
                            <a href="{{ route('login') }}" class="px-8 py-4 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl font-bold text-lg backdrop-blur-sm transition-all duration-300 transform hover:scale-105 w-full sm:w-auto">
                                🔑 Giriş Yap
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 bg-gradient-to-br from-gray-900 via-gray-800 to-black border-t border-white/10 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                            <span class="text-2xl">🎮</span>
                        </div>
                        <div>
                            <div class="text-2xl font-black">PUBG Mobile</div>
                            <div class="text-sm text-gray-400">Topluluk Platformu</div>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-4">
                        Türkiye'nin en büyük PUBG Mobile topluluğu. Takım kur, klan oluştur, turnuvalara katıl!
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all transform hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all transform hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all transform hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03z"/></svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Hızlı Linkler</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('lfg.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center"><span class="mr-2">→</span> İlanlar</a></li>
                        <li><a href="{{ route('clans.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center"><span class="mr-2">→</span> Klanlar</a></li>
                        <li><a href="{{ route('tournaments.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center"><span class="mr-2">→</span> Turnuvalar</a></li>
                        <li><a href="{{ route('xp.leaderboard') }}" class="text-gray-300 hover:text-white transition-colors flex items-center"><span class="mr-2">→</span> Liderlik</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Topluluk</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('guide.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center"><span class="mr-2">→</span> Rehber</a></li>
                        <li><a href="{{ route('devices.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center"><span class="mr-2">→</span> Cihaz Ayarları</a></li>
                        <li><a href="{{ route('community.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center"><span class="mr-2">→</span> Forum</a></li>
                        <li><a href="{{ route('xp.badges') }}" class="text-gray-300 hover:text-white transition-colors flex items-center"><span class="mr-2">→</span> Rozetler</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">
                        &copy; 2025 PUBG Mobile Topluluk. Tüm hakları saklıdır.
                    </p>
                    <div class="flex space-x-6 text-sm text-gray-400">
                        <a href="#" class="hover:text-white transition-colors">Hakkımızda</a>
                        <a href="#" class="hover:text-white transition-colors">Gizlilik</a>
                        <a href="#" class="hover:text-white transition-colors">Şartlar</a>
                        <a href="#" class="hover:text-white transition-colors">İletişim</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="fixed bottom-8 right-8 w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center shadow-2xl shadow-orange-500/30 hover:shadow-orange-500/50 transition-all duration-300 transform hover:scale-110 z-50 opacity-0 pointer-events-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>

    <script>
        // Scroll to top button
        const scrollBtn = document.getElementById('scrollToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollBtn.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                scrollBtn.classList.add('opacity-0', 'pointer-events-none');
            }
        });
        scrollBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>
