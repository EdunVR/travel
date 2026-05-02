@echo off
echo ================================================
echo DEPLOYING SALES LAPORAN DELETE PERMISSION
echo ================================================
echo.

echo 1. Creating permission...
php create_sales_laporan_delete_permission.php
echo.

echo 2. Assigning to Super Admin...
php assign_sales_laporan_delete_to_superadmin.php
echo.

echo 3. Clearing cache...
php artisan config:clear
php artisan view:clear
echo.

echo 4. Testing permission...
php test_sales_laporan_delete_permission.php
echo.

echo ================================================
echo DEPLOYMENT COMPLETE!
echo ================================================
echo.
echo NEXT STEPS:
echo 1. Login as super_admin user
echo 2. Go to Laporan Penjualan page
echo 3. Verify 'Hapus' button is visible
echo 4. Test delete functionality
echo 5. Assign permission to other roles via Role Management modal
echo.
pause