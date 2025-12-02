@extends('admin.layout')

@section('title', 'Klan Başvuruları')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ view: 'grid' }">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-600 bg-clip-text text-transparent">
                    📋 Klan Başvuruları
                </h1>
                <p class="text-gray-400 mt-2">Tüm klan başvurularını görüntüle ve yönet</p>
            </div>
            <a href="{{ route('admin.clans.index') }}" 
                class="px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl hover:from-gray-700 hover:to-gray-800 transition-all shadow-lg">
                ← Klanlara Dön
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 shadow-xl">
                <p class="text-blue-100 text-sm mb-1">Toplam Başvuru</p>
                <p class="text-3xl font-bold text-white">{{ $applications->total() }}</p>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 shadow-xl">
                <p class="text-yellow-100 text-sm mb-1">Bekleyen</p>
                <p class="text-3xl font-bold text-white">{{ $applications->where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 shadow-xl">
                <p class="text-green-100 text-sm mb-1">Kabul Edilen</p>
                <p class="text-3xl font-bold text-white">{{ $applications->where('status', 'accepted')->count() }}</p>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 shadow-xl">
                <p class="text-red-100 text-sm mb-1">Reddedilen</p>
                <p class="text-3xl font-bold text-white">{{ $applications->where('status', 'rejected')->count() }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl p-6 mb-6 border border-gray-700">
            <form method="GET" action="{{ route('admin.clans.applications') }}" class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">🔍 Filtreler</h3>
                    <div class="flex gap-2">
                        <button type="button" @click="view = 'grid'" 
                            :class="view === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300'"
                            class="px-3 py-2 rounded-lg hover:opacity-80 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button type="button" @click="view = 'list'" 
                            :class="view === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300'"
                            class="px-3 py-2 rounded-lg hover:opacity-80 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">🔎 Arama</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Kullanıcı, klan..."
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Clan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">👑 Klan</label>
                        <select name="clan_id" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Tümü</option>
                            @foreach($clans as $clan)
                                <option value="{{ $clan->id }}" {{ request('clan_id') == $clan->id ? 'selected' : '' }}>
                                    {{ $clan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">📊 Durum</label>
                        <select name="status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Tümü</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Bekliyor</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>✅ Kabul Edildi</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Reddedildi</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
                        Filtrele
                    </button>
                    <a href="{{ route('admin.clans.applications') }}" class="px-6 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors">
                        Temizle
                    </a>
                </div>
            </form>
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($applications as $application)
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden border border-gray-700 hover:border-blue-500 transition-all hover:shadow-blue-500/20">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($application->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-white font-semibold">{{ $application->user->name }}</p>
                                <p class="text-blue-100 text-xs">{{ $application->user->profile->nickname ?? '-' }}</p>
                            </div>
                        </div>
                        @if($application->status === 'accepted')
                            <span class="px-2 py-1 bg-green-400 text-green-900 text-xs font-bold rounded-full">✅</span>
                        @elseif($application->status === 'rejected')
                            <span class="px-2 py-1 bg-red-400 text-red-900 text-xs font-bold rounded-full">❌</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full">⏳</span>
                        @endif
                    </div>
                </div>

                <!-- Body -->
                <div class="p-4 space-y-3">
                    <!-- Clan Info -->
                    <div class="bg-gray-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Başvurulan Klan</p>
                        <p class="text-white font-semibold">{{ $application->clan->name }}</p>
                        <p class="text-gray-400 text-xs">Lider: {{ $application->clan->leader->name }}</p>
                    </div>

                    <!-- User Rank -->
                    @if($application->user->profile && $application->user->profile->rank)
                    <div class="bg-gray-700/50 rounded-lg p-2 text-center">
                        <p class="text-xs text-gray-400">Rank</p>
                        <p class="text-purple-400 font-bold">{{ $application->user->profile->rank }}</p>
                    </div>
                    @endif

                    <!-- Message -->
                    @if($application->message)
                    <div class="bg-gray-700/50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Mesaj</p>
                        <p class="text-gray-300 text-sm line-clamp-3">{{ $application->message }}</p>
                    </div>
                    @endif

                    <!-- Date -->
                    <div class="text-xs text-gray-400 pt-2 border-t border-gray-700">
                        📅 {{ $application->created_at->format('d.m.Y H:i') }}
                    </div>

                    <!-- Status Update Form -->
                    <form action="{{ route('admin.clans.update-application-status', $application->id) }}" method="POST" class="space-y-2">
                        @csrf
                        <select name="status" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                            <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>⏳ Bekliyor</option>
                            <option value="accepted" {{ $application->status === 'accepted' ? 'selected' : '' }}>✅ Kabul Et</option>
                            <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>❌ Reddet</option>
                        </select>
                        <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all">
                            Güncelle
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-400 text-lg">Başvuru bulunamadı</p>
            </div>
            @endforelse
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Kullanıcı</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Klan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Mesaj</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($applications as $application)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                    {{ substr($application->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $application->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $application->user->profile->nickname ?? '-' }}</p>
                                    @if($application->user->profile && $application->user->profile->rank)
                                        <span class="text-xs px-2 py-1 bg-purple-600 text-purple-100 rounded-full">
                                            {{ $application->user->profile->rank }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-white">{{ $application->clan->name }}</div>
                            <div class="text-xs text-gray-400">Lider: {{ $application->clan->leader->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300 max-w-xs">
                            <p class="line-clamp-2">{{ $application->message ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($application->status === 'accepted')
                                <span class="px-3 py-1 bg-green-600 text-white rounded-full text-xs font-semibold">✅ Kabul</span>
                            @elseif($application->status === 'rejected')
                                <span class="px-3 py-1 bg-red-600 text-white rounded-full text-xs font-semibold">❌ Red</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-600 text-white rounded-full text-xs font-semibold">⏳ Bekliyor</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $application->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.clans.update-application-status', $application->id) }}" method="POST" class="flex gap-2">
                                @csrf
                                <select name="status" class="text-xs px-2 py-1 bg-gray-700 border border-gray-600 rounded text-white">
                                    <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Bekliyor</option>
                                    <option value="accepted" {{ $application->status === 'accepted' ? 'selected' : '' }}>Kabul</option>
                                    <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Red</option>
                                </select>
                                <button type="submit" class="text-xs px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                    Güncelle
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            Başvuru bulunamadı
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
        <div class="mt-6 bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-2xl px-6 py-4 border border-gray-700">
            {{ $applications->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
