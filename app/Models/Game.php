<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Game Model
 * 
 * Oyun listesi - PUBG Mobile, Call of Duty Mobile, MLBB, etc.
 * 
 * İlişkiler:
 * - hasMany: LfgPost, Clan, GuidePost
 */
class Game extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot method - slug otomatik oluştur
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($game) {
            if (empty($game->slug)) {
                $game->slug = Str::slug($game->name);
            }
        });
    }

    /**
     * Sadece aktif oyunları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Oyunları sıralı getir
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Oyun ikonunun URL'ini al
     */
    public function getIconUrlAttribute(): string
    {
        if ($this->icon) {
            return asset('storage/' . $this->icon);
        }

        // Varsayılan ikon
        return asset('images/default-game-icon.png');
    }

    /**
     * İlişkiler
     */
    
    /**
     * Oyuna ait LFG ilanları
     */
    public function lfgPosts()
    {
        return $this->hasMany(LfgPost::class);
    }

    /**
     * Oyuna ait klanlar
     */
    public function clans()
    {
        return $this->hasMany(Clan::class);
    }

    /**
     * Oyuna ait rehberler
     */
    public function guides()
    {
        return $this->hasMany(GuidePost::class);
    }
}
