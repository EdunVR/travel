<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

try {
    echo "Testing InterOutletSale DataTable query...\n\n";
    
    // Test basic model query
    $count = \App\Models\InterOutletSale::count();
    echo "✅ Total InterOutletSale records: {$count}\n";
    
    // Test query with relationships
    $query = \App\Models\InterOutletSale::query()
        ->with([
            'outletAsal:id_outlet,nama_outlet',
            'outletTujuan:id_outlet,nama_outlet',
            'user:id,name',
            'items:id,inter_outlet_sale_id'
        ])
        ->orderBy('tanggal', 'desc')
        ->limit(1);
    
    $result = $query->first();
    
    if ($result) {
        echo "✅ Query with relationships successful\n";
        echo "   ID: {$result->id}\n";
        echo "   No Transaksi: {$result->no_transaksi}\n";
        echo "   Status: {$result->status}\n";
        echo "   Outlet Asal: " . ($result->outletAsal ? $result->outletAsal->nama_outlet : 'N/A') . "\n";
        echo "   Outlet Tujuan: " . ($result->outletTujuan ? $result->outletTujuan->nama_outlet : 'N/A') . "\n";
        echo "   Items Count: " . $result->items->count() . "\n";
        
        // Test route generation
        try {
            $printUrl = route('admin.penjualan.inter-outlet.print', ['id' => $result->id]);
            echo "✅ Print route generated: {$printUrl}\n";
        } catch (Exception $e) {
            echo "❌ Print route error: {$e->getMessage()}\n";
        }
        
    } else {
        echo "⚠️  No records found to test\n";
    }
    
    echo "\n🎉 DataTable query test completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}