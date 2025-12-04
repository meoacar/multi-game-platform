<?php

namespace App\Models;

use App\Models\Traits\HasGameScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * LFG (Looking For Group) Post Model
 * Takım arama ilanları - Oyuna özel
 * 
 * İlişkiler:
 * - belongsTo: User (ilan sahibi)
 * - belongsTo: Game
 * - hasMany: LfgApplication (başvurular)
 * 
 * Global Scope: HasGameScope (otomatik game_id filtreleme)
 */
class LfgPost extends Model
{
    use HasFactory, SoftDeletes, HasGameScope;

    protected $fillable = [
        'user_id',
        'game_id',
        'title',
        'description',
        'min_rank',
        'max_rank',
        'mode',
        'microphone_required',
        'min_age_range',
        'max_age_range',
        'city',
        'play_style_tag',
        'status',
        'expires_at',
        'is_featured',
        'admin_notes',
        'views_count',
    ];

    protected $casts = [
        'microphone_required' => 'boolean',
        'is_featured' => 'boolean',
        'expires_at' => 'datetime',
        'views_count' => 'integer',
    ];

    /**
     * İlan sahibi
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Oyun
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Başvurular
     */
    public function applications()
    {
        return $this->hasMany(LfgApplication::class);
    }

    /**
     * Bekleyen başvurular
     */
    public function pendingApplications()
    {
        return $this->applications()->where('status', 'pending');
    }

    /**
     * Kabul edilen başvurular
     */
    public function acceptedApplications()
    {
        return $this->applications()->where('status', 'accepted');
    }

    /**
     * Scope: Açık ilanlar
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope: Kapalı ilanlar
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope: Öne çıkan ilanlar
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * İlanı kapat
     */
    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    /**
     * İlanı aç
     */
    public function reopen(): void
    {
        $this->update(['status' => 'open']);
    }

    /**
     * Görüntülenme sayısını artır
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * İlan açık mı?
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * İlan kapalı mı?
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * İlan süresi dolmuş mu?
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Model boot - Otomatik game_id atama
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lfgPost) {
            if (!$lfgPost->game_id && session('game_id')) {
                $lfgPost->game_id = session('game_id');
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

    /**
     * Scope: Eager load ile ilişkileri yükle (N+1 prevention - Requirements 15.3)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRelations($query)
    {
        return $query->with([
            'user.profile',
            'game',
            'applications.user.profile'
        ]);
    }

    /**
     * Scope: Sadece temel ilişkileri yükle (hafif versiyon)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithBasicRelations($query)
    {
        return $query->with([
            'user.profile',
            'game'
        ]);
    }
}
