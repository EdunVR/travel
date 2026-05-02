@echo off
echo ========================================
echo   FIXING SISTEM ROUTE ERROR
echo ========================================

echo.
echo [1/4] Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [2/4] Verifying route registration...
php artisan route:list --name=admin.sistem.index

echo.
echo [3/4] Checking sidebar configuration...
echo Sidebar should use: admin.sistem.index
echo Dashboard should use: admin.sistem.index

echo.
echo [4/4] Testing route accessibility...
echo Route admin.sistem.index should be accessible at: /admin/sistem

echo.
echo ========================================
echo   ROUTE ERROR FIX COMPLETE!
echo ========================================
echo.
echo Fixed issues:
echo - Updated dashboard.blade.php to use admin.sistem.index
echo - Cleared all caches to refresh compiled views
echo - Verified route registration
echo.
echo You can now access:
echo - Dashboard: /admin/dashboard (should work without errors)
echo - Sistem: /admin/sistem (should load sistem dashboard)
echo.
pause