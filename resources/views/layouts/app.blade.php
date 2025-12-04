<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'PUBG Mobile Topluluk Platformu - Takım Bul, Klan Kur')</title>
    <meta name="description" content="@yield('description', 'Türkiye\'nin en büyük PUBG Mobile topluluğu. Takım ara, klan kur, turnuvalara katıl. Binlerce oyuncu ile tanış ve PUBG Mobile deneyimini zirveye taşı!')">
    <meta name="keywords" content="@yield('keywords', 'pubg mobile, pubg mobile türkiye, pubg klan, pubg takım, lfg pubg, pubg mobile topluluk, pubg turnuva, pubg mobile ayarları')">
    <meta name="author" content="PUBG Mobile Topluluk">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', 'PUBG Mobile Topluluk Platformu')">
    <meta property="og:description" content="@yield('og_description', 'Türkiye\'nin en büyük PUBG Mobile topluluğu. Takım ara, klan kur, turnuvalara katıl.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:site_name" content="PUBG Mobile Topluluk">
    <meta property="og:locale" content="tr_TR">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('twitter_url', url()->current())">
    <meta name="twitter:title" content="@yield('twitter_title', 'PUBG Mobile Topluluk Platformu')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Türkiye\'nin en büyük PUBG Mobile topluluğu.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/twitter-card.jpg'))">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Oyuna Özel Tema CSS -->
    @if(session('current_game'))
        <link rel="stylesheet" href="{{ asset('css/themes/' . session('current_game')->slug . '.css') }}">
        
        @php
            $themeColors = [
                'pubg' => ['primary' => '#FF6B00', 'secondary' => '#FFB800'],
                'valorant' => ['primary' => '#FF4655', 'secondary' => '#FD4556'],
                'cod' => ['primary' => '#5C8727', 'secondary' => '#8BC34A'],
                'lol' => ['primary' => '#C89B3C', 'secondary' => '#0AC8B9'],
                'csgo' => ['primary' => '#F7931E', 'secondary' => '#00A8E8'],
            ];
            $colors = $themeColors[session('current_game')->slug] ?? $themeColors['pubg'];
        @endphp
        
        <style>
            :root {
                --game-primary: {{ $colors['primary'] }};
                --game-secondary: {{ $colors['secondary'] }};
            }
            
            /* Navbar Theme Override */
            nav {
                background: linear-gradient(90deg, rgba(0, 0, 0, 0.95) 0%, {{ $colors['primary'] }}15 50%, rgba(0, 0, 0, 0.95) 100%) !important;
                border-bottom: 1px solid {{ $colors['primary'] }}40 !important;
            }
            
            /* Gradient Text Override */
            .bg-gradient-to-r.from-purple-400.via-pink-400.to-red-400 {
                background: linear-gradient(to right, {{ $colors['primary'] }}, {{ $colors['secondary'] }}) !important;
            }
            
            /* Button Override */
            .bg-gradient-to-r.from-purple-600.via-pink-600.to-red-600 {
                background: linear-gradient(to right, {{ $colors['primary'] }}, {{ $colors['secondary'] }}) !important;
            }
            
            .bg-gradient-to-r.from-purple-600.via-pink-600.to-red-600:hover {
                filter: brightness(1.2);
            }
            
            /* Border Colors */
            .border-purple-500\/20,
            .border-purple-500\/30 {
                border-color: {{ $colors['primary'] }}40 !important;
            }
            
            /* Glow Effects */
            .shadow-purple-500\/30,
            .shadow-purple-500\/50 {
                box-shadow: 0 10px 30px {{ $colors['primary'] }}50 !important;
            }
            
            /* Background Overlays */
            .bg-purple-500\/10,
            .bg-purple-500\/20 {
                background-color: {{ $colors['primary'] }}20 !important;
            }
        </style>
    @endif
    
    <!-- Oyuna Özel Tema -->
    <x-game-theme />
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Animations -->
    <style>
        @keyframes gradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 3s ease infinite;
        }
        .animate-bounce-slow {
            animation: bounce-slow 3s ease-in-out infinite;
        }
        .animate-pulse-slow {
            animation: pulse-slow 2s ease-in-out infinite;
        }
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }
        .animate-fade-in {
            animation: fade-in 0.8s ease-out;
        }
    </style>
    
    <!-- Structured Data -->
    @stack('structured-data')
