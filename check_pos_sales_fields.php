<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PosSale;
use App\Models\Penjualan;

echo "=== CHECK POS SALES & PENJUALAN RELATIONSHIP ===\n\n";

// Cek penjualan ID 1507
$penjualan = Penjualan::find(1507);

if ($penjualan) {
    echo "1. Penjualan ID: 1507\n";
    echo "   Fields:\n";
    foreach ($penjualan->getAttributes() as $key => $value) {
        echo "     {$key}: {$value}\n";
    }
    echo "\n";
    
    // Cek apakah ada pos_sale yang terkait
    $posSale = PosSale::where('id_penjualan', 1507)->first();
    
    if ($posSale) {
        echo "2. POS Sale terkait:\n";
        echo "   ID: {$posSale->id}\n";
        echo "   No Transaksi: {$posSale->no_transaksi}\n";
        echo "   Tanggal: {$posSale->tanggal}\n";
        echo "   Total: Rp " . number_format($posSale->total, 0, ',', '.') . "\n";
        echo "\n";
        echo "✅ FOUND! No Transaksi: {$posSale->no_transaksi}\n";
    } else {
        echo "2. ❌ Tidak ada POS Sale terkait\n";
        echo "   Ini adalah penjualan lama (sebelum sistem POS baru)\n";
    }
}

echo "\n=== KESIMPULAN ===\n";
echo "Field yang benar untuk TrxID:\n";
echo "- Jika ada pos_sale: gunakan pos_sale->no_transaksi\n";
echo "- Jika tidak ada: gunakan fallback TRX00{id_penjualan}\n";
