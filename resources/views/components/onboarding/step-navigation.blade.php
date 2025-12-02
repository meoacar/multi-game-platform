@props(['current', 'total'])

<div class="mt-6 flex flex-col sm:flex-row gap-3">
    <!-- Geri Butonu (Adım 1'de gizli) -->
    @if($current > 1)
        <form action="{{ route('onboarding.back') }}" method="POST" class="flex-1">
            @csrf
            <button type="submit" 
                    class="btn-touch btn-mobile w-full px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-xl transition-smooth flex items-center justify-center group touch-feedback">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Geri
            </button>
        </form>
    @endif

    <!-- Atla Butonu -->
    <form action="{{ route('onboarding.skip') }}" method="POST" class="flex-1">
        @csrf
        <button type="submit" 
                onclick="return confirm('Onboarding sürecini atlamak istediğinize emin misiniz? Profil bilgilerinizi daha sonra tamamlayabilirsiniz.')"
                class="btn-touch btn-mobile w-full px-6 py-3 bg-gray-700/50 hover:bg-gray-700 text-gray-300 hover:text-white font-semibold rounded-xl transition-smooth border-2 border-gray-600 hover:border-gray-500 touch-feedback">
            Şimdilik Atla
        </button>
    </form>
</div>

<!-- Yardım Metni -->
<div class="mt-4 text-center text-sm text-gray-400">
    <p>
        @if($current < $total)
            Devam etmek için formu doldurun
        @else
            Son adım! Tamamlamak için formu doldurun
        @endif
    </p>
</div>
