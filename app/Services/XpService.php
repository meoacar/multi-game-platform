<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use App\Models\XpEvent;

/**
 * XP Service
 * XP kazanma ve level sistemi
 */
class XpService
{
    /**
     * XP kazanma türleri ve puanları
     */
    const XP_TYPES = [
        'profile_complete' => 50,      // Profil tamamlama
        'lfg_post_create' => 10,       // LFG ilanı oluşturma
        'clan_create' => 25,           // Klan oluşturma
        'guide_create' => 30,          // Rehber yazma
        'community_post_create' => 15, // Topluluk gönderisi
        'comment_create' => 5,         // Yorum yapma
        'device_add' => 20,            // Cihaz bilgisi ekleme
        'daily_login' => 5,            // Günlük giriş
        'lfg_application_accepted' => 15, // LFG başvurusu kabul edildi
        'clan_member_joined' => 10,    // Klana üye katıldı
        'created_squad' => 40,         // Takım oluşturma
        'created_tournament' => 100,   // Turnuva oluşturma
        'joined_tournament' => 30,     // Turnuvaya katılma
        'won_tournament' => 200,       // Turnuva kazanma
        'matchmaking_success' => 20,   // Matchmaking eşleşmesi tamamlandı
        'matchmaking_first_match' => 50, // İlk matchmaking eşleşmesi
    ];

    /**
     * Level hesaplama (her level için gereken XP artar)
     */
    public function calculateLevel(int $totalXp): int
    {
        // Level 1: 0 XP
        // Level 2: 100 XP
        // Level 3: 250 XP
        // Level 4: 450 XP
        // Her level için gereken XP = 100 + (level * 50)
        
        $level = 1;
        $requiredXp = 0;
        
        while ($totalXp >= $requiredXp) {
            $level++;
            $requiredXp += (100 + ($level * 50));
        }
        
        return $level - 1;
    }

    /**
     * Bir sonraki level için gereken XP
     */
    public function getXpForNextLevel(int $currentLevel): int
    {
        return 100 + (($currentLevel + 1) * 50);
    }

    /**
     * Mevcut level'deki ilerleme yüzdesi
     */
    public function getLevelProgress(int $totalXp, int $currentLevel): array
    {
        $currentLevelXp = $this->getTotalXpForLevel($currentLevel);
        $nextLevelXp = $this->getTotalXpForLevel($currentLevel + 1);
        
        $xpInCurrentLevel = $totalXp - $currentLevelXp;
        $xpNeededForLevel = $nextLevelXp - $currentLevelXp;
        
        $percentage = ($xpInCurrentLevel / $xpNeededForLevel) * 100;
        
        return [
            'current_xp' => $xpInCurrentLevel,
            'needed_xp' => $xpNeededForLevel,
            'percentage' => round($percentage, 2),
        ];
    }

    /**
     * Belirli bir level için gereken toplam XP
     */
    private function getTotalXpForLevel(int $level): int
    {
        $totalXp = 0;
        for ($i = 2; $i <= $level; $i++) {
            $totalXp += (100 + ($i * 50));
        }
        return $totalXp;
    }

    /**
     * Kullanıcıya XP ekle
     */
    public function addXp(User $user, string $type, ?array $meta = null): XpEvent
    {
        $points = self::XP_TYPES[$type] ?? 0;
        
        if ($points === 0) {
            throw new \InvalidArgumentException("Geçersiz XP türü: {$type}");
        }

        // XP event oluştur
        $xpEvent = $user->xpEvents()->create([
            'type' => $type,
            'points' => $points,
            'meta' => $meta,
        ]);

        // Kullanıcının toplam XP'sini güncelle
        $user->increment('xp_total', $points);
        $user->refresh();

        // Level kontrolü
        $oldLevel = $this->calculateLevel($user->xp_total - $points);
        $newLevel = $this->calculateLevel($user->xp_total);

        if ($newLevel > $oldLevel) {
            // Level atladı, badge kontrolü yap
            $this->checkAndUnlockBadges($user);
        }

        return $xpEvent;
    }

    /**
     * Badge kontrolü ve kilidi aç
     */
    public function checkAndUnlockBadges(User $user): array
    {
        $unlockedBadges = [];

        // Kullanıcının henüz kazanmadığı badge'leri al
        $availableBadges = Badge::whereNotIn('id', function($query) use ($user) {
            $query->select('badge_id')
                  ->from('user_badges')
                  ->where('user_id', $user->id);
        })->get();

        foreach ($availableBadges as $badge) {
            // XP gereksinimi kontrolü
            if ($badge->xp_required && $user->xp_total >= $badge->xp_required) {
                $user->badges()->attach($badge->id, [
                    'unlocked_at' => now(),
                ]);
                $unlockedBadges[] = $badge;
            }
        }

        return $unlockedBadges;
    }

    /**
     * Leaderboard - En yüksek XP'ye sahip kullanıcılar
     */
    public function getLeaderboard(int $limit = 10)
    {
        return User::with('profile')
            ->where('status', 'active')
            ->orderBy('xp_total', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($user) {
                return [
                    'user' => $user,
                    'level' => $this->calculateLevel($user->xp_total),
                    'xp_total' => $user->xp_total,
                ];
            });
    }

    /**
     * Kullanıcının XP geçmişi
     */
    public function getUserXpHistory(User $user, int $limit = 20)
    {
        return $user->xpEvents()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * XP türünün açıklaması
     */
    public function getXpTypeDescription(string $type): string
    {
        return match($type) {
            'profile_complete' => 'Profil tamamlandı',
            'lfg_post_create' => 'LFG ilanı oluşturuldu',
            'clan_create' => 'Klan oluşturuldu',
            'guide_create' => 'Rehber yazıldı',
            'community_post_create' => 'Topluluk gönderisi oluşturuldu',
            'comment_create' => 'Yorum yapıldı',
            'device_add' => 'Cihaz bilgisi eklendi',
            'daily_login' => 'Günlük giriş',
            'lfg_application_accepted' => 'LFG başvurusu kabul edildi',
            'clan_member_joined' => 'Klana üye katıldı',
            'matchmaking_success' => 'Matchmaking eşleşmesi tamamlandı',
            'matchmaking_first_match' => 'İlk matchmaking eşleşmesi',
            default => 'Bilinmeyen aktivite',
        };
    }
}
