<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== OYUN LOGOLARI ===\n\n";

$games = App\Models\Game::all();

foreach ($games as $game) {
    echo "Oyun: {$game->name}\n";
    echo "Slug: {$game->slug}\n";
    echo "Logo: {$game->logo}\n";
    echo "Aktif: " . ($game->is_active ? 'Evet' : 'Hayır') . "\n";
    echo "---\n";
}
