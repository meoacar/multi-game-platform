<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Push Notification Gönderim Job'ı
 * 
 * Firebase Cloud Messaging (FCM) API kullanarak push notification gönderir
 * 
 * Kullanım:
 * SendPushNotificationJob::dispatch($user, 'Başlık', 'Mesaj', ['key' => 'value']);
 */
class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public string $title;
    public string $body;
    public array $data;
    public ?string $image;
    public ?string $clickAction;

    /**
     * Job deneme sayısı
     */
    public $tries = 3;

    /**
     * Job timeout süresi (saniye)
     */
    public $timeout = 30;

    /**
     * Backoff stratejisi (saniye)
     */
    public $backoff = [60, 300, 900]; // 1dk, 5dk, 15dk

    /**
     * Yeni job instance oluştur
     */
    public function __construct(
        User $user,
        string $title,
        string $body,
        array $data = [],
        ?string $image = null,
        ?string $clickAction = null
    ) {
        $this->user = $user;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->image = $image;
        $this->clickAction = $clickAction;
        
        // Queue ayarları
        $this->onQueue('notifications');
    }

    /**
     * Job'ı çalıştır
     */
    public function handle(): void
    {
        // FCM token yoksa işlem yapma
        if (!$this->user->fcm_token) {
            Log::warning("Push notification gönderilemedi: FCM token yok", [
                'user_id' => $this->user->id,
            ]);
            return;
        }

        // FCM Server Key kontrolü
        $fcmServerKey = config('services.fcm.server_key');
        if (!$fcmServerKey) {
            Log::error("FCM Server Key tanımlı değil!");
            return;
        }

        try {
            // FCM API endpoint
            $url = 'https://fcm.googleapis.com/fcm/send';

            // Notification payload hazırla
            $notification = [
                'title' => $this->title,
                'body' => $this->body,
                'sound' => 'default',
            ];

            // Görsel varsa ekle
            if ($this->image) {
                $notification['image'] = $this->image;
            }

            // Click action varsa ekle
            if ($this->clickAction) {
                $notification['click_action'] = $this->clickAction;
            }

            // FCM request body
            $payload = [
                'to' => $this->user->fcm_token,
                'notification' => $notification,
                'data' => array_merge($this->data, [
                    'user_id' => $this->user->id,
                    'created_at' => now()->toIso8601String(),
                ]),
                'priority' => 'high',
                'content_available' => true,
            ];

            // Android özel ayarları
            $payload['android'] = [
                'priority' => 'high',
                'notification' => [
                    'sound' => 'default',
                    'channel_id' => 'default',
                ],
            ];

            // iOS özel ayarları
            $payload['apns'] = [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                        'badge' => 1,
                        'content-available' => 1,
                    ],
                ],
            ];

            // FCM API'ye istek gönder
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $fcmServerKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            // Response kontrolü
            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['success']) && $result['success'] === 1) {
                    Log::info("Push notification başarıyla gönderildi", [
                        'user_id' => $this->user->id,
                        'title' => $this->title,
                    ]);
                } else {
                    // Token geçersiz olabilir
                    if (isset($result['results'][0]['error'])) {
                        $error = $result['results'][0]['error'];
                        
                        // Token geçersizse temizle
                        if (in_array($error, ['InvalidRegistration', 'NotRegistered'])) {
                            $this->user->update([
                                'fcm_token' => null,
                                'fcm_token_updated_at' => null,
                            ]);
                            
                            Log::warning("FCM token geçersiz, temizlendi", [
                                'user_id' => $this->user->id,
                                'error' => $error,
                            ]);
                        }
                    }
                }
            } else {
                Log::error("FCM API hatası", [
                    'user_id' => $this->user->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                // Job'ı tekrar kuyruğa ekle
                throw new \Exception("FCM API hatası: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Push notification gönderim hatası", [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            
            // Job'ı tekrar kuyruğa ekle
            throw $e;
        }
    }

    /**
     * Job başarısız olduğunda
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Push notification job başarısız", [
            'user_id' => $this->user->id,
            'title' => $this->title,
            'error' => $exception->getMessage(),
        ]);
    }
}
