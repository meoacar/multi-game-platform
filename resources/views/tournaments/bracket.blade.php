@extends('layouts.app')

@section('title', $tournament->name . ' - Eşleşme Ağacı')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Başlık -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $tournament->name }}</h1>
                <p class="text-gray-600 mt-1">Eşleşme Ağacı</p>
            </div>
            <a href="{{ route('tournaments.show', $tournament->slug) }}" 
               class="text-blue-600 hover:text-blue-700">
                ← Turnuvaya Dön
            </a>
        </div>
    </div>

    @if($tournament->status === 'upcoming' || $tournament->status === 'registration_open')
        <!-- Henüz başlamadı -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <div class="text-4xl mb-4">⏳</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Turnuva Henüz Başlamadı</h3>
            <p class="text-gray-600">
                Eşleşme ağacı turnuva başladığında oluşturulacak.
            </p>
            <p class="text-sm text-gray-500 mt-2">
                Başlangıç: {{ $tournament->tournament_starts_at->format('d.m.Y H:i') }}
            </p>
        </div>
    @elseif(!$tournament->bracket_data || count($tournament->bracket_data) === 0)
        <!-- Bracket oluşturulmamış -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
            <div class="text-4xl mb-4">🎮</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Eşleşmeler Hazırlanıyor</h3>
            <p class="text-gray-600">
                Turnuva organizatörü eşleşmeleri yakında oluşturacak.
            </p>
            
            @if(auth()->check() && auth()->id() === $tournament->organizer_id)
                <form action="{{ route('tournaments.generate-bracket', $tournament->slug) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                        Eşleşmeleri Oluştur
                    </button>
                </form>
            @endif
        </div>
    @else
        <!-- Bracket gösterimi -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="overflow-x-auto">
                <div class="inline-flex space-x-8 pb-4">
                    @foreach($tournament->bracket_data as $roundIndex => $round)
                        <div class="flex flex-col space-y-4 min-w-[250px]">
                            <!-- Round başlığı -->
                            <div class="text-center mb-4">
                                <h3 class="font-bold text-lg text-gray-900">
                                    @if($roundIndex === count($tournament->bracket_data) - 1)
                                        🏆 Final
                                    @elseif($roundIndex === count($tournament->bracket_data) - 2)
                                        Yarı Final
                                    @else
                                        {{ $roundIndex + 1 }}. Tur
                                    @endif
                                </h3>
                            </div>

                            <!-- Maçlar -->
                            @foreach($round as $matchIndex => $match)
                                <div class="bg-gray-50 rounded-lg border-2 {{ isset($match['winner']) ? 'border-green-500' : 'border-gray-300' }} p-4">
                                    <div class="text-xs text-gray-500 mb-2">Maç #{{ $matchIndex + 1 }}</div>
                                    
                                    <!-- Takım 1 -->
                                    <div class="flex items-center justify-between mb-2 p-2 rounded {{ isset($match['winner']) && $match['winner'] === $match['team1_id'] ? 'bg-green-100' : 'bg-white' }}">
                                        <span class="font-semibold {{ isset($match['winner']) && $match['winner'] === $match['team1_id'] ? 'text-green-700' : '' }}">
                                            {{ $match['team1_name'] ?? 'TBD' }}
                                        </span>
                                        @if(isset($match['team1_score']))
                                            <span class="font-bold text-lg">{{ $match['team1_score'] }}</span>
                                        @endif
                                    </div>

                                    <div class="text-center text-xs text-gray-400 my-1">VS</div>

                                    <!-- Takım 2 -->
                                    <div class="flex items-center justify-between p-2 rounded {{ isset($match['winner']) && $match['winner'] === $match['team2_id'] ? 'bg-green-100' : 'bg-white' }}">
                                        <span class="font-semibold {{ isset($match['winner']) && $match['winner'] === $match['team2_id'] ? 'text-green-700' : '' }}">
                                            {{ $match['team2_name'] ?? 'TBD' }}
                                        </span>
                                        @if(isset($match['team2_score']))
                                            <span class="font-bold text-lg">{{ $match['team2_score'] }}</span>
                                        @endif
                                    </div>

                                    <!-- Maç durumu -->
                                    @if(isset($match['status']))
                                        <div class="mt-2 text-center">
                                            @if($match['status'] === 'completed')
                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                                    ✓ Tamamlandı
                                                </span>
                                            @elseif($match['status'] === 'in_progress')
                                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                                    ⚡ Devam Ediyor
                                                </span>
                                            @else
                                                <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full">
                                                    ⏳ Bekliyor
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Organizatör için skor güncelleme -->
                                    @if(auth()->check() && auth()->id() === $tournament->organizer_id && (!isset($match['status']) || $match['status'] !== 'completed'))
                                        <form action="{{ route('tournaments.update-match', [$tournament->slug, $roundIndex, $matchIndex]) }}" 
                                              method="POST" 
                                              class="mt-3 pt-3 border-t">
                                            @csrf
                                            <div class="grid grid-cols-2 gap-2 mb-2">
                                                <input type="number" 
                                                       name="team1_score" 
                                                       placeholder="Skor 1"
                                                       value="{{ $match['team1_score'] ?? '' }}"
                                                       class="px-2 py-1 border rounded text-sm">
                                                <input type="number" 
                                                       name="team2_score" 
                                                       placeholder="Skor 2"
                                                       value="{{ $match['team2_score'] ?? '' }}"
                                                       class="px-2 py-1 border rounded text-sm">
                                            </div>
                                            <button type="submit" 
                                                    class="w-full bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                                Güncelle
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Şampiyon -->
        @if($tournament->status === 'completed' && isset($tournament->bracket_data[count($tournament->bracket_data) - 1][0]['winner']))
            @php
                $finalMatch = $tournament->bracket_data[count($tournament->bracket_data) - 1][0];
                $championName = $finalMatch['winner'] === $finalMatch['team1_id'] 
                    ? $finalMatch['team1_name'] 
                    : $finalMatch['team2_name'];
            @endphp
            <div class="mt-8 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-lg p-8 text-center">
                <div class="text-6xl mb-4">🏆</div>
                <h2 class="text-3xl font-bold text-white mb-2">Şampiyon</h2>
                <p class="text-2xl font-bold text-white">{{ $championName }}</p>
            </div>
        @endif
    @endif
</div>
@endsection
