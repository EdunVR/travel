<?php

/**
 * Script to fix all outlet validation rules across the codebase
 * Changes 'exists:outlets,id_outlet' to use Rule::exists() method
 */

$files = [
    'app/Http/Controllers/FlightController.php',
    'app/Http/Controllers/AgenGerobakController.php',
    'app/Http/Controllers/Admin/KontraBonController.php',
    'app/Http/Controllers/BankReconciliationController.php',
    'app/Http/Controllers/CompanyBankAccountController.php',
    'app/Http/Controllers/CustomerTypeController.php',
    'app/Http/Controllers/GerobakController.php',
    'app/Http/Controllers/HotelController.php',
    'app/Http/Controllers/FinanceAccountantController.php',
    'app/Http/Controllers/BookingController.php',
    'app/Http/Controllers/PackageController.php',
];

$searchPattern = "'id_outlet' => 'required|exists:outlets,id_outlet'";
$replaceWith = "'id_outlet' => [\n                'required',\n                \\Illuminate\\Validation\\Rule::exists('outlets', 'id_outlet')\n            ]";

$searchPattern2 = "'id_outlet' => 'nullable|exists:outlets,id_outlet'";
$replaceWith2 = "'id_outlet' => [\n                'nullable',\n                \\Illuminate\\Validation\\Rule::exists('outlets', 'id_outlet')\n            ]";

$searchPattern3 = "'outlet_id' => 'required|exists:outlets,id_outlet'";
$replaceWith3 = "'outlet_id' => [\n                'required',\n                \\Illuminate\\Validation\\Rule::exists('outlets', 'id_outlet')\n            ]";

$searchPattern4 = "'outlet_id' => 'nullable|exists:outlets,id_outlet'";
$replaceWith4 = "'outlet_id' => [\n                'nullable',\n                \\Illuminate\\Validation\\Rule::exists('outlets', 'id_outlet')\n            ]";

$totalFixed = 0;

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($file);
    $originalContent = $content;
    
    // Replace all patterns
    $content = str_replace($searchPattern, $replaceWith, $content);
    $content = str_replace($searchPattern2, $replaceWith2, $content);
    $content = str_replace($searchPattern3, $replaceWith3, $content);
    $content = str_replace($searchPattern4, $replaceWith4, $content);
    
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        $totalFixed++;
        echo "✅ Fixed: $file\n";
    } else {
        echo "⏭️  No changes needed: $file\n";
    }
}

echo "\n";
echo "========================================\n";
echo "Total files fixed: $totalFixed\n";
echo "========================================\n";
echo "\nPlease run: php artisan cache:clear\n";
