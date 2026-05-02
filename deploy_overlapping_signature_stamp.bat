@echo off
echo ========================================
echo DEPLOYING OVERLAPPING SIGNATURE STAMP
echo ========================================

echo.
echo 1. Clearing view cache to ensure changes take effect...
php artisan view:clear

echo.
echo 2. Testing the overlapping signature implementation...
php test_overlapping_signature_stamp.php

echo.
echo ========================================
echo DEPLOYMENT COMPLETED
echo ========================================
echo.
echo WHAT'S BEEN UPDATED:
echo - Logo/cap now overlaps 50%% right side of signature
echo - Logo positioned absolutely with -10px right offset
echo - Logo has 80%% opacity for subtle overlay effect
echo - Signature height increased to 60px for better visibility
echo - Both sales and service invoices updated consistently
echo.
echo VISUAL EFFECT:
echo ┌─────────────────────────┐
echo │  [Signature Image]      │
echo │              [Logo/Cap] │ ← Logo overlaps here
echo │                         │
echo └─────────────────────────┘
echo         User Name
echo.
echo NEXT STEPS:
echo 1. Test print preview of invoices
echo 2. Verify logo appears as overlay on signature
echo 3. Check that signature is still readable
echo 4. Adjust positioning if needed
echo.
pause