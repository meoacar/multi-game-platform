<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Takım Sistemi - Çoklu Oyun Topluluk Platformu')</title>
    <meta name="description" content="@yield('description', 'Türkiye\'nin en büyük çoklu oyun topluluğu. PUBG Mobile, COD Mobile ve daha fazlası. Takım ara, klan kur, turnuvalara katıl!')">
    <meta name="keywords" content="@yield('keywords', 'oyun topluluğu, pubg mobile, cod mobile, takım bul, klan, turnuva, esports')">
    <meta name="author" content="Takım Sistemi">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', 'Takım Sistemi - Çoklu Oyun Topluluk Platformu')">
    <meta property="og:description" content="@yield('og_description', 'Türkiye\'nin en büyük çoklu oyun topluluğu.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:site_name" content="Takım Sistemi">
    <meta property="og:locale" content="tr_TR">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('twitter_url', url()->current())">
    <meta name="twitter:title" content="@yield('twitter_title', 'Takım Sistemi')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Türkiye\'nin en büyük çoklu oyun topluluğu.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/twitter-card.jpg'))">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="#8B5CF6">
    <meta name="msapplication-TileColor" content="#8B5CF6">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Animations -->
    <style>
        @keyframes gradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(139, 92, 246, 0.5); }
            50% { box-shadow: 0 0 40px rgba(139, 92, 246, 0.8); }
        }
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 3s ease infinite;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        .animate-slide-up {
            animation: slide-up 0.6s ease-out;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-black text-white min-h-screen overflow-x-hidden">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-xl border-b border-purple-500/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('main.home') }}" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform">
                        <span class="text-2xl">🎮</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-white">Takım Sistemi</h1>
                        <p class="text-xs text-gray-400">Çoklu Oyun Platformu</p>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('main.home') }}" class="text-gray-300 hover:text-white transition-colors font-medium">
                        Ana Sayfa
                    </a>
                    <a href="#games" class="text-gray-300 hover:text-white transition-colors font-medium">
                        Oyunlar
                    </a>
                    <a href="#stats" class="text-gray-300 hover:text-white transition-colors font-medium">
                        İstatistikler
                    </a>
                    <a href="#tournaments" class="text-gray-300 hover:text-white transition-colors font-medium">
                        Turnuvalar
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-white hover:text-purple-400 transition-colors">
                            Giriş Yap
                        </a>
                        <a href="{{ route('register') }}" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-sm font-bold rounded-xl hover:shadow-lg hover:shadow-purple-500/50 transition-all">
                            Kayıt Ol
                        </a>
                    @else
                        <a href="{{ route('profile.index') }}" class="flex items-center gap-2 px-4 py-2 bg-purple-500/20 hover:bg-purple-500/30 rounded-xl transition-colors">
                            <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="fixed top-24 right-4 z-50 animate-slide-up">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3 max-w-md">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-24 right-4 z-50 animate-slide-up">
            <div class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3 max-w-md">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="relative bg-black/50 backdrop-blur-xl border-t border-purple-500/20 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Logo ve Açıklama -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-2xl">
                            <span class="text-2xl">🎮</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white">Takım Sistemi</h3>
                            <p class="text-sm text-gray-400">Çoklu Oyun Platformu</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Türkiye'nin en büyük çoklu oyun topluluğu. Takım bul, klan oluştur, turnuvalara katıl!
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
                    <h4 class="text-lg font-black text-white mb-4">Platform</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('main.home') }}" class="text-gray-400 hover:text-purple-400 transition-colors">🏠 Ana Sayfa</a></li>
                        <li><a href="#games" class="text-gray-400 hover:text-purple-400 transition-colors">🎮 Oyunlar</a></li>
                        <li><a href="#stats" class="text-gray-400 hover:text-purple-400 transition-colors">📊 İstatistikler</a></li>
                        <li><a href="#tournaments" class="text-gray-400 hover:text-purple-400 transition-colors">🏆 Turnuvalar</a></li>
                    </ul>
                </div>

                <!-- Destek -->
                <div>
                    <h4 class="text-lg font-black text-white mb-4">Destek</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-purple-400 transition-colors">ℹ️ Hakkımızda</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-purple-400 transition-colors">📧 İletişim</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-purple-400 transition-colors">❓ SSS</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-purple-400 transition-colors">🔒 Gizlilik</a></li>
                    </ul>
                </div>
            </div>

            <!-- Alt Bilgi -->
            <div class="mt-12 pt-8 border-t border-purple-500/20">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-400 text-sm">
                        © {{ date('Y') }} Takım Sistemi. Tüm hakları saklıdır.
                    </p>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span>🚀 v2.0.0</span>
                        <span>•</span>
                        <span>Made with ❤️ in Turkey</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
