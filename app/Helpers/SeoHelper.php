<?php

namespace App\Helpers;

use App\Models\SeoSetting;

/**
 * SEO Helper
 * 
 * SEO meta tag'lerini yönetmek için yardımcı sınıf
 */
class SeoHelper
{
    /**
     * Sayfa için SEO ayarlarını getir
     */
    public static function getForPage(string $pageType): ?SeoSetting
    {
        return SeoSetting::getForPage($pageType);
    }
    /**
     * Sayfa başlığı oluştur
     */
    public static function title(string $title, bool $withSiteName = true): string
    {
        $siteName = config('app.name', 'PUBG Mobile Topluluk');
        
        if ($withSiteName) {
            return $title . ' - ' . $siteName;
        }
        
        return $title;
    }

    /**
     * Meta description oluştur
     */
    public static function description(string $description, int $maxLength = 160): string
    {
        return \Str::limit($description, $maxLength);
    }

    /**
     * Keywords oluştur
     */
    public static function keywords(array $keywords): string
    {
        return implode(', ', $keywords);
    }

    /**
     * Canonical URL oluştur
     */
    public static function canonical(?string $url = null): string
    {
        return $url ?? url()->current();
    }

    /**
     * Open Graph image URL oluştur
     */
    public static function ogImage(?string $image = null): string
    {
        if ($image) {
            // Eğer tam URL değilse asset olarak döndür
            if (!filter_var($image, FILTER_VALIDATE_URL)) {
                return asset($image);
            }
            return $image;
        }
        
        return asset('images/og-default.jpg');
    }

    /**
     * Breadcrumb structured data oluştur
     */
    public static function breadcrumbStructuredData(array $items): string
    {
        $itemListElement = [];
        
        foreach ($items as $index => $item) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }
        
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];
        
        return json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Article structured data oluştur
     */
    public static function articleStructuredData(array $data): string
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? asset('images/og-default.jpg'),
            'datePublished' => $data['published_at'] ?? now()->toIso8601String(),
            'dateModified' => $data['updated_at'] ?? now()->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $data['author'] ?? 'PUBG Mobile Topluluk',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'PUBG Mobile Topluluk'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
        ];
        
        return json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Organization structured data oluştur
     */
    public static function organizationStructuredData(array $data): string
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'url' => $data['url'] ?? url('/'),
            'logo' => $data['logo'] ?? asset('images/logo.png'),
            'foundingDate' => $data['founding_date'] ?? null,
            'numberOfEmployees' => $data['member_count'] ?? null,
        ];
        
        return json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * FAQ structured data oluştur
     */
    public static function faqStructuredData(array $faqs): string
    {
        $mainEntity = [];
        
        foreach ($faqs as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }
        
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
        
        return json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Robots meta tag oluştur
     */
    public static function robots(bool $index = true, bool $follow = true, array $additional = []): string
    {
        $directives = [];
        
        $directives[] = $index ? 'index' : 'noindex';
        $directives[] = $follow ? 'follow' : 'nofollow';
        
        // Ek direktifler (noarchive, nosnippet, etc.)
        $directives = array_merge($directives, $additional);
        
        return implode(', ', $directives);
    }

    /**
     * Sitemap URL'lerini oluştur
     */
    public static function generateSitemapUrls(string $model, array $columns = ['slug', 'updated_at']): array
    {
        $modelClass = "App\\Models\\{$model}";
        
        if (!class_exists($modelClass)) {
            return [];
        }
        
        return $modelClass::select($columns)
            ->get()
            ->map(function ($item) {
                return [
                    'loc' => $item->url ?? url($item->slug),
                    'lastmod' => $item->updated_at->toIso8601String(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            })
            ->toArray();
    }
}

