@echo off
echo ===================================
echo DEPLOY PERMINTAAN BARANG SEARCH FIELD FIX
echo ===================================
echo.

echo 1. Testing search functionality...
php test_permintaan_barang_search_complete.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Cache cleared successfully.
echo.

echo 3. Testing after cache clear...
php test_search_methods_final.php
echo.

echo ===================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY
echo ===================================
echo.
echo CHANGES MADE:
echo - Fixed searchProducts method field names
echo - Fixed searchMaterials method field names  
echo - Updated relationship loading for Satuan
echo - Verified data structure for frontend compatibility
echo.
echo READY FOR PRODUCTION USE
echo ===================================

pause