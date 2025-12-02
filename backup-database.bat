@echo off
REM PUBG Community - Veritabanı Yedekleme Script'i
REM Kullanım: backup-database.bat

echo ========================================
echo PUBG Community - Veritabanı Yedeği
echo ========================================
echo.

REM Tarih ve saat damgası
set TIMESTAMP=%date:~-4%%date:~3,2%%date:~0,2%-%time:~0,2%%time:~3,2%%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%

REM Yedek klasörü
set BACKUP_DIR=F:\Pubg\database-backups
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

REM Yedek dosya adı
set BACKUP_FILE=%BACKUP_DIR%\pubg_community_%TIMESTAMP%.sql

echo Yedek alınıyor...
echo Hedef: %BACKUP_FILE%
echo.

REM MySQL dump
G:\xampp\mysql\bin\mysqldump.exe -u root pubg_community > "%BACKUP_FILE%" 2>&1

if %ERRORLEVEL% EQU 0 (
    echo.
    echo [BAŞARILI] Veritabanı yedeği alındı!
    echo Dosya: %BACKUP_FILE%
    
    REM Dosya boyutunu göster
    for %%A in ("%BACKUP_FILE%") do echo Boyut: %%~zA bytes
    
    REM Eski yedekleri temizle (30 günden eski)
    echo.
    echo Eski yedekler temizleniyor (30+ gün)...
    forfiles /P "%BACKUP_DIR%" /M *.sql /D -30 /C "cmd /c del @path" 2>nul
    
) else (
    echo.
    echo [HATA] Yedek alınamadı!
    echo MySQL çalışmıyor olabilir.
    echo.
    echo Alternatif: Dosya yedeği alınıyor...
    
    REM Dosya yedeği
    set FILE_BACKUP=%BACKUP_DIR%\files_%TIMESTAMP%
    xcopy /E /I /Y "G:\xampp\mysql\data\pubg_community" "%FILE_BACKUP%"
    
    if %ERRORLEVEL% EQU 0 (
        echo [BAŞARILI] Dosya yedeği alındı: %FILE_BACKUP%
    )
)

echo.
echo ========================================
pause
