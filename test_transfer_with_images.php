<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Produk;
use App\Models\ProductImage;
use App\Models\Outlet;

echo "=== Test Transfer Produk dengan Gambar ===\n\n";

// Cari produk dengan gambar
$produkAsal = Produk::whereHas('images')->with('images')->first();

if (!$produkAsal) {
    echo "❌ Tidak ada produk dengan gambar untuk ditest\n";
    exit;
}

echo "📦 Produk Asal:\n";
echo "   Nama: {$produkAsal->nama_produk}\n";
echo "   ID: {$produkAsal->id_produk}\n";
echo "   Outlet: {$produkAsal->id_outlet}\n";
echo "   Jumlah gambar: " . $produkAsal->images->count() . "\n";

foreach ($produkAsal->images as $img) {
    echo "   - {$img->path} (Primary: " . ($img->is_primary ? 'Ya' : 'Tidak') . ")\n";
}

echo "\n";

// Cari outlet tujuan yang berbeda
$outletTujuan = Outlet::where('id_outlet', '!=', $produkAsal->id_outlet)->first();

if (!$outletTujuan) {
    echo "❌ Tidak ada outlet tujuan yang berbeda\n";
    exit;
}

echo "🏢 Outlet Tujuan:\n";
echo "   Nama: {$outletTujuan->nama_outlet}\n";
echo "   ID: {$outletTujuan->id_outlet}\n\n";

// Cek apakah produk sudah ada di outlet tujuan
$produkTujuan = Produk::where('id_outlet', $outletTujuan->id_outlet)
    ->where('nama_produk', $produkAsal->nama_produk)
    ->first();

if ($produkTujuan) {
    echo "ℹ️  Produk sudah ada di outlet tujuan\n";
    echo "   ID: {$produkTujuan->id_produk}\n";
    echo "   Jumlah gambar saat ini: " . $produkTujuan->images->count() . "\n";
} else {
    echo "ℹ️  Produk belum ada di outlet tujuan (akan dibuat baru)\n";
}

echo "\n";
echo "=== Simulasi Selesai ===\n";
echo "\nUntuk test sebenarnya:\n";
echo "1. Buka Transfer Gudang di browser\n";
echo "2. Transfer produk '{$produkAsal->nama_produk}' dari outlet {$produkAsal->id_outlet} ke outlet {$outletTujuan->id_outlet}\n";
echo "3. Approve transfer\n";
echo "4. Cek log: tail -f storage/logs/laravel.log | grep gambar\n";
echo "5. Verifikasi gambar tersalin di database\n";
