{{-- Hero Section - Ana sayfa başlık bölümü --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900 via-black to-pink-900 animate-gradient"></div>
    
    <!-- Floating Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-10 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500/20 backdrop-blur-xl rounded-full border border-purple-500/30 mb-8 animate-slide-up">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span class="text-sm font-medium text-purple-300">Türkiye'nin En Büyük Oyun Topluluğu</span>
        </div>

        <!-- Main Heading -->
        <h1 class="text-5xl md:text-7xl lg:text-8xl font-black mb-6 animate-slide-up" style="animation-delay: 0.1s;">
            <span class="bg-gradient-to-r from-purple-400 via-pink-500 to-purple-600 bg-clip-text text-transparent">
                Takım Sistemi
            </span>
        </h1>

        <!-- Subheading -->
        <p class="text-xl md:text-2xl text-gray-300 mb-4 max-w-3xl mx-auto animate-slide-up" style="animation-delay: 0.2s;">
            Birden fazla oyunda takım bul, klan oluştur, turnuvalara katıl!
        </p>
        
        <p class="text-lg text-gray-400 mb-12 max-w-2xl mx-auto animate-slide-up" style="animation-delay: 0.3s;">
            PUBG Mobile, COD Mobile ve daha fazlası için tek platform
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16 animate-slide-up" style="animation-delay: 0.4s;">
            @guest
                <a href="{{ route('register') }}" class="group relative px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-lg font-bold rounded-2xl hover:shadow-2xl hover:shadow-purple-500/50 transition-all overflow-hidden">
                    <span class="relative z-10 flex items-center gap-2">
                        <span>Hemen Başla</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-pink-600 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </a>
                
                <a href="{{ route('login') }}" class="px-8 py-4 bg-white/10 backdrop-blur-xl text-white text-lg font-bold rounded-2xl border-2 border-white/20 hover:bg-white/20 hover:border-white/40 transition-all">
                    Giriş Yap
                </a>
            @else
                <a href="#games" class="group relative px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-lg font-bold rounded-2xl hover:shadow-2xl hover:shadow-purple-500/50 transition-all">
                    <span class="flex items-center gap-2">
                        <span>Oyun Seç</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                </a>
            @endguest
        </div>

        <!-- Stats Preview -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto animate-slide-up" style="animation-delay: 0.5s;">
            <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
                <div class="text-3xl md:text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent mb-2">
                    {{ number_format($stats['total_users'] ?? 0) }}+
                </div>
                <div class="text-sm text-gray-400 font-medium">Aktif Oyuncu</div>
            </div>

            <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
                <div class="text-3xl md:text-4xl font-black bg-gradient-to-r from-blue-400 to-cyan-500 bg-clip-text text-transparent mb-2">
                    {{ number_format($stats['total_clans'] ?? 0) }}+
                </div>
                <div class="text-sm text-gray-400 font-medium">Klan</div>
            </div>

            <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
                <div class="text-3xl md:text-4xl font-black bg-gradient-to-r from-green-400 to-emerald-500 bg-clip-text text-transparent mb-2">
                    {{ number_format($stats['total_teams'] ?? 0) }}+
                </div>
                <div class="text-sm text-gray-400 font-medium">Turnuva</div>
            </div>

            <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
                <div class="text-3xl md:text-4xl font-black bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent mb-2">
                    {{ count($stats['games'] ?? []) }}
                </div>
                <div class="text-sm text-gray-400 font-medium">Oyun</div>
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
