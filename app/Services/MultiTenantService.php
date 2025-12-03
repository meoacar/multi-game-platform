<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Multi-Tenant Service
 * 
 * Çoklu oyun platformu için tenant-aware operasyonlar
 * 
 * Sorumluluklar:
 * - Query'leri oyuna göre filtreleme
 * - Collection'ları oyuna göre filtreleme
 * - Model'lere otomatik game_id atama
 * - Tenant bağlamı yönetimi
 */
class MultiTenantService
{
    /**
     * Query'yi oyuna göre filtrele
     * 
     * Verilen query builder'a game_id filtresi ekler.
     * Game ID session'dan alınır veya parametre olarak verilebilir.
     * 
     * @param Builder $query Query builder instance
     * @param int|null $gameId Oyun ID (null ise session'dan alınır)
     * @return Builder Filtrelenmiş query builder
     * @throws \Exception Game context bulunamazsa
     * 
     * @example
     * $query = Tournament::query();
     * $filteredQuery = $multiTenantService->scopeQuery($query);
     * $tournaments = $filteredQuery->get(); // Sadece mevcut oyunun turnuvaları
     */
    public function scopeQuery(Builder $query, ?int $gameId = null): Builder
    {
        $gameId = $gameId ?? session('game_id');
        
        if (!$gameId) {
            throw new \Exception('Game context not found. Please select a game first.');
        }
        
        return $query->where('game_id', $gameId);
    }

    /**
     * Collection'ı oyuna göre filtrele
     * 
     * Verilen collection'daki item'ları game_id'ye göre filtreler.
     * Game ID session'dan alınır veya parametre olarak verilebilir.
     * 
     * @param Collection $collection Filtrelenecek collection
     * @param int|null $gameId Oyun ID (null ise session'dan alınır)
     * @return Collection Filtrelenmiş collection
     * 
     * @example
     * $allTournaments = Tournament::withoutGlobalScopes()->get();
     * $gameTournaments = $multiTenantService->filterByGame($allTournaments);
     */
    public function filterByGame(Collection $collection, ?int $gameId = null): Collection
    {
        $gameId = $gameId ?? session('game_id');
        
        if (!$gameId) {
            return $collection;
        }
        
        return $collection->filter(function ($item) use ($gameId) {
            // Model ise game_id property'sine bak
            if ($item instanceof Model) {
                return isset($item->game_id) && $item->game_id === $gameId;
            }
            
            // Array ise game_id key'ine bak
            if (is_array($item)) {
                return isset($item['game_id']) && $item['game_id'] === $gameId;
            }
            
            // Object ise game_id property'sine bak
            if (is_object($item)) {
                return isset($item->game_id) && $item->game_id === $gameId;
            }
            
            return false;
        })->values(); // Index'leri sıfırla
    }

    /**
     * Model'e otomatik game_id ata
     * 
     * Eğer model'in game_id'si yoksa, session'daki game_id'yi atar.
     * Model henüz kaydedilmemiş olmalıdır (creating event'inde kullanılır).
     * 
     * @param Model $model Game ID atanacak model
     * @return void
     * 
     * @example
     * $tournament = new Tournament(['name' => 'Test Tournament']);
     * $multiTenantService->assignGameId($tournament);
     * $tournament->save(); // game_id otomatik atanmış olacak
     */
    public function assignGameId(Model $model): void
    {
        // Eğer model'de zaten game_id varsa, dokunma
        if (isset($model->game_id) && $model->game_id) {
            return;
        }
        
        // Session'dan game_id al
        $gameId = session('game_id');
        
        // Game ID varsa ata
        if ($gameId) {
            $model->game_id = $gameId;
        }
    }

    /**
     * Mevcut game context'i al
     * 
     * Session'daki game_id'yi döndürür
     * 
     * @return int|null Mevcut oyun ID'si veya null
     */
    public function getCurrentGameId(): ?int
    {
        return session('game_id');
    }

    /**
     * Game context'in var olup olmadığını kontrol et
     * 
     * @return bool Game context var mı?
     */
    public function hasGameContext(): bool
    {
        return session()->has('game_id') && session('game_id') !== null;
    }

    /**
     * Game context'i ayarla
     * 
     * Session'a game_id kaydet
     * 
     * @param int $gameId Oyun ID
     * @return void
     */
    public function setGameContext(int $gameId): void
    {
        session(['game_id' => $gameId]);
    }

    /**
     * Game context'i temizle
     * 
     * Session'dan game_id'yi sil
     * 
     * @return void
     */
    public function clearGameContext(): void
    {
        session()->forget('game_id');
        session()->forget('game');
    }

    /**
     * Birden fazla model'e toplu game_id ata
     * 
     * @param array<Model> $models Game ID atanacak model dizisi
     * @return void
     */
    public function assignGameIdBulk(array $models): void
    {
        $gameId = session('game_id');
        
        if (!$gameId) {
            return;
        }
        
        foreach ($models as $model) {
            if ($model instanceof Model && (!isset($model->game_id) || !$model->game_id)) {
                $model->game_id = $gameId;
            }
        }
    }

    /**
     * Query'nin game-specific olup olmadığını kontrol et
     * 
     * Model'in game_id column'una sahip olup olmadığını kontrol eder
     * 
     * @param Builder $query Query builder
     * @return bool Model game-specific mi?
     */
    public function isGameSpecificQuery(Builder $query): bool
    {
        $model = $query->getModel();
        $table = $model->getTable();
        
        // Model'in fillable veya attributes'unda game_id var mı kontrol et
        return in_array('game_id', $model->getFillable()) || 
               array_key_exists('game_id', $model->getAttributes());
    }

    /**
     * Cross-game query oluştur
     * 
     * Global scope'ları devre dışı bırakarak tüm oyunlardan veri çeker
     * 
     * @param string $modelClass Model sınıfı (örn: Tournament::class)
     * @return Builder Cross-game query builder
     * 
     * @example
     * $allTournaments = $multiTenantService->crossGameQuery(Tournament::class)->get();
     */
    public function crossGameQuery(string $modelClass): Builder
    {
        return $modelClass::withoutGlobalScopes();
    }

    /**
     * Belirli bir oyun için query oluştur
     * 
     * @param string $modelClass Model sınıfı
     * @param int $gameId Oyun ID
     * @return Builder Oyuna özel query builder
     * 
     * @example
     * $pubgTournaments = $multiTenantService->queryForGame(Tournament::class, 1)->get();
     */
    public function queryForGame(string $modelClass, int $gameId): Builder
    {
        return $modelClass::withoutGlobalScopes()->where('game_id', $gameId);
    }
}
