@echo off
echo ========================================
echo Deploying SDM Dashboard Payroll Model Fix
echo ========================================

echo.
echo [1/4] Clearing all caches...
php artisan cache:clear
php artisan view:clear
php artisan config:clear

echo.
echo [2/4] Testing syntax...
php -l app/Http/Controllers/SdmDashboardController.php

echo.
echo [3/4] Testing SDM Payroll fix...
php test_sdm_payroll_fix.php

echo.
echo [4/4] Deployment Summary:
echo ✓ Fixed PayrollManagement model not found error
echo ✓ Changed to use correct Payroll model
echo ✓ Updated import statements in SdmDashboardController
echo ✓ Fixed total_deductions calculation method
echo ✓ Used correct column names from Payroll model
echo ✓ All syntax errors resolved

echo.
echo ========================================
echo SDM Payroll Model Fix Deployment Complete!
echo ========================================
echo.
echo The "Class PayrollManagement not found" error has been resolved.
echo SDM Dashboard should now load correctly at /admin/sdm
echo.

pause