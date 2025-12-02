<?php

/**
 * Query Performance Test
 * 
 * Index'lerin performans etkisini test eder
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\LfgPost;
use App\Models\Clan;

echo "=== Query Performance Test ===\n\n";

// Query log'u aktif et
DB::enableQueryLog();

// Test 1: Users tablosu - Status filtreleme
echo "Test 1: Users tablosu - Status filtreleme\n";
$start = microtime(true);
$users = User::where('status', 'active')->limit(100)->get();
$time1 = (microtime(true) - $start) * 1000;
echo "Süre: " . number_format($time1, 2) . " ms\n";
echo "Sonuç: " . $users->count() . " kullanıcı\n\n";

// Test 2: Users tablosu - XP sıralama
echo "Test 2: Users tablosu - XP sıralama\n";
$start = microtime(true);
$topUsers = User::orderBy('xp_total', 'desc')->limit(10)->get();
$time2 = (microtime(true) - $start) * 1000;
echo "Süre: " . number_format($time2, 2) . " ms\n";
echo "Sonuç: " . $topUsers->count() . " kullanıcı\n\n";

// Test 3: Users tablosu - Eager loading ile profile
echo "Test 3: Users tablosu - Eager loading ile profile\n";
$start = microtime(true);
$usersWithProfile = User::with('profile:id,user_id,pubg_id')
    ->select('id', 'name', 'email')
    ->where('status', 'active')
    ->limit(50)
    ->get();
$time3 = (microtime(true) - $start) * 1000;
$queries3 = count(DB::getQueryLog());
DB::flushQueryLog();
echo "Süre: " . number_format($time3, 2) . " ms\n";
echo "Query sayısı: " . $queries3 . "\n";
echo "Sonuç: " . $usersWithProfile->count() . " kullanıcı\n\n";

// Test 4: LFG Posts - Status ve tarih filtreleme (composite index)
echo "Test 4: LFG Posts - Status ve tarih filtreleme\n";
$start = microtime(true);
$lfgPosts = LfgPost::where('status', 'open')
    ->whereDate('created_at', '>=', now()->subDays(30))
    ->limit(50)
    ->get();
$time4 = (microtime(true) - $start) * 1000;
echo "Süre: " . number_format($time4, 2) . " ms\n";
echo "Sonuç: " . $lfgPosts->count() . " ilan\n\n";

// Test 5: Clans - Eager loading ile leader ve game
echo "Test 5: Clans - Eager loading ile leader ve game\n";
$start = microtime(true);
$clans = Clan::with([
        'leader:id,name,email',
        'leader.profile:id,user_id,pubg_id',
        'game:id,name'
    ])
    ->select('id', 'user_id', 'game_id', 'name', 'is_verified')
    ->where('is_verified', true)
    ->limit(20)
    ->get();
$time5 = (microtime(true) - $start) * 1000;
$queries5 = count(DB::getQueryLog());
DB::flushQueryLog();
echo "Süre: " . number_format($time5, 2) . " ms\n";
echo "Query sayısı: " . $queries5 . "\n";
echo "Sonuç: " . $clans->count() . " klan\n\n";

// Test 6: Composite index testi - Game ve status
echo "Test 6: LFG Posts - Game ve status (composite index)\n";
$start = microtime(true);
$lfgByGame = LfgPost::where('game_id', 1)
    ->where('status', 'open')
    ->limit(50)
    ->get();
$time6 = (microtime(true) - $start) * 1000;
echo "Süre: " . number_format($time6, 2) . " ms\n";
echo "Sonuç: " . $lfgByGame->count() . " ilan\n\n";

// Özet
echo "=== ÖZET ===\n";
echo "Test 1 (Status filtreleme): " . number_format($time1, 2) . " ms\n";
echo "Test 2 (XP sıralama): " . number_format($time2, 2) . " ms\n";
echo "Test 3 (Eager loading): " . number_format($time3, 2) . " ms - " . $queries3 . " query\n";
echo "Test 4 (Composite index): " . number_format($time4, 2) . " ms\n";
echo "Test 5 (Multiple eager loading): " . number_format($time5, 2) . " ms - " . $queries5 . " query\n";
echo "Test 6 (Game + Status): " . number_format($time6, 2) . " ms\n";
echo "\nToplam süre: " . number_format($time1 + $time2 + $time3 + $time4 + $time5 + $time6, 2) . " ms\n";

// Index kullanımını kontrol et
echo "\n=== INDEX KULLANIMI ===\n";
$explain = DB::select("EXPLAIN SELECT * FROM users WHERE status = 'active' LIMIT 100");
echo "Users (status): ";
if (isset($explain[0]->key) && $explain[0]->key !== null) {
    echo "✓ Index kullanılıyor (" . $explain[0]->key . ")\n";
} else {
    echo "✗ Index kullanılmıyor\n";
}

$explain2 = DB::select("EXPLAIN SELECT * FROM users ORDER BY xp_total DESC LIMIT 10");
echo "Users (xp_total): ";
if (isset($explain2[0]->key) && $explain2[0]->key !== null) {
    echo "✓ Index kullanılıyor (" . $explain2[0]->key . ")\n";
} else {
    echo "✗ Index kullanılmıyor\n";
}

$explain3 = DB::select("EXPLAIN SELECT * FROM lfg_posts WHERE status = 'open' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) LIMIT 50");
echo "LFG Posts (status + created_at): ";
if (isset($explain3[0]->key) && $explain3[0]->key !== null) {
    echo "✓ Index kullanılıyor (" . $explain3[0]->key . ")\n";
} else {
    echo "✗ Index kullanılmıyor\n";
}

echo "\n✅ Test tamamlandı!\n";
