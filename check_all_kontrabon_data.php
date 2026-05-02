<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "========================================\n";
    echo "CHECK ALL KONTRABON DATA\n";
    echo "========================================\n\n";
    
    // Get all kontrabon
    $kontraBons = DB::table('kontra_bon')
        ->orderBy('id_kontra_bon', 'desc')
        ->limit(5)
        ->get();
    
    echo "Found " . count($kontraBons) . " kontrabon records:\n\n";
    
    foreach ($kontraBons as $kontraBon) {
        echo "KontraBon ID: {$kontraBon->id_kontra_bon}\n";
        echo "Code: {$kontraBon->kode_kontra_bon}\n";
        echo "Member ID: {$kontraBon->id_member}\n";
        echo "Total Pembayaran: {$kontraBon->total_pembayaran}\n";
        echo "Status: {$kontraBon->status}\n";
        
        // Get details
        $details = DB::table('kontra_bon_detail')
            ->where('id_kontra_bon', $kontraBon->id_kontra_bon)
            ->get();
        
        echo "Details count: " . count($details) . "\n";
        
        if (count($details) > 0) {
            echo "Details:\n";
            foreach ($details as $detail) {
                echo "  - ID Penjualan: {$detail->id_penjualan}, Nominal: {$detail->nominal}\n";
                
                // Get piutang info
                $piutang = DB::table('piutang')
                    ->where('id_penjualan', $detail->id_penjualan)
                    ->first();
                
                if ($piutang) {
                    echo "    Piutang: {$piutang->jumlah_piutang}, Dibayar: {$piutang->jumlah_dibayar}, Sisa: {$piutang->sisa_piutang}, Status: {$piutang->status}\n";
                } else {
                    echo "    No piutang found for this penjualan\n";
                }
            }
        }
        
        echo "---\n\n";
    }
    
    // Check if there are any piutang records
    echo "Sample Piutang Records:\n";
    $piutangSample = DB::table('piutang')
        ->limit(5)
        ->get();
    
    foreach ($piutangSample as $piutang) {
        echo "- ID: {$piutang->id_piutang}, Penjualan: {$piutang->id_penjualan}, Member: {$piutang->id_member}\n";
        echo "  Jumlah: {$piutang->jumlah_piutang}, Dibayar: {$piutang->jumlah_dibayar}, Sisa: {$piutang->sisa_piutang}, Status: {$piutang->status}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}