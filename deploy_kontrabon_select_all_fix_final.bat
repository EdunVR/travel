@echo off
echo ========================================
echo DEPLOY KONTRABON SELECT ALL FIX - FINAL
echo ========================================
echo.

echo [1/5] Clearing all cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
echo.

echo [2/5] Running debug script...
php debug_kontrabon_store_issue.php
echo.

echo [3/5] Testing database connection...
php artisan migrate:status
echo.

echo [4/5] Checking log file...
if exist storage\logs\laravel.log (
    echo Log file exists: storage\logs\laravel.log
    echo Last 10 lines:
    powershell -Command "Get-Content storage\logs\laravel.log -Tail 10"
) else (
    echo Warning: Log file not found
)
echo.

echo [5/5] Deployment summary...
echo.
echo CHANGES MADE:
echo - Enhanced logging in KontraBonController::store()
echo - Added detailed debug information
echo - Modal create already uses correct field names
echo.
echo FILES MODIFIED:
echo - app/Http/Controllers/Admin/KontraBonController.php
echo.
echo FILES TO CHECK:
echo - resources/views/admin/penjualan/kontrabon/modals/create.blade.php
echo - resources/views/admin/penjualan/kontrabon/print.blade.php
echo.

echo ========================================
echo NEXT STEPS - MANUAL TESTING REQUIRED
echo ========================================
echo.
echo 1. Open browser and login
echo 2. Go to: Penjualan -^> Kontra Bon
echo 3. Click "Tambah Kontra Bon" button
echo 4. Select customer with piutang
echo 5. Set date range (e.g., 01/01/2024 - 31/01/2024)
echo 6. Click "Select All" checkbox
echo 7. Set pembayaran: 0
echo 8. Click "Buat Kontra Bon"
echo 9. Open browser console (F12) and check for errors
echo 10. Open Network tab and check POST request
echo 11. Check if piutang_ids[] is sent correctly
echo 12. After submit, click "Print" on the new kontra bon
echo 13. Verify PDF shows correct data
echo.
echo DEBUGGING:
echo - Browser Console: Check for JavaScript errors
echo - Network Tab: Check POST payload for piutang_ids[]
echo - Laravel Log: tail -f storage/logs/laravel.log
echo - Database: Check kontra_bon_detail table
echo.
echo READ DOCUMENTATION:
echo - KONTRABON_SELECT_ALL_TROUBLESHOOTING_FINAL.md
echo - MULAI_DISINI_KONTRABON_SELECT_ALL_FIX.md
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo If problem persists, run:
echo   php debug_kontrabon_store_issue.php
echo.
echo And check:
echo   KONTRABON_SELECT_ALL_TROUBLESHOOTING_FINAL.md
echo.

pause
