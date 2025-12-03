{{-- Platform Statistics Section - Platform istatistikleri bölümü --}}
<section id="stats" class="relative py-20 bg-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black mb-4">
                <span class="bg-gradient-to-r from-blue-400 to-cyan-500 bg-clip-text text-transparent">
                    Platform İstatistikleri
                </span>
            </h2>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Büyüyen topluluğumuzun bir parçası ol
            </p>
        </div>

        <!-- Main Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Total Users -->
            <div class="group relative bg-gradient-to-br from-purple-900/30 to-purple-600/30 rounded-3xl p-8 border border-purple-500/20 hover:border-purple-500/50 transition-all hover:scale-105">
                <div class="absolute top-4 right-4 text-4xl opacity-20 group-hover:opacity-40 transition-opacity">
                    👥
                </div>
                <div class="relative">
                    <div class="text-sm font-bold text-purple-400 mb-2">Toplam Oyuncu</div>
                    <div class="text-4xl font-black text-white mb-2">
                        {{ number_format($stats['total_users'] ?? 0) }}
                    </div>
                    <div class="text-xs text-gray-400">Aktif kullanıcı sayısı</div>
                </div>
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/0 to-purple-500/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>

            <!-- Total Clans -->
            <div class="group relative bg-gradient-to-br from-pink-900/30 to-pink-600/30 rounded-3xl p-8 border border-pink-500/20 hover:border-pink-500/50 transition-all hover:scale-105">
                <div class="absolute top-4 right-4 text-4xl opacity-20 group-hover:opacity-40 transition-opacity">
                    🛡️
                </div>
                <div class="relative">
                    <div class="text-sm font-bold text-pink-400 mb-2">Toplam Klan</div>
                    <div class="text-4xl font-black text-white mb-2">
                        {{ number_format($stats['total_clans'] ?? 0) }}
                    </div>
                    <div class="text-xs text-gray-400">Aktif klan sayısı</div>
                </div>
                <div class="absolute inset-0 bg-gradient-to-br from-pink-500/0 to-pink-500/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>

            <!-- Total Tournaments -->
            <div class="group relative bg-gradient-to-br from-blue-900/30 to-blue-600/30 rounded-3xl p-8 border border-blue-500/20 hover:border-blue-500/50 transition-all hover:scale-105">
                <div class="absolute top-4 right-4 text-4xl opacity-20 group-hover:opacity-40 transition-opacity">
                    🏆
                </div>
                <div class="relative">
                    <div class="text-sm font-bold text-blue-400 mb-2">Toplam Turnuva</div>
                    <div class="text-4xl font-black text-white mb-2">
                        {{ number_format($stats['total_teams'] ?? 0) }}
                    </div>
                    <div class="text-xs text-gray-400">Düzenlenen turnuva</div>
                </div>
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>

            <!-- Active Games -->
            <div class="group relative bg-gradient-to-br from-green-900/30 to-green-600/30 rounded-3xl p-8 border border-green-500/20 hover:border-green-500/50 transition-all hover:scale-105">
                <div class="absolute top-4 right-4 text-4xl opacity-20 group-hover:opacity-40 transition-opacity">
                    🎮
                </div>
                <div class="relative">
                    <div class="text-sm font-bold text-green-400 mb-2">Aktif Oyun</div>
                    <div class="text-4xl font-black text-white mb-2">
                        {{ count($stats['games'] ?? []) }}
                    </div>
                    <div class="text-xs text-gray-400">Desteklenen oyun</div>
                </div>
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/0 to-green-500/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
        </div>

        <!-- Per Game Stats -->
        @if(isset($stats['games']) && count($stats['games']) > 0)
            <div class="bg-gradient-to-br from-purple-900/20 to-pink-900/20 rounded-3xl p-8 border border-purple-500/20">
                <h3 class="text-2xl font-black text-white mb-6 flex items-center gap-3">
                    <span>📊</span>
                    <span>Oyun Bazlı İstatistikler</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($stats['games'] as $game)
                        <div class="bg-black/30 rounded-2xl p-6 border border-white/10 hover:border-white/20 transition-all">
                            <!-- Game Header -->
                            <div class="flex items-center gap-3 mb-4">
                                @if($game->logo)
                                    <img src="{{ asset('storage/' . $game->logo) }}" 
                                         alt="{{ $game->name }}" 
                                         class="w-12 h-12 rounded-xl object-cover">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                                        <span class="text-2xl">🎮</span>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-bold text-white">{{ $game->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $game->slug }}</p>
                                </div>
                            </div>

                            <!-- Game Stats -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-400">Turnuvalar</span>
                                    <span class="text-lg font-bold text-purple-400">
                                        {{ number_format($game->tournaments_count ?? 0) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-400">Klanlar</span>
                                    <span class="text-lg font-bold text-pink-400">
                                        {{ number_format($game->clans_count ?? 0) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-400">İlanlar</span>
                                    <span class="text-lg font-bold text-blue-400">
                                        {{ number_format($game->lfg_posts_count ?? 0) }}
                                    </span>
                                </div>
                            </div>

                            <!-- View Game Button -->
                            <a href="{{ $game->slug }}.{{ config('app.domain', 'takimsistemi.com') }}" 
                               class="mt-4 block w-full py-2 bg-gradient-to-r from-purple-500/20 to-pink-600/20 hover:from-purple-500/40 hover:to-pink-600/40 text-white text-center text-sm font-bold rounded-xl border border-purple-500/30 hover:border-purple-500/50 transition-all">
                                Oyuna Git →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Growth Indicator -->
        <div class="mt-12 text-center">
            <div class="inline-flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-green-900/30 to-emerald-900/30 rounded-2xl border border-green-500/30">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="text-2xl font-black text-green-400">+25%</span>
                </div>
                <div class="text-left">
                    <div class="text-sm font-bold text-white">Bu Ay Büyüme</div>
                    <div class="text-xs text-gray-400">Geçen aya göre kullanıcı artışı</div>
                </div>
            </div>
        </div>
    </div>
</section>
