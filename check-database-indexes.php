<?php

/**
 * Database Index Kontrol Scripti
 * 
 * Bu script, multi-game platform için gerekli tüm database index'lerinin
 * mevcut olup olmadığını kontrol eder.
 * 
 * Kullanım:
 * php check-database-indexes.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         Database Index Kontrol - Multi-Game Platform          ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Gerekli index'ler
$requiredIndexes = [
    'games' => [
        'games_slug_unique' => ['slug'],
        'games_status_index' => ['status'],
    ],
    'tournaments' => [
        'tournaments_game_id_foreign' => ['game_id'],
        'tournaments_game_id_index' => ['game_id'],
    ],
    'clans' => [
        'clans_game_id_foreign' => ['game_id'],
        'clans_game_id_index' => ['game_id'],
        'clans_game_verified_idx' => ['game_id', 'is_verified'],
    ],
    'lfg_posts' => [
        'lfg_posts_game_id_foreign' => ['game_id'],
        'lfg_posts_game_id_index' => ['game_id'],
        'lfg_posts_game_status_idx' => ['game_id', 'status'],
    ],
    'badges' => [
        'badges_game_id_foreign' => ['game_id'],
        'badges_game_id_index' => ['game_id'],
    ],
    'notifications' => [
        'notifications_game_id_foreign' => ['game_id'],
        'notifications_game_id_index' => ['game_id'],
    ],
    'community_posts' => [
        'community_posts_game_id_foreign' => ['game_id'],
        'community_posts_game_id_type_created_at_index' => ['game_id', 'type', 'created_at'],
    ],
    'guide_posts' => [
        'guide_posts_game_id_foreign' => ['game_id'],
        'guide_posts_game_id_index' => ['game_id'],
        'guide_posts_game_published_idx' => ['game_id', 'is_published'],
    ],
];

// Performance indexes
$performanceIndexes = [
    'users' => [
        'users_last_login_at_index',
        'users_xp_total_index',
        'users_status_created_at_idx',
    ],
    'profiles' => [
        'profiles_game_id_index',
        'profiles_user_game_idx',
    ],
    'lfg_posts' => [
        'lfg_posts_status_created_idx',
    ],
    'clans' => [
        'clans_leader_id_index',
    ],
    'guide_posts' => [
        'guide_posts_published_created_idx',
    ],
];

$missingIndexes = [];
$existingIndexes = [];
$totalChecked = 0;

/**
 * Tablodaki index'leri al
 */
function getTableIndexes($table) {
    try {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        $indexList = [];
        
        foreach ($indexes as $index) {
            $indexName = $index->Key_name;
            if (!isset($indexList[$indexName])) {
                $indexList[$indexName] = [];
            }
            $indexList[$indexName][] = $index->Column_name;
        }
        
        return $indexList;
    } catch (\Exception $e) {
        return [];
    }
}

/**
 * Index'in var olup olmadığını kontrol et
 */
function checkIndex($table, $indexName, $columns) {
    $indexes = getTableIndexes($table);
    
    if (isset($indexes[$indexName])) {
        // Index var, kolonları kontrol et
        $existingColumns = $indexes[$indexName];
        sort($columns);
        sort($existingColumns);
        
        if ($columns === $existingColumns) {
            return true;
        }
    }
    
    return false;
}

echo "🔍 Gerekli Index'ler Kontrol Ediliyor...\n";
echo str_repeat("─", 64) . "\n\n";

// Gerekli index'leri kontrol et
foreach ($requiredIndexes as $table => $indexes) {
    if (!Schema::hasTable($table)) {
        echo "⚠️  Tablo bulunamadı: {$table}\n";
        continue;
    }
    
    echo "📋 Tablo: {$table}\n";
    
    foreach ($indexes as $indexName => $columns) {
        $totalChecked++;
        
        if (checkIndex($table, $indexName, $columns)) {
            echo "   ✅ {$indexName}\n";
            $existingIndexes[] = "{$table}.{$indexName}";
        } else {
            echo "   ❌ {$indexName} - EKSİK!\n";
            $missingIndexes[] = [
                'table' => $table,
                'index' => $indexName,
                'columns' => $columns,
            ];
        }
    }
    
    echo "\n";
}

