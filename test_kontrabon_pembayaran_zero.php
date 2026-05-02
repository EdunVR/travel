<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "TEST KONTRA BON - PEMBAYARAN = 0\n";
echo "========================================\n\n";

// Simulate form data dengan pembayaran = 0
$formData = [
    'id_outlet' => 2,
    'id_member' => 49, // PT Champ
    'tanggal_jatuh_tempo' => '2026-03-11',
    'pembayaran' => 0, // PEMBAYARAN = 0
    'piutang_ids' => [842], // Hanya 1 piutang
    'start_date_filter' => null,
    'end_date_filter' => null
];

echo "Form Data (Pembayaran = 0):\n";
print_r($formData);
echo "\n";

// Check piutang exists
$piutang = \App\Models\Piutang::find(842);
if ($piutang) {
    echo "✅ Piutang 842 exists:\n";
    echo "   - ID Penjualan: {$piutang->id_penjualan}\n";
    echo "   - Sisa Piutang: Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
    echo "   - Status: {$piutang->status}\n\n";
} else {
    echo "❌ Piutang 842 not found!\n\n";
    exit;
}

// Simulate the store process
echo "Simulating store process dengan pembayaran = 0...\n";
echo "========================================\n\n";

try {
    DB::beginTransaction();
    
    // Generate nomor kontra bon
    $lastKontraBon = \App\Models\KontraBon::whereYear('created_at', date('Y'))
        ->whereMonth('created_at', date('m'))
        ->orderBy('id_kontra_bon', 'desc')
        ->first();

    $counter = $lastKontraBon ? (int)substr($lastKontraBon->no_kontra_bon, -4) + 1 : 1;
    $noKontraBon = 'KB' . date('Ym') . str_pad($counter, 4, '0', STR_PAD_LEFT);
    
    echo "Generated No Kontra Bon: $noKontraBon\n\n";

    // Create kontra bon
    $kontraBon = \App\Models\KontraBon::create([
        'kode_kontra_bon' => $noKontraBon,
        'no_kontra_bon' => $noKontraBon,
        'tanggal' => now(),
        'id_member' => $formData['id_member'],
        'id_outlet' => $formData['id_outlet'],
        'id_user' => 2, // Superadmin
        'total_pembayaran' => $formData['pembayaran'],
        'total' => $formData['pembayaran'],
        'tanggal_jatuh_tempo' => $formData['tanggal_jatuh_tempo'],
        'status' => 'pending',
        'keterangan' => '',
        'start_date_filter' => $formData['start_date_filter'],
        'end_date_filter' => $formData['end_date_filter']
    ]);
    
    echo "✅ Kontra Bon Created:\n";
    echo "   - ID: {$kontraBon->id_kontra_bon}\n";
    echo "   - No: {$kontraBon->no_kontra_bon}\n";
    echo "   - Pembayaran: Rp " . number_format($kontraBon->total_pembayaran, 0, ',', '.') . "\n\n";

    // Create kontra bon details
    $totalBayar = $formData['pembayaran'];
    
    echo "Processing piutang_ids dengan pembayaran = 0:\n";
    echo "Total Bayar: Rp " . number_format($totalBayar, 0, ',', '.') . "\n";
    echo "Piutang IDs: " . implode(', ', $formData['piutang_ids']) . "\n\n";

    // LOGIKA: Jika pembayaran = 0, tetap buat detail
    if ($totalBayar == 0) {
        echo "📋 Logika: Pembayaran = 0, buat detail tanpa update piutang\n\n";
        
        foreach ($formData['piutang_ids'] as $piutangId) {
            $piutang = \App\Models\Piutang::find($piutangId);
            if (!$piutang) {
                echo "❌ Piutang $piutangId not found, skipping...\n";
                continue;
            }

            echo "Processing Piutang $piutangId:\n";
            echo "   - Sisa Piutang: Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
            echo "   - Jumlah Bayar: Rp 0 (belum ada pembayaran)\n";

            // Create detail dengan nominal = sisa piutang
            $detail = \App\Models\KontraBonDetail::create([
                'id_kontra_bon' => $kontraBon->id_kontra_bon,
                'id_penjualan' => $piutang->id_penjualan,
                'nominal' => $piutang->sisa_piutang,
                'jumlah_bayar' => 0 // Belum ada pembayaran
            ]);
            
            echo "   ✅ Detail Created: ID {$detail->id_kontra_bon_detail}\n";
            echo "   ℹ️  Piutang TIDAK diupdate (status tetap belum_lunas)\n\n";
        }
    }

    DB::commit();
    
    echo "========================================\n";
    echo "✅ TRANSACTION COMMITTED\n";
    echo "========================================\n\n";
    
    // Verify the result
    $kontraBon = \App\Models\KontraBon::with('details.penjualan')->find($kontraBon->id_kontra_bon);
    echo "Verification:\n";
    echo "   - Kontra Bon ID: {$kontraBon->id_kontra_bon}\n";
    echo "   - No: {$kontraBon->no_kontra_bon}\n";
    echo "   - Pembayaran: Rp " . number_format($kontraBon->total_pembayaran, 0, ',', '.') . "\n";
    echo "   - Jumlah Detail: " . $kontraBon->details->count() . "\n";
    echo "   - Total Nominal Detail: Rp " . number_format($kontraBon->details->sum('nominal'), 0, ',', '.') . "\n\n";
    
    if ($kontraBon->details->count() > 0) {
        echo "✅ SUCCESS! Details tersimpan dengan benar\n";
        echo "\nDetail List:\n";
        foreach ($kontraBon->details as $detail) {
            echo "   - ID Penjualan: {$detail->id_penjualan}\n";
            echo "     Nominal: Rp " . number_format($detail->nominal, 0, ',', '.') . "\n";
            echo "     Jumlah Bayar: Rp " . number_format($detail->jumlah_bayar, 0, ',', '.') . "\n";
        }
        
        echo "\n📄 Print PDF akan menampilkan:\n";
        echo "   - Data Hutang yang Ditagihkan: " . $kontraBon->details->count() . " piutang\n";
        echo "   - Total: Rp " . number_format($kontraBon->details->sum('nominal'), 0, ',', '.') . "\n";
    } else {
        echo "❌ FAILED! Details tidak tersimpan\n";
    }
    
} catch (\Exception $e) {
    DB::rollback();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
