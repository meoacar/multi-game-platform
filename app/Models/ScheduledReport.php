<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Zamanlanmış Rapor Modeli
 * 
 * Otomatik olarak oluşturulacak ve gönderilecek raporları yönetir
 */
class ScheduledReport extends Model
{
    protected $fillable = [
        'name',
        'type',
        'format',
        'frequency',
        'email',
        'recipients',
        'filters',
        'is_active',
        'last_sent_at',
        'next_send_at',
        'created_by',
    ];

    protected $casts = [
        'recipients' => 'array',
        'filters' => 'array',
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
        'next_send_at' => 'datetime',
    ];

    /**
     * Raporu oluşturan admin
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Sonraki gönderim zamanını hesapla
     */
    public function calculateNextSendTime(): void
    {
        $now = now();
        
        $this->next_send_at = match($this->frequency) {
            'daily' => $now->addDay()->setTime(9, 0), // Her gün saat 09:00
            'weekly' => $now->next('Monday')->setTime(9, 0), // Her Pazartesi 09:00
            'monthly' => $now->addMonth()->startOfMonth()->setTime(9, 0), // Her ayın 1'i 09:00
            default => $now->addDay(),
        };
        
        $this->save();
    }

    /**
     * Tüm alıcı email adreslerini getir
     */
    public function getAllRecipients(): array
    {
        $recipients = [$this->email];
        
        if ($this->recipients) {
            $recipients = array_merge($recipients, $this->recipients);
        }
        
        return array_unique($recipients);
    }
}