echo "\n";
echo "🚀 Performance Index'leri Kontrol Ediliyor...\n";
echo str_repeat("─", 64) . "\n\n";

// Performance index'leri kontrol et
foreach ($performanceIndexes as $table => $indexes) {
    if (!Schema::hasTable($table)) {
        continue;
    }
    
    echo "📋 Tablo: {$table}\n";
    
    foreach ($indexes as $indexName) {
        $totalChecked++;
        $tableIndexes = getTableIndexes($table);
        
        if (isset($tableIndexes[$indexName])) {
            echo "   ✅ {$indexName}\n";
            $existingIndexes[] = "{$table}.{$indexName}";
        } else {
            echo "   ⚠️  {$indexName} - Önerilir (Performance)\n";
        }
    }
    
    echo "\n";
}

// Özet
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                            ÖZET                                ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "📊 Toplam Kontrol Edilen: {$totalChecked}\n";
echo "✅ Mevcut Index'ler: " . count($existingIndexes) . "\n";
echo "❌ Eksik Index'ler: " . count($missingIndexes) . "\n";
echo "\n";

if (count($missingIndexes) > 0) {
    echo "⚠️  EKSİK INDEX'LER:\n";
    echo str_repeat("─", 64) . "\n";
    
    foreach ($missingIndexes as $missing) {
        echo "\n";
        echo "Tablo: {$missing['table']}\n";
        echo "Index: {$missing['index']}\n";
        echo "Kolonlar: " . implode(', ', $missing['columns']) . "\n";
        
        // Migration komutu öner
        $columns = implode("', '", $missing['columns']);
        echo "\nMigration Kodu:\n";
        echo "Schema::table('{$missing['table']}', function (Blueprint \$table) {\n";
        echo "    \$table->index(['{$columns}'], '{$missing['index']}');\n";
        echo "});\n";
    }
    
    echo "\n";
    echo "⚠️  UYARI: Eksik index'ler performans sorunlarına neden olabilir!\n";
    echo "Migration dosyalarını çalıştırın:\n";
    echo "php artisan migrate\n";
    echo "\n";
} else {
    echo "✅ Tüm gerekli index'ler mevcut!\n";
    echo "🎉 Database production'a hazır!\n";
    echo "\n";
}

// Foreign key kontrolü
echo "\n";
echo "🔗 Foreign Key Kontrolü...\n";
echo str_repeat("─", 64) . "\n\n";

$foreignKeys = [
    'tournaments' => ['game_id' => 'games'],
    'clans' => ['game_id' => 'games'],
    'lfg_posts' => ['game_id' => 'games'],
    'badges' => ['game_id' => 'games'],
    'notifications' => ['game_id' => 'games'],
    'community_posts' => ['game_id' => 'games'],
    'guide_posts' => ['game_id' => 'games'],
];

foreach ($foreignKeys as $table => $keys) {
    if (!Schema::hasTable($table)) {
        continue;
    }
    
    echo "📋 Tablo: {$table}\n";
    
    foreach ($keys as $column => $referencedTable) {
        // Foreign key var mı kontrol et
        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = '{$table}'
            AND COLUMN_NAME = '{$column}'
            AND REFERENCED_TABLE_NAME = '{$referencedTable}'
        ");
        
        if (count($fkExists) > 0) {
            echo "   ✅ {$column} -> {$referencedTable}\n";
        } else {
            echo "   ❌ {$column} -> {$referencedTable} - EKSİK!\n";
        }
    }
    
    echo "\n";
}

echo "\n";
echo "✨ Kontrol tamamlandı!\n";
echo "\n";
