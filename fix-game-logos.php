<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Oyun logolarını güncelle - Çalışan CDN linkleri
$games = [
    'pubg' => [
        'logo' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/578080/header.jpg',
        'icon' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/578080/header.jpg',
    ],
    'valorant' => [
        'logo' => 'https://images.contentstack.io/v3/assets/bltb6530b271fddd0b1/blt5c61c0d4e8f7c0e4/5eb7cdc0ee88d36e47530d46/V_AGENTS_587x900_Jett.png',
        'icon' => 'https://images.contentstack.io/v3/assets/bltb6530b271fddd0b1/blt5c61c0d4e8f7c0e4/5eb7cdc0ee88d36e47530d46/V_AGENTS_587x900_Jett.png',
    ],
    'csgo' => [
        'logo' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/730/header.jpg',
        'icon' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/730/header.jpg',
    ],
    'lol' => [
        'logo' => 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Ahri_0.jpg',
        'icon' => 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Ahri_0.jpg',
    ],
    'cod' => [
        'logo' => 'https://www.callofduty.com/content/dam/atvi/callofduty/cod-touchui/blog/hero/mw-wz/WZ-Season-Three-Announce-TOUT.jpg',
        'icon' => 'https://www.callofduty.com/content/dam/atvi/callofduty/cod-touchui/blog/hero/mw-wz/WZ-Season-Three-Announce-TOUT.jpg',
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
