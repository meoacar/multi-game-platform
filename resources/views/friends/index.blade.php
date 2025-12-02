@extends('layouts.app')

@section('content')
<!-- Arka Plan -->
<div class="fixed inset-0 z-0">
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
         style="background-image: url('{{ asset('arkaplan/5.jpg') }}');">
    </div>
    <!-- Orta seviye overlay -->
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900/70 via-gray-900/60 to-gray-900/70"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-transparent to-cyan-600/10"></div>
    
    <!-- Animasyonlu Parıltılar -->
    <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500/15 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
</div>

<div class="relative z-10 py-12" x-data="{ searchQuery: '', filterStatus: 'all' }">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12 animate-fade-in">
            <div class="inline-block bg-black/80 backdrop-blur-xl rounded-3xl p-8 border-2 border-blue-500/50 shadow-2xl hover:shadow-blue-500/60 hover:border-blue-400/60 transition-all duration-500 hover:scale-105">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-500 via-cyan-500 to-teal-600 rounded-3xl mb-6 shadow-2xl shadow-blue-500/50 animate-pulse-slow relative hover:rotate-6 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-cyan-600 rounded-3xl blur-xl opacity-50 animate-pulse"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-400 to-pink-600 rounded-3xl blur-2xl opacity-30 animate-pulse" style="animation-delay: 0.5s;"></div>
                    <svg class="w-12 h-12 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h1 class="text-6xl font-black mb-4 hover:scale-110 transition-transform duration-300" style="background: linear-gradient(135deg, #60a5fa 0%, #22d3ee 50%, #14b8a6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 0 30px rgba(96, 165, 250, 0.8)) drop-shadow(0 0 60px rgba(34, 211, 238, 0.6)) drop-shadow(0 4px 12px rgba(0, 0, 0, 0.9));">
                    👥 ARKADAŞLARIM
                </h1>
                <p class="text-white text-xl font-bold" style="text-shadow: 0 2px 20px rgba(0, 0, 0, 0.9), 0 0 40px rgba(0, 0, 0, 0.8);">Oyun arkadaşlarınla bağlantıda kal! 🎮</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sol Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- İstatistikler -->
                <div class="bg-gradient-to-br from-blue-600/90 to-cyan-600/90 backdrop-blur-xl rounded-3xl shadow-2xl shadow-blue-500/40 p-6 text-white border-2 border-blue-400/50 hover:shadow-blue-500/60 hover:scale-105 transition-all duration-500 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-pink-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <h3 class="text-xl font-black mb-5 flex items-center gap-2 relative z-10" style="text-shadow: 0 2px 15px rgba(0, 0, 0, 0.9), 0 0 30px rgba(0, 0, 0, 0.7);">
                        <svg class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                        </svg>
                        İSTATİSTİKLER
                    </h3>
                    <div class="space-y-4 relative z-10">
                        <div class="flex justify-between items-center bg-white/10 rounded-xl p-3 hover:bg-white/20 transition-all duration-300 hover:scale-105">
                            <span class="text-white font-bold" style="text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);">Toplam Arkadaş:</span>
                            <span class="text-3xl font-black" style="background: linear-gradient(135deg, #fcd34d 0%, #fb923c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 2px 15px rgba(251, 146, 60, 0.8)) drop-shadow(0 0 30px rgba(252, 211, 77, 0.6));">{{ $friends->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-white/10 rounded-xl p-3 hover:bg-white/20 transition-all duration-300 hover:scale-105">
                            <span class="text-white font-bold" style="text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);">Bekleyen İstek:</span>
                            <span class="text-3xl font-black" style="background: linear-gradient(135deg, #6ee7b7 0%, #10b981 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 2px 15px rgba(16, 185, 129, 0.8)) drop-shadow(0 0 30px rgba(110, 231, 183, 0.6));">{{ $pendingRequests ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Hızlı Aksiyonlar -->
                <div class="bg-gray-900/80 backdrop-blur-sm rounded-2xl shadow-lg shadow-blue-500/10 p-6 border-2 border-blue-500/30 hover:border-blue-400/50 transition-all duration-300">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2" style="text-shadow: 0 2px 12px rgba(0, 0, 0, 0.8);">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Hızlı Aksiyonlar
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('friends.requests') }}" class="w-full flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 rounded-xl text-white font-semibold transition-all duration-300 shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 hover:-translate-y-1 group">
                            <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            Arkadaşlık İstekleri
                        </a>
                        <a href="{{ route('lfg.index') }}" class="w-full flex items-center gap-3 px-4 py-3 bg-gray-700/50 hover:bg-gradient-to-r hover:from-blue-600 hover:to-cyan-600 border border-gray-600 hover:border-transparent rounded-xl text-gray-200 hover:text-white font-semibold transition-all duration-300 hover:scale-105 hover:-translate-y-1 group">
                            <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Yeni Arkadaş Bul
                        </a>
                    </div>
                </div>

                <!-- Online Durumu -->
                <div class="bg-gray-900/80 backdrop-blur-sm rounded-2xl shadow-lg shadow-green-500/10 p-6 border-2 border-green-500/30 hover:border-green-400/50 hover:shadow-green-500/30 transition-all duration-300">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2" style="text-shadow: 0 2px 12px rgba(0, 0, 0, 0.8);">
                        <div class="relative">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <div class="absolute inset-0 w-3 h-3 bg-green-400 rounded-full animate-ping"></div>
                        </div>
                        Online Arkadaşlar
                    </h3>
                    <div class="space-y-2">
                        @php
                            $onlineFriends = $friends->filter(fn($f) => $f->last_login_at && $f->last_login_at->diffInMinutes(now()) < 15);
                        @endphp
                        @if($onlineFriends->count() > 0)
                            @foreach($onlineFriends->take(5) as $friend)
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-gray-300">{{ $friend->name }}</span>
                            </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-400">Şu an kimse online değil</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ana İçerik -->
            <div class="lg:col-span-3">
                <!-- Arama ve Filtreler -->
                <div class="bg-gray-900/80 backdrop-blur-sm rounded-2xl shadow-lg shadow-blue-500/10 p-6 border-2 border-blue-500/30 mb-6 hover:border-blue-400/50 hover:shadow-blue-500/30 transition-all duration-300">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Arama -->
                        <div class="flex-1 relative group">
                            <input type="text" 
                                x-model="searchQuery"
                                placeholder="Arkadaş ara..."
                                class="w-full px-4 py-3 pl-12 bg-gray-800/80 border-2 border-gray-600 text-white rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 focus:bg-gray-800 transition-all duration-300 placeholder-gray-400 hover:border-gray-500">
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-4 group-focus-within:text-blue-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        <!-- Sıralama -->
                        <select x-model="filterStatus" class="px-4 py-3 bg-gray-800/80 border-2 border-gray-600 text-white rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 focus:bg-gray-800 transition-all duration-300 hover:border-gray-500 cursor-pointer">
                            <option value="all">Tümü</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                </div>

                <!-- Arkadaş Listesi -->
                @if($friends->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($friends as $friend)
                    <div class="bg-gray-900/90 backdrop-blur-sm rounded-2xl shadow-lg shadow-blue-500/10 border-2 border-gray-700/80 hover:border-blue-500/60 hover:shadow-blue-500/30 transition-all duration-500 overflow-hidden group hover:scale-105 hover:-translate-y-2 animate-fade-in">
                        <!-- Header -->
                        <div class="relative h-24 bg-gradient-to-br from-blue-600 via-purple-600 to-cyan-600 group-hover:from-purple-600 group-hover:via-pink-600 group-hover:to-blue-600 transition-all duration-700">
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-all duration-300"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            <!-- Online Badge -->
                            @if($friend->last_login_at && $friend->last_login_at->diffInMinutes(now()) < 15)
                            <div class="absolute top-3 right-3 flex items-center gap-2 px-3 py-1 bg-green-500 rounded-full text-white text-xs font-semibold shadow-lg shadow-green-500/50 animate-pulse">
                                <div class="relative">
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                    <div class="absolute inset-0 w-2 h-2 bg-white rounded-full animate-ping"></div>
                                </div>
                                Online
                            </div>
                            @endif
                        </div>

                        <!-- Avatar -->
                        <div class="relative px-6 -mt-12">
                            <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-purple-500/50 border-4 border-gray-900">
                                {{ strtoupper(substr($friend->name, 0, 1)) }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 pt-4">
                            <!-- User Info -->
                            <div class="mb-4">
                                <h3 class="text-xl font-bold text-white mb-1" style="text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8), 0 0 20px rgba(0, 0, 0, 0.6);">{{ $friend->name }}</h3>
                                @if($friend->profile)
                                <p class="text-sm text-gray-200 flex items-center gap-2" style="text-shadow: 0 1px 8px rgba(0, 0, 0, 0.8);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $friend->profile->nickname ?? 'PUBG Nick yok' }}
                                </p>
                                @endif
                            </div>

                            <!-- Stats -->
                            @if($friend->profile)
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                @if($friend->profile->rank)
                                <div class="bg-gray-800/80 rounded-lg p-3 border border-gray-600">
                                    <div class="text-xs text-gray-200 mb-1" style="text-shadow: 0 1px 6px rgba(0, 0, 0, 0.8);">Rank</div>
                                    <div class="text-sm font-bold text-white" style="text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);">{{ $friend->profile->rank }}</div>
                                </div>
                                @endif
                                @if($friend->profile->city)
                                <div class="bg-gray-800/80 rounded-lg p-3 border border-gray-600">
                                    <div class="text-xs text-gray-200 mb-1" style="text-shadow: 0 1px 6px rgba(0, 0, 0, 0.8);">Şehir</div>
                                    <div class="text-sm font-bold text-white" style="text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);">{{ $friend->profile->city }}</div>
                                </div>
                                @endif
                            </div>

                            @if($friend->profile->play_style)
                            <div class="mb-4">
                                <span class="inline-flex items-center gap-2 px-3 py-1 text-xs rounded-full bg-blue-600/30 text-blue-300 border border-blue-500/50 font-semibold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $friend->profile->play_style }}
                                </span>
                            </div>
                            @endif
                            @endif

                            <!-- Level Badge -->
                            <div class="mb-4">
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full text-white font-bold shadow-lg shadow-yellow-500/30">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    Level {{ $friend->getLevel() }}
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <a href="{{ route('profile.show', $friend->id) }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition shadow-lg shadow-blue-500/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profil
                                </a>
                                <a href="{{ route('messages.show', $friend->id) }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition shadow-lg shadow-green-500/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    Mesaj
                                </a>
                            </div>

                            <!-- Remove Friend -->
                            <form action="{{ route('friends.destroy', $friend->id) }}" method="POST" onsubmit="return confirm('Bu arkadaşlığı silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex items-center justify-center gap-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 border border-red-500/30 hover:border-red-500/50 rounded-xl py-2 text-sm font-semibold transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path>
                                    </svg>
                                    Arkadaşlıktan Çıkar
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                @else
                <!-- Empty State -->
                <div class="bg-gray-900/80 backdrop-blur-sm rounded-2xl shadow-lg shadow-blue-500/10 p-12 border-2 border-gray-700/80 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-700/50 rounded-2xl mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Henüz arkadaşınız yok</h3>
                    <p class="text-gray-400 mb-6">
                        Diğer oyuncularla tanışın ve arkadaşlık isteği gönderin.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('friends.requests') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-xl font-bold shadow-lg shadow-purple-500/30 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            Arkadaşlık İstekleri
                        </a>
                        <a href="{{ route('lfg.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-700/50 hover:bg-gray-700 border-2 border-gray-600 hover:border-gray-500 text-white rounded-xl font-bold transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Yeni Arkadaş Bul
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
