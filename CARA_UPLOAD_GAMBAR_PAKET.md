# Cara Upload Gambar Paket ke Server Production

## Masalah
Gambar paket tidak muncul di website production karena file gambar ada di local tapi tidak ter-push ke git (folder `storage` di-ignore).

## Solusi

### Opsi 1: Upload Manual via FTP/cPanel (RECOMMENDED)

1. **Buka File Manager di cPanel atau FTP Client**

2. **Navigate ke folder:**
   ```
   /home/username/public_html/storage/app/public/travel-packages/
   ```

3. **Upload semua file gambar dari local:**
   ```
   Local: C:\xampp\htdocs\hm\storage\app\public\travel-packages\
   Server: /home/username/public_html/storage/app/public/travel-packages/
   ```

4. **Set permissions:**
   ```bash
   chmod 755 storage/app/public/travel-packages/
   chmod 644 storage/app/public/travel-packages/*
   ```

### Opsi 2: Zip dan Upload

1. **Zip folder gambar di local:**
   ```bash
   cd storage/app/public
   zip -r travel-packages.zip travel-packages/
   ```

2. **Upload `travel-packages.zip` ke server**

3. **Extract di server:**
   ```bash
   cd /home/username/public_html/storage/app/public/
   unzip travel-packages.zip
   chmod -R 755 travel-packages/
   ```

### Opsi 3: Rsync (Jika ada SSH access)

```bash
rsync -avz storage/app/public/travel-packages/ username@server:/home/username/public_html/storage/app/public/travel-packages/
```

## Verifikasi

Setelah upload, cek apakah gambar bisa diakses:

```
https://yourdomain.com/storage/travel-packages/H8ogPoVI9AaJF1wZNNpJzndaXRTV4ylSyZgcoBDt.jpg
```

## Catatan Penting

1. **Jangan push folder storage ke git** - Ini adalah best practice Laravel
2. **Backup gambar secara terpisah** - Simpan backup gambar di tempat lain
3. **Gunakan CDN untuk production** - Pertimbangkan menggunakan AWS S3, Cloudinary, atau CDN lain untuk performa lebih baik

## File yang Perlu Diupload

Berikut daftar file gambar yang ada di local:

```bash
# Cek semua file gambar
ls -lh storage/app/public/travel-packages/*.jpg
ls -lh storage/app/public/travel-packages/*.png
```

Total file: ~40-50 gambar paket

## Symbolic Link

Pastikan symbolic link sudah dibuat di server:

```bash
php artisan storage:link
```

Ini akan membuat link dari `public/storage` ke `storage/app/public`
