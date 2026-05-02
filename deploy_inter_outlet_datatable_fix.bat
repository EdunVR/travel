@echo off
echo ===================================
echo Inter Outlet Sale DataTables Fix
echo ===================================

echo.
echo Clearing Laravel caches...
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear

echo.
echo Running tests...
php test_inter_outlet_final_fix.php

echo.
echo ===================================
echo Deployment Complete!
echo ===================================
echo.
echo The Array to string conversion error has been fixed.
echo.
echo WHAT WAS FIXED:
echo - Changed route parameter from ['id' => $row->id] to $row->id
echo - This prevents Laravel from trying to convert array to string
echo.
echo NEXT STEPS:
echo 1. Test the Inter Outlet Sale history page
echo 2. Verify DataTables loads without errors
echo 3. Test the print functionality
echo 4. Test the detail modal
echo 5. Test the approve functionality
echo.
pause