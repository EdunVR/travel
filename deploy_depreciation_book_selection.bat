@echo off
echo ========================================
echo DEPLOYING DEPRECIATION BOOK SELECTION
echo ========================================
echo.

echo 1. CLEARING CACHE...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. TESTING DEPRECIATION BOOK SELECTION...
php test_depreciation_book_selection.php

echo.
echo 3. CHECKING ROUTES...
php artisan route:list | findstr "depreciation"

echo.
echo 4. OPTIMIZING FOR PRODUCTION...
php artisan config:cache
php artisan route:cache

echo.
echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Test the book selection modal in browser
echo 2. Verify single posting works with book selection
echo 3. Verify bulk posting works with book selection
echo 4. Check console logs for debugging info
echo 5. Verify journals are created in selected books
echo.
echo DEBUGGING:
echo - Open Developer Tools (F12) to see console logs
echo - Check Network tab for API requests/responses
echo - Ensure outlet has active accounting books
echo - Verify user has permission for the outlet
echo.
pause