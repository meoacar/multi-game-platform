{{-- Featured Tournaments Section - Öne çıkan turnuvalar bölümü --}}
<section id="tournaments" class="relative py-20 bg-gradient-to-b from-black to-purple-900/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black mb-4">
                <span class="bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">
                    Öne Çıkan Turnuvalar
                </span>
            </h2>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Tüm oyunlardan yaklaşan turnuvalara göz at
            </p>
        </div>

        <!-- Tournaments Container -->
        <div id="tournaments-container" class="space-y-6">
            <!-- Loading State -->
            <div id="tournaments-loading" class="text-center py-12">
                <div class="inline-block w-12 h-12 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin"></div>
                <p class="text-gray-400 mt-4">Turnuvalar yükleniyor...</p>
            </div>

            <!-- Tournaments will be loaded here via JavaScript -->
        </div>

        <!-- View All Button -->
        <div class="text-center mt-12">
            <a href="#games" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold rounded-2xl hover:shadow-2xl hover:shadow-orange-500/50 transition-all hover:scale-105">
                <span>Tüm Turnuvaları Gör</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Turnuvaları yükle
    document.addEventListener('DOMContentLoaded', function() {
        loadFeaturedTournaments();
    });

    async function loadFeaturedTournaments() {
        const container = document.getElementById('tournaments-container');
        const loading = document.getElementById('tournaments-loading');

        try {
            const response = await fetch('{{ route('main.featured-tournaments') }}');
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                // Turnuvaları render et
                const tournamentsHTML = result.data.map(tournament => `
                    <div class="group bg-gradient-to-br from-purple-900/30 to-pink-900/30 rounded-3xl overflow-hidden border border-purple-500/20 hover:border-purple-500/50 transition-all hover:scale-[1.02]">
                        <div class="flex flex-col md:flex-row">
                            <!-- Tournament Image/Icon -->
                            <div class="md:w-1/3 bg-gradient-to-br from-orange-600/20 to-red-600/20 flex items-center justify-center p-8">
                                <div class="text-center">
                                    <div class="text-6xl mb-4">🏆</div>
                                    ${tournament.game ? `
                                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-black/50 rounded-xl">
                                            <span class="text-sm font-bold text-white">${tournament.game.name}</span>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>

                            <!-- Tournament Info -->
                            <div class="md:w-2/3 p-8">
                                <!-- Tournament Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-2xl font-black text-white mb-2 group-hover:text-orange-400 transition-colors">
                                            ${tournament.name}
                                        </h3>
                                        <p class="text-gray-400 text-sm">
                                            Organizatör: ${tournament.organizer ? tournament.organizer.name : 'Bilinmiyor'}
                                        </p>
                                    </div>
                                    <span class="px-4 py-2 bg-orange-500/20 text-orange-400 text-sm font-bold rounded-xl">
                                        Yaklaşan
                                    </span>
                                </div>

                                <!-- Tournament Details -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Başlangıç</div>
                                        <div class="text-sm font-bold text-white">
                                            ${new Date(tournament.start_date).toLocaleDateString('tr-TR')}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Bitiş</div>
                                        <div class="text-sm font-bold text-white">
                                            ${new Date(tournament.end_date).toLocaleDateString('tr-TR')}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Katılımcı</div>
                                        <div class="text-sm font-bold text-purple-400">
                                            ${tournament.max_teams || 'Sınırsız'}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Ödül</div>
                                        <div class="text-sm font-bold text-green-400">
                                            ${tournament.prize_pool || 'Belirtilmemiş'}
                                        </div>
                                    </div>
                                </div>

                                <!-- Tournament Description -->
                                ${tournament.description ? `
                                    <p class="text-gray-400 text-sm mb-6 line-clamp-2">
                                        ${tournament.description}
                                    </p>
                                ` : ''}

                                <!-- Action Button -->
                                <a href="${tournament.game ? tournament.game.slug + '.' : ''}{{ config('app.domain', 'takimsistemi.com') }}/tournaments/${tournament.id}" 
                                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-orange-500/50 transition-all">
                                    <span>Detayları Gör</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                `).join('');

                container.innerHTML = tournamentsHTML;
            } else {
                // Turnuva yoksa
                container.innerHTML = `
                    <div class="text-center py-20">
                        <div class="text-6xl mb-4">🏆</div>
                        <h3 class="text-2xl font-bold text-gray-400 mb-2">Henüz Öne Çıkan Turnuva Yok</h3>
                        <p class="text-gray-500">Yakında yeni turnuvalar eklenecek!</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Turnuvalar yüklenirken hata:', error);
            container.innerHTML = `
                <div class="text-center py-20">
                    <div class="text-6xl mb-4">⚠️</div>
                    <h3 class="text-2xl font-bold text-gray-400 mb-2">Turnuvalar Yüklenemedi</h3>
                    <p class="text-gray-500">Lütfen daha sonra tekrar deneyin.</p>
                </div>
            `;
        }
    }
</script>
@endpush
