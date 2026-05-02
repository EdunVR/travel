<?php

/**
 * Test script for Service History outlet filter initialization
 */

echo "🔍 Testing Service History Outlet Filter Initialization\n";
echo "=" . str_repeat("=", 55) . "\n\n";

// Test 1: Check if debug logging is added
echo "1. Checking debug logging implementation...\n";

$viewFile = 'resources/views/admin/service/history/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    $debugLogs = [
        'console.log(\'🚀 Initializing Service History with outlet:\'' => 'Initialization logging',
        'console.log(\'🔧 Setting default outlet filter:\'' => 'Default outlet setting logging',
        'console.log(\'✅ Service History initialized with outlet:\'' => 'Completion logging',
        'console.log(\'📊 Fetching data with outlet:\'' => 'Data fetch logging',
        'console.log(\'📈 Data fetched:\'' => 'Data result logging',
        'console.log(\'📊 Loading status counts for outlet:\'' => 'Status counts logging',
        'console.log(\'📈 Status counts loaded:\'' => 'Status counts result logging',
        'console.log(\'🔄 Changing outlet to:\'' => 'Outlet change logging'
    ];
    
    foreach ($debugLogs as $pattern => $description) {
        if (strpos($content, $pattern) !== false) {
            echo "   ✅ $description\n";
        } else {
            echo "   ❌ $description - NOT FOUND\n";
        }
    }
} else {
    echo "   ❌ View file not found\n";
}

echo "\n";

// Test 2: Check outlet filter initialization
echo "2. Checking outlet filter initialization logic...\n";

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    $initChecks = [
        'if (!this.outletFilter)' => 'Outlet filter validation',
        'this.outletFilter = {{ $selectedOutlet }}' => 'Default outlet assignment',
        'outletFilter: {{ $selectedOutlet }}' => 'Initial outlet filter value'
    ];
    
    foreach ($initChecks as $pattern => $description) {
        if (strpos($content, $pattern) !== false) {
            echo "   ✅ $description\n";
        } else {
            echo "   ❌ $description - NOT FOUND\n";
        }
    }
} else {
    echo "   ❌ View file not found\n";
}

echo "\n";

// Test 3: Check data fetching logic
echo "3. Checking data fetching with outlet filter...\n";

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    $fetchChecks = [
        'outlet_id: this.outletFilter' => 'Outlet ID parameter in data fetch',
        'outlet_id=${this.outletFilter}' => 'Outlet ID parameter in status counts',
        'await Promise.all([' => 'Parallel data loading',
        'this.fetchData(),' => 'Data fetch in init',
        'this.loadStatusCounts()' => 'Status counts in init'
    ];
    
    foreach ($fetchChecks as $pattern => $description) {
        if (strpos($content, $pattern) !== false) {
            echo "   ✅ $description\n";
        } else {
            echo "   ❌ $description - NOT FOUND\n";
        }
    }
} else {
    echo "   ❌ View file not found\n";
}

echo "\n";

// Test 4: Check server-side outlet selection
echo "4. Checking server-side outlet selection...\n";

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    $serverChecks = [
        '{{ $outlet->id_outlet == $selectedOutlet ? \'selected\' : \'\' }}' => 'Server-side outlet selection',
        'outletFilter: {{ $selectedOutlet }}' => 'Server-side outlet filter initialization',
        'currentStatus: \'{{ $status }}\'' => 'Server-side status initialization'
    ];
    
    foreach ($serverChecks as $pattern => $description) {
        if (strpos($content, $pattern) !== false) {
            echo "   ✅ $description\n";
        } else {
            echo "   ❌ $description - NOT FOUND\n";
        }
    }
} else {
    echo "   ❌ View file not found\n";
}

echo "\n";

// Expected behavior
echo "📋 EXPECTED BEHAVIOR AFTER FIX:\n";
echo "=" . str_repeat("=", 35) . "\n";
echo "1. Page loads with selected outlet from URL/session\n";
echo "2. Data is immediately fetched for the selected outlet\n";
echo "3. Status counts are loaded for the selected outlet\n";
echo "4. No need to manually change outlet filter first\n";
echo "5. Debug logs show the outlet being used correctly\n";

echo "\n🧪 TESTING STEPS:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Open Service History page\n";
echo "3. Check browser console for debug logs:\n";
echo "   - Should see '🚀 Initializing Service History with outlet: X'\n";
echo "   - Should see '📊 Fetching data with outlet: X status: terkini'\n";
echo "   - Should see '📈 Data fetched: N invoices for outlet: X'\n";
echo "   - Should see '📊 Loading status counts for outlet: X'\n";
echo "   - Should see '✅ Service History initialized with outlet: X'\n";
echo "4. Verify data is displayed immediately without changing outlet\n";
echo "5. Try changing outlet filter - should see '🔄 Changing outlet to: Y'\n";

echo "\n🔍 CONSOLE LOGS TO LOOK FOR:\n";
echo "✅ '🚀 Initializing Service History with outlet: [outlet_id]'\n";
echo "✅ '📊 Fetching data with outlet: [outlet_id] status: [status]'\n";
echo "✅ '📈 Data fetched: [count] invoices for outlet: [outlet_id]'\n";
echo "✅ '📊 Loading status counts for outlet: [outlet_id]'\n";
echo "✅ '📈 Status counts loaded: {data}'\n";
echo "✅ '✅ Service History initialized with outlet: [outlet_id]'\n";

echo "\n🔧 TROUBLESHOOTING:\n";
echo "- If no data shows: Check if outlet_id parameter is being sent correctly\n";
echo "- If wrong data shows: Verify the selected outlet ID in console logs\n";
echo "- If counts are wrong: Check status counts API response in console\n";
echo "- If outlet filter doesn't work: Check changeOutlet method logging\n";

echo "\n✨ Service History outlet filter initialization enhanced!\n";