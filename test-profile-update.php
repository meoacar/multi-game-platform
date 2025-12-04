<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PROFIL GÜNCELLEME TESTİ ===\n\n";

// Kullanıcı ID'sini al (sen hangi kullanıcı ile giriş yaptıysan)
echo "Lütfen kullanıcı ID'nizi girin (veya Enter'a basın, varsayılan: 1): ";
$userId = trim(fgets(STDIN));
$userId = empty($userId) ? 1 : (int)$userId;

$user = \App\Models\User::with('profile')->find($userId);

if (!$user) {
    echo "❌ Kullanıcı bulunamadı!\n";
    exit(1);
}

echo "✅ Kullanıcı bulundu: {$user->email}\n";
echo "Profile var mı? " . ($user->profile ? "Evet (ID: {$user->profile->id})" : "Hayır") . "\n\n";

if (!$user->profile) {
    echo "⚠️ Profile oluşturuluyor...\n";
    $user->profile()->create([]);
    $user->refresh();
    echo "✅ Profile oluşturuldu (ID: {$user->profile->id})\n\n";
}

// Mevcut değerleri göster
echo "=== MEVCUT DEĞERLER ===\n";
echo "Nickname: " . ($user->profile->nickname ?? 'null') . "\n";
echo "PUBG ID: " . ($user->profile->pubg_id ?? 'null') . "\n";
echo "Rank: " . ($user->profile->rank ?? 'null') . "\n";
echo "City: " . ($user->profile->city ?? 'null') . "\n\n";

// Test verisi ile güncelleme yap
echo "=== TEST GÜNCELLEMESİ YAPILIYOR ===\n";
$testData = [
    'nickname' => 'TestOyuncu_' . time(),
    'pubg_id' => 'TEST' . rand(1000, 9999),
    'rank' => 'Gold III',
    'city' => 'İstanbul',
    'play_style' => 'agresif',
];

echo "Test verisi:\n";
print_r($testData);

try {
    // Güncelleme yap
    $user->profile->update($testData);
    echo "\n✅ Update komutu çalıştırıldı\n";
    
    // Veritabanından tekrar çek
    $user->profile->refresh();
    
    echo "\n=== GÜNCELLENMIŞ DEĞERLER ===\n";
    echo "Nickname: " . ($user->profile->nickname ?? 'null') . "\n";
    echo "PUBG ID: " . ($user->profile->pubg_id ?? 'null') . "\n";
    echo "Rank: " . ($user->profile->rank ?? 'null') . "\n";
    echo "City: " . ($user->profile->city ?? 'null') . "\n\n";
    
    // Kontrol et
    if ($user->profile->nickname === $testData['nickname']) {
        echo "✅ BAŞARILI! Veriler kaydedildi.\n";
    } else {
        echo "❌ HATA! Veriler kaydedilmedi.\n";
        echo "Beklenen: {$testData['nickname']}\n";
        echo "Bulunan: {$user->profile->nickname}\n";
    }
    
} catch (\Exception $e) {
    echo "❌ HATA: " . $e->getMessage() . "\n";
    echo "Dosya: " . $e->getFile() . "\n";
    echo "Satır: " . $e->getLine() . "\n";
}

echo "\n=== TEST TAMAMLANDI ===\n";
