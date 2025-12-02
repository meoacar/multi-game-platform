{{-- 
    Found Modal Component
    Eşleşme bulunduğunda gösterilen modal
    Oyuncu kartları ve kabul/red butonları içerir
--}}

@props(['match'])

<div x-data="foundModal()" 
     x-show="isVisible" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl border-2 border-green-500/50 p-8 max-w-2xl w-full transform transition-all">
            
            <!-- Başarı Animasyonu -->
            <div class="text-center mb-6">
                <div class="relative inline-block">
                    <!-- Patlama Efekti -->
                    <div class="absolute inset-0 w-32 h-32">
                        <div class="absolute inset-0 bg-green-500/20 rounded-full animate-ping"></div>
                        <div class="absolute inset-0 bg-green-500/10 rounded-full animate-pulse"></div>
                    </div>
                    
                    <!-- İkon -->
                    <div class="relative w-32 h-32 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-green-500/50 animate-bounce">
                        <span class="text-6xl">🎉</span>
                    </div>
                </div>
            </div>

            <!-- Başlık -->
            <h3 class="text-3xl font-black text-white text-center mb-2">
                Eşleşme Bulundu!
            </h3>
            
            <!-- Alt Başlık -->
            <p class="text-gray-400 text-center mb-6">
                Uyumlu oyuncular bulundu. Kabul etmek için 30 saniye süreniz var!
            </p>

            <!-- Countdown Timer -->
            <div class="bg-gradient-to-r from-orange-500/20 to-red-500/20 rounded-xl p-4 mb-6 text-center border border-orange-500/30">
                <div class="text-sm text-gray-400 mb-1">Kalan Süre</div>
                <div class="text-4xl font-black text-white" x-text="timeLeft">30</div>
                <div class="text-xs text-gray-500 mt-1">saniye</div>
            </div>

            <!-- Eşleşme Bilgileri -->
            <div class="bg-white/5 rounded-xl p-4 mb-6">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-gray-400 text-xs mb-1">Oyun Modu</div>
                        <div class="text-white font-bold">{{ $match->mode }}</div>
                    </div>
                    <div>
                        <div class="text-gray-400 text-xs mb-1">Uyumluluk</div>
                        <div class="text-green-400 font-bold">{{ $match->compatibility_score }}%</div>
                    </div>
                    <div>
                        <div class="text-gray-400 text-xs mb-1">Oyuncu Sayısı</div>
                        <div class="text-white font-bold">{{ count($match->user_ids) }}</div>
                    </div>
                </div>
            </div>

            <!-- Oyuncu Kartları -->
            <div class="mb-6">
                <h4 class="text-lg font-bold text-white mb-4 flex items-center">
                    <span class="mr-2">👥</span> Takım Arkadaşların
                </h4>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($match->users as $user)
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10 hover:border-purple-500/50 transition-all">
                            <div class="flex items-center">
                                <!-- Avatar -->
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    @if($user->profile && $user->profile->avatar)
                                        <img src="{{ $user->profile->avatar }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                        <span class="text-white font-bold text-lg">{{ substr($user->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                
                                <!-- Bilgiler -->
                                <div class="flex-1 min-w-0">
                                    <div class="text-white font-bold truncate">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-400">
                                        @if($user->profile && $user->profile->rank)
                                            🏆 {{ $user->profile->rank }}
                                        @else
                                            Seviye {{ $user->level ?? 1 }}
                                        @endif
                                    </div>
                                </div>

                                <!-- Kabul Durumu -->
                                @php
                                    $status = $match->getUserAcceptanceStatus($user->id);
                                @endphp
                                @if($status === 'accepted')
                                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-green-400">✓</span>
                                    </div>
                                @elseif($status === 'rejected')
                                    <div class="w-8 h-8 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-red-400">✗</span>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-orange-500/20 rounded-full flex items-center justify-center flex-shrink-0 animate-pulse">
                                        <span class="text-orange-400">⏱</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Eşleşme Kriterleri -->
            @if($match->match_criteria)
                <div class="bg-gradient-to-r from-purple-500/10 to-blue-500/10 rounded-xl p-4 mb-6">
                    <h4 class="text-sm font-bold text-white mb-3">📋 Eşleşme Kriterleri</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($match->match_criteria as $key => $value)
                            <span class="px-3 py-1 bg-white/10 text-gray-300 rounded-lg text-xs">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}: 
                                @if(is_bool($value))
                                    {{ $value ? '✓' : '✗' }}
                                @else
                                    {{ $value }}
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Aksiyon Butonları -->
            <div class="grid grid-cols-2 gap-4">
                <form action="{{ route('matchmaking.accept', $match->id) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 rounded-xl transition-all transform hover:scale-105 shadow-lg shadow-green-500/30">
                        ✓ Kabul Et
                    </button>
                </form>
                
                <form action="{{ route('matchmaking.reject', $match->id) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" 
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-xl transition-all transform hover:scale-105">
                        ✗ Reddet
                    </button>
                </form>
            </div>

            <!-- Uyarı -->
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500">
                    ⚠️ Tüm oyuncular kabul etmezse eşleşme iptal olur
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
/**
 * Found Modal Component
 * Eşleşme bulunduğunda gösterilen modal için Alpine.js component
 */
