<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

/**
 * Klan Başvurusu Alındı Bildirimi
 */
class ClanApplicationReceived extends Notification
{
    use Queueable;

    public function __construct(
        public User $applicant,
        public string $clanName,
        public string $clanSlug
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
            'type' => 'clan_application',
            'title' => 'Yeni Klan Başvurusu',
            'message' => "{$this->applicant->name} klanınıza başvurdu: {$this->clanName}",
            'applicant_id' => $this->applicant->id,
            'applicant_name' => $this->applicant->name,
            'clan_slug' => $this->clanSlug,
            'clan_name' => $this->clanName,
            'url' => route('clans.applications', $this->clanSlug),
            'icon' => 'users',
            'game_id' => session('game_id'), // Oyuna özel bildirim
        ];
    }
}
