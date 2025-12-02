<?php

namespace App\Policies;

use App\Models\GuidePost;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GuidePostPolicy
{
    /**
     * Herkes rehberleri görüntüleyebilir
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Herkes yayınlanmış rehberleri görüntüleyebilir
     */
    public function view(?User $user, GuidePost $guidePost): bool
    {
        if ($guidePost->is_published) {
            return true;
        }

        return $user && ($user->id === $guidePost->user_id || $user->is_admin);
    }

    /**
     * Giriş yapmış kullanıcılar rehber oluşturabilir
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Sadece rehber sahibi veya admin güncelleyebilir
     */
    public function update(User $user, GuidePost $guidePost): bool
    {
        return $user->id === $guidePost->user_id || $user->is_admin;
    }

    /**
     * Sadece rehber sahibi veya admin silebilir
     */
    public function delete(User $user, GuidePost $guidePost): bool
    {
        return $user->id === $guidePost->user_id || $user->is_admin;
    }

    /**
     * Sadece admin geri yükleyebilir
     */
    public function restore(User $user, GuidePost $guidePost): bool
    {
        return $user->is_admin;
    }

    /**
     * Sadece admin kalıcı olarak silebilir
     */
    public function forceDelete(User $user, GuidePost $guidePost): bool
    {
        return $user->is_admin;
    }
}
