@extends('layouts.app')

@section('title', 'Profil Düzenle')

@section('content')
<!-- PUBG Mobile Arka Plan -->
<div class="fixed inset-0 z-0 overflow-hidden pointer-events-none" 
     style="background-image: url('/arkaplan/16.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    
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

<div class="relative z-10 min-h-screen py-12" x-data="{ activeTab: 'profile' }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-500/20 border border-green-500/50 rounded-2xl p-4 backdrop-blur-xl animate-fade-in">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">✅</span>
                    <p class="text-green-400 font-bold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-500/20 border border-red-500/50 rounded-2xl p-4 backdrop-blur-xl animate-fade-in">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">❌</span>
                    <p class="text-red-400 font-bold">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-500/20 border border-red-500/50 rounded-2xl p-4 backdrop-blur-xl animate-fade-in">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">⚠️</span>
                    <p class="text-red-400 font-bold">Lütfen aşağıdaki hataları düzeltin:</p>
                </div>
                <ul class="list-disc list-inside text-red-300 text-sm space-y-1 ml-8">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Hero Header -->
        <div class="text-center mb-12 animate-fade-in">
            <div class="inline-block mb-4">
                <div class="relative">
                    <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-orange-500 via-red-500 to-pink-500 p-1 shadow-2xl shadow-orange-500/50 animate-pulse-slow">
                        <img src="{{ $user->profile->avatar_url ?? asset('images/default-avatar.svg') }}" 
                             alt="Avatar" 
                             class="w-full h-full rounded-full object-cover border-4 border-gray-900">
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg border-4 border-gray-900">
                        <span class="text-2xl">✨</span>
                    </div>
                </div>
            </div>
            <h1 class="text-5xl font-black mb-3">
                <span class="bg-gradient-to-r from-orange-400 via-red-400 to-pink-400 bg-clip-text text-transparent">
                    Profilini Özelleştir
                </span>
            </h1>
            <p class="text-gray-400 text-lg">Kendini ifade et, topluluğa katıl! 🎮</p>

            <!-- Profil Tamamlama Durumu -->
            @if($user->profile_completion < 100)
                <div class="mt-6 max-w-md mx-auto">
                    <div class="bg-gradient-to-r from-orange-500/20 to-red-500/20 backdrop-blur-xl rounded-2xl p-4 border border-orange-500/30">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-white font-bold">Profil Tamamlanma</span>
                            <span class="text-orange-400 font-black text-lg">{{ $user->profile_completion }}%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-500 to-red-500 h-full rounded-full transition-all duration-500" 
                                 style="width: {{ $user->profile_completion }}%"></div>
                        </div>
                        <p class="text-gray-300 text-sm mt-3">
                            💡 Profilini tamamlayarak daha fazla özelliğe erişebilirsin!
                        </p>
                    </div>
                </div>
            @else
                <div class="mt-6">
                    <div class="inline-flex items-center space-x-2 bg-green-500/20 backdrop-blur-xl rounded-full px-6 py-3 border border-green-500/30">
                        <span class="text-2xl">✅</span>
                        <span class="text-green-400 font-bold">Profilin Tam!</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Tab Navigation -->
        <div class="flex justify-center mb-8 animate-scale-in">
            <div class="inline-flex bg-gray-800/50 backdrop-blur-xl rounded-2xl p-2 border border-white/10 shadow-2xl">
                <button @click="activeTab = 'profile'" 
                        :class="activeTab === 'profile' ? 'bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                        class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center space-x-2">
                    <span class="text-xl">🎮</span>
                    <span>PUBG Bilgileri</span>
                </button>
                <button @click="activeTab = 'avatar'" 
                        :class="activeTab === 'avatar' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                        class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center space-x-2">
                    <span class="text-xl">📸</span>
                    <span>Avatar</span>
                </button>
                <button @click="activeTab = 'social'" 
                        :class="activeTab === 'social' ? 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                        class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center space-x-2">
                    <span class="text-xl">🔗</span>
                    <span>Sosyal Medya</span>
                </button>
            </div>
        </div>

        <!-- PUBG Bilgileri ve Sosyal Medya Formu -->
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6" id="profile-form">
            @csrf
            @method('PUT')

            <!-- PUBG Bilgileri Tab -->
            <div x-show="activeTab === 'profile'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="bg-gradient-to-br from-gray-800/50 via-gray-900/50 to-black/50 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl overflow-hidden">
                
                <div class="bg-gradient-to-r from-orange-600/20 via-red-600/20 to-pink-600/20 px-8 py-6 border-b border-white/10">
                    <h2 class="text-3xl font-black text-white flex items-center space-x-3">
                        <span class="text-4xl">🎮</span>
                        <span>PUBG Mobile Bilgileri</span>
                    </h2>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nickname -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>🎯</span>
                            <span>Oyuncu Adı</span>
                        </label>
                        <input type="text" name="nickname" value="{{ old('nickname', $user->profile->nickname) }}"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all group-hover:border-white/20">
                    </div>

                    <!-- PUBG ID -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>🆔</span>
                            <span>PUBG ID</span>
                        </label>
                        <input type="text" name="pubg_id" value="{{ old('pubg_id', $user->profile->pubg_id) }}"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all group-hover:border-white/20">
                    </div>

                    <!-- Rank -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>👑</span>
                            <span>Rütbe</span>
                        </label>
                        <select name="rank" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all group-hover:border-white/20 select-dark">
                            <option value="">Seçiniz</option>
                            <optgroup label="🥉 Bronz">
                                @foreach(['Bronze V', 'Bronze IV', 'Bronze III', 'Bronze II', 'Bronze I'] as $rank)
                                    <option value="{{ $rank }}" {{ old('rank', $user->profile->rank) == $rank ? 'selected' : '' }}>
                                        {{ config('pubg.ranks.' . $rank, $rank) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="🥈 Gümüş">
                                @foreach(['Silver V', 'Silver IV', 'Silver III', 'Silver II', 'Silver I'] as $rank)
                                    <option value="{{ $rank }}" {{ old('rank', $user->profile->rank) == $rank ? 'selected' : '' }}>
                                        {{ config('pubg.ranks.' . $rank, $rank) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="🥇 Altın">
                                @foreach(['Gold V', 'Gold IV', 'Gold III', 'Gold II', 'Gold I'] as $rank)
                                    <option value="{{ $rank }}" {{ old('rank', $user->profile->rank) == $rank ? 'selected' : '' }}>
                                        {{ config('pubg.ranks.' . $rank, $rank) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="💎 Platin">
                                @foreach(['Platinum V', 'Platinum IV', 'Platinum III', 'Platinum II', 'Platinum I'] as $rank)
                                    <option value="{{ $rank }}" {{ old('rank', $user->profile->rank) == $rank ? 'selected' : '' }}>
                                        {{ config('pubg.ranks.' . $rank, $rank) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="💠 Elmas">
                                @foreach(['Diamond V', 'Diamond IV', 'Diamond III', 'Diamond II', 'Diamond I'] as $rank)
                                    <option value="{{ $rank }}" {{ old('rank', $user->profile->rank) == $rank ? 'selected' : '' }}>
                                        {{ config('pubg.ranks.' . $rank, $rank) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="👑 Taç">
                                @foreach(['Crown V', 'Crown IV', 'Crown III', 'Crown II', 'Crown I'] as $rank)
                                    <option value="{{ $rank }}" {{ old('rank', $user->profile->rank) == $rank ? 'selected' : '' }}>
                                        {{ config('pubg.ranks.' . $rank, $rank) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="🔥 As">
                                @foreach(['Ace', 'Ace Master', 'Ace Dominator'] as $rank)
                                    <option value="{{ $rank }}" {{ old('rank', $user->profile->rank) == $rank ? 'selected' : '' }}>
                                        {{ config('pubg.ranks.' . $rank, $rank) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="⚡ Efsane">
                                <option value="Conqueror" {{ old('rank', $user->profile->rank) == 'Conqueror' ? 'selected' : '' }}>
                                    {{ config('pubg.ranks.Conqueror', 'Conqueror') }}
                                </option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Server Region -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>🌍</span>
                            <span>Sunucu Bölgesi</span>
                        </label>
                        <select name="server_region" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all group-hover:border-white/20 select-dark">
                            <option value="">Seçiniz</option>
                            @foreach(['EU', 'MENA', 'ASIA', 'NA', 'SA'] as $region)
                                <option value="{{ $region }}" {{ old('server_region', $user->profile->server_region) == $region ? 'selected' : '' }}>{{ $region }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>📍</span>
                            <span>Şehir</span>
                        </label>
                        <input type="text" name="city" value="{{ old('city', $user->profile->city) }}"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all group-hover:border-white/20">
                    </div>

                    <!-- Play Style -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>⚔️</span>
                            <span>Oyun Tarzı</span>
                        </label>
                        <select name="play_style" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all group-hover:border-white/20 select-dark">
                            <option value="">Seçiniz</option>
                            @foreach(['agresif', 'savunmaci', 'sniper', 'rusher', 'takimci'] as $style)
                                <option value="{{ $style }}" {{ old('play_style', $user->profile->play_style) == $style ? 'selected' : '' }}>{{ ucfirst($style) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Age Range -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>🎂</span>
                            <span>Yaş Aralığı</span>
                        </label>
                        <select name="age_range" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all group-hover:border-white/20 select-dark">
                            <option value="">Seçiniz</option>
                            @foreach(['13-17', '18-24', '25-30', '31-40', '40+'] as $range)
                                <option value="{{ $range }}" {{ old('age_range', $user->profile->age_range) == $range ? 'selected' : '' }}>{{ $range }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>👤</span>
                            <span>Cinsiyet</span>
                        </label>
                        <select name="gender" class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all group-hover:border-white/20 select-dark">
                            <option value="">Seçiniz</option>
                            @foreach(['male' => 'Erkek', 'female' => 'Kadın', 'other' => 'Diğer'] as $value => $label)
                                <option value="{{ $value }}" {{ old('gender', $user->profile->gender) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Favorite Maps -->
                    <div class="md:col-span-2 group">
                        <label class="block text-sm font-bold text-white mb-4 flex items-center space-x-2">
                            <span>🗺️</span>
                            <span>Favori Haritalar</span>
                            <span class="text-xs text-gray-400 font-normal ml-2">(Birden fazla seçebilirsin)</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $maps = [
                                    'Erangel' => ['emoji' => '🏞️', 'image' => 'erangel.jpg', 'gradient' => 'from-green-800 to-blue-900'],
                                    'Miramar' => ['emoji' => '🏜️', 'image' => 'miramar.jpg', 'gradient' => 'from-yellow-800 to-orange-900'],
                                    'Sanhok' => ['emoji' => '🌴', 'image' => 'sanhok.jpg', 'gradient' => 'from-green-700 to-emerald-900'],
                                    'Vikendi' => ['emoji' => '❄️', 'image' => 'vikendi.jpg', 'gradient' => 'from-blue-700 to-cyan-900'],
                                    'Livik' => ['emoji' => '🏝️', 'image' => 'livik.jpg', 'gradient' => 'from-teal-700 to-blue-900'],
                                    'Karakin' => ['emoji' => '🏔️', 'image' => 'karakin.jpg', 'gradient' => 'from-stone-700 to-gray-900'],
                                    'Nusa' => ['emoji' => '🌊', 'image' => 'nusa.jpg', 'gradient' => 'from-blue-600 to-indigo-900'],
                                ];
                                $selectedMaps = old('favorite_maps', $user->profile->favorite_maps ?? []);
                            @endphp
                            @foreach($maps as $map => $data)
                                @php
                                    $imagePath = '/images/maps/' . $data['image'];
                                    $imageExists = file_exists(public_path($imagePath));
                                @endphp
                                <label class="relative cursor-pointer group/map">
                                    <input type="checkbox" name="favorite_maps[]" value="{{ $map }}" 
                                           {{ in_array($map, $selectedMaps) ? 'checked' : '' }}
                                           class="peer sr-only">
                                    
                                    <div class="relative overflow-hidden rounded-2xl border-2 border-white/10 bg-white/5 hover:bg-white/10 peer-checked:border-orange-500 peer-checked:bg-orange-500/20 transition-all duration-300 hover:scale-105 peer-checked:scale-105 shadow-lg">
                                        <!-- Checkmark -->
                                        <div class="absolute top-2 right-2 w-8 h-8 bg-orange-500 rounded-full items-center justify-center hidden peer-checked:flex z-20 shadow-xl border-2 border-white">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        
                                        <!-- Map Card -->
                                        <div class="aspect-video relative">
                                            @if($imageExists)
                                                <!-- Gerçek Harita Resmi -->
                                                <img src="{{ asset($imagePath) }}" 
                                                     alt="{{ $map }}" 
                                                     class="w-full h-full object-cover transition-transform duration-300 group-hover/map:scale-110">
                                                <!-- Karartma Overlay -->
                                                <div class="absolute inset-0 bg-black/30 group-hover/map:bg-black/20 peer-checked:bg-black/10 transition-all"></div>
                                            @else
                                                <!-- Placeholder Gradient -->
                                                <div class="w-full h-full bg-gradient-to-br {{ $data['gradient'] }} flex items-center justify-center">
                                                    <span class="text-5xl group-hover/map:scale-125 transition-transform duration-300">{{ $data['emoji'] }}</span>
                                                </div>
                                            @endif
                                            
                                            <!-- Harita İsmi -->
                                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/70 to-transparent p-3">
                                                <div class="text-white font-bold text-center text-sm group-hover/map:text-orange-400 transition-colors">
                                                    {{ $map }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Selection Glow -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-orange-500/0 to-orange-500/0 peer-checked:from-orange-500/30 peer-checked:to-orange-500/10 transition-all pointer-events-none"></div>
                                        
                                        <!-- Hover Border Glow -->
                                        <div class="absolute inset-0 rounded-2xl opacity-0 group-hover/map:opacity-100 peer-checked:opacity-100 transition-opacity duration-300 pointer-events-none"
                                             style="box-shadow: inset 0 0 20px rgba(249, 115, 22, 0.4);"></div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-gray-400 text-xs mt-3 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Favori haritalarını seç, sana uygun takım arkadaşları bulsun!</span>
                        </p>
                    </div>

                    <!-- Bio -->
                    <div class="md:col-span-2 group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>💬</span>
                            <span>Hakkında</span>
                        </label>
                        <textarea name="bio" rows="4" placeholder="Kendini tanıt..."
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all group-hover:border-white/20 resize-none">{{ old('bio', $user->profile->bio) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Social Media Tab -->
            <div x-show="activeTab === 'social'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="bg-gradient-to-br from-gray-800/50 via-gray-900/50 to-black/50 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl overflow-hidden">
                
                <div class="bg-gradient-to-r from-blue-600/20 via-cyan-600/20 to-teal-600/20 px-8 py-6 border-b border-white/10">
                    <h2 class="text-3xl font-black text-white flex items-center space-x-3">
                        <span class="text-4xl">🔗</span>
                        <span>Sosyal Medya Hesapları</span>
                    </h2>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Twitch -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>📺</span>
                            <span>Twitch</span>
                        </label>
                        <input type="text" name="twitch_username" value="{{ old('twitch_username', $user->profile->twitch_username) }}" placeholder="kullaniciadi"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all group-hover:border-white/20">
                    </div>

                    <!-- YouTube -->
                    <div class="group">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>🎥</span>
                            <span>YouTube</span>
                        </label>
                        <input type="text" name="youtube_channel" value="{{ old('youtube_channel', $user->profile->youtube_channel) }}" placeholder="@kanaladi"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all group-hover:border-white/20">
                    </div>

                    <!-- Discord -->
                    <div class="group md:col-span-2">
                        <label class="block text-sm font-bold text-white mb-2 flex items-center space-x-2">
                            <span>💬</span>
                            <span>Discord</span>
                        </label>
                        <input type="text" name="discord_username" value="{{ old('discord_username', $user->profile->discord_username) }}" placeholder="kullaniciadi#1234"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all group-hover:border-white/20">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 animate-fade-in" x-show="activeTab !== 'avatar'">
                <button type="submit" 
                    class="flex-1 group relative overflow-hidden bg-gradient-to-r from-orange-600 via-red-600 to-pink-600 text-white px-8 py-5 rounded-2xl font-black text-xl shadow-2xl hover:shadow-orange-500/50 transition-all duration-300 hover:scale-105">
                    <span class="relative z-10 flex items-center justify-center space-x-3">
                        <span class="text-2xl">💾</span>
                        <span>Kaydet</span>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-pink-600 via-red-600 to-orange-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </button>
                
                <a href="{{ route('profile.statistics') }}" 
                    class="flex-1 group relative overflow-hidden bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-5 rounded-2xl font-black text-xl shadow-2xl hover:shadow-blue-500/50 transition-all duration-300 hover:scale-105 flex items-center justify-center space-x-3">
                    <span class="text-2xl">📊</span>
                    <span>İstatistikler</span>
                </a>
                
                <a href="{{ route('profile.index') }}" 
                    class="flex-1 group relative overflow-hidden bg-white/5 hover:bg-white/10 text-white px-8 py-5 rounded-2xl font-black text-xl border border-white/10 hover:border-white/20 transition-all duration-300 hover:scale-105 flex items-center justify-center space-x-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>İptal</span>
                </a>
            </div>
        </form>

        <!-- Avatar Formu (Ayrı) -->
        <div x-show="activeTab === 'avatar'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="bg-gradient-to-br from-gray-800/50 via-gray-900/50 to-black/50 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl overflow-hidden">
                
                <div class="bg-gradient-to-r from-purple-600/20 via-pink-600/20 to-blue-600/20 px-8 py-6 border-b border-white/10">
                    <h2 class="text-3xl font-black text-white flex items-center space-x-3">
                        <span class="text-4xl">📸</span>
                        <span>Profil Fotoğrafı</span>
                    </h2>
                </div>

                <div class="p-8">
                    <div class="flex flex-col items-center space-y-6">
                        <!-- Avatar Preview -->
                        <div class="relative group">
                            <div class="w-48 h-48 rounded-full bg-gradient-to-br from-purple-500 via-pink-500 to-blue-500 p-2 shadow-2xl shadow-purple-500/50">
                                <img id="avatar-preview" 
                                     src="{{ $user->profile->avatar_url ?? asset('images/default-avatar.svg') }}" 
                                     alt="Avatar" 
                                     class="w-full h-full rounded-full object-cover border-4 border-gray-900">
                            </div>
                            @if($user->profile->avatar_path)
                                <form action="{{ route('profile.avatar.delete') }}" method="POST" class="absolute -bottom-2 -right-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Profil fotoğrafını silmek istediğine emin misin?')"
                                            class="w-14 h-14 bg-red-500 hover:bg-red-600 rounded-full flex items-center justify-center shadow-lg transition-all transform hover:scale-110">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Upload Form -->
                        <div class="w-full max-w-md space-y-4">
                            <form action="{{ route('profile.avatar.upload') }}" method="POST" enctype="multipart/form-data" id="avatar-upload-form">
                                @csrf
                                <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden" onchange="previewAvatar(event)">
                                
                                <label for="avatar-input" 
                                       class="flex items-center justify-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 rounded-2xl cursor-pointer transition-all transform hover:scale-105 shadow-lg mb-4">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-bold text-lg">Fotoğraf Seç</span>
                                </label>

                                <button type="submit" id="upload-btn" disabled
                                        class="w-full px-8 py-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-600 disabled:cursor-not-allowed rounded-2xl font-bold text-lg transition-all transform hover:scale-105 shadow-lg disabled:transform-none">
                                    <span class="flex items-center justify-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <span>Yükle</span>
                                    </span>
                                </button>
                            </form>
                        </div>

                        <p class="text-gray-400 text-sm text-center">
                            📌 Max 2MB • JPG, PNG, GIF
                        </p>
                    </div>
                </div>
            </div>


    </div>
</div>

<script>
function previewAvatar(event) {
    console.log('📸 Avatar seçildi');
    const file = event.target.files[0];
    const uploadBtn = document.getElementById('upload-btn');
    
    if (!uploadBtn) {
        console.error('❌ Upload butonu bulunamadı!');
        return;
    }
    
    if (file) {
        console.log('📁 Dosya:', file.name, 'Boyut:', (file.size / 1024).toFixed(2) + 'KB');
        
        // Dosya boyutu kontrolü
        if (file.size > 2048 * 1024) {
            alert('❌ Dosya boyutu 2MB\'dan büyük olamaz!');
            event.target.value = '';
            uploadBtn.disabled = true;
            return;
        }

        // Dosya tipi kontrolü
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('❌ Sadece JPG, PNG ve GIF formatları desteklenir!');
            event.target.value = '';
            uploadBtn.disabled = true;
            return;
        }

        // Önizleme göster
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            if (preview) {
                preview.src = e.target.result;
                console.log('✅ Önizleme güncellendi');
            }
        };
        reader.readAsDataURL(file);
        
        // Upload butonunu aktif et
        uploadBtn.disabled = false;
        uploadBtn.classList.remove('bg-gray-600', 'disabled:bg-gray-600');
        uploadBtn.classList.add('bg-green-600');
        console.log('✅ Upload butonu aktif edildi');
    } else {
        uploadBtn.disabled = true;
        uploadBtn.classList.remove('bg-green-600');
        uploadBtn.classList.add('bg-gray-600');
        console.log('⚠️ Dosya seçilmedi');
    }
}

// Form submit kontrolü
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('avatar-upload-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('📤 Form gönderiliyor...');
            const fileInput = document.getElementById('avatar-input');
            if (!fileInput.files || !fileInput.files[0]) {
                e.preventDefault();
                alert('❌ Lütfen bir dosya seçin!');
                console.error('❌ Dosya seçilmemiş!');
                return false;
            }
            console.log('✅ Form gönderimi başarılı');
        });
    } else {
        console.error('❌ Avatar upload formu bulunamadı!');
    }
});
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scale-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes pulse-slow {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
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

.animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 10s ease-in-out infinite;
    animation-delay: 2s;
}

/* Dark Select Dropdown */
.select-dark option {
    background-color: #1f2937;
    color: white;
    padding: 8px;
}

.select-dark option:hover {
    background-color: #374151;
}

.select-dark option:checked {
    background-color: #f97316;
    color: white;
}
</style>

<!-- Footer -->
<footer class="relative z-10 mt-16 pt-12 pb-8 border-t border-white/10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
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
    </div>
</footer>

@endsection
