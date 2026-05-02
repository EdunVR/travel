@echo off
echo ========================================
echo   FIXING SISTEM CONTROLLER ERRORS
echo ========================================

echo.
echo [1/4] Fixed CompanySettingController errors...
echo - Replaced getCurrentOutletId with getSelectedOutlet
echo - Method now exists in HasOutletFilter trait

echo.
echo [2/4] Fixed SistemController errors...
echo - Removed Spatie Permission dependency
echo - Using direct DB query for roles count

echo.
echo [3/4] Clearing caches...
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [4/4] Testing route accessibility...
php artisan route:list --name=admin.sistem.index

echo.
echo ========================================
echo   CONTROLLER ERRORS FIX COMPLETE!
echo ========================================
echo.
echo Fixed issues:
echo 1. CompanySettingController::getCurrentOutletId - FIXED
echo 2. SistemController Spatie Role dependency - FIXED
echo 3. All caches cleared and refreshed
echo.
echo You can now access:
echo - /admin/sistem (should work without errors)
echo - /admin/sistem/pengaturan (should work without errors)
echo.
pause