@extends('admin.layout')

@section('title', 'Profil Yönetimi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 -m-6 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 bg-clip-text text-transparent mb-2">
                        👤 PROFIL YÖNETİMİ
                    </h1>
                    <p class="text-gray-400 text-lg">Kullanıcı profillerini görüntüle ve düzenle</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl shadow-lg shadow-orange-500/50">
                        <span class="font-black text-2xl">{{ $profiles->total() }}</span>
                        <span class="text-sm ml-2 opacity-90">Profil</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-black/40 backdrop-blur-xl rounded-2xl shadow-2xl p-8 mb-8 border border-orange-500/20">
            <form method="GET" action="{{ route('admin.profiles.index') }}" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-bold text-orange-400 mb-3 uppercase tracking-wider">🔍 Ara</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="İsim, email veya nickname..."
                        class="w-full px-5 py-4 bg-gray-900/50 border-2 border-orange-500/30 text-white rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 placeholder-gray-500 transition-all">
                </div>

                <!-- Rank -->
                <div>
                    <label class="block text-sm font-bold text-orange-400 mb-3 uppercase tracking-wider">🏆 Rank</label>
                    <select name="rank" class="w-full px-5 py-4 bg-gray-900/50 border-2 border-orange-500/30 text-white rounded-xl focus:ring-2 focus:ring-orange-500">
                        <option value="">Tümü</option>
                        @foreach($ranks as $rank)
                            <option value="{{ $rank }}" {{ request('rank') == $rank ? 'selected' : '' }}>
                                {{ $rank }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-bold text-orange-400 mb-3 uppercase tracking-wider">📍 Şehir</label>
                    <select name="city" class="w-full px-5 py-4 bg-gray-900/50 border-2 border-orange-500/30 text-white rounded-xl focus:ring-2 focus:ring-orange-500">
                        <option value="">Tümü</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Server Region -->
                <div>
                    <label class="block text-sm font-bold text-orange-400 mb-3 uppercase tracking-wider">🌐 Sunucu Bölgesi</label>
                    <select name="server_region" class="w-full px-5 py-4 bg-gray-900/50 border-2 border-orange-500/30 text-white rounded-xl focus:ring-2 focus:ring-orange-500">
                        <option value="">Tümü</option>
                        @foreach($regions as $region)
                            <option value="{{ $region }}" {{ request('server_region') == $region ? 'selected' : '' }}>
                                {{ $region }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Play Style -->
                <div>
                    <label class="block text-sm font-bold text-orange-400 mb-3 uppercase tracking-wider">🎮 Oyun Stili</label>
                    <select name="play_style" class="w-full px-5 py-4 bg-gray-900/50 border-2 border-orange-500/30 text-white rounded-xl focus:ring-2 focus:ring-orange-500">
                        <option value="">Tümü</option>
                        @foreach($playStyles as $style)
                            <option value="{{ $style }}" {{ request('play_style') == $style ? 'selected' : '' }}>
                                {{ $style }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-8 py-4 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl hover:from-orange-700 hover:to-red-700 font-black uppercase tracking-wider shadow-lg shadow-orange-500/50 transform hover:scale-105 transition-all">
                    🔍 Filtrele
                </button>
                <a href="{{ route('admin.profiles.index') }}" class="px-8 py-4 bg-gray-800 text-gray-300 rounded-xl hover:bg-gray-700 font-bold uppercase tracking-wider border-2 border-gray-700 transition-all">
                    ✖️ Temizle
                </a>
            </div>
    </form>
</div>

    <!-- Profiles List -->
    <div class="bg-black/40 backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden border border-orange-500/20">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-orange-600/20 to-red-600/20 border-b-2 border-orange-500/30">
                <tr>
                    <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">👤 Kullanıcı</th>
                    <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">🎮 Nickname</th>
                    <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">🏆 Rank</th>
                    <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">📍 Şehir</th>
                    <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">🌐 Bölge</th>
                    <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">🎯 Oyun Stili</th>
                    <th class="px-6 py-5 text-left text-xs font-black text-orange-400 uppercase tracking-wider">⚙️ İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-orange-500/10">
                @forelse($profiles as $profile)
                <tr class="hover:bg-orange-500/5 transition-all duration-200">
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg shadow-orange-500/50">
                                {{ substr($profile->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-white">{{ $profile->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $profile->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <span class="text-sm font-bold text-orange-300">{{ $profile->nickname ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-5">
                        @if($profile->rank)
                            <span class="px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-black rounded-lg text-xs font-black shadow-lg shadow-yellow-500/50 uppercase">
                                🏆 {{ $profile->rank }}
                            </span>
                        @else
                            <span class="text-gray-600">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-5 text-sm font-semibold text-gray-300">
                        {{ $profile->city ?? '-' }}
                    </td>
                    <td class="px-6 py-5 text-sm font-semibold text-gray-300">
                        {{ $profile->server_region ?? '-' }}
                    </td>
                    <td class="px-6 py-5">
                        @if($profile->play_style)
                            <span class="px-4 py-2 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg text-xs font-bold shadow-lg shadow-orange-500/50 uppercase">
                                {{ $profile->play_style }}
                            </span>
                        @else
                            <span class="text-gray-600">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.show', $profile->user_id) }}" 
                                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg text-xs font-bold transition-all shadow-lg shadow-blue-500/50 uppercase">
                                👁️ Görüntüle
                            </a>
                            <a href="{{ route('admin.users.edit', $profile->user_id) }}" 
                                class="px-4 py-2 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-lg text-xs font-bold transition-all shadow-lg shadow-orange-500/50 uppercase">
                                ✏️ Düzenle
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center">
                        <div class="text-gray-500">
                            <div class="text-8xl mb-6 opacity-50">😔</div>
                            <p class="text-2xl font-black text-orange-400 mb-2">Profil Bulunamadı</p>
                            <p class="text-sm text-gray-400">Filtreleri değiştirmeyi deneyin</p>
                        </div>
                    </td>
                </tr>
                @endforelse
        </tbody>
    </table>

        <!-- Pagination -->
        @if($profiles->hasPages())
        <div class="px-6 py-5 border-t-2 border-orange-500/30 bg-black/60">
            {{ $profiles->links() }}
        </div>
        @endif
    </div>
    </div>
</div>
@endsection
