<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

/**
 * Game Controller
 * 
 * Multi-game platform için oyun yönetimi API endpoint'leri
 * 
 * Endpoints:
 * - GET /api/v1/games - Oyun listesi
 * - GET /api/v1/games/{slug} - Oyun detayı (ID veya slug ile)
 * 
 * Requirements: 12.1, 12.2, 12.3, 12.4, 12.5
 */
class GameController extends Controller
{
    /**
     * Oyunları listele
     * GET /api/v1/games
     * 
     * Query Parameters:
     * - status: active|inactive (oyun durumu filtresi)
     * - is_active: 1|0 (backward compatibility için)
     * - with_stats: 1|0 (istatistiklerle birlikte getir)
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "PUBG Mobile",
     *       "slug": "pubg",
     *       "logo": "...",
     *       "description": "...",
     *       "status": "active",
     *       "settings": {...},
     *       "stats": {
     *         "tournaments_count": 10,
     *         "clans_count": 50,
     *         "lfg_posts_count": 100
     *       }
     *     }
     *   ],
     *   "meta": {
     *     "total": 1,
     *     "active_count": 1,
     *     "inactive_count": 0
     *   }
     * }
     * 
     * @validates Requirements 12.1, 12.4
     */
    public function index(Request $request)
    {
        $query = Game::query();

        // Status filtresi (yeni format)
        if ($request->has('status')) {
            $status = $request->input('status');
            if (in_array($status, ['active', 'inactive'])) {
                $query->where('status', $status);
            }
        }

        // is_active filtresi (backward compatibility)
        if ($request->has('is_active')) {
            $isActive = $request->boolean('is_active');
            if ($isActive) {
                $query->active();
            } else {
                $query->where('status', 'inactive');
            }
        }

        // İstatistiklerle birlikte getir
        if ($request->boolean('with_stats')) {
            $query->withCount([
                'tournaments',
                'clans',
                'lfgPosts',
                'badges',
                'guides',
                'communityPosts'
            ]);
        }

        $games = $query->ordered()->get();

        // İstatistikler varsa stats objesi oluştur
        if ($request->boolean('with_stats')) {
            $games = $games->map(function ($game) {
                $gameArray = $game->toArray();
                $gameArray['stats'] = [
                    'tournaments_count' => $game->tournaments_count ?? 0,
                    'clans_count' => $game->clans_count ?? 0,
                    'lfg_posts_count' => $game->lfg_posts_count ?? 0,
                    'badges_count' => $game->badges_count ?? 0,
                    'guides_count' => $game->guides_count ?? 0,
                    'community_posts_count' => $game->community_posts_count ?? 0,
                ];
                // Count alanlarını kaldır
                unset($gameArray['tournaments_count']);
                unset($gameArray['clans_count']);
                unset($gameArray['lfg_posts_count']);
                unset($gameArray['badges_count']);
                unset($gameArray['guides_count']);
                unset($gameArray['community_posts_count']);
                return $gameArray;
            });
        }

        // Meta bilgileri
        $meta = [
            'total' => $games->count(),
            'active_count' => Game::where('status', 'active')->count(),
            'inactive_count' => Game::where('status', 'inactive')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $games,
            'meta' => $meta,
        ]);
    }

    /**
     * Oyun detayını getir
     * GET /api/v1/games/{idOrSlug}
     * 
     * Parameters:
     * - idOrSlug: Oyun ID'si veya slug'ı (örn: 1 veya "pubg")
     * 
     * Query Parameters:
     * - with_stats: 1|0 (istatistiklerle birlikte getir)
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "PUBG Mobile",
     *     "slug": "pubg",
     *     "logo": "...",
     *     "description": "...",
     *     "status": "active",
     *     "settings": {
     *       "theme_color": "#FF6B00",
     *       "max_team_size": 4,
     *       "platforms": ["Android", "iOS"]
     *     },
     *     "stats": {
     *       "tournaments_count": 10,
     *       "clans_count": 50,
     *       "lfg_posts_count": 100
     *     }
     *   }
     * }
     * 
     * @validates Requirements 12.2, 12.4
     */
    public function show(Request $request, $idOrSlug)
    {
        // ID veya slug ile oyun bul
        if (is_numeric($idOrSlug)) {
            $query = Game::where('id', $idOrSlug);
        } else {
            $query = Game::bySlug($idOrSlug);
        }

        // İstatistiklerle birlikte getir
        if ($request->boolean('with_stats')) {
            $query->withCount([
                'tournaments',
                'clans',
                'lfgPosts',
                'badges',
                'guides',
                'communityPosts'
            ]);
        }

        $game = $query->firstOrFail();

        $gameData = $game->toArray();

        // İstatistikler varsa stats objesi oluştur
        if ($request->boolean('with_stats')) {
            $gameData['stats'] = [
                'tournaments_count' => $game->tournaments_count ?? 0,
                'clans_count' => $game->clans_count ?? 0,
                'lfg_posts_count' => $game->lfg_posts_count ?? 0,
                'badges_count' => $game->badges_count ?? 0,
                'guides_count' => $game->guides_count ?? 0,
                'community_posts_count' => $game->community_posts_count ?? 0,
            ];
            // Count alanlarını kaldır
            unset($gameData['tournaments_count']);
            unset($gameData['clans_count']);
            unset($gameData['lfg_posts_count']);
            unset($gameData['badges_count']);
            unset($gameData['guides_count']);
            unset($gameData['community_posts_count']);
        }

        return response()->json([
            'success' => true,
            'data' => $gameData,
        ]);
    }
}
