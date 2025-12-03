<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * GameService
 * 
 * Oyun yönetimi iş mantığı servisi
 * 
 * Sorumluluklar:
 * - Mevcut oyunu alma
 * - Oyun değiştirme
 * - Aktif oyunları listeleme
 * - Oyun ayarlarını alma
 * - Oyun URL'i oluşturma
 * - Platform istatistikleri
 * - Son ziyaret edilen oyunu kaydetme
 * 
 * Requirements: 9.1, 9.2
 */
class GameService
{
    /**
     * Mevcut oyunu al
     * 
     * @return Game|null Mevcut oyun veya null
     */
    public function getCurrentGame(): ?Game
    {
        $gameId = session('game_id');
        
        if (!$gameId) {
            return null;
        }
        
        // Cache devre dışı ise direkt veritabanından al
        if (!config('games.cache.enabled', true)) {
            return Game::find($gameId);
        }
        
        $ttl = config('games.cache.ttl', 3600);
        $prefix = config('games.cache.prefix', 'game');
        
        return Cache::remember(
            "{$prefix}.{$gameId}",
            $ttl,
            fn() => Game::find($gameId)
        );
    }
    
    /**
     * Oyun değiştir
     * 
     * @param string $slug Oyun slug'ı
     * @return string Oyun subdomain URL'i
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function switchGame(string $slug): string
    {
        try {
            $game = Game::where('status', 'active')
                ->where('slug', $slug)
                ->firstOrFail();
            
            session([
                'game_id' => $game->id,
                'game' => $game,
                'game_slug' => $game->slug,
            ]);
            
            // Subdomain URL'i döndür
            return $this->getGameUrl($game);
        } catch (ModelNotFoundException $e) {
            // Oyun bulunamadı hatasını logla
            app(SecurityLogService::class)->logGameNotFound($slug, 'slug', [
                'operation' => 'switch_game',
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Aktif oyunları listele
     * 
     * @return Collection Aktif oyunlar koleksiyonu
     */
    public function getActiveGames(): Collection
    {
        // Cache devre dışı ise direkt veritabanından al
        if (!config('games.cache.enabled', true)) {
            return Game::where('status', 'active')
                ->orderBy('name')
                ->get();
        }
        
        $ttl = config('games.cache.ttl', 3600);
        $prefix = config('games.cache.prefix', 'game');
        
        return Cache::remember(
            "{$prefix}s.active",
            $ttl,
            fn() => Game::where('status', 'active')
                ->orderBy('name')
                ->get()
        );
    }
    
    /**
     * Oyun ayarlarını al
     * 
     * @param int $gameId Oyun ID
     * @return array Oyun ayarları
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getGameSettings(int $gameId): array
    {
        try {
            $game = Game::findOrFail($gameId);
            return $game->settings ?? [];
        } catch (ModelNotFoundException $e) {
            // Oyun bulunamadı hatasını logla
            app(SecurityLogService::class)->logGameNotFound((string)$gameId, 'id', [
                'operation' => 'get_game_settings',
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Oyun URL'i oluştur
     * 
     * @param Game $game Oyun modeli
     * @return string Oyun subdomain URL'i
     */
    public function getGameUrl(Game $game): string
    {
        $domain = config('app.domain', 'takimsistemi.com');
        $protocol = config('app.url_protocol', 'https');
        
        // Localhost kontrolü
        if (app()->environment('local')) {
            return config('app.url', 'http://localhost');
        }
        
        return "{$protocol}://{$game->slug}.{$domain}";
    }
    
    /**
     * Platform istatistikleri
     * 
     * @return array Platform geneli istatistikler
     */
    public function getPlatformStats(): array
    {
        // Cache devre dışı ise direkt veritabanından al
        if (!config('games.cache.enabled', true)) {
            return $this->calculatePlatformStats();
        }
        
        // Platform stats için daha kısa TTL (10 dakika)
        $ttl = config('games.cache.ttl', 3600) / 6;
        
        return Cache::remember('platform.stats', $ttl, function () {
            return $this->calculatePlatformStats();
        });
    }
    
    /**
     * Platform istatistiklerini hesapla
     * 
     * @return array
     */
    private function calculatePlatformStats(): array
    {
        return [
            'total_users' => DB::table('users')->count(),
            'total_tournaments' => DB::table('tournaments')->count(),
            'total_clans' => DB::table('clans')->count(),
            'total_lfg_posts' => DB::table('lfg_posts')->count(),
            'games' => Game::where('status', 'active')
                ->withCount([
                    'tournaments',
                    'clans',
                    'lfgPosts'
                ])
                ->get(),
        ];
    }
    
