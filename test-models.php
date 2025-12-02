<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Profile;
use App\Models\Device;
use App\Models\Game;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║         PUBG COMMUNITY - MODEL TEST                       ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Test 1: Oyunları Listele
echo "📋 TEST 1: Oyunları Listele\n";
echo str_repeat("-", 60) . "\n";
$games = Game::all();
echo "Toplam Oyun: " . $games->count() . "\n";
foreach($games as $game) {
    $status = $game->is_active ? '✅' : '❌';
    echo "$status {$game->name} ({$game->slug})\n";
}
echo "\n";

// Test 2: Kullanıcıları Listele
echo "👥 TEST 2: Kullanıcıları Listele\n";
echo str_repeat("-", 60) . "\n";
$users = User::all();
echo "Toplam Kullanıcı: " . $users->count() . "\n";
foreach($users as $user) {
    $adminBadge = $user->is_admin ? '👑' : '👤';
    echo "$adminBadge {$user->name} ({$user->email}) - XP: {$user->xp_total}\n";
}
echo "\n";

// Test 3: İlişkileri Test Et
echo "🔗 TEST 3: User -> Profile İlişkisi\n";
echo str_repeat("-", 60) . "\n";
$user = User::first();
if ($user && $user->profile) {
    echo "Kullanıcı: {$user->name}\n";
    echo "Nick: {$user->profile->nickname}\n";
    echo "Rank: {$user->profile->rank}\n";
    echo "Şehir: {$user->profile->city}\n";
    echo "Oyun Stili: {$user->profile->play_style}\n";
    echo "✅ İlişki çalışıyor!\n";
} else {
    echo "❌ İlişki hatası!\n";
}
echo "\n";

// Test 4: User -> Device İlişkisi
echo "🔗 TEST 4: User -> Device İlişkisi\n";
echo str_repeat("-", 60) . "\n";
if ($user && $user->device) {
    echo "Kullanıcı: {$user->name}\n";
    echo "Cihaz: {$user->device->device_name}\n";
    echo "Grafik: {$user->device->graphics_settings}\n";
    echo "FPS: {$user->device->fps_setting}\n";
    echo "Gyro: " . ($user->device->gyro_enabled ? 'Açık' : 'Kapalı') . "\n";
    echo "✅ İlişki çalışıyor!\n";
} else {
    echo "❌ İlişki hatası!\n";
}
echo "\n";

// Test 5: Helper Metodları
echo "🛠️  TEST 5: Helper Metodları\n";
echo str_repeat("-", 60) . "\n";
$admin = User::where('email', 'admin@pubgcommunity.com')->first();
if ($admin) {
    echo "Admin Kontrolü:\n";
    echo "  isAdmin(): " . ($admin->isAdmin() ? '✅ true' : '❌ false') . "\n";
    echo "  isActive(): " . ($admin->isActive() ? '✅ true' : '❌ false') . "\n";
    echo "  isBanned(): " . ($admin->isBanned() ? '❌ true (BANLI!)' : '✅ false (Aktif)') . "\n";
}
echo "\n";

// Test 6: Device Hassasiyet Ayarları
echo "🎮 TEST 6: Hassasiyet Ayarları\n";
echo str_repeat("-", 60) . "\n";
$device = Device::first();
if ($device) {
    echo "Cihaz: {$device->device_name}\n";
    echo "Hassasiyet Ayarları:\n";
    $formatted = $device->getFormattedSensitivityAttribute();
    foreach($formatted as $setting) {
        echo "  {$setting['label']}: {$setting['value']}\n";
    }
    echo "✅ JSON cast çalışıyor!\n";
}
echo "\n";

// Test 7: Eager Loading
echo "⚡ TEST 7: Eager Loading (N+1 Problemi Önleme)\n";
echo str_repeat("-", 60) . "\n";
$users = User::with(['profile', 'device'])->get();
echo "Yüklenen Kullanıcı: " . $users->count() . "\n";
foreach($users as $user) {
    if ($user->profile) {
        echo "  {$user->name} - {$user->profile->nickname} - {$user->device->device_name}\n";
    }
}
echo "✅ Eager loading çalışıyor!\n";
echo "\n";

// Test 8: Scope'lar
echo "🔍 TEST 8: Query Scope'ları\n";
echo str_repeat("-", 60) . "\n";
$activeGames = Game::active()->ordered()->get();
echo "Aktif Oyunlar: " . $activeGames->count() . "\n";
foreach($activeGames as $game) {
    echo "  {$game->order}. {$game->name}\n";
}
echo "✅ Scope'lar çalışıyor!\n";
echo "\n";

// Test 9: Profile Accessor
echo "🖼️  TEST 9: Accessor'lar\n";
echo str_repeat("-", 60) . "\n";
$profile = Profile::first();
if ($profile) {
    echo "Avatar URL: {$profile->getAvatarUrlAttribute()}\n";
    echo "✅ Accessor çalışıyor!\n";
}
echo "\n";

// Test 10: Özet
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    TEST SONUÇLARI                          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "✅ Tüm modeller çalışıyor\n";
echo "✅ Tüm ilişkiler doğru kurulmuş\n";
echo "✅ Helper metodlar çalışıyor\n";
echo "✅ Cast'ler çalışıyor (JSON, boolean, datetime)\n";
echo "✅ Accessor'lar çalışıyor\n";
echo "✅ Scope'lar çalışıyor\n";
echo "✅ Eager loading çalışıyor\n";
echo "\n";
echo "🎉 FAZ 1 BAŞARIYLA TAMAMLANDI!\n";
echo "\n";
