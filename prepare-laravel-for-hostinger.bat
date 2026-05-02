@echo off
echo ========================================
echo PREPARE LARAVEL APP FOR HOSTINGER
echo ========================================
echo.

REM Set color for better visibility
color 0A

echo [1/10] Creating laravel_app folder...
if exist laravel_app (
    echo Folder laravel_app already exists. Deleting old folder...
    rmdir /s /q laravel_app
)
mkdir laravel_app
echo Done!
echo.

echo [2/10] Copying app folder...
xcopy /E /I /Y app laravel_app\app
echo Done!
echo.

echo [3/10] Copying bootstrap folder...
xcopy /E /I /Y bootstrap laravel_app\bootstrap
echo Done!
echo.

echo [4/10] Copying config folder...
xcopy /E /I /Y config laravel_app\config
echo Done!
echo.

echo [5/10] Copying database folder...
xcopy /E /I /Y database laravel_app\database
echo Done!
echo.

echo [6/10] Copying resources folder...
xcopy /E /I /Y resources laravel_app\resources
echo Done!
echo.

echo [7/10] Copying routes folder...
xcopy /E /I /Y routes laravel_app\routes
echo Done!
echo.

echo [8/10] Copying storage folder structure...
mkdir laravel_app\storage
mkdir laravel_app\storage\app
mkdir laravel_app\storage\app\public
mkdir laravel_app\storage\framework
mkdir laravel_app\storage\framework\cache
mkdir laravel_app\storage\framework\cache\data
mkdir laravel_app\storage\framework\sessions
mkdir laravel_app\storage\framework\views
mkdir laravel_app\storage\logs

REM Copy .gitignore files
if exist storage\app\.gitignore copy /Y storage\app\.gitignore laravel_app\storage\app\.gitignore
if exist storage\app\public\.gitignore copy /Y storage\app\public\.gitignore laravel_app\storage\app\public\.gitignore
if exist storage\framework\.gitignore copy /Y storage\framework\.gitignore laravel_app\storage\framework\.gitignore
if exist storage\framework\cache\.gitignore copy /Y storage\framework\cache\.gitignore laravel_app\storage\framework\cache\.gitignore
if exist storage\framework\cache\data\.gitignore copy /Y storage\framework\cache\data\.gitignore laravel_app\storage\framework\cache\data\.gitignore
if exist storage\framework\sessions\.gitignore copy /Y storage\framework\sessions\.gitignore laravel_app\storage\framework\sessions\.gitignore
if exist storage\framework\views\.gitignore copy /Y storage\framework\views\.gitignore laravel_app\storage\framework\views\.gitignore
if exist storage\logs\.gitignore copy /Y storage\logs\.gitignore laravel_app\storage\logs\.gitignore

echo Done!
echo.

echo [9/10] Copying vendor folder...
echo This may take a while...
xcopy /E /I /Y vendor laravel_app\vendor
echo Done!
echo.

echo [10/10] Copying root files...
if exist artisan copy /Y artisan laravel_app\artisan
if exist composer.json copy /Y composer.json laravel_app\composer.json
if exist composer.lock copy /Y composer.lock laravel_app\composer.lock
if exist package.json copy /Y package.json laravel_app\package.json
if exist .gitignore copy /Y .gitignore laravel_app\.gitignore
if exist .gitattributes copy /Y .gitattributes laravel_app\.gitattributes

REM Copy .env.hostinger as template
if exist .env.hostinger (
    copy /Y .env.hostinger laravel_app\.env.example
    echo .env.hostinger copied as .env.example
)

echo Done!
echo.

echo ========================================
echo CREATING .env FILE FOR HOSTINGER
echo ========================================
echo.

