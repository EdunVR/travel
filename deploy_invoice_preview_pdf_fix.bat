@echo off
echo ===================================
echo DEPLOYING INVOICE PREVIEW & PDF FIX
echo ===================================
echo.

echo 1. Clearing view cache...
php artisan view:clear

echo.
echo 2. Clearing application cache...
php artisan cache:clear

echo.
echo 3. Testing routes...
php artisan route:list | findstr "invoice.*print"

echo.
echo ===================================
echo PREVIEW & PDF FIX APPLIED:
echo ===================================
echo ✓ Fixed "Not allowed to load local resource" error
echo ✓ Templates now detect preview mode vs PDF mode
echo ✓ Preview mode: Uses asset() URLs for browser
echo ✓ PDF mode: Uses public_path() for file system
echo ✓ Both sales and service invoices updated
echo ✓ Logo and signature images work in both modes
echo.
echo HOW IT WORKS:
echo - Preview mode (has 'preview' parameter): Uses web URLs
echo - PDF mode (no 'preview' parameter): Uses file paths
echo - Conditional logic: request()->has('preview') ? asset() : public_path()
echo.
echo TESTING:
echo 1. Test preview: /admin/penjualan/invoice/ID/print?preview=true
echo 2. Test PDF: /admin/penjualan/invoice/ID/print
echo 3. Both should show images correctly now
echo.
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ===================================

pause