@echo off
echo ===================================
echo     API Outlets Fix Deploy
echo ===================================
echo.

echo 1. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 2. Testing API outlets fix...
php test_api_outlets_fix.php

echo.
echo 3. Deployment Summary:
echo ✅ Added /api/outlets route to routes/api.php
echo ✅ Applied auth middleware to outlets API
echo ✅ Updated Service Dashboard to use correct endpoint
echo ✅ Added proper AJAX headers for authentication
echo ✅ Added fallback outlets for error handling
echo.

echo 4. Fix Details:
echo   - Route: GET /api/outlets
echo   - Middleware: auth (requires login)
echo   - Response: JSON with success flag and data array
echo   - Headers: Accept and X-Requested-With for AJAX
echo   - Fallback: Default outlet if API fails
echo.

echo 5. Testing Instructions:
echo   - Login to the application first
echo   - Visit: http://localhost/tofu/admin/service
echo   - Check browser console for no 404 errors
echo   - Verify outlet dropdown loads correctly
echo   - Test checkbox selection functionality
echo.

echo ===================================
echo     API Outlets Fix Complete
echo ===================================
pause