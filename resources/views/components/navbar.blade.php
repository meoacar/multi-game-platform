<!-- Navigation -->
<nav class="relative z-50 border-b border-white/5 backdrop-blur-xl bg-black/20" x-data="{ mobileMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-orange-500/50">
                    <span class="text-2xl">🎮</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">PUBG Mobile</h1>
                    <p class="text-xs text-gray-400">Topluluk Platformu</p>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-1">
                <!-- Game Switcher Component -->
                <x-game-switcher />
                
                <a href="{{ route('home') }}" class="px-3 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all {{ request()->routeIs('home') ? 'bg-white/5 text-white' : '' }}">
                    🏠 Ana Sayfa
                </a>
                <a href="{{ route('lfg.index') }}" class="px-3 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all {{ request()->routeIs('lfg.*') ? 'bg-white/5 text-white' : '' }}">
                    🎯 İlanlar
                </a>
                <a href="{{ route('matchmaking.index') }}" class="px-3 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all {{ request()->routeIs('matchmaking.*') ? 'bg-white/5 text-white' : '' }}">
                    🎮 Eşleşme
                </a>
                <a href="{{ route('clans.index') }}" class="px-3 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all {{ request()->routeIs('clans.*') ? 'bg-white/5 text-white' : '' }}">
                    👥 Klanlar
                </a>
                <a href="{{ route('guide.index') }}" class="px-3 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all {{ request()->routeIs('guide.*') ? 'bg-white/5 text-white' : '' }}">
                    📚 Rehber
                </a>
                
                <!-- Dropdown Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="px-3 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl font-semibold transition-all flex items-center space-x-1">
                        <span>⚡ Diğer</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-56 bg-gray-800/95 backdrop-blur-xl rounded-xl shadow-2xl border border-white/10 py-2 z-50">
                        <a href="{{ route('community.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                            💬 Topluluk
                        </a>
                        <a href="{{ route('tournaments.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                            🎮 Turnuvalar
                        </a>
                        <a href="{{ route('squads.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                            👥 Takımlar
                        </a>
                        <a href="{{ route('xp.leaderboard') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                            🏆 Liderlik Tablosu
                        </a>
                        <a href="{{ route('xp.badges') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                            🎖️ Rozetler
                        </a>
                        <a href="{{ route('devices.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                            📱 Cihaz Ayarları
                        </a>
                        <a href="{{ route('friends.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                            👫 Arkadaşlar
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="hidden lg:flex items-center space-x-2">
                @auth
                    <!-- Mesajlar -->
                    <a href="{{ route('messages.index') }}" class="relative p-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition-all {{ request()->routeIs('messages.*') ? 'bg-white/5 text-white' : '' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <!-- Okunmamış mesaj badge'i (opsiyonel) -->
                        @if(false) {{-- Buraya okunmamış mesaj sayısı eklenebilir --}}
                        <span class="absolute top-0 right-0 w-2 h-2 bg-orange-500 rounded-full"></span>
                        @endif
                    </a>

                    <!-- Bildirimler -->
                    <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition-all {{ request()->routeIs('notifications.*') ? 'bg-white/5 text-white' : '' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </a>

                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 px-4 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition-all font-semibold">
                            <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center text-sm font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-64 bg-gray-800/95 backdrop-blur-xl rounded-xl shadow-2xl border border-white/10 py-2 z-50">
                            <div class="px-4 py-3 border-b border-white/10">
                                <p class="text-sm text-gray-400">Hoş geldin,</p>
                                <p class="text-white font-bold">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                👤 Profilim
                            </a>
                            <a href="{{ route('xp.history') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                ⭐ XP Geçmişim
                            </a>
                            <hr class="my-2 border-white/10">
                            <a href="{{ route('lfg.create') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                ➕ İlan Oluştur
                            </a>
                            <a href="{{ route('clans.create') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                🛡️ Klan Kur
                            </a>
                            <a href="{{ route('squads.create') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                👥 Takım Oluştur
                            </a>
                            <a href="{{ route('tournaments.create') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                🎮 Turnuva Oluştur
                            </a>
                            <hr class="my-2 border-white/10">
                            <a href="{{ route('friends.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                👫 Arkadaşlarım
                            </a>
                            <a href="{{ route('friends.requests') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                📬 Arkadaş İstekleri
                            </a>
                            <a href="{{ route('messages.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                💬 Mesajlarım
                            </a>
                            <hr class="my-2 border-white/10">
                            <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                ⚙️ Ayarlar
                            </a>
                            <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white transition-colors">
                                🔔 Bildirimler
                            </a>
                            <hr class="my-2 border-white/10">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-white/5 transition-colors">
                                    🚪 Çıkış Yap
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-300 hover:text-white font-semibold transition-all">
                        Giriş Yap
                    </a>
                    @if(setting('registration_open', true))
                        <a href="{{ route('register') }}" class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all transform hover:scale-105">
                            Kayıt Ol
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg hover:bg-white/5 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-transition class="lg:hidden py-4 space-y-2 border-t border-white/10">
            <!-- Game Switcher Component (Mobile) -->
            <div class="px-4 pb-2 border-b border-white/10 mb-2">
                <x-game-switcher />
            </div>
            
            <a href="{{ route('home') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('home') ? 'bg-white/5 text-white' : '' }}">🏠 Ana Sayfa</a>
            <a href="{{ route('lfg.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('lfg.*') ? 'bg-white/5 text-white' : '' }}">🎯 İlanlar</a>
            <a href="{{ route('matchmaking.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('matchmaking.*') ? 'bg-white/5 text-white' : '' }}">🎮 Eşleşme Bul</a>
            <a href="{{ route('clans.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('clans.*') ? 'bg-white/5 text-white' : '' }}">👥 Klanlar</a>
            <a href="{{ route('guide.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('guide.*') ? 'bg-white/5 text-white' : '' }}">📚 Rehber</a>
            <a href="{{ route('community.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('community.*') ? 'bg-white/5 text-white' : '' }}">💬 Topluluk</a>
            <a href="{{ route('tournaments.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('tournaments.*') ? 'bg-white/5 text-white' : '' }}">🎮 Turnuvalar</a>
            <a href="{{ route('squads.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('squads.*') ? 'bg-white/5 text-white' : '' }}">👥 Takımlar</a>
            <a href="{{ route('xp.leaderboard') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('xp.leaderboard') ? 'bg-white/5 text-white' : '' }}">🏆 Liderlik Tablosu</a>
            <a href="{{ route('xp.badges') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('xp.badges') ? 'bg-white/5 text-white' : '' }}">🎖️ Rozetler</a>
            <a href="{{ route('devices.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors {{ request()->routeIs('devices.*') ? 'bg-white/5 text-white' : '' }}">📱 Cihaz Ayarları</a>
            
            @auth
                <div class="border-t border-white/10 pt-2 mt-2">
                    <div class="px-4 py-2 text-sm text-gray-400">
                        {{ Auth::user()->name }}
                    </div>
                    <a href="{{ route('profile.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">👤 Profilim</a>
                    <a href="{{ route('messages.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">💬 Mesajlarım</a>
                    <a href="{{ route('friends.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">👫 Arkadaşlarım</a>
                    <a href="{{ route('xp.history') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">⭐ XP Geçmişim</a>
                    <hr class="my-2 border-white/10">
                    <a href="{{ route('lfg.create') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">➕ İlan Oluştur</a>
                    <a href="{{ route('clans.create') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">🛡️ Klan Kur</a>
                    <a href="{{ route('squads.create') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">👥 Takım Oluştur</a>
                    <a href="{{ route('tournaments.create') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">🎮 Turnuva Oluştur</a>
                    <hr class="my-2 border-white/10">
                    <a href="{{ route('settings.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">⚙️ Ayarlar</a>
                    <a href="{{ route('notifications.index') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">🔔 Bildirimler</a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 rounded-lg hover:bg-white/5 transition-colors text-red-400">🚪 Çıkış Yap</button>
                    </form>
                </div>
            @else
                <div class="border-t border-white/10 pt-2 mt-2">
                    <a href="{{ route('login') }}" class="block px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">Giriş Yap</a>
                    @if(setting('registration_open', true))
                        <a href="{{ route('register') }}" class="block px-4 py-2 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg font-semibold text-center">Kayıt Ol</a>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>
