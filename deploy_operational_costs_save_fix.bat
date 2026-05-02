@echo off
echo ===== DEPLOYING OPERATIONAL COSTS SAVE FIX =====
echo.

echo 1. Testing the operational costs save fix...
php test_operational_costs_save_fix.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo 3. Fix deployment completed!
echo.
echo NEXT STEPS:
echo 1. Open production page in browser
echo 2. Click 'Buat Produksi Baru'
echo 3. Select outlet with monthly cost data
echo 4. Add product and set target quantity
echo 5. Click 'Auto dari Biaya Bulanan' in Biaya Operasional section
echo 6. Verify auto operational cost rows are added (blue background)
echo 7. Fill other required fields and save production
echo 8. Check database to verify operational costs are saved
echo 9. Check browser console for debug logs during save
echo.
echo Both manual and auto-generated operational costs should now be saved correctly!
echo.
pause