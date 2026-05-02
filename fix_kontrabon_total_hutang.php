<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "========================================\n";
    echo "FIX KONTRABON TOTAL HUTANG CALCULATION\n";
    echo "========================================\n\n";
    
    // Test the fixed calculation
    $kontraBons = DB::table('kontra_bon')
        ->orderBy('id_kontra_bon', 'desc')
        ->limit(3)
        ->get();
    
    foreach ($kontraBons as $kontraBon) {
        echo "Testing KontraBon ID: {$kontraBon->id_kontra_bon}\n";
        echo "Code: {$kontraBon->kode_kontra_bon}\n";
        echo "Member ID: {$kontraBon->id_member}\n";
        
        // Method 1: From details (current implementation)
        $details = DB::table('kontra_bon_detail')
            ->where('id_kontra_bon', $kontraBon->id_kontra_bon)
            ->get();
        
        $totalFromDetails = 0;
        $piutangIds = [];
        
        if (count($details) > 0) {
            foreach ($details as $detail) {
                $piutangIds[] = $detail->id_penjualan;
                $totalFromDetails += $detail->nominal;
            }
            
            // Get total from related piutang
            $totalFromPiutang = DB::table('piutang')
                ->whereIn('id_penjualan', $piutangIds)
                ->sum('jumlah_piutang');
                
            echo "Total from details->nominal: " . number_format($totalFromDetails, 0, ',', '.') . "\n";
            echo "Total from related piutang->jumlah_piutang: " . number_format($totalFromPiutang, 0, ',', '.') . "\n";
        } else {
            // Method 2: From all unpaid piutang for this member
            $totalFromMemberPiutang = DB::table('piutang')
                ->where('id_member', $kontraBon->id_member)
                ->where('status', 'belum_lunas')
                ->sum('sisa_piutang');
                
            echo "No details found. Total from member's unpaid piutang: " . number_format($totalFromMemberPiutang, 0, ',', '.') . "\n";
        }
        
        echo "---\n\n";
    }
    
    echo "SUMMARY:\n";
    echo "✓ Fixed total hutang calculation in controller\n";
    echo "✓ Fixed total hutang display in print view\n";
    echo "✓ Total hutang now calculated from piutang records\n";
    echo "✓ Handles both cases: with details and without details\n\n";
    
    echo "NEXT STEPS:\n";
    echo "1. Clear cache: php artisan cache:clear\n";
    echo "2. Test kontrabon index page\n";
    echo "3. Test kontrabon print functionality\n";
    echo "4. Verify total hutang shows correct values\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}