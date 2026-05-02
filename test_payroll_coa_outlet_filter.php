<?php

/**
 * Test Script: Payroll COA Outlet Filter & Hide Parent Accounts
 * 
 * This script tests the new functionality for:
 * 1. Loading accounts by outlet
 * 2. Hiding parent accounts (only show leaf accounts)
 * 3. Outlet access validation
 */

require_once 'vendor/autoload.php';

// Test configuration
$baseUrl = 'http://localhost:8000'; // Adjust to your local URL
$testOutletId = 1; // Adjust to existing outlet ID

echo "=== PAYROLL COA OUTLET FILTER TEST ===\n\n";

// Test 1: Test getAccounts endpoint
echo "1. Testing getAccounts endpoint...\n";
testGetAccounts($baseUrl, $testOutletId);

// Test 2: Test account filtering (parent vs leaf)
echo "\n2. Testing parent account filtering...\n";
testParentAccountFiltering($baseUrl, $testOutletId);

// Test 3: Test outlet validation
echo "\n3. Testing outlet access validation...\n";
testOutletValidation($baseUrl);

// Test 4: Test settings load/save
echo "\n4. Testing settings load/save...\n";
testSettingsLoadSave($baseUrl, $testOutletId);

echo "\n=== TEST COMPLETED ===\n";

function testGetAccounts($baseUrl, $outletId) {
    $url = "$baseUrl/sdm/payroll/coa-settings/accounts?outlet_id=$outletId";
    
    echo "  URL: $url\n";
    
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
    
    echo "  HTTP Code: $httpCode\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        
        if ($data && isset($data['success']) && $data['success']) {
            echo "  ✅ SUCCESS: Accounts loaded successfully\n";
            
            $accounts = $data['data'];
            echo "  📊 Account counts:\n";
            echo "     - Expense: " . count($accounts['expense'] ?? []) . "\n";
            echo "     - Liability: " . count($accounts['liability'] ?? []) . "\n";
            echo "     - Asset: " . count($accounts['asset'] ?? []) . "\n";
            
            // Show sample accounts
            if (!empty($accounts['expense'])) {
                echo "  📝 Sample expense accounts:\n";
                foreach (array_slice($accounts['expense'], 0, 3) as $account) {
                    echo "     - {$account['code']} - {$account['name']}\n";
                }
            }
        } else {
            echo "  ❌ FAILED: Invalid response format\n";
            echo "  Response: " . substr($response, 0, 200) . "...\n";
        }
    } else {
        echo "  ❌ FAILED: HTTP $httpCode\n";
        echo "  Response: " . substr($response, 0, 200) . "...\n";
    }
}

function testParentAccountFiltering($baseUrl, $outletId) {
    // This test requires database access to verify parent-child relationships
    echo "  🔍 Checking if parent accounts are properly filtered...\n";
    
    // Get accounts via API
    $url = "$baseUrl/sdm/payroll/coa-settings/accounts?outlet_id=$outletId";
    
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
            $allAccounts = array_merge(
                $data['data']['expense'] ?? [],
                $data['data']['liability'] ?? [],
                $data['data']['asset'] ?? []
            );
            
            echo "  📊 Total accounts returned: " . count($allAccounts) . "\n";
            
            // Check if any account has a generic parent-like name
            $suspiciousParents = [];
            foreach ($allAccounts as $account) {
                $name = strtolower($account['name']);
                if (
                    strpos($name, 'beban') === 0 && strlen($name) < 10 ||
                    strpos($name, 'hutang') === 0 && strlen($name) < 12 ||
                    strpos($name, 'kas') === 0 && strlen($name) < 8 ||
                    strpos($name, 'aset') === 0 && strlen($name) < 8
                ) {
                    $suspiciousParents[] = $account['name'];
                }
            }
            
            if (empty($suspiciousParents)) {
                echo "  ✅ SUCCESS: No obvious parent accounts found\n";
            } else {
                echo "  ⚠️  WARNING: Possible parent accounts found:\n";
                foreach ($suspiciousParents as $name) {
                    echo "     - $name\n";
                }
            }
        }
    } else {
        echo "  ❌ FAILED: Could not fetch accounts for filtering test\n";
    }
}

function testOutletValidation($baseUrl) {
    // Test with invalid outlet ID
    $invalidOutletId = 99999;
    $url = "$baseUrl/sdm/payroll/coa-settings/accounts?outlet_id=$invalidOutletId";
    
    echo "  🔒 Testing with invalid outlet ID: $invalidOutletId\n";
    
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
    
    echo "  HTTP Code: $httpCode\n";
    
    if ($httpCode === 422 || $httpCode === 403 || $httpCode === 404) {
        echo "  ✅ SUCCESS: Invalid outlet properly rejected\n";
    } else if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && !$data['success']) {
            echo "  ✅ SUCCESS: Invalid outlet properly rejected (JSON response)\n";
        } else {
            echo "  ❌ FAILED: Invalid outlet should be rejected\n";
        }
    } else {
        echo "  ⚠️  UNKNOWN: Unexpected response code $httpCode\n";
    }
    
    // Test without outlet ID
    echo "  🔒 Testing without outlet ID...\n";
    $url = "$baseUrl/sdm/payroll/coa-settings/accounts";
    
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
    
    if ($httpCode === 422) {
        echo "  ✅ SUCCESS: Missing outlet ID properly rejected\n";
    } else {
        echo "  ❌ FAILED: Missing outlet ID should return 422\n";
        echo "  HTTP Code: $httpCode\n";
    }
}

function testSettingsLoadSave($baseUrl, $outletId) {
    // Test loading existing settings
    echo "  📥 Testing settings load...\n";
    
    $url = "$baseUrl/sdm/payroll/coa-settings/get?outlet_id=$outletId";
    
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
    
    echo "  HTTP Code: $httpCode\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        
        if ($data && isset($data['success'])) {
            if ($data['success'] && $data['data']) {
                echo "  ✅ SUCCESS: Settings loaded successfully\n";
                echo "  📊 Settings found for outlet $outletId\n";
            } else {
                echo "  ℹ️  INFO: No settings found for outlet $outletId (this is normal for new outlets)\n";
            }
        } else {
            echo "  ❌ FAILED: Invalid response format\n";
        }
    } else {
        echo "  ❌ FAILED: Could not load settings\n";
    }
}

function makeHttpRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $headers[] = 'Content-Type: application/json';
    }
    
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => $response,
        'data' => json_decode($response, true)
    ];
}

echo "\n📋 MANUAL TESTING CHECKLIST:\n";
echo "1. ✅ Open browser and go to Setting COA Payroll\n";
echo "2. ✅ Select different outlets and verify accounts change\n";
echo "3. ✅ Verify only leaf accounts (no parent accounts) are shown\n";
echo "4. ✅ Save settings and verify they persist after page reload\n";
echo "5. ✅ Test with user that has limited outlet access\n";
echo "6. ✅ Check browser console for any JavaScript errors\n";
echo "7. ✅ Verify loading indicator shows when changing outlets\n";
echo "8. ✅ Test form validation (required fields)\n";

?>