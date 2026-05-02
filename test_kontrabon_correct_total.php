<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "========================================\n";
    echo "TEST KONTRABON CORRECT TOTAL CALCULATION\n";
    echo "========================================\n\n";
    
    // Test the corrected calculation
    $kontraBons = DB::table('kontra_bon')
        ->orderBy('id_kontra_bon', 'desc')
        ->limit(3)
        ->get();
    
    foreach ($kontraBons as $kontraBon) {
        echo "Testing KontraBon ID: {$kontraBon->id_kontra_bon}\n";
        echo "Code: {$kontraBon->kode_kontra_bon}\n";
        echo "Member ID: {$kontraBon->id_member}\n";
        echo "Date Filter: {$kontraBon->start_date_filter} to {$kontraBon->end_date_filter}\n";
        
        // Get "Data Hutang yang Ditagihkan" (piutang belum lunas for this member)
        $piutangBelumLunasQuery = DB::table('piutang')
            ->where('id_member', $kontraBon->id_member)
            ->where('status', 'belum_lunas');
        
        // Apply date filter if exists
        if ($kontraBon->start_date_filter && $kontraBon->end_date_filter) {
            $piutangBelumLunasQuery = $piutangBelumLunasQuery->whereBetween('created_at', [
                $kontraBon->start_date_filter . ' 00:00:00',
                $kontraBon->end_date_filter . ' 23:59:59'
            ]);
            echo "Applied date filter\n";
        }
        
        $piutangBelumLunas = $piutangBelumLunasQuery->get();
        $totalHutang = $piutangBelumLunas->sum('sisa_piutang');
        
        echo "Data Hutang yang Ditagihkan:\n";
        foreach ($piutangBelumLunas as $piutang) {
            echo "  - ID Penjualan: {$piutang->id_penjualan}, Sisa Piutang: " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
        }
        
        echo "TOTAL HUTANG (sum of sisa_piutang): " . number_format($totalHutang, 0, ',', '.') . "\n";
        
        // Get "Data Hutang yang Sudah Dilunasi" (kontrabon details)
        $details = DB::table('kontra_bon_detail')
            ->where('id_kontra_bon', $kontraBon->id_kontra_bon)
            ->get();
        
        echo "Data Hutang yang Sudah Dilunasi:\n";
        $totalDilunasi = 0;
        foreach ($details as $detail) {
            echo "  - ID Penjualan: {$detail->id_penjualan}, Nominal: " . number_format($detail->nominal, 0, ',', '.') . "\n";
            $totalDilunasi += $detail->nominal;
        }
        echo "Total Dilunasi: " . number_format($totalDilunasi, 0, ',', '.') . "\n";
        
        echo "---\n\n";
    }
    
    echo "EXPLANATION:\n";
    echo "✓ Total Hutang = SUM of 'sisa_piutang' from 'Data Hutang yang Ditagihkan' table\n";
    echo "✓ This matches the pattern in print.blade.php where:\n";
    echo "  - 'Data Hutang yang Ditagihkan' shows piutang belum lunas\n";
    echo "  - 'Data Hutang yang Sudah Dilunasi' shows kontrabon details\n";
    echo "  - Total Hutang should be sum of the first table (piutang belum lunas)\n\n";
    
    echo "TESTING STEPS:\n";
    echo "1. Go to admin/penjualan/kontrabon\n";
    echo "2. Check 'List Kontra Bon' tab - total should match calculation above\n";
    echo "3. Print any kontrabon - total hutang should match sum of 'Data Hutang yang Ditagihkan'\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}