@echo off
REM ========================================================================
REM DEPLOY LARAVEL TO HOSTINGER - SAFE VERSION
REM Script ini TIDAK akan mengubah file asli aplikasi
REM Semua file akan di-copy ke folder terpisah: HOSTINGER_UPLOAD
REM ========================================================================

color 0A
echo.
echo ╔══════════════════════════════════════════════════════════════════╗
echo ║     DEPLOY LARAVEL TO HOSTINGER - SAFE DEPLOYMENT SCRIPT        ║
echo ╚══════════════════════════════════════════════════════════════════╝
echo.
echo PENTING: Script ini TIDAK akan mengubah file asli Anda!
echo Semua file akan di-copy ke folder: HOSTINGER_UPLOAD
echo.
pause

REM ========================================================================
REM STEP 1: CREATE DEPLOYMENT FOLDER
REM ========================================================================

echo.
echo [STEP 1/12] Membuat folder deployment...
echo.

if exist HOSTINGER_UPLOAD (
    echo Folder HOSTINGER_UPLOAD sudah ada. Menghapus folder lama...
    rmdir /s /q HOSTINGER_UPLOAD
    echo Folder lama dihapus.
)

mkdir HOSTINGER_UPLOAD
mkdir HOSTINGER_UPLOAD\laravel_app
mkdir HOSTINGER_UPLOAD\public_html

echo ✓ Folder deployment dibuat: HOSTINGER_UPLOAD
echo.

REM ========================================================================
REM STEP 2: COPY LARAVEL APP FOLDERS
REM ========================================================================

echo [STEP 2/12] Copying folder app...
xcopy /E /I /Y /Q app HOSTINGER_UPLOAD\laravel_app\app > nul
echo ✓ Folder app copied

echo [STEP 3/12] Copying folder bootstrap...
xcopy /E /I /Y /Q bootstrap HOSTINGER_UPLOAD\laravel_app\bootstrap > nul
echo ✓ Folder bootstrap copied

echo [STEP 4/12] Copying folder config...
xcopy /E /I /Y /Q config HOSTINGER_UPLOAD\laravel_app\config > nul
echo ✓ Folder config copied

echo [STEP 5/12] Copying folder database...
xcopy /E /I /Y /Q database HOSTINGER_UPLOAD\laravel_app\database > nul
echo ✓ Folder database copied

echo [STEP 6/12] Copying folder resources...
xcopy /E /I /Y /Q resources HOSTINGER_UPLOAD\laravel_app\resources > nul
echo ✓ Folder resources copied

echo [STEP 7/12] Copying folder routes...
xcopy /E /I /Y /Q routes HOSTINGER_UPLOAD\laravel_app\routes > nul
echo ✓ Folder routes copied (termasuk api.php, web.php, console.php, channels.php)

REM ========================================================================
REM STEP 3: CREATE STORAGE STRUCTURE
REM ========================================================================

echo [STEP 8/12] Membuat struktur folder storage...

mkdir HOSTINGER_UPLOAD\laravel_app\storage
mkdir HOSTINGER_UPLOAD\laravel_app\storage\app
mkdir HOSTINGER_UPLOAD\laravel_app\storage\app\public
mkdir HOSTINGER_UPLOAD\laravel_app\storage\framework
mkdir HOSTINGER_UPLOAD\laravel_app\storage\framework\cache
mkdir HOSTINGER_UPLOAD\laravel_app\storage\framework\cache\data
mkdir HOSTINGER_UPLOAD\laravel_app\storage\framework\sessions
mkdir HOSTINGER_UPLOAD\laravel_app\storage\framework\views
mkdir HOSTINGER_UPLOAD\laravel_app\storage\logs

