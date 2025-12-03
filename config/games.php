<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Game
    |--------------------------------------------------------------------------
    |
    | Bu değer, game context bulunamadığında kullanılacak varsayılan oyunu
    | belirtir. Genellikle platformun ilk oyunu olmalıdır.
    |
    */

    'default' => env('DEFAULT_GAME', 'pubg'),

    /*
    |--------------------------------------------------------------------------
    | Game Settings
    |--------------------------------------------------------------------------
    |
    | Her oyun için varsayılan ayarlar. Bu ayarlar veritabanındaki game
    | settings ile birleştirilir. Veritabanındaki ayarlar önceliklidir.
    |
    */

    'defaults' => [
        'theme_color' => '#FF6B00',
        'secondary_color' => '#FFB800',
        'max_team_size' => 4,
        'platforms' => ['Android', 'iOS'],
        'features' => [
            'tournaments' => true,
            'clans' => true,
            'lfg' => true,
            'matchmaking' => true,
            'guides' => true,
            'community_posts' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Available Games
    |--------------------------------------------------------------------------
    |
    | Platformda desteklenen oyunların listesi. Bu liste sadece referans
    | amaçlıdır. Gerçek oyun listesi veritabanından gelir.
    |
    */

    'available' => [
        'pubg' => [
            'name' => 'PUBG Mobile',
            'slug' => 'pubg',
            'description' => 'PUBG Mobile Türkiye Topluluğu',
            'settings' => [
                'theme_color' => '#FF6B00',
                'secondary_color' => '#FFB800',
                'max_team_size' => 4,
                'platforms' => ['Android', 'iOS'],
            ],
        ],
        // Gelecekte eklenecek oyunlar için örnek:
        // 'cod' => [
        //     'name' => 'Call of Duty Mobile',
        //     'slug' => 'cod',
        //     'description' => 'COD Mobile Türkiye Topluluğu',
        //     'settings' => [
        //         'theme_color' => '#00A651',
        //         'secondary_color' => '#FFD700',
        //         'max_team_size' => 5,
        //         'platforms' => ['Android', 'iOS'],
        //     ],
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Oyun bilgilerinin cache'lenmesi için ayarlar.
    |
    */

    'cache' => [
        'enabled' => env('GAME_CACHE_ENABLED', true),
        'ttl' => env('GAME_CACHE_TTL', 3600), // 1 saat
        'prefix' => 'game',
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Settings
    |--------------------------------------------------------------------------
    |
    | Oyun context'inin session'da saklanması için ayarlar.
    |
    */

    'session' => [
        'key' => 'game_id',
        'game_key' => 'game',
        'last_game_key' => 'last_game_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Subdomain Settings
    |--------------------------------------------------------------------------
    |
    | Subdomain routing için ayarlar.
    |
    */

    'subdomain' => [
        'enabled' => env('SUBDOMAIN_ENABLED', true),
        'domain' => env('APP_DOMAIN', 'takimsistemi.com'),
        'main_domain' => env('MAIN_DOMAIN', 'takimsistemi.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Oyun oluşturma ve güncelleme için validation kuralları.
    |
    */

    'validation' => [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:games,slug|alpha_dash',
        'logo' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:active,inactive',
        'settings' => 'nullable|array',
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Platform genelinde oyun özelliklerini açıp kapatmak için.
    |
    */

    'features' => [
        'multi_game' => env('MULTI_GAME_ENABLED', true),
        'game_switching' => env('GAME_SWITCHING_ENABLED', true),
        'cross_game_messaging' => env('CROSS_GAME_MESSAGING', true),
        'cross_game_friendships' => env('CROSS_GAME_FRIENDSHIPS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Performans optimizasyonu için ayarlar.
    |
    */

    'performance' => [
        'eager_load_game' => true,
        'cache_active_games' => true,
        'log_slow_queries' => env('LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 1000), // ms
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Güvenlik ayarları.
    |
    */

    'security' => [
        'enforce_game_context' => true,
        'log_cross_game_access' => true,
        'prevent_cross_game_access' => true,
    ],

];
