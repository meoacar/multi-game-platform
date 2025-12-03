<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$email = $argv[1] ?? 'admin@squadbul.com';

$user = App\Models\User::where('email', $email)->with('profile')->first();

if (!$user) {
    echo "❌ Kullanıcı bulunamadı: {$email}\n";
    exit(1);
}

echo "👤 Kullanıcı: {$user->name} ({$user->email})\n";
echo "=====================================\n\n";

if ($user->profile) {
    echo "📋 Profile Bilgileri:\n";
    echo "  - Nickname: " . ($user->profile->nickname ?? 'null') . "\n";
    echo "  - PUBG ID: " . ($user->profile->pubg_id ?? 'null') . "\n";
    echo "  - Rank: " . ($user->profile->rank ?? 'null') . "\n";
    echo "  - Server: " . ($user->profile->server_region ?? 'null') . "\n";
} else {
    echo "❌ Profile bulunamadı!\n";
}

echo "\n📋 User Tablosundaki Onboarding Bilgileri:\n";
echo "  - PUBG ID: " . ($user->pubg_id ?? 'null') . "\n";
echo "  - Player Level: " . ($user->player_level ?? 'null') . "\n";
echo "  - Player Tier: " . ($user->player_tier ?? 'null') . "\n";
