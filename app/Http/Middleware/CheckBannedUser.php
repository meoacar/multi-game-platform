<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedUser
{
    /**
     * Yasaklanmış kullanıcıları kontrol et
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kullanıcı giriş yapmışsa ve yasaklanmışsa
        if ($request->user() && $request->user()->isBanned()) {
            // API isteği ise JSON döndür
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hesabınız yasaklanmış durumda. Lütfen destek ekibi ile iletişime geçin.',
                ], 403);
            }

            // Web isteği ise logout yap ve yönlendir
            auth()->logout();
            return redirect()->route('home')
                ->with('error', 'Hesabınız yasaklanmış durumda. Lütfen destek ekibi ile iletişime geçin.');
        }

        return $next($request);
    }
}