function foundModal() {
    return {
        isVisible: true,
        timeLeft: 30,
        timeLeftSeconds: 30,
        countdownInterval: null,
        pollingInterval: null,
        pollingFrequency: 2000, // 2 saniye
        isExpired: false,
        
        init() {
            console.log('Found modal initialized');
            this.startCountdown();
            this.startPolling();
        },
        
        /**
         * Geri sayım başlat
         * 30 saniye içinde karar verilmeli
         */
        startCountdown() {
            const expiresAt = new Date('{{ $match->expires_at->toIso8601String() }}');
            console.log('Countdown started, expires at:', expiresAt);
            
            // İlk güncellemeyi hemen yap
            this.updateCountdown(expiresAt);
            
            // Her saniye güncelle
            this.countdownInterval = setInterval(() => {
                this.updateCountdown(expiresAt);
            }, 1000);
        },
        
        /**
         * Geri sayımı güncelle
         */
        updateCountdown(expiresAt) {
            const now = new Date();
            const diffMs = expiresAt - now;
            const diffSeconds = Math.max(0, Math.floor(diffMs / 1000));
            
            this.timeLeftSeconds = diffSeconds;
            this.timeLeft = diffSeconds;
            
            // Süre dolduğunda
            if (diffSeconds === 0 && !this.isExpired) {
                console.log('Match expired! Reloading page...');
                this.isExpired = true;
                this.stopCountdown();
                this.stopPolling();
                
                // 1 saniye bekle ve sayfayı yenile
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        },
        
        /**
         * Geri sayımı durdur
         */
        stopCountdown() {
            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
                this.countdownInterval = null;
            }
        },
        
        /**
         * Polling sistemini başlat
         * Her 2 saniyede bir durum kontrolü yapar
         */
        startPolling() {
            console.log('Starting match polling...');
            
            // İlk kontrolü hemen yap
            this.checkStatus();
            
            // Sonraki kontrolleri 2 saniyede bir yap
            this.pollingInterval = setInterval(() => {
                this.checkStatus();
            }, this.pollingFrequency);
        },
        
        /**
         * Polling sistemini durdur
         */
        stopPolling() {
            if (this.pollingInterval) {
                console.log('Stopping match polling...');
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },
        
        /**
         * Eşleşme durumunu kontrol et
         */
        async checkStatus() {
            try {
                const response = await fetch('{{ route('matchmaking.status') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Status check failed: ' + response.status);
                }
                
                const data = await response.json();
                console.log('Match status checked:', data);
                
                // Eğer eşleşme tamamlandıysa veya iptal edildiyse
                if (!data.pending_matches || data.pending_matches.length === 0) {
                    console.log('Match completed or cancelled! Reloading page...');
                    this.stopCountdown();
                    this.stopPolling();
                    window.location.reload();
                    return;
                }
                
                // Eğer tüm oyuncular kabul ettiyse
                const match = data.pending_matches.find(m => m.id === {{ $match->id }});
                if (match && match.all_accepted) {
                    console.log('All players accepted! Reloading page...');
                    this.stopCountdown();
                    this.stopPolling();
                    window.location.reload();
                    return;
                }
                
                // Kabul durumlarını güncelle (opsiyonel - UI güncellemesi için)
                if (match && match.acceptance_status) {
                    // Burada kabul durumlarını güncelleyebiliriz
                    console.log('Acceptance status:', match.acceptance_status);
                }
            } catch (error) {
                console.error('Match status check failed:', error);
                // Hata durumunda polling'i durdurmuyoruz
            }
        },
        
        /**
         * Modal kapatıldığında temizlik yap
         */
        closeModal() {
            this.isVisible = false;
            this.stopCountdown();
            this.stopPolling();
        },
        
        /**
         * Component destroy edildiğinde temizlik yap
         */
        destroy() {
            this.stopCountdown();
            this.stopPolling();
        }
    }
}
</script>
@endpush
