@echo off
echo ========================================
echo DEPLOY KONTRA BON - PEMBAYARAN VALIDATION
echo ========================================
echo.

echo Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear

echo.
echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo WHAT WAS FIXED:
echo 1. Added validation: pembayaran must be greater than 0
echo 2. Added UI hint to fill pembayaran or use Auto Pilih Hutang
echo 3. Added console log for pembayaran value
echo.
echo ROOT CAUSE:
echo - piutang_ids[] was sent correctly
echo - BUT pembayaran = 0 (user didn't fill the field)
echo - Controller skipped creating details because sisaBayar = 0
echo.
echo SOLUTION:
echo - User MUST fill pembayaran field OR
echo - User MUST click "Auto Pilih Hutang" button
echo.
echo NEXT STEPS:
echo 1. Open browser and go to Kontra Bon page
echo 2. Click "Tambah Kontra Bon"
echo 3. Select customer "PT.Champ Resto Indonesia"
echo 4. Check ONE piutang from the list
echo 5. IMPORTANT: Fill pembayaran field (e.g., 18468000)
echo    OR click "Auto Pilih Hutang" button
echo 6. Click "Buat Kontra Bon"
echo 7. After creating, click Print
echo 8. Verify PDF shows the selected piutang
echo.
pause
