@echo off
echo ========================================
echo DEPLOY JURNAL SUPERADMIN DELETE FIX
echo ========================================
echo.

echo 1. Testing database structure...
php test_superadmin_delete_fix.php

echo.
echo 2. Clearing application cache...
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
echo DEPLOYMENT COMPLETED
echo ========================================
echo.

echo NEXT STEPS:
echo 1. Test the fix in staging environment
echo 2. Login as superadmin
echo 3. Try deleting a posted journal
echo 4. Verify opening balances are deleted
echo 5. Check logs for any errors
echo.

echo Press any key to continue...
pause > nul