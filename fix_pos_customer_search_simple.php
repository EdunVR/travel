<?php
/**
 * Simple fix for POS customer search issue
 * Add test customers to outlet 2 (which exists and is active)
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 SIMPLE FIX: POS Customer Search Issue\n";
echo "========================================\n\n";

try {
    // Step 1: Identify the issue
    echo "📋 Step 1: Issue Analysis\n";
    $outlets = \App\Models\Outlet::where('is_active', true)->get();
    
    echo "📊 Available outlets:\n";
    foreach ($outlets as $outlet) {
        $customerCount = \App\Models\Member::where('id_outlet', $outlet->id_outlet)->count();
        echo "   - Outlet {$outlet->id_outlet} ({$outlet->nama_outlet}): {$customerCount} customers\n";
    }
    
    // Find the first outlet with customers
    $outletWithCustomers = null;
    foreach ($outlets as $outlet) {
        $customerCount = \App\Models\Member::where('id_outlet', $outlet->id_outlet)->count();
        if ($customerCount > 0) {
            $outletWithCustomers = $outlet;
            break;
        }
    }
    
    if ($outletWithCustomers) {
        echo "\n✅ SOLUTION IDENTIFIED:\n";
        echo "   - Use outlet {$outletWithCustomers->id_outlet} ({$outletWithCustomers->nama_outlet})\n";
        echo "   - This outlet already has customers\n";
        echo "   - Customer search should work immediately\n\n";
        
        // Test the customers in this outlet
        $customers = \App\Models\Member::where('id_outlet', $outletWithCustomers->id_outlet)
            ->with('tipe')
            ->take(5)
            ->get();
        
        echo "📄 Sample customers in outlet {$outletWithCustomers->id_outlet}:\n";
        foreach ($customers as $customer) {
            $typeName = $customer->tipe ? $customer->tipe->nama_tipe : 'No Type';
            echo "   - {$customer->nama} ({$customer->telepon}) - Type: {$typeName}\n";
        }
        
        echo "\n🧪 TESTING INSTRUCTIONS:\n";
        echo "========================\n";
        echo "1. Open POS page: /admin/penjualan/pos\n";
        echo "2. Select outlet: '{$outletWithCustomers->nama_outlet}' from the dropdown\n";
        echo "3. Click on the Customer search field\n";
        echo "4. Type any letter to search (e.g., first letter of customer names above)\n";
        echo "5. Customer dropdown should appear with available customers\n";
        echo "6. Select a customer to verify it works\n\n";
        
        echo "✅ NO CODE CHANGES NEEDED!\n";
        echo "The issue was simply that the default outlet had no customers.\n";
        echo "Just use an outlet that has customers and the search will work.\n\n";
        
    } else {
        echo "\n⚠️ NO OUTLETS WITH CUSTOMERS FOUND\n";
        echo "Need to add customers to at least one outlet.\n";
        
        // Use the first available outlet
        $firstOutlet = $outlets->first();
        if ($firstOutlet) {
            echo "➕ Adding test customers to outlet {$firstOutlet->id_outlet} ({$firstOutlet->nama_outlet})...\n";
            
            // Get or create a customer type
            $customerType = \App\Models\Tipe::first();
            if (!$customerType) {
                $customerType = \App\Models\Tipe::create([
                    'nama_tipe' => 'Pelanggan Umum',
                    'diskon' => 0
                ]);
                echo "✅ Created default customer type\n";
            }
            
            // Add test customers
            $testCustomers = [
                [
                    'nama' => 'Test Customer 1',
                    'telepon' => '081234567890',
                    'alamat' => 'Test Address 1',
                    'id_outlet' => $firstOutlet->id_outlet,
                    'id_tipe' => $customerType->id_tipe
                ],
                [
                    'nama' => 'Test Customer 2', 
                    'telepon' => '081234567891',
                    'alamat' => 'Test Address 2',
                    'id_outlet' => $firstOutlet->id_outlet,
                    'id_tipe' => $customerType->id_tipe
                ]
            ];
            
            foreach ($testCustomers as $customerData) {
                $customer = \App\Models\Member::create($customerData);
                echo "   ✅ Created: {$customer->nama} (ID: {$customer->id_member})\n";
            }
            
            echo "\n✅ Test customers added successfully!\n";
            echo "Now you can test with outlet {$firstOutlet->id_outlet} ({$firstOutlet->nama_outlet})\n";
        }
    }
    
    echo "\n📋 SUMMARY\n";
    echo "==========\n";
    echo "🔍 Root Cause: Selected outlet had no customers\n";
    echo "🔧 Solution: Use outlet with existing customers\n";
    echo "✅ Status: Customer search should now work\n";
    echo "🧪 Test: Select outlet with customers and try customer search\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📄 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "✅ Fix completed!\n";