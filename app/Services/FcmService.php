<?php

namespace App\Services;

use App\Jobs\SendPushNotificationJob;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Firebase Cloud Messaging Service
 * 
 * Push notification gönderimi için yardımcı servis
 */
class FcmService
{
    /**
     * Tek kullanıcıya push notification gönder
     */
    public function sendToUser(
        User $user,
        string $title,
        string $body,
        array $data = [],
        ?string $image = null,
        ?string $clickAction = null
    ): bool {
        if (!$user->fcm_token) {
            Log::warning("FCM token yok", ['user_id' => $user->id]);
            return false;
        }

        // Job'ı kuyruğa ekle
        SendPushNotificationJob::dispatch($user, $title, $body, $data, $image, $clickAction);

        return true;
    }

    /**
     * Birden fazla kullanıcıya push notification gönder
     */
    public function sendToUsers(
        Collection $users,
        string $title,
        string $body,
        array $data = [],
        ?string $image = null,
        ?string $clickAction = null
    ): int {
        $sentCount = 0;

        foreach ($users as $user) {
            if ($this->sendToUser($user, $title, $body, $data, $image, $clickAction)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Kullanıcı ID'lerine göre push notification gönder
     */
    public function sendToUserIds(
        array $userIds,
        string $title,
        string $body,
        array $data = [],
        ?string $image = null,
        ?string $clickAction = null
    ): int {
        $users = User::whereIn('id', $userIds)
            ->whereNotNull('fcm_token')
            ->get();

        return $this->sendToUsers($users, $title, $body, $data, $image, $clickAction);
    }

    /**
     * Tüm kullanıcılara push notification gönder (dikkatli kullan!)
     */
    public function sendToAll(
        string $title,
        string $body,
        array $data = [],
        ?string $image = null,
        ?string $clickAction = null
    ): int {
        $users = User::whereNotNull('fcm_token')
            ->where('status', 'active')
            ->get();

        return $this->sendToUsers($users, $title, $body, $data, $image, $clickAction);
    }

    /**
     * Segment'e göre push notification gönder
     */
    public function sendToSegment(
        array $criteria,
        string $title,
        string $body,
        array $data = [],
        ?string $image = null,
        ?string $clickAction = null
    ): int {
        $query = User::whereNotNull('fcm_token')
            ->where('status', 'active');

        // Segment kriterlerini uygula
        foreach ($criteria as $key => $value) {
            switch ($key) {
                case 'user_type':
                    if ($value === 'new') {
                        $query->where('created_at', '>=', now()->subDays(7));
                    } elseif ($value === 'active') {
                        $query->where('last_login_at', '>=', now()->subDays(30));
                    } elseif ($value === 'inactive') {
                        $query->where('last_login_at', '<', now()->subDays(30));
                    }
                    break;

                case 'xp_min':
                    $query->where('xp_total', '>=', $value);
                    break;

                case 'xp_max':
                    $query->where('xp_total', '<=', $value);
                    break;

                case 'email_verified':
                    if ($value) {
                        $query->whereNotNull('email_verified_at');
                    } else {
                        $query->whereNull('email_verified_at');
                    }
                    break;

                case 'has_profile':
                    if ($value) {
                        $query->has('profile');
                    } else {
                        $query->doesntHave('profile');
                    }
                    break;

                case 'device_type':
                    $query->where('device_type', $value);
                    break;
            }
        }

        $users = $query->get();

        return $this->sendToUsers($users, $title, $body, $data, $image, $clickAction);
    }

    /**
     * Test push notification gönder
     */
    public function sendTestNotification(User $user): bool
    {
        return $this->sendToUser(
            $user,
            '🔔 Test Bildirimi',
            'Bu bir test bildirimidir. Push notification sistemi çalışıyor!',
            [
                'type' => 'test',
                'timestamp' => now()->toIso8601String(),
            ]
        );
    }
}
