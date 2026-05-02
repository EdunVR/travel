@echo off
echo ========================================
echo DEPLOYING QC EGG TOFU MENTAH PDF FORMAT UPDATE
echo ========================================
echo.

echo [1/4] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/4] Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo [3/4] Checking updated files...
if exist "resources\views\admin\produksi\produksi\bulk-qc-tofu-pdf.blade.php" (
    echo ✓ Updated QC Tofu PDF template with professional form format
) else (
    echo ✗ QC Tofu PDF template missing
)

echo.
echo [4/4] Testing routes...
php artisan route:list --name=produksi.export

echo.
echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo CHANGES IMPLEMENTED:
echo ✓ Updated QC Egg Tofu Mentah PDF format to match professional form
echo ✓ Added PT.PELITA NUSANTARA INDONESIA header with logo
echo ✓ Implemented exact table structure from the image
echo ✓ Added proper column headers matching the form
echo ✓ Mapped tofu_data JSON fields to form columns
echo ✓ Added professional styling and layout
echo.
echo PDF FORMAT FEATURES:
echo ✓ Company header with logo and form information
echo ✓ Document revision and date information
echo ✓ Professional table layout matching the original form
echo ✓ Proper column mapping for QC data
echo ✓ Data extraction from tofu_data JSON column
echo.
echo COLUMN MAPPING:
echo - Perendaman Kacang Kedelai: perendaman_waktu, rijek_telur
echo - Pasteurisasi: pasteurisasi_waktu, pasteurisasi_suhu
echo - Berat Adonan Pencampuran: homogenisasi_waktu, target_quantity
echo - Filling & Pengemasan: packaging_waktu, mesin_1, mesin_2
echo - Total Kuantitas: calculated from realized quantities
echo - Jumlah Reject Mentah: rejected_quantity from production
echo.
echo TESTING INSTRUCTIONS:
echo 1. Go to Production page (admin/produksi/produksi)
echo 2. Click Export PDF dropdown
echo 3. Select "QC Egg Tofu Mentah"
echo 4. Verify PDF matches the professional form format
echo 5. Check that data is properly extracted from tofu_data JSON
echo.
pause