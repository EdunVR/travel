<?php

/**
 * DEBUG KONTRA BON OUTLET ACCESS - ARTISAN VERSION
 * Run with: php artisan tinker --execute="require 'debug_kontrabon_outlet_access_artisan.php';"
 */

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== KONTRA BON OUTLET ACCESS DEBUG ===\n\n";

// Check available outlets
echo "1. Available Outlets in Database:\n";
try {
    $outlets = DB::table('outlets')->select('id_outlet', 'nama_outlet')->get();
    
    if ($outlets->count() > 0) {
        echo "   📊 Total outlets in database: " . $outlets->count() . "\n";
        foreach ($outlets as $outlet) {
            echo "      - ID: {$outlet->id_outlet}, Name: {$outlet->nama_outlet}\n";
        }
    } else {
        echo "   ❌ No outlets found in database\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  Outlet check failed: " . $e->getMessage() . "\n";
}

echo "\n2. Sample Data Check:\n";
try {
    // Check piutang data
    $piutangCount = DB::table('piutang')->count();
    echo "   📊 Total piutang records: $piutangCount\n";
    
    if ($piutangCount > 0) {
        $piutangByOutlet = DB::table('piutang')
            ->select('id_outlet', DB::raw('count(*) as count'))
            ->groupBy('id_outlet')
            ->get();
        
        echo "   📊 Piutang by outlet:\n";
        foreach ($piutangByOutlet as $item) {
            echo "      - Outlet {$item->id_outlet}: {$item->count} records\n";
        }
        
        // Check status distribution
        $piutangByStatus = DB::table('piutang')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        echo "   📊 Piutang by status:\n";
        foreach ($piutangByStatus as $item) {
            echo "      - Status '{$item->status}': {$item->count} records\n";
        }
    }
    
    // Check kontra bon data
    $kontraBonCount = DB::table('kontra_bon')->count();
    echo "   📊 Total kontra bon records: $kontraBonCount\n";
    
    if ($kontraBonCount > 0) {
        $kontraBonByOutlet = DB::table('kontra_bon')
            ->select('id_outlet', DB::raw('count(*) as count'))
            ->groupBy('id_outlet')
            ->get();
        
        echo "   📊 Kontra Bon by outlet:\n";
        foreach ($kontraBonByOutlet as $item) {
            echo "      - Outlet {$item->id_outlet}: {$item->count} records\n";
        }
    }
} catch (Exception $e) {
    echo "   ⚠️  Data check failed: " . $e->getMessage() . "\n";
}

echo "\n3. Users and Outlet Access:\n";
try {
    $users = User::select('id', 'name', 'email', 'akses_outlet')->get();
    
    foreach ($users as $user) {
        echo "   👤 User: {$user->name} (ID: {$user->id})\n";
        echo "      Email: {$user->email}\n";
        
        $userOutlets = $user->akses_outlet ?? [];
        if (is_string($userOutlets)) {
            $userOutlets = json_decode($userOutlets, true) ?? [];
        }
        
        echo "      Outlet Access: " . json_encode($userOutlets) . "\n";
        
        if (empty($userOutlets)) {
            echo "      ❌ NO OUTLET ACCESS - This user won't see any data!\n";
        } else {
            echo "      ✅ Has access to " . count($userOutlets) . " outlets\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  User check failed: " . $e->getMessage() . "\n";
}

echo "\n=== QUICK FIXES ===\n";
echo "To grant outlet access to a user, run one of these commands:\n\n";

try {
    $allOutletIds = DB::table('outlets')->pluck('id_outlet')->toArray();
    $allOutletsJson = json_encode($allOutletIds);
    
    echo "-- Grant access to ALL outlets for user ID 1:\n";
    echo "UPDATE users SET akses_outlet = '$allOutletsJson' WHERE id = 1;\n\n";
    
    echo "-- Grant access to specific outlets (example: outlets 1,2,3):\n";
    echo "UPDATE users SET akses_outlet = '[1,2,3]' WHERE id = 1;\n\n";
    
    echo "-- Check current user outlet access:\n";
    echo "SELECT id, name, akses_outlet FROM users;\n\n";
    
} catch (Exception $e) {
    echo "   ⚠️  Fix generation failed: " . $e->getMessage() . "\n";
}

echo "🎯 KONTRA BON OUTLET ACCESS DEBUG COMPLETE!\n";
echo "The most likely issue is that the current user has no outlet access configured.\n";

?>