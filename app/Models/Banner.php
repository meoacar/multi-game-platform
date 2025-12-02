<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Banner Model
 * 
 * Banner/slider yönetimi için model
 * 
 * Özellikler:
 * - Zamanlama (başlangıç-bitiş tarihi)
 * - Hedef kitle segmentasyonu
 * - A/B testing desteği
 * - İstatistik takibi (görüntülenme, tıklama)
 * - Konum bazlı banner yönetimi
 */
class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_path',
        'mobile_image_path',
        'link_url',
        'link_target',
        'location',
        'order',
        'start_date',
        'end_date',
        'target_audience',
        'target_criteria',
        'is_ab_test',
        'ab_test_group',
        'ab_test_parent_id',
        'ab_test_weight',
        'view_count',
        'click_count',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_criteria' => 'array',
        'settings' => 'array',
        'is_ab_test' => 'boolean',
        'is_active' => 'boolean',
        'view_count' => 'integer',
        'click_count' => 'integer',
        'order' => 'integer',
        'ab_test_weight' => 'integer',
    ];

    /**
     * A/B test parent banner ilişkisi
     */
    public function abTestParent()
    {
        return $this->belongsTo(Banner::class, 'ab_test_parent_id');
    }

    /**
     * A/B test varyantları
     */
    public function abTestVariants()
    {
        return $this->hasMany(Banner::class, 'ab_test_parent_id');
    }

    /**
     * Kullanılabilir konumlar
     */
    public static function getAvailableLocations(): array
    {
        return [
            'home_hero' => 'Ana Sayfa Hero Slider',
            'home_top' => 'Ana Sayfa Üst Banner',
            'home_middle' => 'Ana Sayfa Orta Banner',
            'home_bottom' => 'Ana Sayfa Alt Banner',
            'sidebar' => 'Sidebar Banner',
            'content_top' => 'İçerik Üstü Banner',
            'content_bottom' => 'İçerik Altı Banner',
            'popup' => 'Popup Banner',
        ];
    }

    /**
     * Hedef kitle seçenekleri
     */
    public static function getTargetAudiences(): array
    {
        return [
            'all' => 'Tüm Kullanıcılar',
            'guests' => 'Misafirler (Giriş Yapmamış)',
            'members' => 'Üyeler (Giriş Yapmış)',
            'new_members' => 'Yeni Üyeler (30 gün içinde)',
            'active_members' => 'Aktif Üyeler (Son 7 gün)',
            'inactive_members' => 'Pasif Üyeler (30+ gün)',
            'premium' => 'Premium Üyeler',
            'custom' => 'Özel Segment',
        ];
    }

    /**
     * Banner'ın aktif olup olmadığını kontrol et
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        // Başlangıç tarihi kontrolü
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        // Bitiş tarihi kontrolü
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Banner'ın kullanıcı için gösterilip gösterilmeyeceğini kontrol et
     */
    public function shouldShowToUser($user = null): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        // Hedef kitle kontrolü
        switch ($this->target_audience) {
            case 'all':
                return true;

            case 'guests':
                return !$user;

            case 'members':
                return $user !== null;

            case 'new_members':
                return $user && $user->created_at->gt(now()->subDays(30));

            case 'active_members':
                return $user && $user->last_login_at && $user->last_login_at->gt(now()->subDays(7));

            case 'inactive_members':
                return $user && (!$user->last_login_at || $user->last_login_at->lt(now()->subDays(30)));

            case 'premium':
                return $user && $user->is_premium;

            case 'custom':
                return $this->matchesCustomCriteria($user);

            default:
                return false;
        }
    }

    /**
     * Özel kriterlere göre kullanıcı eşleşmesi
     */
    protected function matchesCustomCriteria($user): bool
    {
        if (!$this->target_criteria || !$user) {
            return false;
        }

        $criteria = $this->target_criteria;

        // Şehir kontrolü
        if (isset($criteria['cities']) && !empty($criteria['cities'])) {
            if (!in_array($user->profile->city ?? null, $criteria['cities'])) {
                return false;
            }
        }

        // XP aralığı kontrolü
        if (isset($criteria['min_xp']) && $user->xp < $criteria['min_xp']) {
            return false;
        }

        if (isset($criteria['max_xp']) && $user->xp > $criteria['max_xp']) {
            return false;
        }

        // Level aralığı kontrolü
        if (isset($criteria['min_level']) && $user->level < $criteria['min_level']) {
            return false;
        }

        if (isset($criteria['max_level']) && $user->level > $criteria['max_level']) {
            return false;
        }

        return true;
    }

    /**
     * Görüntülenme sayısını artır
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Tıklama sayısını artır
     */
    public function incrementClicks(): void
    {
        $this->increment('click_count');
    }

    /**
     * Tıklama oranını hesapla (CTR - Click Through Rate)
     */
    public function getClickThroughRate(): float
    {
        if ($this->view_count === 0) {
            return 0;
        }

        return round(($this->click_count / $this->view_count) * 100, 2);
    }

    /**
     * Görsel URL'ini al
     */
    public function getImageUrl(): string
    {
        return Storage::url($this->image_path);
    }

    /**
     * Mobil görsel URL'ini al
     */
    public function getMobileImageUrl(): ?string
    {
        return $this->mobile_image_path ? Storage::url($this->mobile_image_path) : null;
    }

    /**
     * Banner'ın zamanlanmış olup olmadığını kontrol et
     */
    public function isScheduled(): bool
    {
        return $this->start_date && $this->start_date->gt(now());
    }

    /**
     * Banner'ın süresi dolmuş mu kontrol et
     */
    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->lt(now());
    }

    /**
     * Scope: Aktif banner'lar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope: Konuma göre filtrele
     */
    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope: Sıralı
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    /**
     * Scope: A/B test olmayan banner'lar
     */
    public function scopeNotAbTest($query)
    {
        return $query->where('is_ab_test', false);
    }

    /**
     * Scope: A/B test parent banner'lar
     */
    public function scopeAbTestParents($query)
    {
        return $query->where('is_ab_test', true)
            ->whereNull('ab_test_parent_id');
    }
}
