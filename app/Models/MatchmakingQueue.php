<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MatchmakingQueue Model
 * 
 * Eşleşme bekleyen kullanıcıların kuyruk bilgilerini tutar
 * 
 * İlişkiler:
 * - belongsTo: User, Game
 */
class MatchmakingQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'mode',
        'min_rank',
        'max_rank',
        'city',
        'microphone_required',
        'play_style',
        'status',
        'expires_at',
        'search_attempts',
    ];

    protected function casts(): array
    {
        return [
            'microphone_required' => 'boolean',
            'expires_at' => 'datetime',
            'search_attempts' => 'integer',
        ];
    }

    /**
     * Kuyruğun sahibi kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kuyruğun oyunu
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Kuyruğun aktif olup olmadığını kontrol et
     */
    public function isActive(): bool
    {
        return $this->status === 'searching' && $this->expires_at->isFuture();
    }

    /**
     * Kuyruğun süresi dolmuş mu kontrol et
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Deneme sayısını artır
     */
    public function incrementSearchAttempts(): void
    {
        $this->increment('search_attempts');
    }

    /**
     * Kuyruk durumunu güncelle
     */
    public function updateStatus(string $status): void
    {
        $this->update(['status' => $status]);
    }

    /**
     * Aktif kuyrukları getir
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'searching')
            ->where('expires_at', '>', now());
    }

    /**
     * Süresi dolmuş kuyrukları getir
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'searching')
            ->where('expires_at', '<=', now());
    }

    /**
     * Belirli bir oyun moduna göre filtrele
     */
    public function scopeByMode($query, string $mode)
    {
        return $query->where('mode', $mode);
    }

    /**
     * Belirli bir oyuna göre filtrele
     */
    public function scopeByGame($query, int $gameId)
    {
        return $query->where('game_id', $gameId);
    }
}
