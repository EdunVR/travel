<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

echo "=== TESTING PRE ORDER CREATION ===\n\n";

try {
    // Test 1: Check if Member table exists and has data
    echo "1. Checking Member table...\n";
    $memberCount = Member::count();
    echo "   Members found: {$memberCount}\n";
    
    if ($memberCount > 0) {
        $firstMember = Member::first();
        echo "   First member: {$firstMember->nama} (ID: {$firstMember->id_member})\n";
    }
    
    // Test 2: Test generateKodePreorder
    echo "\n2. Testing generateKodePreorder...\n";
    $preOrder = new PreOrder();
    $kode = $preOrder->generateKodePreorder();
    echo "   Generated code: {$kode}\n";
    
    // Test 3: Try to create a simple PreOrder
    echo "\n3. Testing PreOrder creation...\n";
    
    if ($memberCount > 0) {
        DB::beginTransaction();
        
        $testPreOrder = new PreOrder();
        $testPreOrder->kode_preorder = $preOrder->generateKodePreorder();
        $testPreOrder->customer_id = $firstMember->id_member;
        $testPreOrder->tanggal = now()->format('Y-m-d');
        $testPreOrder->status = 'penawaran';
        $testPreOrder->subtotal = 100000;
        $testPreOrder->diskon = 0;
        $testPreOrder->pajak = 0;
        $testPreOrder->total = 100000;
        $testPreOrder->catatan = 'Test pre order';
        
        $testPreOrder->save();
        echo "   ✓ PreOrder created successfully with ID: {$testPreOrder->id}\n";
        
        // Test PreOrderItem creation
        $testItem = PreOrderItem::create([
            'pre_order_id' => $testPreOrder->id,
            'produk_id' => null,
            'deskripsi' => 'Test item',
            'qty' => 1,
            'harga' => 100000,
            'subtotal' => 100000
        ]);
        echo "   ✓ PreOrderItem created successfully with ID: {$testItem->id}\n";
        
        DB::rollback(); // Don't actually save the test data
        echo "   ✓ Test completed successfully (rolled back)\n";
    } else {
        echo "   ✗ No members found - cannot test creation\n";
    }
    
} catch (Exception $e) {
    DB::rollback();
    echo "   ✗ Error: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}:{$e->getLine()}\n";
}

echo "\n=== TEST COMPLETE ===\n";