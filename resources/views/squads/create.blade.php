@extends('layouts.app')

@section('title', 'Yeni Takım Oluştur')

@push('styles')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    
    .delay-1000 {
        animation-delay: 1s;
    }
    
    /* Custom scrollbar */
    textarea::-webkit-scrollbar {
        width: 8px;
    }
    
    textarea::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }
    
    textarea::-webkit-scrollbar-thumb {
        background: rgba(34, 197, 94, 0.5);
        border-radius: 10px;
    }
    
    textarea::-webkit-scrollbar-thumb:hover {
        background: rgba(34, 197, 94, 0.7);
    }
</style>
@endpush

@section('content')
<!-- Hero Banner with Background -->
<div class="relative min-h-screen bg-gradient-to-br from-green-900/60 via-emerald-900/60 to-teal-900/60 text-white overflow-hidden" style="background-image: url('/arkaplan/13.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">
    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black/85"></div>
    
    <!-- Animated Particles -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute w-96 h-96 bg-green-500/10 rounded-full blur-3xl -top-48 -left-48 animate-pulse"></div>
        <div class="absolute w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -bottom-48 -right-48 animate-pulse delay-1000"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <!-- Animated Badge -->
            <div class="inline-flex items-center gap-2 bg-green-500/20 backdrop-blur-sm border border-green-500/30 rounded-full px-6 py-2 mb-6 animate-bounce">
                <span class="text-2xl">⚡</span>
                <span class="text-sm font-bold text-green-300">+25 XP Kazan</span>
            </div>
            
            <div class="text-8xl mb-6 animate-float">👥</div>
            <h1 class="text-6xl md:text-7xl font-black mb-4 bg-gradient-to-r from-green-400 via-emerald-300 to-teal-400 bg-clip-text text-transparent">
                YENİ TAKIM OLUŞTUR
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 max-w-2xl mx-auto">
                Arkadaşlarınla birlikte oyna, stratejiler geliştir ve zafere ulaş! 🏆
            </p>
            
            <!-- Stats -->
            <div class="flex justify-center gap-8 mt-8">
                <div class="text-center">
                    <div class="text-3xl font-black text-green-400">2-10</div>
                    <div class="text-sm text-gray-400">Üye Kapasitesi</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-black text-emerald-400">∞</div>
                    <div class="text-sm text-gray-400">Sınırsız Oyun</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-black text-teal-400">+25</div>
                    <div class="text-sm text-gray-400">XP Kazanç</div>
                </div>
            </div>
        </div>
    </div>

