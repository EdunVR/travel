<?php

/**
 * Debug script for customer search functionality
 */

echo "🔍 Customer Search Debug Test\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Test the search-customers endpoint directly
echo "1. Testing search-customers API endpoint...\n";

$testSearchTerm = 'test';
$testOutletId = 1;

$url = "http://localhost/tofu/admin/service/search-customers?q={$testSearchTerm}&outlet_id={$testOutletId}";

echo "   📡 Testing URL: $url\n";

try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Accept: application/json',
                'User-Agent: Debug Script'
            ]
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        
        echo "   ✅ API Response received\n";
        echo "   📊 Response structure:\n";
        echo "   - success: " . (isset($data['success']) ? ($data['success'] ? 'true' : 'false') : 'missing') . "\n";
        echo "   - customers: " . (isset($data['customers']) ? 'array(' . count($data['customers']) . ')' : 'missing') . "\n";
        echo "   - count: " . (isset($data['count']) ? $data['count'] : 'missing') . "\n";
        
        if (isset($data['customers']) && count($data['customers']) > 0) {
            echo "\n   📋 First customer example:\n";
            $firstCustomer = $data['customers'][0];
            foreach ($firstCustomer as $key => $value) {
                $displayValue = is_array($value) ? 'array(' . count($value) . ')' : $value;
                echo "     - $key: $displayValue\n";
            }
            
            // Show what the JavaScript would create
            echo "\n   🔧 JavaScript transformation would create:\n";
            echo "     - id: " . $firstCustomer['id'] . "\n";
            echo "     - text: " . $firstCustomer['nama'] . " - " . ($firstCustomer['telepon'] ?? 'No Phone') . "\n";
            echo "     - nama: " . $firstCustomer['nama'] . "\n";
            echo "     - telepon: " . ($firstCustomer['telepon'] ?? 'No Phone') . "\n";
        }
        
    } else {
        echo "   ❌ Failed to get response from API\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing API: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check if there are any customers in the database
echo "2. Checking customer data in database...\n";

try {
    // This would need Laravel bootstrap, so just show the concept
    echo "   📝 To check manually, run this SQL query:\n";
    echo "   SELECT id, nama, telepon, id_outlet FROM members WHERE id_outlet = 1 LIMIT 5;\n";
    echo "\n   💡 Make sure there are customers in the members table for outlet 1\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: JavaScript debugging tips
echo "3. JavaScript debugging tips:\n";
echo "   🔧 Open browser console and check for:\n";
echo "   - 'Customer search response:' log messages\n";
echo "   - 'Customer auto-selected:' log messages\n";
echo "   - 'Customer not found in results, clearing ID' messages\n";
echo "   - Network tab for API requests/responses\n";

echo "\n";

echo "4. Common issues and solutions:\n";
echo "   ❌ Issue: Customer search returns empty results\n";
echo "   ✅ Solution: Check if members table has data for the outlet\n";
echo "\n";
echo "   ❌ Issue: Customer ID not set when selecting from datalist\n";
echo "   ✅ Solution: Make sure the text matches exactly with datalist option\n";
echo "\n";
echo "   ❌ Issue: 'Pilih customer terlebih dahulu' error on submit\n";
echo "   ✅ Solution: Check console logs to see if customer ID is being set\n";

echo "\n";

echo "5. Manual testing steps:\n";
echo "   1. Open Mesin Customer page\n";
echo "   2. Click 'Tambah Mesin' button\n";
echo "   3. Type in customer search field\n";
echo "   4. Check browser console for 'Customer search response:' log\n";
echo "   5. Select a customer from the dropdown suggestions\n";
echo "   6. Check if green checkmark appears with customer ID\n";
echo "   7. Try to submit the form\n";

echo "\n✨ Debug script completed!\n";