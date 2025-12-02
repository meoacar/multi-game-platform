<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BackupLog Model
 * 
 * Veritabanı ve dosya yedekleme işlemlerinin kaydını tutar
 * 
 * İlişkiler:
 * - belongsTo: User (created_by) - Yedeklemeyi oluşturan admin
 */
class BackupLog extends Model
{
    // Sadece created_at kullanılır, updated_at yok
    const UPDATED_AT = null;

    protected $fillable = [
        'type',
        'status',
        'file_path',
        'file_size',
        'started_at',
        'completed_at',
        'error_message',
        'created_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Yedekleme tipleri
     */
    const TYPE_DATABASE = 'database';
    const TYPE_FILES = 'files';
    const TYPE_FULL = 'full';

    /**
     * Yedekleme durumları
     */
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Yedeklemeyi oluşturan admin kullanıcı
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Yedekleme başlat
     */
    public function start(): void
    {
        $this->update([
            'status' => self::STATUS_RUNNING,
            'started_at' => now(),
        ]);
    }

    /**
     * Yedekleme tamamlandı
     */
    public function complete(string $filePath, int $fileSize): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'completed_at' => now(),
        ]);
    }

    /**
     * Yedekleme başarısız
     */
    public function fail(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    /**
     * Yedekleme süresi (saniye)
     */
    public function getDurationAttribute(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->completed_at->diffInSeconds($this->started_at);
    }

    /**
     * Dosya boyutu (okunabilir format)
     */
    public function getFileSizeHumanAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->file_size;
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Yedekleme tipi için Türkçe isim
     */
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            self::TYPE_DATABASE => 'Veritabanı',
            self::TYPE_FILES => 'Dosyalar',
            self::TYPE_FULL => 'Tam Yedek',
            default => $this->type,
        };
    }

    /**
     * Yedekleme durumu için Türkçe isim
     */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Bekliyor',
            self::STATUS_RUNNING => 'Çalışıyor',
            self::STATUS_COMPLETED => 'Tamamlandı',
            self::STATUS_FAILED => 'Başarısız',
            default => $this->status,
        };
    }

    /**
     * Durum için renk döndür (UI için)
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_RUNNING => 'blue',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_FAILED => 'red',
            default => 'gray',
        };
    }

    /**
     * Yedekleme başarılı mı?
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Yedekleme başarısız mı?
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Yedekleme çalışıyor mu?
     */
    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    /**
     * Yedekleme bekliyor mu?
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Scope: Türe göre filtrele
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Duruma göre filtrele
     */
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Tamamlanmış yedekler
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope: Başarısız yedekler
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope: Çalışan yedekler
     */
    public function scopeRunning($query)
    {
        return $query->where('status', self::STATUS_RUNNING);
    }

    /**
     * Scope: Bekleyen yedekler
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
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
     * Scope: Belirli bir kullanıcının yedekleri
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Yeni yedekleme kaydı oluştur
     */
    public static function createBackup(string $type, ?int $createdBy = null): self
    {
        return self::create([
            'type' => $type,
            'status' => self::STATUS_PENDING,
            'created_by' => $createdBy ?? auth()->id(),
        ]);
    }

    /**
     * Eski yedekleme loglarını temizle
     */
    public static function cleanup(int $days = 90): int
    {
        return self::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Toplam yedekleme boyutu (tamamlanmış yedekler)
     */
    public static function getTotalSize(): int
    {
        return self::completed()->sum('file_size') ?? 0;
    }

    /**
     * Toplam yedekleme boyutu (okunabilir format)
     */
    public static function getTotalSizeHuman(): string
    {
        $totalSize = self::getTotalSize();
        
        if ($totalSize === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $totalSize;
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Son başarılı yedekleme
     */
    public static function getLastSuccessful(?string $type = null): ?self
    {
        $query = self::completed()->latest('completed_at');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->first();
    }

    /**
     * Başarı oranı (yüzde)
     */
    public static function getSuccessRate(int $days = 30): float
    {
        $total = self::lastDays($days)->count();
        
        if ($total === 0) {
            return 0;
        }
        
        $successful = self::completed()->lastDays($days)->count();
        
        return round(($successful / $total) * 100, 2);
    }
}

