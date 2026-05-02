<?php

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING USER OUTLET ACCESS ===\n\n";

try {
    // Check outlets
    echo "1. Available Outlets:\n";
    $outlets = DB::table('outlets')->select('id_outlet', 'nama_outlet')->get();
    foreach ($outlets as $outlet) {
        echo "   - ID: {$outlet->id_outlet}, Name: {$outlet->nama_outlet}\n";
    }
    
    echo "\n2. Users and Their Outlet Access:\n";
    $users = DB::table('users')->select('id', 'name', 'email', 'akses_outlet')->get();
    foreach ($users as $user) {
        echo "   👤 {$user->name} (ID: {$user->id})\n";
        echo "      Email: {$user->email}\n";
        echo "      Outlet Access: " . ($user->akses_outlet ?? 'NULL') . "\n";
        
        if (empty($user->akses_outlet) || $user->akses_outlet === 'null') {
            echo "      ❌ NO OUTLET ACCESS!\n";
        } else {
            echo "      ✅ Has outlet access\n";
        }
        echo "\n";
    }
    
    echo "3. Sample Data by Outlet:\n";
    $piutangByOutlet = DB::table('piutang')
        ->select('id_outlet', DB::raw('count(*) as count'))
        ->groupBy('id_outlet')
        ->get();
    
    echo "   Piutang records by outlet:\n";
    foreach ($piutangByOutlet as $item) {
        echo "   - Outlet {$item->id_outlet}: {$item->count} records\n";
    }
    
    echo "\n4. Quick Fix Commands:\n";
    $allOutletIds = DB::table('outlets')->pluck('id_outlet')->toArray();
    $allOutletsJson = json_encode($allOutletIds);
    
    echo "   -- Grant access to ALL outlets for user ID 1:\n";
    echo "   UPDATE users SET akses_outlet = '$allOutletsJson' WHERE id = 1;\n\n";
    
    echo "   -- Or run this to grant access to the first user:\n";
    $firstUser = $users->first();
    if ($firstUser) {
        echo "   UPDATE users SET akses_outlet = '$allOutletsJson' WHERE id = {$firstUser->id};\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n🎯 DIAGNOSIS COMPLETE!\n";

?>