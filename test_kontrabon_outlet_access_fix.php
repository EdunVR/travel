<?php

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== KONTRA BON OUTLET ACCESS FIX TEST ===\n\n";

try {
    echo "1. Checking User Outlet Access After Fix:\n";
    $users = DB::table('users')->select('id', 'name', 'email', 'akses_outlet')->get();
    
    foreach ($users as $user) {
        echo "   👤 {$user->name} (ID: {$user->id})\n";
        echo "      Email: {$user->email}\n";
        echo "      Outlet Access: " . ($user->akses_outlet ?? 'NULL') . "\n";
        
        if (empty($user->akses_outlet) || $user->akses_outlet === 'null') {
            echo "      ❌ NO OUTLET ACCESS\n";
        } else {
            $outlets = json_decode($user->akses_outlet, true);
            if (is_array($outlets)) {
                echo "      ✅ Has access to outlets: " . implode(', ', $outlets) . "\n";
            } else {
                echo "      ⚠️  Invalid outlet access format\n";
            }
        }
        echo "\n";
    }
    
    echo "2. Verifying Data Availability:\n";
    
    // Check piutang data for outlet 2 (which has data)
    $piutangCount = DB::table('piutang')->where('id_outlet', 2)->count();
    echo "   📊 Piutang records in outlet 2: $piutangCount\n";
    
    if ($piutangCount > 0) {
        $belumLunasCount = DB::table('piutang')
            ->where('id_outlet', 2)
            ->where('status', 'belum_lunas')
            ->count();
        
        $lunasCount = DB::table('piutang')
            ->where('id_outlet', 2)
            ->where('status', 'lunas')
            ->count();
        
        echo "   📊 Belum Lunas: $belumLunasCount\n";
        echo "   📊 Lunas: $lunasCount\n";
    }
    
    // Check kontra bon data
    $kontraBonCount = DB::table('kontra_bon')->count();
    echo "   📊 Total Kontra Bon records: $kontraBonCount\n";
    
    echo "\n3. Testing Controller Logic Simulation:\n";
    
    // Simulate the controller logic for Super Administrator
    $superAdmin = DB::table('users')->where('id', 2)->first();
    if ($superAdmin) {
        echo "   👤 Testing for: {$superAdmin->name}\n";
        
        $userOutlets = json_decode($superAdmin->akses_outlet, true) ?? [];
        echo "   📊 User outlets from DB: " . json_encode($userOutlets) . "\n";
        
        // Simulate super admin logic
        $allOutlets = DB::table('outlets')->pluck('id_outlet')->toArray();
        echo "   📊 All available outlets: " . json_encode($allOutlets) . "\n";
        
        // Simulate selected outlets (user selects outlet 2)
        $selectedOutlets = [2];
        echo "   📊 User selected outlets: " . json_encode($selectedOutlets) . "\n";
        
        // Simulate intersection logic
        $finalOutlets = array_intersect($selectedOutlets, $userOutlets);
        echo "   📊 Final outlets (after intersection): " . json_encode($finalOutlets) . "\n";
        
        if (!empty($finalOutlets)) {
            echo "   ✅ SUCCESS: User should see data for outlets " . implode(', ', $finalOutlets) . "\n";
            
            // Test actual query
            $testPiutang = DB::table('piutang')
                ->whereIn('id_outlet', $finalOutlets)
                ->where('status', 'belum_lunas')
                ->count();
            
            echo "   📊 Expected piutang records: $testPiutang\n";
        } else {
            echo "   ❌ PROBLEM: Final outlets is empty - no data will show\n";
        }
    }
    
    echo "\n4. Expected Results:\n";
    echo "   ✅ Super Administrator should now have outlet access\n";
    echo "   ✅ Piutang tab should show data when outlet 2 is selected\n";
    echo "   ✅ List Kontra Bon tab should work (if there's kontra bon data)\n";
    echo "   ✅ Outlet filter should function properly\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n🎯 OUTLET ACCESS FIX TEST COMPLETE!\n";
echo "The Super Administrator should now be able to see data in Kontra Bon page.\n";

?>