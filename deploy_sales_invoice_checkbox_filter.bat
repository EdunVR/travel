@echo off
echo ========================================
echo DEPLOYING SALES INVOICE CHECKBOX FILTER
echo ========================================
echo.

echo 1. Testing implementation...
php test_sales_invoice_checkbox_filter.php
echo.

echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo 3. Deployment complete!
echo.
echo TESTING INSTRUCTIONS:
echo 1. Open browser and go to: /admin/penjualan/invoice
echo 2. Click on the outlet filter dropdown (top-left area)
echo 3. Test checkbox selection (single, multiple, all, none)
echo 4. Verify invoice data filtering works correctly
echo 5. Test "Pilih Semua" and "Hapus Semua" buttons
echo 6. Test invoice creation with different outlet selections
echo 7. Check responsive design on mobile
echo 8. Test export functions (PDF/Excel) with filtering
echo.
echo ========================================
pause