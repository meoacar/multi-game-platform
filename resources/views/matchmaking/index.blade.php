@extends('layouts.app')

@section('title', 'Eşleşme Bul - PUBG Mobile Topluluk')

@section('content')
<style>
    /* Select option'ları için stil */
    select option {
        background-color: #1f2937 !important;
        color: white !important;
        padding: 10px;
    }
</style>
<!-- PUBG Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" 
     style="background-image: url('/arkaplan/3.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    
    <!-- Karartma Katmanı -->
    <div class="absolute inset-0 bg-black/70"></div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-purple-900/30 via-transparent to-black/60"></div>
</div>

<div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="matchmaking()">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-black bg-gradient-to-r from-purple-500 to-pink-600 bg-clip-text text-transparent mb-2">🎯 Eşleşme Bul</h1>
        <p class="text-gray-300 text-lg">Seviyene uygun oyuncularla otomatik eşleş!</p>
    </div>

    <!-- Form -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl shadow-xl border border-white/10 p-8">
        <form @submit.prevent="startMatchmaking" class="space-y-6">
            @csrf

            <!-- Oyun Modu -->
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-3">
                    🎮 Oyun Modu <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-3 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="mode" value="squad" x-model="form.mode" class="sr-only peer" required>
                        <div class="bg-white/10 border-2 border-white/20 rounded-xl p-4 text-center peer-checked:border-purple-500 peer-checked:bg-purple-500/20 hover:bg-white/20 transition-all">
                            <div class="text-3xl mb-2">👥</div>
                            <div class="font-bold text-white">Squad</div>
                            <div class="text-xs text-gray-400">4 Kişi</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="mode" value="duo" x-model="form.mode" class="sr-only peer">
                        <div class="bg-white/10 border-2 border-white/20 rounded-xl p-4 text-center peer-checked:border-purple-500 peer-checked:bg-purple-500/20 hover:bg-white/20 transition-all">
                            <div class="text-3xl mb-2">👫</div>
                            <div class="font-bold text-white">Duo</div>
                            <div class="text-xs text-gray-400">2 Kişi</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="mode" value="solo" x-model="form.mode" class="sr-only peer">
                        <div class="bg-white/10 border-2 border-white/20 rounded-xl p-4 text-center peer-checked:border-purple-500 peer-checked:bg-purple-500/20 hover:bg-white/20 transition-all">
                            <div class="text-3xl mb-2">🧍</div>
                            <div class="font-bold text-white">Solo</div>
                            <div class="text-xs text-gray-400">1 Kişi</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Rank Aralığı -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-300 mb-2">
                        🏆 Minimum Rank
                    </label>
                    <select name="min_rank" x-model="form.min_rank" class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-all">
                        <option value="">Farketmez</option>
                        <option value="Bronze">Bronze</option>
                        <option value="Silver">Silver</option>
                        <option value="Gold">Gold</option>
                        <option value="Platinum">Platinum</option>
                        <option value="Diamond">Diamond</option>
                        <option value="Crown">Crown</option>
                        <option value="Ace">Ace</option>
                        <option value="Conqueror">Conqueror</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-300 mb-2">
                        💎 Maksimum Rank
                    </label>
                    <select name="max_rank" x-model="form.max_rank" class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-all">
                        <option value="">Farketmez</option>
                        <option value="Bronze">Bronze</option>
                        <option value="Silver">Silver</option>
                        <option value="Gold">Gold</option>
                        <option value="Platinum">Platinum</option>
                        <option value="Diamond">Diamond</option>
                        <option value="Crown">Crown</option>
                        <option value="Ace">Ace</option>
                        <option value="Conqueror">Conqueror</option>
                    </select>
                </div>
            </div>

            <!-- Şehir -->
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">
                    📍 Şehir
                </label>
                <input type="text" name="city" x-model="form.city" placeholder="Örn: İstanbul" class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-all placeholder-gray-500">
            </div>

            <!-- Mikrofon -->
            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="microphone_required" x-model="form.microphone_required" class="w-5 h-5 rounded border-2 border-white/20 bg-white/10 text-purple-600 focus:ring-purple-500">
                    <span class="ml-3 text-sm font-bold text-gray-300">🎤 Mikrofon zorunlu</span>
                </label>
            </div>

            <!-- Oyun Stili -->
            <div>
                <label class="block text-sm font-bold text-gray-300 mb-2">
                    ⚡ Oyun Stili
                </label>
                <select name="play_style" x-model="form.play_style" class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring focus:ring-purple-200 transition-all">
                    <option value="">Farketmez</option>
                    <option value="aggressive">🔥 Aggressive (Saldırgan)</option>
                    <option value="balanced">⚖️ Balanced (Dengeli)</option>
                    <option value="defensive">🛡️ Defensive (Savunmacı)</option>
                </select>
            </div>

            <!-- Buton -->
            <div class="pt-4">
                <button type="submit" :disabled="searching" class="w-full px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white rounded-xl font-bold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed text-lg">
                    <span x-show="!searching">🚀 Eşleşme Aramaya Başla</span>
                    <span x-show="searching">⏳ Aranıyor...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Nasıl Çalışır -->
    <div class="mt-6 bg-gradient-to-r from-blue-500/10 to-purple-500/10 backdrop-blur-sm rounded-2xl border border-blue-500/20 p-6">
        <div class="flex items-start">
            <div class="text-3xl mr-4">💡</div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-white mb-3">Nasıl Çalışır?</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-300">
                    <div>
                        <div class="font-bold text-purple-400 mb-2">1️⃣ Tercihlerini Belirle</div>
                        <p>Oyun modu, rank aralığı, şehir ve oyun stilini seç. Mikrofon zorunluluğunu belirt.</p>
                    </div>
                    <div>
                        <div class="font-bold text-purple-400 mb-2">2️⃣ Aramaya Başla</div>
                        <p>Sistem her 10 saniyede bir sana uygun oyuncuları arayacak. Maksimum 5 dakika bekleyebilirsin.</p>
                    </div>
                    <div>
                        <div class="font-bold text-purple-400 mb-2">3️⃣ Eşleşme Bulundu</div>
                        <p>Uyumlu oyuncular bulunduğunda bildirim alacaksın. Oyuncu kartlarını görebilirsin.</p>
                    </div>
                    <div>
                        <div class="font-bold text-purple-400 mb-2">4️⃣ Kabul veya Reddet</div>
                        <p>30 saniye içinde eşleşmeyi kabul et veya reddet. Tüm oyuncular kabul ederse başarılı!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Uyumluluk Algoritması -->
    <div class="mt-6 bg-gradient-to-r from-purple-500/10 to-pink-500/10 backdrop-blur-sm rounded-2xl border border-purple-500/20 p-6">
        <div class="flex items-start">
            <div class="text-3xl mr-4">🧮</div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-white mb-3">Uyumluluk Algoritması</h3>
                <p class="text-gray-300 mb-4">Sistem, oyuncular arasında 0-100 arası bir uyumluluk skoru hesaplar:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="bg-white/5 rounded-lg p-3 border border-white/10">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-gray-300">🏆 Rank Uyumluluğu</span>
                            <span class="text-purple-400 font-bold">30 puan</span>
                        </div>
                        <p class="text-xs text-gray-400">Rank farkı ne kadar az olursa o kadar yüksek puan</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-3 border border-white/10">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-gray-300">⚡ Oyun Stili</span>
                            <span class="text-purple-400 font-bold">25 puan</span>
                        </div>
                        <p class="text-xs text-gray-400">Aynı oyun stiline sahip oyuncular daha uyumlu</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-3 border border-white/10">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-gray-300">📍 Şehir</span>
                            <span class="text-purple-400 font-bold">20 puan</span>
                        </div>
                        <p class="text-xs text-gray-400">Aynı şehirden oyuncular ping avantajı sağlar</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-3 border border-white/10">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-gray-300">🎤 Mikrofon</span>
                            <span class="text-purple-400 font-bold">15 puan</span>
                        </div>
                        <p class="text-xs text-gray-400">Mikrofon tercihleri uyuşmalı</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-3 border border-white/10">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-gray-300">🎮 Oyun Modu</span>
                            <span class="text-purple-400 font-bold">10 puan</span>
                        </div>
                        <p class="text-xs text-gray-400">Aynı modu tercih eden oyuncular</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-3 border border-white/10">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-gray-300">✅ Minimum Eşik</span>
                            <span class="text-orange-400 font-bold">60 puan</span>
                        </div>
                        <p class="text-xs text-gray-400">60 puanın altındaki eşleşmeler yapılmaz</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- XP Kazanma -->
    <div class="mt-6 bg-gradient-to-r from-orange-500/10 to-red-500/10 backdrop-blur-sm rounded-2xl border border-orange-500/20 p-6">
        <div class="flex items-start">
            <div class="text-3xl mr-4">⭐</div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-white mb-3">XP Kazanma Sistemi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white/5 rounded-lg p-4 border border-white/10 text-center">
                        <div class="text-3xl mb-2">🎯</div>
                        <div class="text-2xl font-bold text-orange-500 mb-1">+20 XP</div>
                        <div class="text-sm text-gray-300">Başarılı Eşleşme</div>
                        <p class="text-xs text-gray-400 mt-2">Tüm oyuncular kabul ettiğinde</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-4 border border-white/10 text-center">
                        <div class="text-3xl mb-2">🏆</div>
                        <div class="text-2xl font-bold text-orange-500 mb-1">+50 XP</div>
                        <div class="text-sm text-gray-300">İlk Eşleşme</div>
                        <p class="text-xs text-gray-400 mt-2">İlk başarılı eşleşmen için bonus</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-4 border border-white/10 text-center">
                        <div class="text-3xl mb-2">🎖️</div>
                        <div class="text-2xl font-bold text-purple-500 mb-1">Rozet</div>
                        <div class="text-sm text-gray-300">Eşleşme Ustası</div>
                        <p class="text-xs text-gray-400 mt-2">10+ başarılı eşleşme sonrası</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kurallar ve İpuçları -->
    <div class="mt-6 bg-gradient-to-r from-green-500/10 to-emerald-500/10 backdrop-blur-sm rounded-2xl border border-green-500/20 p-6">
        <div class="flex items-start">
            <div class="text-3xl mr-4">📋</div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-white mb-3">Kurallar ve İpuçları</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-300">
                    <div>
                        <div class="font-bold text-green-400 mb-2">✅ Yapılması Gerekenler</div>
                        <ul class="space-y-1">
                            <li>• Profil bilgilerini eksiksiz doldur</li>
                            <li>• Gerçekçi rank aralığı belirle</li>
                            <li>• Eşleşme bulunduğunda hızlıca karar ver</li>
                            <li>• Diğer oyuncularla saygılı ol</li>
                            <li>• Eşleşme geçmişini kontrol et</li>
                        </ul>
                    </div>
                    <div>
                        <div class="font-bold text-red-400 mb-2">❌ Yapılmaması Gerekenler</div>
                        <ul class="space-y-1">
                            <li>• Çok dar rank aralığı seçme (eşleşme zorlaşır)</li>
                            <li>• Sürekli reddetme (ceza alabilirsin)</li>
                            <li>• Fake bilgi verme</li>
                            <li>• Spam başvuru yapma</li>
                            <li>• Eşleşme sonrası ghosting yapma</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 p-4 text-center">
            <div class="text-3xl mb-2">👥</div>
            <div class="text-2xl font-bold text-white">1,234</div>
            <div class="text-sm text-gray-400">Aktif Oyuncu</div>
        </div>
        <div class="bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 p-4 text-center">
            <div class="text-3xl mb-2">🎯</div>
            <div class="text-2xl font-bold text-white">5,678</div>
            <div class="text-sm text-gray-400">Başarılı Eşleşme</div>
        </div>
        <div class="bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 p-4 text-center">
            <div class="text-3xl mb-2">⏱️</div>
            <div class="text-2xl font-bold text-white">2.5 dk</div>
            <div class="text-sm text-gray-400">Ort. Bekleme</div>
        </div>
        <div class="bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 p-4 text-center">
            <div class="text-3xl mb-2">📊</div>
            <div class="text-2xl font-bold text-white">%87</div>
            <div class="text-sm text-gray-400">Başarı Oranı</div>
        </div>
    </div>