</head>
<body class="bg-black text-white min-h-screen">
    <!-- Header -->
    <x-navbar />

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center animate-slide-down" role="alert">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center animate-slide-down" role="alert">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="relative bg-black/50 backdrop-blur-xl border-t border-purple-500/20 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                <!-- Logo ve Açıklama -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center shadow-2xl">
                            <span class="text-2xl">🎮</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white">PUBG Mobile</h3>
                            <p class="text-sm text-gray-400">Topluluk Platformu</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Türkiye'nin en büyük PUBG Mobile topluluğu. Takım bul, klan oluştur, turnuvalara katıl!
                    </p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-purple-500/20 hover:bg-purple-500/40 rounded-xl flex items-center justify-center transition-all">
                            <span class="text-xl">📱</span>
                        </a>
                        <a href="#" class="w-10 h-10 bg-purple-500/20 hover:bg-purple-500/40 rounded-xl flex items-center justify-center transition-all">
                            <span class="text-xl">🎮</span>
                        </a>
                        <a href="#" class="w-10 h-10 bg-purple-500/20 hover:bg-purple-500/40 rounded-xl flex items-center justify-center transition-all">
                            <span class="text-xl">💬</span>
                        </a>
                    </div>
                </div>

                <!-- Hızlı Linkler -->
                <div>
                    <h4 class="text-lg font-black text-white mb-4">Hızlı Linkler</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-purple-400 transition-colors">🏠 Ana Sayfa</a></li>
                        <li><a href="{{ route('lfg.index') }}" class="text-gray-400 hover:text-purple-400 transition-colors">🎯 İlanlar</a></li>
                        <li><a href="{{ route('clans.index') }}" class="text-gray-400 hover:text-purple-400 transition-colors">👥 Klanlar</a></li>
                        <li><a href="{{ route('guide.index') }}" class="text-gray-400 hover:text-purple-400 transition-colors">📚 Rehberler</a></li>
                        <li><a href="{{ route('tournaments.index') }}" class="text-gray-400 hover:text-purple-400 transition-colors">🏆 Turnuvalar</a></li>
                    </ul>
                </div>

                <!-- XP Sistemi -->
                <div>
                    <h4 class="text-lg font-black text-white mb-4">XP Sistemi</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('xp.history') }}" class="text-gray-400 hover:text-purple-400 transition-colors">📊 XP Geçmişi</a></li>
                        <li><a href="{{ route('xp.leaderboard') }}" class="text-gray-400 hover:text-purple-400 transition-colors">🏅 Liderlik Tablosu</a></li>
                        <li><a href="{{ route('xp.badges') }}" class="text-gray-400 hover:text-purple-400 transition-colors">🎖️ Rozetler</a></li>
                        <li><a href="{{ route('profile.index') }}" class="text-gray-400 hover:text-purple-400 transition-colors">👤 Profilim</a></li>
                    </ul>
                </div>

                <!-- Destek -->
                <div>
                    <h4 class="text-lg font-black text-white mb-4">Destek</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('pages.show', 'hakkimizda') }}" class="text-gray-400 hover:text-purple-400 transition-colors">ℹ️ Hakkımızda</a></li>
                        <li><a href="{{ route('pages.show', 'iletisim') }}" class="text-gray-400 hover:text-purple-400 transition-colors">📧 İletişim</a></li>
                        <li><a href="{{ route('pages.show', 'sss') }}" class="text-gray-400 hover:text-purple-400 transition-colors">❓ SSS</a></li>
                        <li><a href="{{ route('pages.show', 'gizlilik-politikasi') }}" class="text-gray-400 hover:text-purple-400 transition-colors">🔒 Gizlilik</a></li>
                        <li><a href="{{ route('pages.show', 'kullanim-sartlari') }}" class="text-gray-400 hover:text-purple-400 transition-colors">📜 Şartlar</a></li>
                    </ul>
                </div>
            </div>

            <!-- Alt Bilgi -->
            <div class="mt-12 pt-8 border-t border-purple-500/20">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-400 text-sm">
                        © {{ date('Y') }} PUBG Mobile Topluluk Platformu. Tüm hakları saklıdır.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span>🚀 v1.0.0</span>
                        <span>•</span>
                        <span>Made with ❤️ in Turkey</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
