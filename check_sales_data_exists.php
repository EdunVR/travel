<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Penjualan;
use App\Models\PosSale;
use App\Models\InterOutletSale;

echo "🔍 Checking Sales Data Existence\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Check Penjualan (Invoice) data
echo "📄 Invoice Data (Penjualan):\n";
$penjualanCount = Penjualan::count();
echo "   Total records: $penjualanCount\n";

if ($penjualanCount > 0) {
    $recentPenjualan = Penjualan::with(['outlet:id_outlet,nama_outlet'])
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    echo "   Recent records:\n";
    foreach ($recentPenjualan as $p) {
        $outletName = $p->outlet ? $p->outlet->nama_outlet : 'Unknown';
        echo "      - ID: {$p->id_penjualan}, Outlet: $outletName (ID: {$p->id_outlet}), Total: {$p->total_harga}, Date: {$p->created_at}\n";
    }
}

echo "\n🛒 POS Data:\n";
$posCount = PosSale::count();
echo "   Total records: $posCount\n";

if ($posCount > 0) {
    $recentPos = PosSale::with(['outlet:id_outlet,nama_outlet'])
        ->orderBy('tanggal', 'desc')
        ->take(5)
        ->get();
    
    echo "   Recent records:\n";
    foreach ($recentPos as $p) {
        $outletName = $p->outlet ? $p->outlet->nama_outlet : 'Unknown';
        echo "      - ID: {$p->id}, Outlet: $outletName (ID: {$p->id_outlet}), Total: {$p->total}, Date: {$p->tanggal}\n";
    }
}

echo "\n🔄 Inter Outlet Data:\n";
$interOutletCount = 0;
if (class_exists('App\Models\InterOutletSale')) {
    $interOutletCount = \App\Models\InterOutletSale::count();
    echo "   Total records: $interOutletCount\n";
    
    if ($interOutletCount > 0) {
        $recentInterOutlet = \App\Models\InterOutletSale::with(['outletAsal:id_outlet,nama_outlet'])
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();
        
        echo "   Recent records:\n";
        foreach ($recentInterOutlet as $p) {
            $outletName = $p->outletAsal ? $p->outletAsal->nama_outlet : 'Unknown';
            echo "      - ID: {$p->id}, Outlet: $outletName (ID: {$p->outlet_asal}), Total: {$p->total}, Date: {$p->tanggal}\n";
        }
    }
} else {
    echo "   InterOutletSale model not found\n";
}

echo "\n📊 Summary:\n";
echo "   Total Invoice: $penjualanCount\n";
echo "   Total POS: $posCount\n";
echo "   Total Inter Outlet: $interOutletCount\n";
echo "   Grand Total: " . ($penjualanCount + $posCount + $interOutletCount) . "\n";

echo "\n✅ Check Complete!\n";