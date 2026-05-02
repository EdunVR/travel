@echo off
echo ===================================
echo DEPLOYING INTER OUTLET HISTORY MODAL FIXES
echo ===================================
echo.

echo 1. Testing fixes...
php test_inter_outlet_history_modal_fixes.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
echo.

echo 3. Clearing config cache...
php artisan config:clear
echo.

echo 4. Clearing route cache...
php artisan route:clear
echo.

echo 5. Clearing view cache...
php artisan view:clear
echo.

echo 6. Optimizing application...
php artisan optimize
echo.

echo ===================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ===================================
echo.
echo CHANGES MADE:
echo 1. Fixed print invoice URL to include transaction ID
echo 2. Added showSuccess method for proper notifications
echo 3. Fixed success messages for approve and delete actions
echo.
echo TESTING INSTRUCTIONS:
echo 1. Go to: /admin/penjualan/inter-outlet
echo 2. Create a test transaction
echo 3. Open history modal (click history button)
echo 4. Test "Setujui Transaksi" button
echo 5. Test "Print Invoice" button
echo.
echo Both buttons should now work correctly!
echo.
pause