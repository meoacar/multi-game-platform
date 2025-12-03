<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Matchmaking\JoinQueueRequest;
use App\Services\MatchmakingService;
use App\Models\Game;
use App\Models\MatchmakingMatch;
use Illuminate\Http\Request;

/**
 * Web Matchmaking Controller
 * Otomatik eşleşme sistemi (Web arayüzü)
 */
class MatchmakingController extends Controller
{
    protected MatchmakingService $matchmakingService;

    public function __construct(MatchmakingService $matchmakingService)
    {
        $this->middleware('auth'); // Tüm metodlar için auth zorunlu
        $this->matchmakingService = $matchmakingService;
    }

    /**
     * Ana sayfa - Eşleşme tercihleri ve başlat butonu
     * GET /eslesme
     * 
     * Requirements: 1.1, 2.1
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Aktif oyunları getir
        $games = Game::active()->ordered()->get();

        // Kullanıcının aktif kuyruğu var mı?
        $activeQueue = $this->matchmakingService->getUserActiveQueue($user);

        // Bekleyen eşleşmeler var mı?
        $pendingMatches = $this->matchmakingService->getUserPendingMatches($user);

        // Kullanıcının tercihlerini getir (varsa)
        $preferences = $user->matchmakingPreference;

        // Şehir listesi (profil verilerinden)
        $cities = \App\Models\Profile::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city');

        return view('matchmaking.index', compact(
            'games',
            'activeQueue',
            'pendingMatches',
            'preferences',
            'cities'
        ));
    }

    /**
     * Eşleşme başlat - Kuyruğa katıl
     * POST /eslesme/baslat
     * 
     * Requirements: 1.1, 2.1, 2.2, 2.3, 2.4, 2.5
     */
    public function start(JoinQueueRequest $request)
    {
        try {
            $queue = $this->matchmakingService->joinQueue(
                $request->user(),
                $request->validated()
            );

            return redirect()->route('matchmaking.index')
                ->with('success', 'Eşleşme aranıyor! Uygun oyuncular bulunduğunda bildirim alacaksınız.')
                ->with('queue_started', true);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Eşleşmeyi iptal et - Kuyruktan çık
     * POST /eslesme/iptal
     * 
     * Requirements: 1.1
     */
    public function cancel(Request $request)
    {
        try {
            $result = $this->matchmakingService->leaveQueue($request->user());

            if (!$result) {
                return back()->with('error', 'Kuyrukta bekleyen bir kaydınız bulunamadı');
            }

            return redirect()->route('matchmaking.index')
                ->with('success', 'Eşleşme araması iptal edildi');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Eşleşme geçmişi
     * GET /eslesme/gecmis
     * 
     * Requirements: 5.1
     */
    public function history(Request $request)
    {
        $user = $request->user();

        // Geçmiş kayıtları getir (sayfalama ile)
        $history = $this->matchmakingService->getUserHistory($user, 50);

        // İstatistikler
        $successRate = $this->matchmakingService->getUserSuccessRate($user);
        $avgWaitTime = $this->matchmakingService->getUserAverageWaitTime($user);

        return view('matchmaking.history', compact(
            'history',
            'successRate',
            'avgWaitTime'
        ));
    }

    /**
     * Eşleşmeyi kabul et (AJAX için)
     * POST /eslesme/eslesme/{id}/kabul
     * 
     * Requirements: 4.2
     */
    public function acceptMatch(Request $request, $id)
    {
        try {
            $match = MatchmakingMatch::findOrFail($id);

            $this->matchmakingService->acceptMatch($match, $request->user());

            // AJAX isteği ise JSON döndür
            if ($request->expectsJson()) {
                $match->refresh();
                return response()->json([
                    'success' => true,
                    'message' => 'Eşleşme kabul edildi',
                    'all_accepted' => $match->allUsersAccepted(),
                ]);
            }

            return back()->with('success', 'Eşleşme kabul edildi!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Eşleşmeyi reddet (AJAX için)
     * POST /eslesme/eslesme/{id}/reddet
     * 
     * Requirements: 4.3
     */
    public function rejectMatch(Request $request, $id)
    {
        try {
            $match = MatchmakingMatch::findOrFail($id);

            $this->matchmakingService->rejectMatch($match, $request->user());

            // AJAX isteği ise JSON döndür
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Eşleşme reddedildi',
                ]);
            }

            return back()->with('success', 'Eşleşme reddedildi');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Durum kontrolü (AJAX polling için)
     * GET /eslesme/durum
     * 
     * Requirements: 1.2, 1.3
     */
    public function status(Request $request)
    {
        $user = $request->user();

        // Aktif kuyruk var mı?
        $activeQueue = $this->matchmakingService->getUserActiveQueue($user);

        // Bekleyen eşleşme var mı?
        $pendingMatches = $this->matchmakingService->getUserPendingMatches($user);

        return response()->json([
            'success' => true,
            'in_queue' => $activeQueue !== null,
            'queue' => $activeQueue ? [
                'id' => $activeQueue->id,
                'status' => $activeQueue->status,
                'mode' => $activeQueue->mode,
                'game_id' => $activeQueue->game_id,
                'search_attempts' => $activeQueue->search_attempts,
                'expires_at' => $activeQueue->expires_at->toIso8601String(),
                'elapsed_seconds' => now()->diffInSeconds($activeQueue->created_at),
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
                    'remaining_seconds' => max(0, $match->expires_at->diffInSeconds(now())),
                ];
            })->values(),
        ]);
    }
}
