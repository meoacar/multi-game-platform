@extends('layouts.app')

@section('title', 'Oyun Bulunamadı')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900">
    <div class="max-w-md w-full px-6">
        <div class="text-center">
            <!-- Icon -->
            <div class="mb-6">
                <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <!-- Başlık -->
            <h1 class="text-4xl font-bold text-white mb-4">
                Oyun Bulunamadı
            </h1>

            <!-- Mesaj -->
            <p class="text-gray-400 mb-8">
                @if(isset($message))
                    {{ $message }}
                @else
                    Aradığınız oyun bulunamadı veya şu anda aktif değil.
                @endif
            </p>

            <!-- Slug bilgisi (sadece admin için) -->
            @if(auth()->check() && auth()->user()->is_admin && isset($slug))
                <div class="bg-gray-800 rounded-lg p-4 mb-6 text-left">
                    <p class="text-sm text-gray-400 mb-1">Admin Bilgisi:</p>
                    <p class="text-sm text-white font-mono">Slug: {{ $slug }}</p>
                </div>
            @endif

            <!-- Butonlar -->
            <div class="space-y-3">
                <a href="{{ route('main.home') }}" 
                   class="block w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    Ana Sayfaya Dön
                </a>

                @auth
                    <a href="{{ route('home') }}" 
                       class="block w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                        Dashboard'a Git
                    </a>
                @endauth
            </div>

            <!-- Aktif Oyunlar -->
            @if(isset($activeGames) && $activeGames->count() > 0)
                <div class="mt-8">
                    <p class="text-gray-400 text-sm mb-4">Aktif Oyunlar:</p>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($activeGames as $game)
                            <a href="{{ url($game->slug . '.' . config('app.domain')) }}" 
                               class="bg-gray-800 hover:bg-gray-700 rounded-lg p-3 transition duration-200">
                                @if($game->logo)
                                    <img src="{{ asset($game->logo) }}" 
                                         alt="{{ $game->name }}" 
                                         class="h-8 w-8 mx-auto mb-2">
                                @endif
                                <p class="text-white text-sm font-medium">{{ $game->name }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
