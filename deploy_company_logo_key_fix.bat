@echo off
echo ========================================
echo DEPLOYING COMPANY LOGO KEY FIX
echo ========================================

echo.
echo 1. Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 2. Testing the fix...
php test_company_logo_key_fix.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo FIXES APPLIED:
echo ✓ Fixed "Undefined array key 'company_logo'" error
echo ✓ Templates now use correct 'logo_url' key
echo ✓ Updated HasCompanySettings trait to use accessor
echo ✓ Updated ServiceController to use accessor
echo ✓ Logo paths now work correctly for PDF generation
echo.
echo KEY CHANGES:
echo - Templates use: $companySettings['logo_url']
echo - Model accessor: getLogoUrlAttribute() 
echo - Database column: company_logo
echo - Accessor converts: company_logo → logo_url
echo.
echo TESTING CHECKLIST:
echo □ Print sales invoice - no errors
echo □ Print service invoice - no errors
echo □ Company logo appears in both invoices
echo □ Logo overlaps signature correctly
echo □ Due dates show without decimals
echo.
echo TROUBLESHOOTING:
echo - If logo still missing: Check company settings has logo uploaded
echo - If path errors: Verify storage link exists (php artisan storage:link)
echo - If 404 errors: Check file permissions on storage directory
echo.
pause