@extends('layouts.app')

@section('content')
<!-- Arka Plan -->
<div class="fixed inset-0 -z-10">
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900/95 via-gray-900/98 to-pink-900/95"></div>
    
    <!-- Arka Plan Resmi -->
    <div class="absolute inset-0 opacity-20">
        <img src="{{ asset('arkaplan/' . rand(1, 14) . '.jpg') }}" 
             alt="Background" 
             class="w-full h-full object-cover">
    </div>
    
    <!-- Animated Gradient Circles -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-pink-500/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
</div>

<div class="py-12 relative" x-data="tournamentForm()">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl mb-6 shadow-lg shadow-purple-500/50">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-3">
                Yeni Turnuva Oluştur
            </h1>
            <p class="text-gray-300 text-lg">Kendi turnuvanı düzenle ve oyuncuları bir araya getir! 🏆</p>
            
            <!-- XP Badge -->
            <div class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full text-white font-semibold shadow-lg">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span>+50 XP Kazan!</span>
            </div>
        </div>

        <form action="{{ route('tournaments.store') }}" method="POST" @submit="validateForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Sol Sidebar - İpuçları -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- İpuçları Kartı -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-purple-500/10 p-6 border-2 border-purple-500/20">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center shadow-lg shadow-purple-500/50">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">💡 İpuçları</h3>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">✓</span>
                                <span>Turnuva adını çekici ve açıklayıcı yapın</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">✓</span>
                                <span>Ödül havuzunu net belirtin</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">✓</span>
                                <span>Kuralları detaylı yazın</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">✓</span>
                                <span>Kayıt süresini yeterli tutun</span>
                            </li>
                        </ul>
                    </div>

                    <!-- İstatistikler -->
                    <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl shadow-lg p-6 text-white">
                        <h3 class="text-lg font-bold mb-4">📊 Turnuva İstatistikleri</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-purple-100">Tahmini Süre:</span>
                                <span class="font-bold" x-text="estimatedDuration"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-purple-100">Toplam Maç:</span>
                                <span class="font-bold" x-text="totalMatches"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-purple-100">Tur Sayısı:</span>
                                <span class="font-bold" x-text="totalRounds"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Popüler Formatlar -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-purple-500/10 p-6 border-2 border-purple-500/20">
                        <h3 class="text-lg font-bold text-white mb-4">🔥 Popüler Formatlar</h3>
                        <div class="space-y-2">
                            <button type="button" @click="applyTemplate('solo16')" class="w-full text-left px-4 py-3 bg-gray-700/50 hover:bg-purple-600/30 border border-gray-600 hover:border-purple-500 rounded-lg transition text-sm">
                                <div class="font-semibold text-white">Solo 16 Takım</div>
                                <div class="text-xs text-gray-400">Hızlı turnuva</div>
                            </button>
                            <button type="button" @click="applyTemplate('squad32')" class="w-full text-left px-4 py-3 bg-gray-700/50 hover:bg-purple-600/30 border border-gray-600 hover:border-purple-500 rounded-lg transition text-sm">
                                <div class="font-semibold text-white">Squad 32 Takım</div>
                                <div class="text-xs text-gray-400">Klasik format</div>
                            </button>
                            <button type="button" @click="applyTemplate('duo64')" class="w-full text-left px-4 py-3 bg-gray-700/50 hover:bg-purple-600/30 border border-gray-600 hover:border-purple-500 rounded-lg transition text-sm">
                                <div class="font-semibold text-white">Duo 64 Takım</div>
                                <div class="text-xs text-gray-400">Büyük turnuva</div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Ana Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Temel Bilgiler -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-blue-500/10 p-8 border-2 border-blue-500/20">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/50">
                                <span class="text-white font-bold">1</span>
                            </div>
                            <h2 class="text-2xl font-bold text-white">Temel Bilgiler</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Turnuva Adı -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-200 mb-2">
                                    🏆 Turnuva Adı *
                                </label>
                                <input type="text" name="name" id="name" required
                                    x-model="form.name"
                                    class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition @error('name') border-red-500 @enderror placeholder-gray-400"
                                    placeholder="Örn: PUBG Mobile Şampiyonası 2024"
                                    value="{{ old('name') }}">
                                <div class="mt-2 flex items-center justify-between text-xs">
                                    <span class="text-gray-400" x-show="form.name.length > 0" x-text="form.name.length + '/255 karakter'"></span>
                                    @error('name')
                                    <p class="text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Oyun -->
                                <div>
                                    <label for="game_id" class="block text-sm font-semibold text-gray-200 mb-2">
                                        🎮 Oyun *
                                    </label>
                                    <select name="game_id" id="game_id" required
                                        x-model="form.game_id"
                                        class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition @error('game_id') border-red-500 @enderror">
                                        <option value="">Oyun Seçin</option>
                                        @foreach($games as $game)
                                        <option value="{{ $game->id }}" {{ old('game_id') == $game->id ? 'selected' : '' }}>
                                            {{ $game->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('game_id')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Ödül Havuzu -->
                                <div>
                                    <label for="prize_pool" class="block text-sm font-semibold text-gray-200 mb-2">
                                        💰 Ödül Havuzu
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="prize_pool" id="prize_pool"
                                            x-model="form.prize_pool"
                                            class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition placeholder-gray-400"
                                            placeholder="Örn: 5000 TL"
                                            value="{{ old('prize_pool') }}">
                                        <div class="absolute right-3 top-3 text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Turnuva Formatı -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-green-500/10 p-8 border-2 border-green-500/20">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center shadow-lg shadow-green-500/50">
                                <span class="text-white font-bold">2</span>
                            </div>
                            <h2 class="text-2xl font-bold text-white">Turnuva Formatı</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Maksimum Takım -->
                            <div>
                                <label for="max_teams" class="block text-sm font-semibold text-gray-200 mb-2">
                                    👥 Maksimum Takım Sayısı *
                                </label>
                                <select name="max_teams" id="max_teams" required
                                    x-model="form.max_teams"
                                    @change="calculateStats"
                                    class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition">
                                    <option value="4">4 Takım (2 Tur)</option>
                                    <option value="8">8 Takım (3 Tur)</option>
                                    <option value="16" selected>16 Takım (4 Tur)</option>
                                    <option value="32">32 Takım (5 Tur)</option>
                                    <option value="64">64 Takım (6 Tur)</option>
                                </select>
                            </div>

                            <!-- Takım Boyutu -->
                            <div>
                                <label for="team_size" class="block text-sm font-semibold text-gray-200 mb-2">
                                    🎯 Takım Boyutu *
                                </label>
                                <div class="grid grid-cols-4 gap-2">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="team_size" value="1" x-model="form.team_size" class="peer sr-only" {{ old('team_size') == 1 ? 'checked' : '' }}>
                                        <div class="px-3 py-3 border-2 border-gray-600 bg-gray-700/30 rounded-xl text-center peer-checked:border-purple-500 peer-checked:bg-purple-600/30 transition">
                                            <div class="font-bold text-white">Solo</div>
                                            <div class="text-xs text-gray-400">1</div>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="team_size" value="2" x-model="form.team_size" class="peer sr-only" {{ old('team_size') == 2 ? 'checked' : '' }}>
                                        <div class="px-3 py-3 border-2 border-gray-600 bg-gray-700/30 rounded-xl text-center peer-checked:border-purple-500 peer-checked:bg-purple-600/30 transition">
                                            <div class="font-bold text-white">Duo</div>
                                            <div class="text-xs text-gray-400">2</div>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="team_size" value="4" x-model="form.team_size" class="peer sr-only" {{ old('team_size', 4) == 4 ? 'checked' : '' }}>
                                        <div class="px-3 py-3 border-2 border-gray-600 bg-gray-700/30 rounded-xl text-center peer-checked:border-purple-500 peer-checked:bg-purple-600/30 transition">
                                            <div class="font-bold text-white">Squad</div>
                                            <div class="text-xs text-gray-400">4</div>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="team_size" value="5" x-model="form.team_size" class="peer sr-only" {{ old('team_size') == 5 ? 'checked' : '' }}>
                                        <div class="px-3 py-3 border-2 border-gray-600 bg-gray-700/30 rounded-xl text-center peer-checked:border-purple-500 peer-checked:bg-purple-600/30 transition">
                                            <div class="font-bold text-white">5v5</div>
                                            <div class="text-xs text-gray-400">5</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarih ve Saat -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-orange-500/10 p-8 border-2 border-orange-500/20">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center shadow-lg shadow-orange-500/50">
                                <span class="text-white font-bold">3</span>
                            </div>
                            <h2 class="text-2xl font-bold text-white">Tarih ve Saat</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kayıt Başlangıç -->
                            <div>
                                <label for="registration_starts_at" class="block text-sm font-semibold text-gray-200 mb-2">
                                    📅 Kayıt Başlangıç *
                                </label>
                                <input type="datetime-local" name="registration_starts_at" id="registration_starts_at" required
                                    x-model="form.registration_starts_at"
                                    class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition"
                                    value="{{ old('registration_starts_at') }}">
                            </div>

                            <!-- Kayıt Bitiş -->
                            <div>
                                <label for="registration_ends_at" class="block text-sm font-semibold text-gray-200 mb-2">
                                    📅 Kayıt Bitiş *
                                </label>
                                <input type="datetime-local" name="registration_ends_at" id="registration_ends_at" required
                                    x-model="form.registration_ends_at"
                                    class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition"
                                    value="{{ old('registration_ends_at') }}">
                            </div>

                            <!-- Turnuva Başlangıç -->
                            <div>
                                <label for="tournament_starts_at" class="block text-sm font-semibold text-gray-200 mb-2">
                                    🏁 Turnuva Başlangıç *
                                </label>
                                <input type="datetime-local" name="tournament_starts_at" id="tournament_starts_at" required
                                    x-model="form.tournament_starts_at"
                                    class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition"
                                    value="{{ old('tournament_starts_at') }}">
                            </div>

                            <!-- Turnuva Bitiş -->
                            <div>
                                <label for="tournament_ends_at" class="block text-sm font-semibold text-gray-200 mb-2">
                                    🏁 Turnuva Bitiş *
                                </label>
                                <input type="datetime-local" name="tournament_ends_at" id="tournament_ends_at" required
                                    x-model="form.tournament_ends_at"
                                    class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition"
                                    value="{{ old('tournament_ends_at') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Açıklama ve Kurallar -->
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-pink-500/10 p-8 border-2 border-pink-500/20">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-500 rounded-lg flex items-center justify-center shadow-lg shadow-pink-500/50">
                                <span class="text-white font-bold">4</span>
                            </div>
                            <h2 class="text-2xl font-bold text-white">Detaylar</h2>
                        </div>

                        <div class="space-y-6">
                            <!-- Açıklama -->
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-200 mb-2">
                                    📝 Açıklama *
                                </label>
                                <textarea name="description" id="description" rows="4" required
                                    x-model="form.description"
                                    class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition resize-none placeholder-gray-400"
                                    placeholder="Turnuva hakkında detaylı bilgi verin...">{{ old('description') }}</textarea>
                                <div class="mt-2 text-xs text-gray-400" x-show="form.description.length > 0" x-text="form.description.length + ' karakter'"></div>
                            </div>

                            <!-- Kurallar -->
                            <div>
                                <label for="rules" class="block text-sm font-semibold text-gray-200 mb-2">
                                    📋 Kurallar *
                                </label>
                                <textarea name="rules" id="rules" rows="8" required
                                    x-model="form.rules"
                                    class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition resize-none font-mono text-sm placeholder-gray-400"
                                    placeholder="1. Turnuva kuralı...&#10;2. Katılım şartları...&#10;3. Hile yasağı...">{{ old('rules') }}</textarea>
                                <div class="mt-2 text-xs text-gray-400" x-show="form.rules.length > 0" x-text="form.rules.length + ' karakter'"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-xl hover:from-purple-700 hover:to-pink-700 font-bold text-lg shadow-lg shadow-purple-500/50 hover:shadow-xl hover:shadow-purple-500/60 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Turnuvayı Oluştur
                        </button>
                        <a href="{{ route('tournaments.index') }}" 
                            class="flex-1 text-center bg-gray-700/50 text-gray-200 px-8 py-4 rounded-xl hover:bg-gray-700 border-2 border-gray-600 hover:border-gray-500 font-bold text-lg transition flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            İptal
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function tournamentForm() {
    return {
        form: {
            name: '',
            game_id: '',
            prize_pool: '',
            max_teams: 16,
            team_size: 4,
            registration_starts_at: '',
            registration_ends_at: '',
            tournament_starts_at: '',
            tournament_ends_at: '',
            description: '',
            rules: ''
        },
        
        get totalMatches() {
            return this.form.max_teams ? this.form.max_teams - 1 : 0;
        },
        
        get totalRounds() {
            return this.form.max_teams ? Math.log2(this.form.max_teams) : 0;
        },
        
        get estimatedDuration() {
            const rounds = this.totalRounds;
            if (rounds === 0) return '0 saat';
            const hours = rounds * 1.5; // Her tur ~1.5 saat
            return Math.ceil(hours) + ' saat';
        },
        
        calculateStats() {
            // İstatistikleri güncelle
        },
        
        applyTemplate(template) {
            if (template === 'solo16') {
                this.form.max_teams = 16;
                this.form.team_size = 1;
                this.form.name = 'Solo Turnuvası';
                this.form.description = 'Hızlı tempolu solo turnuvası. En iyi 16 oyuncu yarışacak!';
            } else if (template === 'squad32') {
                this.form.max_teams = 32;
                this.form.team_size = 4;
                this.form.name = 'Squad Şampiyonası';
                this.form.description = 'Klasik squad formatında büyük turnuva. 32 takım mücadele edecek!';
            } else if (template === 'duo64') {
                this.form.max_teams = 64;
                this.form.team_size = 2;
                this.form.name = 'Duo Mega Turnuva';
                this.form.description = 'Dev duo turnuvası! 64 takım, büyük ödüller!';
            }
        },
        
        validateForm(e) {
            // Form validasyonu
            if (!this.form.name || !this.form.game_id || !this.form.description || !this.form.rules) {
                e.preventDefault();
                alert('Lütfen tüm zorunlu alanları doldurun!');
                return false;
            }
        }
    }
}
</script>
@endsection
