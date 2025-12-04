<?php

use Illuminate\Support\Facades\Route;

// Debug route - sadece development için
if (app()->environment('local', 'development')) {
    Route::get('/debug-game', function () {
        return response()->json([
            'host' => request()->getHost(),
            'session_current_game' => session('current_game') ? session('current_game')->toArray() : null,
            'session_current_game_id' => session('current_game_id'),
            'config_current_game' => config('app.current_game') ? config('app.current_game')->toArray() : null,
            'view_currentGame' => isset($currentGame) ? $currentGame->toArray() : 'not set',
        ]);
    });
}
