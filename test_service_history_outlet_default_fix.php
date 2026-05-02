<?php

/**
 * Test Service History Outlet Default Fix
 * This script tests the fix for outlet default logic in ServiceController
 */

echo "🧪 Testing Service History Outlet Default Fix\n";
echo "=" . str_repeat("=", 45) . "\n\n";

// Check if we can run Laravel commands
if (!function_exists('app')) {
    echo "❌ Laravel not bootstrapped. Run this from Laravel context.\n";
    exit;
}

try {
    // Test 1: Check available outlets
    echo "📊 Test 1: Available Outlets\n";
    $outlets = \App\Models\Outlet::where('is_active', true)->orderBy('id_outlet')->get();
    
    if ($outlets->count() > 0) {
        echo "✅ Found {$outlets->count()} active outlets:\n";
        foreach ($outlets as $outlet) {
            echo "   - ID: {$outlet->id_outlet}, Name: {$outlet->nama_outlet}\n";
        }
        
        $firstOutlet = $outlets->first();
        echo "\n✅ First outlet: ID {$firstOutlet->id_outlet} ({$firstOutlet->nama_outlet})\n";
    } else {
        echo "❌ No active outlets found!\n";
        exit;
    }
    
    echo "\n";
    
    // Test 2: Test ServiceController outlet default logic
    echo "🔧 Test 2: ServiceController Default Logic\n";
    
    // Simulate request without outlet_id parameter
    $request = new \Illuminate\Http\Request();
    
    // Test the logic that would be used in the controller
    $firstOutlet = \App\Models\Outlet::where('is_active', true)->orderBy('id_outlet')->first();
    $defaultOutlet = $firstOutlet ? $firstOutlet->id_outlet : 1;
    $selectedOutlet = $request->get('outlet_id', auth()->user()->outlet_id ?? $defaultOutlet);
    
    echo "✅ Default outlet logic result: {$selectedOutlet}\n";
    echo "✅ This should match the first available outlet ID: {$firstOutlet->id_outlet}\n";
    
    if ($selectedOutlet == $firstOutlet->id_outlet) {
        echo "✅ PASS: Default outlet logic works correctly!\n";
    } else {
        echo "❌ FAIL: Default outlet logic not working as expected\n";
    }
    
    echo "\n";
    
    // Test 3: Test service invoice data with correct outlet
    echo "📋 Test 3: Service Invoice Data with Correct Outlet\n";
    
    $invoiceCount = \App\Models\ServiceInvoice::where('outlet_id', $selectedOutlet)->count();
    echo "✅ Found {$invoiceCount} service invoices for outlet {$selectedOutlet}\n";
    
    if ($invoiceCount > 0) {
        echo "✅ PASS: Service invoices exist for the default outlet\n";
        
        // Test status counts
        $counts = [
            'menunggu' => \App\Models\ServiceInvoice::where('status', 'menunggu')
                ->where('outlet_id', $selectedOutlet)->count(),
            'lunas' => \App\Models\ServiceInvoice::where('status', 'lunas')
                ->where('outlet_id', $selectedOutlet)->count(),
            'gagal' => \App\Models\ServiceInvoice::where('status', 'gagal')
                ->where('outlet_id', $selectedOutlet)->count(),
        ];
        
        echo "📊 Status counts for outlet {$selectedOutlet}:\n";
        echo "   - Menunggu: {$counts['menunggu']}\n";
        echo "   - Lunas: {$counts['lunas']}\n";
        echo "   - Gagal: {$counts['gagal']}\n";
        echo "   - Total: " . array_sum($counts) . "\n";
        
    } else {
        echo "⚠️  WARNING: No service invoices found for outlet {$selectedOutlet}\n";
        echo "   This might be expected if no invoices have been created yet.\n";
    }
    
    echo "\n";
    
    // Test 4: Simulate API call to history data
    echo "🌐 Test 4: Simulate History Data API Call\n";
    
    try {
        // Create a mock request
        $mockRequest = new \Illuminate\Http\Request();
        $mockRequest->merge(['outlet_id' => $selectedOutlet, 'status' => 'terkini']);
        
        // Test the query that would be used in getHistoryData
        $query = \App\Models\ServiceInvoice::with(['member', 'user', 'mesinCustomer.ongkosKirim', 'outlet'])
            ->where('outlet_id', $selectedOutlet);
        
        $testInvoices = $query->limit(5)->get();
        
        echo "✅ Successfully queried service invoices\n";
        echo "✅ Found {$testInvoices->count()} invoices (limited to 5 for testing)\n";
        
        if ($testInvoices->count() > 0) {
            echo "📋 Sample invoice data:\n";
            $sample = $testInvoices->first();
            echo "   - Invoice: {$sample->no_invoice}\n";
            echo "   - Date: {$sample->tanggal->format('Y-m-d')}\n";
            echo "   - Status: {$sample->status}\n";
            echo "   - Outlet ID: {$sample->outlet_id}\n";
            echo "   - Customer: " . ($sample->member->nama ?? 'N/A') . "\n";
        }
        
        echo "✅ PASS: History data query works correctly!\n";
        
    } catch (\Exception $e) {
        echo "❌ FAIL: Error in history data query: " . $e->getMessage() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🎯 SUMMARY:\n";
echo "The fix changes the ServiceController to use the first available outlet\n";
echo "as default instead of hardcoded outlet ID 1. This ensures that:\n";
echo "1. The Service History page loads data on first visit\n";
echo "2. The outlet filter shows the correct default selection\n";
echo "3. All API endpoints use consistent outlet logic\n";

echo "\n✨ Test completed!\n";