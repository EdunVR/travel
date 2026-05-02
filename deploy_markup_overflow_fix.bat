@echo off
echo ========================================
echo DEPLOYING MARKUP OVERFLOW FIX
echo ========================================
echo.

echo 1. Testing markup overflow fix...
php test_markup_overflow_fix.php
if %errorlevel% neq 0 (
    echo ERROR: Markup overflow fix test failed
    pause
    exit /b 1
)

echo.
echo ========================================
echo DEPLOYMENT SUMMARY
echo ========================================
echo ✓ Database column markup_percent increased to DECIMAL(10,2)
echo ✓ Controller validation with maximum limits added
echo ✓ Frontend calculation protection implemented
echo ✓ Input field max attributes added
echo ✓ Sanitization before database save
echo.
echo MAXIMUM LIMITS:
echo - Inter outlet price: 999,999,999,999.99
echo - Markup percent: 99,999,999.99%%
echo.
echo ERROR FIXED:
echo - "Numeric value out of range" for markup_percent
echo - Prevents overflow when calculating markup from price
echo - User-friendly error messages for extreme values
echo.
echo Deployment completed successfully!
pause