<?php

/**
 * Check PT Champ Kontra Bon
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;
use App\Models\KontraBon;
use App\Models\KontraBonDetail;
use App\Models\Piutang;

echo "========================================\n";
echo "CHECK PT CHAMP KONTRA BON\n";
echo "========================================\n\n";

// Cari customer PT Champ
$member = Member::where('nama', 'LIKE', '%champ%')->first();

if (!$member) {
    echo "❌ Customer PT Champ tidak ditemukan\n";
    echo "\nCoba cari dengan nama lain:\n";
    $members = Member::where('nama', 'LIKE', '%pt%')->limit(10)->get();
    foreach ($members as $m) {
        echo "  - {$m->nama} (ID: {$m->id_member})\n";
    }
    exit;
}

echo "✅ Customer Found:\n";
echo "  - Nama: {$member->nama}\n";
echo "  - ID: {$member->id_member}\n";
echo "\n";

// Cek piutang PT Champ
echo "Piutang PT Champ:\n";
echo "-----------------\n";
$piutangs = Piutang::where('id_member', $member->id_member)
    ->with(['penjualan'])
    ->orderBy('created_at', 'desc')
    ->get();

if ($piutangs->count() == 0) {
    echo "❌ Tidak ada piutang untuk PT Champ\n";
    exit;
}

echo "Total Piutang: {$piutangs->count()}\n\n";

foreach ($piutangs as $index => $piutang) {
    echo ($index + 1) . ". ID Piutang: {$piutang->id_piutang}\n";
    echo "   ID Penjualan: {$piutang->id_penjualan}\n";
    echo "   Kode: {$piutang->penjualan->kode_penjualan}\n";
    echo "   Tanggal: {$piutang->created_at->format('d/m/Y')}\n";
    echo "   Jumlah: Rp " . number_format($piutang->jumlah_piutang, 0, ',', '.') . "\n";
    echo "   Dibayar: Rp " . number_format($piutang->jumlah_dibayar, 0, ',', '.') . "\n";
    echo "   Sisa: Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
    echo "   Status: {$piutang->status}\n";
    echo "\n";
}

// Cek kontra bon PT Champ
echo "Kontra Bon PT Champ:\n";
echo "--------------------\n";
$kontrabons = KontraBon::where('id_member', $member->id_member)
    ->with(['details'])
    ->orderBy('created_at', 'desc')
    ->get();

if ($kontrabons->count() == 0) {
    echo "❌ Belum ada kontra bon untuk PT Champ\n";
    echo "\nSilakan buat kontra bon baru dengan:\n";
    echo "1. Pilih customer: {$member->nama}\n";
    echo "2. Centang 1 piutang dari daftar di atas\n";
    echo "3. Buat kontra bon\n";
    echo "4. Jalankan script ini lagi\n";
    exit;
}

echo "Total Kontra Bon: {$kontrabons->count()}\n\n";

foreach ($kontrabons as $index => $kb) {
    echo ($index + 1) . ". Kontra Bon:\n";
    echo "   ID: {$kb->id_kontra_bon}\n";
    echo "   No: {$kb->no_kontra_bon}\n";
    echo "   Tanggal: {$kb->created_at->format('d/m/Y H:i:s')}\n";
    echo "   Status: {$kb->status}\n";
    echo "   Total Pembayaran: Rp " . number_format($kb->total_pembayaran, 0, ',', '.') . "\n";
    echo "\n";
    
    // Cek detail
    echo "   Detail Kontra Bon:\n";
    if ($kb->details->count() == 0) {
        echo "   ❌ TIDAK ADA DETAIL!\n";
        echo "   Masalah: Detail tidak tersimpan saat create\n";
    } else {
        echo "   ✅ Jumlah Detail: {$kb->details->count()}\n";
        foreach ($kb->details as $detailIndex => $detail) {
            echo "   " . ($detailIndex + 1) . ") ID Penjualan: {$detail->id_penjualan}\n";
            echo "      Nominal: Rp " . number_format($detail->nominal, 0, ',', '.') . "\n";
            
            // Cek piutang terkait
            $piutang = Piutang::where('id_penjualan', $detail->id_penjualan)->first();
            if ($piutang) {
                echo "      Piutang: ✅ Found\n";
                echo "      Kode: {$piutang->penjualan->kode_penjualan}\n";
            } else {
                echo "      Piutang: ❌ Not Found\n";
            }
        }
    }
    echo "\n";
}

// Test method print untuk kontra bon terakhir
echo "========================================\n";
echo "TEST METHOD PRINT (Kontra Bon Terakhir)\n";
echo "========================================\n\n";

$lastKb = $kontrabons->first();

if (!$lastKb) {
    echo "❌ Tidak ada kontra bon\n";
    exit;
}

echo "Testing Kontra Bon: {$lastKb->no_kontra_bon}\n";
echo "Jumlah Detail: {$lastKb->details->count()}\n\n";

if ($lastKb->details->count() == 0) {
    echo "❌ DETAIL KOSONG - Tidak bisa print\n";
    echo "\nMasalah: Detail kontra bon tidak tersimpan\n";
    echo "Solusi:\n";
    echo "1. Cek console browser (F12) saat submit form\n";
    echo "2. Pastikan piutang_ids[] terkirim\n";
    echo "3. Cek storage/logs/laravel.log\n";
    exit;
}

// Simulasi method print
$piutangBelumLunas = collect();
$totalHutang = 0;

foreach ($lastKb->details as $detail) {
    $piutang = Piutang::where('id_penjualan', $detail->id_penjualan)
        ->with(['penjualan'])
        ->first();
    
    if ($piutang) {
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
    }
}

echo "Hasil Simulasi:\n";
echo "  - Jumlah Detail: {$lastKb->details->count()}\n";
echo "  - Jumlah di Collection: {$piutangBelumLunas->count()}\n";
echo "  - Total Hutang: Rp " . number_format($totalHutang, 0, ',', '.') . "\n";
echo "\n";

if ($piutangBelumLunas->count() == 0) {
    echo "❌ COLLECTION KOSONG!\n";
    echo "\nMasalah: Method print tidak mengambil data\n";
    echo "Kemungkinan:\n";
    echo "1. Relasi details tidak di-load\n";
    echo "2. Piutang tidak ditemukan\n";
    echo "3. Loop tidak berjalan\n";
} else {
    echo "✅ COLLECTION BERISI DATA\n";
    echo "\nData yang akan muncul di PDF:\n";
    foreach ($piutangBelumLunas as $index => $item) {
        echo ($index + 1) . ". {$item->penjualan->kode_penjualan} - Rp " . number_format($item->sisa_piutang, 0, ',', '.') . "\n";
    }
    echo "\n";
    echo "✅ Method print seharusnya berfungsi\n";
    echo "\nJika PDF masih kosong:\n";
    echo "1. Clear cache: php artisan cache:clear\n";
    echo "2. Clear view: php artisan view:clear\n";
    echo "3. Hard refresh browser: Ctrl + Shift + R\n";
    echo "4. Cek view print.blade.php\n";
}

echo "\n";
