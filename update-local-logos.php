<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Local logo dosyalarını kullan
$games = [
    'pubg' => 'images/logolar/pubg.jpeg',
    'valorant' => 'images/logolar/valo.png',
    'csgo' => 'images/logolar/csgo.png',
    'cod' => 'images/logolar/calloflogo.png',
    'lol' => null, // LoL logosu yok, emoji kullanacağız
];

foreach ($games as $slug => $logoPath) {
    $game = App\Models\Game::where('slug', $slug)->first();
    
    if ($game) {
        $game->logo = $logoPath;
        $game->icon = $logoPath;
        $game->save();
        
        echo "✅ {$game->name} logosu güncellendi: {$logoPath}\n";
    } else {
        echo "❌ Oyun bulunamadı: {$slug}\n";
    }
}

echo "\n🎉 Tüm oyun logoları local dosyalara güncellendi!\n";
