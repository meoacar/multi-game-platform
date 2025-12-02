@extends('layouts.app')

@section('title', 'Liderlik Tablosu')

@section('content')
<!-- Arka Plan -->
<div class="fixed inset-0 z-0">
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
         style="background-image: url('{{ asset('arkaplan/3.jpg') }}');">
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/95 via-gray-900/90 to-gray-900/95"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-yellow-600/10 via-transparent to-orange-600/10"></div>
</div>

<div class="relative z-10 py-12">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12 animate-fade-in">
            <!-- Arka Plan Kutusu -->
            <div class="inline-block bg-black/60 backdrop-blur-xl rounded-3xl p-8 border-2 border-yellow-500/30 shadow-2xl">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-yellow-500 via-orange-500 to-red-600 rounded-3xl mb-6 shadow-2xl shadow-yellow-500/50 animate-pulse-slow relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-400 to-orange-600 rounded-3xl blur-xl opacity-50 animate-pulse"></div>
                    <svg class="w-14 h-14 text-white relative z-10 animate-bounce-slow" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
                <h1 class="text-6xl font-black mb-4">
                    <span class="text-yellow-400 drop-shadow-[0_0_30px_rgba(250,204,21,0.8)]" style="text-shadow: 0 0 20px rgba(250,204,21,0.8), 0 0 40px rgba(251,146,60,0.6), 0 4px 8px rgba(0,0,0,0.8);">
                        🏆 LİDERLİK TABLOSU 🏆
                    </span>
                </h1>
                <p class="text-white text-xl font-bold drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)] mb-4">
                    En yüksek XP'ye sahip efsane oyuncular!
                </p>
                <div class="flex items-center justify-center gap-2">
                    <span class="px-6 py-3 bg-yellow-500/30 border-2 border-yellow-400/60 rounded-full text-yellow-300 text-base font-black animate-pulse shadow-lg shadow-yellow-500/30">
                        ⚡ Canlı Sıralama
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sol Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Top 3 Podium -->
                <div class="bg-gradient-to-br from-gray-800/90 via-gray-900/90 to-gray-800/90 backdrop-blur-xl rounded-3xl shadow-2xl shadow-yellow-500/30 p-6 border-2 border-yellow-500/40 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 via-transparent to-orange-500/10"></div>
                    <div class="relative z-10">
                        <h3 class="text-xl font-black text-yellow-400 mb-6 flex items-center gap-2 drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)]" style="text-shadow: 0 0 15px rgba(250,204,21,0.6), 0 2px 4px rgba(0,0,0,0.8);">
                            <svg class="w-6 h-6 text-yellow-400 animate-spin-slow drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            TOP 3 EFSANELER
                        </h3>
                        <div class="space-y-4">
                            @foreach($leaderboard->take(3) as $index => $entry)
                            @php
                                $medals = ['🥇', '🥈', '🥉'];
                                $colors = [
                                    'from-yellow-400 via-yellow-500 to-orange-500',
                                    'from-gray-300 via-gray-400 to-gray-500',
                                    'from-orange-500 via-orange-600 to-red-600'
                                ];
                                $shadows = [
                                    'shadow-yellow-500/50',
                                    'shadow-gray-400/50',
                                    'shadow-orange-500/50'
                                ];
                                $scales = ['scale-105', 'scale-100', 'scale-95'];
                            @endphp
                            <div class="bg-gradient-to-r {{ $colors[$index] }} p-4 rounded-2xl text-white shadow-xl {{ $shadows[$index] }} transform {{ $scales[$index] }} hover:scale-110 transition-all duration-300 relative overflow-hidden group">
                                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="relative z-10 flex items-center gap-3">
                                    <span class="text-4xl animate-bounce-slow">{{ $medals[$index] }}</span>
                                    <div class="flex-1">
                                        <p class="font-black text-base drop-shadow-lg">{{ $entry['user']->name }}</p>
                                        <p class="text-sm font-bold opacity-95 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            {{ number_format($entry['xp_total']) }} XP
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- XP Kazanma Rehberi -->
                <div class="bg-gradient-to-br from-blue-900/90 via-purple-900/90 to-blue-900/90 backdrop-blur-xl rounded-3xl shadow-2xl shadow-blue-500/30 p-6 border-2 border-blue-500/40 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-pink-500/10"></div>
                    <div class="relative z-10">
                        <h3 class="text-xl font-black text-blue-300 mb-5 flex items-center gap-2 drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)]" style="text-shadow: 0 0 15px rgba(147,197,253,0.6), 0 2px 4px rgba(0,0,0,0.8);">
                            <svg class="w-6 h-6 text-blue-400 animate-pulse drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            XP NASIL KAZANILIR?
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-600/20 to-emerald-600/20 rounded-xl border border-green-500/30 hover:border-green-400/50 transition-all duration-300 hover:scale-105 transform">
                                <span class="text-gray-200 font-semibold">✅ Profil tamamlama</span>
                                <span class="font-black text-green-400 text-base">+50 XP</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-600/20 to-emerald-600/20 rounded-xl border border-green-500/30 hover:border-green-400/50 transition-all duration-300 hover:scale-105 transform">
                                <span class="text-gray-200 font-semibold">📝 Rehber yazma</span>
                                <span class="font-black text-green-400 text-base">+30 XP</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-600/20 to-emerald-600/20 rounded-xl border border-green-500/30 hover:border-green-400/50 transition-all duration-300 hover:scale-105 transform">
                                <span class="text-gray-200 font-semibold">🛡️ Klan oluşturma</span>
                                <span class="font-black text-green-400 text-base">+25 XP</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-600/20 to-emerald-600/20 rounded-xl border border-green-500/30 hover:border-green-400/50 transition-all duration-300 hover:scale-105 transform">
                                <span class="text-gray-200 font-semibold">📱 Cihaz bilgisi</span>
                                <span class="font-black text-green-400 text-base">+20 XP</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-600/20 to-emerald-600/20 rounded-xl border border-green-500/30 hover:border-green-400/50 transition-all duration-300 hover:scale-105 transform">
                                <span class="text-gray-200 font-semibold">💬 Topluluk gönderisi</span>
                                <span class="font-black text-green-400 text-base">+15 XP</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-600/20 to-emerald-600/20 rounded-xl border border-green-500/30 hover:border-green-400/50 transition-all duration-300 hover:scale-105 transform">
                                <span class="text-gray-200 font-semibold">🎯 LFG ilanı</span>
                                <span class="font-black text-green-400 text-base">+10 XP</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ana Tablo -->
            <div class="lg:col-span-3">
                <div class="bg-gradient-to-br from-gray-800/90 via-gray-900/90 to-gray-800/90 backdrop-blur-xl rounded-3xl shadow-2xl shadow-yellow-500/30 border-2 border-yellow-500/40 overflow-hidden relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 via-orange-500/5 to-red-500/5"></div>
                    <!-- Table Header -->
                    <div class="bg-gradient-to-r from-yellow-500 via-orange-500 to-red-600 px-6 py-5 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/20 via-transparent to-orange-400/20 animate-pulse"></div>
                        <div class="grid grid-cols-12 gap-4 text-white font-black text-sm uppercase tracking-wider relative z-10">
                            <div class="col-span-1">Sıra</div>
                            <div class="col-span-5">Oyuncu</div>
                            <div class="col-span-2">Level</div>
                            <div class="col-span-2">Toplam XP</div>
                            <div class="col-span-2">Rütbe</div>
                        </div>
                    </div>

                    <!-- Table Body -->
                    <div class="divide-y divide-gray-700/50 relative z-10">
                        @foreach($leaderboard as $index => $entry)
                        <div class="px-6 py-5 hover:bg-gradient-to-r hover:from-yellow-600/20 hover:via-orange-600/20 hover:to-red-600/20 transition-all duration-300 transform hover:scale-[1.02] {{ $index < 3 ? 'bg-gradient-to-r from-yellow-600/15 via-orange-600/15 to-red-600/15' : '' }} relative group">
                            @if($index < 3)
                                <div class="absolute inset-0 bg-gradient-to-r from-yellow-500/10 to-orange-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            @endif
                            <div class="grid grid-cols-12 gap-4 items-center relative z-10">
                                <!-- Sıra -->
                                <div class="col-span-1">
                                    @if($index === 0)
                                        <span class="text-4xl animate-bounce-slow drop-shadow-lg">🥇</span>
                                    @elseif($index === 1)
                                        <span class="text-4xl animate-bounce-slow drop-shadow-lg" style="animation-delay: 0.1s;">🥈</span>
                                    @elseif($index === 2)
                                        <span class="text-4xl animate-bounce-slow drop-shadow-lg" style="animation-delay: 0.2s;">🥉</span>
                                    @else
                                        <span class="text-2xl font-black text-gray-400 group-hover:text-yellow-400 transition-colors">{{ $index + 1 }}</span>
                                    @endif
                                </div>

                                <!-- Oyuncu -->
                                <div class="col-span-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-xl shadow-purple-500/50 transform group-hover:scale-110 transition-transform duration-300 relative overflow-hidden">
                                            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                            <span class="relative z-10">{{ strtoupper(substr($entry['user']->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-black text-white text-base group-hover:text-yellow-300 transition-colors">{{ $entry['user']->name }}</p>
                                            @if($entry['user']->profile && $entry['user']->profile->nickname)
                                                <p class="text-sm text-gray-400 font-semibold">{{ $entry['user']->profile->nickname }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Level -->
                                <div class="col-span-2">
                                    <span class="inline-flex items-center gap-1 px-4 py-2 rounded-xl text-sm font-black bg-gradient-to-r from-purple-600/40 to-pink-600/40 text-purple-200 border-2 border-purple-500/60 shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform">
                                        <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        {{ $entry['level'] }}
                                    </span>
                                </div>

                                <!-- XP -->
                                <div class="col-span-2">
                                    <div class="flex flex-col">
                                        <span class="text-xl font-black text-transparent bg-gradient-to-r from-yellow-300 to-orange-400 bg-clip-text group-hover:scale-110 transition-transform inline-block">{{ number_format($entry['xp_total']) }}</span>
                                        <span class="text-xs text-gray-400 font-bold">XP</span>
                                    </div>
                                </div>

                                <!-- Rütbe -->
                                <div class="col-span-2">
                                    @if($entry['user']->profile && $entry['user']->profile->rank)
                                        @php
                                            $rank = $entry['user']->profile->rank;
                                            $rankTr = config('pubg.ranks.' . $rank, $rank);
                                            
                                            // Rütbe rengini belirle (İngilizce key'lere göre)
                                            $rankColor = 'from-blue-600/40 to-cyan-600/40';
                                            $rankShadow = 'shadow-blue-500/30';
                                            
                                            // Bronze - Bronz
                                            if (stripos($rank, 'Bronze') !== false) {
                                                $rankColor = 'from-orange-700/60 to-orange-900/60';
                                                $rankShadow = 'shadow-orange-600/40';
                                            } 
                                            // Silver - Gümüş
                                            elseif (stripos($rank, 'Silver') !== false) {
                                                $rankColor = 'from-gray-400/60 to-gray-600/60';
                                                $rankShadow = 'shadow-gray-400/40';
                                            } 
                                            // Gold - Altın
                                            elseif (stripos($rank, 'Gold') !== false) {
                                                $rankColor = 'from-yellow-400/60 to-yellow-600/60';
                                                $rankShadow = 'shadow-yellow-500/40';
                                            } 
                                            // Platinum - Platin
                                            elseif (stripos($rank, 'Platinum') !== false) {
                                                $rankColor = 'from-cyan-400/60 to-cyan-600/60';
                                                $rankShadow = 'shadow-cyan-500/40';
                                            } 
                                            // Diamond - Elmas
                                            elseif (stripos($rank, 'Diamond') !== false) {
                                                $rankColor = 'from-blue-400/60 to-blue-600/60';
                                                $rankShadow = 'shadow-blue-500/40';
                                            } 
                                            // Crown - Taç
                                            elseif (stripos($rank, 'Crown') !== false) {
                                                $rankColor = 'from-purple-400/60 to-purple-600/60';
                                                $rankShadow = 'shadow-purple-500/40';
                                            } 
                                            // Ace - As
                                            elseif (stripos($rank, 'Ace') !== false) {
                                                $rankColor = 'from-red-500/60 to-pink-600/60';
                                                $rankShadow = 'shadow-red-500/40';
                                            } 
                                            // Conqueror - Fatih
                                            elseif (stripos($rank, 'Conqueror') !== false) {
                                                $rankColor = 'from-yellow-300/70 to-red-600/70';
                                                $rankShadow = 'shadow-yellow-500/50';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r {{ $rankColor }} text-white border-2 border-white/30 shadow-lg {{ $rankShadow }} group-hover:scale-110 transition-transform">
                                            {{ $rankTr }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 text-sm">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Motivasyon Mesajı -->
                <div class="mt-8 bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 rounded-3xl shadow-2xl shadow-purple-500/40 p-8 text-white relative overflow-hidden group hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 via-pink-500/20 to-red-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                    <div class="flex items-center gap-6 relative z-10">
                        <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center shadow-xl backdrop-blur-sm group-hover:rotate-12 transition-transform duration-300">
                            <svg class="w-10 h-10 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-black mb-2 drop-shadow-lg">⚡ SIRALAMADA YÜKSEL!</h3>
                            <p class="text-white/90 font-semibold text-base">Daha fazla aktivite yaparak XP kazan ve liderlik tablosunda üst sıralara çık. Sen de efsaneler arasına katıl! 🚀</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
