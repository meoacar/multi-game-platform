@extends('layouts.app')

@section('title', 'E-posta Doğrulama')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                E-posta Adresini Doğrula
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Devam etmeden önce e-posta adresini doğrulaman gerekiyor.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            Yeni bir doğrulama linki e-posta adresine gönderildi!
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
            <div class="flex items-center justify-center">
                <svg class="h-16 w-16 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>

            <p class="text-center text-gray-700">
                Kayıt sırasında verdiğin <strong>{{ auth()->user()->email }}</strong> adresine bir doğrulama linki gönderdik.
            </p>

            <p class="text-center text-sm text-gray-600">
                E-postayı almadıysan, spam klasörünü kontrol et veya yeni bir link gönderebiliriz.
            </p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Doğrulama Linkini Tekrar Gönder
                </button>
            </form>

            <div class="text-center pt-4 border-t">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                        Çıkış Yap
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
