@echo off
echo ========================================
echo Deploy Image Routes Fix
echo ========================================
echo.

echo Step 1: Clear route cache...
php artisan route:clear
echo.

echo Step 2: Clear view cache...
php artisan view:clear
echo.

echo Step 3: Clear config cache...
php artisan config:clear
echo.

echo Step 4: Verify routes exist...
echo.
echo Checking set-primary route:
php artisan route:list --name=admin.inventaris.produk.set-primary
echo.
echo Checking remove-image route:
php artisan route:list --name=admin.inventaris.produk.remove
echo.

echo ========================================
echo Deployment Complete!
echo ========================================
echo.
echo IMPORTANT: If you're on production server, run:
echo   php artisan optimize
echo   php artisan route:cache
echo.
pause
