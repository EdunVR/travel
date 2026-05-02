@echo off
echo ========================================
echo DEPLOY KONTRA BON DELETE FEATURE
echo ========================================
echo.

echo [1/4] Creating permission...
php artisan tinker --execute="include 'create_kontrabon_delete_permission.php';"
if %errorlevel% neq 0 (
    echo ERROR: Failed to create permission
    pause
    exit /b 1
)
echo.

echo [2/4] Clearing route cache...
php artisan route:clear
if %errorlevel% neq 0 (
    echo ERROR: Failed to clear route cache
    pause
    exit /b 1
)
echo.

echo [3/4] Caching routes...
php artisan route:cache
if %errorlevel% neq 0 (
    echo ERROR: Failed to cache routes
    pause
    exit /b 1
)
echo.

echo [4/4] Running tests...
php artisan tinker --execute="include 'test_kontrabon_delete.php';"
if %errorlevel% neq 0 (
    echo ERROR: Tests failed
    pause
    exit /b 1
)
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo Next steps:
echo 1. Open browser and login
echo 2. Go to: /admin/penjualan/kontrabon
echo 3. Click tab "List Kontra Bon"
echo 4. Check if delete button (trash icon) appears
echo 5. Try to delete a kontra bon
echo 6. Verify piutang status is restored
echo.
echo Permission created: sales.kontrabon.delete
echo Route added: admin.penjualan.kontrabon.destroy
echo.
pause
