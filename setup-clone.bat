@echo off
REM ========================================
REM Setup Clone Project - MORRA ERP
REM ========================================
REM Script ini akan mengkonfigurasi project
REM yang di-clone dengan nama folder baru
REM ========================================

echo.
echo ========================================
echo   SETUP CLONE PROJECT - MORRA ERP
echo ========================================
echo.
echo Script ini akan:
echo 1. Update APP_URL di .env
echo 2. Update SESSION_PATH di .env  
echo 3. Clear semua cache Laravel
echo 4. Regenerate config cache
echo.
echo ========================================
echo.

REM Deteksi nama folder otomatis
for %%I in (.) do set CURRENT_FOLDER=%%~nxI
echo Folder saat ini: %CURRENT_FOLDER%
echo.

set /p CONFIRM="Gunakan nama folder '%CURRENT_FOLDER%'? (Y/n): "
if /i "%CONFIRM%"=="n" (
    set /p FOLDER_NAME="Masukkan nama folder project: "
) else (
    set FOLDER_NAME=%CURRENT_FOLDER%
)

echo.
echo ========================================
echo Konfigurasi yang akan diterapkan:
echo ========================================
echo Nama Folder  : %FOLDER_NAME%
echo APP_URL      : http://localhost/%FOLDER_NAME%
echo SESSION_PATH : /%FOLDER_NAME%
echo ========================================
echo.

set /p FINAL_CONFIRM="Lanjutkan? (Y/n): "
if /i "%FINAL_CONFIRM%"=="n" (
    echo.
    echo Setup dibatalkan.
    pause
    exit /b
)

echo.
echo [1/5] Backup .env file...
copy .env .env.backup.%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2% >nul 2>&1
echo       Backup tersimpan sebagai .env.backup.*

echo.
echo [2/5] Updating .env file...

REM Update APP_URL
powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=http://localhost/%FOLDER_NAME%' | Set-Content .env"
echo       - APP_URL updated

REM Update SESSION_PATH
powershell -Command "(Get-Content .env) -replace 'SESSION_PATH=.*', 'SESSION_PATH=/%FOLDER_NAME%' | Set-Content .env"
echo       - SESSION_PATH updated

REM Update SESSION_DOMAIN untuk localhost
powershell -Command "(Get-Content .env) -replace 'SESSION_DOMAIN=.*', 'SESSION_DOMAIN=null' | Set-Content .env"
echo       - SESSION_DOMAIN updated

REM Update SESSION_SECURE_COOKIE untuk localhost
powershell -Command "(Get-Content .env) -replace 'SESSION_SECURE_COOKIE=.*', 'SESSION_SECURE_COOKIE=false' | Set-Content .env"
echo       - SESSION_SECURE_COOKIE updated

echo.
echo [3/5] Clearing Laravel cache...
php artisan config:clear >nul 2>&1
echo       - Config cache cleared
php artisan cache:clear >nul 2>&1
echo       - Application cache cleared
php artisan route:clear >nul 2>&1
echo       - Route cache cleared
php artisan view:clear >nul 2>&1
echo       - View cache cleared

echo.
echo [4/5] Regenerating config cache...
php artisan config:cache >nul 2>&1
echo       - Config cache regenerated

echo.
echo [5/5] Creating storage link...
php artisan storage:link >nul 2>&1
echo       - Storage link created

echo.
echo ========================================
echo   SETUP SELESAI!
echo ========================================
echo.
echo Konfigurasi baru:
echo   APP_URL      : http://localhost/%FOLDER_NAME%
echo   SESSION_PATH : /%FOLDER_NAME%
echo.
echo LANGKAH SELANJUTNYA:
echo.
echo 1. RESTART WEB SERVER
echo    - Jika XAMPP: Stop dan Start Apache
echo    - Jika Artisan: Ctrl+C lalu php artisan serve
echo.
echo 2. CLEAR BROWSER CACHE
echo    - Tekan Ctrl+Shift+Delete
echo    - Pilih "Cached images and files"
echo    - Klik "Clear data"
echo.
echo 3. AKSES PROJECT
echo    - URL: http://localhost/%FOLDER_NAME%/admin
echo    - Gunakan incognito mode untuk test pertama
echo.
echo 4. TEST MENU
echo    - Login ke sistem
echo    - Klik menu dari sidebar (contoh: Point of Sales)
echo    - Pastikan TIDAK ada nested layout
echo.
echo ========================================
echo.
echo File backup: .env.backup.*
echo Dokumentasi: FIX_INFINITE_MIRROR_COMPLETE.md
echo.
pause
