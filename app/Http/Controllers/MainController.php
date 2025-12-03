<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Clan;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * MainController
 * 
 * Ana domain (takimsistemi.com) için controller
 * Landing page ve platform geneli özellikleri yönetir
 */
class MainController extends Controller
{
    protected GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Ana sayfa (Landing Page)
     * Tüm aktif oyunları listeler
     * 
     * @return View
     */
    public function index(): View
    {
        $games = $this->gameService->getActiveGames();
        $stats = $this->gameService->getPlatformStats();

        return view('main.home', compact('games', 'stats'));
    }

    /**
     * Platform istatistikleri (AJAX)
     * 
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        $stats = $this->gameService->getPlatformStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Öne çıkan turnuvalar (tüm oyunlardan)
     * 
     * @return JsonResponse
     */
    public function getFeaturedTournaments(): JsonResponse
    {
        // Global scope'u devre dışı bırakarak tüm oyunlardan turnuvaları al
        $tournaments = Tournament::withoutGlobalScope(\App\Models\Scopes\GameScope::class)
            ->with(['game', 'organizer'])
            ->where('is_featured', true)
            ->where('status', 'upcoming')
            ->orderBy('start_date', 'asc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tournaments,
        ]);
    }

    /**
     * Oyun değiştir ve subdomain'e yönlendir
     * 
     * @param string $slug
     * @return RedirectResponse
     */
    public function switchGame(string $slug): RedirectResponse
    {
        try {
            $gameUrl = $this->gameService->switchGame($slug);
            
            return redirect()->away($gameUrl)->with('success', 'Oyun değiştirildi');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Oyun bulunamadı: ' . $e->getMessage());
        }
    }
}
