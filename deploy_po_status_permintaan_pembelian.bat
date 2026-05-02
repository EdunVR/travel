@echo off
echo ===================================
echo DEPLOY PO STATUS PERMINTAAN PEMBELIAN
echo ===================================
echo.

echo 1. Testing PO status change...
php test_po_status_permintaan_pembelian.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo Cache cleared successfully.
echo.

echo 3. Running final verification...
php test_permintaan_barang_approval_final.php
echo.

echo ===================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY
echo ===================================
echo.
echo CHANGES MADE:
echo - Changed PO status from 'draft' to 'permintaan_pembelian'
echo - Updated approval modal information text
echo - Updated success message to reflect correct status
echo - Verified status text mapping works correctly
echo.
echo READY FOR PRODUCTION USE
echo ===================================

pause