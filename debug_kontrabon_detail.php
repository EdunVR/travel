<?php

/**
 * Debug Script - Kontra Bon Detail
 * 
 * Script ini untuk mengecek apakah detail kontra bon tersimpan dengan benar
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KontraBon;
use App\Models\KontraBonDetail;
use App\Models\Piutang;

echo "========================================\n";
echo "DEBUG KONTRA BON DETAIL\n";
echo "========================================\n\n";

// Ambil kontra bon terakhir
$kontraBon = KontraBon::with(['details', 'member'])->latest()->first();

if (!$kontraBon) {
    echo "❌ Tidak ada kontra bon di database\n";
    echo "\nSilakan buat kontra bon terlebih dahulu\n";
    exit;
}

echo "Kontra Bon Terakhir:\n";
echo "-------------------\n";
echo "ID: {$kontraBon->id_kontra_bon}\n";
echo "No: {$kontraBon->no_kontra_bon}\n";
echo "Customer: {$kontraBon->member->nama}\n";
echo "Tanggal: {$kontraBon->created_at->format('d/m/Y H:i:s')}\n";
echo "Status: {$kontraBon->status}\n";
echo "\n";

// Cek detail
echo "Detail Kontra Bon:\n";
echo "-------------------\n";
$details = KontraBonDetail::where('id_kontra_bon', $kontraBon->id_kontra_bon)->get();

if ($details->count() == 0) {
    echo "❌ TIDAK ADA DETAIL!\n";
    echo "\nMasalah: Detail kontra bon tidak tersimpan\n";
    echo "Solusi: Cek method store() di controller\n";
    exit;
}

echo "✅ Jumlah Detail: {$details->count()}\n\n";

foreach ($details as $index => $detail) {
    echo "Detail #" . ($index + 1) . ":\n";
    echo "  - ID Detail: {$detail->id_kontra_bon_detail}\n";
    echo "  - ID Penjualan: {$detail->id_penjualan}\n";
    echo "  - Nominal: Rp " . number_format($detail->nominal, 0, ',', '.') . "\n";
    
    // Cek piutang terkait
    $piutang = Piutang::where('id_penjualan', $detail->id_penjualan)->first();
    if ($piutang) {
        echo "  - Piutang Found: ✅\n";
        echo "  - Status Piutang: {$piutang->status}\n";
        echo "  - Sisa Piutang: Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
    } else {
        echo "  - Piutang Found: ❌\n";
    }
    echo "\n";
}

// Hitung total
$totalDetail = $details->sum('nominal');
echo "Total dari Detail: Rp " . number_format($totalDetail, 0, ',', '.') . "\n";
echo "Total Pembayaran: Rp " . number_format($kontraBon->total_pembayaran, 0, ',', '.') . "\n";

if ($totalDetail == $kontraBon->total_pembayaran) {
    echo "✅ Total SESUAI\n";
} else {
    echo "⚠️ Total TIDAK SESUAI\n";
}

echo "\n";
echo "========================================\n";
echo "KESIMPULAN\n";
echo "========================================\n\n";

if ($details->count() > 0) {
    echo "✅ Detail kontra bon tersimpan dengan benar\n";
    echo "✅ Jumlah detail: {$details->count()}\n";
    echo "✅ Total detail: Rp " . number_format($totalDetail, 0, ',', '.') . "\n";
    echo "\n";
    echo "Jika print PDF tidak menampilkan data:\n";
    echo "1. Clear cache: php artisan cache:clear\n";
    echo "2. Clear view: php artisan view:clear\n";
    echo "3. Cek log: storage/logs/laravel.log\n";
    echo "4. Cek method print() di controller\n";
} else {
    echo "❌ Detail kontra bon TIDAK tersimpan\n";
    echo "\n";
    echo "Kemungkinan masalah:\n";
    echo "1. Form tidak mengirim piutang_ids[]\n";
    echo "2. Validation gagal\n";
    echo "3. Transaction rollback\n";
    echo "4. Error di method store()\n";
    echo "\n";
    echo "Cek:\n";
    echo "1. Console browser (F12) → Network tab\n";
    echo "2. storage/logs/laravel.log\n";
    echo "3. Method store() di KontraBonController\n";
}

echo "\n";
