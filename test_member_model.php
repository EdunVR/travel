<?php
/**
 * Test Member model and customer data
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Member model and customer data...\n";
echo "==========================================\n\n";

try {
    // Check if Member model exists
    if (class_exists('App\Models\Member')) {
        echo "✅ Member model exists\n";
        
        // Test database connection
        $count = \App\Models\Member::count();
        echo "📊 Total members in database: $count\n";
        
        if ($count > 0) {
            // Get sample member
            $sample = \App\Models\Member::first();
            echo "📄 Sample member data:\n";
            echo "   - ID: {$sample->id_member}\n";
            echo "   - Name: {$sample->nama}\n";
            echo "   - Phone: " . ($sample->telepon ?? 'NULL') . "\n";
            echo "   - Outlet ID: " . ($sample->id_outlet ?? 'NULL') . "\n";
            echo "   - Type ID: " . ($sample->id_tipe ?? 'NULL') . "\n";
            
            // Check outlet distribution
            $outlets = \App\Models\Member::selectRaw('id_outlet, COUNT(*) as count')
                ->groupBy('id_outlet')
                ->get();
            
            echo "📊 Members by outlet:\n";
            foreach ($outlets as $outlet) {
                echo "   - Outlet {$outlet->id_outlet}: {$outlet->count} members\n";
            }
            
            // Test the actual getCustomers query for outlet 1
            echo "\n🧪 Testing getCustomers query for outlet 1:\n";
            $customers = \App\Models\Member::select('id_member', 'nama', 'telepon', 'id_tipe', 'id_outlet')
                ->with('tipe:id_tipe,nama_tipe')
                ->where('id_outlet', 1)
                ->orderBy('nama')
                ->get();
            
            echo "📊 Customers found for outlet 1: " . $customers->count() . "\n";
            
            if ($customers->count() > 0) {
                echo "📄 Sample customer for outlet 1:\n";
                $firstCustomer = $customers->first();
                echo "   - ID: {$firstCustomer->id_member}\n";
                echo "   - Name: {$firstCustomer->nama}\n";
                echo "   - Phone: " . ($firstCustomer->telepon ?? 'NULL') . "\n";
                echo "   - Type: " . ($firstCustomer->tipe->nama_tipe ?? 'NULL') . "\n";
            }
        } else {
            echo "⚠️ No members found in database\n";
        }
    } else {
        echo "❌ Member model not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📄 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✅ Test completed!\n";