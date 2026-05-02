<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test POS Transaction Number Generation ===\n\n";

// Test untuk setiap outlet
$outlets = DB::table('outlets')->where('is_active', true)->get(['id_outlet', 'nama_outlet']);

foreach($outlets as $outlet) {
    echo "Testing outlet: {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
    
    try {
        $transactionNumber = App\Models\PosSale::generateTransactionNumber($outlet->id_outlet);
        echo "✅ Generated number: {$transactionNumber}\n";
        
        // Check if this number already exists
        $exists = DB::table('pos_sales')->where('no_transaksi', $transactionNumber)->exists();
        echo "   Exists in DB: " . ($exists ? 'Yes (will increment)' : 'No (safe to use)') . "\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test dengan data existing
echo "Checking existing POS transactions:\n";
$existingTransactions = DB::table('pos_sales')
    ->select('no_transaksi', 'id_outlet')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach($existingTransactions as $transaction) {
    echo "- {$transaction->no_transaksi} (Outlet: {$transaction->id_outlet})\n";
}

echo "\n=== Test Complete ===\n";