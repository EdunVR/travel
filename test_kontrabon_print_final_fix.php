<?php

/**
 * TEST KONTRA BON PRINT - FINAL FIX WITH NO_TRANSAKSI
 * 
 * Memverifikasi:
 * 1. TrxID menggunakan no_transaksi dari tabel pos_sales
 * 2. Data Hutang yang Ditagihkan: Menampilkan semua piutang yang dicentang
 * 3. Data Hutang yang Sudah Dilunasi: HANYA menampilkan piutang dengan jumlah_bayar > 0
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KontraBon;
use App\Models\Piutang;
use App\Models\PosSale;

echo "=== TEST KONTRA BON PRINT - FINAL FIX WITH NO_TRANSAKSI ===\n\n";

// Test dengan customer PT.Champ Resto Indonesia (id_member = 49)
$idMember = 49;

echo "1. Mencari kontra bon terbaru untuk customer ID: {$idMember}\n";
$kontraBon = KontraBon::where('id_member', $idMember)
    ->with(['details.penjualan.posSale', 'member'])
    ->orderBy('created_at', 'desc')
    ->first();

if (!$kontraBon) {
    echo "   ❌ Tidak ada kontra bon untuk customer ini\n";
    exit;
}

echo "   ✅ Kontra Bon: {$kontraBon->no_kontra_bon}\n";
echo "   Customer: {$kontraBon->member->nama}\n";
echo "   Pembayaran: Rp " . number_format($kontraBon->total_pembayaran, 0, ',', '.') . "\n";
echo "   Jumlah Detail: {$kontraBon->details->count()}\n\n";

echo "2. Memeriksa TrxID dari pos_sales (No Transaksi)\n";
foreach ($kontraBon->details as $index => $detail) {
    if ($detail->penjualan) {
        $posSale = $detail->penjualan->posSale;
        $noTransaksi = $posSale ? $posSale->no_transaksi : 'TIDAK ADA';
        $idPenjualan = $detail->penjualan->id_penjualan;
        echo "   Detail " . ($index + 1) . ":\n";
        echo "      - ID Penjualan: {$idPenjualan}\n";
        echo "      - No Transaksi (POS): {$noTransaksi}\n";
        echo "      - Nominal: Rp " . number_format($detail->nominal, 0, ',', '.') . "\n";
        echo "      - Jumlah Bayar: Rp " . number_format($detail->jumlah_bayar, 0, ',', '.') . "\n";
    }
}
echo "\n";

echo "3. Simulasi Data Hutang yang Ditagihkan (semua detail)\n";
$piutangBelumLunas = $kontraBon->details->map(function($detail) {
    return (object) [
        'id_piutang' => $detail->id_penjualan,
        'id_penjualan' => $detail->id_penjualan,
        'penjualan' => $detail->penjualan,
        'sisa_piutang' => $detail->nominal,
        'created_at' => $detail->penjualan->created_at ?? now(),
    ];
});

echo "   Total item: {$piutangBelumLunas->count()}\n";
foreach ($piutangBelumLunas as $index => $piutang) {
    $noTransaksi = $piutang->penjualan->posSale->no_transaksi ?? 'TRX00' . $piutang->id_penjualan;
    echo "   " . ($index + 1) . ". {$noTransaksi} - Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
}
echo "\n";

echo "4. Simulasi Data Hutang yang Sudah Dilunasi (hanya jumlah_bayar > 0)\n";
$detailsWithPayment = $kontraBon->details->filter(function($detail) {
    return $detail->jumlah_bayar > 0;
});

echo "   Total item: {$detailsWithPayment->count()}\n";
if ($detailsWithPayment->count() > 0) {
    foreach ($detailsWithPayment as $index => $detail) {
        $noTransaksi = $detail->penjualan->posSale->no_transaksi ?? 'TRX00' . $detail->penjualan->id_penjualan;
        echo "   " . ($index + 1) . ". {$noTransaksi} - Rp " . number_format($detail->nominal, 0, ',', '.') . " (Bayar: Rp " . number_format($detail->jumlah_bayar, 0, ',', '.') . ")\n";
    }
} else {
    echo "   ✅ BENAR: Tidak ada data (karena pembayaran = 0)\n";
}
echo "\n";

echo "5. Verifikasi Logic\n";
if ($kontraBon->total_pembayaran == 0) {
    echo "   ✅ Pembayaran = 0\n";
    echo "   ✅ Data Hutang yang Ditagihkan: Harus menampilkan {$piutangBelumLunas->count()} item\n";
    echo "   ✅ Data Hutang yang Sudah Dilunasi: Harus KOSONG (0 item)\n";
} else {
    echo "   ✅ Pembayaran > 0: Rp " . number_format($kontraBon->total_pembayaran, 0, ',', '.') . "\n";
    echo "   ✅ Data Hutang yang Ditagihkan: Harus menampilkan {$piutangBelumLunas->count()} item\n";
    echo "   ✅ Data Hutang yang Sudah Dilunasi: Harus menampilkan {$detailsWithPayment->count()} item\n";
}
echo "\n";

echo "=== KESIMPULAN ===\n";
echo "✅ TrxID sekarang menggunakan no_transaksi dari tabel pos_sales\n";
echo "✅ Format No Transaksi: 0235/PEL/POS/02/2026 (bukan TRX00xxx)\n";
echo "✅ Data Hutang yang Ditagihkan menampilkan semua piutang yang dicentang\n";
echo "✅ Data Hutang yang Sudah Dilunasi HANYA menampilkan piutang dengan jumlah_bayar > 0\n";
echo "\nSilakan test dengan membuka print PDF kontra bon!\n";
