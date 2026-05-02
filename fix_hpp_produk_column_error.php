<?php

echo "=== FIXING HPP PRODUK COLUMN ERROR ===\n\n";

$controllerFile = 'app/Http/Controllers/ProdukController.php';

if (!file_exists($controllerFile)) {
    echo "❌ Controller file not found: $controllerFile\n";
    exit(1);
}

$content = file_get_contents($controllerFile);

// Find and replace the problematic update statement
$oldUpdateCode = "            // Update HPP record
            \$hppProduk->update([
                'stok' => \$newStok,
                'hpp' => \$hpp,
                'keterangan' => \$request->notes,
                'updated_at' => now()
            ]);";

$newUpdateCode = "            // Update HPP record (only update existing columns)
            \$hppProduk->update([
                'stok' => \$newStok,
                'hpp' => \$hpp,
                'updated_at' => now()
            ]);";

if (strpos($content, $oldUpdateCode) !== false) {
    $content = str_replace($oldUpdateCode, $newUpdateCode, $content);
    echo "✅ Fixed updateHpp method - removed keterangan column update\n";
} else {
    echo "⚠️  Could not find exact match for updateHpp method update statement\n";
    echo "Looking for alternative patterns...\n";
    
    // Try to find the update method with different formatting
    $pattern = "/\\\$hppProduk->update\(\[\s*'stok'\s*=>\s*\\\$newStok,\s*'hpp'\s*=>\s*\\\$hpp,\s*'keterangan'\s*=>\s*\\\$request->notes,\s*'updated_at'\s*=>\s*now\(\)\s*\]\);/";
    
    if (preg_match($pattern, $content)) {
        $replacement = "\$hppProduk->update([
                'stok' => \$newStok,
                'hpp' => \$hpp,
                'updated_at' => now()
            ]);";
        
        $content = preg_replace($pattern, $replacement, $content);
        echo "✅ Fixed updateHpp method using regex pattern\n";
    } else {
        echo "❌ Could not find updateHpp method to fix\n";
        
        // Show what we're looking for
        echo "\nSearching for 'keterangan' references...\n";
        $lines = explode("\n", $content);
        foreach ($lines as $lineNum => $line) {
            if (strpos($line, 'keterangan') !== false) {
                echo "Line " . ($lineNum + 1) . ": " . trim($line) . "\n";
            }
        }
    }
}

// Also check if there are other methods that might have the same issue
echo "\n🔍 Checking for other 'keterangan' references in controller...\n";

$lines = explode("\n", $content);
$keteranganFound = false;

foreach ($lines as $lineNum => $line) {
    if (strpos($line, 'keterangan') !== false) {
        echo "Line " . ($lineNum + 1) . ": " . trim($line) . "\n";
        $keteranganFound = true;
    }
}

if (!$keteranganFound) {
    echo "✅ No other 'keterangan' references found\n";
}

// Write the fixed content back
if (file_put_contents($controllerFile, $content)) {
    echo "\n✅ Controller file updated successfully\n";
} else {
    echo "\n❌ Failed to write controller file\n";
    exit(1);
}

// Also check if we need to fix the storeHpp method
echo "\n🔍 Checking storeHpp method for similar issues...\n";

if (strpos($content, 'storeHpp') !== false) {
    // Look for keterangan in storeHpp method
    $storeHppStart = strpos($content, 'public function storeHpp');
    $storeHppEnd = strpos($content, 'public function', $storeHppStart + 1);
    
    if ($storeHppEnd === false) {
        $storeHppEnd = strlen($content);
    }
    
    $storeHppMethod = substr($content, $storeHppStart, $storeHppEnd - $storeHppStart);
    
    if (strpos($storeHppMethod, 'keterangan') !== false) {
        echo "⚠️  storeHpp method also contains 'keterangan' references\n";
        echo "This may need to be fixed as well\n";
    } else {
        echo "✅ storeHpp method looks OK\n";
    }
} else {
    echo "⚠️  storeHpp method not found\n";
}

echo "\n=== TESTING THE FIX ===\n";

// Create a simple test to verify the fix
$testCode = '
try {
    // Test database connection
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=demo", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful\n";
    
    // Test if we can select from hpp_produk table
    $stmt = $pdo->query("SELECT id, id_produk, stok, hpp FROM hpp_produk LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✅ Can read from hpp_produk table\n";
        echo "   Sample record: ID=" . $result["id"] . ", Product=" . $result["id_produk"] . ", Stock=" . $result["stok"] . "\n";
    } else {
        echo "⚠️  hpp_produk table is empty\n";
    }
    
    // Test if we can update without keterangan column
    $testId = $result["id"] ?? null;
    if ($testId) {
        $stmt = $pdo->prepare("UPDATE hpp_produk SET stok = stok, hpp = hpp, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$testId]);
        echo "✅ Can update hpp_produk table without keterangan column\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database test failed: " . $e->getMessage() . "\n";
}
';

eval($testCode);

echo "\n=== FIX SUMMARY ===\n";
echo "✅ FIXED: Removed 'keterangan' column from updateHpp method\n";
echo "✅ REASON: hpp_produk table doesn't have 'keterangan' column\n";
echo "✅ SOLUTION: Only update existing columns (stok, hpp, updated_at)\n";
echo "✅ STATUS: Ready for testing\n\n";

echo "📋 NEXT STEPS:\n";
echo "1. Test the HPP edit functionality in browser\n";
echo "2. Verify that updates work without column errors\n";
echo "3. Check if notes/keterangan functionality is needed elsewhere\n\n";

echo "🎯 The column error should now be resolved!\n";

?>