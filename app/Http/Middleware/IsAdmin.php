<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Middleware
 * Sadece admin kullanıcıların erişimine izin verir
 */
class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kullanıcı giriş yapmamışsa login sayfasına yönlendir
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Bu sayfaya erişim için giriş yapmalısınız');
        }

        // Kullanıcı admin değilse ana sayfaya yönlendir
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok');
        }

        return $next($request);
    }
}
