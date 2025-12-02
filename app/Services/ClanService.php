<?php

namespace App\Services;

use App\Models\Clan;
use App\Models\ClanApplication;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

/**
 * Clan Service
 * Klan işlemleri
 */
class ClanService
{
    /**
     * Yeni klan oluştur
     *
     * @param User $user
     * @param array $data
     * @return Clan
     */
    public function createClan(User $user, array $data): Clan
    {
        // Slug oluştur
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        $clan = $user->ownedClans()->create($data);

        // Lideri otomatik üye yap
        $clan->members()->attach($user->id, [
            'role' => 'leader',
            'joined_at' => now(),
        ]);

        $clan->updateMemberCount();

        return $clan->load(['leader.profile', 'game']);
    }

    /**
     * Klan güncelle
     *
     * @param Clan $clan
     * @param array $data
     * @return Clan
     */
    public function updateClan(Clan $clan, array $data): Clan
    {
        $clan->update($data);

        return $clan->fresh(['leader.profile', 'game']);
    }

    /**
     * Klana başvur
     *
     * @param Clan $clan
     * @param User $user
     * @param string|null $message
     * @return ClanApplication
     * @throws \Exception
     */
    public function applyToClan(Clan $clan, User $user, ?string $message = null): ClanApplication
    {
        // Daha önce başvuru yapılmış mı?
        $existingApplication = $clan->applications()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingApplication) {
            throw new \Exception('Bu klana zaten başvurdunuz');
        }

        $application = $clan->applications()->create([
            'user_id' => $user->id,
            'message' => $message,
        ]);

        return $application->load('user.profile');
    }

    /**
     * Başvuruyu kabul et ve üye yap
     *
     * @param ClanApplication $application
     * @return ClanApplication
     */
    public function acceptApplication(ClanApplication $application): ClanApplication
    {
        $application->update(['status' => 'accepted']);

        // Kullanıcıyı klana ekle
        $application->clan->members()->attach($application->user_id, [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        // Üye sayısını güncelle
        $application->clan->updateMemberCount();

        return $application->fresh();
    }

    /**
     * Başvuruyu reddet
     *
     * @param ClanApplication $application
     * @return ClanApplication
     */
    public function rejectApplication(ClanApplication $application): ClanApplication
    {
        $application->update(['status' => 'rejected']);

        return $application->fresh();
    }

    /**
     * Üyeyi klandan çıkar
     *
     * @param Clan $clan
     * @param int $userId
     * @return bool
     */
    public function removeMember(Clan $clan, int $userId): bool
    {
        $clan->members()->detach($userId);
        $clan->updateMemberCount();

        return true;
    }

    /**
     * Üye rolünü güncelle
     *
     * @param Clan $clan
     * @param int $userId
     * @param string $role
     * @return bool
     */
    public function updateMemberRole(Clan $clan, int $userId, string $role): bool
    {
        $clan->members()->updateExistingPivot($userId, [
            'role' => $role,
        ]);

        return true;
    }

    /**
     * Klanı onayla (verified)
     *
     * @param Clan $clan
     * @return Clan
     */
    public function verifyClan(Clan $clan): Clan
    {
        $clan->update(['is_verified' => true]);

        return $clan->fresh();
    }

    /**
     * Klan onayını kaldır
     *
     * @param Clan $clan
     * @return Clan
     */
    public function unverifyClan(Clan $clan): Clan
    {
        $clan->update(['is_verified' => false]);

        return $clan->fresh();
    }

    /**
     * Kullanıcının klanlarını getir
     *
     * @param User $user
     * @return Collection
     */
    public function getUserClans(User $user): Collection
    {
        return $user->clans()
            ->with(['leader.profile', 'game'])
            ->get();
    }

    /**
     * Kullanıcının klan başvurularını getir
     *
     * @param User $user
     * @return Collection
     */
    public function getUserApplications(User $user): Collection
    {
        return ClanApplication::where('user_id', $user->id)
            ->with(['clan.leader.profile', 'clan.game'])
            ->latest()
            ->get();
    }

    /**
     * Benzersiz slug oluştur
     *
     * @param string $name
     * @return string
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Clan::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
