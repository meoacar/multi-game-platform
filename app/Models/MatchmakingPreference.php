<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MatchmakingPreference Model
 * 
 * Kullanıcıların varsayılan eşleşme tercihlerini tutar
 * 
 * İlişkiler:
 * - belongsTo: User
 */
class MatchmakingPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'default_mode',
        'auto_accept_matches',
        'microphone_required',
        'preferred_play_style',
        'same_city_only',
        'max_rank_difference',
    ];

    protected function casts(): array
    {
        return [
            'auto_accept_matches' => 'boolean',
            'microphone_required' => 'boolean',
            'same_city_only' => 'boolean',
            'max_rank_difference' => 'integer',
        ];
    }

    /**
     * Tercihin sahibi kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Otomatik kabul aktif mi kontrol et
     */
    public function hasAutoAccept(): bool
    {
        return $this->auto_accept_matches;
    }

    /**
     * Mikrofon zorunlu mu kontrol et
     */
    public function requiresMicrophone(): bool
    {
        return $this->microphone_required;
    }

    /**
     * Sadece aynı şehir mi kontrol et
     */
    public function requiresSameCity(): bool
    {
        return $this->same_city_only;
    }

    /**
     * Varsayılan tercihleri getir veya oluştur
     */
    public static function getOrCreateForUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'default_mode' => 'squad',
                'auto_accept_matches' => false,
                'microphone_required' => false,
                'preferred_play_style' => null,
                'same_city_only' => false,
                'max_rank_difference' => 2,
            ]
        );
    }

    /**
     * Tercihleri güncelle
     */
    public function updatePreferences(array $preferences): bool
    {
        return $this->update($preferences);
    }
}
