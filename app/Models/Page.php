<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'template',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Kullanılabilir şablonlar
     */
    public static function getAvailableTemplates(): array
    {
        return [
            'default' => 'Varsayılan',
            'full-width' => 'Tam Genişlik',
            'sidebar-left' => 'Sol Kenar Çubuğu',
            'sidebar-right' => 'Sağ Kenar Çubuğu',
            'landing' => 'Landing Page',
        ];
    }

    /**
     * SEO başlığını al (meta_title varsa onu, yoksa title'ı kullan)
     */
    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: $this->title;
    }

    /**
     * Özet metni al (excerpt varsa onu, yoksa content'in ilk 160 karakterini kullan)
     */
    public function getExcerptTextAttribute(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }
        
        return \Illuminate\Support\Str::limit(strip_tags($this->content), 160);
    }
}
