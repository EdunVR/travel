@echo off
REM Script untuk memisahkan file Laravel untuk upload ke Hostinger
REM Jalankan: split-for-hostinger.bat

echo ==========================================
echo Memisahkan File untuk Upload Hostinger
echo ==========================================
echo.

REM Buat folder untuk upload
echo 1. Membuat folder upload...
if not exist "hostinger_upload" mkdir hostinger_upload
if not exist "hostinger_upload\laravel_app" mkdir hostinger_upload\laravel_app
if not exist "hostinger_upload\public_html" mkdir hostinger_upload\public_html
echo [OK] Folder dibuat
echo.

REM Copy semua file KECUALI public ke laravel_app
echo 2. Menyalin file aplikasi ke laravel_app...
echo    (Ini akan memakan waktu beberapa menit...)

xcopy /E /I /Y /EXCLUDE:exclude_list.txt app hostinger_upload\laravel_app\app
xcopy /E /I /Y /EXCLUDE:exclude_list.txt bootstrap hostinger_upload\laravel_app\bootstrap
xcopy /E /I /Y /EXCLUDE:exclude_list.txt config hostinger_upload\laravel_app\config
xcopy /E /I /Y /EXCLUDE:exclude_list.txt database hostinger_upload\laravel_app\database
xcopy /E /I /Y /EXCLUDE:exclude_list.txt resources hostinger_upload\laravel_app\resources
xcopy /E /I /Y /EXCLUDE:exclude_list.txt routes hostinger_upload\laravel_app\routes
xcopy /E /I /Y /EXCLUDE:exclude_list.txt storage hostinger_upload\laravel_app\storage
xcopy /E /I /Y /EXCLUDE:exclude_list.txt vendor hostinger_upload\laravel_app\vendor

REM Copy file-file root
copy /Y artisan hostinger_upload\laravel_app\
copy /Y composer.json hostinger_upload\laravel_app\
copy /Y composer.lock hostinger_upload\laravel_app\
copy /Y package.json hostinger_upload\laravel_app\
copy /Y .htaccess hostinger_upload\laravel_app\ 2>nul

REM Copy .env.production sebagai .env
if exist .env.production (
    copy /Y .env.production hostinger_upload\laravel_app\.env
    echo [OK] .env.production copied as .env
) else (
    echo [WARNING] .env.production not found! Please create it manually
)

echo [OK] File aplikasi disalin
echo.

REM Copy isi folder public ke public_html
echo 3. Menyalin file public ke public_html...
xcopy /E /I /Y public\* hostinger_upload\public_html\
echo [OK] File public disalin
echo.

REM Edit index.php
echo 4. Membuat index.php yang sudah diedit...
(
echo ^<?php
echo.
echo use Illuminate\Contracts\Http\Kernel;
echo use Illuminate\Http\Request;
echo.
echo define^('LARAVEL_START', microtime^(true^)^);
echo.
echo // Path sudah disesuaikan untuk Hostinger
echo require __DIR__.'/../laravel_app/vendor/autoload.php';
echo.
echo $app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';
echo.
echo $kernel = $app-^>make^(Kernel::class^);
echo.
echo $response = $kernel-^>handle^(
echo     $request = Request::capture^(^)
echo ^)-^>send^(^);
echo.
echo $kernel-^>terminate^($request, $response^);
) > hostinger_upload\public_html\index.php

echo [OK] index.php sudah diedit dengan path yang benar
echo.

REM Bersihkan folder storage/logs
echo 5. Membersihkan logs...
del /Q hostinger_upload\laravel_app\storage\logs\*.log 2>nul
echo [OK] Logs dibersihkan
echo.

REM Copy setup script
echo 6. Menyalin setup script...
if exist public_html\setup-hostinger.php (
    copy /Y public_html\setup-hostinger.php hostinger_upload\public_html\
    echo [OK] setup-hostinger.php disalin
) else (
    echo [WARNING] setup-hostinger.php not found
)
echo.

REM Buat file instruksi
echo 7. Membuat file instruksi...
(
echo ==========================================
echo INSTRUKSI UPLOAD KE HOSTINGER
echo ==========================================
echo.
echo 1. UPLOAD FOLDER laravel_app:
echo    - Login ke File Manager Hostinger
echo    - Buat folder "laravel_app" di /home/u123456789/
echo    - Upload SEMUA isi folder "laravel_app" ke folder tersebut
echo.
echo 2. UPLOAD FOLDER public_html:
echo    - Upload SEMUA isi folder "public_html" ke /home/u123456789/public_html/
echo    - REPLACE jika ada file yang sama
echo.
echo 3. SETUP DATABASE:
echo    - Buat database MySQL di hPanel Hostinger
echo    - Catat: DB_NAME, DB_USER, DB_PASSWORD
echo    - Edit file laravel_app/.env dengan credentials tersebut
echo.
echo 4. JALANKAN SETUP:
echo    Opsi A - Via Browser:
echo    - Akses: https://your-domain.com/setup-hostinger.php?password=xxx
echo    - Ikuti instruksi
echo    - HAPUS file setup-hostinger.php setelah selesai
echo.
echo    Opsi B - Via SSH:
echo    ssh u123456789@your-domain.com
echo    cd /home/u123456789/laravel_app
echo    php artisan key:generate
echo    php artisan migrate --force
echo    php artisan db:seed --class=TravelPermissionSeeder --force
echo    php artisan storage:link
echo    php artisan optimize
echo.
echo 5. SET PERMISSIONS:
echo    chmod -R 755 storage bootstrap/cache
echo    chmod -R 775 storage/logs storage/framework
echo    chmod 600 .env
echo.
echo 6. TESTING:
echo    - Akses: https://your-domain.com
echo    - Test login
echo    - Test fitur upload
echo    - Test Fonnte WhatsApp
echo.
echo ==========================================
echo STRUKTUR FOLDER DI SERVER:
echo ==========================================
echo.
echo /home/u123456789/
echo ├── laravel_app/          ^<- Upload folder "laravel_app" ke sini
echo │   ├── app/
echo │   ├── bootstrap/
echo │   ├── config/
echo │   ├── .env
echo │   └── ...
echo └── public_html/          ^<- Upload folder "public_html" ke sini
echo     ├── index.php
echo     ├── .htaccess
echo     └── ...
echo.
echo ==========================================
) > hostinger_upload\INSTRUKSI_UPLOAD.txt

echo [OK] File instruksi dibuat
echo.

echo ==========================================
echo SELESAI!
echo ==========================================
echo.
echo Folder "hostinger_upload" sudah siap dengan struktur:
echo.
echo hostinger_upload\
echo ├── laravel_app\      ^<- Upload ke /home/u123456789/laravel_app/
echo ├── public_html\      ^<- Upload ke /home/u123456789/public_html/
echo └── INSTRUKSI_UPLOAD.txt
echo.
echo LANGKAH SELANJUTNYA:
echo 1. Baca file INSTRUKSI_UPLOAD.txt
echo 2. Compress folder laravel_app menjadi ZIP
echo 3. Compress folder public_html menjadi ZIP
echo 4. Upload ke Hostinger sesuai instruksi
echo.
echo CATATAN PENTING:
echo - Pastikan .env di laravel_app sudah berisi credentials Hostinger
echo - Jangan lupa edit DB_HOST menjadi "localhost" (bukan 127.0.0.1)
echo - Test Fonnte token masih aktif
echo.
pause
