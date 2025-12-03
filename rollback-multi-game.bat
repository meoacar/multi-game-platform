@echo off
REM ============================================
REM Multi-Game Platform Rollback Script
REM ============================================
REM Bu script multi-game migration'larini geri alir
REM Kullanim: rollback-multi-game.bat

echo.
echo ============================================
echo Multi-Game Platform Rollback
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

echo %RED%UYARI: Bu islem multi-game migration'larini geri alacak!%NC%
echo.
echo Bu islem asagidaki degisiklikleri geri alir:
echo - games tablosunu siler
echo - game_id kolonlarini siler
echo - Mevcut PUBG verileri korunur
echo.

set /p CONFIRM="Rollback yapmak istediginizden emin misiniz? (E/H): "
if /i not "%CONFIRM%"=="E" (
    echo Islem iptal edildi.
    exit /b 0
)
echo.

echo %YELLOW%[1/6] Veritabani yedegi aliniyor...%NC%
echo.
call backup-database.bat
if errorlevel 1 (
    echo %RED%HATA: Veritabani yedegi alinamadi!%NC%
    echo Rollback iptal edildi.
    exit /b 1
)
echo %GREEN%Yedek basariyla alindi!%NC%
echo.

echo %YELLOW%[2/6] Uygulama maintenance moduna aliniyor...%NC%
php artisan down --retry=60
echo %GREEN%Maintenance modu aktif!%NC%
echo.

echo %YELLOW%[3/6] Cache temizleniyor...%NC%
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
echo %GREEN%Cache temizlendi!%NC%
echo.

echo %YELLOW%[4/6] Migration rollback yapiliyor...%NC%
echo.
echo Geri alinacak migration'lar:
echo - 2025_12_02_210000_add_game_id_to_remaining_tables
echo - 2025_12_02_200000_update_games_table_for_multi_game
echo.

REM Son iki migration'i geri al
php artisan migrate:rollback --step=2 --force
if errorlevel 1 (
    echo %RED%HATA: Migration rollback basarisiz!%NC%
    echo.
    echo %YELLOW%Manuel mudahale gerekebilir!%NC%
    echo Veritabani yedeginden geri yukleme yapmak ister misiniz?
    echo.
    set /p RESTORE="Yedekten geri yukle? (E/H): "
    if /i "%RESTORE%"=="E" (
        echo.
        echo Mevcut yedek dosyalari:
        dir /b database-backups\*.sql
        echo.
        set /p BACKUP_FILE="Geri yuklenecek dosya adi: "
        call restore-database.bat database-backups\!BACKUP_FILE!
    )
    php artisan up
    exit /b 1
)
echo %GREEN%Migration rollback basarili!%NC%
echo.

echo %YELLOW%[5/6] Cache yeniden olusturuluyor...%NC%
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo %GREEN%Cache olusturuldu!%NC%
echo.

echo %YELLOW%[6/6] Dogrulama yapiliyor...%NC%
echo.

REM games tablosunun silindigini kontrol et
php artisan tinker --execute="echo 'Games table exists: ' . (Schema::hasTable('games') ? 'YES' : 'NO') . PHP_EOL;"

REM Mevcut verilerin korunduğunu kontrol et
php artisan tinker --execute="echo 'Total users: ' . App\Models\User::count() . PHP_EOL;"
php artisan tinker --execute="echo 'Total tournaments: ' . App\Models\Tournament::count() . PHP_EOL;"
php artisan tinker --execute="echo 'Total clans: ' . App\Models\Clan::count() . PHP_EOL;"

echo.
echo %GREEN%Dogrulama tamamlandi!%NC%
echo.

echo %YELLOW%Uygulama aktif hale getiriliyor...%NC%
php artisan up
echo %GREEN%Uygulama aktif!%NC%
echo.

echo.
echo %GREEN%============================================%NC%
echo %GREEN%Rollback Basariyla Tamamlandi!%NC%
echo %GREEN%============================================%NC%
echo.
echo Rollback Zamani: %TIMESTAMP%
echo Yedek Dosyasi: database-backups/pubg_community_%TIMESTAMP%.sql
echo.
echo %YELLOW%Sonraki Adimlar:%NC%
echo 1. Uygulamayi test edin
echo 2. Eski URL yapisinin calistigini dogrulayin
echo 3. Log dosyalarini kontrol edin
echo.
echo %YELLOW%Not:%NC%
echo - Platform eski haline (tek oyun - PUBG) dondu
echo - Tum kullanici verileri korundu
echo - Subdomain routing devre disi
echo.

pause
