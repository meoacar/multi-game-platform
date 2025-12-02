@extends('onboarding.layout')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">
            Son Adım! 🎉
        </h2>
        <p class="text-gray-300">
            Bildirim tercihlerini ayarla ve tamamla
        </p>
    </div>

    <form action="{{ route('onboarding.step4') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Push Notifications -->
        <div class="p-5 bg-gray-700/30 rounded-xl border-2 border-gray-600 transition-smooth hover:border-gray-500">
            <label class="flex items-start cursor-pointer touch-feedback">
                <input type="checkbox" name="push_enabled" value="1" 
                       {{ old('push_enabled', $user->push_enabled) ? 'checked' : '' }}
                       class="checkbox-pubg mt-1">
                <div class="ml-4 flex-1">
                    <div class="flex items-center mb-1">
                        <span class="text-2xl mr-2">🔔</span>
                        <span class="font-semibold text-white">Push Bildirimleri</span>
                    </div>
                    <p class="text-sm text-gray-300">
                        Yeni mesajlar, başvurular ve önemli güncellemeler için bildirim al
                    </p>
                </div>
            </label>
        </div>

        <!-- Email Notifications -->
        <div class="p-5 bg-gray-700/30 rounded-xl border-2 border-gray-600 transition-smooth hover:border-gray-500">
            <label class="flex items-start cursor-pointer touch-feedback">
                <input type="checkbox" name="email_notifications" value="1" 
                       {{ old('email_notifications') ? 'checked' : '' }}
                       class="checkbox-pubg mt-1">
                <div class="ml-4 flex-1">
                    <div class="flex items-center mb-1">
                        <span class="text-2xl mr-2">📧</span>
                        <span class="font-semibold text-white">Email Bildirimleri</span>
                    </div>
                    <p class="text-sm text-gray-300">
                        Haftalık özet ve önemli duyurular için email al
                    </p>
                </div>
            </label>
        </div>

        <!-- Completion Badge -->
        <x-onboarding.completion-badge />

        <!-- Submit Button -->
        <button type="submit" 
                class="btn-pubg-success btn-touch-lg btn-mobile w-full flex items-center justify-center group touch-feedback">
            <span>🎉 Tamamla ve Başla!</span>
            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <p class="text-center text-sm text-gray-400">
            Tamamladığında <span class="text-game-success font-semibold">+50 XP</span> kazanacaksın!
        </p>
    </form>
</div>
@endsection
