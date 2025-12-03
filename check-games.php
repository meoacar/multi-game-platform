<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎮 Aktif Oyunlar:\n";
echo "================\n\n";

$games = App\Models\Game::where('status', 'active')->orderBy('order')->get();

foreach ($games as $game) {
    echo "ID: {$game->id}\n";
    echo "İsim: {$game->name}\n";
    echo "Slug: {$game->slug}\n";
    echo "Durum: {$game->status}\n";
    echo "---\n";
}

echo "\nToplam: " . $games->count() . " oyun\n";
