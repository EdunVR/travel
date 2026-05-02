@echo off
echo === DEPLOYING PRODUCTION STOCK CALCULATION FIX ===
echo.

echo 1. Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 2. Testing the stock calculation fixes...
php test_production_stock_calculation_fix.php

echo.
echo 3. Stock Calculation Fixes Summary:
echo    - Removed references to non-existent produk.stok column
echo    - Implemented SUM(hpp_produk.stok) for total stock calculation
echo    - Added GROUP BY for proper aggregation per product
echo    - Used HAVING clause to filter by calculated stock > 0
echo    - Implemented AVG(hpp_produk.hpp) for average FIFO cost
echo    - Added COALESCE to handle NULL values properly
echo.

echo 4. FIFO Stock System Implementation:
echo    - Stock is now calculated from hpp_produk table (FIFO system)
echo    - Each production creates hpp_produk records with stock
echo    - Total stock = SUM of all hpp_produk.stok for each product
echo    - Average cost = AVG of all hpp_produk.hpp for each product
echo    - Only products with actual stock (total_stock > 0) are shown
echo.

echo 5. SQL Query Structure:
echo    SELECT produk.*, SUM(hpp_produk.stok) as total_stock, AVG(hpp_produk.hpp) as cost
echo    FROM produk
echo    LEFT JOIN hpp_produk ON produk.id_produk = hpp_produk.id_produk
echo    WHERE produk.id_outlet = ? AND produk.is_active = 1
echo    GROUP BY produk.id_produk
echo    HAVING total_stock > 0
echo.

echo 6. Manual Testing Steps:
echo    - Open production page: /admin/produksi/produksi
echo    - Test materials search API endpoint
echo    - Test products search API endpoint
echo    - Verify only products with stock appear
echo    - Check that costs are calculated from hpp_produk
echo    - Verify no SQL column errors in logs
echo.

echo === DEPLOYMENT COMPLETE ===
pause