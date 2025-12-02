<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LfgApplication;
use Illuminate\Http\Request;

/**
 * LFG Application Controller
 * İlan başvuruları yönetimi
 */
class LfgApplicationController extends Controller
{
    /**
     * Başvuruyu kabul et
     * PATCH /api/v1/lfg-applications/{id}/accept
     */
    public function accept(Request $request, $id)
    {
        $application = LfgApplication::with('lfgPost')->findOrFail($id);

        // Sadece ilan sahibi kabul edebilir
        if ($application->lfgPost->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu işlem için yetkiniz yok',
            ], 403);
        }

        $application->accept();

        return response()->json([
            'success' => true,
            'message' => 'Başvuru kabul edildi',
            'data' => $application->fresh('user.profile'),
        ]);
    }

    /**
     * Başvuruyu reddet
     * PATCH /api/v1/lfg-applications/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        $application = LfgApplication::with('lfgPost')->findOrFail($id);

        // Sadece ilan sahibi reddedebilir
        if ($application->lfgPost->user_id !== $request->user()->id) {
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
