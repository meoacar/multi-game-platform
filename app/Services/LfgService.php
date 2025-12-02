<?php

namespace App\Services;

use App\Models\LfgPost;
use App\Models\LfgApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * LFG Service
 * Takım arama ilanları işlemleri
 */
class LfgService
{
    /**
     * Yeni ilan oluştur
     *
     * @param User $user
     * @param array $data
     * @return LfgPost
     */
    public function createPost(User $user, array $data): LfgPost
    {
        $post = $user->lfgPosts()->create($data);

        return $post->load(['user.profile', 'game']);
    }

    /**
     * İlan güncelle
     *
     * @param LfgPost $post
     * @param array $data
     * @return LfgPost
     */
    public function updatePost(LfgPost $post, array $data): LfgPost
    {
        $post->update($data);

        return $post->fresh(['user.profile', 'game']);
    }

    /**
     * İlan sil
     *
     * @param LfgPost $post
     * @return bool
     */
    public function deletePost(LfgPost $post): bool
    {
        return $post->delete();
    }

    /**
     * İlana başvur
     *
     * @param LfgPost $post
     * @param User $user
     * @param string|null $message
     * @return LfgApplication
     * @throws \Exception
     */
    public function applyToPost(LfgPost $post, User $user, ?string $message = null): LfgApplication
    {
        // Daha önce başvuru yapılmış mı kontrol et
        $existingApplication = $post->applications()
            ->where('user_id', $user->id)
            ->first();

        if ($existingApplication) {
            throw new \Exception('Bu ilana zaten başvurdunuz');
        }

        $application = $post->applications()->create([
            'user_id' => $user->id,
            'message' => $message,
        ]);

        return $application->load('user.profile');
    }

    /**
     * Başvuruyu kabul et
     *
     * @param LfgApplication $application
     * @return LfgApplication
     */
    public function acceptApplication(LfgApplication $application): LfgApplication
    {
        $application->update(['status' => 'accepted']);

        return $application->fresh();
    }

    /**
     * Başvuruyu reddet
     *
     * @param LfgApplication $application
     * @return LfgApplication
     */
    public function rejectApplication(LfgApplication $application): LfgApplication
    {
        $application->update(['status' => 'rejected']);

        return $application->fresh();
    }

    /**
     * İlanı kapat
     *
     * @param LfgPost $post
     * @return LfgPost
     */
    public function closePost(LfgPost $post): LfgPost
    {
        $post->update(['status' => 'closed']);

        return $post->fresh();
    }

    /**
     * İlanı yeniden aç
     *
     * @param LfgPost $post
     * @return LfgPost
     */
    public function reopenPost(LfgPost $post): LfgPost
    {
        $post->update(['status' => 'open']);

        return $post->fresh();
    }

    /**
     * Kullanıcının ilanlarını getir
     *
     * @param User $user
     * @return Collection
     */
    public function getUserPosts(User $user): Collection
    {
        return $user->lfgPosts()
            ->with(['game', 'applications'])
            ->latest()
            ->get();
    }

    /**
     * Kullanıcının başvurularını getir
     *
     * @param User $user
     * @return Collection
     */
    public function getUserApplications(User $user): Collection
    {
        return LfgApplication::where('user_id', $user->id)
            ->with(['post.user.profile', 'post.game'])
            ->latest()
            ->get();
    }

    /**
     * Süresi dolan ilanları kapat
     *
     * @return int
     */
    public function closeExpiredPosts(): int
    {
        return LfgPost::where('status', 'open')
            ->where('expires_at', '<', now())
            ->update(['status' => 'closed']);
    }
}
