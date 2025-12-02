<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Profile Model
 * 
 * Kullanıcı profil bilgileri - PUBG ve kişisel bilgiler
 * 
 * İlişkiler:
 * - belongsTo: User
 */
class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nickname',
        'pubg_id',
        'rank',
        'server_region',
        'city',
        'age_range',
        'gender',
        'play_style',
        'favorite_maps',
        'bio',
        'avatar_path',
        'twitch_username',
        'youtube_channel',
        'discord_username',
        'profile_views',
        'is_profile_completed',
        // Oyun İstatistikleri
        'matches_played',
        'wins',
        'win_rate',
        'kills',
        'deaths',
        'kd_ratio',
        'headshots',
        'headshot_rate',
        'top_10_finishes',
        'damage_dealt',
        'survival_time',
        'longest_kill',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'profile_views' => 'integer',
        'is_profile_completed' => 'boolean',
        'favorite_maps' => 'array',
        // Oyun İstatistikleri
        'matches_played' => 'integer',
        'wins' => 'integer',
        'win_rate' => 'decimal:2',
        'kills' => 'integer',
        'deaths' => 'integer',
        'kd_ratio' => 'decimal:2',
        'headshots' => 'integer',
        'headshot_rate' => 'decimal:2',
        'top_10_finishes' => 'integer',
        'damage_dealt' => 'integer',
        'survival_time' => 'integer',
        'longest_kill' => 'integer',
    ];

    /**
     * Profilin sahibi kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Profil görüntülenme sayısını artır
     */
    public function incrementViews(): void
    {
        $this->increment('profile_views');
    }

    /**
     * Profilin tamamlanma durumunu kontrol et ve güncelle
     */
    public function checkCompletion(): void
    {
        $requiredFields = ['nickname', 'rank', 'city', 'play_style'];
        $isCompleted = true;

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                $isCompleted = false;
                break;
            }
        }

        if ($this->is_profile_completed !== $isCompleted) {
            $this->update(['is_profile_completed' => $isCompleted]);
        }
    }

    /**
     * Avatar URL'ini al
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            return asset('storage/' . $this->avatar_path);
        }

        // Varsayılan avatar
        return asset('images/default-avatar.svg');
    }

    /**
     * K/D oranını hesapla ve güncelle
     */
    public function calculateKdRatio(): void
    {
        if ($this->deaths > 0) {
            $this->kd_ratio = round($this->kills / $this->deaths, 2);
        } else {
            $this->kd_ratio = $this->kills; // Hiç ölmediyse K/D = kills
        }
        $this->save();
    }

    /**
     * Win rate'i hesapla ve güncelle
     */
    public function calculateWinRate(): void
    {
        if ($this->matches_played > 0) {
            $this->win_rate = round(($this->wins / $this->matches_played) * 100, 2);
        } else {
            $this->win_rate = 0;
        }
        $this->save();
    }

    /**
     * Headshot rate'i hesapla ve güncelle
     */
    public function calculateHeadshotRate(): void
    {
        if ($this->kills > 0) {
            $this->headshot_rate = round(($this->headshots / $this->kills) * 100, 2);
        } else {
            $this->headshot_rate = 0;
        }
        $this->save();
    }

    /**
     * Tüm istatistikleri yeniden hesapla
     */
    public function recalculateStatistics(): void
    {
        $this->calculateKdRatio();
        $this->calculateWinRate();
        $this->calculateHeadshotRate();
    }

    /**
     * İstatistikleri güncelle (admin tarafından manuel güncelleme)
     */
    public function updateStatistics(array $stats): void
    {
        $this->update([
            'matches_played' => $stats['matches_played'] ?? $this->matches_played,
            'wins' => $stats['wins'] ?? $this->wins,
            'kills' => $stats['kills'] ?? $this->kills,
            'deaths' => $stats['deaths'] ?? $this->deaths,
            'headshots' => $stats['headshots'] ?? $this->headshots,
            'top_10_finishes' => $stats['top_10_finishes'] ?? $this->top_10_finishes,
            'damage_dealt' => $stats['damage_dealt'] ?? $this->damage_dealt,
            'survival_time' => $stats['survival_time'] ?? $this->survival_time,
            'longest_kill' => $stats['longest_kill'] ?? $this->longest_kill,
        ]);

        // Oranları yeniden hesapla
        $this->recalculateStatistics();
    }
}
