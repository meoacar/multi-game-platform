<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check Onboarding Middleware
 * 
 * Onboarding tamamlanmamış kullanıcıları onboarding sayfasına yönlendirir.
 * Tamamlamış kullanıcıların istenen sayfaya erişmesine izin verir.
 */
class CheckOnboarding
{
    /**
     * Onboarding kontrolünden muaf tutulacak route'lar
     * 
     * @var array
     */
    protected $except = [
        'onboarding.*',
        'logout',
        'login',
        'register',
    ];

    /**
     * İsteği işle
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kullanıcı giriş yapmamışsa middleware'i atla
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Muaf route kontrolü
        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }

        // Ana domain'de (oyun seçilmemiş) onboarding kontrolü yapma
        if (!session('game_id')) {
            return $next($request);
        }

        // Mevcut oyun için profil var mı kontrol et
        $profile = $user->profileForGame(session('game_id'))->first();

        if (!$profile) {
            // Bu oyun için profil yok → Onboarding'e yönlendir
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu oyun için profilinizi oluşturmanız gerekiyor.',
                    'redirect' => route('onboarding.start'),
                ], 403);
            }

            return redirect()->route('onboarding.start')
                ->with('info', session('game')->name . ' için profilinizi oluşturalım!');
        }

        // Profil var ama onboarding tamamlanmamış
        if (!$profile->onboarding_completed) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lütfen profil tamamlama sürecini tamamlayın.',
                    'redirect' => route('onboarding.step', $profile->onboarding_step),
                ], 403);
            }

            return redirect()->route('onboarding.step', $profile->onboarding_step)
                ->with('info', 'Devam etmek için lütfen profil bilgilerinizi tamamlayın.');
        }

        return $next($request);
    }

    /**
     * İsteğin middleware'den geçip geçmeyeceğini kontrol et
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldPassThrough(Request $request): bool
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            // Wildcard kontrolü (onboarding.* gibi)
            if (str_contains($except, '*')) {
                $pattern = str_replace('*', '.*', $except);
                if (preg_match('#^' . $pattern . '$#', $request->route()->getName() ?? '')) {
                    return true;
                }
            }

            // Tam eşleşme kontrolü
            if ($request->route()->getName() === $except) {
                return true;
            }
        }

        return false;
    }
}
