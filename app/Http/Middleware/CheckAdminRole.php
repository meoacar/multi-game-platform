<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckAdminRole Middleware
 * 
 * Belirli bir role sahip olmayan kullanıcıların erişimini engeller.
 * Kullanım: Route::middleware('role:moderator')
 * Birden fazla rol: Route::middleware('role:moderator,content_manager')
 */
class CheckAdminRole
{
    /**
     * Gelen isteği işle
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles Gerekli rol slug'ları (örn: moderator, super_admin)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Kullanıcı giriş yapmamışsa login sayfasına yönlendir
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu sayfaya erişim için giriş yapmalısınız'
                ], 401);
            }
            
            return redirect('/')->with('error', 'Bu sayfaya erişim için giriş yapmalısınız');
        }

        $user = auth()->user();

        // Kullanıcının rollerinden herhangi birine sahip mi kontrol et
        if (!$user->hasAnyRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu sayfaya erişim yetkiniz yok'
                ], 403);
            }
            
            abort(403, 'Bu sayfaya erişim yetkiniz yok');
        }

        return $next($request);
    }
}
