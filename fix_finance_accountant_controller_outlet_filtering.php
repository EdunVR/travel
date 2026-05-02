<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\File;

echo "🔧 FIXING FINANCE ACCOUNTANT CONTROLLER OUTLET FILTERING ISSUES\n";
echo "=" . str_repeat("=", 70) . "\n\n";

$controllerPath = 'app/Http/Controllers/FinanceAccountantController.php';

if (!File::exists($controllerPath)) {
    echo "❌ FinanceAccountantController not found!\n";
    exit(1);
}

echo "📋 Reading FinanceAccountantController...\n";
$content = File::get($controllerPath);

echo "🔍 Analyzing outlet filtering usage...\n\n";

// Check if controller already uses HasOutletFilter trait
$usesOutletFilter = strpos($content, 'use HasOutletFilter') !== false;
$hasOutletFilterImport = strpos($content, 'use App\Traits\HasOutletFilter') !== false;

echo "📊 CURRENT STATUS:\n";
echo "✅ Uses HasOutletFilter trait: " . ($usesOutletFilter ? 'YES' : 'NO') . "\n";
echo "✅ Has trait import: " . ($hasOutletFilterImport ? 'YES' : 'NO') . "\n\n";

if (!$hasOutletFilterImport) {
    echo "🔧 Adding HasOutletFilter trait import...\n";
    
    // Find the last use statement and add the trait import
    $lines = explode("\n", $content);
    $lastUseIndex = -1;
    
    foreach ($lines as $index => $line) {
        if (strpos(trim($line), 'use ') === 0 && strpos($line, '\\') !== false) {
            $lastUseIndex = $index;
        }
    }
    
    if ($lastUseIndex !== -1) {
        array_splice($lines, $lastUseIndex + 1, 0, 'use App\Traits\HasOutletFilter;');
        $content = implode("\n", $lines);
        echo "   ✅ Added trait import\n";
    }
}

if (!$usesOutletFilter) {
    echo "🔧 Adding HasOutletFilter trait usage...\n";
    
    // Find the class declaration and add the trait
    $classPattern = '/class\s+FinanceAccountantController\s+extends\s+Controller\s*\{/';
    if (preg_match($classPattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        $insertPosition = $matches[0][1] + strlen($matches[0][0]);
        $traitUsage = "\n    use HasOutletFilter;\n";
        $content = substr_replace($content, $traitUsage, $insertPosition, 0);
        echo "   ✅ Added trait usage\n";
    }
}

echo "\n📋 CRITICAL METHODS TO REVIEW:\n";
echo "-" . str_repeat("-", 50) . "\n";

// Methods that likely need outlet filtering
$criticalMethods = [
    'getChartOfAccounts' => 'Chart of Accounts should be filtered by outlet',
    'getJournalEntries' => 'Journal entries should be filtered by outlet', 
    'getTrialBalance' => 'Trial balance should be filtered by outlet',
    'getPiutangData' => 'Piutang data should be filtered by outlet',
    'getHutangData' => 'Hutang data should be filtered by outlet',
    'getFixedAssets' => 'Fixed assets should be filtered by outlet',
    'getRabData' => 'RAB data should be filtered by outlet'
];

foreach ($criticalMethods as $method => $description) {
    if (strpos($content, "function $method") !== false) {
        echo "⚠️  $method: $description\n";
    }
}

echo "\n🎯 RECOMMENDED FIXES:\n";
echo "-" . str_repeat("-", 50) . "\n";

echo "1. ✅ HasOutletFilter trait: " . ($usesOutletFilter ? 'Already implemented' : 'Added') . "\n";
echo "2. 🔧 Review all ->get() calls to ensure outlet filtering\n";
echo "3. 🔧 Add outlet parameter validation in methods\n";
echo "4. 🔧 Use \$this->applyOutletFilter() for queries\n";
echo "5. 🔧 Validate outlet access with \$this->validateOutletAccess()\n\n";

// Save the updated content
if ($content !== File::get($controllerPath)) {
    echo "💾 Saving updated FinanceAccountantController...\n";
    File::put($controllerPath, $content);
    echo "✅ FinanceAccountantController updated successfully!\n\n";
} else {
    echo "ℹ️  No changes needed for FinanceAccountantController\n\n";
}

echo "🧪 TESTING RECOMMENDATIONS:\n";
echo "-" . str_repeat("-", 50) . "\n";
echo "1. Test chart of accounts with different user access levels\n";
echo "2. Test journal entries filtering by outlet\n";
echo "3. Test trial balance calculations per outlet\n";
echo "4. Test piutang/hutang data filtering\n";
echo "5. Test fixed assets filtering by outlet\n";
echo "6. Verify no unauthorized data is shown\n\n";

echo "📋 NEXT STEPS:\n";
echo "-" . str_repeat("-", 50) . "\n";
echo "1. ✅ HasOutletFilter trait added to FinanceAccountantController\n";
echo "2. 🔧 Manual review needed for specific methods\n";
echo "3. 🧪 Create comprehensive tests for outlet filtering\n";
echo "4. 🚀 Deploy and test in browser\n\n";

echo "⚠️  IMPORTANT NOTE:\n";
echo "FinanceAccountantController is complex with many financial calculations.\n";
echo "Each method needs careful review to ensure outlet filtering doesn't break\n";
echo "accounting logic. Most methods already receive \$outletId parameter,\n";
echo "which is good for outlet-specific operations.\n\n";

echo "🎉 FINANCE ACCOUNTANT CONTROLLER: TRAIT ADDED, MANUAL REVIEW NEEDED!\n";