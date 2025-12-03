@echo off
REM ============================================
REM Multi-Game Platform Verification Script
REM ============================================
REM Bu script deployment'in basarili olup olmadigini dogrular
REM Kullanim: verify-deployment.bat

echo.
echo ============================================
echo Multi-Game Platform Verification
echo ============================================
echo.

REM Renk kodlari
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "NC=[0m"

set ERROR_COUNT=0
set WARNING_COUNT=0

echo %YELLOW%[1/10] Veritabani baglantisi kontrol ediliyor...%NC%
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connection: OK' . PHP_EOL; } catch (Exception $e) { echo 'Database connection: FAILED - ' . $e->getMessage() . PHP_EOL; exit(1); }"
if errorlevel 1 (
    echo %RED%HATA: Veritabani baglantisi basarisiz!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%Veritabani baglantisi basarili!%NC%
)
echo.

echo %YELLOW%[2/10] games tablosu kontrol ediliyor...%NC%
php artisan tinker --execute="if (Schema::hasTable('games')) { echo 'games table: EXISTS' . PHP_EOL; $count = DB::table('games')->count(); echo 'Game count: ' . $count . PHP_EOL; if ($count == 0) { echo 'WARNING: No games found!' . PHP_EOL; exit(2); } } else { echo 'games table: NOT FOUND' . PHP_EOL; exit(1); }"
if errorlevel 2 (
    echo %YELLOW%UYARI: games tablosu bos!%NC%
    set /a WARNING_COUNT+=1
) else if errorlevel 1 (
    echo %RED%HATA: games tablosu bulunamadi!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%games tablosu mevcut ve dolu!%NC%
)
echo.

echo %YELLOW%[3/10] game_id kolonlari kontrol ediliyor...%NC%
php artisan tinker --execute="$tables = ['tournaments', 'clans', 'lfg_posts', 'badges', 'notifications', 'community_posts', 'guide_posts']; $missing = []; foreach ($tables as $table) { if (!Schema::hasColumn($table, 'game_id')) { $missing[] = $table; } } if (empty($missing)) { echo 'All game_id columns: EXISTS' . PHP_EOL; } else { echo 'Missing game_id in: ' . implode(', ', $missing) . PHP_EOL; exit(1); }"
if errorlevel 1 (
    echo %RED%HATA: Bazi tablolarda game_id kolonu eksik!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%Tum game_id kolonlari mevcut!%NC%
)
echo.

echo %YELLOW%[4/10] Foreign key constraint'leri kontrol ediliyor...%NC%
php artisan tinker --execute="try { $fks = DB::select('SELECT TABLE_NAME, CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_TYPE = \"FOREIGN KEY\" AND CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME LIKE \"%%_game\"'); echo 'Foreign keys found: ' . count($fks) . PHP_EOL; if (count($fks) < 5) { echo 'WARNING: Expected at least 5 game foreign keys' . PHP_EOL; exit(2); } } catch (Exception $e) { echo 'Error checking foreign keys: ' . $e->getMessage() . PHP_EOL; exit(1); }"
if errorlevel 2 (
    echo %YELLOW%UYARI: Beklenen foreign key sayisi eksik!%NC%
    set /a WARNING_COUNT+=1
) else if errorlevel 1 (
    echo %RED%HATA: Foreign key kontrolu basarisiz!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%Foreign key constraint'leri mevcut!%NC%
)
echo.

echo %YELLOW%[5/10] Mevcut veri butunlugu kontrol ediliyor...%NC%
php artisan tinker --execute="$users = App\Models\User::count(); $tournaments = App\Models\Tournament::withoutGlobalScope(App\Models\Scopes\GameScope::class)->count(); $clans = App\Models\Clan::withoutGlobalScope(App\Models\Scopes\GameScope::class)->count(); echo 'Users: ' . $users . PHP_EOL; echo 'Tournaments: ' . $tournaments . PHP_EOL; echo 'Clans: ' . $clans . PHP_EOL; if ($users == 0) { echo 'WARNING: No users found!' . PHP_EOL; exit(2); }"
if errorlevel 2 (
    echo %YELLOW%UYARI: Kullanici verisi bulunamadi!%NC%
    set /a WARNING_COUNT+=1
) else if errorlevel 1 (
    echo %RED%HATA: Veri butunlugu kontrolu basarisiz!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%Mevcut veriler korunmus!%NC%
)
echo.

