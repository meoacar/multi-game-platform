<?php

namespace App\Notifications;

use App\Models\BulkNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Toplu Bildirim Alındı Notification
 * 
 * Kullanıcılara toplu bildirim gönderildiğinde tetiklenir
 */
class BulkNotificationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Toplu bildirim instance
     */
    protected $bulkNotification;

    /**
     * Create a new notification instance.
     */
    public function __construct(BulkNotification $bulkNotification)
    {
        $this->bulkNotification = $bulkNotification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Bildirim tipine göre kanalları belirle
        if ($this->bulkNotification->isEmail()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->bulkNotification->title)
            ->line($this->bulkNotification->message)
            ->action('Siteyi Ziyaret Et', url('/'))
            ->line('Teşekkürler!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'bulk_notification_id' => $this->bulkNotification->id,
            'title' => $this->bulkNotification->title,
            'message' => $this->bulkNotification->message,
            'type' => $this->bulkNotification->type,
        ];
    }
}
