<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SystemLog Model
 * 
 * Sistem loglarını saklar (hata, uyarı, bilgi, güvenlik, performans)
 * 
 * İlişkiler:
 * - Yok (bağımsız log tablosu)
 */
class SystemLog extends Model
{
    // Sadece created_at kullanılır, updated_at yok
    const UPDATED_AT = null;

    protected $fillable = [
        'type',
        'level',
        'message',
        'context',
        'stack_trace',
        'ip_address',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Log türleri
     */
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';
    const TYPE_SECURITY = 'security';
    const TYPE_PERFORMANCE = 'performance';

    /**
     * Log seviyeleri
     */
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_NOTICE = 'notice';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_CRITICAL = 'critical';
    const LEVEL_ALERT = 'alert';
    const LEVEL_EMERGENCY = 'emergency';

    /**
     * Hata logu oluştur
     */
    public static function logError(string $message, array $context = [], ?string $stackTrace = null): void
    {
        self::createLog(self::TYPE_ERROR, self::LEVEL_ERROR, $message, $context, $stackTrace);
    }

    /**
     * Uyarı logu oluştur
     */
    public static function logWarning(string $message, array $context = []): void
    {
        self::createLog(self::TYPE_WARNING, self::LEVEL_WARNING, $message, $context);
    }

    /**
     * Bilgi logu oluştur
     */
    public static function logInfo(string $message, array $context = []): void
    {
        self::createLog(self::TYPE_INFO, self::LEVEL_INFO, $message, $context);
    }

    /**
     * Güvenlik logu oluştur
     */
    public static function logSecurity(string $message, array $context = []): void
    {
        self::createLog(self::TYPE_SECURITY, self::LEVEL_WARNING, $message, $context);
    }

    /**
     * Performans logu oluştur
     */
    public static function logPerformance(string $message, array $context = []): void
    {
        self::createLog(self::TYPE_PERFORMANCE, self::LEVEL_INFO, $message, $context);
    }

    /**
     * Genel log oluşturma metodu
     */
    public static function createLog(
        string $type,
        string $level,
        string $message,
        array $context = [],
        ?string $stackTrace = null
    ): void {
        self::create([
            'type' => $type,
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'stack_trace' => $stackTrace,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    /**
     * Kritik hata logu oluştur
     */
    public static function logCritical(string $message, array $context = [], ?string $stackTrace = null): void
    {
        self::create([
            'type' => self::TYPE_ERROR,
            'level' => self::LEVEL_CRITICAL,
            'message' => $message,
            'context' => $context,
            'stack_trace' => $stackTrace,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    /**
     * Log türü için renk döndür (UI için)
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            self::TYPE_ERROR => 'red',
            self::TYPE_WARNING => 'yellow',
            self::TYPE_INFO => 'blue',
            self::TYPE_SECURITY => 'purple',
            self::TYPE_PERFORMANCE => 'green',
            default => 'gray',
        };
    }

    /**
     * Log seviyesi için renk döndür (UI için)
     */
    public function getLevelColorAttribute(): string
    {
        return match($this->level) {
            self::LEVEL_EMERGENCY, self::LEVEL_ALERT, self::LEVEL_CRITICAL => 'red',
            self::LEVEL_ERROR => 'orange',
            self::LEVEL_WARNING => 'yellow',
            self::LEVEL_NOTICE => 'blue',
            self::LEVEL_INFO, self::LEVEL_DEBUG => 'gray',
            default => 'gray',
        };
    }

    /**
     * Log türü için Türkçe isim döndür
     */
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            self::TYPE_ERROR => 'Hata',
            self::TYPE_WARNING => 'Uyarı',
            self::TYPE_INFO => 'Bilgi',
            self::TYPE_SECURITY => 'Güvenlik',
            self::TYPE_PERFORMANCE => 'Performans',
            default => $this->type,
        };
    }

    /**
     * Log seviyesi için Türkçe isim döndür
     */
    public function getLevelNameAttribute(): string
    {
        return match($this->level) {
            self::LEVEL_EMERGENCY => 'Acil',
            self::LEVEL_ALERT => 'Alarm',
            self::LEVEL_CRITICAL => 'Kritik',
            self::LEVEL_ERROR => 'Hata',
            self::LEVEL_WARNING => 'Uyarı',
            self::LEVEL_NOTICE => 'Bildirim',
            self::LEVEL_INFO => 'Bilgi',
            self::LEVEL_DEBUG => 'Debug',
            default => $this->level,
        };
    }

    /**
     * Scope: Türe göre filtrele
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Seviyeye göre filtrele
     */
    public function scopeOfLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope: Tarih aralığına göre filtrele
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Son X gün
     */
    public function scopeLastDays($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: Kritik loglar (critical, alert, emergency)
     */
    public function scopeCritical($query)
    {
        return $query->whereIn('level', [
            self::LEVEL_CRITICAL,
            self::LEVEL_ALERT,
            self::LEVEL_EMERGENCY
        ]);
    }

    /**
     * Scope: Güvenlik logları
     */
    public function scopeSecurity($query)
    {
        return $query->where('type', self::TYPE_SECURITY);
    }

    /**
     * Eski logları temizle
     */
    public static function cleanup(int $days = 30): int
    {
        return self::where('created_at', '<', now()->subDays($days))->delete();
    }
}
