@extends('layouts.app')

@section('title', 'Veritabanı Hatası')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900">
    <div class="max-w-md w-full px-6">
        <div class="text-center">
            <!-- Icon -->
            <div class="mb-6">
                <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>
            </div>

            <!-- Başlık -->
            <h1 class="text-4xl font-bold text-white mb-4">
                Veritabanı Hatası
            </h1>

            <!-- Mesaj -->
            <p class="text-gray-400 mb-8">
                @if(isset($message))
                    {{ $message }}
                @else
                    Bir veritabanı hatası oluştu. Lütfen daha sonra tekrar deneyin.
                @endif
            </p>

            <!-- Admin için detaylı bilgi -->
            @if(auth()->check() && auth()->user()->is_admin && isset($details))
                <div class="bg-gray-800 rounded-lg p-4 mb-6 text-left">
                    <p class="text-sm text-red-400 font-semibold mb-2">Admin Detayları:</p>
                    <div class="space-y-2">
                        @if(isset($details['error']))
                            <div>
                                <p class="text-xs text-gray-400">Hata:</p>
                                <p class="text-sm text-white font-mono break-all">{{ $details['error'] }}</p>
                            </div>
                        @endif
                        @if(isset($details['file']))
                            <div>
                                <p class="text-xs text-gray-400">Dosya:</p>
                                <p class="text-sm text-white font-mono">{{ $details['file'] }}:{{ $details['line'] ?? '?' }}</p>
                            </div>
                        @endif
                        @if(isset($details['game_id']))
                            <div>
                                <p class="text-xs text-gray-400">Game ID:</p>
                                <p class="text-sm text-white">{{ $details['game_id'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Butonlar -->
            <div class="space-y-3">
                <button onclick="window.history.back()" 
                        class="block w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    Geri Dön
                </button>

                <a href="{{ route('main.home') }}" 
                   class="block w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    Ana Sayfaya Git
                </a>
            </div>

            <!-- Yardım -->
            <div class="mt-8 bg-gray-800 rounded-lg p-4">
                <p class="text-sm text-gray-400">
                    Sorun devam ederse lütfen 
                    <a href="mailto:destek@takimsistemi.com" class="text-orange-500 hover:text-orange-400">
                        destek ekibiyle iletişime geçin
                    </a>.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
