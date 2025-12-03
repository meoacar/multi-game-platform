@extends('layouts.app')

@section('title', 'Tema Demo - ' . ($currentGame->name ?? 'Platform'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-black text-white mb-4">
            @if(isset($currentGame))
                {{ $currentGame->name }} Tema Demo
            @else
                Platform Tema Demo
            @endif
        </h1>
        <p class="text-xl text-gray-400">
            Oyuna özel tema bileşenlerinin görsel örneği
        </p>
    </div>

    <!-- Butonlar -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-white mb-6">Butonlar</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card-game">
                <h3 class="text-lg font-bold text-white mb-4">Primary Button</h3>
                <button class="btn-game-primary w-full">
                    Takıma Katıl
                </button>
            </div>
            <div class="card-game">
                <h3 class="text-lg font-bold text-white mb-4">Secondary Button</h3>
                <button class="btn-game-secondary w-full">
                    Profili Görüntüle
                </button>
            </div>
            <div class="card-game">
                <h3 class="text-lg font-bold text-white mb-4">Outline Button</h3>
                <button class="btn-game-outline w-full">
                    Daha Fazla Bilgi
                </button>
            </div>
        </div>
    </section>

    <!-- Kartlar -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-white mb-6">Kartlar</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card-game">
                <h3 class="text-xl font-bold text-white mb-2">Standart Kart</h3>
                <p class="text-gray-400 mb-4">
                    Bu standart bir oyun kartıdır. Hover efekti ile etkileşimlidir.
                </p>
                <div class="flex gap-2">
                    <span class="badge-game">Aktif</span>
                    <span class="badge-game-outline">Yeni</span>
                </div>
            </div>
            <div class="card-game-featured">
                <h3 class="text-xl font-bold text-white mb-2">Öne Çıkan Kart</h3>
                <p class="text-gray-400 mb-4">
                    Bu öne çıkan bir içerik kartıdır. Üst kısmında gradient çizgi vardır.
                </p>
                <div class="flex gap-2">
                    <span class="badge-game">⭐ Öne Çıkan</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Badge'ler -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-white mb-6">Badge'ler</h2>
        <div class="card-game">
            <div class="flex flex-wrap gap-3">
                <span class="badge-game">🏆 Turnuva</span>
                <span class="badge-game">👥 Klan</span>
                <span class="badge-game">🎯 LFG</span>
                <span class="badge-game-outline">📊 İstatistik</span>
                <span class="badge-game-outline">⚙️ Ayarlar</span>
                <span class="badge-game-outline">🎮 Oyuncu</span>
            </div>
        </div>
    </section>

    <!-- Input'lar -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-white mb-6">Form Elemanları</h2>
        <div class="card-game max-w-2xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-white font-semibold mb-2">Oyuncu Adı</label>
                    <input type="text" class="input-game" placeholder="Oyuncu adınızı girin...">
                </div>
                <div>
                    <label class="block text-white font-semibold mb-2">Takım Açıklaması</label>
                    <textarea class="input-game" rows="4" placeholder="Takımınız hakkında bilgi verin..."></textarea>
                </div>
            </div>
        </div>
    </section>

    <!-- Progress Bar -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-white mb-6">Progress Bar</h2>
        <div class="card-game max-w-2xl">
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between text-sm text-gray-400 mb-2">
                        <span>XP İlerlemesi</span>
                        <span>75%</span>
                    </div>
                    <div class="progress-game">
                        <div class="progress-game-bar" style="width: 75%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm text-gray-400 mb-2">
                        <span>Turnuva Tamamlanma</span>
                        <span>45%</span>
                    </div>
                    <div class="progress-game">
                        <div class="progress-game-bar" style="width: 45%"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alert'ler -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-white mb-6">Alert Mesajları</h2>
        <div class="space-y-4 max-w-2xl">
            <div class="alert-game-success">
                <strong>Başarılı!</strong> Takıma başarıyla katıldınız.
            </div>
            <div class="alert-game-warning">
                <strong>Uyarı!</strong> Profilinizi tamamlamanız önerilir.
            </div>
            <div class="alert-game-danger">
                <strong>Hata!</strong> İşlem gerçekleştirilemedi.
            </div>
            <div class="alert-game-info">
                <strong>Bilgi:</strong> Yeni bir turnuva başladı.
            </div>
        </div>
    </section>

    <!-- Animasyonlar -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-white mb-6">Animasyonlar</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card-game text-center">
                <div class="w-16 h-16 bg-game-gradient rounded-full mx-auto mb-4 animate-game-pulse"></div>
                <h3 class="text-white font-bold">Pulse</h3>
            </div>
            <div class="card-game text-center">
                <div class="w-16 h-16 bg-game-gradient rounded-full mx-auto mb-4 animate-game-glow"></div>
                <h3 class="text-white font-bold">Glow</h3>
            </div>
            <div class="card-game text-center animate-game-slide-in">
                <div class="w-16 h-16 bg-game-gradient rounded-full mx-auto mb-4"></div>
                <h3 class="text-white font-bold">Slide In</h3>
            </div>
        </div>
    </section>

    <!-- Divider -->
    <div class="divider-game"></div>

    <!-- Utility Classes -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-white mb-6">Utility Classes</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-game-primary text-white p-6 rounded-lg text-center font-bold">
                bg-game-primary
            </div>
            <div class="bg-game-secondary text-white p-6 rounded-lg text-center font-bold">
                bg-game-secondary
            </div>
            <div class="bg-game-gradient text-white p-6 rounded-lg text-center font-bold">
                bg-game-gradient
            </div>
            <div class="bg-gray-800 text-game-primary p-6 rounded-lg text-center font-bold border-2 border-game-primary">
                text-game-primary
            </div>
        </div>
    </section>

    <!-- Tema Bilgileri -->
    @if(isset($currentGame))
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-white mb-6">Tema Bilgileri</h2>
        <div class="card-game-featured">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Oyun Bilgileri</h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-gray-400">Oyun:</dt>
                            <dd class="text-white font-semibold">{{ $currentGame->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-400">Slug:</dt>
                            <dd class="text-white font-mono">{{ $currentGame->slug }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-400">Durum:</dt>
                            <dd>
                                <span class="badge-game">{{ $currentGame->status }}</span>
                            </dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Tema Renkleri</h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between items-center">
                            <dt class="text-gray-400">Primary:</dt>
                            <dd class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded" style="background-color: {{ $currentGame->settings['theme_color'] ?? '#FF6B00' }}"></div>
                                <span class="text-white font-mono text-sm">{{ $currentGame->settings['theme_color'] ?? '#FF6B00' }}</span>
                            </dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-gray-400">Secondary:</dt>
                            <dd class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded" style="background-color: {{ $currentGame->settings['secondary_color'] ?? '#FFB800' }}"></div>
                                <span class="text-white font-mono text-sm">{{ $currentGame->settings['secondary_color'] ?? '#FFB800' }}</span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- JavaScript Demo -->
    <section>
        <h2 class="text-2xl font-bold text-white mb-6">JavaScript Entegrasyonu</h2>
        <div class="card-game">
            <pre class="bg-gray-900 p-4 rounded-lg overflow-x-auto"><code class="text-green-400" id="game-info">// Yükleniyor...</code></pre>
        </div>
    </section>
</div>

<script>
    // Oyun bilgilerini göster
    document.addEventListener('DOMContentLoaded', function() {
        const gameInfo = document.getElementById('game-info');
        if (window.currentGame) {
            gameInfo.textContent = JSON.stringify(window.currentGame, null, 2);
        } else {
            gameInfo.textContent = '// Oyun bağlamı bulunamadı\n// Ana domain\'desiniz';
        }
    });
</script>
@endsection
