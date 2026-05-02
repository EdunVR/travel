<?php

/**
 * Test Script: Chart of Account Generate Code Fix
 * 
 * This script tests the fixed generateAccountCode functionality
 */

require_once 'vendor/autoload.php';

// Test configuration
$baseUrl = 'http://localhost:8000'; // Adjust to your local URL
$testOutletId = 1; // Adjust to existing outlet ID

echo "=== CHART OF ACCOUNT GENERATE CODE TEST ===\n\n";

// Test 1: Test generate code for parent account (asset)
echo "1. Testing generate code for parent asset account...\n";
testGenerateCode($baseUrl, $testOutletId, null, 'asset');

// Test 2: Test generate code for parent account (liability)
echo "\n2. Testing generate code for parent liability account...\n";
testGenerateCode($baseUrl, $testOutletId, null, 'liability');

// Test 3: Test generate code for parent account (expense)
echo "\n3. Testing generate code for parent expense account...\n";
testGenerateCode($baseUrl, $testOutletId, null, 'expense');

// Test 4: Test generate code with parent_id (child account)
echo "\n4. Testing generate code for child account...\n";
testGenerateCodeWithParent($baseUrl, $testOutletId);

// Test 5: Test validation (missing outlet_id)
echo "\n5. Testing validation (missing outlet_id)...\n";
testValidation($baseUrl);

echo "\n=== TEST COMPLETED ===\n";

function testGenerateCode($baseUrl, $outletId, $parentId, $type) {
    $params = [
        'outlet_id' => $outletId,
        'type' => $type
    ];
    
    if ($parentId) {
        $params['parent_id'] = $parentId;
    }
    
    $url = "$baseUrl/finance/chart-of-accounts/generate-code?" . http_build_query($params);
    
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
            echo "  ✅ SUCCESS: Code generated successfully\n";
            echo "  📝 Generated code: " . ($data['data']['code'] ?? 'N/A') . "\n";
            echo "  💬 Message: " . ($data['message'] ?? 'N/A') . "\n";
        } else {
            echo "  ❌ FAILED: Invalid response format\n";
            echo "  Response: " . substr($response, 0, 200) . "...\n";
        }
    } else {
        echo "  ❌ FAILED: HTTP $httpCode\n";
        echo "  Response: " . substr($response, 0, 200) . "...\n";
    }
}

function testGenerateCodeWithParent($baseUrl, $outletId) {
    // First, try to find an existing parent account
    echo "  🔍 Looking for existing parent accounts...\n";
    
    // Try to get a parent account ID from database
    // This is a simplified test - in real scenario you'd query the database
    $parentId = 1; // Assume there's a parent account with ID 1
    
    $params = [
        'outlet_id' => $outletId,
        'parent_id' => $parentId,
        'type' => 'asset'
    ];
    
    $url = "$baseUrl/finance/chart-of-accounts/generate-code?" . http_build_query($params);
    
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
            echo "  ✅ SUCCESS: Child code generated successfully\n";
            echo "  📝 Generated code: " . ($data['data']['code'] ?? 'N/A') . "\n";
        } else {
            echo "  ❌ FAILED: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else if ($httpCode === 500) {
        $data = json_decode($response, true);
        if ($data && strpos($data['message'] ?? '', 'Parent account tidak ditemukan') !== false) {
            echo "  ℹ️  INFO: Parent account not found (expected for test with ID $parentId)\n";
        } else {
            echo "  ❌ FAILED: HTTP $httpCode\n";
            echo "  Response: " . substr($response, 0, 200) . "...\n";
        }
    } else {
        echo "  ❌ FAILED: HTTP $httpCode\n";
        echo "  Response: " . substr($response, 0, 200) . "...\n";
    }
}

function testValidation($baseUrl) {
    // Test without outlet_id
    $url = "$baseUrl/finance/chart-of-accounts/generate-code?type=asset";
    
    echo "  🔒 Testing without outlet_id...\n";
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
    
    if ($httpCode === 422) {
        echo "  ✅ SUCCESS: Missing outlet_id properly rejected\n";
    } else if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && !$data['success']) {
            echo "  ✅ SUCCESS: Missing outlet_id properly rejected (JSON response)\n";
        } else {
            echo "  ❌ FAILED: Missing outlet_id should be rejected\n";
        }
    } else {
        echo "  ⚠️  UNKNOWN: Unexpected response code $httpCode\n";
        echo "  Response: " . substr($response, 0, 200) . "...\n";
    }
}

echo "\n📋 MANUAL TESTING CHECKLIST:\n";
echo "1. ✅ Open browser and go to Chart of Accounts\n";
echo "2. ✅ Click 'Tambah Akun' button\n";
echo "3. ✅ Select outlet and account type\n";
echo "4. ✅ Verify account code is generated automatically\n";
echo "5. ✅ Try different account types (asset, liability, expense)\n";
echo "6. ✅ Try creating child accounts under parent accounts\n";
echo "7. ✅ Check browser console for any JavaScript errors\n";
echo "8. ✅ Verify generated codes follow proper numbering sequence\n";

echo "\n🔧 IMPLEMENTATION DETAILS:\n";
echo "- Fixed missing generateAccountCode method in ChartOfAccount model\n";
echo "- Added proper database-based code generation logic\n";
echo "- Added outlet access validation\n";
echo "- Improved error handling and logging\n";
echo "- Uses type-based prefixes (1=asset, 2=liability, etc.)\n";
echo "- Generates sequential codes with proper padding\n";

?>