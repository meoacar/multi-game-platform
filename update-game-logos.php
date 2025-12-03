<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Oyun logolarını güncelle
$games = [
    'pubg' => [
        'logo' => 'https://i.imgur.com/8YqZQ5L.png', // PUBG Mobile logo
        'icon' => 'https://i.imgur.com/8YqZQ5L.png',
    ],
    'valorant' => [
        'logo' => 'https://i.imgur.com/rP8qvHv.png', // Valorant logo
        'icon' => 'https://i.imgur.com/rP8qvHv.png',
    ],
    'csgo' => [
        'logo' => 'https://i.imgur.com/kEJq8nL.png', // CS:GO logo
        'icon' => 'https://i.imgur.com/kEJq8nL.png',
    ],
    'lol' => [
        'logo' => 'https://i.imgur.com/qhB3aNE.png', // League of Legends logo
        'icon' => 'https://i.imgur.com/qhB3aNE.png',
    ],
    'cod' => [
        'logo' => 'https://i.imgur.com/7XqJZ5L.png', // Call of Duty Mobile logo
        'icon' => 'https://i.imgur.com/7XqJZ5L.png',
    ],
];

foreach ($games as $slug => $data) {
    $game = App\Models\Game::where('slug', $slug)->first();
    
    if ($game) {
        $game->logo = $data['logo'];
        $game->icon = $data['icon'];
        $game->save();
        
        echo "✅ {$game->name} logosu güncellendi\n";
    } else {
        echo "❌ Oyun bulunamadı: {$slug}\n";
    }
}

echo "\n🎉 Tüm oyun logoları güncellendi!\n";
