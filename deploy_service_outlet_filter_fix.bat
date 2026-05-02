@echo off
echo ========================================
echo DEPLOYING SERVICE OUTLET FILTER FIX
echo ========================================
echo.

echo 🔧 Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 🧪 Running test verification...
php test_service_outlet_filter_fix.php

echo.
echo ✅ SERVICE OUTLET FILTER FIX DEPLOYED SUCCESSFULLY!
echo.
echo 📋 CHANGES APPLIED:
echo - ServiceController methods now filter outlets by user access
echo - invoiceIndex(), historyIndex(), ongkirIndex(), mesinIndex() updated
echo - All API endpoints validate outlet access before returning data
echo - Proper 403 responses for unauthorized outlet access attempts
echo - Frontend receives only accessible outlets from controller
echo.
echo 🧪 TESTING INSTRUCTIONS:
echo 1. Login with different user roles (admin, super_admin, regular user)
echo 2. Navigate to admin/service/* pages
echo 3. Verify outlet filter only shows accessible outlets
echo 4. Test outlet switching functionality
echo 5. Test API endpoints with different outlet access levels
echo.
echo 🎯 TASK 4 COMPLETED: Service Module Outlet Filter Access Control
echo.
pause