<?php

/**
 * DEBUG KONTRA BON OUTLET ACCESS ISSUE
 * 
 * This script diagnoses why data is not showing despite outlet selection
 */

echo "=== KONTRA BON OUTLET ACCESS DEBUG ===\n\n";

// Check current user
echo "1. Current User Information:\n";
try {
    $user = auth()->user();
    if ($user) {
        echo "   ✅ User ID: " . $user->id . "\n";
        echo "   ✅ User Name: " . $user->name . "\n";
        echo "   ✅ User Email: " . $user->email . "\n";
        
        // Check user outlet access
        $userOutlets = $user->akses_outlet ?? [];
        echo "   📊 User Outlet Access: " . json_encode($userOutlets) . "\n";
        
        if (empty($userOutlets)) {
            echo "   ❌ PROBLEM: User has NO outlet access configured!\n";
        } else {
            echo "   ✅ User has access to " . count($userOutlets) . " outlets\n";
        }
        
        // Check user roles
        if (method_exists($user, 'getRoleNames')) {
            $roles = $user->getRoleNames();
            echo "   📊 User Roles: " . $roles->implode(', ') . "\n";
        }
        
        // Check if super admin
        if (method_exists($user, 'hasRole')) {
            $isSuperAdmin = $user->hasRole('super_admin');
            echo "   📊 Is Super Admin: " . ($isSuperAdmin ? 'Yes' : 'No') . "\n";
        }
    } else {
        echo "   ❌ No authenticated user found\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  User check failed: " . $e->getMessage() . "\n";
}

echo "\n2. Available Outlets in Database:\n";
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

echo "\n3. Sample Data Check:\n";
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

echo "\n4. Controller Logic Simulation:\n";
try {
    $user = auth()->user();
    $userOutlets = $user->akses_outlet ?? [];
    $selectedOutlets = [2, 3]; // Example selection
    
    echo "   📊 User outlets: " . json_encode($userOutlets) . "\n";
    echo "   📊 Selected outlets: " . json_encode($selectedOutlets) . "\n";
    
    // Simulate controller logic
    if (!empty($selectedOutlets)) {
        $finalOutlets = array_intersect($selectedOutlets, $userOutlets);
    } else {
        $finalOutlets = $userOutlets;
    }
    
    echo "   📊 Final outlets (after intersection): " . json_encode($finalOutlets) . "\n";
    
    if (empty($finalOutlets)) {
        echo "   ❌ PROBLEM: Final outlets is empty - no data will be returned!\n";
        echo "   💡 SOLUTION: User needs outlet access or should be super admin\n";
    } else {
        echo "   ✅ Final outlets is not empty - data should be returned\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  Logic simulation failed: " . $e->getMessage() . "\n";
}

echo "\n=== SOLUTIONS ===\n";
echo "If user has NO outlet access:\n";
echo "1. Grant outlet access to user:\n";
echo "   UPDATE users SET akses_outlet = '[1,2,3]' WHERE id = {user_id};\n";
echo "2. OR make user super admin (bypasses outlet restrictions)\n";
echo "3. OR modify controller to allow access to all outlets for certain roles\n\n";

echo "If user IS super admin but still no data:\n";
echo "1. Check if super admin logic is implemented in controller\n";
echo "2. Verify role assignment in database\n";
echo "3. Check permission system configuration\n\n";

echo "🔧 QUICK FIX COMMANDS:\n";
if (isset($user) && $user) {
    echo "-- Grant access to all outlets for current user:\n";
    $allOutletIds = DB::table('outlets')->pluck('id_outlet')->toArray();
    echo "UPDATE users SET akses_outlet = '" . json_encode($allOutletIds) . "' WHERE id = {$user->id};\n\n";
    
    echo "-- OR make user super admin:\n";
    echo "-- (This depends on your role system implementation)\n";
}

echo "🎯 KONTRA BON OUTLET ACCESS DEBUG COMPLETE!\n";

?>