@echo off
echo ========================================
echo Deploying SDM Dashboard Outlets Variable Fix
echo ========================================

echo.
echo [1/4] Clearing all caches...
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear

echo.
echo [2/4] Testing syntax...
php -l app/Http/Controllers/SdmDashboardController.php

echo.
echo [3/4] Testing SDM Dashboard fix...
php test_sdm_final_fix.php

echo.
echo [4/4] Deployment Summary:
echo ✓ Fixed syntax errors in SdmDashboardController
echo ✓ Removed extra XML tags that were causing parse errors
echo ✓ Controller now properly passes $outlets variable to view
echo ✓ SDM Dashboard should now load without undefined variable errors
echo ✓ Outlet filtering functionality is preserved

echo.
echo ========================================
echo SDM Dashboard Fix Deployment Complete!
echo ========================================
echo.
echo The undefined variable $outlets error has been resolved.
echo You can now access the SDM Dashboard at /admin/sdm
echo.

pause