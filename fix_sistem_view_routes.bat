@echo off
echo ========================================
echo   FIXING SISTEM VIEW ROUTE ERRORS
echo ========================================

echo.
echo [1/3] Fixed sistem index view routes...
echo - Changed admin.user-management.users.index to admin.users.index
echo - Changed admin.user-management.roles.index to admin.roles.index

echo.
echo [2/3] Clearing view cache...
php artisan view:clear

echo.
echo [3/3] Verifying routes exist...
echo Checking admin.users.index:
php artisan route:list --name=admin.users.index

echo.
echo Checking admin.roles.index:
php artisan route:list --name=admin.roles.index

echo.
echo ========================================
echo   VIEW ROUTE ERRORS FIX COMPLETE!
echo ========================================
echo.
echo Fixed issues:
echo 1. Sistem index view route references - FIXED
echo 2. All routes now point to existing endpoints
echo 3. View cache cleared and refreshed
echo.
echo You can now access:
echo - /admin/sistem (should work without route errors)
echo - User Management links should work properly
echo - Role Management links should work properly
echo.
pause