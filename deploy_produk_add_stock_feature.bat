@echo off
echo ========================================
echo DEPLOYING PRODUK ADD STOCK FEATURE
echo ========================================
echo.

echo [1/5] Testing database connection...
php -r "try { require_once 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo 'Database connection: OK' . PHP_EOL; } catch (Exception $e) { echo 'Database connection failed: ' . $e->getMessage() . PHP_EOL; exit(1); }"

echo.
echo [2/5] Checking required tables...
php -r "require_once 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); use Illuminate\Support\Facades\Schema; $tables = ['produk', 'hpp_produk']; foreach ($tables as $table) { if (Schema::hasTable($table)) { echo 'Table ' . $table . ': EXISTS' . PHP_EOL; } else { echo 'Table ' . $table . ': MISSING - Please run migrations' . PHP_EOL; exit(1); } }"

echo.
echo [3/5] Testing Produk model addStock method...
php test_produk_add_stock.php

echo.
echo [4/5] Clearing application cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [5/5] Checking route registration...
php artisan route:list --name=produk.add-stock

echo.
echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Test the feature in browser
echo 2. Verify permissions are working
echo 3. Test with different user roles
echo 4. Check error handling
echo.
echo TESTING URLS:
echo - Main page: /admin/inventaris/produk
echo - Add stock: Click "Tambah" button next to stock field
echo.
pause