<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * EmailTemplate Model
 * 
 * Email şablonlarını yönetir. Sistem genelinde kullanılacak email şablonlarını
 * veritabanında saklar ve değişken desteği sağlar.
 * 
 * @property int $id
 * @property string $name Şablon adı (örn: "Hoş Geldin Emaili")
 * @property string $slug Şablon slug'ı (örn: "welcome-email")
 * @property string $subject Email konusu
 * @property string $body Email içeriği (HTML destekli)
 * @property array|null $variables Kullanılabilir değişkenler (JSON)
 * @property bool $is_active Şablon aktif mi?
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class EmailTemplate extends Model
{
    use HasFactory;

    /**
     * Veritabanı tablosu
     */
    protected $table = 'email_templates';

    /**
     * Toplu atanabilir alanlar
     */
    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'variables',
        'is_active',
    ];

    /**
     * Tip dönüşümleri
     */
    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Model event'leri
     */
    protected static function boot()
    {
        parent::boot();

        // Slug otomatik oluşturma
        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });

        // Slug güncelleme
        static::updating(function ($template) {
            if ($template->isDirty('name') && empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }

    /**
     * Şablonu slug ile bul
     * 
     * @param string $slug
     * @return EmailTemplate|null
     */
    public static function findBySlug(string $slug): ?EmailTemplate
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Aktif şablonları getir
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActive()
    {
        return static::where('is_active', true)->get();
    }

    /**
     * Şablonu değişkenlerle render et
     * 
     * @param array $data Değişken değerleri
     * @return array ['subject' => string, 'body' => string]
     */
    public function render(array $data = []): array
    {
        $subject = $this->subject;
        $body = $this->body;

        // Değişkenleri değiştir ({{variable}} formatında)
        foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $subject = str_replace($placeholder, $value, $subject);
            $body = str_replace($placeholder, $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Şablonda kullanılabilir değişkenleri getir
     * 
     * @return array
     */
    public function getAvailableVariables(): array
    {
        return $this->variables ?? [];
    }

    /**
     * Şablonun aktif olup olmadığını kontrol et
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Şablonu aktif et
     * 
     * @return bool
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Şablonu pasif et
     * 
     * @return bool
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Şablon önizlemesi oluştur
     * 
     * @param array $sampleData Örnek veri
     * @return array
     */
    public function preview(array $sampleData = []): array
    {
        // Eğer örnek veri verilmemişse, değişkenler için varsayılan değerler kullan
        if (empty($sampleData) && !empty($this->variables)) {
            foreach ($this->variables as $variable) {
                $sampleData[$variable] = '[' . $variable . ']';
            }
        }

        return $this->render($sampleData);
    }

    /**
     * Şablonu klonla
     * 
     * @param string|null $newName Yeni şablon adı
     * @return EmailTemplate
     */
    public function duplicate(?string $newName = null): EmailTemplate
    {
        $newTemplate = $this->replicate();
        $newTemplate->name = $newName ?? $this->name . ' (Kopya)';
        $newTemplate->slug = Str::slug($newTemplate->name);
        $newTemplate->is_active = false; // Kopyalar varsayılan olarak pasif
        $newTemplate->save();

        return $newTemplate;
    }

    /**
     * Scope: Sadece aktif şablonlar
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Slug'a göre filtrele
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
