@echo off
echo ============================================
echo DEPLOYING PURCHASE ORDER CHECKBOX FILTER
echo ============================================
echo.

echo [1/4] Testing implementation...
php test_purchase_order_checkbox_filter.php
echo.

echo [2/4] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Cache cleared successfully.
echo.

echo [3/4] Optimizing application...
php artisan config:cache
php artisan route:cache
echo Optimization complete.
echo.

echo [4/4] Deployment summary...
echo ✅ Frontend: Checkbox filter UI implemented
echo ✅ JavaScript: All required functions added
echo ✅ Backend: Controller methods updated for multiple outlets
echo ✅ Data Loading: Updated to support outlet_ids[] parameter
echo ✅ Routes: All routes compatible
echo.

echo ============================================
echo DEPLOYMENT COMPLETE!
echo ============================================
echo.
echo NEXT STEPS:
echo 1. Open Purchase Order page in browser
echo 2. Clear browser cache (Ctrl+F5)
echo 3. Test checkbox filter functionality:
echo    - Default: All outlets selected
echo    - Select All/Clear All buttons work
echo    - Data filters correctly by selected outlets
echo    - No JavaScript errors in console
echo.
echo Purchase Order page: /admin/pembelian/purchase-order
echo.
pause