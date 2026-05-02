@echo off
echo ========================================
echo TESTING INTER OUTLET SALE MODULE
echo ========================================

echo.
echo [1/6] Testing database connection...
php artisan tinker --execute="echo 'Database connected: ' . (DB::connection()->getPdo() ? 'YES' : 'NO');"

echo.
echo [2/6] Testing migrations...
php artisan migrate:status | findstr "inter_outlet"

echo.
echo [3/6] Testing models...
php artisan tinker --execute="echo 'InterOutletSale model: ' . (class_exists('App\Models\InterOutletSale') ? 'EXISTS' : 'NOT FOUND');"
php artisan tinker --execute="echo 'InterOutletSaleItem model: ' . (class_exists('App\Models\InterOutletSaleItem') ? 'EXISTS' : 'NOT FOUND');"
php artisan tinker --execute="echo 'SettingCOAInterOutletSale model: ' . (class_exists('App\Models\SettingCOAInterOutletSale') ? 'EXISTS' : 'NOT FOUND');"

echo.
echo [4/6] Testing routes...
php artisan route:list | findstr "inter-outlet"

echo.
echo [5/6] Testing main route...
php artisan tinker --execute="echo 'Main route test: ' . route('admin.penjualan.inter-outlet.index');"

echo.
echo [6/6] Testing permissions...
php artisan tinker --execute="echo 'Permissions count: ' . \App\Models\Permission::where('name', 'like', 'sales.inter-outlet.%%')->count();"

echo.
echo ========================================
echo TESTING COMPLETED!
echo ========================================
echo.
echo If all tests show positive results, the module is ready to use.
echo Access the module at: http://your-domain/admin/penjualan/inter-outlet
echo Menu: Penjualan (S&M) ^> Penjualan Antar Outlet
echo.
pause