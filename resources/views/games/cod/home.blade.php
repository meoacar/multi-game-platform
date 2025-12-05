@extends('layouts.app')

@section('title', 'Ana Sayfa - ' . ($currentGame->name ?? 'Call of Duty Mobile') . ' Topluluk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- HERO SECTION - Askeri Operasyon -->
    <div class="relative bg-gradient-to-br from-[#0a0f0a] via-[#1a2412] to-[#0f1a0a] rounded-3xl shadow-2xl overflow-hidden mb-12 border-2 border-[#5C8727]">
        <!-- Askeri Grid Overlay -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 35px, rgba(92, 135, 39, 0.3) 35px, rgba(92, 135, 39, 0.3) 36px), repeating-linear-gradient(90deg, transparent, transparent 35px, rgba(92, 135, 39, 0.3) 35px, rgba(92, 135, 39, 0.3) 36px);"></div>
        </div>

        <!-- Radar Sweep -->
        <div class="absolute top-1/2 left-1/2 w-[600px] h-[600px] -ml-[300px] -mt-[300px] opacity-20">
            <div class="absolute inset-0 rounded-full" style="background: conic-gradient(from 0deg, transparent 0deg, rgba(92, 135, 39, 0.5) 30deg, transparent 60deg); animation: radar-sweep 8s linear infinite;"></div>
        </div>

        <div class="relative px-8 py-16 md:px-16 md:py-24">
            <!-- Askeri Şerit -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#FFD700] to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#FFD700] to-transparent"></div>

            <div class="flex flex-col md:flex-row items-center gap-8">
                <!-- Sol: İçerik -->
                <div class="flex-1 text-white">
                    <!-- Askeri Kod Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#5C8727]/30 border-2 border-[#FFD700] rounded-lg mb-6 backdrop-blur-md">
                        <span class="w-2 h-2 bg-[#FFD700] rounded-full animate-pulse"></span>
                        <span class="text-[#FFD700] font-bold text-sm uppercase tracking-wider">OPERATION ACTIVE</span>
                    </div>

                    <h1 class="text-5xl md:text-7xl font-black mb-4 leading-tight">
                        <span class="text-[#FFD700] drop-shadow-[0_0_30px_rgba(255,215,0,0.5)]">CALL OF DUTY</span><br>
                        <span class="text-[#8BC34A]">MOBILE</span>
                    </h1>
                    
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-1 w-20 bg-gradient-to-r from-[#FFD700] to-transparent"></div>
                        <p class="text-xl text-[#8BC34A] font-bold uppercase tracking-wider">Türkiye Topluluğu</p>
                    </div>

                    <p class="text-xl md:text-2xl mb-8 text-gray-300 max-w-2xl leading-relaxed">
                        🎖️ Askeri operasyonlara katıl, elit takımlar kur, düşmanı yok et!<br>
                        ⚡ Binlerce asker ile savaş meydanında buluş!
                    </p>

                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('lfg.create') }}" class="group relative px-8 py-4 bg-gradient-to-r from-[#5C8727] to-[#8BC34A] text-white font-black rounded-xl overflow-hidden transform hover:scale-105 transition-all shadow-[0_0_30px_rgba(92,135,39,0.6)] hover:shadow-[0_0_50px_rgba(255,215,0,0.8)] border-2 border-[#FFD700]">
                                <span class="relative z-10 flex items-center gap-2">
                                    🎯 OPERASYON BAŞLAT
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>
                            </a>
                            <a href="{{ route('clans.create') }}" class="px-8 py-4 bg-[#0a0f0a]/80 backdrop-blur-md text-[#FFD700] font-black rounded-xl border-2 border-[#5C8727] hover:bg-[#5C8727]/30 transition-all transform hover:scale-105">
                                🛡️ KLAN KUR
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="group relative px-8 py-4 bg-gradient-to-r from-[#5C8727] to-[#8BC34A] text-white font-black rounded-xl overflow-hidden transform hover:scale-105 transition-all shadow-[0_0_30px_rgba(92,135,39,0.6)] hover:shadow-[0_0_50px_rgba(255,215,0,0.8)] border-2 border-[#FFD700]">
                                <span class="relative z-10">🚀 HEMEN KATIL</span>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></div>
                            </a>
                            <a href="{{ route('login') }}" class="px-8 py-4 bg-[#0a0f0a]/80 backdrop-blur-md text-[#FFD700] font-black rounded-xl border-2 border-[#5C8727] hover:bg-[#5C8727]/30 transition-all transform hover:scale-105">
                                GİRİŞ YAP
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Sağ: Askeri İstatistikler -->
                <div class="flex-shrink-0">
                    <div class="relative w-64 h-64 bg-[#0a0f0a]/50 backdrop-blur-md rounded-2xl border-2 border-[#5C8727] p-6">
                        <!-- Hedef İşareti -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-10">
                            <div class="w-48 h-48 border-4 border-[#FFD700] rounded-full"></div>
                            <div class="absolute w-32 h-32 border-2 border-[#FFD700] rounded-full"></div>
                            <div class="absolute w-16 h-16 border-2 border-[#FFD700] rounded-full"></div>
                            <div class="absolute w-1 h-48 bg-[#FFD700]"></div>
                            <div class="absolute w-48 h-1 bg-[#FFD700]"></div>
                        </div>

                        <div class="relative space-y-4">
                            <div class="text-center">
                                <div class="text-4xl font-black text-[#FFD700] mb-1">{{ number_format($stats['total_users']) }}</div>
                                <div class="text-xs text-[#8BC34A] uppercase tracking-wider">Toplam Asker</div>
                            </div>
                            <div class="h-px bg-gradient-to-r from-transparent via-[#5C8727] to-transparent"></div>
                            <div class="text-center">
                                <div class="text-4xl font-black text-[#FFD700] mb-1">{{ number_format($stats['active_lfg']) }}</div>
                                <div class="text-xs text-[#8BC34A] uppercase tracking-wider">Aktif Operasyon</div>
                            </div>
                            <div class="h-px bg-gradient-to-r from-transparent via-[#5C8727] to-transparent"></div>
                            <div class="text-center">
                                <div class="text-4xl font-black text-[#FFD700] mb-1">{{ number_format($stats['total_clans']) }}</div>
                                <div class="text-xs text-[#8BC34A] uppercase tracking-wider">Elit Birlik</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- İSTATİSTİKLER - Askeri HUD -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        @php
            $statCards = [
                ['icon' => '👥', 'value' => $stats['total_users'], 'label' => 'Toplam Asker', 'color' => 'from-[#5C8727] to-[#8BC34A]'],
                ['icon' => '⚡', 'value' => $stats['active_users'], 'label' => 'Aktif Asker', 'color' => 'from-[#8BC34A] to-[#5C8727]'],
                ['icon' => '🎯', 'value' => $stats['active_lfg'], 'label' => 'Aktif Operasyon', 'color' => 'from-[#FFD700] to-[#5C8727]'],
                ['icon' => '🛡️', 'value' => $stats['total_clans'], 'label' => 'Elit Birlik', 'color' => 'from-[#5C8727] to-[#FFD700]'],
            ];
        @endphp

        @foreach($statCards as $card)
            <div class="group relative bg-gradient-to-br {{ $card['color'] }} rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition-all border-2 border-[#FFD700]/30 hover:border-[#FFD700] overflow-hidden">
                <!-- Köşe İşaretleri -->
                <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-[#FFD700]"></div>
                <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-[#FFD700]"></div>
                <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-[#FFD700]"></div>
                <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-[#FFD700]"></div>

                <div class="relative">
                    <div class="text-4xl mb-2">{{ $card['icon'] }}</div>
                    <div class="text-4xl font-black text-white mb-2 drop-shadow-[0_0_10px_rgba(255,215,0,0.5)]">{{ number_format($card['value']) }}</div>
                    <div class="text-sm text-white/90 font-bold uppercase tracking-wider">{{ $card['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- SON OPERASYONLAR -->
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-4xl font-black text-[#FFD700] drop-shadow-[0_0_20px_rgba(255,215,0,0.5)] flex items-center gap-3">
                <span class="text-5xl">🎯</span>
                AKTİF OPERASYONLAR
            </h2>
            <a href="{{ route('lfg.index') }}" class="text-[#8BC34A] hover:text-[#FFD700] font-bold flex items-center gap-2 transition-colors">
                Tümünü Gör
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>

        @if($recentLfg->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($recentLfg as $lfg)
                    <a href="{{ route('lfg.show', $lfg->id) }}" class="group relative bg-gradient-to-br from-[#0a0f0a] to-[#1a2412] rounded-2xl shadow-lg overflow-hidden hover:shadow-[0_0_30px_rgba(92,135,39,0.5)] transition-all transform hover:scale-105 border-2 border-[#5C8727]/30 hover:border-[#FFD700]">
                        <!-- Köşe İşaretleri -->
                        <div class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-[#FFD700] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-[#FFD700] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-[#FFD700] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-[#FFD700] opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div class="relative p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 bg-[#5C8727]/30 text-[#FFD700] rounded-lg text-sm font-bold border border-[#FFD700]/30">
                                    🎖️ OPERASYON
                                </span>
                                <span class="text-[#8BC34A] text-sm">{{ $lfg->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2 group-hover:text-[#FFD700] transition-colors">{{ Str::limit($lfg->title, 50) }}</h3>
                            <p class="text-gray-400 mb-4">{{ Str::limit($lfg->description, 80) }}</p>
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($lfg->user->name) }}&background=5C8727&color=FFD700" 
                                     class="w-10 h-10 rounded-full mr-3 border-2 border-[#FFD700]">
                                <div>
                                    <div class="font-bold text-white">{{ $lfg->user->name }}</div>
                                    <div class="text-sm text-[#8BC34A]">{{ $lfg->city ?? 'Türkiye' }}</div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-gradient-to-br from-[#0a0f0a] to-[#1a2412] rounded-2xl shadow-lg p-12 text-center border-2 border-[#5C8727]">
                <div class="text-8xl mb-4">🎯</div>
                <h3 class="text-2xl font-bold text-[#FFD700] mb-2">Henüz Operasyon Yok</h3>
                <p class="text-gray-400 mb-6">İlk operasyonu sen başlat ve takımını topla!</p>
                @auth
                    <a href="{{ route('lfg.create') }}" class="inline-block px-8 py-4 bg-gradient-to-r from-[#5C8727] to-[#8BC34A] text-white font-bold rounded-xl hover:shadow-[0_0_30px_rgba(92,135,39,0.6)] transition-all border-2 border-[#FFD700]">
                        🎯 OPERASYON BAŞLAT
                    </a>
                @endauth
            </div>
        @endif
    </div>
</div>

<style>
@keyframes radar-sweep {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endsection
