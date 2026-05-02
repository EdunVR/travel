@echo off
echo === DEPLOYING PRODUCTION MISSING METHODS FIX ===
echo.

echo 1. Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 2. Testing the fix...
php test_production_missing_methods_fix.php

echo.
echo 3. Fix Summary:
echo    - Added 12 missing methods to ProductionController
echo    - getStatistics() - Production statistics
echo    - getMaterials() - Materials search
echo    - calculateHppPreview() - HPP calculation
echo    - getMaterialFifo() - Material FIFO data
echo    - getAttendanceCount() - Attendance count
echo    - getMonthlyCosts() - Monthly costs data
echo    - storeMonthlyCost() - Store monthly cost
echo    - deleteMonthlyCost() - Delete monthly cost
echo    - approve() - Approve production
echo    - start() - Start production
echo    - complete() - Complete production
echo    - All methods include proper error handling
echo.

echo 4. Manual Testing Steps:
echo    - Open production page: /admin/produksi/produksi
echo    - Check browser console for errors
echo    - Test statistics loading
echo    - Test materials search
echo    - Test production workflow (approve/start/complete)
echo    - Verify monthly costs functionality
echo.

echo === DEPLOYMENT COMPLETE ===
pause