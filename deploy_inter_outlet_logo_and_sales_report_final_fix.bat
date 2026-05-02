@echo off
echo ========================================
echo DEPLOYING INTER OUTLET LOGO AND SALES REPORT FINAL FIX
echo ========================================

echo.
echo 1. Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Running test to verify fixes...
php test_inter_outlet_logo_and_sales_report_fix.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo FIXES APPLIED:
echo ✅ Added Inter Outlet source badge in sales report (purple badge)
echo ✅ Fixed invoice preview for Inter Outlet transactions
echo ✅ Removed broken invoice preview route (404 error fixed)
echo ✅ Fixed logo display in PDF by setting correct outlet context
echo ✅ Updated PDF generation to use outlet asal for company settings
echo.
echo TESTING INSTRUCTIONS:
echo 1. Go to Admin ^> Penjualan ^> Laporan
echo 2. Load data - Inter Outlet transactions should show purple "Inter Outlet" badge
echo 3. Click on Inter Outlet invoice number - should open PDF in modal
echo 4. Verify company logo appears in the PDF header
echo 5. Check that 404 error no longer occurs when clicking invoice numbers
echo.
echo TROUBLESHOOTING:
echo - If logo still not showing: Check Admin ^> Sistem ^> Company Settings
echo - If source column empty: Clear browser cache (Ctrl+F5)
echo - If 404 error persists: Check browser console for specific error
echo.
pause