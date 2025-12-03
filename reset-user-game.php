<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Kullanıcı email'ini al (komut satırından)
$email = $argv[1] ?? null;

if (!$email) {
    echo "Kullanım: php reset-user-game.php email@example.com\n";
    exit(1);
}

$user = App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "❌ Kullanıcı bulunamadı: {$email}\n";
    exit(1);
}

// game_id ve onboarding_step'i sıfırla
$user->game_id = null;
$user->onboarding_step = 0;
$user->onboarding_completed = false;
$user->save();

echo "✅ Kullanıcı sıfırlandı: {$user->name} ({$user->email})\n";
echo "   - game_id: null\n";
echo "   - onboarding_step: 0\n";
echo "   - onboarding_completed: false\n";
