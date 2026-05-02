@echo off
echo ========================================
echo DEPLOYING SPAREPART IMPROVEMENTS
echo ========================================

echo.
echo [1/4] Running migrations...
php artisan migrate --path=database/migrations/2024_12_11_000001_add_kategori_karyawan_to_sparepart_logs.php --force

echo.
echo [2/4] Seeding permissions...
php artisan db:seed --class=SparepartPermissionSeeder --force

echo.
echo [3/4] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [4/4] Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo ========================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo New Features Added:
echo - Filter range tanggal dan outlet
echo - Checkbox untuk bulk operations
echo - Sortable harga column
echo - Export PDF/Excel dengan detail log
echo - Penyesuaian harga (superadmin/permission)
echo - Penyesuaian stok dengan kategori dan karyawan
echo - Kode log otomatis
echo - Filter dan sort log dalam modal
echo - Bulk delete functionality
echo.
echo Please test the following:
echo 1. Filter by date range and outlet
echo 2. Select items and bulk delete
echo 3. Export data (PDF for all, Excel for superadmin)
echo 4. Adjust stock with category and employee
echo 5. Adjust price (check permissions)
echo 6. Sort and filter logs in adjust modal
echo.
pause