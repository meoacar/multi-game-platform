{{-- 
    Searching Modal Component
    Eşleşme arama sırasında gösterilen modal
    Alpine.js ile polling yaparak durum kontrolü yapar
--}}

@props(['queue'])

<div x-data="searchingModal()" 
     x-show="isSearching" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl border-2 border-purple-500/50 p-8 max-w-md w-full transform transition-all">
            
            <!-- Animasyonlu Arama İkonu -->
            <div class="text-center mb-6">
                <div class="relative inline-block">
                    <!-- Dış Halka - Yavaş Dönüş -->
                    <div class="absolute inset-0 w-32 h-32 border-4 border-purple-500/30 rounded-full animate-spin" style="animation-duration: 3s;"></div>
                    
                    <!-- Orta Halka - Orta Hız -->
                    <div class="absolute inset-2 w-28 h-28 border-4 border-blue-500/40 rounded-full animate-spin" style="animation-duration: 2s; animation-direction: reverse;"></div>
                    
                    <!-- İç İkon -->
                    <div class="relative w-32 h-32 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-purple-500/50">
                        <svg class="w-16 h-16 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Başlık -->
            <h3 class="text-2xl font-black text-white text-center mb-2">
                Eşleşme Aranıyor...
            </h3>
            
            <!-- Alt Başlık -->
            <p class="text-gray-400 text-center mb-6">
                Uyumlu oyuncular bulunuyor
            </p>

            <!-- Bilgiler -->
            <div class="bg-white/5 rounded-xl p-4 mb-6 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Oyun Modu</span>
                    <span class="text-white font-bold" x-text="queueData.mode">{{ $queue->mode ?? 'Squad' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Arama Denemesi</span>
                    <span class="text-white font-bold" x-text="queueData.search_attempts">{{ $queue->search_attempts ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Geçen Süre</span>
                    <span class="text-white font-bold" x-text="elapsedTime">0:00</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between text-xs text-gray-400 mb-2">
                    <span>Arama Süresi</span>
                    <span x-text="Math.min(100, Math.floor((queueData.elapsed_seconds / 300) * 100)) + '%'">0%</span>
                </div>
                <div class="w-full bg-white/10 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-blue-600 h-full rounded-full transition-all duration-1000"
                         :style="`width: ${Math.min(100, Math.floor((queueData.elapsed_seconds / 300) * 100))}%`">
                    </div>
                </div>
                <p class="text-xs text-gray-500 text-center mt-2">
                    Maksimum 5 dakika içinde eşleşme bulunacak
                </p>
            </div>

            <!-- İpuçları -->
            <div class="bg-gradient-to-r from-purple-500/10 to-blue-500/10 rounded-xl p-4 mb-6">
                <p class="text-sm text-gray-300 text-center">
                    💡 <strong>İpucu:</strong> Eşleşme bulunduğunda bildirim alacaksınız. 
                    Sayfayı kapatabilirsiniz!
                </p>
            </div>

            <!-- İptal Butonu -->
            <form action="{{ route('matchmaking.cancel') }}" method="POST">
                @csrf
                <button type="submit" 
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-xl transition-all transform hover:scale-105">
                    ✗ Aramayı İptal Et
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
/**
 * Searching Modal Component
 * Eşleşme arama sırasında gösterilen modal için Alpine.js component
 */
function searchingModal() {
    return {
        isSearching: {{ $queue ? 'true' : 'false' }},
        queueData: {
            mode: '{{ $queue->mode ?? 'Squad' }}',
            search_attempts: {{ $queue->search_attempts ?? 0 }},
            elapsed_seconds: {{ $queue ? now()->diffInSeconds($queue->created_at) : 0 }}
        },
        elapsedTime: '0:00',
        polling: null,
        timeInterval: null,
        pollingInterval: 5000, // 5 saniye
        
        init() {
            console.log('Searching modal initialized:', this.isSearching);
            
            if (this.isSearching) {
                this.updateElapsedTime();
                this.startPolling();
                this.startTimeCounter();
            }
        },
        
        /**
         * Zaman sayacını başlat
         * Her saniye geçen süreyi günceller
         */
        startTimeCounter() {
            this.timeInterval = setInterval(() => {
                this.queueData.elapsed_seconds++;
                this.updateElapsedTime();
                
                // 5 dakika (300 saniye) dolduğunda uyarı
                if (this.queueData.elapsed_seconds >= 300) {
                    console.log('Search timeout reached (5 minutes)');
                }
            }, 1000);
        },
        
        /**
         * Geçen süreyi formatla (MM:SS)
         */
        updateElapsedTime() {
            const minutes = Math.floor(this.queueData.elapsed_seconds / 60);
            const seconds = this.queueData.elapsed_seconds % 60;
            this.elapsedTime = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        },
        
        /**
         * Polling sistemini başlat
         * Her 5 saniyede bir durum kontrolü yapar
         */
        startPolling() {
            console.log('Starting search polling...');
            
            // İlk kontrolü hemen yap
            this.checkStatus();
            
            // Sonraki kontrolleri 5 saniyede bir yap
            this.polling = setInterval(() => {
                this.checkStatus();
            }, this.pollingInterval);
        },
        
        /**
         * Polling sistemini durdur
         */
        stopPolling() {
            if (this.polling) {
                console.log('Stopping search polling...');
                clearInterval(this.polling);
                this.polling = null;
            }
            
            if (this.timeInterval) {
                clearInterval(this.timeInterval);
                this.timeInterval = null;
            }
        },
        
        /**
         * Matchmaking durumunu kontrol et
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
                console.log('Search status checked:', data);
                
                // Kuyruk bilgilerini güncelle
                if (data.queue) {
                    this.queueData.search_attempts = data.queue.search_attempts;
                    // elapsed_seconds'ı sadece sunucudan gelen değer daha büyükse güncelle
                    if (data.queue.elapsed_seconds > this.queueData.elapsed_seconds) {
                        this.queueData.elapsed_seconds = data.queue.elapsed_seconds;
                    }
                }
                
                // Eğer eşleşme bulunduysa veya kuyruk bittiyse
                if (!data.in_queue || (data.pending_matches && data.pending_matches.length > 0)) {
                    console.log('Search completed! Reloading page...');
                    this.stopPolling();
                    window.location.reload();
                }
            } catch (error) {
                console.error('Search status check failed:', error);
                // Hata durumunda polling'i durdurmuyoruz
            }
        },
        
        /**
         * Modal kapatıldığında temizlik yap
         */
        closeModal() {
            this.isSearching = false;
            this.stopPolling();
        },
        
        /**
         * Component destroy edildiğinde temizlik yap
         */
        destroy() {
            this.stopPolling();
        }
    }
}
</script>
@endpush
