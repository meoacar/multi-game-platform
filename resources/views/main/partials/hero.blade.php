{{-- Hero Section - Ana sayfa başlık bölümü --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Animated Background with Mesh Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900 via-black via-50% to-pink-900 animate-gradient"></div>
    
    <!-- Animated Grid Pattern -->
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0" style="background-image: linear-gradient(rgba(139, 92, 246, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(139, 92, 246, 0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>
    
    <!-- Floating Elements with Enhanced Glow -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-10 w-96 h-96 bg-purple-500/30 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-20 right-10 w-[500px] h-[500px] bg-pink-500/30 rounded-full blur-[120px] animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-blue-500/20 rounded-full blur-[100px] animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-40 right-1/4 w-64 h-64 bg-cyan-500/20 rounded-full blur-[80px] animate-float" style="animation-delay: 1.5s;"></div>
    </div>
    
    <!-- Particle Effect -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        @for($i = 0; $i < 20; $i++)
            <div class="absolute w-1 h-1 bg-white/30 rounded-full animate-float" 
                 style="left: {{ rand(0, 100) }}%; top: {{ rand(0, 100) }}%; animation-delay: {{ $i * 0.3 }}s; animation-duration: {{ rand(3, 8) }}s;"></div>
        @endfor
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <!-- Badge with Glassmorphism -->
        <div class="inline-flex items-center gap-3 px-6 py-3 bg-white/10 backdrop-blur-2xl rounded-full border border-white/20 mb-8 animate-slide-up shadow-2xl shadow-purple-500/20 hover:scale-105 transition-transform">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-sm font-bold text-white tracking-wide">🔥 Türkiye'nin En Büyük Oyun Topluluğu</span>
        </div>

        <!-- Main Heading with 3D Effect -->
        <h1 class="text-6xl md:text-8xl lg:text-9xl font-black mb-8 animate-slide-up leading-tight" style="animation-delay: 0.1s;">
            <span class="relative inline-block">
                <span class="absolute inset-0 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 bg-clip-text text-transparent blur-2xl opacity-50">
                    Takım Sistemi
                </span>
                <span class="relative bg-gradient-to-r from-purple-400 via-pink-500 to-purple-400 bg-clip-text text-transparent animate-gradient bg-[length:200%_auto]">
                    Takım Sistemi
                </span>
            </span>
        </h1>

        <!-- Subheading with Enhanced Typography -->
        <p class="text-2xl md:text-3xl text-white font-semibold mb-4 max-w-4xl mx-auto animate-slide-up drop-shadow-2xl" style="animation-delay: 0.2s;">
            Birden fazla oyunda <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">takım bul</span>, 
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-red-500">klan oluştur</span>, 
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-500">turnuvalara katıl!</span>
        </p>
        
        <p class="text-lg md:text-xl text-gray-300 mb-16 max-w-3xl mx-auto animate-slide-up" style="animation-delay: 0.3s;">
            🎮 PUBG Mobile • 🔫 COD Mobile • 🎯 Ve Daha Fazlası
        </p>

        <!-- CTA Buttons with Enhanced Design -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mb-20 animate-slide-up" style="animation-delay: 0.4s;">
            @guest
                <a href="{{ route('register') }}" class="group relative px-10 py-5 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 text-white text-xl font-black rounded-2xl hover:shadow-2xl hover:shadow-purple-500/60 transition-all overflow-hidden transform hover:scale-110 hover:-rotate-1">
                    <span class="relative z-10 flex items-center gap-3">
                        <span class="text-2xl">🚀</span>
                        <span>Hemen Başla</span>
                        <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-pink-600 via-purple-600 to-pink-600 opacity-0 group-hover:opacity-100 transition-opacity bg-[length:200%_auto] animate-gradient"></div>
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="absolute inset-0 bg-white/20 blur-xl"></div>
                    </div>
                </a>
                
                <a href="{{ route('login') }}" class="group px-10 py-5 bg-white/5 backdrop-blur-2xl text-white text-xl font-black rounded-2xl border-2 border-white/30 hover:bg-white/10 hover:border-white/50 transition-all transform hover:scale-105 shadow-xl">
                    <span class="flex items-center gap-3">
                        <span>✨</span>
                        <span>Giriş Yap</span>
                    </span>
                </a>
            @else
                <a href="#games" class="group relative px-10 py-5 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 text-white text-xl font-black rounded-2xl hover:shadow-2xl hover:shadow-purple-500/60 transition-all transform hover:scale-110">
                    <span class="flex items-center gap-3">
                        <span class="text-2xl">🎮</span>
                        <span>Oyun Seç</span>
                        <svg class="w-6 h-6 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                </a>
            @endguest
        </div>

        <!-- Stats Preview with Glassmorphism -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto animate-slide-up" style="animation-delay: 0.5s;">
            <div class="group relative bg-white/5 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 hover:bg-white/10 hover:border-purple-500/50 transition-all transform hover:scale-110 hover:-translate-y-2 shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-transparent rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="text-5xl mb-3">👥</div>
                    <div class="text-4xl md:text-5xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent mb-2">
                        {{ number_format($stats['total_users'] ?? 0) }}+
                    </div>
                    <div class="text-sm text-gray-300 font-bold">Aktif Oyuncu</div>
                </div>
            </div>

            <div class="group relative bg-white/5 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 hover:bg-white/10 hover:border-blue-500/50 transition-all transform hover:scale-110 hover:-translate-y-2 shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-transparent rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="text-5xl mb-3">🛡️</div>
                    <div class="text-4xl md:text-5xl font-black bg-gradient-to-r from-blue-400 to-cyan-500 bg-clip-text text-transparent mb-2">
                        {{ number_format($stats['total_clans'] ?? 0) }}+
                    </div>
                    <div class="text-sm text-gray-300 font-bold">Klan</div>
                </div>
            </div>

            <div class="group relative bg-white/5 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 hover:bg-white/10 hover:border-green-500/50 transition-all transform hover:scale-110 hover:-translate-y-2 shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-transparent rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="text-5xl mb-3">🏆</div>
                    <div class="text-4xl md:text-5xl font-black bg-gradient-to-r from-green-400 to-emerald-500 bg-clip-text text-transparent mb-2">
                        {{ number_format($stats['total_teams'] ?? 0) }}+
                    </div>
                    <div class="text-sm text-gray-300 font-bold">Turnuva</div>
                </div>
            </div>

            <div class="group relative bg-white/5 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 hover:bg-white/10 hover:border-orange-500/50 transition-all transform hover:scale-110 hover:-translate-y-2 shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/20 to-transparent rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="text-5xl mb-3">🎮</div>
                    <div class="text-4xl md:text-5xl font-black bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent mb-2">
                        {{ count($stats['games'] ?? []) }}
                    </div>
                    <div class="text-sm text-gray-300 font-bold">Oyun</div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#games" class="flex flex-col items-center gap-2 text-gray-400 hover:text-white transition-colors">
                <span class="text-sm font-medium">Oyunları Keşfet</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </a>
        </div>
    </div>
</section>
