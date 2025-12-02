<?php

/**
 * Form Request ve Policy Test Scripti
 * Bu script, oluşturulan Form Request ve Policy sınıflarının varlığını kontrol eder
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

echo "=== FORM REQUEST VE POLICY TEST ===\n\n";

// Test edilecek Form Request'ler
$formRequests = [
    'Auth' => [
        'App\Http\Requests\Api\V1\Auth\RegisterRequest',
        'App\Http\Requests\Api\V1\Auth\LoginRequest',
    ],
    'Profile' => [
        'App\Http\Requests\Api\V1\Profile\UpdateProfileRequest',
    ],
    'Device' => [
        'App\Http\Requests\Api\V1\Device\StoreUpdateDeviceRequest',
    ],
    'LFG' => [
        'App\Http\Requests\Api\V1\Lfg\StoreLfgRequest',
        'App\Http\Requests\Api\V1\Lfg\UpdateLfgRequest',
        'App\Http\Requests\Api\V1\Lfg\ApplyLfgRequest',
    ],
    'Clan' => [
        'App\Http\Requests\Api\V1\Clan\StoreClanRequest',
        'App\Http\Requests\Api\V1\Clan\UpdateClanRequest',
        'App\Http\Requests\Api\V1\Clan\ApplyClanRequest',
    ],
];

// Test edilecek Policy'ler
$policies = [
    'App\Policies\LfgPostPolicy',
    'App\Policies\ClanPolicy',
    'App\Policies\ProfilePolicy',
    'App\Policies\DevicePolicy',
];

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

// Form Request Testleri
echo "📋 FORM REQUEST TESTLERİ\n";
echo str_repeat("-", 50) . "\n\n";

foreach ($formRequests as $category => $requests) {
    echo "[$category]\n";
    foreach ($requests as $class) {
        $totalTests++;
        $shortName = substr($class, strrpos($class, '\\') + 1);
        
        if (class_exists($class)) {
            echo "  ✅ $shortName - Sınıf mevcut\n";
            
            // Metodları kontrol et
            $reflection = new ReflectionClass($class);
            $methods = ['authorize', 'rules', 'messages', 'attributes'];
            $allMethodsExist = true;
            
            foreach ($methods as $method) {
                if (!$reflection->hasMethod($method)) {
                    echo "     ⚠️  $method metodu eksik\n";
                    $allMethodsExist = false;
                }
            }
            
            if ($allMethodsExist) {
                $passedTests++;
                echo "     ✓ Tüm metodlar mevcut\n";
            } else {
                $failedTests++;
            }
        } else {
            echo "  ❌ $shortName - Sınıf bulunamadı\n";
            $failedTests++;
        }
    }
    echo "\n";
}

// Policy Testleri
echo "🔒 POLICY TESTLERİ\n";
echo str_repeat("-", 50) . "\n\n";

foreach ($policies as $class) {
    $totalTests++;
    $shortName = substr($class, strrpos($class, '\\') + 1);
    
    if (class_exists($class)) {
        echo "✅ $shortName - Sınıf mevcut\n";
        
        // Metodları kontrol et
        $reflection = new ReflectionClass($class);
        $methods = ['viewAny', 'view', 'create', 'update', 'delete'];
        $methodCount = 0;
        
        foreach ($methods as $method) {
            if ($reflection->hasMethod($method)) {
                $methodCount++;
            }
        }
        
        echo "   ✓ $methodCount temel metod mevcut\n";
        
        // Özel metodları kontrol et
        if ($shortName === 'LfgPostPolicy' && $reflection->hasMethod('apply')) {
            echo "   ✓ apply metodu mevcut\n";
        }
        if ($shortName === 'ClanPolicy' && $reflection->hasMethod('manageMembers')) {
            echo "   ✓ manageMembers metodu mevcut\n";
        }
        
        $passedTests++;
    } else {
        echo "❌ $shortName - Sınıf bulunamadı\n";
        $failedTests++;
    }
    echo "\n";
}

// Özet
echo str_repeat("=", 50) . "\n";
echo "TEST ÖZETİ\n";
echo str_repeat("=", 50) . "\n";
echo "Toplam Test: $totalTests\n";
echo "Başarılı: $passedTests ✅\n";
echo "Başarısız: $failedTests ❌\n";
echo "Başarı Oranı: " . round(($passedTests / $totalTests) * 100, 2) . "%\n";

if ($failedTests === 0) {
    echo "\n🎉 Tüm testler başarılı!\n";
} else {
    echo "\n⚠️  Bazı testler başarısız oldu.\n";
}
