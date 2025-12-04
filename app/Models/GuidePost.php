<?php

namespace App\Models;

use App\Models\Traits\HasGameScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * GuidePost Model
 * Rehber gönderileri - Oyuna özel
 * 
 * İlişkiler:
 * - belongsTo: User
 * - belongsTo: Game
 * - morphMany: Comment
 * 
 * Global Scope: HasGameScope (otomatik game_id filtreleme)
 */
class GuidePost extends Model
{
    use HasFactory, SoftDeletes, HasGameScope;

    protected $fillable = [
        'user_id',
        'game_id',
        'title',
        'slug',
        'content',
        'is_published',
        'is_featured',
        'views_count',
        'likes_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Model boot - Otomatik game_id atama
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($guide) {
            if (!$guide->game_id && session('game_id')) {
                $guide->game_id = session('game_id');
            }
        });
    }

    /**
     * Scope: Belirli bir oyuna göre filtrele
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $gameId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForGame($query, int $gameId)
    {
        return $query->where('game_id', $gameId);
    }
}
