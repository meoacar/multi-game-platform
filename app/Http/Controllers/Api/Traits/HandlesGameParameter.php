<?php

namespace App\Http\Controllers\Api\Traits;

use App\Models\Game;
use Illuminate\Http\Request;

/**
 * Handles Game Parameter Trait
 * 
 * API controller'larında game_id veya game_slug parametresini
 * işlemek için kullanılan trait.
 */
trait HandlesGameParameter
{
    /**
     * Request'ten game_id'yi al
     * 
     * game_slug varsa önce onu game_id'ye çevir
     * game_id yoksa ve session'da varsa session'dan al
     * 
     * @param Request $request
     * @return int|null
     */
    protected function getGameIdFromRequest(Request $request): ?int
    {
        // Önce game_slug kontrolü (öncelikli)
        if ($request->has('game_slug')) {
            $game = Game::where('slug', $request->game_slug)
                ->where('status', 'active')
                ->first();
            
            if ($game) {
                return $game->id;
            }
            
            // Geçersiz game_slug
            return null;
        }
        
        // Sonra game_id kontrolü
        if ($request->has('game_id')) {
            return (int) $request->game_id;
        }
        
        // Son olarak session'dan al (web istekleri için)
        if (session()->has('game_id')) {
            return session('game_id');
        }
        
        return null;
    }
    
    /**
     * Query'ye game filtresi uygula
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyGameFilter($query, Request $request)
    {
        $gameId = $this->getGameIdFromRequest($request);
        
        if ($gameId) {
            $query->where('game_id', $gameId);
        }
        
        return $query;
    }
    
    /**
     * Game ID'yi validate et
     * 
     * @param int|null $gameId
     * @return bool
     */
    protected function validateGameId(?int $gameId): bool
    {
        if (!$gameId) {
            return false;
        }
        
        return Game::where('id', $gameId)
            ->where('status', 'active')
            ->exists();
    }
}
