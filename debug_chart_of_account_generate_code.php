<?php

/**
 * Debug Script: Chart of Account Generate Code
 * 
 * This script helps debug the account code generation logic
 */

require_once 'vendor/autoload.php';

use App\Models\ChartOfAccount;
use App\Models\Outlet;

echo "=== DEBUG CHART OF ACCOUNT GENERATE CODE ===\n\n";

// Test configuration
$testOutletId = 1; // Adjust to existing outlet ID

echo "1. Checking existing accounts in database...\n";
debugExistingAccounts($testOutletId);

echo "\n2. Testing generate code logic...\n";
testGenerateCodeLogic($testOutletId);

echo "\n3. Testing API endpoint...\n";
testApiEndpoint($testOutletId);

echo "\n=== DEBUG COMPLETED ===\n";

function debugExistingAccounts($outletId) {
    try {
        // Check if outlet exists
        $outlet = \App\Models\Outlet::find($outletId);
        if (!$outlet) {
            echo "  ❌ Outlet ID $outletId not found!\n";
            return;
        }
        
        echo "  ✅ Outlet: {$outlet->nama_outlet}\n";
        
        // Get all accounts for this outlet
        $accounts = ChartOfAccount::where('outlet_id', $outletId)->get();
        echo "  📊 Total accounts: " . $accounts->count() . "\n";
        
        // Group by type
        $types = ['asset', 'liability', 'equity', 'revenue', 'expense', 'other_income', 'other_expense'];
        
        foreach ($types as $type) {
            $typeAccounts = $accounts->where('type', $type);
            if ($typeAccounts->count() > 0) {
                echo "  📝 $type accounts (" . $typeAccounts->count() . "):\n";
                foreach ($typeAccounts->take(5) as $account) {
                    $parentInfo = $account->parent_id ? " (child of ID: {$account->parent_id})" : " (parent)";
                    echo "    - {$account->code} - {$account->name}{$parentInfo}\n";
                }
                if ($typeAccounts->count() > 5) {
                    echo "    ... and " . ($typeAccounts->count() - 5) . " more\n";
                }
            }
        }
        
        // Show parent accounts
        $parentAccounts = $accounts->whereNull('parent_id');
        echo "  👨‍👩‍👧‍👦 Parent accounts: " . $parentAccounts->count() . "\n";
        
        // Show child accounts
        $childAccounts = $accounts->whereNotNull('parent_id');
        echo "  👶 Child accounts: " . $childAccounts->count() . "\n";
        
    } catch (\Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
    }
}

function testGenerateCodeLogic($outletId) {
    try {
        // Test different account types
        $types = ['asset', 'liability', 'expense'];
        
        foreach ($types as $type) {
            echo "  🧪 Testing $type account generation...\n";
            
            // Get type prefix
            $prefixes = [
                'asset' => '1',
                'liability' => '2', 
                'equity' => '3',
                'revenue' => '4',
                'expense' => '5',
                'other_income' => '6',
                'other_expense' => '7'
            ];
            
            $typePrefix = $prefixes[$type] ?? '1';
            echo "    Type prefix: $typePrefix\n";
            
            // Find existing accounts of this type
            $accounts = ChartOfAccount::where('outlet_id', $outletId)
                ->where('type', $type)
                ->whereNull('parent_id')
                ->get();
            
            echo "    Existing accounts: " . $accounts->count() . "\n";
            
            if ($accounts->count() > 0) {
                echo "    Existing codes: " . $accounts->pluck('code')->implode(', ') . "\n";
                
                // Analyze existing codes
                $maxNumber = 0;
                foreach ($accounts as $account) {
                    if (strpos($account->code, $typePrefix) === 0) {
                        $numberPart = substr($account->code, strlen($typePrefix));
                        $firstPart = explode('.', $numberPart)[0];
                        $number = (int)$firstPart;
                        if ($number > $maxNumber) {
                            $maxNumber = $number;
                        }
                    }
                }
                
                $nextNumber = $maxNumber + 1;
                $suggestedCode = $typePrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                
                echo "    Max number found: $maxNumber\n";
                echo "    Next number: $nextNumber\n";
                echo "    Suggested code: $suggestedCode\n";
            } else {
                $suggestedCode = $typePrefix . '001';
                echo "    No existing accounts, suggested code: $suggestedCode\n";
            }
            
            echo "\n";
        }
        
    } catch (\Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
    }
}

function testApiEndpoint($outletId) {
    $baseUrl = 'http://localhost:8000'; // Adjust to your local URL
    
    $types = ['asset', 'liability', 'expense'];
    
    foreach ($types as $type) {
        echo "  🌐 Testing API for $type...\n";
        
        $url = "$baseUrl/finance/chart-of-accounts/generate-code?outlet_id=$outletId&type=$type";
        
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
        
        echo "    HTTP Code: $httpCode\n";
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if ($data && isset($data['success']) && $data['success']) {
                echo "    ✅ Generated code: " . ($data['data']['code'] ?? 'N/A') . "\n";
            } else {
                echo "    ❌ API Error: " . ($data['message'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "    ❌ HTTP Error: $httpCode\n";
            echo "    Response: " . substr($response, 0, 100) . "...\n";
        }
        
        echo "\n";
    }
}

echo "\n📋 DEBUGGING CHECKLIST:\n";
echo "1. ✅ Check if outlet exists and has accounts\n";
echo "2. ✅ Verify existing account codes and patterns\n";
echo "3. ✅ Test code generation logic manually\n";
echo "4. ✅ Test API endpoint responses\n";
echo "5. ✅ Check Laravel logs for detailed debugging info\n";
echo "6. ✅ Verify database constraints and unique indexes\n";

echo "\n🔍 COMMON ISSUES:\n";
echo "- Duplicate codes: Check if unique constraint exists on code+outlet_id\n";
echo "- Wrong prefixes: Verify type mapping in getTypePrefixForCode()\n";
echo "- Number extraction: Check if existing codes follow expected format\n";
echo "- Parent-child logic: Verify parent_id relationships\n";

?>