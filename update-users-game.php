<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// PUBG oyununu bul
$pubg = App\Models\Game::where('slug', 'pubg')->first();

if (!$pubg) {
    echo "PUBG oyunu bulunamadı!\n";
    exit(1);
}

// game_id'si null olan kullanıcıları güncelle
$updated = App\Models\User::whereNull('game_id')->update(['game_id' => $pubg->id]);

echo "✅ {$updated} kullanıcı güncellendi. PUBG oyunu (ID: {$pubg->id}) atandı.\n";
