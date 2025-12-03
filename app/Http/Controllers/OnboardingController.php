<?php

namespace App\Http\Controllers;

use App\Http\Requests\Onboarding\Step1Request;
use App\Http\Requests\Onboarding\Step2Request;
use App\Http\Requests\Onboarding\Step3Request;
use App\Http\Requests\Onboarding\Step4Request;
use App\Models\Game;
use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Onboarding Controller
 * 
 * Yeni kullanıcıların 4 adımlı profil tamamlama sürecini yönetir.
 */
class OnboardingController extends Controller
{
    /**
     * Onboarding Service
     */
    protected OnboardingService $service;

    /**
     * Constructor
     */
    public function __construct(OnboardingService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    /**
     * Onboarding ana sayfası
     * 
     * Kullanıcının mevcut adımını belirler ve ilgili view'e yönlendirir.
     * 
     * GET /onboarding
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Onboarding zaten tamamlanmışsa dashboard'a yönlendir
        if ($user->hasCompletedOnboarding()) {
            return redirect()->route('home')
                ->with('info', 'Onboarding sürecini zaten tamamladınız.');
        }

        // Mevcut adımı al
        $currentStep = $this->service->getCurrentStep($user);

        // Eğer oyun seçilmemişse, adım 0'a yönlendir (oyun seçimi)
        if (!$user->game_id && $currentStep > 0) {
            $currentStep = 0;
            $user->updateOnboardingStep(0);
        }

        // Eğer adım 0 ise, oyun seçim sayfasını göster
        if ($currentStep === 0) {
            $games = Game::active()->ordered()->get();
            return view('onboarding.step0', [
                'user' => $user,
                'games' => $games,
                'currentStep' => 0,
                'totalSteps' => OnboardingService::TOTAL_STEPS + 1, // +1 oyun seçimi için
            ]);
        }

        // İlgili adım view'ine yönlendir
        return view("onboarding.step{$currentStep}", [
            'user' => $user,
            'currentStep' => $currentStep,
            'totalSteps' => OnboardingService::TOTAL_STEPS + 1,
        ]);
    }

    /**
     * Adım 0: Oyun Seçimi
     * 
     * POST /onboarding/step0
     */
    public function step0(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Validasyon
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
        ], [
            'game_id.required' => 'Lütfen bir oyun seçin.',
            'game_id.exists' => 'Geçersiz oyun seçimi.',
        ]);

        // Seçilen oyunu bul
        $game = Game::findOrFail($validated['game_id']);

        // Oyunu kaydet
        $user->game_id = $game->id;
        $user->save();

        // Adım 1'e geç
        $user->updateOnboardingStep(1);

        // Seçilen oyunun subdomain'ine yönlendir
        $domain = config('app.domain', 'squadbul.com');
        $gameUrl = "https://{$game->slug}.{$domain}/onboarding";

        return redirect()->away($gameUrl)
            ->with('success', "{$game->name} oyunu seçildi! Profil bilgilerini tamamla.");
    }

    /**
     * Adım 1: PUBG Profil Bilgileri
     * 
     * POST /onboarding/step1
     */
    public function step1(Step1Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Verileri kaydet
        $success = $this->service->saveStep($user, 1, $validated);

        if (!$success) {
            return back()
                ->withInput()
                ->with('error', 'Bir hata oluştu. Lütfen tekrar deneyin.');
        }

        // Adım 2'ye yönlendir
        return redirect()->route('onboarding.index')
            ->with('success', 'PUBG profil bilgileriniz kaydedildi!');
    }

    /**
     * Adım 2: Oyun Tercihleri
     * 
     * POST /onboarding/step2
     */
    public function step2(Step2Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Verileri kaydet
        $success = $this->service->saveStep($user, 2, $validated);

        if (!$success) {
            return back()
                ->withInput()
                ->with('error', 'Bir hata oluştu. Lütfen tekrar deneyin.');
        }

        // Adım 3'e yönlendir
        return redirect()->route('onboarding.index')
            ->with('success', 'Oyun tercihleriniz kaydedildi!');
    }

    /**
     * Adım 3: İlgi Alanları
     * 
     * POST /onboarding/step3
     */
    public function step3(Step3Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Verileri kaydet
        $success = $this->service->saveStep($user, 3, $validated);

        if (!$success) {
            return back()
                ->withInput()
                ->with('error', 'Bir hata oluştu. Lütfen tekrar deneyin.');
        }

        // Adım 4'e yönlendir
        return redirect()->route('onboarding.index')
            ->with('success', 'İlgi alanlarınız kaydedildi!');
    }

    /**
     * Adım 4: Bildirim Tercihleri & Tamamlama
     * 
     * POST /onboarding/step4
     */
    public function step4(Step4Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Verileri kaydet
        $success = $this->service->saveStep($user, 4, $validated);

        if (!$success) {
            return back()
                ->withInput()
                ->with('error', 'Bir hata oluştu. Lütfen tekrar deneyin.');
        }

        // Onboarding'i tamamla
        $this->service->completeOnboarding($user, false);

        // XP kazandır (profil tamamlama bonusu)
        $user->increment('xp_total', 50);

        // "Topluluğa İlk Adım" rozetini ver
        $welcomeBadge = \App\Models\Badge::where('slug', 'first-step')->first();
        if ($welcomeBadge && !$user->badges()->where('badge_id', $welcomeBadge->id)->exists()) {
            $user->badges()->attach($welcomeBadge->id, [
                'unlocked_at' => now(),
                'progress' => 1,
                'progress_max' => 1,
            ]);
        }

        // Dashboard'a yönlendir
        return redirect()->route('home')
            ->with('success', '🎉 Tebrikler! Profil tamamlama sürecini başarıyla tamamladınız! +50 XP ve "Hoşgeldin Çaylağı" rozeti kazandınız!');
    }

    /**
     * Onboarding'i atla
     * 
     * POST /onboarding/skip
     */
    public function skip(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Onboarding'i atlanmış olarak tamamla
        $this->service->completeOnboarding($user, true);

        // Dashboard'a yönlendir
        return redirect()->route('home')
            ->with('info', 'Onboarding atlandı. Profil bilgilerinizi daha sonra tamamlayabilirsiniz.');
    }

    /**
     * Geri git (önceki adıma dön)
     * 
     * POST /onboarding/back
     */
    public function back(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Önceki adıma dön
        $this->service->moveToPreviousStep($user);

        return redirect()->route('onboarding.index');
    }
}
