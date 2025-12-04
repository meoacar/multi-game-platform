<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

/**
 * GameSelectionController
 * 
 * Ana sayfa (squadbul.com) için oyun seçim controller'ı
 */
class GameSelectionController extends Controller
{
    /**
     * Ana sayfa - Oyun seçimi
     */
    public function index()
    {
        // Aktif oyunları getir
        $games = Game::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        // Kullanıcı giriş yapmışsa, profili olan oyunları işaretle
        if (auth()->check()) {
            $userGameIds = auth()->user()->profiles()->pluck('game_id')->toArray();
            
            $games = $games->map(function ($game) use ($userGameIds) {
                $game->has_profile = in_array($game->id, $userGameIds);
                return $game;
            });
        }

        return view('game-selection.index', compact('games'));
    }

    /**
     * Oyun seç ve subdomain'e yönlendir
     */
    public function select(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Subdomain URL'ini oluştur
        $protocol = config('app.url_protocol', 'https');
        $domain = config('app.domain', 'squadbul.com');
        
        // Localhost kontrolü
        if (app()->environment('local')) {
            $url = config('app.url', 'http://localhost');
        } else {
            $url = "{$protocol}://{$game->subdomain}.{$domain}";
        }

        return redirect()->to($url);
    }
}
