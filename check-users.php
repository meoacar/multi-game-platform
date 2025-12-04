<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== KULLANICILAR LİSTESİ ===\n\n";

$users = \App\Models\User::with('profile')->take(10)->get();

if ($users->isEmpty()) {
    echo "❌ Hiç kullanıcı bulunamadı!\n";
    exit(1);
}

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Email: {$user->email}\n";
    echo "İsim: {$user->name}\n";
    echo "Profile var mı? " . ($user->profile ? "Evet (ID: {$user->profile->id})" : "Hayır") . "\n";
    if ($user->profile) {
        echo "  - Nickname: " . ($user->profile->nickname ?? 'null') . "\n";
        echo "  - PUBG ID: " . ($user->profile->pubg_id ?? 'null') . "\n";
        echo "  - Rank: " . ($user->profile->rank ?? 'null') . "\n";
    }
    echo "---\n\n";
}

echo "Toplam kullanıcı sayısı: " . \App\Models\User::count() . "\n";
