<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Ayar değerini getir
     * 
     * @param string $key Ayar anahtarı (örn: 'auth.registration_enabled')
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

/**
 * Oyuna özel içerik al
 */
if (!function_exists('game_content')) {
    function game_content($key, $default = null)
    {
        $game = session('game');
        if (!$game) {
            return $default;
        }
        
        $content = config('game-content.' . $game->slug, []);
        return data_get($content, $key, $default);
    }
}

/**
 * Oyuna özel asset yolu
 */
if (!function_exists('game_asset')) {
    function game_asset($path)
    {
        $game = session('game');
        if (!$game) {
            return asset($path);
        }
        
        return asset('images/games/' . $game->slug . '/' . $path);
    }
}

/**
 * Oyuna özel view seç
 */
if (!function_exists('game_view')) {
    function game_view($view, $data = [])
    {
        $game = session('game');
        
        if ($game) {
            // Oyuna özel view'ı dene
            $gameView = str_replace('.', '.games.' . $game->slug . '.', $view);
            if (view()->exists($gameView)) {
                return view($gameView, $data);
            }
        }
        
        // Genel view'ı kullan
        return view($view, $data);
    }
}
