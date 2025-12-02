<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Clan Model
 * Oyuncu klanları
 * 
 * İlişkiler:
 * - belongsTo: User (lider)
 * - belongsTo: Game
 * - belongsToMany: User (üyeler - pivot: clan_members)
 * - hasMany: ClanApplication (başvurular)
 */
class Clan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'game_id',
        'name',
        'slug',
        'logo_path',
        'description',
        'requirements',
        'min_rank',
        'max_rank',
        'min_age_range',
        'max_age_range',
        'city',
        'is_verified',
        'member_count',
        'max_members',
        'discord_invite',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'member_count' => 'integer',
        'max_members' => 'integer',
    ];

    /**
     * Model boot
     * Slug otomatik oluştur
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($clan) {
            if (empty($clan->slug)) {
                $clan->slug = Str::slug($clan->name);
            }
        });
    }

    /**
     * Klan lideri
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Oyun
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Klan üyeleri
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'clan_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Başvurular
     */
    public function applications()
    {
        return $this->hasMany(ClanApplication::class);
    }

    /**
     * Bekleyen başvurular
     */
    public function pendingApplications()
    {
        return $this->applications()->where('status', 'pending');
    }

    /**
     * Scope: Doğrulanmış klanlar
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Logo URL'si
     */
    public function getLogoUrlAttribute(): string
    {
        return $this->logo_path 
            ? asset('storage/' . $this->logo_path)
            : asset('images/default-clan-logo.png');
    }

    /**
     * Klan dolu mu?
     */
    public function isFull(): bool
    {
        return $this->member_count >= $this->max_members;
    }

    /**
     * Kullanıcı üye mi?
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Kullanıcı lider mi?
     */
    public function isLeader(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Üye sayısını güncelle
     */
    public function updateMemberCount(): void
    {
        $this->update([
            'member_count' => $this->members()->count()
        ]);
    }
}
