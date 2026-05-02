<?php

/**
 * Check outlets data to understand the outlet ID issue
 */

echo "🔍 Checking Outlets Data\n";
echo "=" . str_repeat("=", 25) . "\n\n";

// Check if we can run Laravel commands
if (!function_exists('app')) {
    echo "❌ Laravel not bootstrapped. Run this from Laravel context.\n";
    echo "\n💡 To check outlets manually, run this SQL query:\n";
    echo "SELECT id_outlet, nama_outlet, is_active FROM outlets WHERE is_active = 1 ORDER BY id_outlet;\n";
    echo "\n💡 To check user outlet access, run:\n";
    echo "SELECT id, name, outlet_id FROM users WHERE id = [current_user_id];\n";
    exit;
}

try {
    // Get active outlets
    $outlets = \App\Models\Outlet::where('is_active', true)->orderBy('id_outlet')->get();
    
    echo "📊 Active Outlets:\n";
    if ($outlets->count() > 0) {
        foreach ($outlets as $outlet) {
            echo "   - ID: {$outlet->id_outlet}, Name: {$outlet->nama_outlet}\n";
        }
        
        $firstOutlet = $outlets->first();
        echo "\n✅ First available outlet: ID {$firstOutlet->id_outlet} ({$firstOutlet->nama_outlet})\n";
    } else {
        echo "   ❌ No active outlets found!\n";
    }
    
    echo "\n";
    
    // Check current user's outlet
    if (auth()->check()) {
        $user = auth()->user();
        echo "👤 Current User Info:\n";
        echo "   - ID: {$user->id}\n";
        echo "   - Name: {$user->name}\n";
        echo "   - Outlet ID: " . ($user->outlet_id ?? 'NULL') . "\n";
        
        if ($user->outlet_id) {
            $userOutlet = \App\Models\Outlet::find($user->outlet_id);
            if ($userOutlet) {
                echo "   - Outlet Name: {$userOutlet->nama_outlet}\n";
                echo "   - Outlet Active: " . ($userOutlet->is_active ? 'Yes' : 'No') . "\n";
            } else {
                echo "   ❌ User's outlet ID {$user->outlet_id} not found in outlets table!\n";
            }
        }
    } else {
        echo "👤 No authenticated user\n";
    }
    
    echo "\n";
    
    // Check service invoices by outlet
    echo "📋 Service Invoices by Outlet:\n";
    $invoiceCounts = \App\Models\ServiceInvoice::selectRaw('outlet_id, COUNT(*) as count')
        ->groupBy('outlet_id')
        ->orderBy('outlet_id')
        ->get();
    
    if ($invoiceCounts->count() > 0) {
        foreach ($invoiceCounts as $count) {
            $outlet = \App\Models\Outlet::find($count->outlet_id);
            $outletName = $outlet ? $outlet->nama_outlet : 'Unknown';
            echo "   - Outlet {$count->outlet_id} ({$outletName}): {$count->count} invoices\n";
        }
    } else {
        echo "   ❌ No service invoices found!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🔧 RECOMMENDED FIX:\n";
echo "Update the ServiceController historyIndex method to use the first available outlet as default:\n\n";
echo "// Instead of:\n";
echo "\$selectedOutlet = \$request->get('outlet_id', auth()->user()->outlet_id ?? 1);\n\n";
echo "// Use:\n";
echo "\$firstOutlet = Outlet::where('is_active', true)->orderBy('id_outlet')->first();\n";
echo "\$defaultOutlet = \$firstOutlet ? \$firstOutlet->id_outlet : 1;\n";
echo "\$selectedOutlet = \$request->get('outlet_id', auth()->user()->outlet_id ?? \$defaultOutlet);\n";

echo "\n✨ Analysis completed!\n";