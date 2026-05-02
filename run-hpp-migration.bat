@echo off
echo ========================================
echo HPP Custom Components - SQL Migration
echo ========================================
echo.
echo This script will add custom_components columns to hpp_calculations table
echo.
set /p dbname="Enter database name: "
set /p dbuser="Enter MySQL username (default: root): "
if "%dbuser%"=="" set dbuser=root

echo.
echo Running SQL migration...
echo.

mysql -u %dbuser% -p %dbname% < add-custom-components-column.sql

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo SUCCESS! Columns added successfully.
    echo ========================================
    echo.
    echo Next steps:
    echo 1. Test HPP modal - add custom components
    echo 2. Verify components save with original names
    echo 3. Create keberangkatan and generate RAB
    echo 4. Check RAB has individual items for each custom component
    echo.
) else (
    echo.
    echo ========================================
    echo ERROR! Migration failed.
    echo ========================================
    echo.
    echo Please check:
    echo 1. MySQL is running
    echo 2. Database name is correct
    echo 3. Username and password are correct
    echo 4. You have ALTER TABLE permissions
    echo.
)

pause