    /**
     * Kullanıcının son ziyaret ettiği oyunu kaydet
     * 
     * @param int $userId Kullanıcı ID
     * @param int $gameId Oyun ID
     * @return void
     */
    public function saveLastVisitedGame(int $userId, int $gameId): void
    {
        // Cache'e kaydet (veritabanı yerine)
        Cache::put("user.{$userId}.last_game", $gameId, 86400 * 30); // 30 gün
    }
    
    /**
     * Kullanıcının son ziyaret ettiği oyunu al
     * 
     * @param int $userId Kullanıcı ID
     * @return int|null Son ziyaret edilen oyun ID'si veya null
     */
    public function getLastVisitedGame(int $userId): ?int
    {
        return Cache::get("user.{$userId}.last_game");
    }
    
    /**
     * Oyun cache'ini temizle
     * 
     * @param int|null $gameId Belirli bir oyun ID'si (null ise tüm oyunlar)
     * @return void
     */
    public function clearGameCache(?int $gameId = null): void
    {
        $prefix = config('games.cache.prefix', 'game');
        
        if ($gameId) {
            // Belirli bir oyunun cache'ini temizle
            Cache::forget("{$prefix}.{$gameId}");
        } else {
            // Tüm oyun cache'lerini temizle
            Cache::forget("{$prefix}s.active");
            Cache::forget('platform.stats');
            
            // Tüm oyunların bireysel cache'lerini temizle
            $games = Game::all();
            foreach ($games as $game) {
                Cache::forget("{$prefix}.{$game->id}");
            }
        }
    }
    
    /**
     * Aktif oyunlar cache'ini temizle
     * 
     * @return void
     */
    public function clearActiveGamesCache(): void
    {
        $prefix = config('games.cache.prefix', 'game');
        Cache::forget("{$prefix}s.active");
    }
    
    /**
     * Platform istatistikleri cache'ini temizle
     * 
     * @return void
     */
    public function clearPlatformStatsCache(): void
    {
        Cache::forget('platform.stats');
    }
    
    /**
     * Tüm oyun ile ilgili cache'leri temizle
     * 
     * @return array Temizlenen cache anahtarları
     */
    public function clearAllGameCaches(): array
    {
        $prefix = config('games.cache.prefix', 'game');
        $cleared = [];
        
        // Aktif oyunlar cache'i
        Cache::forget("{$prefix}s.active");
        $cleared[] = "{$prefix}s.active";
        
        // Platform istatistikleri cache'i
        Cache::forget('platform.stats');
        $cleared[] = 'platform.stats';
        
        // Her oyunun bireysel cache'i
        $games = Game::all();
        foreach ($games as $game) {
            Cache::forget("{$prefix}.{$game->id}");
            $cleared[] = "{$prefix}.{$game->id}";
            
            // Kullanıcıların son ziyaret cache'lerini temizle (opsiyonel)
            // Bu çok fazla cache key olabileceği için dikkatli kullanılmalı
        }
        
        return $cleared;
    }
    
    /**
     * Cache'in sıcak tutulması (warm up)
     * Sık kullanılan verileri önceden cache'e yükle
     * 
     * @return array Yüklenen cache anahtarları
     */
    public function warmUpCache(): array
    {
        $prefix = config('games.cache.prefix', 'game');
        $ttl = config('games.cache.ttl', 3600);
        $warmed = [];
        
        try {
            // Aktif oyunları cache'e yükle
            $this->getActiveGames();
            $warmed[] = "{$prefix}s.active";
            
            // Platform istatistiklerini cache'e yükle
            $this->getPlatformStats();
            $warmed[] = 'platform.stats';
            
            // Her aktif oyunu cache'e yükle
            $games = Game::where('status', 'active')->get();
            foreach ($games as $game) {
                Cache::remember(
                    "{$prefix}.{$game->id}",
                    $ttl,
                    fn() => $game
                );
                $warmed[] = "{$prefix}.{$game->id}";
            }
            
        } catch (\Exception $e) {
            $warmed[] = 'error: ' . $e->getMessage();
        }
        
        return $warmed;
    }
}
