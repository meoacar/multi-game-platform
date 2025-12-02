@extends('layouts.app')

@section('title', 'Yeni Klan Oluştur')

@section('content')
<!-- Full Page Background -->
<div class="fixed inset-0 z-0 bg-cover bg-center bg-fixed" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('{{ asset('arkaplan/14.jpg') }}');"></div>

<!-- Content Wrapper -->
<div class="relative z-10">
    <!-- Hero Header -->
    <div class="text-center py-12 mb-8">
        <h1 class="text-6xl font-black bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 bg-clip-text text-transparent mb-3 drop-shadow-2xl">
            👑 Yeni Klan Oluştur
        </h1>
        <p class="text-white text-xl font-semibold drop-shadow-lg">Kendi klanını kur, lider ol! +20 XP kazanacaksın 🎉</p>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-12" x-data="{ 
    selectedGame: '{{ old('game_id') }}',
    clanName: '{{ old('name') }}',
    description: '{{ old('description') }}',
    maxMembers: {{ old('max_members', 50) }},
    showPreview: false
}">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">1</div>
                <span class="ml-2 text-white font-semibold">Temel Bilgiler</span>
            </div>
            <div class="w-16 h-1 bg-white/20"></div>
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white font-bold">2</div>
                <span class="ml-2 text-gray-400 font-semibold">Gereksinimler</span>
            </div>
            <div class="w-16 h-1 bg-white/20"></div>
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white font-bold">3</div>
                <span class="ml-2 text-gray-400 font-semibold">Yayınla</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('clans.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Oyun Seçimi -->
                <div class="bg-gray-800 rounded-2xl border-2 border-purple-500/50 p-6 shadow-2xl shadow-purple-500/20">
                    <label class="block text-xl font-black text-white mb-4 drop-shadow-lg">
                        🎮 Hangi Oyun İçin Klan Kuruyorsun? <span class="text-red-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($games as $game)
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="game_id" value="{{ $game->id }}" 
                                    x-model="selectedGame"
                                    {{ old('game_id') == $game->id ? 'checked' : '' }}
                                    class="peer sr-only" required>
                                <div class="bg-white/5 border-2 border-white/20 rounded-xl p-4 transition-all peer-checked:border-purple-500 peer-checked:bg-purple-500/10 hover:border-white/40">
                                    <div class="text-center">
                                        <div class="text-3xl mb-2">🎯</div>
                                        <div class="text-white font-semibold">{{ $game->name }}</div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('game_id')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Klan Adı ve Açıklama -->
                <div class="bg-gray-800 rounded-2xl border-2 border-purple-500/50 p-6 shadow-2xl shadow-purple-500/20">
                    <label class="block text-xl font-black text-white mb-4 drop-shadow-lg">
                        👑 Klan Adı <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="name" x-model="clanName" value="{{ old('name') }}" required
                        class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 transition-all placeholder-gray-500 @error('name') border-red-500 @enderror"
                        placeholder="Örn: Elite Warriors, Phoenix Squad"
                        maxlength="100">
                    <div class="flex justify-between mt-2">
                        <span class="text-gray-300 text-sm font-medium">Benzersiz ve akılda kalıcı bir isim seç</span>
                        <span class="text-gray-300 text-sm font-medium" x-text="clanName.length + '/100'">0/100</span>
                    </div>
                    @error('name')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror

                    <label class="block text-xl font-black text-white mb-4 mt-6 drop-shadow-lg">
                        📝 Klan Açıklaması <span class="text-red-400">*</span>
                    </label>
                    <textarea name="description" x-model="description" rows="6" required
                        class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 transition-all placeholder-gray-500 @error('description') border-red-500 @enderror"
                        placeholder="Klanınızı tanıtın... Hedefleriniz neler? Nasıl bir topluluk oluşturmak istiyorsunuz?">{{ old('description') }}</textarea>
                    <div class="flex justify-between mt-2">
                        <span class="text-gray-300 text-sm font-medium">Klanınızı detaylı tanıtın</span>
                        <span class="text-gray-300 text-sm font-medium" x-text="description.length + ' karakter'">0 karakter</span>
                    </div>
                    @error('description')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gereksinimler -->
                <div class="bg-gray-800 rounded-2xl border-2 border-purple-500/50 p-6 shadow-2xl shadow-purple-500/20">
                    <label class="block text-xl font-black text-white mb-4 drop-shadow-lg">
                        📋 Katılım Gereksinimleri
                    </label>
                    <textarea name="requirements" rows="4"
                        class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 transition-all placeholder-gray-500"
                        placeholder="Örn: Minimum Gold rütbesi, aktif oyuncu, Discord kullanımı zorunlu...">{{ old('requirements') }}</textarea>
                    <p class="text-gray-300 text-sm mt-2 font-medium">Klana katılmak isteyenlerin karşılaması gereken şartlar</p>
                </div>

                <!-- Rütbe ve Yaş Gereksinimleri -->
                <div class="bg-gray-800 rounded-2xl border-2 border-purple-500/50 p-6 shadow-2xl shadow-purple-500/20">
                    <label class="block text-xl font-black text-white mb-4 drop-shadow-lg">
                        🏆 Rütbe ve Yaş Gereksinimleri
                    </label>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Minimum Rütbe</label>
                            <input type="text" name="min_rank" value="{{ old('min_rank') }}"
                                class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 transition-all placeholder-gray-500"
                                placeholder="Örn: Gold, Platinum">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Maximum Rütbe</label>
                            <input type="text" name="max_rank" value="{{ old('max_rank') }}"
                                class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 transition-all placeholder-gray-500"
                                placeholder="Örn: Ace, Conqueror">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                <span class="mr-1">👶</span> Min Yaş
                            </label>
                            <input type="number" name="min_age_range" value="{{ old('min_age_range') }}"
                                class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 transition-all placeholder-gray-500"
                                placeholder="18" min="13" max="99">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">
                                <span class="mr-1">👴</span> Max Yaş
                            </label>
                            <input type="number" name="max_age_range" value="{{ old('max_age_range') }}"
                                class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 transition-all placeholder-gray-500"
                                placeholder="35" min="13" max="99">
                        </div>
                    </div>
                </div>

                <!-- Klan Ayarları -->
                <div class="bg-gray-800 rounded-2xl border-2 border-purple-500/50 p-6 shadow-2xl shadow-purple-500/20">
                    <label class="block text-xl font-black text-white mb-4 drop-shadow-lg">
                        ⚙️ Klan Ayarları
                    </label>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            <span class="mr-1">👥</span> Maksimum Üye Sayısı
                        </label>
                        <div class="flex items-center space-x-4">
                            <input type="range" name="max_members" x-model="maxMembers" value="{{ old('max_members', 50) }}" 
                                min="5" max="100" step="5"
                                class="flex-1 h-2 bg-white/20 rounded-lg appearance-none cursor-pointer accent-purple-500">
                            <span class="text-white font-bold text-xl min-w-[60px] text-center" x-text="maxMembers">50</span>
                        </div>
                        <p class="text-gray-300 text-sm mt-2 font-medium">Klanınızda maksimum kaç üye olabilir?</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            <span class="mr-1">🌍</span> Şehir
                        </label>
                        <input type="text" name="city" value="{{ old('city') }}"
                            class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 transition-all placeholder-gray-500"
                            placeholder="Örn: İstanbul, Ankara, İzmir">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            <span class="mr-1">💬</span> Discord Davet Linki
                        </label>
                        <input type="url" name="discord_invite" value="{{ old('discord_invite') }}"
                            class="w-full bg-white/10 border-2 border-white/20 text-white rounded-xl px-4 py-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 transition-all placeholder-gray-500"
                            placeholder="https://discord.gg/...">
                        <p class="text-gray-300 text-sm mt-2 font-medium">Klan üyelerinin iletişim kurabileceği Discord sunucusu</p>
                    </div>
                </div>

                <!-- Butonlar -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 transition-all transform hover:scale-105 flex items-center justify-center">
                        <span class="mr-2">👑</span> Klanı Oluştur (+20 XP)
                    </button>
                    <a href="{{ route('clans.index') }}" 
                        class="flex-1 bg-white/10 hover:bg-white/20 text-white font-bold py-4 px-8 rounded-xl border-2 border-white/20 transition-all text-center flex items-center justify-center">
                        <span class="mr-2">❌</span> İptal
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar - Önizleme ve İpuçları -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Canlı Önizleme -->
            <div class="bg-gray-800 rounded-2xl border-2 border-purple-500/50 p-6 sticky top-6 shadow-2xl shadow-purple-500/20">
                <h3 class="text-2xl font-black text-white mb-4 flex items-center drop-shadow-lg">
                    <span class="mr-2">👁️</span> Canlı Önizleme
                </h3>
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="text-white font-bold text-lg mb-1 flex items-center">
                                <span class="mr-2">👑</span>
                                <span x-text="clanName || 'Klan Adı'">Klan Adı</span>
                            </h4>
                            <p class="text-gray-400 text-sm">Lider: {{ Auth::user()->name }}</p>
                        </div>
                        <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-lg text-xs font-bold">YENİ</span>
                    </div>
                    <p class="text-gray-300 text-sm mb-3 line-clamp-3" x-text="description || 'Klan açıklaması buraya gelecek...'">Klan açıklaması buraya gelecek...</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-400">Üye Sayısı</span>
                        <span class="text-white font-bold">1/<span x-text="maxMembers">50</span></span>
                    </div>
                </div>
            </div>

            <!-- İpuçları -->
            <div class="bg-gradient-to-br from-purple-700 to-pink-700 rounded-2xl border-2 border-purple-400/60 p-6 shadow-2xl shadow-pink-500/20">
                <h3 class="text-2xl font-black text-white mb-4 flex items-center drop-shadow-lg">
                    <span class="mr-2">💡</span> İpuçları
                </h3>
                <ul class="space-y-3 text-base text-white font-medium">
                    <li class="flex items-start">
                        <span class="text-green-400 mr-2 text-lg">✓</span>
                        <span>Klan adını dikkatli seç, sonradan değiştirilemez</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-400 mr-2 text-lg">✓</span>
                        <span>Klan kurallarını ve hedeflerini açıkça belirt</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-400 mr-2 text-lg">✓</span>
                        <span>Discord sunucusu oluştur, iletişim önemli</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-400 mr-2 text-lg">✓</span>
                        <span>Gerçekçi gereksinimler belirle</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-yellow-400 mr-2 text-lg">⚡</span>
                        <span>Klan kurarak <strong class="text-yellow-300">+20 XP</strong> kazanırsın!</span>
                    </li>
                </ul>
            </div>

            <!-- Klan Lideri Bilgileri -->
            <div class="bg-gray-800 rounded-2xl border-2 border-purple-500/50 p-6 shadow-2xl shadow-purple-500/20">
                <h3 class="text-2xl font-black text-white mb-4 drop-shadow-lg">👑 Lider Bilgileri</h3>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-white font-bold">{{ Auth::user()->name }}</p>
                        <p class="text-gray-400 text-sm">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="space-y-2 text-base">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-200 font-medium">Toplam XP</span>
                        <span class="text-orange-400 font-bold text-lg">{{ Auth::user()->xp_total ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-200 font-medium">Klan Sayısı</span>
                        <span class="text-white font-bold text-lg">{{ Auth::user()->ownedClans()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
