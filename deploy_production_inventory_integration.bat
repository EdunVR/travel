@echo off
echo ========================================
echo DEPLOYING PRODUCTION INVENTORY INTEGRATION
echo ========================================
echo.

echo 1. Testing inventory integration...
php test_production_inventory_integration.php
echo.

echo 2. Running inventory simulation...
php test_inventory_simulation.php
echo.

echo 3. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo 4. Testing production realization...
echo Visit: /admin/produksi/produksi
echo - Create a production with materials
echo - Set status to "in_progress"
echo - Add realization
echo - Check material stock reduced (FIFO)
echo - Check product stock increased
echo.

echo 5. Monitoring logs...
echo Check storage/logs/laravel.log for:
echo - [INVENTORY] tags for inventory movements
echo - [FIFO] tags for FIFO stock reduction
echo - HPP calculations and stock updates
echo.

echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo WHAT WAS IMPLEMENTED:
echo 1. Automatic material stock reduction (FIFO)
echo 2. Automatic product stock addition
echo 3. Real-time HPP calculation
echo 4. Multi-product support
echo 5. Comprehensive logging and error handling
echo.
echo FIFO SYSTEM:
echo - Oldest material batches consumed first
echo - Stock reduced from harga_bahan table
echo - New product stock added to hpp_produk table
echo - HPP calculated from actual production costs
echo.
pause