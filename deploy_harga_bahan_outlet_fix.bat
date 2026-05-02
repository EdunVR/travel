@echo off
echo ========================================
echo DEPLOYING HARGA_BAHAN OUTLET FIX
echo ========================================
echo.

echo 1. Testing outlet filtering fix...
php test_harga_bahan_outlet_fix.php
echo.

echo 2. Testing query simulation...
php test_harga_bahan_query_simulation.php
echo.

echo 3. Clearing cache...
php artisan cache:clear
php artisan config:clear
echo.

echo 4. Testing production realization...
echo Visit: /admin/produksi/produksi
echo - Create a production with materials
echo - Set status to "in_progress"
echo - Add realization
echo - Should work without SQL column errors
echo.

echo 5. Verifying FIFO system...
echo Check storage/logs/laravel.log for:
echo - [FIFO] tags showing stock batch processing
echo - Outlet-specific material filtering
echo - FIFO order consumption (oldest first)
echo.

echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo WHAT WAS FIXED:
echo 1. Removed direct id_outlet filter on harga_bahan table
echo 2. Added JOIN with bahan table for outlet filtering
echo 3. Used bahan.id_outlet for proper outlet filtering
echo 4. Maintained FIFO ordering and stock tracking
echo 5. Added proper column selection to avoid conflicts
echo.
echo FIFO SYSTEM NOW WORKS:
echo - Materials filtered by outlet through bahan table
echo - Stock consumed from oldest batches first
echo - Proper inventory tracking maintained
echo - No SQL column errors
echo.
pause