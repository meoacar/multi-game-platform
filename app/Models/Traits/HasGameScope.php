<?php

namespace App\Models\Traits;

use App\Models\Scopes\GameScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * HasGameScope Trait
 * 
 * Bu trait'i kullanan model'ler otomatik olarak mevcut oyuna göre filtrelenir.
 * 
 * Kullanım:
 * class Clan extends Model {
 *     use HasGameScope;
 * }
 * 
 * Tüm oyunları görmek için:
 * Clan::withoutGameScope()->get()
 */
trait HasGameScope
{
    /**
     * Model boot edildiğinde global scope ekle
     */
    protected static function bootHasGameScope(): void
    {
        static::addGlobalScope(new GameScope());
    }

    /**
     * Game scope olmadan query yap
     */
    public function scopeWithoutGameScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(GameScope::class);
    }

    /**
     * Belirli bir oyun için query yap
     */
    public function scopeForGame(Builder $query, int $gameId): Builder
    {
        return $query->withoutGlobalScope(GameScope::class)
                     ->where('game_id', $gameId);
    }

    /**
     * Tüm oyunlar için query yap
     */
    public function scopeAllGames(Builder $query): Builder
    {
        return $query->withoutGlobalScope(GameScope::class);
    }
}
