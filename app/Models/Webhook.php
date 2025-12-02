<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Webhook extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'url',
        'secret',
        'events',
        'headers',
        'timeout',
        'retry_count',
        'is_active',
        'last_triggered_at',
        'success_count',
        'failure_count',
    ];

    protected $casts = [
        'events' => 'array',
        'headers' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    /**
     * İlişki: Webhook sahibi
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * İlişki: Webhook logları
     */
    public function logs()
    {
        return $this->hasMany(WebhookLog::class);
    }

    /**
     * Webhook'u tetikle
     */
    public function trigger(string $event, array $payload): bool
    {
        // Webhook aktif mi ve bu olayı dinliyor mu?
        if (!$this->is_active || !in_array($event, $this->events)) {
            return false;
        }

        $startTime = microtime(true);
        $attempt = 1;
        $success = false;
        $statusCode = null;
        $response = null;
        $errorMessage = null;

        // Payload'a timestamp ve signature ekle
        $payload['timestamp'] = now()->toIso8601String();
        $payload['event'] = $event;
        
        // İmza oluştur (secret varsa)
        if ($this->secret) {
            $payload['signature'] = hash_hmac('sha256', json_encode($payload), $this->secret);
        }

        // Retry mekanizması ile gönder
        while ($attempt <= $this->retry_count && !$success) {
            try {
                $httpRequest = Http::timeout($this->timeout);
                
                // Özel header'lar varsa ekle
                if ($this->headers) {
                    $httpRequest = $httpRequest->withHeaders($this->headers);
                }
                
                $httpResponse = $httpRequest->post($this->url, $payload);
                
                $statusCode = $httpResponse->status();
                $response = $httpResponse->body();
                $success = $httpResponse->successful();
                
                if ($success) {
                    break;
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
            }
            
            $attempt++;
            
            // Başarısızsa biraz bekle
            if (!$success && $attempt <= $this->retry_count) {
                sleep(2 ** ($attempt - 1)); // Exponential backoff: 1s, 2s, 4s...
            }
        }

        $responseTime = (int) ((microtime(true) - $startTime) * 1000);

        // Log kaydet
        WebhookLog::create([
            'webhook_id' => $this->id,
            'event' => $event,
            'payload' => json_encode($payload),
            'status_code' => $statusCode,
            'response' => $response,
            'response_time' => $responseTime,
            'success' => $success,
            'error_message' => $errorMessage,
            'attempt' => $attempt - 1,
            'created_at' => now(),
        ]);

        // İstatistikleri güncelle
        if ($success) {
            $this->increment('success_count');
        } else {
            $this->increment('failure_count');
        }
        
        $this->update(['last_triggered_at' => now()]);

        return $success;
    }

    /**
     * Belirli bir olay için tüm webhook'ları tetikle
     */
    public static function triggerEvent(string $event, array $payload): void
    {
        $webhooks = self::where('is_active', true)
            ->whereJsonContains('events', $event)
            ->get();

        foreach ($webhooks as $webhook) {
            // Asenkron olarak tetikle (queue kullanarak)
            dispatch(function () use ($webhook, $event, $payload) {
                $webhook->trigger($event, $payload);
            })->afterResponse();
        }
    }
}
