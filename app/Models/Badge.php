<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Badge Model
 * Rozet sistemi - Oyuna özel veya cross-game (nullable game_id)
 * 
 * İlişkiler:
 * - belongsTo: Game (nullable - cross-game rozetler için)
 * - belongsToMany: User (pivot: user_badges)
 * 
 * Not: GameScope kullanılmaz çünkü rozetler cross-game olabilir
 */
class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'rarity',
        'is_hidden',
        'is_active',
        'sort_order',
        'xp_required',
    ];

    protected $casts = [
        'xp_required' => 'integer',
        'is_hidden' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Oyun ilişkisi (nullable - cross-game rozetler için)
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('unlocked_at', 'progress', 'progress_max')
            ->withTimestamps();
    }

    /**
     * Scope: Sadece aktif rozetler
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Kategoriye göre filtrele
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Nadirliğe göre filtrele
     */
    public function scopeByRarity($query, string $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    /**
     * Scope: Gizli olmayan rozetler
     */
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    /**
     * Scope: Belirli bir oyuna göre filtrele
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $gameId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForGame($query, ?int $gameId)
    {
        return $query->where('game_id', $gameId);
    }

    /**
     * Scope: Cross-game rozetler (game_id null olanlar)
     */
    public function scopeCrossGame($query)
    {
        return $query->whereNull('game_id');
    }
}
