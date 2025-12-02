@extends('onboarding.layout')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">
            İlgi Alanların 🎯
        </h2>
        <p class="text-gray-300">
            Platformda nelerle ilgileniyorsun?
        </p>
    </div>

    <form action="{{ route('onboarding.step3') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-200 mb-3">
                İlgi Alanlarını Seç <span class="text-red-400">*</span>
                <span class="text-xs text-gray-400 font-normal">(En az 1 tane seç)</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $interests = [
                        'lfg' => ['icon' => '👥', 'title' => 'Takım Arkadaşı Bul', 'desc' => 'LFG ilanları oluştur ve başvur'],
                        'clan' => ['icon' => '🛡️', 'title' => 'Klan', 'desc' => 'Klan kur veya klanlara katıl'],
                        'tournament' => ['icon' => '🏆', 'title' => 'Turnuva', 'desc' => 'Turnuvalara katıl ve yarış'],
                        'social' => ['icon' => '💬', 'title' => 'Sosyal', 'desc' => 'Toplulukla etkileşim kur']
                    ];
                    $oldInterests = old('interests', $user->interests ?? []);
                @endphp
                @foreach($interests as $key => $data)
                    <label class="relative cursor-pointer touch-feedback">
                        <input type="checkbox" name="interests[]" value="{{ $key }}" 
                               {{ in_array($key, $oldInterests) ? 'checked' : '' }}
                               class="peer sr-only">
                        <div class="btn-touch p-5 bg-gray-700/50 border-2 border-gray-600 rounded-xl transition-smooth peer-checked:border-game-accent peer-checked:bg-game-accent/20 hover:border-gray-500 hover:bg-gray-700/70">
                            <div class="flex items-start">
                                <div class="text-4xl mr-4">{{ $data['icon'] }}</div>
                                <div class="flex-1">
                                    <div class="font-bold text-white text-lg mb-1">{{ $data['title'] }}</div>
                                    <div class="text-sm text-gray-300">{{ $data['desc'] }}</div>
                                </div>
                                <div class="ml-2">
                                    <div class="w-6 h-6 rounded-full border-2 border-gray-500 peer-checked:border-game-accent peer-checked:bg-game-accent flex items-center justify-center transition-smooth">
                                        <svg class="w-4 h-4 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('interests')
                <p class="mt-1 text-sm text-game-danger flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button type="submit" 
                class="btn-pubg-primary btn-touch-lg btn-mobile w-full flex items-center justify-center group touch-feedback">
            <span>Devam Et</span>
            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </form>
</div>
@endsection
