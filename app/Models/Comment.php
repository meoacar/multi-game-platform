<?php

namespace App\Models;

use App\Models\Traits\HasGameScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Comment Model
 * Yorumlar - Oyuna özel içeriklere ait
 * 
 * İlişkiler:
 * - belongsTo: User
 * - belongsTo: Game
 * - morphTo: Commentable (GuidePost, CommunityPost vb.)
 * 
 * Global Scope: HasGameScope (otomatik game_id filtreleme)
 */
class Comment extends Model
{
    use HasFactory, SoftDeletes, HasGameScope;

    protected $fillable = [
        'user_id',
        'game_id',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Yorumun ait olduğu oyun
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Model boot - Otomatik game_id atama
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            if (!$comment->game_id && session('game_id')) {
                $comment->game_id = session('game_id');
            }
        });
    }
}
