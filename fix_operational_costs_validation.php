<?php

/**
 * Fix Operational Costs Validation and Storage
 * Supports both manual (cost_type) and auto-generated (description) operational costs
 */

echo "=== FIXING OPERATIONAL COSTS VALIDATION AND STORAGE ===\n\n";

$controllerFile = 'app/Http/Controllers/ProductionController.php';

if (!file_exists($controllerFile)) {
    echo "❌ ProductionController.php not found\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

// Fix validation rules for store method (first occurrence)
$oldValidation1 = "'operational_costs.*.cost_type' => 'required_with:operational_costs|string|max:255',";
$newValidation1 = "'operational_costs.*.cost_type' => 'nullable|string|max:255',
                'operational_costs.*.description' => 'nullable|string|max:255',";

// Fix validation rules for update method (second occurrence)
$oldValidation2 = $oldValidation1; // Same pattern
$newValidation2 = $newValidation1; // Same replacement

// Fix storage logic
$oldStorage = "if (!empty(\$cost['cost_type']) && !empty(\$cost['amount'])) {
                        ProductionOperationalCost::create([
                            'production_id' => \$production->id,
                            'cost_type' => \$cost['cost_type'],
                            'amount' => \$cost['amount'],
                        ]);
                    }";

$newStorage = "// Handle both manual (cost_type) and auto-generated (description) operational costs
                    \$costType = \$cost['cost_type'] ?? \$cost['description'] ?? '';
                    if (!empty(\$costType) && !empty(\$cost['amount'])) {
                        ProductionOperationalCost::create([
                            'production_id' => \$production->id,
                            'cost_type' => \$costType,
                            'amount' => \$cost['amount'],
                        ]);
                    }";

// Apply fixes
$fixes = 0;

// Fix first validation (store method)
if (strpos($content, $oldValidation1) !== false) {
    $content = preg_replace(
        '/' . preg_quote($oldValidation1, '/') . '/',
        $newValidation1,
        $content,
        1 // Only replace first occurrence
    );
    $fixes++;
    echo "✅ Fixed validation rules in store method\n";
}

// Fix second validation (update method) 
$pos = strpos($content, $oldValidation1); // Find second occurrence
if ($pos !== false) {
    $pos = strpos($content, $oldValidation1, $pos + 1);
    if ($pos !== false) {
        $before = substr($content, 0, $pos);
        $after = substr($content, $pos + strlen($oldValidation1));
        $content = $before . $newValidation1 . $after;
        $fixes++;
        echo "✅ Fixed validation rules in update method\n";
    }
}

// Fix storage logic
if (strpos($content, $oldStorage) !== false) {
    $content = str_replace($oldStorage, $newStorage, $content);
    $fixes++;
    echo "✅ Fixed storage logic to handle both cost_type and description\n";
}

// Write back to file
if ($fixes > 0) {
    file_put_contents($controllerFile, $content);
    echo "\n✅ Applied $fixes fixes to ProductionController.php\n";
} else {
    echo "\n⚠️ No fixes were applied - patterns may have already been updated\n";
}

echo "\n=== OPERATIONAL COSTS FIX COMPLETE ===\n";
echo "Now operational costs with both 'cost_type' (manual) and 'description' (auto-generated) will be saved properly.\n";

?>