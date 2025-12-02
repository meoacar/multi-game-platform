<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ClanApplication;
use Illuminate\Http\Request;

/**
 * Clan Application Controller
 * Klan başvuruları yönetimi
 */
class ClanApplicationController extends Controller
{
    /**
     * Başvuruyu kabul et
     * POST /api/v1/clan-applications/{id}/accept
     */
    public function accept(Request $request, $id)
    {
        $application = ClanApplication::with('clan')->findOrFail($id);

        // Sadece klan lideri kabul edebilir
        if ($application->clan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu işlem için yetkiniz yok',
            ], 403);
        }

        // Klan dolu mu?
        if ($application->clan->isFull()) {
            return response()->json([
                'success' => false,
                'message' => 'Klan dolu',
            ], 400);
        }

        $application->accept();

        return response()->json([
            'success' => true,
            'message' => 'Başvuru kabul edildi ve kullanıcı klana eklendi',
            'data' => $application->fresh('user.profile'),
        ]);
    }

    /**
     * Başvuruyu reddet
     * POST /api/v1/clan-applications/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        $application = ClanApplication::with('clan')->findOrFail($id);

        // Sadece klan lideri reddedebilir
        if ($application->clan->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu işlem için yetkiniz yok',
            ], 403);
        }

        $application->reject();

        return response()->json([
            'success' => true,
            'message' => 'Başvuru reddedildi',
            'data' => $application->fresh('user.profile'),
        ]);
    }
}
