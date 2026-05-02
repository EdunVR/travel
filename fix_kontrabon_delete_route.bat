@echo off
echo ========================================
echo FIX KONTRA BON DELETE ROUTE
echo ========================================
echo.

echo [1/3] Clearing route cache...
php artisan route:clear
if %errorlevel% neq 0 (
    echo ERROR: Failed to clear route cache
    pause
    exit /b 1
)
echo.

echo [2/3] Clearing view cache...
php artisan view:clear
if %errorlevel% neq 0 (
    echo ERROR: Failed to clear view cache
    pause
    exit /b 1
)
echo.

echo [3/3] Verifying route...
php artisan route:list | findstr "admin.penjualan.kontrabon.destroy"
if %errorlevel% neq 0 (
    echo ERROR: Route not found
    pause
    exit /b 1
)
echo.

echo ========================================
echo ROUTE FIX COMPLETE!
echo ========================================
echo.
echo Route: admin.penjualan.kontrabon.destroy
echo Method: DELETE
echo Path: admin/penjualan/kontrabon/{id}
echo.
echo Next steps:
echo 1. Refresh your browser (Ctrl+Shift+R)
echo 2. Go to: /admin/penjualan/kontrabon
echo 3. Click tab "List Kontra Bon"
echo 4. Check if delete button appears
echo.
pause
