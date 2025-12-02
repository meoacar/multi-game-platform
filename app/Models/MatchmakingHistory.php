<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MatchmakingHistory Model
 * 
 * Tamamlanan eşleşmelerin geçmişini tutar
 * 
 * İlişkiler:
 * - belongsTo: User, MatchmakingMatch
 */
class MatchmakingHistory extends Model
{
    use HasFactory;

    protected $table = 'matchmaking_history';

    protected $fillable = [
        'user_id',
        'match_id',
        'result',
        'wait_time_seconds',
        'compatibility_score',
        'matched_users',
    ];

    protected function casts(): array
    {
        return [
            'wait_time_seconds' => 'integer',
            'compatibility_score' => 'integer',
            'matched_users' => 'array',
        ];
    }

    /**
     * Geçmişin sahibi kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * İlgili eşleşme
     */
    public function match()
    {
        return $this->belongsTo(MatchmakingMatch::class, 'match_id');
    }

    /**
     * Eşleşme başarılı mı kontrol et
     */
    public function isSuccessful(): bool
    {
        return $this->result === 'completed';
    }

    /**
     * Bekleme süresini dakika cinsinden getir
     */
    public function getWaitTimeInMinutes(): float
    {
        return round($this->wait_time_seconds / 60, 2);
    }

    /**
     * Belirli bir kullanıcının geçmişini getir
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Başarılı eşleşmeleri getir
     */
    public function scopeSuccessful($query)
    {
        return $query->where('result', 'completed');
    }

    /**
     * Başarısız eşleşmeleri getir
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('result', ['cancelled', 'timeout', 'rejected']);
    }

    /**
     * Belirli bir tarih aralığındaki geçmişi getir
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
