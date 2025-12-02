<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Matchmaking\JoinQueueRequest;
use App\Services\MatchmakingService;
use App\Models\MatchmakingMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * MatchmakingController
 * 
 * Matchmaking API endpoint'leri
 * Oyuncuları otomatik eşleştirme sistemi
 */
class MatchmakingController extends Controller
{
    protected MatchmakingService $matchmakingService;

    public function __construct(MatchmakingService $matchmakingService)
    {
        $this->matchmakingService = $matchmakingService;
    }

    /**
     * Kuyruğa katıl
     * POST /api/v1/matchmaking/join
     * 
     * @param JoinQueueRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function join(JoinQueueRequest $request)
    {
        try {
            $queue = $this->matchmakingService->joinQueue(
                $request->user(),
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Kuyruğa eklendi, eşleşme aranıyor...',
                'data' => [
                    'queue_id' => $queue->id,
                    'status' => $queue->status,
                    'mode' => $queue->mode,
                    'expires_at' => $queue->expires_at->toIso8601String(),
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Kuyruktan çık
     * POST /api/v1/matchmaking/leave
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function leave(Request $request)
    {
        try {
            $result = $this->matchmakingService->leaveQueue($request->user());

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuyrukta bekleyen bir kaydınız bulunamadı',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kuyruktan çıkıldı',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Kuyruk ve eşleşme durumunu kontrol et
     * GET /api/v1/matchmaking/status
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $user = $request->user();

        // Aktif kuyruk var mı?
        $activeQueue = $this->matchmakingService->getUserActiveQueue($user);

        // Bekleyen eşleşme var mı?
        $pendingMatches = $this->matchmakingService->getUserPendingMatches($user);

        $response = [
            'success' => true,
            'data' => [
                'in_queue' => $activeQueue !== null,
                'queue' => $activeQueue ? [
                    'id' => $activeQueue->id,
                    'status' => $activeQueue->status,
                    'mode' => $activeQueue->mode,
                    'game_id' => $activeQueue->game_id,
                    'created_at' => $activeQueue->created_at->toIso8601String(),
                    'expires_at' => $activeQueue->expires_at->toIso8601String(),
                    'search_attempts' => $activeQueue->search_attempts,
                ] : null,
                'pending_matches' => $pendingMatches->map(function ($match) use ($user) {
                    return [
                        'id' => $match->id,
                        'mode' => $match->mode,
                        'game_id' => $match->game_id,
                        'user_ids' => $match->user_ids,
                        'compatibility_score' => $match->compatibility_score,
                        'status' => $match->status,
                        'expires_at' => $match->expires_at->toIso8601String(),
                        'user_acceptance_status' => $match->getUserAcceptanceStatus($user->id),
                        'all_accepted' => $match->allUsersAccepted(),
                    ];
                }),
            ],
        ];

        return response()->json($response);
    }

    /**
     * Eşleşmeyi kabul et
     * POST /api/v1/matchmaking/matches/{id}/accept
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptMatch(Request $request, $id)
    {
        try {
            $match = MatchmakingMatch::findOrFail($id);

            $this->matchmakingService->acceptMatch($match, $request->user());

            // Eşleşmeyi yeniden yükle
            $match->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Eşleşme kabul edildi',
                'data' => [
                    'match_id' => $match->id,
                    'status' => $match->status,
                    'all_accepted' => $match->allUsersAccepted(),
                    'acceptance_status' => $match->acceptance_status,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Eşleşme bulunamadı',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Eşleşmeyi reddet
     * POST /api/v1/matchmaking/matches/{id}/reject
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectMatch(Request $request, $id)
    {
        try {
            $match = MatchmakingMatch::findOrFail($id);

            $this->matchmakingService->rejectMatch($match, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Eşleşme reddedildi',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Eşleşme bulunamadı',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Eşleşme geçmişi
     * GET /api/v1/matchmaking/history
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        $user = $request->user();

        // Geçmiş kayıtları getir
        $history = $this->matchmakingService->getUserHistory($user, 50);

        // İstatistikler
        $successRate = $this->matchmakingService->getUserSuccessRate($user);
        $avgWaitTime = $this->matchmakingService->getUserAverageWaitTime($user);

        return response()->json([
            'success' => true,
            'data' => [
                'history' => $history->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'match_id' => $record->match_id,
                        'result' => $record->result,
                        'wait_time_seconds' => $record->wait_time_seconds,
                        'compatibility_score' => $record->compatibility_score,
                        'matched_users' => $record->matched_users,
                        'created_at' => $record->created_at->toIso8601String(),
                    ];
                }),
                'statistics' => [
                    'success_rate' => $successRate,
                    'average_wait_time_seconds' => $avgWaitTime,
                    'total_matches' => $history->count(),
                ],
            ],
        ]);
    }
}
