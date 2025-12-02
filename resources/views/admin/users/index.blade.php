@extends('admin.layout')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="userManagement()">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                👥 Kullanıcı Yönetimi
            </h1>
            <p class="text-gray-600 mt-2">Tüm kullanıcıları yönet, filtrele ve düzenle</p>
        </div>
        
        <!-- Export Butonları -->
        <div class="flex gap-2">
            <a href="{{ route('admin.users.export.csv', request()->query()) }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                CSV İndir
            </a>
            <a href="{{ route('admin.users.export.excel', request()->query()) }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Excel İndir
            </a>
        </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <!-- Satır 1: Temel İstatistikler -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">📊 Toplam</p>
            <p class="text-2xl font-bold">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">✅ Aktif</p>
            <p class="text-2xl font-bold">{{ number_format($stats['active']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">🚫 Banlı</p>
            <p class="text-2xl font-bold">{{ number_format($stats['banned']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">❄️ Dondurulmuş</p>
            <p class="text-2xl font-bold">{{ number_format($stats['frozen'] ?? 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">🗑️ Silinmiş</p>
            <p class="text-2xl font-bold">{{ number_format($stats['deleted'] ?? 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
            <p class="text-xs opacity-90 mb-1">👑 Admin</p>
            <p class="text-2xl font-bold">{{ number_format($stats['admins']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <!-- Satır 2: Zaman Bazlı İstatistikler -->
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-orange-500">
            <p class="text-xs text-gray-600 mb-1">🌅 Bugün</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['today']) }}</p>
            @if(isset($stats['yesterday']))
                <p class="text-xs text-gray-500 mt-1">Dün: {{ number_format($stats['yesterday']) }}</p>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-pink-500">
            <p class="text-xs text-gray-600 mb-1">📅 Bu Hafta</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_week']) }}</p>
            @if(isset($stats['weekly_growth']))
                <p class="text-xs {{ $stats['weekly_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                    {{ $stats['weekly_growth'] >= 0 ? '↗' : '↘' }} {{ abs($stats['weekly_growth']) }}%
                </p>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-indigo-500">
            <p class="text-xs text-gray-600 mb-1">📆 Bu Ay</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_month']) }}</p>
            @if(isset($stats['monthly_growth']))
                <p class="text-xs {{ $stats['monthly_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                    {{ $stats['monthly_growth'] >= 0 ? '↗' : '↘' }} {{ abs($stats['monthly_growth']) }}%
                </p>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-teal-500">
            <p class="text-xs text-gray-600 mb-1">🔥 Bugün Aktif</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_today'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-cyan-500">
            <p class="text-xs text-gray-600 mb-1">✉️ Doğrulanmış</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['verified'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-amber-500">
            <p class="text-xs text-gray-600 mb-1">⭐ Ort. XP</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['avg_xp'] ?? 0) }}</p>
        </div>
    </div>

    <!-- Gelişmiş Filtre Paneli -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form method="GET" class="space-y-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-semibold text-gray-900">🔍 Gelişmiş Filtreler</h3>
                    <button type="button" @click="showAdvancedFilters = !showAdvancedFilters"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        <span x-text="showAdvancedFilters ? '▲ Gizle' : '▼ Göster'"></span>
                    </button>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-3 py-2 rounded-lg hover:opacity-80 transition-opacity">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button type="button" @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-3 py-2 rounded-lg hover:opacity-80 transition-opacity">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Temel Filtreler (Her Zaman Görünür) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Arama -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">🔎 Arama</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="İsim, email, PUBG ID ara...">
                </div>

                <!-- Durum -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📊 Durum</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Tümü</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Aktif</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>🚫 Banlı</option>
                        <option value="frozen" {{ request('status') == 'frozen' ? 'selected' : '' }}>❄️ Dondurulmuş</option>
                        <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>🗑️ Silinmiş</option>
                    </select>
                </div>

                <!-- Rol -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">👑 Rol</label>
                    <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Tümü</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>👑 Admin</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>👤 Kullanıcı</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>⭐ Süper Admin</option>
                        <option value="moderator" {{ request('role') == 'moderator' ? 'selected' : '' }}>🛡️ Moderatör</option>
                        <option value="content_manager" {{ request('role') == 'content_manager' ? 'selected' : '' }}>📝 İçerik Yöneticisi</option>
                    </select>
                </div>
            </div>

            <!-- Gelişmiş Filtreler (Açılır/Kapanır) -->
            <div x-show="showAdvancedFilters" x-collapse class="space-y-4 pt-4 border-t">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Email Doğrulama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">✉️ Email Doğrulama</label>
                        <select name="email_verified" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Tümü</option>
                            <option value="verified" {{ request('email_verified') == 'verified' ? 'selected' : '' }}>✅ Doğrulanmış</option>
                            <option value="unverified" {{ request('email_verified') == 'unverified' ? 'selected' : '' }}>❌ Doğrulanmamış</option>
                        </select>
                    </div>

                    <!-- Şehir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">🏙️ Şehir</label>
                        <select name="city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Tümü</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Oyun -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">🎮 Oyun</label>
                        <select name="game_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Tümü</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sayfa Başına -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">📄 Sayfa Başına</label>
                        <select name="per_page" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                        </select>
                    </div>
                </div>

                <!-- Tarih Aralıkları -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kayıt Tarihi -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">📅 Kayıt Başlangıç</label>
                            <input type="date" name="created_from" value="{{ request('created_from') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">📅 Kayıt Bitiş</label>
                            <input type="date" name="created_to" value="{{ request('created_to') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Son Giriş Tarihi -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">🕐 Son Giriş Başlangıç</label>
                            <input type="date" name="last_login_from" value="{{ request('last_login_from') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">🕐 Son Giriş Bitiş</label>
                            <input type="date" name="last_login_to" value="{{ request('last_login_to') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- XP Aralığı -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">⭐ Min XP</label>
                            <input type="number" name="xp_min" value="{{ request('xp_min') }}" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">⭐ Max XP</label>
                            <input type="number" name="xp_max" value="{{ request('xp_max') }}" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="999999">
                        </div>
                    </div>

                    <!-- Sıralama -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">📈 Sırala</label>
                            <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Kayıt Tarihi</option>
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>İsim</option>
                                <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="xp" {{ request('sort_by') == 'xp' ? 'selected' : '' }}>XP</option>
                                <option value="last_login" {{ request('sort_by') == 'last_login' ? 'selected' : '' }}>Son Giriş</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">↕️ Yön</label>
                            <select name="sort_order" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>↓ Azalan</option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>↑ Artan</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Butonlar -->
            <div class="flex gap-2 pt-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    🔍 Filtrele
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    🔄 Temizle
                </a>
                <div class="flex-1"></div>
                <span class="text-sm text-gray-600 self-center">
                    Toplam {{ $users->total() }} kullanıcı bulundu
                </span>
            </div>
        </form>
    </div>

    <!-- Toplu İşlem Paneli -->
    <div x-show="selectedUsers.length > 0" x-cloak
        class="bg-blue-50 border-2 border-blue-200 rounded-xl shadow-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-blue-900 font-semibold">
                    <span x-text="selectedUsers.length"></span> kullanıcı seçildi
                </span>
                <button type="button" @click="selectAll()" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Tümünü Seç
                </button>
                <button type="button" @click="deselectAll()" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Seçimi Temizle
                </button>
            </div>
            <div class="flex gap-2">
                <button type="button" @click="bulkAction('ban')" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                    🚫 Banla
                </button>
                <button type="button" @click="bulkAction('unban')" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                    ✅ Ban Kaldır
                </button>
                <button type="button" @click="bulkAction('add_xp')" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                    ⭐ XP Ekle
                </button>
                <button type="button" @click="bulkAction('send_email')" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    ✉️ Email Gönder
                </button>
                <button type="button" @click="bulkAction('delete')" 
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium">
                    🗑️ Sil
                </button>
            </div>
        </div>
    </div>

    <!-- Grid Görünümü -->
    <div x-show="view === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($users as $user)
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow overflow-hidden relative">
            <!-- Checkbox -->
            <div class="absolute top-4 left-4 z-10">
                <input type="checkbox" 
                    :checked="selectedUsers.includes({{ $user->id }})"
                    @change="toggleUser({{ $user->id }})"
                    class="w-5 h-5 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
            </div>
            
            <!-- Header -->
            <div class="bg-gradient-to-br from-blue-500 to-purple-600 p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-2xl font-bold">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="flex gap-2">
                        @if($user->is_admin)
                            <span class="px-2 py-1 bg-yellow-400 text-yellow-900 text-xs font-semibold rounded-full">👑 Admin</span>
                        @endif
                        @if($user->status === 'active')
                            <span class="px-2 py-1 bg-green-400 text-green-900 text-xs font-semibold rounded-full">✅</span>
                        @elseif($user->status === 'banned')
                            <span class="px-2 py-1 bg-red-400 text-red-900 text-xs font-semibold rounded-full">🚫</span>
                        @elseif($user->status === 'frozen')
                            <span class="px-2 py-1 bg-blue-400 text-blue-900 text-xs font-semibold rounded-full">❄️</span>
                        @endif
                    </div>
                </div>
                <h3 class="text-xl font-bold truncate">{{ $user->name }}</h3>
                <p class="text-sm opacity-90 truncate">{{ $user->email }}</p>
            </div>

            <!-- Body -->
            <div class="p-4 space-y-3">
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-blue-50 rounded-lg p-2 text-center">
                        <p class="text-xs text-gray-600">Level</p>
                        <p class="text-lg font-bold text-blue-600">{{ $user->level }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-2 text-center">
                        <p class="text-xs text-gray-600">XP</p>
                        <p class="text-lg font-bold text-purple-600">{{ number_format($user->xp_total) }}</p>
                    </div>
                </div>

                <!-- Profile Info -->
                @if($user->profile)
                <div class="text-sm space-y-1">
                    @if($user->profile->pubg_id)
                        <p class="text-gray-600">🎮 <span class="font-semibold">{{ $user->profile->pubg_id }}</span></p>
                    @endif
                    @if($user->profile->rank)
                        <p class="text-gray-600">🏆 {{ $user->profile->rank }}</p>
                    @endif
                </div>
                @endif

                <!-- Dates -->
                <div class="text-xs text-gray-500 space-y-1 pt-2 border-t">
                    <p>📅 Kayıt: {{ $user->created_at->format('d.m.Y') }}</p>
                    @if($user->last_login_at)
                        <p>🕐 Son Giriş: {{ $user->last_login_at->diffForHumans() }}</p>
                    @endif
                </div>

                <!-- Actions -->
                <a href="{{ route('admin.users.show', $user->id) }}" 
                    class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Detay Görüntüle →
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Liste Görünümü -->
    <div x-show="view === 'list'" class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left">
                        <input type="checkbox" 
                            @change="$event.target.checked ? selectAll() : deselectAll()"
                            class="w-5 h-5 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kullanıcı</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Level/XP</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kayıt</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition-colors" :class="selectedUsers.includes({{ $user->id }}) ? 'bg-blue-50' : ''">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" 
                            :checked="selectedUsers.includes({{ $user->id }})"
                            @change="toggleUser({{ $user->id }})"
                            class="w-5 h-5 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                                @if($user->profile && $user->profile->pubg_id)
                                    <p class="text-sm text-gray-500 truncate">🎮 {{ $user->profile->pubg_id }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <p class="text-sm text-gray-900">{{ $user->email }}</p>
                        @if($user->last_login_at)
                            <p class="text-xs text-gray-500">Son: {{ $user->last_login_at->diffForHumans() }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">
                                Lv {{ $user->level }}
                            </span>
                            <span class="text-sm text-gray-600">{{ number_format($user->xp_total) }} XP</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->status === 'active')
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                ✅ Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                                🚫 Banlı
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->is_admin)
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded-full">
                                👑 Admin
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
                                👤 Kullanıcı
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('d.m.Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        <a href="{{ route('admin.users.show', $user->id) }}" 
                            class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Detay →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->appends(request()->query())->links() }}
    </div>

    <!-- Toplu İşlem Modal'ları -->
    <!-- Ban Modal -->
    <div x-show="showBulkModal && bulkModalType === 'ban'" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="showBulkModal = false">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">🚫 Toplu Ban İşlemi</h3>
            <p class="text-gray-600 mb-4">
                <span x-text="selectedUsers.length"></span> kullanıcıyı banlamak istediğinizden emin misiniz?
            </p>
            <form @submit.prevent="submitBulkAction('ban')">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sebep (Opsiyonel)</label>
                    <textarea x-model="bulkActionReason" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Ban sebebini yazın..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Banla
                    </button>
                    <button type="button" @click="showBulkModal = false"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- XP Ekle Modal -->
    <div x-show="showBulkModal && bulkModalType === 'add_xp'" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="showBulkModal = false">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">⭐ Toplu XP Ekleme</h3>
            <p class="text-gray-600 mb-4">
                <span x-text="selectedUsers.length"></span> kullanıcıya XP ekleyeceksiniz.
            </p>
            <form @submit.prevent="submitBulkAction('add_xp')">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">XP Miktarı *</label>
                    <input type="number" x-model="bulkActionValue" min="1" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Örn: 1000">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sebep (Opsiyonel)</label>
                    <textarea x-model="bulkActionReason" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="XP ekleme sebebini yazın..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        XP Ekle
                    </button>
                    <button type="button" @click="showBulkModal = false"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Email Gönder Modal -->
    <div x-show="showBulkModal && bulkModalType === 'send_email'" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="showBulkModal = false">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-lg w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">✉️ Toplu Email Gönderimi</h3>
            <p class="text-gray-600 mb-4">
                <span x-text="selectedUsers.length"></span> kullanıcıya email göndereceksiniz.
            </p>
            <form @submit.prevent="submitBulkAction('send_email')">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konu *</label>
                    <input type="text" x-model="bulkEmailSubject" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Email konusu">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mesaj *</label>
                    <textarea x-model="bulkEmailMessage" rows="5" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Email mesajınızı yazın..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Gönder
                    </button>
                    <button type="button" @click="showBulkModal = false"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function userManagement() {
    return {
        view: 'list',
        selectedUsers: [],
        showAdvancedFilters: false,
        showBulkModal: false,
        bulkModalType: '',
        bulkActionReason: '',
        bulkActionValue: '',
        bulkEmailSubject: '',
        bulkEmailMessage: '',
        
        toggleUser(userId) {
            const index = this.selectedUsers.indexOf(userId);
            if (index > -1) {
                this.selectedUsers.splice(index, 1);
            } else {
                this.selectedUsers.push(userId);
            }
        },
        
        selectAll() {
            this.selectedUsers = @json($users->pluck('id')->toArray());
        },
        
        deselectAll() {
            this.selectedUsers = [];
        },
        
        bulkAction(action) {
            if (this.selectedUsers.length === 0) {
                alert('Lütfen en az bir kullanıcı seçin');
                return;
            }
            
            if (action === 'unban') {
                if (confirm(`${this.selectedUsers.length} kullanıcının banını kaldırmak istediğinizden emin misiniz?`)) {
                    this.submitBulkAction('unban');
                }
            } else if (action === 'delete') {
                if (confirm(`${this.selectedUsers.length} kullanıcıyı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!`)) {
                    this.submitBulkAction('delete');
                }
            } else {
                this.bulkModalType = action;
                this.showBulkModal = true;
                this.bulkActionReason = '';
                this.bulkActionValue = '';
                this.bulkEmailSubject = '';
                this.bulkEmailMessage = '';
            }
        },
        
        submitBulkAction(action) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.users.bulk-action") }}';
            
            // CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            // Action
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            
            // User IDs
            this.selectedUsers.forEach(userId => {
                const userInput = document.createElement('input');
                userInput.type = 'hidden';
                userInput.name = 'user_ids[]';
                userInput.value = userId;
                form.appendChild(userInput);
            });
            
            // Additional fields
            if (this.bulkActionReason) {
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = this.bulkActionReason;
                form.appendChild(reasonInput);
            }
            
            if (this.bulkActionValue) {
                const valueInput = document.createElement('input');
                valueInput.type = 'hidden';
                valueInput.name = 'value';
                valueInput.value = this.bulkActionValue;
                form.appendChild(valueInput);
            }
            
            if (this.bulkEmailSubject) {
                const subjectInput = document.createElement('input');
                subjectInput.type = 'hidden';
                subjectInput.name = 'email_subject';
                subjectInput.value = this.bulkEmailSubject;
                form.appendChild(subjectInput);
            }
            
            if (this.bulkEmailMessage) {
                const messageInput = document.createElement('input');
                messageInput.type = 'hidden';
                messageInput.name = 'email_message';
                messageInput.value = this.bulkEmailMessage;
                form.appendChild(messageInput);
            }
            
            document.body.appendChild(form);
            form.submit();
        }
    }
}
</script>
@endpush

@endsection
