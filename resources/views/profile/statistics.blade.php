@extends('layouts.app')

@section('title', 'Oyun İstatistiklerim')

@section('content')
<!-- PUBG Mobile Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" 
     style="background-image: url('/arkaplan/8.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    
    <!-- Karartma Katmanı -->
    <div class="absolute inset-0 bg-black/70"></div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/70"></div>
    
    <!-- Animasyonlu Işık Efektleri -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-float-delayed"></div>
    </div>
</div>

<div class="relative z-10 min-h-screen py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 animate-fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black bg-gradient-to-r from-orange-400 via-red-400 to-pink-400 bg-clip-text text-transparent mb-2">
                    📊 Oyun İstatistiklerim
                </h1>
                <p class="text-gray-400">PUBG Mobile oyun istatistiklerini güncelle ve profilinde göster</p>
            </div>
            <a href="{{ route('profile.edit') }}" 
                class="hidden md:flex items-center space-x-2 px-4 py-2 bg-white/5 hover:bg-white/10 rounded-xl transition-all border border-white/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Geri Dön</span>
            </a>
        </div>
    </div>

    <!-- Mevcut İstatistikler - Modern Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 animate-scale-in">
        <div class="group relative overflow-hidden bg-gradient-to-br from-blue-600/20 to-blue-800/20 backdrop-blur-xl rounded-2xl p-6 border border-blue-500/20 hover:border-blue-500/40 transition-all duration-300 hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10">
                <div class="text-sm text-blue-300 font-semibold mb-2">⚔️ K/D ORANI</div>
                <div class="text-4xl font-black text-white mb-1">{{ number_format($profile->kd_ratio, 2) }}</div>
                <div class="text-xs text-blue-400">Kills / Deaths</div>
            </div>
        </div>
        
        <div class="group relative overflow-hidden bg-gradient-to-br from-green-600/20 to-green-800/20 backdrop-blur-xl rounded-2xl p-6 border border-green-500/20 hover:border-green-500/40 transition-all duration-300 hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10">
                <div class="text-sm text-green-300 font-semibold mb-2">🏆 WIN RATE</div>
                <div class="text-4xl font-black text-white mb-1">{{ number_format($profile->win_rate, 1) }}%</div>
                <div class="text-xs text-green-400">Kazanma Oranı</div>
            </div>
        </div>
        
        <div class="group relative overflow-hidden bg-gradient-to-br from-purple-600/20 to-purple-800/20 backdrop-blur-xl rounded-2xl p-6 border border-purple-500/20 hover:border-purple-500/40 transition-all duration-300 hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10">
                <div class="text-sm text-purple-300 font-semibold mb-2">🎯 HEADSHOT</div>
                <div class="text-4xl font-black text-white mb-1">{{ number_format($profile->headshot_rate, 1) }}%</div>
                <div class="text-xs text-purple-400">Kafa Vuruşu Oranı</div>
            </div>
        </div>
        
        <div class="group relative overflow-hidden bg-gradient-to-br from-orange-600/20 to-red-800/20 backdrop-blur-xl rounded-2xl p-6 border border-orange-500/20 hover:border-orange-500/40 transition-all duration-300 hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative z-10">
                <div class="text-sm text-orange-300 font-semibold mb-2">🎮 TOPLAM MAÇ</div>
                <div class="text-4xl font-black text-white mb-1">{{ number_format($profile->matches_played) }}</div>
                <div class="text-xs text-orange-400">Oynanan Maç</div>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-gray-800/50 via-gray-900/50 to-black/50 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl overflow-hidden animate-fade-in" style="animation-delay: 0.2s;">
        <form action="{{ route('profile.statistics.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Maç İstatistikleri -->
            <div class="bg-gradient-to-r from-blue-600/20 via-purple-600/20 to-pink-600/20 px-8 py-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">📊</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Maç İstatistikleri</h2>
                        <p class="text-sm text-gray-400">Oynanan maç, kazanılan ve top 10 bilgileri</p>
                    </div>
                </div>
            </div>

            <div class="p-8 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Matches Played -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            🎮 Oynanan Maç Sayısı
                        </label>
                        <input type="number" name="matches_played" value="{{ old('matches_played', $profile->matches_played) }}" min="0"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('matches_played') border-red-500 @enderror">
                        @error('matches_played')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Wins -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            🏆 Kazanılan Maç
                        </label>
                        <input type="number" name="wins" value="{{ old('wins', $profile->wins) }}" min="0"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('wins') border-red-500 @enderror">
                        @error('wins')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-2">💡 Win Rate otomatik hesaplanacak</p>
                    </div>

                    <!-- Top 10 Finishes -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            🔟 İlk 10'a Girme
                        </label>
                        <input type="number" name="top_10_finishes" value="{{ old('top_10_finishes', $profile->top_10_finishes) }}" min="0"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('top_10_finishes') border-red-500 @enderror">
                        @error('top_10_finishes')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Savaş İstatistikleri -->
            <div class="bg-gradient-to-r from-red-600/20 via-orange-600/20 to-yellow-600/20 px-8 py-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">⚔️</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Savaş İstatistikleri</h2>
                        <p class="text-sm text-gray-400">Öldürme, ölüm ve kafa vuruşu bilgileri</p>
                    </div>
                </div>
            </div>

            <div class="p-8 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kills -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            💀 Toplam Öldürme
                        </label>
                        <input type="number" name="kills" value="{{ old('kills', $profile->kills) }}" min="0"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('kills') border-red-500 @enderror">
                        @error('kills')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deaths -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            ☠️ Toplam Ölüm
                        </label>
                        <input type="number" name="deaths" value="{{ old('deaths', $profile->deaths) }}" min="0"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('deaths') border-red-500 @enderror">
                        @error('deaths')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-2">💡 K/D oranı otomatik hesaplanacak</p>
                    </div>

                    <!-- Headshots -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            🎯 Kafa Vuruşu
                        </label>
                        <input type="number" name="headshots" value="{{ old('headshots', $profile->headshots) }}" min="0"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('headshots') border-red-500 @enderror">
                        @error('headshots')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-2">💡 Headshot rate otomatik hesaplanacak</p>
                    </div>
                </div>
            </div>

            <!-- Diğer İstatistikler -->
            <div class="bg-gradient-to-r from-purple-600/20 via-pink-600/20 to-blue-600/20 px-8 py-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">📈</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Diğer İstatistikler</h2>
                        <p class="text-sm text-gray-400">Hasar, hayatta kalma ve en uzak öldürme</p>
                    </div>
                </div>
            </div>

            <div class="p-8 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Damage Dealt -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            💥 Verilen Hasar
                        </label>
                        <input type="number" name="damage_dealt" value="{{ old('damage_dealt', $profile->damage_dealt) }}" min="0"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('damage_dealt') border-red-500 @enderror">
                        @error('damage_dealt')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Survival Time -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            ⏱️ Hayatta Kalma (dakika)
                        </label>
                        <input type="number" name="survival_time" value="{{ old('survival_time', $profile->survival_time) }}" min="0"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('survival_time') border-red-500 @enderror">
                        @error('survival_time')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Longest Kill -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            🎯 En Uzak Öldürme (metre)
                        </label>
                        <input type="number" name="longest_kill" value="{{ old('longest_kill', $profile->longest_kill) }}" min="0"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('longest_kill') border-red-500 @enderror">
                        @error('longest_kill')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Bilgilendirme -->
            <div class="p-8">
                <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/20 rounded-2xl p-6">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-white mb-2">🧮 Otomatik Hesaplama</h3>
                            <p class="text-gray-400 text-sm mb-3">Aşağıdaki oranlar otomatik olarak hesaplanacak:</p>
                            <ul class="space-y-2 text-sm">
                                <li class="flex items-center space-x-2 text-blue-300">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                                    <span><strong>K/D Oranı:</strong> Kills ÷ Deaths</span>
                                </li>
                                <li class="flex items-center space-x-2 text-green-300">
                                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                    <span><strong>Win Rate:</strong> (Wins ÷ Matches) × 100</span>
                                </li>
                                <li class="flex items-center space-x-2 text-purple-300">
                                    <span class="w-2 h-2 bg-purple-400 rounded-full"></span>
                                    <span><strong>Headshot Rate:</strong> (Headshots ÷ Kills) × 100</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Butonlar -->
            <div class="p-8 pt-0">
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" 
                        class="flex-1 group relative overflow-hidden bg-gradient-to-r from-orange-600 to-red-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-2xl hover:shadow-orange-500/50 transition-all duration-300 hover:scale-105">
                        <span class="relative z-10 flex items-center justify-center space-x-2">
                            <span class="text-2xl">💾</span>
                            <span>İstatistikleri Güncelle</span>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-orange-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                    
                    <a href="{{ route('profile.edit') }}" 
                        class="flex-1 group relative overflow-hidden bg-white/5 hover:bg-white/10 text-white px-8 py-4 rounded-2xl font-bold text-lg border border-white/10 hover:border-white/20 transition-all duration-300 hover:scale-105 flex items-center justify-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Geri Dön</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="relative mt-16 pt-12 pb-8 border-t border-white/10">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent pointer-events-none"></div>
        
        <div class="relative">
            <!-- Ana Footer İçerik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Logo & Açıklama -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl">🎮</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white">PUBG Community</h3>
                            <p class="text-xs text-gray-400">Türkiye'nin En Büyük PUBG Mobile Topluluğu</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-4">
                        Oyuncuları bir araya getiren, takım kurmayı kolaylaştıran ve PUBG Mobile deneyimini 
                        daha eğlenceli hale getiren sosyal platform. Hemen katıl, arkadaşlar edin, takım kur!
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-white/5 hover:bg-orange-500/20 border border-white/10 hover:border-orange-500/50 rounded-lg flex items-center justify-center transition-all group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515a.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0a12.64 12.64 0 0 0-.617-1.25a.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057a19.9 19.9 0 0 0 5.993 3.03a.078.078 0 0 0 .084-.028a14.09 14.09 0 0 0 1.226-1.994a.076.076 0 0 0-.041-.106a13.107 13.107 0 0 1-1.872-.892a.077.077 0 0 1-.008-.128a10.2 10.2 0 0 0 .372-.292a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127a12.299 12.299 0 0 1-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028a19.839 19.839 0 0 0 6.002-3.03a.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/5 hover:bg-orange-500/20 border border-white/10 hover:border-orange-500/50 rounded-lg flex items-center justify-center transition-all group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/5 hover:bg-orange-500/20 border border-white/10 hover:border-orange-500/50 rounded-lg flex items-center justify-center transition-all group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/5 hover:bg-orange-500/20 border border-white/10 hover:border-orange-500/50 rounded-lg flex items-center justify-center transition-all group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Hızlı Linkler -->
                <div>
                    <h4 class="text-white font-bold mb-4 flex items-center">
                        <span class="text-orange-400 mr-2">🔗</span>
                        Hızlı Linkler
                    </h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-orange-400 text-sm transition-colors flex items-center group">
                            <span class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity">→</span>Ana Sayfa
                        </a></li>
                        <li><a href="{{ route('lfg.index') }}" class="text-gray-400 hover:text-orange-400 text-sm transition-colors flex items-center group">
                            <span class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity">→</span>Takım Bul
                        </a></li>
                        <li><a href="{{ route('clans.index') }}" class="text-gray-400 hover:text-orange-400 text-sm transition-colors flex items-center group">
                            <span class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity">→</span>Klanlar
                        </a></li>
                        <li><a href="{{ route('community.index') }}" class="text-gray-400 hover:text-orange-400 text-sm transition-colors flex items-center group">
                            <span class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity">→</span>Topluluk
                        </a></li>
                        <li><a href="{{ route('xp.badges') }}" class="text-gray-400 hover:text-orange-400 text-sm transition-colors flex items-center group">
                            <span class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity">→</span>Rozetler
                        </a></li>
                    </ul>
                </div>

                <!-- Destek -->
                <div>
                    <h4 class="text-white font-bold mb-4 flex items-center">
                        <span class="text-orange-400 mr-2">💬</span>
                        Destek
                    </h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-orange-400 text-sm transition-colors flex items-center group">
                            <span class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity">→</span>Yardım Merkezi
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-400 text-sm transition-colors flex items-center group">
                            <span class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity">→</span>İletişim
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-400 text-sm transition-colors flex items-center group">
                            <span class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity">→</span>Gizlilik Politikası
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-400 text-sm transition-colors flex items-center group">
                            <span class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity">→</span>Kullanım Şartları
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-400 text-sm transition-colors flex items-center group">
                            <span class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity">→</span>SSS
                        </a></li>
                    </ul>
                </div>
            </div>

            <!-- İstatistikler -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10 text-center">
                    <div class="text-2xl font-black text-orange-400 mb-1">{{ \App\Models\User::count() }}+</div>
                    <div class="text-xs text-gray-400">Aktif Oyuncu</div>
                </div>
                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10 text-center">
                    <div class="text-2xl font-black text-blue-400 mb-1">{{ \App\Models\Clan::count() }}+</div>
                    <div class="text-xs text-gray-400">Klan</div>
                </div>
                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10 text-center">
                    <div class="text-2xl font-black text-green-400 mb-1">{{ \App\Models\LfgPost::count() }}+</div>
                    <div class="text-xs text-gray-400">Takım İlanı</div>
                </div>
                <div class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10 text-center">
                    <div class="text-2xl font-black text-purple-400 mb-1">{{ \App\Models\CommunityPost::count() }}+</div>
                    <div class="text-xs text-gray-400">Paylaşım</div>
                </div>
            </div>

            <!-- Alt Bilgi -->
            <div class="flex flex-col md:flex-row justify-between items-center pt-8 border-t border-white/10">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    © {{ date('Y') }} PUBG Community. Tüm hakları saklıdır.
                </div>
                <div class="flex items-center space-x-4 text-xs text-gray-500">
                    <span>🚀 v1.0.0</span>
                    <span>•</span>
                    <span>Made with ❤️ in Turkey</span>
                    <span>•</span>
                    <span>🎮 {{ auth()->check() ? '1' : '0' }} Çevrimiçi</span>
                </div>
            </div>
        </div>
    </footer>

    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scale-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes float {
    0%, 100% { 
        transform: translate(0, 0) scale(1);
        opacity: 0.3;
    }
    33% { 
        transform: translate(30px, -30px) scale(1.1);
        opacity: 0.4;
    }
    66% { 
        transform: translate(-20px, 20px) scale(0.9);
        opacity: 0.35;
    }
}

@keyframes float-delayed {
    0%, 100% { 
        transform: translate(0, 0) scale(1);
        opacity: 0.3;
    }
    33% { 
        transform: translate(-30px, 30px) scale(1.1);
        opacity: 0.4;
    }
    66% { 
        transform: translate(20px, -20px) scale(0.9);
        opacity: 0.35;
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out forwards;
}

.animate-scale-in {
    animation: scale-in 0.6s ease-out forwards;
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 10s ease-in-out infinite;
    animation-delay: 2s;
}
</style>
@endsection
