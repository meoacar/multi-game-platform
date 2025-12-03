@echo off
echo ========================================
echo PRODUCTION HAZIRLIK KONTROLU
echo ========================================
echo.

echo [1/8] PHP Versiyonu Kontrol Ediliyor...
C:\xampp\php\php.exe -v
if %errorlevel% neq 0 (
    echo HATA: PHP bulunamadi!
    pause
    exit /b 1
)
echo.

echo [2/8] Veritabani Baglantisi Kontrol Ediliyor...
C:\xampp\php\php.exe artisan tinker --execute="DB::connection()->getPdo(); echo 'Veritabani: OK';"
if %errorlevel% neq 0 (
    echo HATA: Veritabani baglantisi basarisiz!
    pause
    exit /b 1
)
echo.

echo [3/8] Migration Durumu Kontrol Ediliyor...
C:\xampp\php\php.exe artisan migrate:status
if %errorlevel% neq 0 (
    echo HATA: Migration kontrolu basarisiz!
    pause
    exit /b 1
)
echo.

echo [4/8] Oyun Verileri Kontrol Ediliyor...
C:\xampp\php\php.exe artisan tinker --execute="echo 'Oyun Sayisi: ' . App\Models\Game::count();"
if %errorlevel% neq 0 (
    echo HATA: Oyun verileri kontrol edilemedi!
    pause
    exit /b 1
)
echo.

echo [5/8] Config Dosyalari Kontrol Ediliyor...
if not exist "config\games.php" (
    echo HATA: config/games.php bulunamadi!
    pause
    exit /b 1
)
if not exist "config\game-content.php" (
    echo HATA: config/game-content.php bulunamadi!
    pause
    exit /b 1
)
echo Config dosyalari: OK
echo.

echo [6/8] Middleware Kontrol Ediliyor...
if not exist "app\Http\Middleware\DetectGame.php" (
    echo HATA: DetectGame middleware bulunamadi!
    pause
    exit /b 1
)
echo Middleware: OK
echo.

echo [7/8] Service Siniflari Kontrol Ediliyor...
if not exist "app\Services\GameService.php" (
    echo HATA: GameService bulunamadi!
    pause
    exit /b 1
)
if not exist "app\Services\MultiTenantService.php" (
    echo HATA: MultiTenantService bulunamadi!
    pause
    exit /b 1
)
echo Service siniflari: OK
echo.

echo [8/8] View Dosyalari Kontrol Ediliyor...
if not exist "resources\views\components\game-switcher.blade.php" (
    echo HATA: game-switcher component bulunamadi!
    pause
    exit /b 1
)
if not exist "resources\views\main\home.blade.php" (
    echo HATA: main/home view bulunamadi!
    pause
    exit /b 1
)
echo View dosyalari: OK
echo.

echo ========================================
echo TUM KONTROLLER BASARILI!
echo ========================================
echo.
echo Sistem production icin hazir.
echo.
echo SONRAKI ADIMLAR:
echo 1. .env dosyasini production ayarlarina guncelle
echo 2. Veritabani yedegi al (backup-database.bat)
echo 3. Composer install --no-dev --optimize-autoloader
echo 4. php artisan config:cache
echo 5. php artisan view:cache
echo.
pause
