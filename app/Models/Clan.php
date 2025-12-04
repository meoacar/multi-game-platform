<?php

namespace App\Models;

use App\Models\Traits\HasGameScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Clan Model
 * Oyuncu klanları - Oyuna özel
 * 
 * İlişkiler:
 * - belongsTo: User (lider)
 * - belongsTo: Game
 * - belongsToMany: User (üyeler - pivot: clan_members)
 * - hasMany: ClanApplication (başvurular)
 * 
 * Global Scope: HasGameScope (otomatik game_id filtreleme)
 */
class Clan extends Model
{
    use HasFactory, SoftDeletes, HasGameScope;

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
     * GameScope ekle, slug ve game_id otomatik ata
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($clan) {
            // Slug otomatik oluştur
            if (empty($clan->slug)) {
                $clan->slug = Str::slug($clan->name);
            }

            // Otomatik game_id ata
            if (!$clan->game_id && session('game_id')) {
                $clan->game_id = session('game_id');
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
            'leader.profile',
            'game',
            'members.profile'
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
            'leader.profile',
            'game'
        ]);
    }
}
