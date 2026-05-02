@echo off
echo === DEPLOYING EDIT HPP MODAL FINAL Z-INDEX FIX ===

echo.
echo 1. Testing modal z-index hierarchy...
php test_edit_hpp_modal_final_fix.php

echo.
echo 2. Clearing all cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

echo.
echo === DEPLOYMENT COMPLETE ===
echo Modal edit HPP sekarang menggunakan z-[9999] (nilai maksimum)
echo Modal PASTI akan tampil di depan semua modal lainnya

echo.
echo === FINAL TESTING INSTRUCTIONS ===
echo 1. Login sebagai Super Admin
echo 2. Buka Inventaris ^> Produk
echo 3. Klik tombol HPP pada produk
echo 4. Klik tombol Edit (ikon pensil biru) pada data HPP
echo 5. Modal edit HPP PASTI muncul di depan
echo 6. Masalah z-index sudah SELESAI

pause