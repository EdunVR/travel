@echo off
echo ========================================
echo DEPLOY KONTRA BON PRINT - FINAL FIX
echo ========================================
echo.
echo Fixes:
echo 1. TrxID menggunakan kode_penjualan dari tabel penjualan
echo 2. Data Hutang yang Sudah Dilunasi HANYA menampilkan piutang dengan jumlah_bayar ^> 0
echo.
echo ========================================
echo.

echo Step 1: Clear cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
echo.

echo Step 2: Test implementation...
php test_kontrabon_print_final_fix.php
echo.

echo ========================================
echo DEPLOYMENT COMPLETE!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Buka halaman Kontra Bon
echo 2. Pilih customer PT.Champ Resto Indonesia
echo 3. Centang 1 piutang dari modal
echo 4. Isi pembayaran = 0
echo 5. Buat kontra bon
echo 6. Klik tombol Print
echo.
echo EXPECTED RESULTS:
echo - TrxID menampilkan kode invoice yang benar (bukan TRX00xxx)
echo - Data Hutang yang Ditagihkan: Menampilkan piutang yang dicentang
echo - Data Hutang yang Sudah Dilunasi: KOSONG (karena pembayaran = 0)
echo.
pause