REM Copy .gitignore files untuk struktur storage
if exist storage\app\.gitignore copy /Y storage\app\.gitignore HOSTINGER_UPLOAD\laravel_app\storage\app\.gitignore > nul
if exist storage\app\public\.gitignore copy /Y storage\app\public\.gitignore HOSTINGER_UPLOAD\laravel_app\storage\app\public\.gitignore > nul
if exist storage\framework\.gitignore copy /Y storage\framework\.gitignore HOSTINGER_UPLOAD\laravel_app\storage\framework\.gitignore > nul
if exist storage\framework\cache\.gitignore copy /Y storage\framework\cache\.gitignore HOSTINGER_UPLOAD\laravel_app\storage\framework\cache\.gitignore > nul
if exist storage\framework\cache\data\.gitignore copy /Y storage\framework\cache\data\.gitignore HOSTINGER_UPLOAD\laravel_app\storage\framework\cache\data\.gitignore > nul
if exist storage\framework\sessions\.gitignore copy /Y storage\framework\sessions\.gitignore HOSTINGER_UPLOAD\laravel_app\storage\framework\sessions\.gitignore > nul
if exist storage\framework\views\.gitignore copy /Y storage\framework\views\.gitignore HOSTINGER_UPLOAD\laravel_app\storage\framework\views\.gitignore > nul
if exist storage\logs\.gitignore copy /Y storage\logs\.gitignore HOSTINGER_UPLOAD\laravel_app\storage\logs\.gitignore > nul

echo ✓ Struktur storage dibuat

REM ========================================================================
REM STEP 4: COPY VENDOR FOLDER
REM ========================================================================

echo [STEP 9/12] Copying folder vendor (ini akan memakan waktu)...
xcopy /E /I /Y /Q vendor HOSTINGER_UPLOAD\laravel_app\vendor > nul
echo ✓ Folder vendor copied

REM ========================================================================
REM STEP 5: COPY ROOT FILES
REM ========================================================================

echo [STEP 10/12] Copying root files...

if exist artisan copy /Y artisan HOSTINGER_UPLOAD\laravel_app\artisan > nul
if exist composer.json copy /Y composer.json HOSTINGER_UPLOAD\laravel_app\composer.json > nul
if exist composer.lock copy /Y composer.lock HOSTINGER_UPLOAD\laravel_app\composer.lock > nul
if exist package.json copy /Y package.json HOSTINGER_UPLOAD\laravel_app\package.json > nul
if exist .gitignore copy /Y .gitignore HOSTINGER_UPLOAD\laravel_app\.gitignore > nul
if exist .gitattributes copy /Y .gitattributes HOSTINGER_UPLOAD\laravel_app\.gitattributes > nul

echo ✓ Root files copied

REM ========================================================================
REM STEP 6: CREATE .ENV FOR HOSTINGER
REM ========================================================================

echo [STEP 11/12] Membuat file .env untuk Hostinger...

if exist .env.hostinger (
    copy /Y .env.hostinger HOSTINGER_UPLOAD\laravel_app\.env > nul
    echo ✓ File .env dibuat dari .env.hostinger
    echo.
    echo ⚠️  PENTING: Edit file HOSTINGER_UPLOAD\laravel_app\.env
    echo    Ganti DB_PASSWORD dengan password database Hostinger Anda!
    echo.
) else (
    echo ⚠️  WARNING: File .env.hostinger tidak ditemukan!
    echo    Anda harus membuat file .env secara manual di folder HOSTINGER_UPLOAD\laravel_app\
    echo.
)

REM ========================================================================
REM STEP 7: COPY PUBLIC FILES
REM ========================================================================

echo [STEP 12/12] Copying public files ke public_html...

xcopy /E /I /Y /Q public\* HOSTINGER_UPLOAD\public_html > nul

echo ✓ Public files copied ke public_html

REM ========================================================================
REM STEP 8: CREATE README AND INSTRUCTIONS
REM ========================================================================

echo.
echo Membuat file README dan instruksi...
echo.