</div>

<script>
function matchmaking() {
    return {
        searching: false,
        form: {
            mode: 'squad',
            min_rank: '',
            max_rank: '',
            city: '',
            microphone_required: false,
            play_style: '',
            game_id: 1 // PUBG Mobile
        },
        
        async startMatchmaking() {
            this.searching = true;
            
            try {
                const response = await fetch('{{ route("matchmaking.start") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Başarılı - polling başlat
                    this.startPolling();
                } else {
                    // Hata
                    alert(data.message || 'Bir hata oluştu');
                    this.searching = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Bir hata oluştu');
                this.searching = false;
            }
        },
        
        startPolling() {
            const interval = setInterval(async () => {
                try {
                    const response = await fetch('{{ route("matchmaking.status") }}');
                    const data = await response.json();
                    
                    if (data.status === 'matched') {
                        clearInterval(interval);
                        this.searching = false;
                        // Eşleşme bulundu modal'ı göster
                        alert('Eşleşme bulundu! ' + data.match.compatibility_score + ' uyumluluk');
                    } else if (data.status === 'cancelled' || data.status === 'expired') {
                        clearInterval(interval);
                        this.searching = false;
                        alert('Eşleşme iptal edildi veya zaman aşımına uğradı');
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 5000); // Her 5 saniyede bir kontrol
        }
    }
}
</script>
@endsection
