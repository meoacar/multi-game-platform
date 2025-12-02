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

        // Onboarding tamamlanmamışsa yönlendir
        if (!$user->hasCompletedOnboarding()) {
            // API isteği ise JSON döndür
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lütfen önce profil tamamlama sürecini tamamlayın.',
                    'redirect' => route('onboarding.index'),
                ], 403);
            }

            // Web isteği ise onboarding'e yönlendir
            return redirect()->route('onboarding.index')
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
