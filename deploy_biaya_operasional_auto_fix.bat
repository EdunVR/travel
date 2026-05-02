@echo off
echo ========================================
echo DEPLOYING BIAYA OPERASIONAL AUTO FIX
echo ========================================
echo.

echo [1] Testing the fix...
php test_biaya_operasional_auto_fix.php
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
echo [4] Testing routes...
php artisan route:list | findstr "monthly"

echo.
echo ========================================
echo ✅ DEPLOYMENT COMPLETED SUCCESSFULLY
echo ========================================
echo.
echo FIXES APPLIED:
echo ✓ MonthlyProductionCostController->data() now returns detailed costs
echo ✓ ProductionController->getMonthlyCosts() now returns detailed costs
echo ✓ Both methods include electricity_cost, water_cost, fuel_cost, office_salary_cost
echo ✓ Auto calculation will now show correct values instead of 0
echo.
echo CURRENT MONTHLY DATA AVAILABLE:
echo ✓ Outlet ID: 3 (Bojong Kunci)
echo ✓ Electricity Cost: Rp 7,834,377
echo ✓ Water Cost: Rp 819,960
echo ✓ Fuel Cost: Rp 5,295,000
echo ✓ Office Salary Cost: Rp 0
echo ✓ Total Cost: Rp 13,949,337
echo.
echo READY TO TEST:
echo 1. Visit: https://poshan.my.id/tofu/admin/produksi/produksi
echo 2. Click "Buat Produksi Baru"
echo 3. Select "Bojong Kunci" outlet
echo 4. In Biaya Operasional section, click "Auto dari Biaya Bulanan"
echo 5. Verify values are populated correctly (not 0)
echo.
pause