REM Create .env file from .env.hostinger
if exist .env.hostinger (
    copy /Y .env.hostinger laravel_app\.env
    echo .env file created from .env.hostinger
    echo.
    echo IMPORTANT: Edit laravel_app\.env and change:
    echo   - DB_PASSWORD (set your Hostinger database password)
    echo.
) else (
    echo WARNING: .env.hostinger not found!
    echo Please create .env file manually in laravel_app folder
    echo.
)

echo ========================================
echo CREATING README FOR UPLOAD
echo ========================================
echo.

(
echo # Laravel App for Hostinger
echo.
echo ## Upload Instructions:
echo.
echo 1. Compress this laravel_app folder to ZIP
echo 2. Upload ZIP to Hostinger via File Manager
echo 3. Extract to: /home/u127727849/domains/hmtourtravel.com/
echo 4. Edit .env file and set DB_PASSWORD
echo 5. Run via SSH:
echo    ```
echo    cd /home/u127727849/domains/hmtourtravel.com/laravel_app
echo    chmod -R 775 storage bootstrap/cache
echo    php artisan config:clear
echo    php artisan cache:clear
echo    php artisan migrate --force
echo    php artisan db:seed --class=TravelPermissionSeeder --force
echo    php artisan storage:link
echo    ```
echo.
echo ## Important Files:
echo - .env: Database configuration (EDIT DB_PASSWORD!)
echo - routes/: All route files (web.php, api.php, console.php, channels.php)
echo - storage/: Writable folders (need permission 775)
echo - bootstrap/cache/: Cache folder (need permission 775)
echo.
echo ## After Upload:
echo 1. Set permission: chmod -R 775 storage bootstrap/cache
echo 2. Clear cache: php artisan config:clear
echo 3. Run migration: php artisan migrate --force
echo 4. Test: https://hmtourtravel.com
) > laravel_app\README_UPLOAD.md

echo README_UPLOAD.md created!
echo.

echo ========================================
echo VERIFICATION
echo ========================================
echo.

echo Checking important files...
echo.

if exist laravel_app\artisan (
    echo [OK] artisan
) else (
    echo [MISSING] artisan
)

if exist laravel_app\composer.json (
    echo [OK] composer.json
) else (
    echo [MISSING] composer.json
)

if exist laravel_app\.env (
    echo [OK] .env
) else (
    echo [WARNING] .env - Please create manually!
)

if exist laravel_app\routes\web.php (
    echo [OK] routes\web.php
) else (
    echo [MISSING] routes\web.php
)

if exist laravel_app\routes\api.php (
    echo [OK] routes\api.php
) else (
    echo [MISSING] routes\api.php
)

if exist laravel_app\routes\console.php (
    echo [OK] routes\console.php
) else (
    echo [MISSING] routes\console.php
)

if exist laravel_app\routes\channels.php (
    echo [OK] routes\channels.php
) else (
    echo [MISSING] routes\channels.php
)

if exist laravel_app\app\Providers\AppServiceProvider.php (
    echo [OK] app\Providers\AppServiceProvider.php
) else (
    echo [MISSING] app\Providers\AppServiceProvider.php
)

if exist laravel_app\vendor\autoload.php (
    echo [OK] vendor\autoload.php
) else (
    echo [MISSING] vendor\autoload.php
)

if exist laravel_app\storage\logs (
    echo [OK] storage\logs
) else (
    echo [MISSING] storage\logs
)

if exist laravel_app\bootstrap\cache (
    echo [OK] bootstrap\cache
) else (
    echo [MISSING] bootstrap\cache
)

echo.
echo ========================================
echo SUMMARY
echo ========================================
echo.
echo Folder laravel_app is ready!
echo.
echo Next steps:
echo 1. Check laravel_app\.env and set DB_PASSWORD
echo 2. Compress laravel_app folder to ZIP
echo 3. Upload to Hostinger
echo 4. Extract and set permissions
echo 5. Run artisan commands
echo.
echo Read laravel_app\README_UPLOAD.md for detailed instructions.
echo.
echo ========================================
echo DONE!
echo ========================================
pause
