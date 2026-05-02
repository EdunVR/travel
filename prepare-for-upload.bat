@echo off
REM Script untuk mempersiapkan project Laravel sebelum upload ke Hostinger (Windows)
REM Jalankan: prepare-for-upload.bat

echo ==========================================
echo Persiapan Upload ke Hostinger
echo ==========================================
echo.

REM 1. Clear cache
echo 1. Clearing cache...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo [OK] Cache cleared
echo.

REM 2. Install dependencies production
echo 2. Installing production dependencies...
call composer install --optimize-autoloader --no-dev
echo [OK] Dependencies installed
echo.

REM 3. Build assets
echo 3. Building production assets...
call npm install
call npm run build
echo [OK] Assets built
echo.

REM 4. Create production .env
echo 4. Creating production .env template...
if not exist .env.production (
    copy .env.production.example .env.production
    echo [OK] .env.production created - EDIT FILE INI SEBELUM UPLOAD!
) else (
    echo [WARNING] .env.production already exists - skipping
)
echo.

echo ==========================================
echo Persiapan Selesai!
echo ==========================================
echo.
echo Langkah selanjutnya:
echo 1. Edit file .env.production dengan konfigurasi Hostinger
echo 2. Compress folder project (exclude: node_modules, .git, storage/logs, tests)
echo 3. Upload ke server Hostinger
echo 4. Ikuti PANDUAN_UPLOAD_HOSTINGER.md
echo.
echo Folder yang TIDAK perlu diupload:
echo - node_modules (akan di-install di server jika perlu)
echo - .git
echo - storage/logs/* (biarkan folder kosong)
echo - tests
echo - file .env lokal
echo.
pause
