@echo off
echo ========================================
echo  TESTING POTENTIAL ALPINE.JS ISSUES
echo ========================================
echo.
echo Berdasarkan analisis, halaman berikut berpotensi mengalami
echo masalah Alpine.js yang sama dengan halaman Role & Permission:
echo.

echo 🚨 HALAMAN YANG PERLU DICEK:
echo.
echo 1. PERMINTAAN BARANG
echo    URL: /admin/supply-chain/permintaan-barang
echo    Function: permintaanBarangApp()
echo    Error yang mungkin: "permintaanBarangApp is not defined"
echo.

echo 2. SERVICE ONGKIR  
echo    URL: /admin/service/ongkir
echo    Function: ongkirCrud()
echo    Error yang mungkin: "ongkirCrud is not defined"
echo.

echo 3. SERVICE MESIN
echo    URL: /admin/service/mesin
echo    Function: mesinCrud()
echo    Error yang mungkin: "mesinCrud is not defined"
echo.

echo 4. SDM KINERJA
echo    URL: /admin/sdm/kinerja
echo    Function: kinerjaCrud()
echo    Error yang mungkin: "kinerjaCrud is not defined"
echo.

echo ========================================
echo  CARA TESTING
echo ========================================
echo.
echo 1. Buka browser dan navigate ke setiap URL di atas
echo 2. Buka Developer Tools (F12)
echo 3. Lihat Console tab
echo 4. Cari error seperti:
echo    - "functionName is not defined"
echo    - "Alpine Expression Error"
echo    - "init is not defined"
echo.
echo 5. Test functionality:
echo    - Klik tombol "Tambah"
echo    - Coba edit/delete data
echo    - Cek apakah modal berfungsi
echo    - Cek apakah form submission bekerja
echo.

echo ========================================
echo  JIKA DITEMUKAN ERROR
echo ========================================
echo.
echo Jika ada halaman yang error, laporkan:
echo 1. Nama halaman yang error
echo 2. Error message di console
echo 3. Functionality yang tidak bekerja
echo.
echo Saya akan membuat fix yang sama seperti roles page:
echo - Buat external JS file
echo - Pindahkan function ke external file  
echo - Tambah cache busting
echo - Tambah fallback function
echo.

echo ========================================
echo  MULAI TESTING SEKARANG
echo ========================================
echo.
echo Silakan test halaman-halaman di atas dan laporkan hasilnya.
echo Jika semua berfungsi normal, berarti tidak ada masalah.
echo Jika ada yang error, kita akan fix dengan pola yang sama.
echo.
pause