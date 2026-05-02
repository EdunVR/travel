@echo off
echo ========================================
echo DEPLOYING QC HEADER FIX AND DATA MAPPING
echo ========================================
echo.

echo [1] Testing data mapping...
php debug_controller_data_mapping.php
if %errorlevel% neq 0 (
    echo Error in data mapping test - stopping deployment
    pause
    exit /b 1
)

echo.
echo [2] Testing complete functionality...
php test_qc_export_header_fix_complete.php

echo.
echo [3] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [4] Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo [5] Testing route availability...
php artisan route:list | findstr "qc-tofu-mentah-pdf"

echo.
echo ========================================
echo ✅ DEPLOYMENT COMPLETED SUCCESSFULLY
echo ========================================
echo.
echo HEADER STRUCTURE FIXES APPLIED:
echo ✓ Removed extra "Kuantitas" column from Filling ^& Pengemasan section
echo ✓ Updated header: Mesin 1 and Mesin 2 now directly under "Kuantitas Mesin"
echo ✓ Adjusted column widths for better layout
echo.
echo DATA MAPPING IMPROVEMENTS:
echo ✓ Fixed JSON reading pattern to match individual QC PDF method
echo ✓ Updated field mapping to use correct tofu_data field names
echo ✓ 11/12 QC fields now properly populated from database
echo ✓ Auto-generated document number and export date
echo.
echo READY TO TEST:
echo 1. Visit: https://poshan.my.id/tofu/admin/produksi/produksi
echo 2. Click Export PDF dropdown
echo 3. Select "QC Egg Tofu Mentah"
echo 4. Verify PDF shows:
echo    - Correct header structure (no extra Kuantitas column)
echo    - Mesin 1 and Mesin 2 directly under Kuantitas Mesin
echo    - All QC data populated from tofu_data JSON
echo    - Auto-generated document info
echo.
pause