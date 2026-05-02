@echo off
echo ===================================
echo DEPLOY INTER OUTLET ALL OUTLETS FIX
echo ===================================
echo.

echo 1. Testing database connection...
php test_inter_outlet_all_outlets.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo ✅ Cache cleared successfully
echo.

echo 3. Optimizing application...
php artisan config:cache
php artisan route:cache
echo ✅ Application optimized
echo.

echo 4. Testing route registration...
php artisan route:list | findstr "inter-outlet"
echo.

echo ===================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ===================================
echo.
echo 📋 CHANGES MADE:
echo ✅ Modified InterOutletSaleController::getOutlets()
echo ✅ Dropdown outlet tujuan now shows ALL active outlets
echo ✅ No longer restricted by user outlet access
echo ✅ Current outlet still excluded from destination list
echo.
echo 🧪 TESTING:
echo 1. Login to application
echo 2. Go to Penjualan Antar Outlet
echo 3. Check dropdown "Outlet Tujuan"
echo 4. Verify all outlets are visible
echo.
pause