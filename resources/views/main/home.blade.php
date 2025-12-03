@extends('main.layout')

@section('title', 'Takım Sistemi - Çoklu Oyun Topluluk Platformu')
@section('description', 'Türkiye\'nin en büyük çoklu oyun topluluğu. PUBG Mobile, COD Mobile ve daha fazlası. Takım ara, klan kur, turnuvalara katıl!')

@section('content')
    {{-- Hero Section --}}
    @include('main.partials.hero', ['stats' => $stats])

    {{-- Game Cards Section --}}
    @include('main.partials.game-cards', ['games' => $games])

    {{-- Platform Statistics Section --}}
    @include('main.partials.stats', ['stats' => $stats])

    {{-- Featured Tournaments Section --}}
    @include('main.partials.tournaments')

    {{-- Call to Action Section --}}
    <section class="relative py-20 bg-gradient-to-br from-purple-900 via-black to-pink-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Floating Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-10 left-10 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl animate-float"></div>
                <div class="absolute bottom-10 right-10 w-80 h-80 bg-pink-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 1s;"></div>
            </div>

            <div class="relative z-10">
                <!-- Icon -->
                <div class="text-6xl mb-6 animate-bounce-slow">🚀</div>

                <!-- Heading -->
                <h2 class="text-4xl md:text-5xl font-black mb-6">
                    <span class="bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
                        Hemen Başla!
                    </span>
                </h2>

                <!-- Description -->
                <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                    Binlerce oyuncu ile tanış, takım kur, turnuvalara katıl ve oyun deneyimini zirveye taşı!
                </p>

                <!-- CTA Buttons -->
                @guest
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="group relative px-10 py-5 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-lg font-bold rounded-2xl hover:shadow-2xl hover:shadow-purple-500/50 transition-all overflow-hidden">
                            <span class="relative z-10 flex items-center gap-2">
                                <span>Ücretsiz Kayıt Ol</span>
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-pink-600 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </a>
                        
                        <a href="{{ route('login') }}" class="px-10 py-5 bg-white/10 backdrop-blur-xl text-white text-lg font-bold rounded-2xl border-2 border-white/20 hover:bg-white/20 hover:border-white/40 transition-all">
                            Zaten Hesabım Var
                        </a>
                    </div>
                @else
                    <a href="#games" class="inline-flex items-center gap-2 px-10 py-5 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-lg font-bold rounded-2xl hover:shadow-2xl hover:shadow-purple-500/50 transition-all">
                        <span>Oyun Seç ve Başla</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                @endguest

                <!-- Features List -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                    <div class="flex items-center gap-3 text-left">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl">✅</span>
                        </div>
                        <div>
                            <div class="font-bold text-white">Tamamen Ücretsiz</div>
                            <div class="text-sm text-gray-400">Hiçbir ücret yok</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-left">
                        <div class="w-12 h-12 bg-pink-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl">⚡</span>
                        </div>
                        <div>
                            <div class="font-bold text-white">Hızlı Kayıt</div>
                            <div class="text-sm text-gray-400">30 saniyede başla</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-left">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl">🔒</span>
                        </div>
                        <div>
                            <div class="font-bold text-white">Güvenli Platform</div>
                            <div class="text-sm text-gray-400">Verileriniz güvende</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
