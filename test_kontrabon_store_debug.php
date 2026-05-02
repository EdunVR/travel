<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "TEST KONTRA BON STORE DEBUG\n";
echo "========================================\n\n";

// Simulate form data
$formData = [
    'id_outlet' => 2,
    'id_member' => 49, // PT Champ
    'tanggal_jatuh_tempo' => '2026-03-11',
    'pembayaran' => 17100000, // Untuk 1 piutang pertama
    'piutang_ids' => [850], // Hanya 1 piutang
    'start_date_filter' => null,
    'end_date_filter' => null
];

echo "Form Data yang akan dikirim:\n";
print_r($formData);
echo "\n";

// Check piutang exists
$piutang = \App\Models\Piutang::find(850);
if ($piutang) {
    echo "✅ Piutang 850 exists:\n";
    echo "   - ID Penjualan: {$piutang->id_penjualan}\n";
    echo "   - Sisa Piutang: Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
    echo "   - Status: {$piutang->status}\n\n";
} else {
    echo "❌ Piutang 850 not found!\n\n";
    exit;
}

// Simulate the store process
echo "Simulating store process...\n";
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
    echo "   - No: {$kontraBon->no_kontra_bon}\n\n";

    // Create kontra bon details
    $totalBayar = $formData['pembayaran'];
    $sisaBayar = $totalBayar;
    
    echo "Processing piutang_ids:\n";
    echo "Total Bayar: Rp " . number_format($totalBayar, 0, ',', '.') . "\n";
    echo "Piutang IDs: " . implode(', ', $formData['piutang_ids']) . "\n\n";

    foreach ($formData['piutang_ids'] as $piutangId) {
        if ($sisaBayar <= 0) break;

        $piutang = \App\Models\Piutang::find($piutangId);
        if (!$piutang) {
            echo "❌ Piutang $piutangId not found, skipping...\n";
            continue;
        }

        $sisaPiutang = $piutang->sisa_piutang;
        $bayarPiutang = min($sisaBayar, $sisaPiutang);

        echo "Processing Piutang $piutangId:\n";
        echo "   - Sisa Piutang: Rp " . number_format($sisaPiutang, 0, ',', '.') . "\n";
        echo "   - Bayar: Rp " . number_format($bayarPiutang, 0, ',', '.') . "\n";

        // Create detail
        $detail = \App\Models\KontraBonDetail::create([
            'id_kontra_bon' => $kontraBon->id_kontra_bon,
            'id_penjualan' => $piutang->id_penjualan,
            'nominal' => $bayarPiutang,
            'jumlah_bayar' => $bayarPiutang
        ]);
        
        echo "   ✅ Detail Created: ID {$detail->id_kontra_bon_detail}\n";

        // Update piutang
        $piutang->update([
            'jumlah_dibayar' => $piutang->jumlah_dibayar + $bayarPiutang,
            'sisa_piutang' => $piutang->sisa_piutang - $bayarPiutang,
            'status' => ($piutang->sisa_piutang - $bayarPiutang <= 0) ? 'lunas' : 'belum_lunas'
        ]);
        
        echo "   ✅ Piutang Updated\n\n";

        $sisaBayar -= $bayarPiutang;
    }

    DB::commit();
    
    echo "========================================\n";
    echo "✅ TRANSACTION COMMITTED\n";
    echo "========================================\n\n";
    
    // Verify the result
    $kontraBon = \App\Models\KontraBon::with('details')->find($kontraBon->id_kontra_bon);
    echo "Verification:\n";
    echo "   - Kontra Bon ID: {$kontraBon->id_kontra_bon}\n";
    echo "   - No: {$kontraBon->no_kontra_bon}\n";
    echo "   - Jumlah Detail: " . $kontraBon->details->count() . "\n";
    echo "   - Total Detail: Rp " . number_format($kontraBon->details->sum('nominal'), 0, ',', '.') . "\n\n";
    
    if ($kontraBon->details->count() > 0) {
        echo "✅ SUCCESS! Details tersimpan dengan benar\n";
        echo "\nDetail List:\n";
        foreach ($kontraBon->details as $detail) {
            echo "   - ID Penjualan: {$detail->id_penjualan}, Nominal: Rp " . number_format($detail->nominal, 0, ',', '.') . "\n";
        }
    } else {
        echo "❌ FAILED! Details tidak tersimpan\n";
    }
    
} catch (\Exception $e) {
    DB::rollback();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
