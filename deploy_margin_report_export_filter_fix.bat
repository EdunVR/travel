@echo off
echo ========================================
echo DEPLOY MARGIN REPORT EXPORT FILTER FIX
echo ========================================
echo.

echo [1/3] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo [2/3] Testing margin report export functionality...
php test_margin_report_export_filter_fix.php

echo.
echo [3/3] Deployment completed!
echo.
echo CHANGES APPLIED:
echo - Fixed export PDF to use applied filters
echo - Updated PDF template to use inter-outlet print header style
echo - Added company settings integration for logo and company info
echo - Enhanced filter information display in PDF
echo - Improved summary calculations with null handling
echo.
echo TEST THE FOLLOWING:
echo 1. Go to Laporan Margin (admin/penjualan/margin)
echo 2. Apply filters (outlet, date range, search)
echo 3. Click Export PDF button
echo 4. Verify PDF shows filtered data only
echo 5. Check PDF header matches inter-outlet print style
echo.
pause