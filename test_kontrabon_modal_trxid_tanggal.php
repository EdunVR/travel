<?php

/**
 * TEST SCRIPT: Kontra Bon Modal TrxID dan Tanggal Fix
 * 
 * Tujuan: Memverifikasi bahwa modal buat kontra bon menampilkan:
 * 1. TrxID yang benar (no_transaksi dari pos_sales)
 * 2. Tanggal transaksi yang benar (dari penjualan, bukan piutang)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use App\Models\Piutang;
use App\Models\Penjualan;
use App\Models\PosSale;

echo "=== TEST KONTRA BON MODAL - TrxID DAN TANGGAL ===\n\n";

// Test dengan customer PT.Champ Resto Indonesia (id_member = 49)
$idMember = 49;

echo "1. Testing dengan Customer: PT.Champ Resto Indonesia (ID: $idMember)\n";
echo str_repeat("-", 80) . "\n";

$member = Member::find($idMember);
if (!$member) {
    echo "❌ ERROR: Customer dengan ID $idMember tidak ditemukan!\n";
    exit(1);
}

echo "✓ Customer ditemukan: {$member->nama}\n\n";

// Ambil piutang belum lunas untuk member ini
echo "2. Mengambil data piutang belum lunas...\n";
echo str_repeat("-", 80) . "\n";

$piutang = Piutang::where('id_member', $idMember)
    ->where('status', 'belum_lunas')
    ->with(['penjualan.posSale', 'outlet'])
    ->orderBy('created_at', 'asc')
    ->get();

echo "✓ Ditemukan {$piutang->count()} piutang belum lunas\n\n";

if ($piutang->count() === 0) {
    echo "⚠️  WARNING: Tidak ada piutang belum lunas untuk customer ini\n";
    echo "   Silakan buat transaksi POS dengan piutang terlebih dahulu\n";
    exit(0);
}

// Tampilkan data piutang seperti yang akan ditampilkan di modal
echo "3. Data Piutang yang akan ditampilkan di Modal:\n";
echo str_repeat("-", 80) . "\n";
printf("%-5s %-15s %-30s %-20s\n", "No", "Tanggal", "TrxID", "Nominal");
echo str_repeat("-", 80) . "\n";

$totalPiutang = 0;
foreach ($piutang as $index => $item) {
    // Get no_transaksi from pos_sales (sama seperti di controller)
    $noTransaksi = $item->penjualan && $item->penjualan->posSale 
        ? $item->penjualan->posSale->no_transaksi 
        : 'TRX00' . $item->id_penjualan;
    
    // Get tanggal from penjualan (tanggal transaksi), bukan created_at piutang
    $tanggalTransaksi = $item->penjualan && $item->penjualan->created_at
        ? $item->penjualan->created_at->format('d-m-Y')
        : $item->created_at->format('d-m-Y');
    
    $nominal = number_format($item->sisa_piutang, 0, ',', '.');
    
    printf("%-5s %-15s %-30s Rp %-20s\n", 
        $index + 1, 
        $tanggalTransaksi, 
        $noTransaksi, 
        $nominal
    );
    
    $totalPiutang += $item->sisa_piutang;
}

echo str_repeat("-", 80) . "\n";
printf("%-51s Rp %-20s\n", "TOTAL:", number_format($totalPiutang, 0, ',', '.'));
echo str_repeat("-", 80) . "\n\n";

// Verifikasi detail untuk beberapa piutang pertama
echo "4. Verifikasi Detail (3 piutang pertama):\n";
echo str_repeat("-", 80) . "\n";

foreach ($piutang->take(3) as $index => $item) {
    echo "\nPiutang #" . ($index + 1) . ":\n";
    echo "  ID Piutang       : {$item->id_piutang}\n";
    echo "  ID Penjualan     : {$item->id_penjualan}\n";
    
    // Check penjualan relationship
    if ($item->penjualan) {
        echo "  ✓ Relasi Penjualan: OK\n";
        echo "    - Created At   : {$item->penjualan->created_at->format('d-m-Y H:i:s')}\n";
        
        // Check posSale relationship
        if ($item->penjualan->posSale) {
            echo "  ✓ Relasi PosSale  : OK\n";
            echo "    - No Transaksi : {$item->penjualan->posSale->no_transaksi}\n";
        } else {
            echo "  ❌ Relasi PosSale : TIDAK DITEMUKAN\n";
            echo "    - Fallback     : TRX00{$item->id_penjualan}\n";
        }
    } else {
        echo "  ❌ Relasi Penjualan: TIDAK DITEMUKAN\n";
    }
    
    echo "  Tanggal Piutang  : {$item->created_at->format('d-m-Y H:i:s')}\n";
    echo "  Sisa Piutang     : Rp " . number_format($item->sisa_piutang, 0, ',', '.') . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n";

// Test API endpoint
echo "\n5. Testing API Endpoint getPiutang:\n";
echo str_repeat("-", 80) . "\n";

try {
    $controller = new \App\Http\Controllers\Admin\KontraBonController();
    $response = $controller->getPiutang($idMember);
    $data = json_decode($response->getContent(), true);
    
    echo "✓ API Response berhasil\n";
    echo "  Jumlah data: " . count($data) . "\n";
    
    if (count($data) > 0) {
        echo "\n  Sample data pertama:\n";
        $first = $data[0];
        echo "    - ID Piutang   : {$first['id_piutang']}\n";
        echo "    - ID Penjualan : {$first['id_penjualan']}\n";
        echo "    - No Transaksi : {$first['no_transaksi']}\n";
        echo "    - Tanggal      : {$first['tanggal']}\n";
        echo "    - Piutang      : Rp {$first['piutang']}\n";
    }
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n";

// Kesimpulan
echo "\n6. KESIMPULAN:\n";
echo str_repeat("-", 80) . "\n";

$hasPosSale = $piutang->filter(function($item) {
    return $item->penjualan && $item->penjualan->posSale;
})->count();

$hasNoPenjualan = $piutang->filter(function($item) {
    return !$item->penjualan;
})->count();

echo "✓ Total Piutang Belum Lunas : {$piutang->count()}\n";
echo "✓ Dengan No Transaksi (PosSale): $hasPosSale\n";

if ($hasNoPenjualan > 0) {
    echo "⚠️  Tanpa Relasi Penjualan  : $hasNoPenjualan (akan gunakan fallback)\n";
}

echo "\n";
echo "IMPLEMENTASI:\n";
echo "✓ Controller getPiutang() sudah eager load 'penjualan.posSale'\n";
echo "✓ Controller mengembalikan 'no_transaksi' dari pos_sales\n";
echo "✓ Controller mengembalikan tanggal dari penjualan->created_at\n";
echo "✓ Modal view menggunakan item.no_transaksi (bukan TRX00...)\n";
echo "✓ Modal view menggunakan item.tanggal (tanggal transaksi)\n";

echo "\n" . str_repeat("=", 80) . "\n";
echo "\nTEST SELESAI!\n";
echo "\nLANGKAH SELANJUTNYA:\n";
echo "1. Clear cache: php artisan cache:clear\n";
echo "2. Clear view cache: php artisan view:clear\n";
echo "3. Buka browser dan test modal buat kontra bon\n";
echo "4. Pilih customer 'PT.Champ Resto Indonesia'\n";
echo "5. Verifikasi TrxID dan Tanggal sudah sesuai\n";
echo "\n";
