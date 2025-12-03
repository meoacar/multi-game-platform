@extends('layouts.app')

@section('title', 'Oyun Seçimi Gerekli')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900">
    <div class="max-w-md w-full px-6">
        <div class="text-center">
            <!-- Icon -->
            <div class="mb-6">
                <svg class="mx-auto h-24 w-24 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Başlık -->
            <h1 class="text-4xl font-bold text-white mb-4">
                Oyun Seçimi Gerekli
            </h1>

            <!-- Mesaj -->
            <p class="text-gray-400 mb-8">
                @if(isset($message))
                    {{ $message }}
                @else
                    Bu işlemi gerçekleştirmek için önce bir oyun seçmeniz gerekiyor.
                @endif
            </p>

            <!-- Butonlar -->
            <div class="space-y-3">
                <a href="{{ route('main.home') }}" 
                   class="block w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    Oyun Seç
                </a>

                @if(session('last_game_slug'))
                    <a href="{{ url(session('last_game_slug') . '.' . config('app.domain')) }}" 
                       class="block w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                        Son Oyuna Dön
                    </a>
                @endif
            </div>

            <!-- Bilgi -->
            <div class="mt-8 bg-gray-800 rounded-lg p-4">
                <p class="text-sm text-gray-400">
                    <strong class="text-white">İpucu:</strong> 
                    Her oyun için ayrı bir subdomain kullanıyoruz. 
                    Örneğin: pubg.takimsistemi.com
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
