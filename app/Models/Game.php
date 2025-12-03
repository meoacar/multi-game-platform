<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Game Model
 * 
 * Multi-game platform için oyun yönetimi
 * 
 * İlişkiler:
 * - hasMany: Tournament, Clan, LfgPost, Badge, GuidePost, CommunityPost
 * 
 * Özellikler:
 * - Subdomain routing için slug
 * - Oyuna özel tema ayarları (settings JSON)
 * - Aktif/inaktif durum kontrolü
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
        'logo',
        'icon',
        'description',
        'status',
        'is_active',
        'settings',
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
        'settings' => 'array',
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
     * Scope: Sadece aktif oyunları getir
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Slug'a göre oyun bul
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope: Oyunları sıralı getir
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Helper: Oyun tema rengini al
     */
    public function getThemeColor(): string
    {
        return $this->settings['theme_color'] ?? '#FF6B00';
    }

    /**
     * Helper: Maksimum takım boyutunu al
     */
    public function getMaxTeamSize(): int
    {
        return $this->settings['max_team_size'] ?? 4;
    }

    /**
     * Helper: Desteklenen platformları al
     */
    public function getPlatforms(): array
    {
        return $this->settings['platforms'] ?? [];
    }

    /**
     * Helper: Oyun özelliklerini kontrol et
     */
    public function hasFeature(string $feature): bool
    {
        return $this->settings['features'][$feature] ?? false;
    }

    /**
     * Accessor: Oyun logo URL'ini al
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }

        // Varsayılan logo
        return asset('images/default-game-logo.png');
    }

    /**
     * Accessor: Oyun ikonunun URL'ini al (backward compatibility)
     */
    public function getIconUrlAttribute(): string
    {
        if ($this->icon) {
            return asset('storage/' . $this->icon);
        }

        // Logo varsa onu kullan
        if ($this->logo) {
            return $this->logo_url;
        }

        // Varsayılan ikon
        return asset('images/default-game-icon.png');
    }

    /**
     * İlişkiler
     */
    
    /**
     * Oyuna ait turnuvalar
     */
    public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }

    /**
     * Oyuna ait klanlar
     */
    public function clans()
    {
        return $this->hasMany(Clan::class);
    }

    /**
     * Oyuna ait LFG ilanları
     */
    public function lfgPosts()
    {
        return $this->hasMany(LfgPost::class);
    }

    /**
     * Oyuna ait rozetler
     */
    public function badges()
    {
        return $this->hasMany(Badge::class);
    }

    /**
     * Oyuna ait rehberler
     */
    public function guides()
    {
        return $this->hasMany(GuidePost::class);
    }

    /**
     * Oyuna ait topluluk gönderileri
     */
    public function communityPosts()
    {
        return $this->hasMany(CommunityPost::class);
    }
}
