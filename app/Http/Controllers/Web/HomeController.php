<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Ana Sayfa Controller
 */
class HomeController extends Controller
{
    /**
     * Ana sayfa
     * GET /
     */
    public function index(Request $request)
    {
        try {
            // Mevcut oyunu al
            $currentGame = $request->attributes->get('current_game');
            $gameSlug = $currentGame ? $currentGame->slug : 'pubg';
            
            // Cache ile istatistikleri 5 dakika boyunca sakla
            $stats = cache()->remember("home_stats_{$gameSlug}", 300, function () use ($currentGame) {
                $gameId = $currentGame ? $currentGame->id : null;
                
                return [
                    'total_users' => User::count(),
                    'active_users' => User::where('status', 'active')->count(),
                    'active_lfg' => \App\Models\LfgPost::where('status', 'open')
                        ->when($gameId, fn($q) => $q->where('game_id', $gameId))
                        ->count(),
                    'total_clans' => \App\Models\Clan::when($gameId, fn($q) => $q->where('game_id', $gameId))->count(),
                ];
            });

            // Son ilanları cache'le (1 dakika) - Oyuna özel
            $recentLfg = cache()->remember("home_recent_lfg_{$gameSlug}", 60, function () use ($currentGame) {
                $gameId = $currentGame ? $currentGame->id : null;
                
                return \App\Models\LfgPost::with(['user:id,name', 'game:id,name,slug'])
                    ->where('status', 'open')
                    ->when($gameId, fn($q) => $q->where('game_id', $gameId))
                    ->latest()
                    ->take(6)
                    ->get();
            });

            // Popüler klanları cache'le (1 dakika) - Oyuna özel
            $popularClans = cache()->remember("home_popular_clans_{$gameSlug}", 60, function () use ($currentGame) {
                $gameId = $currentGame ? $currentGame->id : null;
                
                return \App\Models\Clan::withCount('members')
                    ->when($gameId, fn($q) => $q->where('game_id', $gameId))
                    ->orderBy('members_count', 'desc')
                    ->take(6)
                    ->get();
            });

            // Oyuna özel view'ı seç
            $viewPath = "games.{$gameSlug}.home";
            
            // Eğer oyuna özel view yoksa, default home'u kullan
            if (!view()->exists($viewPath)) {
                $viewPath = 'home';
            }

            return view($viewPath, compact('stats', 'recentLfg', 'popularClans', 'currentGame'));
        } catch (\Exception $e) {
            \Log::error('HomeController error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response($e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine(), 500);
        }
    }
}
