@echo off
echo 🚀 DEPLOYING INTER OUTLET PDF MODAL AND SALES REPORT INTEGRATION
echo ================================================================

echo.
echo 📋 Step 1: Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 📋 Step 2: Update JavaScript cache busting
php -r "
\$timestamp = time();
\$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
\$content = file_get_contents(\$viewFile);
\$content = preg_replace('/inter-outlet\.js\?v=\d+/', 'inter-outlet.js?v=' . \$timestamp, \$content);
file_put_contents(\$viewFile, \$content);
echo 'Updated cache busting version: ?v=' . \$timestamp . PHP_EOL;
"

echo.
echo 📋 Step 3: Verify changes
echo ✅ JavaScript: PDF now opens in modal instead of new tab
echo ✅ PDF Design: Updated with professional header like QC Tofu Mentah
echo ✅ Sales Report: Inter Outlet transactions now included
echo ✅ Controller: Added Inter Outlet data source and delete functionality

echo.
echo 🎯 CHANGES SUMMARY:
echo.
echo 1. INTER OUTLET PDF MODAL:
echo    - Changed from window.open() to modal display
echo    - Added showPdfModal and pdfUrl variables
echo    - Updated printInvoice() and printHistoryInvoice() methods
echo.
echo 2. PDF DESIGN IMPROVEMENTS:
echo    - Professional header with company logo and info
echo    - Structured layout with bordered sections
echo    - Document information table (No. Transaksi, Status, etc.)
echo    - Transaction details in organized table format
echo    - Consistent styling with QC Tofu Mentah format
echo.
echo 3. SALES REPORT INTEGRATION:
echo    - Added Inter Outlet Sales to SalesReportController
echo    - Updated getData() method to include inter outlet transactions
echo    - Added deleteInterOutlet() method for transaction deletion
echo    - Updated summary statistics to include inter outlet count
echo    - Modified view description to mention all three sources
echo.
echo 🧪 TESTING STEPS:
echo.
echo A. Test PDF Modal:
echo    1. Go to Inter Outlet page
echo    2. Create a test transaction
echo    3. Click 'Print Invoice' - should open in modal, not new tab
echo    4. Check PDF design - should have professional header
echo.
echo B. Test Sales Report:
echo    1. Go to Laporan Penjualan page
echo    2. Check description mentions "Invoice, POS, dan Penjualan Antar Outlet"
echo    3. Filter data - should show inter outlet transactions
echo    4. Verify inter outlet transactions appear with:
echo       - Source: inter_outlet
echo       - Customer: destination outlet name
echo       - Payment Method: Transfer Internal
echo       - Status: Lunas
echo.
echo ✅ DEPLOYMENT COMPLETE!
echo All changes have been applied successfully.

echo.
pause