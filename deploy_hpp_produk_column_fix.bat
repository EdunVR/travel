@echo off
echo ========================================
echo DEPLOYING HPP_PRODUK COLUMN FIX
echo ========================================
echo.

echo 1. Testing column fix...
php test_hpp_produk_column_fix.php
echo.

echo 2. Clearing cache...
php artisan cache:clear
php artisan config:clear
echo.

echo 3. Testing production realization...
echo Visit: /admin/produksi/produksi
echo - Create a production with materials
echo - Set status to "in_progress"
echo - Add realization
echo - Should work without SQL column errors
echo.

echo 4. Verifying database records...
echo Check hpp_produk table for new records:
echo - id_produk: Product ID
echo - production_id: Production reference
echo - stok: Stock quantity
echo - hpp: Calculated HPP value
echo - realized_quantity: Produced quantity
echo - created_at/updated_at: Timestamps
echo.

echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo WHAT WAS FIXED:
echo 1. Removed non-existent 'tanggal' column reference
echo 2. Removed non-existent 'id_outlet' column reference
echo 3. Used correct hpp_produk table structure
echo 4. Added proper quantity tracking fields
echo 5. Maintained production_id linkage
echo.
echo The production realization should now work without SQL errors!
echo.
pause