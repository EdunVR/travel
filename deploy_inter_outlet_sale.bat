@echo off
echo ========================================
echo DEPLOYING INTER OUTLET SALE MODULE
echo ========================================

echo.
echo [1/5] Running migrations...
php artisan migrate --force

echo.
echo [2/5] Running permission seeder...
php artisan db:seed --class=InterOutletSalePermissionSeeder

echo.
echo [3/5] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [4/5] Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo [5/5] Testing routes...
php artisan tinker --execute="echo 'Route test: ' . route('admin.penjualan.inter-outlet.index');"

echo.
echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo Inter Outlet Sale module has been deployed with the following features:
echo - Penjualan Antar Outlet interface
echo - Stock management between outlets
echo - Automatic journal entries
echo - COA settings
echo - Transaction history
echo - Print/Export functionality
echo - Permission system
echo.
echo Access the module at: /admin/penjualan/inter-outlet
echo Menu: Penjualan (S&M) ^> Penjualan Antar Outlet
echo.
pause