<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "========================================\n";
    echo "DEBUG KONTRABON TOTAL HUTANG\n";
    echo "========================================\n\n";
    
    // Get sample kontrabon data
    $kontraBon = DB::table('kontra_bon')
        ->orderBy('id_kontra_bon', 'desc')
        ->first();
    
    if (!$kontraBon) {
        echo "No kontrabon found in database\n";
        exit;
    }
    
    echo "Sample KontraBon ID: {$kontraBon->id_kontra_bon}\n";
    echo "KontraBon Code: {$kontraBon->kode_kontra_bon}\n";
    echo "Member ID: {$kontraBon->id_member}\n";
    echo "Total Pembayaran: {$kontraBon->total_pembayaran}\n\n";
    
    // Get kontrabon details
    $details = DB::table('kontra_bon_detail')
        ->where('id_kontra_bon', $kontraBon->id_kontra_bon)
        ->get();
    
    echo "KontraBon Details:\n";
    $totalNominal = 0;
    foreach ($details as $detail) {
        echo "- ID Penjualan: {$detail->id_penjualan}, Nominal: {$detail->nominal}\n";
        $totalNominal += $detail->nominal;
    }
    echo "Total from details->nominal: {$totalNominal}\n\n";
    
    // Get related piutang
    $piutangIds = [];
    foreach ($details as $detail) {
        $piutangIds[] = $detail->id_penjualan;
    }
    
    $totalPiutang = 0;
    $totalSisaPiutang = 0;
    
    if (!empty($piutangIds)) {
        $piutangData = DB::table('piutang')
            ->whereIn('id_penjualan', $piutangIds)
            ->get();
        
        echo "Related Piutang Data:\n";
        foreach ($piutangData as $piutang) {
            echo "- ID Penjualan: {$piutang->id_penjualan}\n";
            echo "  Jumlah Piutang: {$piutang->jumlah_piutang}\n";
            echo "  Jumlah Dibayar: {$piutang->jumlah_dibayar}\n";
            echo "  Sisa Piutang: {$piutang->sisa_piutang}\n";
            echo "  Status: {$piutang->status}\n\n";
            
            $totalPiutang += $piutang->jumlah_piutang;
            $totalSisaPiutang += $piutang->sisa_piutang;
        }
        
        echo "Total Jumlah Piutang: {$totalPiutang}\n";
        echo "Total Sisa Piutang: {$totalSisaPiutang}\n\n";
    } else {
        echo "No piutang data found for this kontrabon\n\n";
    }
    
    // Check what should be the correct total
    echo "ANALYSIS:\n";
    echo "- Total from kontra_bon_detail.nominal: {$totalNominal}\n";
    echo "- Total from piutang.jumlah_piutang: {$totalPiutang}\n";
    echo "- Total from piutang.sisa_piutang: {$totalSisaPiutang}\n\n";
    
    echo "RECOMMENDATION:\n";
    echo "Total hutang should be calculated from the piutang records that are involved in this kontrabon.\n";
    echo "This should be the sum of jumlah_piutang for all piutang records linked to this kontrabon.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}