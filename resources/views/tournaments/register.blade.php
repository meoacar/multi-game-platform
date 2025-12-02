@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Turnuvaya Kayıt Ol</h1>
        <p class="text-gray-600 mb-6">{{ $tournament->name }}</p>

        <form action="{{ route('tournaments.register.store', $tournament->slug) }}" method="POST">
            @csrf

            <!-- Takım Adı -->
            <div class="mb-6">
                <label for="team_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Takım Adı *
                </label>
                <input type="text" name="team_name" id="team_name" required
                    class="w-full border-gray-300 rounded-lg @error('team_name') border-red-500 @enderror"
                    value="{{ old('team_name') }}">
                @error('team_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Takım Üyeleri -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Takım Üyeleri ({{ $tournament->team_size }} Kişi) *
                </label>
                <p class="text-sm text-gray-500 mb-3">Her üye için kullanıcı ID'sini girin</p>
                
                @for($i = 0; $i < $tournament->team_size; $i++)
                <div class="mb-3">
                    <input type="number" name="members[]" required
                        class="w-full border-gray-300 rounded-lg"
                        placeholder="Üye {{ $i + 1 }} - Kullanıcı ID"
                        value="{{ old('members.' . $i) }}">
                </div>
                @endfor
                
                @error('members')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-medium text-blue-900 mb-2">Önemli Bilgiler:</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Takım boyutu: {{ $tournament->team_size }} kişi</li>
                    <li>• Tüm üyelerin kayıtlı kullanıcı olması gerekir</li>
                    <li>• Kayıt sonrası değişiklik yapılamaz</li>
                    <li>• Turnuva kurallarını okuyup kabul ettiğinizden emin olun</li>
                </ul>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4">
                <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-medium">
                    Kayıt Ol
                </button>
                <a href="{{ route('tournaments.show', $tournament->slug) }}" class="flex-1 text-center bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 font-medium">
                    İptal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