echo %YELLOW%[6/10] Game model kontrol ediliyor...%NC%
php artisan tinker --execute="try { $game = App\Models\Game::first(); if ($game) { echo 'Game model: OK' . PHP_EOL; echo 'Game name: ' . $game->name . PHP_EOL; echo 'Game slug: ' . $game->slug . PHP_EOL; } else { echo 'No games found in database' . PHP_EOL; exit(2); } } catch (Exception $e) { echo 'Game model error: ' . $e->getMessage() . PHP_EOL; exit(1); }"
if errorlevel 2 (
    echo %YELLOW%UYARI: Veritabaninda oyun bulunamadi!%NC%
    set /a WARNING_COUNT+=1
) else if errorlevel 1 (
    echo %RED%HATA: Game model hatasi!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%Game model calisiyor!%NC%
)
echo.

echo %YELLOW%[7/10] GameScope kontrol ediliyor...%NC%
php artisan tinker --execute="try { session(['game_id' => 1]); $tournaments = App\Models\Tournament::count(); echo 'GameScope working: Filtered ' . $tournaments . ' tournaments for game_id=1' . PHP_EOL; $allTournaments = App\Models\Tournament::withoutGlobalScope(App\Models\Scopes\GameScope::class)->count(); echo 'Total tournaments (without scope): ' . $allTournaments . PHP_EOL; } catch (Exception $e) { echo 'GameScope error: ' . $e->getMessage() . PHP_EOL; exit(1); }"
if errorlevel 1 (
    echo %RED%HATA: GameScope calismıyor!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%GameScope calisiyor!%NC%
)
echo.

echo %YELLOW%[8/10] Config dosyalari kontrol ediliyor...%NC%
if not exist config\games.php (
    echo %RED%HATA: config\games.php bulunamadi!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%config\games.php mevcut!%NC%
)

REM Session domain kontrolu
findstr /C:"SESSION_DOMAIN" .env >nul
if errorlevel 1 (
    echo %YELLOW%UYARI: .env dosyasinda SESSION_DOMAIN tanimli degil!%NC%
    set /a WARNING_COUNT+=1
) else (
    echo %GREEN%.env SESSION_DOMAIN tanimli!%NC%
)
echo.

echo %YELLOW%[9/10] Middleware kontrol ediliyor...%NC%
if not exist app\Http\Middleware\DetectGame.php (
    echo %RED%HATA: DetectGame middleware bulunamadi!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%DetectGame middleware mevcut!%NC%
)
echo.

echo %YELLOW%[10/10] Service siniflari kontrol ediliyor...%NC%
if not exist app\Services\GameService.php (
    echo %RED%HATA: GameService bulunamadi!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%GameService mevcut!%NC%
)

if not exist app\Services\MultiTenantService.php (
    echo %RED%HATA: MultiTenantService bulunamadi!%NC%
    set /a ERROR_COUNT+=1
) else (
    echo %GREEN%MultiTenantService mevcut!%NC%
)
echo.

echo.
echo ============================================
echo Verification Sonuclari
echo ============================================
echo.

if %ERROR_COUNT% EQU 0 (
    if %WARNING_COUNT% EQU 0 (
        echo %GREEN%TUM KONTROLLER BASARILI!%NC%
        echo %GREEN%Deployment tamamen basarili.%NC%
    ) else (
        echo %YELLOW%UYARILAR MEVCUT: %WARNING_COUNT%%NC%
        echo Deployment basarili ama bazi uyarilar var.
        echo Lutfen uyarilari inceleyin.
    )
) else (
    echo %RED%HATALAR BULUNDU: %ERROR_COUNT%%NC%
    echo %YELLOW%Uyarilar: %WARNING_COUNT%%NC%
    echo.
    echo %RED%Deployment basarisiz!%NC%
    echo Lutfen hatalari duzeltin ve tekrar deneyin.
    exit /b 1
)

echo.
echo %YELLOW%Manuel Test Onerileri:%NC%
echo 1. Ana sayfayi ziyaret edin: http://takimsistemi.com
echo 2. PUBG subdomain'i test edin: http://pubg.takimsistemi.com
echo 3. Kullanici girisi yapin ve oyunlar arasi gecis yapin
echo 4. Turnuva, klan ve LFG ozelliklerini test edin
echo 5. Log dosyalarini kontrol edin: storage\logs\laravel.log
echo.

pause
exit /b 0
