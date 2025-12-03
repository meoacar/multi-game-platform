@echo off
echo ========================================
echo Multi-Game Platform - Test Deployment
echo ========================================
echo.

REM PHP path
set PHP=C:\xampp\php\php.exe

echo [1/10] PHP Versiyonu Kontrol Ediliyor...
%PHP% -v
if %errorlevel% neq 0 (
    echo HATA: PHP bulunamadi!
    pause
    exit /b 1
)
echo ✓ PHP OK
echo.

echo [2/10] Laravel Versiyonu Kontrol Ediliyor...
%PHP% artisan --version
if %errorlevel% neq 0 (
    echo HATA: Laravel bulunamadi!
    pause
    exit /b 1
)
echo ✓ Laravel OK
echo.

echo [3/10] Veritabani Baglantisi Kontrol Ediliyor...
%PHP% artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';"
if %errorlevel% neq 0 (
    echo HATA: Veritabani baglantisi basarisiz!
    pause
    exit /b 1
)
echo ✓ Veritabani OK
echo.

echo [4/10] Migration Durumu Kontrol Ediliyor...
%PHP% artisan migrate:status
echo ✓ Migration durumu kontrol edildi
echo.

echo [5/10] Game Model Kontrol Ediliyor...
%PHP% artisan tinker --execute="echo 'Game model: ' . (class_exists('App\Models\Game') ? 'EXISTS' : 'NOT FOUND');"
echo.

echo [6/10] GameScope Kontrol Ediliyor...
%PHP% artisan tinker --execute="echo 'GameScope: ' . (class_exists('App\Models\Scopes\GameScope') ? 'EXISTS' : 'NOT FOUND');"
echo.

echo [7/10] DetectGame Middleware Kontrol Ediliyor...
%PHP% artisan tinker --execute="echo 'DetectGame: ' . (class_exists('App\Http\Middleware\DetectGame') ? 'EXISTS' : 'NOT FOUND');"
echo.

echo [8/10] GameService Kontrol Ediliyor...
%PHP% artisan tinker --execute="echo 'GameService: ' . (class_exists('App\Services\GameService') ? 'EXISTS' : 'NOT FOUND');"
echo.

echo [9/10] Config Dosyalari Kontrol Ediliyor...
if exist "config\games.php" (
    echo ✓ config/games.php EXISTS
) else (
    echo ✗ config/games.php NOT FOUND
)
echo.

echo [10/10] Route Dosyalari Kontrol Ediliyor...
if exist "routes\main.php" (
    echo ✓ routes/main.php EXISTS
) else (
    echo ✗ routes/main.php NOT FOUND
)
echo.

echo ========================================
echo Test Deployment Tamamlandi!
echo ========================================
echo.
echo Sonraki adim: deploy-multi-game.bat
echo.
pause