REM Create README for laravel_app
(
echo # LARAVEL APP - READY FOR HOSTINGER
echo.
echo ## FOLDER INI SIAP UNTUK DI-UPLOAD KE HOSTINGER
echo.
echo ### LANGKAH UPLOAD:
echo.
echo 1. EDIT FILE .env
echo    - Buka file: .env
echo    - Cari: DB_PASSWORD=GANTI_DENGAN_PASSWORD_DATABASE_HOSTINGER
echo    - Ganti dengan password database Hostinger yang benar
echo    - SAVE FILE!
echo.
echo 2. COMPRESS FOLDER INI
echo    - Klik kanan folder laravel_app
echo    - Send to ^> Compressed ^(zipped^) folder
echo    - Atau gunakan WinRAR/7-Zip
echo.
echo 3. UPLOAD KE HOSTINGER
echo    - Login Hostinger File Manager
echo    - Navigate ke: /home/u127727849/domains/hmtourtravel.com/
echo    - Hapus folder laravel_app lama ^(jika ada^)
echo    - Upload laravel_app.zip
echo    - Extract ZIP
echo    - Hapus ZIP file
echo.
echo 4. SET PERMISSION VIA SSH
echo    ```
echo    ssh u127727849@hmtourtravel.com
echo    cd /home/u127727849/domains/hmtourtravel.com/laravel_app
echo    chmod -R 775 storage bootstrap/cache
echo    chown -R u127727849:u127727849 storage bootstrap/cache
echo    ```
echo.
echo 5. RUN ARTISAN COMMANDS
echo    ```
echo    cd /home/u127727849/domains/hmtourtravel.com/laravel_app
echo    /opt/alt/php82/usr/bin/php artisan config:clear
echo    /opt/alt/php82/usr/bin/php artisan cache:clear
echo    /opt/alt/php82/usr/bin/php artisan route:clear
echo    /opt/alt/php82/usr/bin/php artisan migrate --force
echo    /opt/alt/php82/usr/bin/php artisan db:seed --class=TravelPermissionSeeder --force
echo    /opt/alt/php82/usr/bin/php artisan storage:link
echo    ```
echo.
echo 6. TEST WEBSITE
echo    https://hmtourtravel.com
echo.
echo ### FILE PENTING:
echo - .env: Konfigurasi database ^(EDIT DB_PASSWORD!^)
echo - routes/: Semua route files ^(web.php, api.php, console.php, channels.php^)
echo - storage/: Folder writable ^(permission 775^)
echo - bootstrap/cache/: Cache folder ^(permission 775^)
echo.
echo ### CATATAN:
echo - File .env HARUS di-edit sebelum upload!
echo - Password database HARUS diganti!
echo - Permission storage dan bootstrap/cache HARUS di-set!
echo - Artisan commands HARUS di-run setelah upload!
echo.
) > HOSTINGER_UPLOAD\laravel_app\README_UPLOAD.txt

REM Create README for public_html
(
echo # PUBLIC HTML FILES - READY FOR HOSTINGER
echo.
echo ## FOLDER INI BERISI FILE PUBLIC LARAVEL
echo.
echo ### LANGKAH UPLOAD:
echo.
echo 1. COMPRESS FOLDER INI
echo    - Klik kanan folder public_html
echo    - Send to ^> Compressed ^(zipped^) folder
echo.
echo 2. UPLOAD KE HOSTINGER
echo    - Login Hostinger File Manager
echo    - Navigate ke: /home/u127727849/domains/hmtourtravel.com/public_html/
echo    - Upload semua file dari folder ini
echo    - ATAU upload ZIP dan extract
echo.
echo 3. PASTIKAN FILE INI ADA:
echo    - index.php ^(Laravel entry point^)
echo    - .htaccess ^(URL rewriting^)
echo    - css/ ^(folder CSS^)
echo    - js/ ^(folder JavaScript^)
echo    - images/ ^(folder images^)
echo.
echo ### CATATAN:
echo - File index.php HARUS mengarah ke ../laravel_app/public/index.php
echo - File .htaccess HARUS dikonfigurasi untuk LiteSpeed
echo - Jangan upload file .env ke public_html!
echo.
) > HOSTINGER_UPLOAD\public_html\README_UPLOAD.txt

