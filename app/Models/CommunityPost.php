<?php

namespace App\Models;

use App\Models\Scopes\GameScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * CommunityPost Model
 * Topluluk gönderileri - Oyuna özel
 * 
 * İlişkiler:
 * - belongsTo: User
 * - belongsTo: Game
 * - morphMany: Comment
 * 
 * Global Scope: GameScope (otomatik game_id filtreleme)
 */
class CommunityPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'game_id',
        'type',
        'content',
        'is_featured',
        'likes_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
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
     * Model booted
     * GameScope ekle ve otomatik game_id atama
     */
    protected static function booted(): void
    {
        // Global scope ekle
        static::addGlobalScope(new GameScope());

        // Yeni kayıt oluşturulurken otomatik game_id ata
        static::creating(function ($post) {
            if (!$post->game_id && session('game_id')) {
                $post->game_id = session('game_id');
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
