{{-- Game Cards Section - Oyun kartları bölümü --}}
<section id="games" class="relative py-20 bg-gradient-to-b from-black to-purple-900/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black mb-4">
                <span class="bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
                    Desteklenen Oyunlar
                </span>
            </h2>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Favori oyununu seç ve topluluğa katıl
            </p>
        </div>

        <!-- Games Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($games as $game)
                <div class="group relative bg-gradient-to-br from-purple-900/50 to-pink-900/50 rounded-3xl overflow-hidden border border-purple-500/20 hover:border-purple-500/50 transition-all hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/30">
                    <!-- Game Logo/Image -->
                    <div class="relative h-48 bg-gradient-to-br from-purple-600/20 to-pink-600/20 flex items-center justify-center overflow-hidden">
                        @if($game->logo)
                            <img src="{{ asset('storage/' . $game->logo) }}" 
                                 alt="{{ $game->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="text-6xl">🎮</div>
                        @endif
                        
                        <!-- Overlay Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4 px-3 py-1 bg-green-500/90 backdrop-blur-sm rounded-full flex items-center gap-2">
                            <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                            <span class="text-xs font-bold text-white">Aktif</span>
                        </div>
                    </div>

                    <!-- Game Info -->
                    <div class="p-6">
                        <!-- Game Name -->
                        <h3 class="text-2xl font-black text-white mb-2 group-hover:text-purple-400 transition-colors">
                            {{ $game->name }}
                        </h3>

                        <!-- Game Description -->
                        <p class="text-gray-400 text-sm mb-4 line-clamp-2">
                            {{ $game->description ?? 'Topluluğa katıl ve diğer oyuncularla tanış!' }}
                        </p>

                        <!-- Game Stats -->
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="text-center">
                                <div class="text-lg font-black text-purple-400">
                                    {{ number_format($game->tournaments_count ?? 0) }}
                                </div>
                                <div class="text-xs text-gray-500">Turnuva</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-black text-pink-400">
                                    {{ number_format($game->clans_count ?? 0) }}
                                </div>
                                <div class="text-xs text-gray-500">Klan</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-black text-blue-400">
                                    {{ number_format($game->lfg_posts_count ?? 0) }}
                                </div>
                                <div class="text-xs text-gray-500">İlan</div>
                            </div>
                        </div>

                        <!-- Game Settings Info -->
                        @if($game->settings)
                            <div class="flex flex-wrap gap-2 mb-6">
                                @if(isset($game->settings['platforms']))
                                    @foreach($game->settings['platforms'] as $platform)
                                        <span class="px-3 py-1 bg-purple-500/20 text-purple-300 text-xs font-medium rounded-lg">
                                            {{ $platform }}
                                        </span>
                                    @endforeach
                                @endif
                                
                                @if(isset($game->settings['max_team_size']))
                                    <span class="px-3 py-1 bg-pink-500/20 text-pink-300 text-xs font-medium rounded-lg">
                                        Max {{ $game->settings['max_team_size'] }} Kişi
                                    </span>
                                @endif
                            </div>
                        @endif

                        <!-- Action Button -->
                        <a href="http://{{ $game->slug }}.{{ config('app.domain', 'takimsistemi.test') }}" 
                           class="block w-full py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-center font-bold rounded-xl hover:shadow-lg hover:shadow-purple-500/50 transition-all group-hover:scale-105">
                            <span class="flex items-center justify-center gap-2">
                                <span>Oyuna Git</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                        </a>
                    </div>

                    <!-- Hover Effect Border -->
                    <div class="absolute inset-0 rounded-3xl border-2 border-transparent group-hover:border-purple-500/50 transition-all pointer-events-none"></div>
                </div>
            @empty
                <!-- No Games Message -->
                <div class="col-span-full text-center py-20">
                    <div class="text-6xl mb-4">🎮</div>
                    <h3 class="text-2xl font-bold text-gray-400 mb-2">Henüz Oyun Eklenmemiş</h3>
                    <p class="text-gray-500">Yakında yeni oyunlar eklenecek!</p>
                </div>
            @endforelse
        </div>

        <!-- Coming Soon Section -->
        @if($games->count() > 0)
            <div class="mt-16 text-center">
                <div class="inline-flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-purple-900/50 to-pink-900/50 rounded-2xl border border-purple-500/30">
                    <span class="text-2xl">🚀</span>
                    <div class="text-left">
                        <div class="text-sm font-bold text-white">Daha Fazla Oyun Yakında!</div>
                        <div class="text-xs text-gray-400">Yeni oyunlar için bizi takip edin</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
