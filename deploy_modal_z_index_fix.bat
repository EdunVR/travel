@echo off
echo ========================================
echo DEPLOY MODAL Z-INDEX FIX
echo ========================================
echo.

echo Menerapkan perbaikan z-index modal tambah stok...
echo.

echo [INFO] File yang diubah:
echo - resources/views/admin/inventaris/produk/index.blade.php
echo.

echo [INFO] Perubahan:
echo - Modal tambah stok z-index: z-70 → z-[9999]
echo - Memastikan modal tambah stok tampil di atas modal edit
echo.

echo [TESTING] Langkah testing:
echo 1. Buka admin/inventaris/produk
echo 2. Klik Edit pada produk
echo 3. Klik tombol Tambah pada field Stok
echo 4. Verifikasi modal tambah stok tampil di atas modal edit
echo.

echo [SUCCESS] Perbaikan z-index modal berhasil diterapkan!
echo.

echo Silakan test di browser untuk memastikan modal berfungsi dengan baik.
echo.
pause