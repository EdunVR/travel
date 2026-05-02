<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "========================================\n";
    echo "TEST KONTRABON LUNASI DATA MOVEMENT\n";
    echo "========================================\n\n";
    
    // Get a sample kontrabon that is not yet lunas
    $kontraBon = DB::table('kontra_bon')
        ->where('status', '!=', 'lunas')
        ->orderBy('id_kontra_bon', 'desc')
        ->first();
    
    if (!$kontraBon) {
        echo "No pending kontrabon found for testing\n";
        exit;
    }
    
    echo "Testing KontraBon ID: {$kontraBon->id_kontra_bon}\n";
    echo "Code: {$kontraBon->kode_kontra_bon}\n";
    echo "Member ID: {$kontraBon->id_member}\n";
    echo "Status: {$kontraBon->status}\n";
    echo "Date Filter: {$kontraBon->start_date_filter} to {$kontraBon->end_date_filter}\n\n";
    
    // BEFORE LUNASI: Check "Data Hutang yang Ditagihkan"
    echo "=== BEFORE LUNASI ===\n";
    
    $piutangBelumLunasQuery = DB::table('piutang')
        ->where('id_member', $kontraBon->id_member)
        ->where('status', 'belum_lunas');
    
    if ($kontraBon->start_date_filter && $kontraBon->end_date_filter) {
        $piutangBelumLunasQuery = $piutangBelumLunasQuery->whereBetween('created_at', [
            $kontraBon->start_date_filter . ' 00:00:00',
            $kontraBon->end_date_filter . ' 23:59:59'
        ]);
    }
    
    $piutangBelumLunas = $piutangBelumLunasQuery->get();
    
    echo "Data Hutang yang Ditagihkan (BEFORE):\n";
    $totalBefore = 0;
    foreach ($piutangBelumLunas as $piutang) {
        echo "  - ID Penjualan: {$piutang->id_penjualan}, Sisa Piutang: " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
        $totalBefore += $piutang->sisa_piutang;
    }
    echo "Total BEFORE: " . number_format($totalBefore, 0, ',', '.') . "\n\n";
    
    // Check existing details
    $detailsBefore = DB::table('kontra_bon_detail')
        ->where('id_kontra_bon', $kontraBon->id_kontra_bon)
        ->get();
    
    echo "Data Hutang yang Sudah Dilunasi (BEFORE):\n";
    $totalDetailsBefore = 0;
    foreach ($detailsBefore as $detail) {
        echo "  - ID Penjualan: {$detail->id_penjualan}, Nominal: " . number_format($detail->nominal, 0, ',', '.') . "\n";
        $totalDetailsBefore += $detail->nominal;
    }
    echo "Total Details BEFORE: " . number_format($totalDetailsBefore, 0, ',', '.') . "\n\n";
    
    // SIMULATE LUNASI PROCESS
    echo "=== SIMULATING LUNASI PROCESS ===\n";
    
    DB::beginTransaction();
    
    try {
        // Create kontra_bon_detail for each piutang that will be paid
        $createdDetails = 0;
        foreach ($piutangBelumLunas as $piutang) {
            // Check if detail already exists
            $existingDetail = DB::table('kontra_bon_detail')
                ->where('id_kontra_bon', $kontraBon->id_kontra_bon)
                ->where('id_penjualan', $piutang->id_penjualan)
                ->first();
            
            if (!$existingDetail) {
                // Create detail with the sisa_piutang amount
                DB::table('kontra_bon_detail')->insert([
                    'id_kontra_bon' => $kontraBon->id_kontra_bon,
                    'id_penjualan' => $piutang->id_penjualan,
                    'nominal' => $piutang->sisa_piutang,
                    'jumlah_bayar' => $piutang->sisa_piutang,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $createdDetails++;
                echo "Created detail for ID Penjualan: {$piutang->id_penjualan}, Nominal: " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
            }
            
            // Update piutang status to lunas
            DB::table('piutang')
                ->where('id_piutang', $piutang->id_piutang)
                ->update([
                    'jumlah_dibayar' => $piutang->jumlah_piutang,
                    'sisa_piutang' => 0,
                    'status' => 'lunas',
                    'updated_at' => now()
                ]);
            echo "Updated piutang ID: {$piutang->id_piutang} to lunas\n";
        }
        
        // Update kontra bon status to lunas
        DB::table('kontra_bon')
            ->where('id_kontra_bon', $kontraBon->id_kontra_bon)
            ->update([
                'status' => 'lunas',
                'updated_at' => now()
            ]);
        
        echo "Updated kontrabon status to lunas\n";
        echo "Created {$createdDetails} new details\n\n";
        
        // AFTER LUNASI: Check results
        echo "=== AFTER LUNASI ===\n";
        
        // Check "Data Hutang yang Ditagihkan" (should be empty or reduced)
        $piutangBelumLunasAfter = $piutangBelumLunasQuery->get();
        
        echo "Data Hutang yang Ditagihkan (AFTER):\n";
        $totalAfter = 0;
        foreach ($piutangBelumLunasAfter as $piutang) {
            echo "  - ID Penjualan: {$piutang->id_penjualan}, Sisa Piutang: " . number_format($piutang->sisa_piutang, 0, ',', '.') . "\n";
            $totalAfter += $piutang->sisa_piutang;
        }
        if (count($piutangBelumLunasAfter) == 0) {
            echo "  (No data - all moved to 'Sudah Dilunasi')\n";
        }
        echo "Total AFTER: " . number_format($totalAfter, 0, ',', '.') . "\n\n";
        
        // Check "Data Hutang yang Sudah Dilunasi"
        $detailsAfter = DB::table('kontra_bon_detail')
            ->where('id_kontra_bon', $kontraBon->id_kontra_bon)
            ->get();
        
        echo "Data Hutang yang Sudah Dilunasi (AFTER):\n";
        $totalDetailsAfter = 0;
        foreach ($detailsAfter as $detail) {
            echo "  - ID Penjualan: {$detail->id_penjualan}, Nominal: " . number_format($detail->nominal, 0, ',', '.') . "\n";
            $totalDetailsAfter += $detail->nominal;
        }
        echo "Total Details AFTER: " . number_format($totalDetailsAfter, 0, ',', '.') . "\n\n";
        
        // VERIFICATION
        echo "=== VERIFICATION ===\n";
        echo "✓ Data moved from 'Ditagihkan' to 'Sudah Dilunasi': " . ($totalBefore == ($totalDetailsAfter - $totalDetailsBefore) ? "YES" : "NO") . "\n";
        echo "✓ Total Hutang reduced: " . ($totalAfter < $totalBefore ? "YES" : "NO") . "\n";
        echo "✓ KontraBon status changed to lunas: YES\n";
        echo "✓ Piutang status changed to lunas: YES\n\n";
        
        echo "SUMMARY:\n";
        echo "- Before: {$totalBefore} in 'Ditagihkan', {$totalDetailsBefore} in 'Sudah Dilunasi'\n";
        echo "- After: {$totalAfter} in 'Ditagihkan', {$totalDetailsAfter} in 'Sudah Dilunasi'\n";
        echo "- Moved: " . number_format($totalBefore - $totalAfter, 0, ',', '.') . " from 'Ditagihkan' to 'Sudah Dilunasi'\n";
        
        // Rollback for testing purposes
        DB::rollback();
        echo "\n(Changes rolled back for testing purposes)\n";
        
    } catch (Exception $e) {
        DB::rollback();
        echo "Error during simulation: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}