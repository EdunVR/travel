@echo off
echo ========================================
echo DEPLOYING QC TOFU EXPORT FINAL IMPROVEMENTS
echo ========================================
echo.

echo [1] Testing current implementation...
php test_qc_tofu_export_final.php
if %errorlevel% neq 0 (
    echo Error in testing - stopping deployment
    pause
    exit /b 1
)

echo.
echo [2] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [3] Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo [4] Testing route availability...
php artisan route:list | findstr "qc-tofu-mentah-pdf"

echo.
echo ========================================
echo ✅ DEPLOYMENT COMPLETED SUCCESSFULLY
echo ========================================
echo.
echo IMPROVEMENTS APPLIED:
echo ✓ Auto-generated document number (PNI/FSOP/QC/01-YY format)
echo ✓ Auto-generated current export date
echo ✓ Fixed double-encoded JSON handling
echo ✓ Updated field mapping to match actual tofu_data structure
echo ✓ Proper margins to prevent edge sticking
echo ✓ Correct header table structure (Mesin 1 & 2 under Kuantitas)
echo.
echo READY TO TEST:
echo 1. Visit: https://poshan.my.id/tofu/admin/produksi/produksi
echo 2. Click Export PDF dropdown
echo 3. Select "QC Egg Tofu Mentah"
echo 4. Verify PDF generates correctly
echo.
pause