<div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-20">
    <!-- Main Form Card -->
    <div class="relative bg-gradient-to-br from-gray-900/95 to-black/95 backdrop-blur-xl rounded-3xl border-2 border-green-500/30 overflow-hidden shadow-2xl">
        <!-- Glow Effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-green-500/5 via-emerald-500/5 to-teal-500/5 animate-pulse"></div>
        
        <!-- Header -->
        <div class="relative bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 px-8 py-6 border-b-2 border-green-500/50">
            <div class="flex items-center justify-between">
                <h2 class="text-3xl font-black text-white flex items-center gap-3">
                    <span class="text-4xl animate-bounce">⚙️</span>
                    TAKIM BİLGİLERİ
                </h2>
                <div class="hidden md:flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2">
                    <span class="text-yellow-400">⭐</span>
                    <span class="text-sm font-bold text-white">Ücretsiz</span>
                </div>
            </div>
        </div>

        <form action="{{ route('squads.store') }}" method="POST" class="relative p-8 space-y-8">
            @csrf

            <!-- Takım Adı -->
            <div class="group">
                <label class="block text-white font-bold mb-3 flex items-center gap-2">
                    <span class="text-xl">🏷️</span>
                    Takım Adı *
                </label>
                <div class="relative">
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full bg-gradient-to-r from-black/50 to-gray-900/50 border-2 border-green-500/30 rounded-2xl px-6 py-4 text-white text-lg placeholder-gray-500 focus:border-green-500 focus:ring-4 focus:ring-green-500/20 focus:outline-none transition-all group-hover:border-green-500/50"
                        placeholder="Örn: Erangel Warriors">
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </div>
                </div>
                @error('name')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                        <span>⚠️</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Açıklama -->
            <div class="group">
                <label class="block text-white font-bold mb-3 flex items-center gap-2">
                    <span class="text-xl">📝</span>
                    Açıklama
                </label>
                <textarea name="description" rows="5"
                    class="w-full bg-gradient-to-r from-black/50 to-gray-900/50 border-2 border-green-500/30 rounded-2xl px-6 py-4 text-white text-lg placeholder-gray-500 focus:border-green-500 focus:ring-4 focus:ring-green-500/20 focus:outline-none transition-all resize-none group-hover:border-green-500/50"
                    placeholder="Takımınız hakkında bilgi verin... (Oyun tarzı, hedefler, kurallar vb.)">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                        <span>⚠️</span>
                        {{ $message }}
                    </p>
                @enderror
                <p class="text-gray-400 text-sm mt-2">💡 İyi bir açıklama, doğru oyuncuları çekmenize yardımcı olur</p>
            </div>

            <!-- Grid Layout for Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Maksimum Üye -->
                <div class="group">
                    <label class="block text-white font-bold mb-3 flex items-center gap-2">
                        <span class="text-xl">👥</span>
                        Maksimum Üye Sayısı *
                    </label>
                    <div class="relative">
                        <select name="max_members" required
                            class="w-full bg-gradient-to-r from-black/50 to-gray-900/50 border-2 border-green-500/30 rounded-2xl px-6 py-4 text-white text-lg focus:border-green-500 focus:ring-4 focus:ring-green-500/20 focus:outline-none transition-all appearance-none cursor-pointer group-hover:border-green-500/50">
                            <option value="2" {{ old('max_members') == 2 ? 'selected' : '' }}>👥 2 Kişi (Duo)</option>
                            <option value="4" {{ old('max_members', 4) == 4 ? 'selected' : '' }}>👥👥 4 Kişi (Squad)</option>
                            <option value="5" {{ old('max_members') == 5 ? 'selected' : '' }}>👥👥 5 Kişi</option>
                            <option value="10" {{ old('max_members') == 10 ? 'selected' : '' }}>👥👥👥 10 Kişi</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('max_members')
                        <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                            <span>⚠️</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Oyun Modu -->
                <div class="group">
                    <label class="block text-white font-bold mb-3 flex items-center gap-2">
                        <span class="text-xl">🎮</span>
                        Oyun Modu
                    </label>
                    <div class="relative">
                        <select name="game_mode"
                            class="w-full bg-gradient-to-r from-black/50 to-gray-900/50 border-2 border-green-500/30 rounded-2xl px-6 py-4 text-white text-lg focus:border-green-500 focus:ring-4 focus:ring-green-500/20 focus:outline-none transition-all appearance-none cursor-pointer group-hover:border-green-500/50">
                            <option value="">🎯 Farketmez</option>
                            <option value="TPP" {{ old('game_mode') == 'TPP' ? 'selected' : '' }}>👁️ TPP (Third Person)</option>
                            <option value="FPP" {{ old('game_mode') == 'FPP' ? 'selected' : '' }}>🔫 FPP (First Person)</option>
                            <option value="Both" {{ old('game_mode') == 'Both' ? 'selected' : '' }}>🎮 Her İkisi</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('game_mode')
                        <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                            <span>⚠️</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Rütbe Gereksinimi -->
                <div class="group">
                    <label class="block text-white font-bold mb-3 flex items-center gap-2">
                        <span class="text-xl">🏆</span>
                        Rütbe Gereksinimi
                    </label>
                    <div class="relative">
                        <input type="text" name="rank_requirement" value="{{ old('rank_requirement') }}"
                            class="w-full bg-gradient-to-r from-black/50 to-gray-900/50 border-2 border-green-500/30 rounded-2xl px-6 py-4 text-white text-lg placeholder-gray-500 focus:border-green-500 focus:ring-4 focus:ring-green-500/20 focus:outline-none transition-all group-hover:border-green-500/50"
                            placeholder="Örn: Platinum+">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('rank_requirement')
                        <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                            <span>⚠️</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Bölge -->
                <div class="group">
                    <label class="block text-white font-bold mb-3 flex items-center gap-2">
                        <span class="text-xl">🌍</span>
                        Bölge
                    </label>
                    <div class="relative">
                        <select name="region"
                            class="w-full bg-gradient-to-r from-black/50 to-gray-900/50 border-2 border-green-500/30 rounded-2xl px-6 py-4 text-white text-lg focus:border-green-500 focus:ring-4 focus:ring-green-500/20 focus:outline-none transition-all appearance-none cursor-pointer group-hover:border-green-500/50">
                            <option value="">🌐 Seçiniz</option>
                            <option value="Europe" {{ old('region') == 'Europe' ? 'selected' : '' }}>🇪🇺 Europe</option>
                            <option value="Asia" {{ old('region') == 'Asia' ? 'selected' : '' }}>🇨🇳 Asia</option>
                            <option value="North America" {{ old('region') == 'North America' ? 'selected' : '' }}>🇺🇸 North America</option>
                            <option value="South America" {{ old('region') == 'South America' ? 'selected' : '' }}>🇧🇷 South America</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('region')
                        <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                            <span>⚠️</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Butonlar -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6">
                <button type="submit"
                    class="flex-1 group relative bg-gradient-to-r from-green-500 via-emerald-600 to-teal-600 hover:from-green-600 hover:via-emerald-700 hover:to-teal-700 text-white font-black py-5 px-8 rounded-2xl transition-all shadow-2xl transform hover:scale-105 hover:shadow-green-500/50 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                    <span class="relative flex items-center justify-center gap-3 text-lg">
                        <span class="text-2xl">✅</span>
                        TAKIMI OLUŞTUR
                        <span class="bg-yellow-400 text-green-900 px-3 py-1 rounded-full text-sm font-black">+25 XP</span>
                    </span>
                </button>
                <a href="{{ route('squads.index') }}"
                    class="flex-1 bg-gradient-to-r from-gray-700 to-gray-800 hover:from-gray-800 hover:to-gray-900 text-white font-bold py-5 px-8 rounded-2xl transition-all text-center shadow-xl transform hover:scale-105 flex items-center justify-center gap-2">
                    <span class="text-xl">❌</span>
                    <span class="text-lg">İptal</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Info Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <!-- İpuçları -->
        <div class="bg-gradient-to-br from-blue-900 to-blue-800 backdrop-blur-sm border-2 border-blue-500/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-white font-black text-xl mb-4 flex items-center gap-3">
                <span class="text-3xl">💡</span>
                İpuçları
            </h3>
            <ul class="text-gray-200 space-y-3">
                <li class="flex items-start gap-3">
                    <span class="text-green-400 text-xl flex-shrink-0">✅</span>
                    <span>Takım adını dikkatli seçin, sonradan değiştirilemez</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-green-400 text-xl flex-shrink-0">✅</span>
                    <span>Açıklama kısmında takım hedeflerinizi ve oyun tarzınızı belirtin</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-green-400 text-xl flex-shrink-0">✅</span>
                    <span>Rütbe gereksinimi belirterek uygun oyuncuları çekin</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-yellow-400 text-xl flex-shrink-0">⭐</span>
                    <span>Takım oluşturarak <strong class="text-yellow-400">25 XP</strong> kazanırsınız!</span>
                </li>
            </ul>
        </div>

        <!-- Avantajlar -->
        <div class="bg-gradient-to-br from-purple-900 to-purple-800 backdrop-blur-sm border-2 border-purple-500/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-white font-black text-xl mb-4 flex items-center gap-3">
                <span class="text-3xl">🎯</span>
                Takım Avantajları
            </h3>
            <ul class="text-gray-200 space-y-3">
                <li class="flex items-start gap-3">
                    <span class="text-purple-400 text-xl flex-shrink-0">🤝</span>
                    <span>Düzenli oyun arkadaşları bulun</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-purple-400 text-xl flex-shrink-0">📈</span>
                    <span>Takım stratejileri geliştirin</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-purple-400 text-xl flex-shrink-0">🏆</span>
                    <span>Turnuvalara katılın</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-purple-400 text-xl flex-shrink-0">💬</span>
                    <span>Özel takım sohbeti</span>
                </li>
            </ul>
        </div>
    </div>
</div>
</div>
@endsection
