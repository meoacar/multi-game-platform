@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-500 to-orange-600 rounded-lg p-8 text-white mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-4xl font-bold mb-2">{{ $tournament->name }}</h1>
                <p class="text-lg opacity-90">{{ $tournament->game->name }}</p>
            </div>
            @php
                $statusColors = [
                    'upcoming' => 'bg-blue-100 text-blue-800',
                    'registration_open' => 'bg-green-100 text-green-800',
                    'in_progress' => 'bg-yellow-100 text-yellow-800',
                    'completed' => 'bg-gray-100 text-gray-800',
                ];
                $statusLabels = [
                    'upcoming' => 'Yaklaşan',
                    'registration_open' => 'Kayıt Açık',
                    'in_progress' => 'Devam Ediyor',
                    'completed' => 'Tamamlandı',
                ];
            @endphp
            <span class="px-4 py-2 rounded-full {{ $statusColors[$tournament->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusLabels[$tournament->status] ?? $tournament->status }}
            </span>
        </div>
        
        <!-- Bracket Linki -->
        @if($tournament->status === 'in_progress' || $tournament->status === 'completed')
            <div class="mt-4">
                <a href="{{ route('tournaments.bracket', $tournament->slug) }}" 
                   class="inline-flex items-center px-6 py-3 bg-white text-red-600 rounded-lg hover:bg-gray-100 font-semibold">
                    🏆 Eşleşme Ağacını Görüntüle
                </a>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Turnuva Hakkında</h2>
                <p class="text-gray-600 whitespace-pre-line">{{ $tournament->description }}</p>
            </div>

            <!-- Rules -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Kurallar</h2>
                <p class="text-gray-600 whitespace-pre-line">{{ $tournament->rules }}</p>
            </div>

            <!-- Teams -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    Kayıtlı Takımlar ({{ $tournament->teams->count() }}/{{ $tournament->max_teams }})
                </h2>
                @if($tournament->teams->count() > 0)
                <div class="space-y-3">
                    @foreach($tournament->teams as $team)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $team->name }}</p>
                            <p class="text-sm text-gray-600">Kaptan: {{ $team->captain->name }}</p>
                            @if($team->placement)
                            <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800 mt-1">
                                {{ $team->placement }}. Sıra
                            </span>
                            @endif
                        </div>
                        <span class="px-3 py-1 text-xs rounded-full {{ $team->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $team->status === 'confirmed' ? 'Onaylandı' : 'Kayıtlı' }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-8">Henüz kayıtlı takım yok</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Prize Pool -->
            @if($tournament->prize_pool)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Ödül Havuzu</h3>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-3xl font-bold text-yellow-700">{{ $tournament->prize_pool }}</p>
                </div>
            </div>
            @endif

            <!-- Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Bilgiler</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Takım Sayısı</span>
                        <span class="font-bold text-gray-900">{{ $tournament->teams->count() }}/{{ $tournament->max_teams }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Takım Boyutu</span>
                        <span class="font-bold text-gray-900">{{ $tournament->team_size }} Kişi</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kayıt Başlangıç</span>
                        <span class="font-bold text-gray-900">{{ $tournament->registration_starts_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kayıt Bitiş</span>
                        <span class="font-bold text-gray-900">{{ $tournament->registration_ends_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Turnuva Başlangıç</span>
                        <span class="font-bold text-gray-900">{{ $tournament->tournament_starts_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Turnuva Bitiş</span>
                        <span class="font-bold text-gray-900">{{ $tournament->tournament_ends_at->format('d.m.Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Organizer -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Organizatör</h3>
                <div class="flex items-center space-x-3">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($tournament->organizer->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $tournament->organizer->name }}</p>
                        <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-800 mt-1">
                            Level {{ $tournament->organizer->getLevel() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Register Button -->
            @auth
            @if($tournament->status === 'registration_open' && $tournament->teams->count() < $tournament->max_teams)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <a href="{{ route('tournaments.register', $tournament->slug) }}" class="block w-full text-center bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 font-medium">
                    Turnuvaya Kayıt Ol
                </a>
            </div>
            @endif
            @endauth
        </div>
    </div>
</div>
@endsection
