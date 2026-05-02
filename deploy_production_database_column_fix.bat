@echo off
echo === DEPLOYING PRODUCTION DATABASE COLUMN FIX ===
echo.

echo 1. Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 2. Testing the database column fixes...
php test_production_database_column_fix.php

echo.
echo 3. Database Column Fixes Summary:
echo    - total_biaya_operasional → total_cost (productions table)
echo    - harga_beli → hpp via JOIN with hpp_produk table
echo    - total_hpp → hpp_per_unit (productions table)
echo    - Added proper FIFO implementation using hpp_produk
echo    - Fixed outlet filtering to use correct column names
echo    - All methods now use actual database column names
echo.

echo 4. FIFO System Implementation:
echo    - getMaterials() now JOINs with hpp_produk for cost data
echo    - getMaterialFifo() uses HppProduk model with FIFO ordering
echo    - Cost calculations based on actual hpp records
echo    - No database schema changes required
echo.

echo 5. Manual Testing Steps:
echo    - Open production page: /admin/produksi/produksi
echo    - Check browser console - no SQL column errors
echo    - Test statistics loading (should show correct totals)
echo    - Test materials search (should show costs from hpp_produk)
echo    - Test FIFO data retrieval
echo    - Check Laravel logs for SQL errors
echo.

echo === DEPLOYMENT COMPLETE ===
pause