<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User;

/**
 * Profile Service
 * Kullanıcı profil işlemleri
 */
class ProfileService
{
    /**
     * Profil güncelle
     *
     * @param Profile $profile
     * @param array $data
     * @return Profile
     */
    public function updateProfile(Profile $profile, array $data): Profile
    {
        $profile->update($data);

        // Profil tamamlanma durumunu kontrol et
        $profile->checkCompletion();

        return $profile->fresh();
    }

    /**
     * Kullanıcı profilini getir
     *
     * @param int $userId
     * @return Profile
     */
    public function getUserProfile(int $userId): Profile
    {
        $profile = Profile::where('user_id', $userId)
            ->with('user')
            ->firstOrFail();

        // Görüntülenme sayısını artır
        $profile->incrementViews();

        return $profile;
    }

    /**
     * Profil tamamlanma yüzdesini hesapla
     *
     * @param Profile $profile
     * @return int
     */
    public function calculateCompletionPercentage(Profile $profile): int
    {
        $fields = [
            'nickname',
            'pubg_id',
            'rank',
            'server_region',
            'city',
            'age_range',
            'gender',
            'play_style',
            'bio',
        ];

        $filledFields = 0;
        foreach ($fields as $field) {
            if (!empty($profile->$field)) {
                $filledFields++;
            }
        }

        return (int) (($filledFields / count($fields)) * 100);
    }

    /**
     * Popüler profilleri getir
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularProfiles(int $limit = 10)
    {
        return Profile::with('user')
            ->where('is_profile_complete', true)
            ->orderBy('profile_views', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Benzer profilleri bul
     *
     * @param Profile $profile
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findSimilarProfiles(Profile $profile, int $limit = 5)
    {
        $query = Profile::with('user')
            ->where('user_id', '!=', $profile->user_id)
            ->where('is_profile_complete', true);

        // Aynı sunucu bölgesi
        if ($profile->server_region) {
            $query->where('server_region', $profile->server_region);
        }

        // Aynı şehir
        if ($profile->city) {
            $query->where('city', $profile->city);
        }

        // Benzer oyun stili
        if ($profile->play_style) {
            $query->where('play_style', $profile->play_style);
        }

        return $query->limit($limit)->get();
    }
}
