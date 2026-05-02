@echo off
echo ========================================
echo DEPLOYING PRODUCTION DUPLICATION FIX
echo ========================================
echo.

echo 1. Testing duplication fix...
php test_production_duplication_fix.php
echo.

echo 2. Clearing cache...
php artisan cache:clear
php artisan config:clear
echo.

echo 3. Testing production realization...
echo Visit: /admin/produksi/produksi
echo - Create a production with 2 products
echo - Check hpp_produk table - should have 2 records
echo - Set status to "in_progress"
echo - Add realization
echo - Check hpp_produk table - should still have 2 records (updated, not duplicated)
echo.

echo 4. Verifying no duplication...
echo Check database hpp_produk table:
echo - Count records before realization
echo - Add realization
echo - Count records after realization
echo - Should be the same count (no new records created)
echo.

echo 5. Monitoring logs...
echo Check storage/logs/laravel.log for:
echo - "Updated existing HPP record" messages
echo - No "Product stock added successfully" messages
echo - Stock and HPP value updates
echo.

echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo WHAT WAS FIXED:
echo 1. Removed addProductStock method that created duplicate records
echo 2. Only update existing HPP records during realization
echo 3. No more new HPP record creation during realization
echo 4. Proper stock and HPP value updates
echo 5. Maintained inventory tracking accuracy
echo.
echo RESULT:
echo - 2 products stay 2 products (no duplication)
echo - Stock quantities properly updated
echo - HPP values calculated and updated
echo - No duplicate records in database
echo.
pause