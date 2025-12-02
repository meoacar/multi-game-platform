<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// İlk 3 kullanıcıya test token ekle
$users = \App\Models\User::take(3)->get();

foreach ($users as $user) {
    $deviceTypes = ['android', 'ios', 'web'];
    $randomDevice = $deviceTypes[array_rand($deviceTypes)];
    
    $user->update([
        'fcm_token' => 'test_fcm_token_' . uniqid(),
        'device_type' => $randomDevice,
        'fcm_token_updated_at' => now(),
    ]);
    
    echo "✅ Token eklendi: {$user->name} ({$randomDevice})\n";
}

echo "\n🎉 Test token'ları başarıyla eklendi!\n";
echo "Şimdi admin paneli yenile: http://127.0.0.1:8000/admin/push-notifications\n";
