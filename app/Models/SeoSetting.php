<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SEO Settings Model
 * 
 * Sayfa bazlı SEO ayarlarını yönetir
 */
class SeoSetting extends Model
{
    protected $fillable = [
        'page_type',
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'structured_data',
        'is_active',
    ];

    protected $casts = [
        'structured_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Sayfa tipine göre SEO ayarlarını getir
     */
    public static function getForPage(string $pageType): ?self
    {
        return cache()->remember("seo_settings_{$pageType}", 3600, function () use ($pageType) {
            return self::where('page_type', $pageType)
                ->where('is_active', true)
                ->first();
        });
    }

    /**
     * Cache'i temizle
     */
    public static function clearCache(string $pageType): void
    {
        cache()->forget("seo_settings_{$pageType}");
    }

    /**
     * Model kaydedildiğinde cache'i temizle
     */
    protected static function booted(): void
    {
        static::saved(function ($seoSetting) {
            self::clearCache($seoSetting->page_type);
        });

        static::deleted(function ($seoSetting) {
            self::clearCache($seoSetting->page_type);
        });
    }
}
