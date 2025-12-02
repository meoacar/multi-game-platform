<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Bakım modu ayarını kontrol et
        $maintenanceMode = \App\Models\Setting::where('key', 'maintenance_mode')->first();
        
        // Bakım modu aktif değilse normal devam et
        if (!$maintenanceMode || !$maintenanceMode->value) {
            return $next($request);
        }
        
        // Admin kullanıcılar bakım modunda da erişebilir
        if (auth()->check() && auth()->user()->is_admin) {
            return $next($request);
        }
        
        // Admin paneline erişim izni ver (giriş yapabilmek için)
        if ($request->is('admin*') || $request->is('giris') || $request->is('login')) {
            return $next($request);
        }
        
        // Bakım modu mesajını al
        $maintenanceMessage = \App\Models\Setting::where('key', 'maintenance_message')->first();
        $maintenanceEta = \App\Models\Setting::where('key', 'maintenance_eta')->first();
        
        $message = $maintenanceMessage ? $maintenanceMessage->value : 'Sitemiz şu anda bakımdadır. Lütfen daha sonra tekrar deneyin.';
        $eta = $maintenanceEta ? $maintenanceEta->value : null;
        
        // Bakım modu sayfasını göster
        return response()->view('maintenance', [
            'message' => $message,
            'eta' => $eta,
        ], 503);
    }
}
