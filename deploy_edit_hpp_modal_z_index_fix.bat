@echo off
echo === DEPLOYING EDIT HPP MODAL Z-INDEX FIX ===

echo.
echo 1. Testing modal z-index hierarchy...
php test_edit_hpp_modal_z_index_fix.php

echo.
echo 2. Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear

echo.
echo === DEPLOYMENT COMPLETE ===
echo Modal edit HPP sekarang memiliki z-index tertinggi (z-80)
echo Modal akan tampil di depan semua modal lainnya

echo.
echo === TESTING INSTRUCTIONS ===
echo 1. Login sebagai Super Admin
echo 2. Buka Inventaris ^> Produk
echo 3. Klik tombol HPP pada produk
echo 4. Klik tombol Edit (ikon pensil biru) pada data HPP
echo 5. Verifikasi modal edit HPP muncul di depan

pause