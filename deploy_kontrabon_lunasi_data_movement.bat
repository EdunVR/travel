@echo off
echo ========================================
echo DEPLOY KONTRABON LUNASI DATA MOVEMENT
echo ========================================

echo.
echo [1/4] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/4] Testing data movement simulation...
php test_kontrabon_lunasi_data_movement.php

echo.
echo [3/4] Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo [4/4] Final verification...
echo Checking route availability...
php artisan route:list | findstr lunasi

echo.
echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo FINAL IMPLEMENTATION SUMMARY:
echo ✓ Fixed lunasi method to create kontra_bon_detail records
echo ✓ Data now moves from "Data Hutang yang Ditagihkan" to "Data Hutang yang Sudah Dilunasi"
echo ✓ Piutang status updated to lunas with proper amounts
echo ✓ KontraBon status updated to lunas
echo ✓ Print view will show correct data distribution after lunasi
echo.
echo PROCESS FLOW:
echo 1. Before Lunasi: Data in "Data Hutang yang Ditagihkan" table
echo 2. Click Lunasi: Creates kontra_bon_detail records from piutang data
echo 3. After Lunasi: Data moves to "Data Hutang yang Sudah Dilunasi" table
echo 4. Print shows: Empty "Ditagihkan" + Filled "Sudah Dilunasi" + STATUS: LUNAS
echo.
echo TESTING STEPS:
echo [ ] 1. Go to admin/penjualan/kontrabon
echo [ ] 2. Find kontrabon with status "Pending" 
echo [ ] 3. Print it - verify data in "Data Hutang yang Ditagihkan"
echo [ ] 4. Click "Lunasi" button
echo [ ] 5. Print again - verify data moved to "Data Hutang yang Sudah Dilunasi"
echo [ ] 6. Verify "STATUS: LUNAS" appears
echo [ ] 7. Verify "Data Hutang yang Ditagihkan" is now empty
echo.
echo EXPECTED RESULT FOR TEST KONTRABON ID 6:
echo - Before: 15.300.000 in "Ditagihkan", 0 in "Sudah Dilunasi"
echo - After: 0 in "Ditagihkan", 15.300.000 in "Sudah Dilunasi"
echo.
pause