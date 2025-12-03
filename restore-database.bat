@echo off
REM ============================================
REM Database Restore Script
REM ============================================
REM Bu script veritabani yedeginden geri yukleme yapar
REM Kullanim: restore-database.bat [backup_file.sql]

echo.
echo ============================================
echo Database Restore
echo ============================================
echo.

REM Renk kodlari
set "GREEN=[92m"
set "RED=[91m"
set "YELLOW=[93m"
set "NC=[0m"

REM Parametre kontrolu
if "%~1"=="" (
    echo %YELLOW%Kullanim: restore-database.bat [backup_file.sql]%NC%
    echo.
    echo Mevcut yedek dosyalari:
    echo.
    dir /b database-backups\*.sql
    echo.
    set /p BACKUP_FILE="Geri yuklenecek dosya adi (database-backups\ ile birlikte): "
) else (
    set BACKUP_FILE=%~1
)

REM Dosya kontrolu
if not exist "%BACKUP_FILE%" (
    echo %RED%HATA: Yedek dosyasi bulunamadi: %BACKUP_FILE%%NC%
    exit /b 1
)

echo.
echo %YELLOW%Geri yuklenecek dosya: %BACKUP_FILE%%NC%
echo.

REM Dosya boyutunu goster
for %%A in ("%BACKUP_FILE%") do (
    echo Dosya boyutu: %%~zA bytes
    echo Dosya tarihi: %%~tA
)
echo.

echo %RED%UYARI: Bu islem mevcut veritabanini tamamen silecek!%NC%
echo %RED%Tum mevcut veriler kaybolacak!%NC%
echo.

set /p CONFIRM="Devam etmek istediginizden emin misiniz? (E/H): "
if /i not "%CONFIRM%"=="E" (
    echo Islem iptal edildi.
    exit /b 0
)
echo.

echo %YELLOW%[1/4] Mevcut veritabaninin yedegi aliniyor...%NC%
call backup-database.bat
if errorlevel 1 (
    echo %RED%HATA: Guvenlik yedegi alinamadi!%NC%
    echo Restore islemi iptal edildi.
    exit /b 1
)
echo %GREEN%Guvenlik yedegi alindi!%NC%
echo.

echo %YELLOW%[2/4] Uygulama maintenance moduna aliniyor...%NC%
php artisan down --retry=60
echo %GREEN%Maintenance modu aktif!%NC%
echo.

echo %YELLOW%[3/4] Veritabani geri yukleniyor...%NC%
echo.

REM MySQL restore
G:\xampp\mysql\bin\mysql.exe -u root pubg_community < "%BACKUP_FILE%" 2>&1

if errorlevel 1 (
    echo %RED%HATA: Veritabani geri yuklenemedi!%NC%
    echo.
    echo MySQL hatasi olustu. Lutfen:
    echo 1. MySQL servisinin calistigini kontrol edin
    echo 2. Yedek dosyasinin bozuk olmadigini kontrol edin
    echo 3. Veritabani kullanici yetkilerini kontrol edin
    echo.
    php artisan up
    exit /b 1
)

echo %GREEN%Veritabani basariyla geri yuklendi!%NC%
echo.

echo %YELLOW%[4/4] Cache temizleniyor...%NC%
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
echo %GREEN%Cache temizlendi!%NC%
echo.

echo %YELLOW%Uygulama aktif hale getiriliyor...%NC%
php artisan up
echo %GREEN%Uygulama aktif!%NC%
echo.

echo.
echo %GREEN%============================================%NC%
echo %GREEN%Restore Basariyla Tamamlandi!%NC%
echo %GREEN%============================================%NC%
echo.
echo Geri yuklenen dosya: %BACKUP_FILE%
echo.
echo %YELLOW%Sonraki Adimlar:%NC%
echo 1. Uygulamayi test edin
echo 2. Verilerin dogru yukledigini kontrol edin
echo 3. Log dosyalarini inceleyin
echo.

pause
