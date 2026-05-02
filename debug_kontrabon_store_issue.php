<?php

/**
 * DEBUG KONTRABON STORE ISSUE
 * 
 * Script untuk debug masalah kontrabon store
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\KontraBon;
use App\Models\KontraBonDetail;
use App\Models\Piutang;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

echo "=== DEBUG KONTRABON STORE ISSUE ===\n\n";

// Test 1: Cek kontrabon terbaru
echo "TEST 1: Cek Kontra Bon Terbaru\n";
echo str_repeat("-", 50) . "\n";

$latestKontraBon = KontraBon::with(['details.penjualan.posSale', 'member'])
    ->orderBy('created_at', 'desc')
    ->first();

if ($latestKontraBon) {
    echo "✅ Kontra Bon Terbaru:\n";
    echo "   ID: {$latestKontraBon->id_kontra_bon}\n";
    echo "   No: {$latestKontraBon->no_kontra_bon}\n";
    echo "   Customer: {$latestKontraBon->member->nama}\n";
    echo "   Tanggal: {$latestKontraBon->created_at->format('d/m/Y H:i')}\n";
    echo "   Total Pembayaran: Rp " . number_format($latestKontraBon->total_pembayaran, 0, ',', '.') . "\n";
    echo "   Start Date Filter: " . ($latestKontraBon->start_date_filter ?? 'NULL') . "\n";
    echo "   End Date Filter: " . ($latestKontraBon->end_date_filter ?? 'NULL') . "\n\n";
    
    // Cek details
    echo "   Jumlah Detail: " . $latestKontraBon->details->count() . "\n\n";
    
    if ($latestKontraBon->details->count() > 0) {
        echo "   Detail Kontra Bon:\n";
        foreach ($latestKontraBon->details as $index => $detail) {
            $noTransaksi = $detail->penjualan && $detail->penjualan->posSale 
                ? $detail->penjualan->posSale->no_transaksi 
                : 'TRX00' . $detail->id_penjualan;
            
            echo "   " . ($index + 1) . ". ID Penjualan: {$detail->id_penjualan}\n";
            echo "      No Transaksi: {$noTransaksi}\n";
            echo "      Nominal: Rp " . number_format($detail->nominal, 0, ',', '.') . "\n";
            echo "      Jumlah Bayar: Rp " . number_format($detail->jumlah_bayar, 0, ',', '.') . "\n";
            echo "\n";
        }
    } else {
        echo "   ❌ TIDAK ADA DETAIL! Ini masalahnya!\n\n";
    }
    
    // Cek piutang member
    echo "\n   Piutang Member (Belum Lunas):\n";
    $piutangMember = Piutang::where('id_member', $latestKontraBon->id_member)
        ->where('status', 'belum_lunas')
        ->with('penjualan.posSale')
        ->orderBy('created_at', 'asc')
        ->get();
    
    echo "   Jumlah: " . $piutangMember->count() . "\n\n";
    
    if ($piutangMember->count() > 0) {
        foreach ($piutangMember->take(5) as $index => $piutang) {
            $noTransaksi = $piutang->penjualan && $piutang->penjualan->posSale 
                ? $piutang->penjualan->posSale->no_transaksi 
                : 'TRX00' . $piutang->id_penjualan;
            
            echo "   " . ($index + 1) . ". ID Piutang: {$piutang->id_piutang}\n";
            echo "      ID Penjualan: {$piutang->id_penjualan}\n";
            echo "      No Transaksi: {$noTransaksi}\n";
            echo "      Tanggal: " . $piutang->created_at->format('d/m/Y') . "\n";
            echo "      Sisa Piutang: Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
            echo "\n";
        }
    }
    
    // Cek apakah ada filter tanggal
    if ($latestKontraBon->start_date_filter && $latestKontraBon->end_date_filter) {
        echo "\n   Filter Tanggal Aktif:\n";
        echo "   Dari: {$latestKontraBon->start_date_filter}\n";
        echo "   Sampai: {$latestKontraBon->end_date_filter}\n\n";
        
        // Cek piutang dalam range
        $piutangInRange = Piutang::where('id_member', $latestKontraBon->id_member)
            ->where('status', 'belum_lunas')
            ->whereBetween('created_at', [
                $latestKontraBon->start_date_filter . ' 00:00:00',
                $latestKontraBon->end_date_filter . ' 23:59:59'
            ])
            ->get();
        
        echo "   Piutang dalam range: " . $piutangInRange->count() . "\n";
        echo "   Detail yang tersimpan: " . $latestKontraBon->details->count() . "\n\n";
        
        if ($piutangInRange->count() != $latestKontraBon->details->count()) {
            echo "   ❌ TIDAK SESUAI! Ini masalahnya!\n";
            echo "   Seharusnya: {$piutangInRange->count()} detail\n";
            echo "   Tersimpan: {$latestKontraBon->details->count()} detail\n\n";
        } else {
            echo "   ✅ SESUAI!\n\n";
        }
    }
    
} else {
    echo "❌ Tidak ada kontra bon\n";
}

// Test 2: Simulasi request yang benar
echo "\n\nTEST 2: Simulasi Request yang Benar\n";
echo str_repeat("-", 50) . "\n";

if ($latestKontraBon) {
    $member = $latestKontraBon->member;
    
    echo "✅ Simulasi untuk member: {$member->nama}\n\n";
    
    // Ambil piutang
    $piutang = Piutang::where('id_member', $member->id_member)
        ->where('status', 'belum_lunas')
        ->orderBy('created_at', 'asc')
        ->take(3)
        ->get();
    
    if ($piutang->count() > 0) {
        echo "   Piutang yang akan dipilih (3 pertama):\n";
        $piutangIds = [];
        foreach ($piutang as $index => $p) {
            echo "   " . ($index + 1) . ". ID Piutang: {$p->id_piutang}\n";
            echo "      ID Penjualan: {$p->id_penjualan}\n";
            echo "      Sisa: Rp " . number_format($p->sisa_piutang, 0, ',', '.') . "\n";
            $piutangIds[] = $p->id_piutang;
        }
        
        echo "\n   Request yang BENAR:\n";
        echo "   piutang_ids[] = " . json_encode($piutangIds) . "\n\n";
        
        echo "   Controller akan query:\n";
        echo "   Piutang::whereIn('id_piutang', [" . implode(', ', $piutangIds) . "])\n\n";
        
        // Test query
        $foundPiutang = Piutang::whereIn('id_piutang', $piutangIds)->get();
        echo "   Hasil query: {$foundPiutang->count()} piutang ditemukan\n";
        
        if ($foundPiutang->count() == count($piutangIds)) {
            echo "   ✅ BENAR - Semua piutang ditemukan\n";
        } else {
            echo "   ❌ ERROR - Tidak semua piutang ditemukan\n";
        }
    }
}

// Test 3: Cek log Laravel
echo "\n\nTEST 3: Cek Log Laravel (10 baris terakhir)\n";
echo str_repeat("-", 50) . "\n";

$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -20);
    
    echo "Log terakhir:\n";
    foreach ($lastLines as $line) {
        if (stripos($line, 'kontra') !== false || stripos($line, 'piutang') !== false) {
            echo $line;
        }
    }
} else {
    echo "❌ File log tidak ditemukan\n";
}

echo "\n\n" . str_repeat("=", 50) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 50) . "\n\n";

echo "KEMUNGKINAN MASALAH:\n\n";

echo "1. ❌ Frontend mengirim data yang salah\n";
echo "   - Cek: Browser console (F12)\n";
echo "   - Cek: Network tab → POST request → Form Data\n";
echo "   - Harus: piutang_ids[] = [123, 124, 125]\n\n";

echo "2. ❌ Controller tidak menerima piutang_ids[]\n";
echo "   - Cek: Log Laravel untuk 'Kontra Bon Store Request'\n";
echo "   - Cek: Validation error\n\n";

echo "3. ❌ Detail tidak tersimpan ke database\n";
echo "   - Cek: Tabel kontra_bon_detail\n";
echo "   - Cek: Apakah ada error saat insert\n\n";

echo "4. ❌ Filter tanggal tidak diterapkan\n";
echo "   - Cek: start_date_filter dan end_date_filter di tabel kontra_bon\n";
echo "   - Cek: Apakah detail sesuai dengan filter\n\n";

echo "NEXT STEPS:\n";
echo "1. Buat kontra bon baru dengan filter tanggal\n";
echo "2. Centang beberapa piutang (jangan select all dulu)\n";
echo "3. Submit form\n";
echo "4. Jalankan script ini lagi untuk cek hasilnya\n";
echo "5. Cek browser console dan network tab\n";
echo "6. Cek log Laravel: tail -f storage/logs/laravel.log\n\n";
