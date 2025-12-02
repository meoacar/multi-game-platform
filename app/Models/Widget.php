<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Widget Model
 * 
 * Widget yönetimi için model
 * 
 * Özellikler:
 * - Farklı widget türleri (HTML, son içerikler, popüler, reklam)
 * - Farklı konumlar (sidebar, footer)
 * - Sıralama ve aktiflik durumu
 * - JSON ayarlar
 */
class Widget extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'location',
        'content',
        'settings',
        'order',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Kullanılabilir widget türleri
     */
    public static function getAvailableTypes(): array
    {
        return [
            'html' => 'HTML İçerik',
            'recent_content' => 'Son İçerikler',
            'popular' => 'Popüler İçerikler',
            'ad' => 'Reklam',
            'custom' => 'Özel Widget',
        ];
    }

    /**
     * Kullanılabilir konumlar
     */
    public static function getAvailableLocations(): array
    {
        return [
            'sidebar_left' => 'Sol Sidebar',
            'sidebar_right' => 'Sağ Sidebar',
            'footer_1' => 'Footer Kolon 1',
            'footer_2' => 'Footer Kolon 2',
            'footer_3' => 'Footer Kolon 3',
            'footer_4' => 'Footer Kolon 4',
        ];
    }

    /**
     * Belirli bir konumdaki aktif widget'ları getir
     */
    public static function getByLocation(string $location)
    {
        return static::where('location', $location)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Widget içeriğini render et
     */
    public function render(): string
    {
        switch ($this->type) {
            case 'html':
                return $this->content ?? '';
                
            case 'recent_content':
                return $this->renderRecentContent();
                
            case 'popular':
                return $this->renderPopularContent();
                
            case 'ad':
                return $this->renderAd();
                
            case 'custom':
                return $this->renderCustom();
                
            default:
                return '';
        }
    }

    /**
     * Son içerikleri render et
     */
    protected function renderRecentContent(): string
    {
        $limit = $this->settings['limit'] ?? 5;
        $contentType = $this->settings['content_type'] ?? 'all';
        
        // İçerik tipine göre sorgu
        $items = [];
        
        if ($contentType === 'all' || $contentType === 'guides') {
            $guides = \App\Models\GuidePost::where('is_published', true)
                ->latest()
                ->limit($limit)
                ->get();
            $items = array_merge($items, $guides->toArray());
        }
        
        if ($contentType === 'all' || $contentType === 'community') {
            $posts = \App\Models\CommunityPost::latest()
                ->limit($limit)
                ->get();
            $items = array_merge($items, $posts->toArray());
        }
        
        return view('widgets.recent-content', [
            'items' => collect($items)->take($limit),
            'title' => $this->title,
        ])->render();
    }

    /**
     * Popüler içerikleri render et
     */
    protected function renderPopularContent(): string
    {
        $limit = $this->settings['limit'] ?? 5;
        $contentType = $this->settings['content_type'] ?? 'all';
        
        // Popüler içerikleri getir (views_count'a göre)
        $items = [];
        
        if ($contentType === 'all' || $contentType === 'guides') {
            $guides = \App\Models\GuidePost::where('is_published', true)
                ->orderBy('views_count', 'desc')
                ->limit($limit)
                ->get();
            $items = array_merge($items, $guides->toArray());
        }
        
        return view('widgets.popular-content', [
            'items' => collect($items)->take($limit),
            'title' => $this->title,
        ])->render();
    }

    /**
     * Reklam widget'ını render et
     */
    protected function renderAd(): string
    {
        return view('widgets.ad', [
            'content' => $this->content,
            'title' => $this->title,
        ])->render();
    }

    /**
     * Özel widget'ı render et
     */
    protected function renderCustom(): string
    {
        $viewName = $this->settings['view'] ?? null;
        
        if ($viewName && view()->exists($viewName)) {
            return view($viewName, [
                'widget' => $this,
                'settings' => $this->settings,
            ])->render();
        }
        
        return $this->content ?? '';
    }

    /**
     * Scope: Aktif widget'lar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Konuma göre
     */
    public function scopeLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope: Sıralı
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
