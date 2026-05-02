<?php

/**
 * Debug Inter Outlet Print Issue
 * 
 * This script debugs:
 * 1. Check if transactions exist in database
 * 2. Test route accessibility
 * 3. Check for any access control issues
 */

echo "=== DEBUGGING INTER OUTLET PRINT ISSUE ===\n\n";

// Test 1: Check database connection and table
echo "1. TESTING DATABASE CONNECTION:\n";
try {
    // Simple database connection test
    $pdo = new PDO(
        'mysql:host=localhost;dbname=tofu', 
        'root', 
        '', 
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "   ✅ Database connection successful\n";
    
    // Check if inter_outlet_sales table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'inter_outlet_sales'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ inter_outlet_sales table exists\n";
    } else {
        echo "   ❌ inter_outlet_sales table NOT found\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check for existing transactions
echo "2. TESTING EXISTING TRANSACTIONS:\n";
try {
    $stmt = $pdo->query("SELECT id, no_transaksi, status, created_at FROM inter_outlet_sales ORDER BY created_at DESC LIMIT 5");
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($transactions) > 0) {
        echo "   ✅ Found " . count($transactions) . " transactions:\n";
        foreach ($transactions as $transaction) {
            echo "      - ID: {$transaction['id']}, No: {$transaction['no_transaksi']}, Status: {$transaction['status']}\n";
        }
        
        // Test URL for first transaction
        $firstTransaction = $transactions[0];
        $testUrl = "/admin/penjualan/inter-outlet/{$firstTransaction['id']}/print";
        echo "   ✅ Test URL: {$testUrl}\n";
        
    } else {
        echo "   ⚠️  No transactions found in database\n";
        echo "   ℹ️  Create a transaction first to test print functionality\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error querying transactions: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Check route cache
echo "3. TESTING ROUTE CACHE:\n";
if (file_exists('bootstrap/cache/routes-v7.php')) {
    echo "   ⚠️  Route cache file exists\n";
    echo "   ℹ️  Run: php artisan route:clear to clear cache\n";
} else {
    echo "   ✅ No route cache file found\n";
}

echo "\n";

// Test 4: Check Laravel logs
echo "4. CHECKING LARAVEL LOGS:\n";
$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    echo "   ✅ Laravel log file exists\n";
    
    // Get last few lines of log
    $lines = file($logFile);
    $lastLines = array_slice($lines, -10);
    
    echo "   📋 Last 10 log entries:\n";
    foreach ($lastLines as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'inter-outlet') !== false) {
            echo "      " . trim($line) . "\n";
        }
    }
} else {
    echo "   ⚠️  Laravel log file not found\n";
}

echo "\n";

// Test 5: Generate test URLs
echo "5. GENERATING TEST URLS:\n";
if (isset($transactions) && count($transactions) > 0) {
    foreach ($transactions as $transaction) {
        $url = "http://localhost/tofu/admin/penjualan/inter-outlet/{$transaction['id']}/print";
        echo "   🔗 Test URL for transaction {$transaction['no_transaksi']}: {$url}\n";
    }
} else {
    echo "   ℹ️  No transactions available for URL generation\n";
}

echo "\n";

// Test 6: Check middleware and permissions
echo "6. CHECKING MIDDLEWARE:\n";
$routeFile = file_get_contents('routes/web.php');
if (strpos($routeFile, "middleware(['auth'])") !== false) {
    echo "   ✅ Auth middleware found - user must be logged in\n";
} else {
    echo "   ⚠️  Auth middleware not found\n";
}

echo "\n";

// Summary and recommendations
echo "=== SUMMARY & RECOMMENDATIONS ===\n";
echo "COMMON CAUSES OF 'NOT FOUND' ERROR:\n";
echo "1. 🔍 Transaction ID doesn't exist in database\n";
echo "2. 🔐 User not logged in or lacks permission\n";
echo "3. 🚫 Route cache is stale\n";
echo "4. 🌐 Incorrect base URL or path\n";
echo "5. 📝 Laravel application error\n";
echo "\n";

echo "DEBUGGING STEPS:\n";
echo "1. Clear route cache: php artisan route:clear\n";
echo "2. Clear application cache: php artisan cache:clear\n";
echo "3. Check Laravel logs: tail -f storage/logs/laravel.log\n";
echo "4. Test URL directly in browser (while logged in)\n";
echo "5. Check browser Network tab for actual HTTP status\n";
echo "6. Verify transaction exists: SELECT * FROM inter_outlet_sales WHERE id = X\n";
echo "\n";

echo "IMMEDIATE ACTIONS:\n";
echo "1. 🔄 Clear all caches\n";
echo "2. 📊 Create a test transaction\n";
echo "3. 🧪 Test print URL directly\n";
echo "4. 📋 Check Laravel logs for errors\n";
echo "\n";

echo "✅ DEBUG ANALYSIS COMPLETE!\n";

?>