@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-cover bg-center bg-fixed" style="background-image: url('{{ asset('arkaplan/14.jpg') }}');">
    <div class="min-h-screen bg-black/60 backdrop-blur-sm">
        <div class="py-12" x-data="{ filterOpen: false }">
            <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-red-600 to-orange-600 rounded-2xl mb-6 shadow-lg shadow-red-500/50">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-red-400 to-orange-400 bg-clip-text text-transparent mb-3">
                Turnuvalar
            </h1>
            <p class="text-gray-300 text-lg mb-6">Topluluk turnuvalarına katıl ve ödüller kazan! 🏆</p>
            
            @auth
            <a href="{{ route('tournaments.create') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white rounded-xl font-bold text-lg shadow-lg shadow-red-500/50 transition transform hover:-translate-y-0.5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Turnuva Oluştur
            </a>
            @endauth
        </div>

        <!-- Filters -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-red-500/10 p-6 border-2 border-red-500/20 mb-8">
            <form method="GET" action="{{ route('tournaments.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Durum -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-200 mb-2">
                            📊 Durum
                        </label>
                        <select name="status" class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-500/20 transition">
                            <option value="">Tüm Turnuvalar</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>🔜 Yaklaşan</option>
                            <option value="registration_open" {{ request('status') == 'registration_open' ? 'selected' : '' }}>✅ Kayıt Açık</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>⚡ Devam Eden</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>🏁 Tamamlanan</option>
                        </select>
                    </div>

                    <!-- Oyun -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-200 mb-2">
                            🎮 Oyun
                        </label>
                        <select name="game_id" class="w-full px-4 py-3 bg-gray-700/50 border-2 border-gray-600 text-white rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-500/20 transition">
                            <option value="">Tüm Oyunlar</option>
                            @foreach($games as $game)
                            <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>
                                {{ $game->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtrele Butonu -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-red-500/30 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filtrele
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tournaments Grid -->
        @if($tournaments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($tournaments as $tournament)
            @php
                $statusColors = [
                    'upcoming' => 'from-blue-600 to-cyan-600',
                    'registration_open' => 'from-green-600 to-emerald-600',
                    'in_progress' => 'from-yellow-600 to-orange-600',
                    'completed' => 'from-gray-600 to-gray-700',
                ];
                $statusBadgeColors = [
                    'upcoming' => 'bg-blue-600/30 text-blue-300 border-blue-500/50',
                    'registration_open' => 'bg-green-600/30 text-green-300 border-green-500/50',
                    'in_progress' => 'bg-yellow-600/30 text-yellow-300 border-yellow-500/50',
                    'completed' => 'bg-gray-600/30 text-gray-300 border-gray-500/50',
                ];
                $statusLabels = [
                    'upcoming' => '🔜 Yaklaşan',
                    'registration_open' => '✅ Kayıt Açık',
                    'in_progress' => '⚡ Devam Ediyor',
                    'completed' => '🏁 Tamamlandı',
                ];
                $statusGradient = $statusColors[$tournament->status] ?? 'from-gray-600 to-gray-700';
            @endphp
            
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-red-500/10 border-2 border-gray-700 hover:border-red-500/50 transition-all duration-300 overflow-hidden group">
                <!-- Header -->
                <div class="relative h-32 bg-gradient-to-br {{ $statusGradient }} p-6">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="relative">
                        <!-- Status Badge -->
                        <div class="flex justify-between items-start mb-3">
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full {{ $statusBadgeColors[$tournament->status] ?? 'bg-gray-600/30 text-gray-300 border-gray-500/50' }} border font-bold">
                                {{ $statusLabels[$tournament->status] ?? $tournament->status }}
                            </span>
                            @if($tournament->status === 'registration_open')
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-500 text-white text-xs rounded-full font-bold animate-pulse">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                AÇIK
                            </span>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1 line-clamp-2">{{ $tournament->name }}</h3>
                        <p class="text-sm text-white/80 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $tournament->game->name }}
                        </p>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Prize Pool -->
                    @if($tournament->prize_pool)
                    <div class="mb-4 p-4 bg-gradient-to-r from-yellow-600/20 to-orange-600/20 border-2 border-yellow-500/30 rounded-xl text-center">
                        <p class="text-xs text-yellow-300 mb-1 font-semibold">💰 Ödül Havuzu</p>
                        <p class="text-2xl font-bold text-yellow-400">{{ $tournament->prize_pool }}</p>
                    </div>
                    @endif

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="text-center p-4 bg-gray-700/50 border border-gray-600 rounded-xl">
                            <div class="text-3xl font-bold text-white mb-1">{{ $tournament->teams->count() }}</div>
                            <div class="text-xs text-gray-400 font-semibold">Kayıtlı Takım</div>
                        </div>
                        <div class="text-center p-4 bg-gray-700/50 border border-gray-600 rounded-xl">
                            <div class="text-3xl font-bold text-white mb-1">{{ $tournament->max_teams }}</div>
                            <div class="text-xs text-gray-400 font-semibold">Maksimum</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @php
                        $progress = ($tournament->teams->count() / $tournament->max_teams) * 100;
                    @endphp
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                            <span>Doluluk Oranı</span>
                            <span class="font-bold">{{ number_format($progress, 0) }}%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm bg-gray-700/30 px-3 py-2 rounded-lg">
                            <span class="text-gray-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Kayıt:
                            </span>
                            <span class="font-bold text-white">{{ $tournament->registration_starts_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm bg-gray-700/30 px-3 py-2 rounded-lg">
                            <span class="text-gray-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Başlangıç:
                            </span>
                            <span class="font-bold text-white">{{ $tournament->tournament_starts_at->format('d.m.Y') }}</span>
                        </div>
                    </div>

                    <!-- Organizer -->
                    <div class="flex items-center gap-2 mb-4 p-3 bg-gray-700/30 rounded-lg">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr($tournament->organizer->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-400">Organizatör</p>
                            <p class="text-sm font-bold text-white">{{ $tournament->organizer->name }}</p>
                        </div>
                    </div>

                    <!-- Action -->
                    <a href="{{ route('tournaments.show', $tournament->slug) }}" class="block w-full text-center bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-red-500/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Detayları Gör
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $tournaments->links() }}
        </div>

        @else
        <!-- Empty State -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl shadow-lg shadow-red-500/10 p-12 border-2 border-gray-700 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-700/50 rounded-2xl mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz turnuva yok</h3>
            <p class="text-gray-400 mb-6">İlk turnuvayı sen düzenle ve toplulukta iz bırak!</p>
            @auth
            <a href="{{ route('tournaments.create') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white rounded-xl font-bold shadow-lg shadow-red-500/50 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Turnuva Oluştur
            </a>
            @endauth
        </div>
        @endif
            </div>
        </div>
    </div>
</div>
@endsection
