<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Hoş Geldin Bildirimi
 */
class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct() {}

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
        return [
            'type' => 'welcome',
            'title' => 'Hoş Geldin!',
            'message' => 'PUBG Mobile Topluluk Platformuna hoş geldin! Profilini tamamlayarak başla.',
            'url' => route('profile.edit'),
            'icon' => 'heart',
        ];
    }
}
