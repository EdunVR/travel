# 🖼️ UPLOAD GAMBAR PAKET KE SERVER - PANDUAN CEPAT

## ✅ Yang Sudah Selesai
1. ✅ Kode ustadz display sudah di-push ke git
2. ✅ File zip gambar sudah dibuat: `travel-packages-upload.zip`
3. ✅ Database sudah diupdate dengan data ustadz

## 📦 File yang Perlu Diupload

File: **travel-packages-upload.zip** (sudah dibuat di root folder project)

Ukuran: ~25-30 MB (berisi semua gambar paket)

## 🚀 CARA UPLOAD (Pilih salah satu)

### Opsi 1: Via cPanel File Manager (PALING MUDAH)

1. **Login ke cPanel Hostinger**
   - Buka: https://hpanel.hostinger.com
   - Login dengan akun Anda

2. **Buka File Manager**
   - Klik "File Manager" di menu

3. **Navigate ke folder storage**
   ```
   public_html/storage/app/public/
   ```

4. **Upload file zip**
   - Klik tombol "Upload"
   - Pilih file `travel-packages-upload.zip`
   - Tunggu sampai selesai upload

5. **Extract file zip**
   - Klik kanan pada `travel-packages-upload.zip`
   - Pilih "Extract"
   - Pilih lokasi: current directory
   - Klik "Extract Files"

6. **Set Permissions**
   - Klik kanan folder `travel-packages`
   - Pilih "Change Permissions"
   - Set ke: 755 (rwxr-xr-x)
   - Centang "Recurse into subdirectories"
   - Klik "Change"

7. **Hapus file zip** (opsional)
   - Hapus `travel-packages-upload.zip` untuk menghemat space

### Opsi 2: Via FTP (FileZilla)

1. **Buka FileZilla**
   - Host: ftp.yourdomain.com
   - Username: (dari cPanel)
   - Password: (dari cPanel)
   - Port: 21

2. **Navigate ke folder**
   ```
   Remote: /public_html/storage/app/public/
   Local: C:\xampp\htdocs\hm\
   ```

3. **Upload file zip**
   - Drag & drop `travel-packages-upload.zip` ke server

4. **Extract via cPanel** (ikuti langkah 5-7 dari Opsi 1)

### Opsi 3: Via SSH (Jika ada akses)

```bash
# 1. Upload file zip ke server
scp travel-packages-upload.zip username@server:/home/username/

# 2. SSH ke server
ssh username@server

# 3. Extract
cd /home/username/public_html/storage/app/public/
unzip ~/travel-packages-upload.zip

# 4. Set permissions
chmod -R 755 travel-packages/
chown -R username:username travel-packages/

# 5. Hapus zip
rm ~/travel-packages-upload.zip
```

## 🔍 Verifikasi Upload

Setelah upload, cek apakah gambar bisa diakses:

1. **Buka browser**
2. **Test URL gambar:**
   ```
   https://yourdomain.com/storage/travel-packages/H8ogPoVI9AaJF1wZNNpJzndaXRTV4ylSyZgcoBDt.jpg
   ```
3. **Jika muncul gambar = BERHASIL! ✅**
4. **Jika 404 = Cek langkah berikut:**

## ❌ Troubleshooting

### Gambar masih 404 setelah upload?

1. **Cek symbolic link:**
   ```bash
   # Via SSH
   cd /home/username/public_html
   php artisan storage:link
   ```

2. **Cek permissions:**
   ```bash
   ls -la public/storage
   ls -la storage/app/public/travel-packages/
   ```

3. **Cek path di database:**
   ```sql
   SELECT id, package_name, image_path 
   FROM travel_packages 
   WHERE image_path LIKE '%H8ogPoVI9AaJF1wZNNpJzndaXRTV4ylSyZgcoBDt%';
   ```

4. **Cek .htaccess:**
   Pastikan file `public/.htaccess` ada dan benar

## 📊 Struktur Folder yang Benar

```
public_html/
├── public/
│   └── storage/  → (symbolic link ke ../storage/app/public)
└── storage/
    └── app/
        └── public/
            └── travel-packages/
                ├── H8ogPoVI9AaJF1wZNNpJzndaXRTV4ylSyZgcoBDt.jpg
                ├── (gambar lainnya...)
                └── photos/
```

## 🎯 Checklist

- [ ] File `travel-packages-upload.zip` sudah dibuat
- [ ] Login ke cPanel/FTP
- [ ] Upload file zip ke `public_html/storage/app/public/`
- [ ] Extract file zip
- [ ] Set permissions 755
- [ ] Test URL gambar di browser
- [ ] Refresh homepage website
- [ ] Gambar paket muncul dengan ustadz ✅

## 📝 Catatan Penting

1. **Jangan push folder storage ke git** - File upload tidak masuk git
2. **Backup gambar** - Simpan backup di Google Drive/Dropbox
3. **Untuk gambar baru** - Upload langsung via admin panel atau FTP
4. **Ukuran gambar** - Compress gambar sebelum upload untuk performa lebih baik

## 🆘 Butuh Bantuan?

Jika masih ada masalah:
1. Screenshot error yang muncul
2. Cek file `storage/logs/laravel.log` di server
3. Hubungi support Hostinger jika masalah permissions

---

**Setelah upload gambar, refresh homepage dan semua gambar paket akan muncul dengan informasi ustadz! 🎉**
