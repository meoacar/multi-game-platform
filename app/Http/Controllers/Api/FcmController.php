<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * FCM Token Controller
 * 
 * Mobil uygulamadan FCM token kaydetme ve yönetme
 */
class FcmController extends Controller
{
    protected FcmService $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * FCM token kaydet/güncelle
     * 
     * POST /api/v1/fcm/token
     */
    public function storeToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string|max:500',
            'device_type' => 'required|in:android,ios,web',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = auth()->user();

            // Token'ı kaydet
            $user->update([
                'fcm_token' => $request->fcm_token,
                'device_type' => $request->device_type,
                'fcm_token_updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token başarıyla kaydedildi',
                'data' => [
                    'fcm_token' => $user->fcm_token,
                    'device_type' => $user->device_type,
                    'updated_at' => $user->fcm_token_updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token kaydedilemedi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * FCM token sil
     * 
     * DELETE /api/v1/fcm/token
     */
    public function deleteToken(Request $request)
    {
        try {
            $user = auth()->user();

            // Token'ı temizle
            $user->update([
                'fcm_token' => null,
                'device_type' => null,
                'fcm_token_updated_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token başarıyla silindi',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token silinemedi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test push notification gönder
     * 
     * POST /api/v1/fcm/test
     */
    public function sendTest(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user->fcm_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'FCM token kayıtlı değil',
                ], 400);
            }

            // Test bildirimi gönder
            $sent = $this->fcmService->sendTestNotification($user);

            if ($sent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test bildirimi gönderildi',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Test bildirimi gönderilemedi',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * FCM token durumunu kontrol et
     * 
     * GET /api/v1/fcm/status
     */
    public function getStatus(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'data' => [
                'has_token' => !empty($user->fcm_token),
                'device_type' => $user->device_type,
                'updated_at' => $user->fcm_token_updated_at,
            ],
        ]);
    }
}
