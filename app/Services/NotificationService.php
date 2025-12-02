<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\LfgApplicationReceived;
use App\Notifications\ClanApplicationReceived;
use App\Notifications\ApplicationAccepted;
use App\Notifications\ApplicationRejected;
use App\Notifications\WelcomeNotification;

/**
 * Notification Service
 * Bildirim gönderme işlemleri
 */
class NotificationService
{
    /**
     * LFG başvurusu bildirimi gönder
     *
     * @param User $postOwner
     * @param User $applicant
     * @param string $postTitle
     * @param int $postId
     * @return void
     */
    public function sendLfgApplicationNotification(User $postOwner, User $applicant, string $postTitle, int $postId): void
    {
        $postOwner->notify(new LfgApplicationReceived($applicant, $postTitle, $postId));
    }

    /**
     * Klan başvurusu bildirimi gönder
     *
     * @param User $clanLeader
     * @param User $applicant
     * @param string $clanName
     * @param string $clanSlug
     * @return void
     */
    public function sendClanApplicationNotification(User $clanLeader, User $applicant, string $clanName, string $clanSlug): void
    {
        $clanLeader->notify(new ClanApplicationReceived($applicant, $clanName, $clanSlug));
    }

    /**
     * Başvuru kabul bildirimi gönder
     *
     * @param User $applicant
     * @param string $title
     * @param string $type (lfg veya clan)
     * @param int $targetId
     * @return void
     */
    public function sendApplicationAcceptedNotification(User $applicant, string $title, string $type, int $targetId): void
    {
        $applicant->notify(new ApplicationAccepted($title, $type, $targetId));
    }

    /**
     * Başvuru red bildirimi gönder
     *
     * @param User $applicant
     * @param string $title
     * @param string $type (lfg veya clan)
     * @return void
     */
    public function sendApplicationRejectedNotification(User $applicant, string $title, string $type): void
    {
        $applicant->notify(new ApplicationRejected($title, $type));
    }

    /**
     * Hoş geldin mesajı gönder
     *
     * @param User $user
     * @return void
     */
    public function sendWelcomeNotification(User $user): void
    {
        $user->notify(new WelcomeNotification());
    }

    /**
     * Profil tamamlama hatırlatması gönder
     *
     * @param User $user
     * @return void
     */
    public function sendProfileCompletionReminder(User $user): void
    {
        // Profil tamamlanmamışsa bildirim gönder
        if (!$user->profile || !$user->profile->is_profile_completed) {
            // Bu özellik ileride eklenebilir
        }
    }
}
