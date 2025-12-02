@echo off
REM PUBG Community - Veritabanı Geri Yükleme Script'i
REM Kullanım: restore-database.bat [yedek_dosyası.sql]

echo ========================================
echo PUBG Community - Veritabanı Geri Yükleme
echo ========================================
echo.

if "%~1"=="" (
    echo KULLANIM: restore-database.bat [yedek_dosyası.sql]
    echo.
    echo Örnek: restore-database.bat F:\Pubg\database-backups\pubg_community_20251202.sql
    echo.
    pause
    exit /b 1
)

set BACKUP_FILE=%~1

if not exist "%BACKUP_FILE%" (
    echo [HATA] Yedek dosyası bulunamadı: %BACKUP_FILE%
    pause
    exit /b 1
)

echo Yedek dosyası: %BACKUP_FILE%
echo.
echo UYARI: Bu işlem mevcut veritabanını silecek!
echo.
set /p CONFIRM=Devam etmek istiyor musunuz? (E/H): 

if /i not "%CONFIRM%"=="E" (
    echo İşlem iptal edildi.
    pause
    exit /b 0
)

echo.
echo Veritabanı geri yükleniyor...

REM Veritabanını sil ve yeniden oluştur
G:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE IF EXISTS pubg_community; CREATE DATABASE pubg_community CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

REM Yedekten geri yükle
G:\xampp\mysql\bin\mysql.exe -u root pubg_community < "%BACKUP_FILE%"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo [BAŞARILI] Veritabanı geri yüklendi!
) else (
    echo.
    echo [HATA] Geri yükleme başarısız!
)

echo.
pause
