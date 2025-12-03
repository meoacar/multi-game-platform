<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Game;
use App\Observers\GameObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Policy'leri otomatik keşfet
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'App\\Policies\\' . class_basename($modelClass) . 'Policy';
        });

        // Carbon Türkçe dil ayarı
        \Carbon\Carbon::setLocale('tr');

        // Game config validation
        $this->validateGameConfig();

        // Game Observer'ı kaydet (cache invalidation için)
        Game::observe(GameObserver::class);

        // Slow query logging (Requirements 15.5)
        $this->enableSlowQueryLogging();

        // N+1 query detection (sadece local ve testing ortamlarında)
        if (app()->environment(['local', 'testing'])) {
            $this->enableN1QueryDetection();
        }
    }

    /**
     * Game config'ini validate et
     */
    protected function validateGameConfig(): void
    {
        // Sadece production'da validate et
        if (!app()->environment('production')) {
            return;
        }

        $requiredKeys = [
            'default',
            'defaults',
            'subdomain.domain',
            'session.key',
        ];

        foreach ($requiredKeys as $key) {
            if (config("games.{$key}") === null) {
                throw new \RuntimeException(
                    "Game configuration key 'games.{$key}' is required but not set."
                );
            }
        }

        // Default game'in available listesinde olup olmadığını kontrol et
        $defaultGame = config('games.default');
        if (!isset(config('games.available')[$defaultGame])) {
            throw new \RuntimeException(
                "Default game '{$defaultGame}' is not defined in available games list."
            );
        }
    }

    /**
     * Slow query logging'i etkinleştir (Requirements 15.5)
     * Threshold: 1000ms (1 saniye)
     */
    protected function enableSlowQueryLogging(): void
    {
        $threshold = config('database.slow_query_threshold', 1000); // milliseconds

        \DB::listen(function ($query) use ($threshold) {
            if ($query->time > $threshold) {
                \Log::warning('Slow Query Detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'game_id' => session('game_id'),
                    'user_id' => auth()->id(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                ]);
            }
        });
    }

    /**
     * N+1 query detection (sadece development ortamlarında)
     * Requirements 15.3
     */
    protected function enableN1QueryDetection(): void
    {
        \DB::listen(function ($query) {
            // Query sayısını session'da tut
            $queryCount = session('query_count', 0);
            session(['query_count' => $queryCount + 1]);

            // Eğer bir request'te 50'den fazla query varsa uyar
            if ($queryCount > 50) {
                \Log::warning('Possible N+1 Query Problem Detected', [
                    'query_count' => $queryCount,
                    'url' => request()->fullUrl(),
                    'game_id' => session('game_id'),
                ]);
            }
        });

        // Request sonunda query count'u sıfırla
        app()->terminating(function () {
            session()->forget('query_count');
        });
    }
}
