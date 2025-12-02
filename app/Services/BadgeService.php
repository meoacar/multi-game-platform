<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BadgeService
{
    /**
     * Kullanıcının tüm rozetlerini kontrol et ve kilidi aç
     */
    public function checkAndUnlockAllBadges(User $user): array
    {
        $unlockedBadges = [];

        // Profil tamamlama rozeti
        if ($this->checkProfileCompletion($user)) {
            $unlockedBadges[] = 'first-step';
        }

        // İlk arkadaş rozeti
        if ($this->checkFirstFriend($user)) {
            $unlockedBadges[] = 'first-friend';
        }

        // Sosyal rozetler
        $friendCount = $user->friends()->count();
        if ($friendCount >= 50) {
            $unlockedBadges[] = 'social-butterfly';
        }
        if ($friendCount >= 100) {
            $unlockedBadges[] = 'popular-player';
        }

        // İçerik rozetleri
        if ($this->checkFirstLfg($user)) {
            $unlockedBadges[] = 'first-lfg';
        }
        if ($user->lfgPosts()->count() >= 25) {
            $unlockedBadges[] = 'active-poster';
        }

        if ($this->checkFirstClan($user)) {
            $unlockedBadges[] = 'clan-founder';
        }

        if ($this->checkFirstGuide($user)) {
            $unlockedBadges[] = 'guide-writer';
        }
        if ($user->guidePosts()->count() >= 10) {
            $unlockedBadges[] = 'productive-writer';
        }

        // Cihaz uzmanı
        if ($user->device()->exists()) {
            $unlockedBadges[] = 'device-expert';
        }

        // Takım kurucusu
        if ($user->squads()->where('leader_id', $user->id)->exists()) {
            $unlockedBadges[] = 'squad-founder';
        }

        // XP bazlı rozetler
        $xp = $user->xp_total ?? 0;
        if ($xp >= 300) {
            $unlockedBadges[] = 'active-player';
        }
        if ($xp >= 600) {
            $unlockedBadges[] = 'community-core';
        }
        if ($xp >= 1500) {
            $unlockedBadges[] = 'legendary-player';
        }

        // Mesaj rozetleri
        $messageCount = $user->sentMessages()->count();
        if ($messageCount >= 1000) {
            $unlockedBadges[] = 'message-machine';
        }

        // Yorum rozetleri
        $commentCount = $user->comments()->count();
        if ($commentCount >= 100) {
            $unlockedBadges[] = 'comment-master';
        }

        // Beğeni rozetleri
        $likesReceived = $this->getTotalLikesReceived($user);
        if ($likesReceived >= 500) {
            $unlockedBadges[] = 'like-king';
        }

        // Rank bazlı rozetler
        if ($user->profile && $user->profile->rank) {
            if (in_array($user->profile->rank, ['Ace', 'Ace Master', 'Ace Dominator'])) {
                $unlockedBadges[] = 'ace-player';
            }
            if ($user->profile->rank === 'Conqueror') {
                $unlockedBadges[] = 'conqueror-legend';
            }
        }

        // Rozetleri kilitle
        return $this->unlockBadges($user, $unlockedBadges);
    }

    /**
     * Belirli rozetleri kilitle
     */
    public function unlockBadges(User $user, array $badgeSlugs): array
    {
        $newlyUnlocked = [];

        foreach ($badgeSlugs as $slug) {
            $badge = Badge::where('slug', $slug)->where('is_active', true)->first();
            
            if (!$badge) {
                continue;
            }

            // Zaten açılmış mı kontrol et
            if ($user->badges()->where('badge_id', $badge->id)->exists()) {
                continue;
            }

            // Rozeti kilitle
            $user->badges()->attach($badge->id, [
                'unlocked_at' => now(),
                'progress' => 0,
                'progress_max' => null,
            ]);

            $newlyUnlocked[] = $badge;

            Log::info("Badge unlocked", [
                'user_id' => $user->id,
                'badge_slug' => $slug,
                'badge_name' => $badge->name,
            ]);
        }

        return $newlyUnlocked;
    }

    /**
     * Tek bir rozeti kilitle
     */
    public function unlockBadge(User $user, string $badgeSlug): ?Badge
    {
        $unlocked = $this->unlockBadges($user, [$badgeSlug]);
        return $unlocked[0] ?? null;
    }

    /**
     * Rozet ilerlemesini güncelle
     */
    public function updateBadgeProgress(User $user, string $badgeSlug, int $progress, ?int $progressMax = null): void
    {
        $badge = Badge::where('slug', $badgeSlug)->first();
        
        if (!$badge) {
            return;
        }

        $userBadge = $user->badges()->where('badge_id', $badge->id)->first();

        if ($userBadge) {
            // Zaten açılmış, sadece progress güncelle
            $user->badges()->updateExistingPivot($badge->id, [
                'progress' => $progress,
                'progress_max' => $progressMax,
            ]);
        } else {
            // Henüz açılmamış, progress ile ekle
            $user->badges()->attach($badge->id, [
                'unlocked_at' => null,
                'progress' => $progress,
                'progress_max' => $progressMax,
            ]);
        }

        // Progress tamamlandıysa kilidi aç
        if ($progressMax && $progress >= $progressMax) {
            $this->unlockBadge($user, $badgeSlug);
        }
    }

    /**
     * Profil tamamlama kontrolü
     */
    private function checkProfileCompletion(User $user): bool
    {
        if (!$user->profile) {
            return false;
        }

        $profile = $user->profile;
        return !empty($profile->nickname) 
            && !empty($profile->rank) 
            && !empty($profile->city);
    }

    /**
     * İlk arkadaş kontrolü
     */
    private function checkFirstFriend(User $user): bool
    {
        return $user->friends()->count() > 0;
    }

    /**
     * İlk LFG kontrolü
     */
    private function checkFirstLfg(User $user): bool
    {
        return $user->lfgPosts()->count() > 0;
    }

    /**
     * İlk klan kontrolü
     */
    private function checkFirstClan(User $user): bool
    {
        return $user->clans()->where('user_id', $user->id)->count() > 0;
    }

    /**
     * İlk rehber kontrolü
     */
    private function checkFirstGuide(User $user): bool
    {
        return $user->guidePosts()->count() > 0;
    }

    /**
     * Toplam alınan beğeni sayısı
     */
    private function getTotalLikesReceived(User $user): int
    {
        $guideLikes = DB::table('guide_likes')
            ->join('guide_posts', 'guide_likes.guide_post_id', '=', 'guide_posts.id')
            ->where('guide_posts.user_id', $user->id)
            ->count();

        $communityLikes = DB::table('community_post_likes')
            ->join('community_posts', 'community_post_likes.community_post_id', '=', 'community_posts.id')
            ->where('community_posts.user_id', $user->id)
            ->count();

        return $guideLikes + $communityLikes;
    }

    /**
     * Kullanıcının rozet istatistikleri
     */
    public function getUserBadgeStats(User $user): array
    {
        $totalBadges = Badge::active()->count();
        $unlockedBadges = $user->badges()->wherePivot('unlocked_at', '!=', null)->count();
        $inProgressBadges = $user->badges()->wherePivot('unlocked_at', null)->count();

        return [
            'total' => $totalBadges,
            'unlocked' => $unlockedBadges,
            'in_progress' => $inProgressBadges,
            'locked' => $totalBadges - $unlockedBadges - $inProgressBadges,
            'completion_percentage' => $totalBadges > 0 ? round(($unlockedBadges / $totalBadges) * 100, 2) : 0,
        ];
    }

    /**
     * Kategoriye göre rozetleri getir
     */
    public function getBadgesByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Badge::active()
            ->byCategory($category)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Nadirliğe göre rozetleri getir
     */
    public function getBadgesByRarity(string $rarity): \Illuminate\Database\Eloquent\Collection
    {
        return Badge::active()
            ->byRarity($rarity)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Kullanıcının açtığı rozetleri kategoriye göre getir
     */
    public function getUserBadgesByCategory(User $user, string $category): \Illuminate\Database\Eloquent\Collection
    {
        return $user->badges()
            ->wherePivot('unlocked_at', '!=', null)
            ->where('category', $category)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
