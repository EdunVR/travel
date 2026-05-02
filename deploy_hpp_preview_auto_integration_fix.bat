@echo off
echo ===== DEPLOYING HPP PREVIEW AUTO INTEGRATION FIX =====
echo.

echo 1. Testing the integration fix...
php test_hpp_preview_auto_integration.php
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
echo 4. Click 'Auto dari Biaya Bulanan' in Biaya Operasional section
echo 5. Verify HPP Preview shows updated operational costs
echo 6. Check browser console for debug logs
echo.
echo The auto operational costs should now properly integrate with HPP preview!
echo.
pause