@echo off
REM ============================================
REM Multi-Game Platform Deployment Script
REM ============================================
REM Bu script multi-game platformunu production'a deploy eder
REM Kullanim: deploy-multi-game.bat

echo.
echo ============================================
echo Multi-Game Platform Deployment
echo ============================================
echo.

REM Renk kodlari
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "NC=[0m"

REM Tarih ve zaman
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set TIMESTAMP=%datetime:~0,8%-%datetime:~8,6%

echo %YELLOW%[1/10] Ortam kontrolleri yapiliyor...%NC%
echo.

REM PHP versiyonu kontrolu
php -v >nul 2>&1
if errorlevel 1 (
    echo %RED%HATA: PHP bulunamadi!%NC%
    exit /b 1
)

REM Composer kontrolu
composer --version >nul 2>&1
if errorlevel 1 (
    echo %RED%HATA: Composer bulunamadi!%NC%
    exit /b 1
)

REM .env dosyasi kontrolu
if not exist .env (
    echo %RED%HATA: .env dosyasi bulunamadi!%NC%
    echo Lutfen .env.example dosyasindan .env olusturun.
    exit /b 1
)

echo %GREEN%Ortam kontrolleri basarili!%NC%
echo.

REM Kullanicidan onay al
echo %YELLOW%UYARI: Bu islem veritabaninda degisiklik yapacak!%NC%
echo.
set /p CONFIRM="Devam etmek istiyor musunuz? (E/H): "
if /i not "%CONFIRM%"=="E" (
    echo Islem iptal edildi.
    exit /b 0
)
echo.

echo %YELLOW%[2/10] Veritabani yedegi aliniyor...%NC%
echo.
call backup-database.bat
if errorlevel 1 (
    echo %RED%HATA: Veritabani yedegi alinamadi!%NC%
    exit /b 1
)
echo %GREEN%Yedek basariyla alindi!%NC%
echo.

echo %YELLOW%[3/10] Uygulama maintenance moduna aliniyor...%NC%
php artisan down --retry=60
echo %GREEN%Maintenance modu aktif!%NC%
echo.

echo %YELLOW%[4/10] Git repository guncelleniyor...%NC%
git pull origin main
if errorlevel 1 (
    echo %RED%HATA: Git pull basarisiz!%NC%
    php artisan up
    exit /b 1
)
echo %GREEN%Repository guncellendi!%NC%
echo.

echo %YELLOW%[5/10] Composer bagimliliklari guncelleniyor...%NC%
composer install --no-dev --optimize-autoloader
if errorlevel 1 (
    echo %RED%HATA: Composer install basarisiz!%NC%
    php artisan up
    exit /b 1
)
echo %GREEN%Composer bagimliliklari guncellendi!%NC%
echo.

echo %YELLOW%[6/10] Cache temizleniyor...%NC%
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
echo %GREEN%Cache temizlendi!%NC%
echo.

echo %YELLOW%[7/10] Migration'lar calistiriliyor...%NC%
echo.
echo %RED%DIKKAT: Simdi migration'lar calistirilacak!%NC%
echo Bu islem geri alinamaz (rollback script haric).
echo.
set /p MIGRATE_CONFIRM="Migration'lari calistirmak istediginizden emin misiniz? (E/H): "
if /i not "%MIGRATE_CONFIRM%"=="E" (
    echo Migration'lar atlandı.
    echo %YELLOW%Uygulamayi aktif hale getiriyorum...%NC%
    php artisan up
    echo Deployment tamamlanamadi - Migration'lar calistirilmadi.
    exit /b 1
)

php artisan migrate --force
if errorlevel 1 (
    echo %RED%HATA: Migration basarisiz!%NC%
    echo.
    echo %YELLOW%Rollback yapmak ister misiniz? (E/H): %NC%
    set /p ROLLBACK="Rollback: "
    if /i "%ROLLBACK%"=="E" (
        call rollback-multi-game.bat
    )
    php artisan up
    exit /b 1
)
echo %GREEN%Migration'lar basariyla tamamlandi!%NC%
echo.

echo %YELLOW%[8/10] Seeder'lar calistiriliyor...%NC%
php artisan db:seed --class=GameSeeder --force
if errorlevel 1 (
    echo %RED%UYARI: GameSeeder basarisiz! Manuel kontrol gerekli.%NC%
) else (
    echo %GREEN%Seeder'lar basariyla calistirildi!%NC%
)
echo.

echo %YELLOW%[9/10] Cache optimize ediliyor...%NC%
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo %GREEN%Cache optimize edildi!%NC%
echo.

echo %YELLOW%[10/10] Dogrulama yapiliyor...%NC%
echo.
call verify-deployment.bat
if errorlevel 1 (
    echo %RED%UYARI: Dogrulama hatalari bulundu!%NC%
    echo Lutfen verify-deployment.bat ciktisini inceleyin.
    echo.
    set /p CONTINUE="Yine de devam etmek istiyor musunuz? (E/H): "
    if /i not "%CONTINUE%"=="E" (
        echo Deployment iptal edildi.
        php artisan up
        exit /b 1
    )
)
echo.

echo %YELLOW%Uygulama aktif hale getiriliyor...%NC%
php artisan up
echo %GREEN%Uygulama aktif!%NC%
echo.

echo.
echo %GREEN%============================================%NC%
echo %GREEN%Deployment Basariyla Tamamlandi!%NC%
echo %GREEN%============================================%NC%
echo.
echo Deployment Zamani: %TIMESTAMP%
echo Yedek Dosyasi: database-backups/pubg_community_%TIMESTAMP%.sql
echo.
echo %YELLOW%Sonraki Adimlar:%NC%
echo 1. Uygulamayi test edin
echo 2. Log dosyalarini kontrol edin
echo 3. Kullanicilara duyuru yapin
echo.
echo %YELLOW%Sorun yasarsaniz:%NC%
echo - rollback-multi-game.bat ile geri alin
echo - Log dosyalarini inceleyin: storage/logs/laravel.log
echo.

pause
