<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Push Notification
 * 
 * Firebase Cloud Messaging (FCM) ile push notification gönderimi
 * 
 * Kullanım:
 * $user->notify(new PushNotification('Başlık', 'Mesaj', ['key' => 'value']));
 */
class PushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $title;
    public string $body;
    public array $data;
    public ?string $image;
    public ?string $clickAction;

    /**
     * Yeni notification instance oluştur
     */
    public function __construct(
        string $title,
        string $body,
        array $data = [],
        ?string $image = null,
        ?string $clickAction = null
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->image = $image;
        $this->clickAction = $clickAction;
        
        // Queue ayarları
        $this->onQueue('notifications');
    }

    /**
     * Notification kanallarını belirle
     */
    public function via($notifiable): array
    {
        // FCM channel kullanacağız (manuel implementasyon)
        return ['fcm'];
    }

    /**
     * FCM için notification data'sını hazırla
     */
    public function toFcm($notifiable): array
    {
        $payload = [
            'notification' => [
                'title' => $this->title,
                'body' => $this->body,
            ],
            'data' => array_merge($this->data, [
                'created_at' => now()->toIso8601String(),
            ]),
        ];

        // Görsel varsa ekle
        if ($this->image) {
            $payload['notification']['image'] = $this->image;
        }

        // Click action varsa ekle
        if ($this->clickAction) {
            $payload['notification']['click_action'] = $this->clickAction;
        }

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
                ],
            ],
        ];

        return $payload;
    }

    /**
     * Database notification için data
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
            'image' => $this->image,
            'click_action' => $this->clickAction,
        ];
    }

    /**
     * Array representation
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
