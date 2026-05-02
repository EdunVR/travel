@echo off
echo ========================================
echo Fix Session SQLite Error
echo ========================================
echo.

echo Step 1: Clear config cache...
php artisan config:clear
echo.

echo Step 2: Clear cache...
php artisan cache:clear
echo.

echo Step 3: Clear view cache...
php artisan view:clear
echo.

echo Step 4: Verify session driver...
php artisan tinker --execute="echo config('session.driver');"
echo.

echo ========================================
echo Fix Complete!
echo ========================================
echo.
echo If error persists, check:
echo 1. .env file - SESSION_DRIVER should be 'file'
echo 2. storage/framework/sessions folder exists
echo 3. Permissions on storage folder
echo.
pause
