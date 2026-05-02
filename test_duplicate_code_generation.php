<?php

/**
 * Test Script: Duplicate Code Generation
 * 
 * This script tests multiple generate code calls to ensure no duplicates
 */

require_once 'vendor/autoload.php';

echo "=== TEST DUPLICATE CODE GENERATION ===\n\n";

$baseUrl = 'http://localhost:8000'; // Adjust to your local URL
$testOutletId = 1; // Adjust to existing outlet ID

echo "Testing multiple generate code calls for outlet $testOutletId...\n\n";

// Test asset accounts
echo "1. Testing Asset Account Generation (5 calls):\n";
testMultipleGenerations($baseUrl, $testOutletId, 'asset', 5);

echo "\n2. Testing Liability Account Generation (5 calls):\n";
testMultipleGenerations($baseUrl, $testOutletId, 'liability', 5);

echo "\n3. Testing Expense Account Generation (5 calls):\n";
testMultipleGenerations($baseUrl, $testOutletId, 'expense', 5);

echo "\n=== TEST COMPLETED ===\n";

function testMultipleGenerations($baseUrl, $outletId, $type, $count) {
    $generatedCodes = [];
    $duplicates = [];
    
    for ($i = 1; $i <= $count; $i++) {
        echo "  Call $i: ";
        
        $url = "$baseUrl/finance/chart-of-accounts/generate-code?outlet_id=$outletId&type=$type&_t=" . time() . rand(1000, 9999);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if ($data && isset($data['success']) && $data['success']) {
                $code = $data['data']['code'];
                echo "$code";
                
                // Check for duplicates
                if (in_array($code, $generatedCodes)) {
                    echo " ❌ DUPLICATE!";
                    $duplicates[] = $code;
                } else {
                    echo " ✅";
                }
                
                $generatedCodes[] = $code;
            } else {
                echo "❌ API Error: " . ($data['message'] ?? 'Unknown error');
            }
        } else {
            echo "❌ HTTP Error: $httpCode";
        }
        
        echo "\n";
        
        // Small delay to avoid race conditions
        usleep(100000); // 0.1 second
    }
    
    echo "  📊 Summary:\n";
    echo "    Generated codes: " . implode(', ', $generatedCodes) . "\n";
    echo "    Unique codes: " . count(array_unique($generatedCodes)) . "/" . count($generatedCodes) . "\n";
    
    if (!empty($duplicates)) {
        echo "    ❌ Duplicates found: " . implode(', ', array_unique($duplicates)) . "\n";
        echo "    🚨 ISSUE: Code generation is producing duplicates!\n";
    } else {
        echo "    ✅ No duplicates found\n";
    }
}

echo "\n📋 WHAT TO CHECK IF DUPLICATES FOUND:\n";
echo "1. Database transaction isolation\n";
echo "2. Race condition in code generation logic\n";
echo "3. Caching issues\n";
echo "4. Database locking mechanism\n";
echo "5. Unique constraints on code+outlet_id\n";

echo "\n🔧 POTENTIAL SOLUTIONS:\n";
echo "1. Add database transaction with proper locking\n";
echo "2. Use database sequences or auto-increment\n";
echo "3. Add unique constraint and handle conflicts\n";
echo "4. Implement retry mechanism for conflicts\n";

?>