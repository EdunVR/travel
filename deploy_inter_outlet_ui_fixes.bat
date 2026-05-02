@echo off
echo ========================================
echo DEPLOYING INTER OUTLET UI FIXES
echo ========================================

echo.
echo 1. Testing fixes...
php test_inter_outlet_ui_fixes.php

echo.
echo 2. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 3. Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo ========================================
echo DEPLOYMENT COMPLETE
echo ========================================
echo.
echo FIXES DEPLOYED:
echo ✓ COA Settings Modal - Save button and outlet change functionality
echo ✓ History Modal - Direct table with JSON data loading
echo ✓ Print URL - Fixed format from //print/id to /id/print
echo ✓ Error Handling - Improved AJAX error messages
echo.
echo BROWSER TESTING:
echo 1. Open Inter Outlet Sales page
echo 2. Click 'Setting COA' - should show form with save button
echo 3. Change outlet in COA settings - should reload accounts
echo 4. Click 'Riwayat' - should show transaction table
echo 5. Test print functionality after creating transaction
echo.
pause