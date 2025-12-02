<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Toplu Bildirim Modeli
 * 
 * Admin panelinden kullanıcılara toplu bildirim göndermek için kullanılır.
 * Email, SMS, push notification veya site içi bildirim olarak gönderilebilir.
 * 
 * İlişkiler:
 * - belongsTo: User (creator - oluşturan admin)
 */
class BulkNotification extends Model
{
    /**
     * Mass assignment için izin verilen alanlar
     */
    protected $fillable = [
        'title',
        'message',
        'type',
        'target_type',
        'target_criteria',
        'user_ids',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'failed_count',
        'created_by',
    ];

    /**
     * Otomatik tip dönüşümleri
     */
    protected $casts = [
        'target_criteria' => 'array',
        'user_ids' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'total_recipients' => 'integer',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
    ];

    /**
     * Bildirimi oluşturan admin kullanıcı
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Bildirimin taslak durumunda olup olmadığını kontrol et
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Bildirimin zamanlanmış olup olmadığını kontrol et
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Bildirimin gönderilme aşamasında olup olmadığını kontrol et
     */
    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    /**
     * Bildirimin gönderilmiş olup olmadığını kontrol et
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Bildirimin başarısız olup olmadığını kontrol et
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Bildirimin tüm kullanıcılara gönderilip gönderilmediğini kontrol et
     */
    public function isTargetingAll(): bool
    {
        return $this->target_type === 'all';
    }

    /**
     * Bildirimin segment hedefli olup olmadığını kontrol et
     */
    public function isTargetingSegment(): bool
    {
        return $this->target_type === 'segment';
    }

    /**
     * Bildirimin özel kullanıcı listesine gönderilip gönderilmediğini kontrol et
     */
    public function isTargetingCustom(): bool
    {
        return $this->target_type === 'custom';
    }

    /**
     * Bildirimin email türünde olup olmadığını kontrol et
     */
    public function isEmail(): bool
    {
        return $this->type === 'email';
    }

    /**
     * Bildirimin SMS türünde olup olmadığını kontrol et
     */
    public function isSms(): bool
    {
        return $this->type === 'sms';
    }

    /**
     * Bildirimin push notification türünde olup olmadığını kontrol et
     */
    public function isPush(): bool
    {
        return $this->type === 'push';
    }

    /**
     * Bildirimin site içi bildirim türünde olup olmadığını kontrol et
     */
    public function isSite(): bool
    {
        return $this->type === 'site';
    }

    /**
     * Bildirimin gönderilmeye hazır olup olmadığını kontrol et
     */
    public function isReadyToSend(): bool
    {
        // Taslak veya zamanlanmış durumda olmalı
        if (!in_array($this->status, ['draft', 'scheduled'])) {
            return false;
        }

        // Zamanlanmış ise, zamanı gelmiş olmalı
        if ($this->isScheduled() && $this->scheduled_at && $this->scheduled_at->isFuture()) {
            return false;
        }

        // Başlık ve mesaj dolu olmalı
        if (empty($this->title) || empty($this->message)) {
            return false;
        }

        // Hedef kitle belirlenmiş olmalı
        if ($this->isTargetingCustom() && empty($this->user_ids)) {
            return false;
        }

        return true;
    }

    /**
     * Başarı oranını hesapla (yüzde olarak)
     */
    public function getSuccessRate(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }

        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }

    /**
     * Başarısızlık oranını hesapla (yüzde olarak)
     */
    public function getFailureRate(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }

        return round(($this->failed_count / $this->total_recipients) * 100, 2);
    }

    /**
     * Kalan alıcı sayısını hesapla
     */
    public function getRemainingRecipients(): int
    {
        return max(0, $this->total_recipients - $this->sent_count - $this->failed_count);
    }

    /**
     * Bildirimin tamamlanıp tamamlanmadığını kontrol et
     */
    public function isCompleted(): bool
    {
        return $this->getRemainingRecipients() === 0 && $this->total_recipients > 0;
    }

    /**
     * Gönderim sayacını artır
     */
    public function incrementSentCount(int $count = 1): void
    {
        $this->increment('sent_count', $count);
    }

    /**
     * Başarısız sayacını artır
     */
    public function incrementFailedCount(int $count = 1): void
    {
        $this->increment('failed_count', $count);
    }

    /**
     * Bildirimi gönderildi olarak işaretle
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Bildirimi başarısız olarak işaretle
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed',
        ]);
    }

    /**
     * Bildirimi gönderilme aşamasına al
     */
    public function markAsSending(): void
    {
        $this->update([
            'status' => 'sending',
        ]);
    }

    /**
     * Hedef kullanıcı ID'lerini al
     * 
     * @return array
     */
    public function getTargetUserIds(): array
    {
        if ($this->isTargetingCustom()) {
            return $this->user_ids ?? [];
        }

        if ($this->isTargetingAll()) {
            return User::where('status', 'active')->pluck('id')->toArray();
        }

        if ($this->isTargetingSegment()) {
            return $this->getUserIdsBySegment();
        }

        return [];
    }

    /**
     * Segment kriterlerine göre kullanıcı ID'lerini al
     * 
     * @return array
     */
    protected function getUserIdsBySegment(): array
    {
        if (empty($this->target_criteria)) {
            return [];
        }

        $query = User::query()->where('status', 'active');

        // Segment kriterlerini uygula
        foreach ($this->target_criteria as $key => $value) {
            switch ($key) {
                case 'user_type':
                    // Örnek: 'new', 'active', 'inactive'
                    if ($value === 'new') {
                        $query->where('created_at', '>=', now()->subDays(7));
                    } elseif ($value === 'active') {
                        $query->where('last_login_at', '>=', now()->subDays(30));
                    } elseif ($value === 'inactive') {
                        $query->where('last_login_at', '<', now()->subDays(30));
                    }
                    break;

                case 'xp_min':
                    $query->where('xp_total', '>=', $value);
                    break;

                case 'xp_max':
                    $query->where('xp_total', '<=', $value);
                    break;

                case 'email_verified':
                    if ($value) {
                        $query->whereNotNull('email_verified_at');
                    } else {
                        $query->whereNull('email_verified_at');
                    }
                    break;

                case 'has_profile':
                    if ($value) {
                        $query->has('profile');
                    } else {
                        $query->doesntHave('profile');
                    }
                    break;

                case 'created_after':
                    $query->where('created_at', '>=', $value);
                    break;

                case 'created_before':
                    $query->where('created_at', '<=', $value);
                    break;
            }
        }

        return $query->pluck('id')->toArray();
    }

    /**
     * Toplam alıcı sayısını hesapla ve güncelle
     */
    public function calculateTotalRecipients(): void
    {
        $userIds = $this->getTargetUserIds();
        $this->update([
            'total_recipients' => count($userIds),
        ]);
    }

    /**
     * Scope: Taslak bildirimleri getir
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope: Zamanlanmış bildirimleri getir
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope: Gönderilmiş bildirimleri getir
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope: Gönderilmeye hazır zamanlanmış bildirimleri getir
     */
    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now());
    }

    /**
     * Scope: Belirli bir türdeki bildirimleri getir
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Belirli bir admin tarafından oluşturulan bildirimleri getir
     */
    public function scopeCreatedBy($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }
}
