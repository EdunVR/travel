@echo off
echo ========================================
echo DEPLOYING INVOICE SIGNATURE FIXES
echo ========================================

echo.
echo 1. Running migration to ensure signature_path column exists...
php artisan migrate --force

echo.
echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo 3. Creating signature directory if not exists...
if not exist "public\img\signatures" mkdir "public\img\signatures"

echo.
echo 4. Setting permissions for signature directory...
icacls "public\img\signatures" /grant Everyone:F

echo.
echo 5. Testing the fixes...
php test_invoice_signature_fixes.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo WHAT'S BEEN FIXED:
echo - Due dates in sales invoice now show without decimals
echo - Signature sections in both invoices now match
echo - Service invoice now shows company logo
echo - Both invoices use dynamic user signatures + company stamp
echo - User management modal now has signature upload functionality
echo.
echo NEXT STEPS:
echo 1. Go to User Management and upload signature images for users
echo 2. Test printing invoices to verify signatures appear
echo 3. Check that due dates show as whole numbers (e.g., "20 hari lagi")
echo.
pause