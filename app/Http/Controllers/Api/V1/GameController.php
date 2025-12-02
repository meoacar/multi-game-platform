<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

/**
 * Game Controller
 * Oyun listesi yönetimi
 */
class GameController extends Controller
{
    /**
     * Aktif oyunları listele
     * GET /api/v1/games
     */
    public function index()
    {
        $games = Game::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $games,
        ]);
    }

    /**
     * Oyun detayını getir
     * GET /api/v1/games/{id}
     */
    public function show($id)
    {
        $game = Game::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $game,
        ]);
    }
}
