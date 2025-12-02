<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MatchmakingMatch Model
 * 
 * Oluşturulan eşleşmeleri ve kabul durumlarını tutar
 * 
 * İlişkiler:
 * - belongsTo: Game
 * - hasMany: MatchmakingHistory
 */
class MatchmakingMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'mode',
        'user_ids',
        'compatibility_score',
        'match_criteria',
        'status',
        'acceptance_status',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'user_ids' => 'array',
            'match_criteria' => 'array',
            'acceptance_status' => 'array',
            'compatibility_score' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Eşleşmenin oyunu
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Eşleşmenin geçmiş kayıtları
     */
    public function history()
    {
        return $this->hasMany(MatchmakingHistory::class, 'match_id');
    }

    /**
     * Eşleşmedeki kullanıcıları getir
     */
    public function users()
    {
        return User::whereIn('id', $this->user_ids ?? [])->get();
    }

    /**
     * Eşleşmenin aktif olup olmadığını kontrol et
     */
    public function isActive(): bool
    {
        return $this->status === 'pending' && $this->expires_at->isFuture();
    }

    /**
     * Eşleşmenin süresi dolmuş mu kontrol et
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Kullanıcının eşleşmeyi kabul etmesini kaydet
     */
    public function acceptByUser(int $userId): void
    {
        $acceptanceStatus = $this->acceptance_status ?? [];
        $acceptanceStatus[$userId] = 'accepted';
        $this->update(['acceptance_status' => $acceptanceStatus]);
    }

    /**
     * Kullanıcının eşleşmeyi reddetmesini kaydet
     */
    public function rejectByUser(int $userId): void
    {
        $acceptanceStatus = $this->acceptance_status ?? [];
        $acceptanceStatus[$userId] = 'rejected';
        $this->update([
            'acceptance_status' => $acceptanceStatus,
            'status' => 'rejected',
        ]);
    }

    /**
     * Tüm kullanıcılar kabul etti mi kontrol et
     */
    public function allUsersAccepted(): bool
    {
        $acceptanceStatus = $this->acceptance_status ?? [];
        $userIds = $this->user_ids ?? [];

        if (count($acceptanceStatus) !== count($userIds)) {
            return false;
        }

        foreach ($userIds as $userId) {
            if (!isset($acceptanceStatus[$userId]) || $acceptanceStatus[$userId] !== 'accepted') {
                return false;
            }
        }

        return true;
    }

    /**
     * Herhangi bir kullanıcı reddetti mi kontrol et
     */
    public function anyUserRejected(): bool
    {
        $acceptanceStatus = $this->acceptance_status ?? [];
        return in_array('rejected', $acceptanceStatus);
    }

    /**
     * Kullanıcının kabul durumunu getir
     */
    public function getUserAcceptanceStatus(int $userId): ?string
    {
        $acceptanceStatus = $this->acceptance_status ?? [];
        return $acceptanceStatus[$userId] ?? null;
    }

    /**
     * Bekleyen eşleşmeleri getir
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    /**
     * Süresi dolmuş eşleşmeleri getir
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '<=', now());
    }

    /**
     * Belirli bir kullanıcının eşleşmelerini getir
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->whereJsonContains('user_ids', $userId);
    }
}
