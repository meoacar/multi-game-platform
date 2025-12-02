<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckAdminPermission Middleware
 * 
 * Belirli bir yetkiye sahip olmayan kullanıcıların erişimini engeller.
 * Kullanım: Route::middleware('permission:users.edit')
 */
class CheckAdminPermission
{
    /**
     * Gelen isteği işle
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission Gerekli yetki slug'ı (örn: users.edit, content.delete)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Kullanıcı giriş yapmamışsa login sayfasına yönlendir
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu işlem için giriş yapmalısınız'
                ], 401);
            }
            
            return redirect('/')->with('error', 'Bu işlem için giriş yapmalısınız');
        }

        $user = auth()->user();

        // Süper adminler tüm yetkilere sahiptir
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Kullanıcının yetkisi yoksa 403 hatası döndür
        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu işlem için yetkiniz yok'
                ], 403);
            }
            
            abort(403, 'Bu işlem için yetkiniz yok');
        }

        return $next($request);
    }
}
