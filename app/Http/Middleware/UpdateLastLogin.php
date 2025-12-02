<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kullanıcının son giriş zamanını günceller
 */
class UpdateLastLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Son güncelleme 5 dakikadan eskiyse güncelle (performans için)
            if (!$user->last_login_at || $user->last_login_at->lt(now()->subMinutes(5))) {
                $user->update(['last_login_at' => now()]);
            }
        }

        return $next($request);
    }
}
