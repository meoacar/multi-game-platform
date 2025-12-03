<?php

namespace App\Observers;

use App\Models\Game;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * GameObserver
 * 
 * Game model değişikliklerinde cache invalidation işlemlerini yönetir
 * 
 * Sorumluluklar:
 * - Game güncellendiğinde ilgili cache'leri temizle
 * - Game silindiğinde ilgili cache'leri temizle
 * - Game oluşturulduğunda aktif oyunlar cache'ini temizle
 * 
 * Requirements: 15.2
 */
class GameObserver
{
    /**
     * Yeni oyun oluşturulduğunda
     * 
     * @param Game $game
     * @return void
     */
    public function created(Game $game): void
    {
        // Aktif oyunlar listesi cache'ini temizle
        $this->clearActiveGamesCache();
        
        // Platform istatistikleri cache'ini temizle
        $this->clearPlatformStatsCache();
        
        Log::info('Game created, cache cleared', [
            'game_id' => $game->id,
            'game_slug' => $game->slug,
        ]);
    }

    /**
     * Oyun güncellendiğinde
     * 
     * @param Game $game
     * @return void
     */
    public function updated(Game $game): void
    {
        // Oyuna özel cache'i temizle
        $this->clearGameCache($game->id);
        
        // Aktif oyunlar listesi cache'ini temizle
        $this->clearActiveGamesCache();
        
        // Platform istatistikleri cache'ini temizle
        $this->clearPlatformStatsCache();
        
        Log::info('Game updated, cache cleared', [
            'game_id' => $game->id,
            'game_slug' => $game->slug,
            'changed_attributes' => $game->getDirty(),
        ]);
    }

    /**
     * Oyun silindiğinde
     * 
     * @param Game $game
     * @return void
     */
    public function deleted(Game $game): void
    {
        // Oyuna özel cache'i temizle
        $this->clearGameCache($game->id);
        
        // Aktif oyunlar listesi cache'ini temizle
        $this->clearActiveGamesCache();
        
        // Platform istatistikleri cache'ini temizle
        $this->clearPlatformStatsCache();
        
        Log::info('Game deleted, cache cleared', [
            'game_id' => $game->id,
            'game_slug' => $game->slug,
        ]);
    }

    /**
     * Belirli bir oyunun cache'ini temizle
     * 
     * @param int $gameId
     * @return void
     */
    private function clearGameCache(int $gameId): void
    {
        $prefix = config('games.cache.prefix', 'game');
        Cache::forget("{$prefix}.{$gameId}");
    }

    /**
     * Aktif oyunlar cache'ini temizle
     * 
     * @return void
     */
    private function clearActiveGamesCache(): void
    {
        $prefix = config('games.cache.prefix', 'game');
        Cache::forget("{$prefix}s.active");
    }

    /**
     * Platform istatistikleri cache'ini temizle
     * 
     * @return void
     */
    private function clearPlatformStatsCache(): void
    {
        Cache::forget('platform.stats');
    }
}
