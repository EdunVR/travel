@echo off
echo ========================================
echo DEPLOYING INTER OUTLET PDF MODAL AND SALES REPORT FINAL FIX
echo ========================================

echo.
echo 1. Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Running final test...
php test_inter_outlet_pdf_modal_and_sales_report_final.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo FIXES APPLIED:
echo ✅ Fixed user_id column error in SalesReportController
echo ✅ Updated PDF template to use correct CompanySettings structure  
echo ✅ Added error handling to PDF generation
echo ✅ PDF modal should now work correctly
echo ✅ Sales report should include Inter Outlet transactions
echo.
echo TESTING INSTRUCTIONS:
echo 1. Go to Admin ^> Penjualan ^> Inter Outlet Sale
echo 2. Click on history/riwayat to see transaction list
echo 3. Click Print button - should open PDF in modal (not new tab)
echo 4. Go to Admin ^> Penjualan ^> Laporan to test sales report
echo 5. Verify Inter Outlet transactions are included in the report
echo 6. Check that company logo and name appear correctly in PDF
echo.
pause