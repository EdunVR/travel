<?php

/**
 * Debug Operational Costs Frontend Issue
 * Check if the issue is in the frontend JavaScript handling
 */

echo "=== DEBUGGING OPERATIONAL COSTS FRONTEND ISSUE ===\n\n";

echo "ISSUE ANALYSIS:\n";
echo "- Backend (Controller): ✅ Operational costs loaded correctly in edit method\n";
echo "- Backend (HPP Calculation): ✅ HPP calculation includes operational costs\n";
echo "- Frontend (Edit Loading): ❓ Need to check if operational costs are populated in form\n";
echo "- Frontend (HPP Preview): ❓ Need to check if operational costs are collected for preview\n\n";

echo "POTENTIAL ISSUES:\n";
echo "1. loadOperationalCostsForEdit() function might not be populating form fields correctly\n";
echo "2. calculateHppPreview() function might not be collecting operational costs from form\n";
echo "3. Timing issue - HPP preview calculated before operational costs are loaded\n";
echo "4. Form field selectors might be incorrect\n\n";

echo "DEBUGGING STEPS:\n";
echo "1. Check browser console for JavaScript errors during edit\n";
echo "2. Inspect form fields after edit modal opens\n";
echo "3. Check if operational cost rows are created with correct values\n";
echo "4. Verify HPP preview request includes operational costs\n\n";

echo "FRONTEND CODE ANALYSIS:\n";

// Read the loadOperationalCostsForEdit function from the view file
$viewFile = 'resources/views/admin/produksi/produksi/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Extract the loadOperationalCostsForEdit function
    $pattern = '/async loadOperationalCostsForEdit\(.*?\n        },/s';
    if (preg_match($pattern, $content, $matches)) {
        echo "Found loadOperationalCostsForEdit function:\n";
        echo "```javascript\n";
        echo trim($matches[0]) . "\n";
        echo "```\n\n";
        
        // Check for potential issues
        $functionCode = $matches[0];
        
        $issues = [];
        
        if (strpos($functionCode, 'cost_type') === false) {
            $issues[] = "❌ Function doesn't handle cost_type field";
        } else {
            echo "✅ Function handles cost_type field\n";
        }
        
        if (strpos($functionCode, 'amount') === false) {
            $issues[] = "❌ Function doesn't handle amount field";
        } else {
            echo "✅ Function handles amount field\n";
        }
        
        if (strpos($functionCode, 'calculateHppPreview') === false) {
            $issues[] = "❌ Function doesn't trigger HPP preview calculation";
        } else {
            echo "✅ Function triggers HPP preview calculation\n";
        }
        
        if (strpos($functionCode, 'addOperationalCost') === false) {
            $issues[] = "❌ Function doesn't call addOperationalCost";
        } else {
            echo "✅ Function calls addOperationalCost\n";
        }
        
        if (!empty($issues)) {
            echo "\nPOTENTIAL ISSUES FOUND:\n";
            foreach ($issues as $issue) {
                echo "  $issue\n";
            }
        }
    } else {
        echo "❌ Could not find loadOperationalCostsForEdit function\n";
    }
} else {
    echo "❌ View file not found\n";
}

echo "\nRECOMMENDED FIXES:\n";
echo "1. Add console.log statements to loadOperationalCostsForEdit to debug\n";
echo "2. Check if operational cost form fields have correct name attributes\n";
echo "3. Verify calculateHppPreview collects operational costs from all rows\n";
echo "4. Increase delay before HPP calculation to ensure data is loaded\n";
echo "5. Add validation to ensure operational costs are properly formatted\n\n";

echo "MANUAL TESTING STEPS:\n";
echo "1. Open production edit modal\n";
echo "2. Open browser developer tools (F12)\n";
echo "3. Check Console tab for JavaScript errors\n";
echo "4. Check Network tab for HPP preview requests\n";
echo "5. Inspect operational cost form fields for correct values\n";
echo "6. Verify HPP preview shows operational costs\n\n";

echo "🎯 FRONTEND DEBUGGING GUIDE COMPLETE!\n";
echo "Use browser developer tools to identify the exact issue.\n";

?>