REM Create main README
(
echo ╔══════════════════════════════════════════════════════════════════╗
echo ║          DEPLOYMENT PACKAGE - READY FOR HOSTINGER                ║
echo ╚══════════════════════════════════════════════════════════════════╝
echo.
echo FOLDER INI BERISI:
echo.
echo 1. laravel_app/
echo    - Semua file aplikasi Laravel
echo    - Folder: app, bootstrap, config, database, resources, routes, storage, vendor
echo    - File: artisan, composer.json, .env, dll
echo    - EDIT .env SEBELUM UPLOAD!
echo.
echo 2. public_html/
echo    - File public Laravel
echo    - File: index.php, .htaccess, css, js, images
echo    - Upload ke folder public_html di server
echo.
echo ═══════════════════════════════════════════════════════════════════
echo LANGKAH DEPLOYMENT:
echo ═══════════════════════════════════════════════════════════════════
echo.
echo STEP 1: EDIT .env
echo ────────────────────────────────────────────────────────────────
echo File: laravel_app\.env
echo Cari: DB_PASSWORD=GANTI_DENGAN_PASSWORD_DATABASE_HOSTINGER
echo Ganti dengan password database Hostinger yang benar
echo SAVE!
echo.
echo STEP 2: COMPRESS
echo ────────────────────────────────────────────────────────────────
echo Compress folder laravel_app ke ZIP
echo Compress folder public_html ke ZIP ^(opsional^)
echo.
echo STEP 3: UPLOAD laravel_app
echo ────────────────────────────────────────────────────────────────
echo Login Hostinger File Manager
echo Navigate ke: /home/u127727849/domains/hmtourtravel.com/
echo Hapus folder laravel_app lama ^(jika ada^)
echo Upload laravel_app.zip
echo Extract ZIP
echo.
echo STEP 4: UPLOAD public_html
echo ────────────────────────────────────────────────────────────────
echo Navigate ke: /home/u127727849/domains/hmtourtravel.com/public_html/
echo Upload semua file dari folder public_html
echo ^(File index.php dan .htaccess sudah ada, skip jika tidak berubah^)
echo.
echo STEP 5: SET PERMISSION ^(VIA SSH^)
echo ────────────────────────────────────────────────────────────────
echo ssh u127727849@hmtourtravel.com
echo cd /home/u127727849/domains/hmtourtravel.com/laravel_app
echo chmod -R 775 storage bootstrap/cache
echo chown -R u127727849:u127727849 storage bootstrap/cache
echo.
echo STEP 6: RUN ARTISAN COMMANDS ^(VIA SSH^)
echo ────────────────────────────────────────────────────────────────
echo cd /home/u127727849/domains/hmtourtravel.com/laravel_app
echo /opt/alt/php82/usr/bin/php artisan config:clear
echo /opt/alt/php82/usr/bin/php artisan cache:clear
echo /opt/alt/php82/usr/bin/php artisan route:clear
echo /opt/alt/php82/usr/bin/php artisan migrate --force
echo /opt/alt/php82/usr/bin/php artisan db:seed --class=TravelPermissionSeeder --force
echo /opt/alt/php82/usr/bin/php artisan storage:link
echo.
echo STEP 7: TEST WEBSITE
echo ────────────────────────────────────────────────────────────────
echo https://hmtourtravel.com
echo.
echo ═══════════════════════════════════════════════════════════════════
echo TROUBLESHOOTING:
echo ═══════════════════════════════════════════════════════════════════
echo.
echo Jika ada error 500:
echo 1. Upload file: public_html/debug-error.php ke server
echo 2. Akses: https://hmtourtravel.com/debug-error.php
echo 3. Lihat error dan solusinya
echo.
echo Jika database connection error:
echo 1. Cek password di laravel_app/.env
echo 2. Reset password database di Hostinger Control Panel
echo 3. Update .env dengan password yang benar
echo 4. Clear cache: php artisan config:clear
echo.
echo ═══════════════════════════════════════════════════════════════════
echo INFO SERVER:
echo ═══════════════════════════════════════════════════════════════════
echo Domain: hmtourtravel.com
echo Server Path: /home/u127727849/domains/hmtourtravel.com/
echo Database: u127727849_morra
echo DB User: u127727849_morra
echo DB Host: localhost
echo SSH: u127727849@hmtourtravel.com
echo PHP: 8.2 atau 8.3 ^(gunakan /opt/alt/php82/usr/bin/php^)
echo.
echo ═══════════════════════════════════════════════════════════════════
echo SELAMAT DEPLOY! 🚀
echo ═══════════════════════════════════════════════════════════════════
) > HOSTINGER_UPLOAD\README_DEPLOYMENT.txt

echo ✓ README files created

REM ========================================================================
REM STEP 9: VERIFICATION
REM ========================================================================

echo.
echo ╔══════════════════════════════════════════════════════════════════╗
echo ║                         VERIFICATION                             ║
echo ╚══════════════════════════════════════════════════════════════════╝
echo.

echo Checking important files...
echo.

REM Check laravel_app files
if exist HOSTINGER_UPLOAD\laravel_app\artisan (
    echo ✓ artisan
) else (
    echo ✗ artisan MISSING!
)

if exist HOSTINGER_UPLOAD\laravel_app\composer.json (
    echo ✓ composer.json
) else (
    echo ✗ composer.json MISSING!
)

if exist HOSTINGER_UPLOAD\laravel_app\.env (
    echo ✓ .env
) else (
    echo ✗ .env MISSING!
)

if exist HOSTINGER_UPLOAD\laravel_app\routes\web.php (
    echo ✓ routes\web.php
) else (
    echo ✗ routes\web.php MISSING!
)

