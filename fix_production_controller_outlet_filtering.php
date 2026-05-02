<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\File;

echo "🔧 FIXING PRODUCTION CONTROLLER OUTLET FILTERING ISSUES\n";
echo "=" . str_repeat("=", 70) . "\n\n";

$controllerPath = 'app/Http/Controllers/ProductionController.php';

if (!File::exists($controllerPath)) {
    echo "❌ ProductionController not found!\n";
    exit(1);
}

echo "📋 Reading ProductionController...\n";
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
    $classPattern = '/class\s+ProductionController\s+extends\s+Controller\s*\{/';
    if (preg_match($classPattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        $insertPosition = $matches[0][1] + strlen($matches[0][0]);
        $traitUsage = "\n    use HasOutletFilter;\n";
        $content = substr_replace($content, $traitUsage, $insertPosition, 0);
        echo "   ✅ Added trait usage\n";
    }
}

echo "\n📋 IDENTIFYING SPECIFIC ISSUES:\n";
echo "-" . str_repeat("-", 50) . "\n";

// Look for specific problematic patterns
$issues = [];

// Check for Produk model usage without outlet filtering
if (preg_match_all('/Produk::[^;]+/', $content, $matches)) {
    foreach ($matches[0] as $match) {
        $issues[] = [
            'type' => 'Produk Model Usage',
            'code' => $match,
            'risk' => 'HIGH',
            'description' => 'Produk queries should be filtered by outlet'
        ];
    }
}

// Check for Bahan model usage without outlet filtering
if (preg_match_all('/Bahan::[^;]+/', $content, $matches)) {
    foreach ($matches[0] as $match) {
        $issues[] = [
            'type' => 'Bahan Model Usage', 
            'code' => $match,
            'risk' => 'HIGH',
            'description' => 'Bahan queries should be filtered by outlet'
        ];
    }
}

// Check for direct ->get() calls
if (preg_match_all('/->get\(\)[^;]*/', $content, $matches)) {
    foreach (array_slice($matches[0], 0, 5) as $match) { // Show first 5
        $issues[] = [
            'type' => 'Direct get() Call',
            'code' => $match,
            'risk' => 'MEDIUM',
            'description' => 'Direct get() calls should be reviewed for outlet filtering'
        ];
    }
}

echo "🚨 FOUND ISSUES: " . count($issues) . "\n\n";

foreach (array_slice($issues, 0, 10) as $index => $issue) {
    echo ($index + 1) . ". {$issue['type']} ({$issue['risk']} RISK)\n";
    echo "   Code: " . substr($issue['code'], 0, 80) . "...\n";
    echo "   Issue: {$issue['description']}\n\n";
}

if (count($issues) > 10) {
    echo "... and " . (count($issues) - 10) . " more issues\n\n";
}

echo "🔧 APPLYING AUTOMATIC FIXES:\n";
echo "-" . str_repeat("-", 50) . "\n";

$fixesApplied = 0;

// Fix 1: Add outlet filtering to production data queries
$oldPattern = 'public function data(Request $request)
    {
        $query = Production::with([\'produk\', \'outlet\', \'materials.bahan\']);';

$newPattern = 'public function data(Request $request)
    {
        $query = Production::with([\'produk\', \'outlet\', \'materials.bahan\']);
        
        // Apply outlet filtering
        $query = $this->applyOutletFilter($query, \'id_outlet\');';

if (strpos($content, $oldPattern) !== false) {
    $content = str_replace($oldPattern, $newPattern, $content);
    echo "1. ✅ Added outlet filtering to data() method\n";
    $fixesApplied++;
} else {
    echo "1. ⚠️  data() method pattern not found\n";
}

// Fix 2: Add outlet filtering to getProducts method
$oldPattern = 'public function getProducts(Request $request)
    {
        $query = Produk::with([\'satuan\', \'kategori\']);';

$newPattern = 'public function getProducts(Request $request)
    {
        $query = Produk::with([\'satuan\', \'kategori\']);
        
        // Apply outlet filtering
        $query = $this->applyOutletFilter($query, \'id_outlet\');';

if (strpos($content, $oldPattern) !== false) {
    $content = str_replace($oldPattern, $newPattern, $content);
    echo "2. ✅ Added outlet filtering to getProducts() method\n";
    $fixesApplied++;
} else {
    echo "2. ⚠️  getProducts() method pattern not found\n";
}

// Fix 3: Add outlet filtering to getBahan method
$oldPattern = 'public function getBahan(Request $request)
    {
        $query = Bahan::with([\'satuan\']);';

$newPattern = 'public function getBahan(Request $request)
    {
        $query = Bahan::with([\'satuan\']);
        
        // Apply outlet filtering
        $query = $this->applyOutletFilter($query, \'id_outlet\');';

if (strpos($content, $oldPattern) !== false) {
    $content = str_replace($oldPattern, $newPattern, $content);
    echo "3. ✅ Added outlet filtering to getBahan() method\n";
    $fixesApplied++;
} else {
    echo "3. ⚠️  getBahan() method pattern not found\n";
}

echo "\n";

// Save the updated content
if ($content !== File::get($controllerPath)) {
    echo "💾 Saving updated ProductionController...\n";
    File::put($controllerPath, $content);
    echo "✅ ProductionController updated successfully!\n\n";
} else {
    echo "ℹ️  No changes made to ProductionController\n\n";
}

echo "📊 SUMMARY:\n";
echo "-" . str_repeat("-", 40) . "\n";
echo "✅ HasOutletFilter trait: " . ($usesOutletFilter ? 'Already added' : 'Added') . "\n";
echo "✅ Automatic fixes applied: $fixesApplied\n";
echo "⚠️  Manual review needed: " . (count($issues) - $fixesApplied) . " issues\n\n";

echo "🧪 TESTING RECOMMENDATIONS:\n";
echo "-" . str_repeat("-", 50) . "\n";
echo "1. Test production listing with different user access levels\n";
echo "2. Test product selection in production forms\n";
echo "3. Test material (bahan) selection in production\n";
echo "4. Test production creation and editing\n";
echo "5. Verify no unauthorized data is shown\n\n";

echo "📋 MANUAL REVIEW NEEDED:\n";
echo "-" . str_repeat("-", 50) . "\n";
echo "1. Review all Produk:: model calls for outlet filtering\n";
echo "2. Review all Bahan:: model calls for outlet filtering\n";
echo "3. Review production calculations for outlet consistency\n";
echo "4. Review HPP calculations for outlet-specific data\n\n";

echo "🎉 PRODUCTION CONTROLLER: BASIC FIXES APPLIED!\n";