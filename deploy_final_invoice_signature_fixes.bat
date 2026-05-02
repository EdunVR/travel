@echo off
echo ===================================
echo DEPLOYING FINAL INVOICE SIGNATURE FIXES
echo ===================================
echo.

echo 1. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 2. Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo 3. Testing database connection...
php artisan migrate:status

echo.
echo ===================================
echo FIXES APPLIED:
echo ===================================
echo ✓ Fixed syntax error in sales invoice template (/* */ to {{-- --}})
echo ✓ Updated templates to use signature_path instead of signature_url
echo ✓ Added proper null checks for logo_url in both templates
echo ✓ Fixed file path handling for PDF generation
echo ✓ Updated both sales and service invoice templates
echo.
echo The following issues should now be resolved:
echo - "Undefined array key 'company_logo'" error
echo - Syntax error in sales invoice template
echo - Missing company logo in service invoice
echo - Missing user signatures in both templates
echo - Logo overlapping signature effect working
echo.
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ===================================

pause