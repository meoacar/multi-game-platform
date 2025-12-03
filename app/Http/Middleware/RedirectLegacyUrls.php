<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Game;
use Illuminate\Support\Facades\Cache;

/**
 * Eski URL'leri yeni multi-game URL'lerine yönlendirir
 * 
 * Eski format: takimsistemi.com/turnuvalar
 * Yeni format: pubg.takimsistemi.com/turnuvalar
 */
class RedirectLegacyUrls
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Subdomain kontrolü - eğer subdomain varsa, bu yeni format demektir
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // Subdomain varsa (örn: pubg.takimsistemi.com), redirect yapma
        if (count($parts) >= 3) {
            return $next($request);
        }
        
        // Ana domain'deyiz (takimsistemi.com)
        // Eğer path game-specific bir route ise, default game'e yönlendir
        
        $path = $request->path();
        
        // Game-specific route'lar listesi
        $gameSpecificRoutes = [
            'ilanlar',
            'klanlar',
            'rehber',
            'topluluk',
            'cihazlar',
            'liderlik-tablosu',
            'rozetler',
            'xp-gecmisi',
            'takimlar',
            'turnuvalar',
            'arkadaslar',
            'arkadas-istekleri',
            'mesajlar',
            'eslesme',
            'profil',
            'profilim',
            'ayarlar',
            'bildirimler',
            'notifications',
        ];
        
        // Path'in ilk segmentini al
        $firstSegment = explode('/', $path)[0];
        
        // Eğer game-specific bir route ise
        if (in_array($firstSegment, $gameSpecificRoutes)) {
            // Default game'i al (PUBG - game_id = 1)
            $defaultGame = $this->getDefaultGame();
            
            if ($defaultGame) {
                // Yeni URL'i oluştur
                $domain = config('app.domain', 'takimsistemi.com');
                $newUrl = $request->secure() ? 'https://' : 'http://';
                $newUrl .= $defaultGame->slug . '.' . $domain;
                $newUrl .= '/' . $path;
                
                // Query string varsa ekle
                if ($request->getQueryString()) {
                    $newUrl .= '?' . $request->getQueryString();
                }
                
                // Permanent redirect (301)
                return redirect($newUrl, 301);
            }
        }
        
        // Ana sayfa veya diğer route'lar için normal akış
        return $next($request);
    }
    
    /**
     * Default game'i al (cache'lenmiş)
     */
    private function getDefaultGame(): ?Game
    {
        return Cache::remember('default_game', 3600, function () {
            // İlk aktif oyunu default olarak kullan (genellikle PUBG - id=1)
            return Game::where('status', 'active')
                ->orderBy('id', 'asc')
                ->first();
        });
    }
}
