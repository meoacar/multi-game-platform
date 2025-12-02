<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Başvuru Reddedildi Bildirimi
 */
class ApplicationRejected extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $type // 'lfg' veya 'clan'
    ) {}

    /**
     * Bildirim kanalları
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Veritabanı için bildirim verisi
     */
    public function toArray(object $notifiable): array
    {
        $message = $this->type === 'lfg' 
            ? "Başvurunuz reddedildi: {$this->title}"
            : "Klan başvurunuz reddedildi: {$this->title}";

        return [
            'type' => 'application_rejected',
            'title' => 'Başvuru Reddedildi',
            'message' => $message,
            'target_type' => $this->type,
            'target_title' => $this->title,
            'url' => $this->type === 'lfg' ? route('lfg.index') : route('clans.index'),
            'icon' => 'x-circle',
        ];
    }
}
