@echo off
echo ========================================
echo DEPLOYING INTER OUTLET HISTORY FIX
echo ========================================

echo.
echo 1. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Testing history view fix...
php test_history_view_fix.php

echo.
echo 3. Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo ========================================
echo DEPLOYMENT COMPLETED!
echo ========================================
echo.
echo MANUAL TESTING REQUIRED:
echo 1. Visit /MORRA/admin/penjualan/inter-outlet
echo 2. Click "Riwayat" button to test history modal
echo 3. Verify history page displays without admin layout
echo 4. Test all DataTable functionality (filters, pagination)
echo 5. Test transaction detail modal
echo 6. Test print functionality (should not show logo error)
echo 7. Test COA settings modal (outlets should appear)
echo.
pause