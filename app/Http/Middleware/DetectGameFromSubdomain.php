<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Game;
use Illuminate\Support\Facades\Cache;

/**
 * DetectGameFromSubdomain Middleware
 * 
 * Subdomain'e göre otomatik olarak oyunu belirler ve session'a kaydeder.
 * 
 * Örnekler:
 * - pubg.squadbul.com → game_id = 1 (PUBG Mobile)
 * - valorant.squadbul.com → game_id = 2 (Valorant)
 * - lol.squadbul.com → game_id = 3 (League of Legends)
 * - squadbul.com → Ana sayfa (game_id = null)
 */
class DetectGameFromSubdomain
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);

        if ($subdomain && $subdomain !== 'www') {
            // Subdomain varsa oyunu bul
            $game = $this->findGameBySubdomain($subdomain);
            
            if ($game) {
                // Session'a kaydet
                session(['current_game_id' => $game->id]);
                session(['current_game' => $game]);
                
                // Config'e de kaydet (global scope için)
                config(['app.current_game_id' => $game->id]);
                config(['app.current_game' => $game]);
                
                // View'larda kullanmak için share et
                view()->share('currentGame', $game);
            } else {
                // Geçersiz subdomain - 404
                abort(404, 'Oyun bulunamadı');
            }
        } else {
            // Ana domain (squadbul.com) - game_id yok
            session()->forget(['current_game_id', 'current_game']);
            config(['app.current_game_id' => null]);
            config(['app.current_game' => null]);
            view()->share('currentGame', null);
        }

        return $next($request);
    }

    /**
     * Host'tan subdomain'i çıkar
     */
    protected function extractSubdomain(string $host): ?string
    {
        // localhost için özel durum
        if (str_contains($host, 'localhost') || str_contains($host, '127.0.0.1')) {
            return null;
        }

        $parts = explode('.', $host);
        
        // En az 3 parça olmalı (subdomain.domain.com)
        if (count($parts) >= 3) {
            return $parts[0];
        }

        return null;
    }

    /**
     * Subdomain'e göre oyunu bul (cache ile)
     */
    protected function findGameBySubdomain(string $subdomain): ?Game
    {
        return Cache::remember("game_subdomain_{$subdomain}", 3600, function () use ($subdomain) {
            return Game::where('subdomain', $subdomain)
                       ->where('is_active', true)
                       ->first();
        });
    }
}
