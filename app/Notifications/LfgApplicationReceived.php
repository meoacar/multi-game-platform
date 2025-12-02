<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

/**
 * LFG Başvurusu Alındı Bildirimi
 */
class LfgApplicationReceived extends Notification
{
    use Queueable;

    public function __construct(
        public User $applicant,
        public string $postTitle,
        public int $postId
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
        return [
            'type' => 'lfg_application',
            'title' => 'Yeni LFG Başvurusu',
            'message' => "{$this->applicant->name} ilanınıza başvurdu: {$this->postTitle}",
            'applicant_id' => $this->applicant->id,
            'applicant_name' => $this->applicant->name,
            'post_id' => $this->postId,
            'post_title' => $this->postTitle,
            'url' => route('lfg.applications', $this->postId),
            'icon' => 'user-plus',
        ];
    }
}
