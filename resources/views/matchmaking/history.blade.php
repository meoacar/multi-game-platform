@extends('layouts.app')

@section('title', 'Eşleşme Geçmişi - PUBG Mobile Topluluk')

@section('content')
<!-- PUBG Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" 
     style="background-image: url('/arkaplan/fFetCZ0H0O_x8QSHv6LGz.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    
    <!-- Karartma Katmanı -->
    <div class="absolute inset-0 bg-black/60"></div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-purple-900/30 via-transparent to-black/60"></div>
    
    <!-- Animasyonlu Işık Efektleri -->
    <div class="absolute inset-0">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-duration: 4s;"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse" style="animation-duration: 6s; animation-delay: 1s;"></div>
    </div>
</div>

<div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black bg-gradient-to-r from-purple-500 to-blue-600 bg-clip-text text-transparent mb-2">
                📊 Eşleşme Geçmişi
            </h1>
            <p class="text-gray-400 text-lg">Tüm eşleşme kayıtların ve istatistiklerin</p>
        </div>
        <a href="{{ route('matchmaking.index') }}" 
            class="bg-gradient-to-r from-purple-500 to-blue-600 hover:from-purple-600 hover:to-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all transform hover:scale-105 whitespace-nowrap">
            ⚡ Yeni Eşleşme
        </a>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Toplam Eşleşme -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">Toplam Eşleşme</p>
                    <p class="text-3xl font-black text-white">{{ $history->total() }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-600 rounded-2xl flex items-center justify-center">
                    <span class="text-3xl">🎯</span>
                </div>
            </div>
        </div>

        <!-- Başarı Oranı -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">Başarı Oranı</p>
                    <p class="text-3xl font-black text-green-400">{{ number_format($successRate, 1) }}%</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
                    <span class="text-3xl">✓</span>
                </div>
            </div>
        </div>

        <!-- Ortalama Bekleme Süresi -->
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">Ort. Bekleme</p>
                    <p class="text-3xl font-black text-blue-400">{{ gmdate('i:s', $avgWaitTime) }}</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center">
                    <span class="text-3xl">⏱️</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Geçmiş Listesi -->
    <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-6">
        <h2 class="text-2xl font-bold text-white mb-6">Geçmiş Kayıtlar</h2>
        
        @forelse($history as $record)
            <div class="bg-white/5 rounded-xl p-5 mb-4 border border-white/10 hover:border-purple-500/50 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <!-- Durum İkonu -->
                        @if($record->result === 'completed')
                            <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center mr-4">
                                <span class="text-2xl">✓</span>
                            </div>
                        @elseif($record->result === 'rejected')
                            <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mr-4">
                                <span class="text-2xl">✗</span>
                            </div>
                        @elseif($record->result === 'expired')
                            <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center mr-4">
                                <span class="text-2xl">⏱️</span>
                            </div>
                        @else
                            <div class="w-12 h-12 bg-gray-500/20 rounded-xl flex items-center justify-center mr-4">
                                <span class="text-2xl">❓</span>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-lg font-bold text-white">
                                {{ $record->match->mode ?? 'Bilinmiyor' }} • 
                                {{ $record->match->game->name ?? 'Bilinmiyor' }}
                            </h3>
                            <p class="text-sm text-gray-400">
                                {{ $record->created_at->format('d.m.Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <!-- Durum Badge -->
                        @if($record->result === 'completed')
                            <span class="px-4 py-2 bg-green-500/20 text-green-400 rounded-lg text-sm font-bold">
                                Tamamlandı
                            </span>
                        @elseif($record->result === 'rejected')
                            <span class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg text-sm font-bold">
                                Reddedildi
                            </span>
                        @elseif($record->result === 'expired')
                            <span class="px-4 py-2 bg-orange-500/20 text-orange-400 rounded-lg text-sm font-bold">
                                Zaman Aşımı
                            </span>
                        @else
                            <span class="px-4 py-2 bg-gray-500/20 text-gray-400 rounded-lg text-sm font-bold">
                                {{ ucfirst($record->result) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Detaylar -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-white/10">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Uyumluluk</p>
                        <p class="text-lg font-bold text-white">
                            {{ $record->compatibility_score ?? 0 }}%
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Bekleme Süresi</p>
                        <p class="text-lg font-bold text-white">
                            {{ gmdate('i:s', $record->wait_time_seconds ?? 0) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Oyuncu Sayısı</p>
                        <p class="text-lg font-bold text-white">
                            {{ $record->match ? count($record->match->user_ids ?? []) : 0 }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Eşleşme ID</p>
                        <p class="text-lg font-bold text-gray-400">
                            #{{ $record->match_id ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <!-- Eşleşme Kriterleri -->
                @if($record->match && $record->match->match_criteria)
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <p class="text-xs text-gray-500 mb-2">Eşleşme Kriterleri</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($record->match->match_criteria as $key => $value)
                                <span class="px-3 py-1 bg-purple-500/20 text-purple-400 rounded-lg text-xs">
                                    {{ ucfirst($key) }}: {{ is_bool($value) ? ($value ? 'Evet' : 'Hayır') : $value }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📊</div>
                <h3 class="text-2xl font-black text-white mb-2">Henüz Eşleşme Geçmişin Yok</h3>
                <p class="text-gray-400 text-lg mb-6">İlk eşleşmeni oluştur ve istatistiklerini takip et!</p>
                <a href="{{ route('matchmaking.index') }}" 
                    class="inline-block bg-gradient-to-r from-purple-500 to-blue-600 hover:from-purple-600 hover:to-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all transform hover:scale-105">
                    ⚡ İlk Eşleşmeni Bul!
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($history->hasPages())
        <div class="mt-8">
            {{ $history->links() }}
        </div>
    @endif
</div>
@endsection
