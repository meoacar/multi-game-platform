<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\Request;

/**
 * Profile Controller
 * Kullanıcı profil yönetimi
 */
class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }
    /**
     * Profil bilgilerini güncelle
     * PUT /api/v1/me/profile
     */
    public function update(UpdateProfileRequest $request)
    {
        $profile = $request->user()->profile;
        
        // Policy kontrolü
        $this->authorize('update', $profile);

        $updatedProfile = $this->profileService->updateProfile(
            $profile,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Profil güncellendi',
            'data' => $updatedProfile,
        ]);
    }

    /**
     * Kullanıcı profilini görüntüle
     * GET /api/v1/users/{id}/profile
     */
    public function show($id)
    {
        $profile = $this->profileService->getUserProfile($id);

        return response()->json([
            'success' => true,
            'data' => $profile,
        ]);
    }
}