if exist HOSTINGER_UPLOAD\laravel_app\routes\api.php (
    echo ✓ routes\api.php
) else (
    echo ✗ routes\api.php MISSING!
)

if exist HOSTINGER_UPLOAD\laravel_app\routes\console.php (
    echo ✓ routes\console.php
) else (
    echo ✗ routes\console.php MISSING!
)

if exist HOSTINGER_UPLOAD\laravel_app\routes\channels.php (
    echo ✓ routes\channels.php
) else (
    echo ✗ routes\channels.php MISSING!
)

if exist HOSTINGER_UPLOAD\laravel_app\app\Providers\AppServiceProvider.php (
    echo ✓ app\Providers\AppServiceProvider.php
) else (
    echo ✗ app\Providers\AppServiceProvider.php MISSING!
)

if exist HOSTINGER_UPLOAD\laravel_app\vendor\autoload.php (
    echo ✓ vendor\autoload.php
) else (
    echo ✗ vendor\autoload.php MISSING!
)

if exist HOSTINGER_UPLOAD\laravel_app\storage\logs (
    echo ✓ storage\logs
) else (
    echo ✗ storage\logs MISSING!
)

if exist HOSTINGER_UPLOAD\laravel_app\bootstrap\cache (
    echo ✓ bootstrap\cache
) else (
    echo ✗ bootstrap\cache MISSING!
)

REM Check public_html files
if exist HOSTINGER_UPLOAD\public_html\index.php (
    echo ✓ public_html\index.php
) else (
    echo ✗ public_html\index.php MISSING!
)

if exist HOSTINGER_UPLOAD\public_html\.htaccess (
    echo ✓ public_html\.htaccess
) else (
    echo ✗ public_html\.htaccess MISSING!
)

REM ========================================================================
REM STEP 10: SUMMARY
REM ========================================================================

echo.
echo ╔══════════════════════════════════════════════════════════════════╗
echo ║                            SUMMARY                               ║
echo ╚══════════════════════════════════════════════════════════════════╝
echo.
echo ✓ Deployment package berhasil dibuat!
echo.
echo Lokasi: HOSTINGER_UPLOAD\
echo.
echo Folder yang dibuat:
echo   - HOSTINGER_UPLOAD\laravel_app\     (aplikasi Laravel lengkap)
echo   - HOSTINGER_UPLOAD\public_html\     (file public)
echo.
echo File README:
echo   - HOSTINGER_UPLOAD\README_DEPLOYMENT.txt        (panduan utama)
echo   - HOSTINGER_UPLOAD\laravel_app\README_UPLOAD.txt
echo   - HOSTINGER_UPLOAD\public_html\README_UPLOAD.txt
echo.
echo ═══════════════════════════════════════════════════════════════════
echo LANGKAH SELANJUTNYA:
echo ═══════════════════════════════════════════════════════════════════
echo.
echo 1. Buka folder: HOSTINGER_UPLOAD\laravel_app\
echo 2. Edit file: .env
echo 3. Ganti: DB_PASSWORD dengan password database Hostinger
echo 4. Compress laravel_app ke ZIP
echo 5. Upload ke Hostinger
echo 6. Set permission dan run artisan commands
echo 7. Test: https://hmtourtravel.com
echo.
echo Baca file: HOSTINGER_UPLOAD\README_DEPLOYMENT.txt untuk panduan lengkap
echo.
echo ═══════════════════════════════════════════════════════════════════
echo CATATAN PENTING:
echo ═══════════════════════════════════════════════════════════════════
echo.
echo ✓ File asli aplikasi Anda TIDAK BERUBAH!
echo ✓ Semua file ada di folder terpisah: HOSTINGER_UPLOAD
echo ✓ Anda bisa jalankan script ini berkali-kali tanpa khawatir
echo ✓ File .env di root folder tetap untuk localhost
echo ✓ File .env di HOSTINGER_UPLOAD untuk server
echo.
echo ╔══════════════════════════════════════════════════════════════════╗
echo ║                         DEPLOYMENT READY!                        ║
echo ╚══════════════════════════════════════════════════════════════════╝
echo.

REM Open folder in explorer
echo Membuka folder HOSTINGER_UPLOAD...
start explorer HOSTINGER_UPLOAD

echo.
echo Script selesai! Silakan lanjutkan dengan edit .env dan upload.
echo.
pause
