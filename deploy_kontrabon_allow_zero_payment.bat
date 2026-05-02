@echo off
echo ========================================
echo DEPLOY KONTRA BON - ALLOW ZERO PAYMENT
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
echo 1. Allow pembayaran = 0 for penagihan without payment
echo 2. Create details even when pembayaran = 0
echo 3. Details show piutang yang dicentang (selected piutang only)
echo 4. Print PDF will show selected piutang
echo.
echo USE CASES:
echo - Pembayaran = 0: Penagihan hutang tanpa pembayaran langsung
echo - Pembayaran ^> 0: Penagihan dengan pembayaran langsung
echo.
echo NEXT STEPS:
echo 1. Open browser and go to Kontra Bon page
echo 2. Click "Tambah Kontra Bon"
echo 3. Select customer "PT.Champ Resto Indonesia"
echo 4. Check ONE piutang from the list
echo 5. Leave pembayaran = 0 (for penagihan only)
echo 6. Click "Buat Kontra Bon"
echo 7. After creating, click Print
echo 8. Verify PDF shows ONLY the selected piutang
echo.
pause
