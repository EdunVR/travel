<?php

/**
 * Test Script - Kontra Bon Print Detail
 * 
 * Script ini untuk test apakah method print() mengambil data dengan benar
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KontraBon;
use App\Models\KontraBonDetail;
use App\Models\Piutang;

echo "========================================\n";
echo "TEST KONTRA BON PRINT DETAIL\n";
echo "========================================\n\n";

// Ambil kontra bon terakhir
$kontraBon = KontraBon::with(['details.penjualan', 'member', 'outlet'])->latest()->first();

if (!$kontraBon) {
    echo "❌ Tidak ada kontra bon di database\n";
    exit;
}

echo "Testing Kontra Bon:\n";
echo "-------------------\n";
echo "ID: {$kontraBon->id_kontra_bon}\n";
echo "No: {$kontraBon->no_kontra_bon}\n";
echo "Customer: {$kontraBon->member->nama}\n";
echo "\n";

// Simulasi method print()
echo "Simulasi Method print():\n";
echo "------------------------\n";

$piutangBelumLunas = collect();
$totalHutang = 0;

echo "Jumlah Detail: {$kontraBon->details->count()}\n\n";

foreach ($kontraBon->details as $index => $detail) {
    echo "Processing Detail #" . ($index + 1) . ":\n";
    echo "  - ID Penjualan: {$detail->id_penjualan}\n";
    echo "  - Nominal: Rp " . number_format($detail->nominal, 0, ',', '.') . "\n";
    
    // Cari piutang
    $piutang = Piutang::where('id_penjualan', $detail->id_penjualan)
        ->with(['penjualan'])
        ->first();
    
    if ($piutang) {
        echo "  - Piutang Found: ✅\n";
        echo "  - Kode Penjualan: {$piutang->penjualan->kode_penjualan}\n";
        
        // Buat object
        $piutangItem = (object) [
            'id_piutang' => $piutang->id_piutang,
            'id_penjualan' => $piutang->id_penjualan,
            'created_at' => $piutang->created_at,
            'penjualan' => $piutang->penjualan,
            'sisa_piutang' => $detail->nominal,
            'jumlah_piutang' => $piutang->jumlah_piutang,
            'jumlah_dibayar' => $piutang->jumlah_dibayar,
            'status' => $piutang->status
        ];
        
        $piutangBelumLunas->push($piutangItem);
        $totalHutang += $detail->nominal;
        
        echo "  - Added to Collection: ✅\n";
    } else {
        echo "  - Piutang Found: ❌\n";
        echo "  - ERROR: Piutang tidak ditemukan!\n";
    }
    echo "\n";
}

echo "========================================\n";
echo "HASIL\n";
echo "========================================\n\n";

echo "Jumlah Detail Kontra Bon: {$kontraBon->details->count()}\n";
echo "Jumlah Piutang di Collection: {$piutangBelumLunas->count()}\n";
echo "Total Hutang: Rp " . number_format($totalHutang, 0, ',', '.') . "\n";
echo "\n";

if ($piutangBelumLunas->count() == 0) {
    echo "❌ COLLECTION KOSONG!\n\n";
    echo "Kemungkinan masalah:\n";
    echo "1. Detail kontra bon tidak ada\n";
    echo "2. Piutang tidak ditemukan berdasarkan id_penjualan\n";
    echo "3. Relasi penjualan tidak ada\n";
    echo "\n";
    echo "Solusi:\n";
    echo "1. Jalankan: php debug_kontrabon_detail.php\n";
    echo "2. Cek tabel kontra_bon_detail\n";
    echo "3. Cek tabel piutang\n";
} else {
    echo "✅ COLLECTION BERISI DATA\n\n";
    echo "Data yang akan dikirim ke PDF:\n";
    echo "------------------------------\n";
    foreach ($piutangBelumLunas as $index => $item) {
        echo ($index + 1) . ". {$item->penjualan->kode_penjualan} - Rp " . number_format($item->sisa_piutang, 0, ',', '.') . "\n";
    }
    echo "\n";
    echo "✅ Method print() seharusnya berfungsi dengan benar\n";
    echo "\n";
    echo "Jika PDF masih kosong:\n";
    echo "1. Clear cache: php artisan cache:clear\n";
    echo "2. Clear view: php artisan view:clear\n";
    echo "3. Cek view: resources/views/admin/penjualan/kontrabon/print.blade.php\n";
    echo "4. Cek apakah view menggunakan variabel \$piutangBelumLunas\n";
}

echo "\n";
