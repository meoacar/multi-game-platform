<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Başvuru Kabul Edildi Bildirimi
 */
class ApplicationAccepted extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $type, // 'lfg' veya 'clan'
        public int $targetId
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
            ? "Başvurunuz kabul edildi: {$this->title}"
            : "Klan başvurunuz kabul edildi: {$this->title}";

        $url = $this->type === 'lfg'
            ? route('lfg.show', $this->targetId)
            : route('clans.show', $this->targetId);

        return [
            'type' => 'application_accepted',
            'title' => 'Başvuru Kabul Edildi',
            'message' => $message,
            'target_type' => $this->type,
            'target_id' => $this->targetId,
            'target_title' => $this->title,
            'url' => $url,
            'icon' => 'check-circle',
            'game_id' => session('game_id'), // Oyuna özel bildirim
        ];
    }
}
