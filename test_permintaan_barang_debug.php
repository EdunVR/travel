<?php

echo "=== DEBUGGING PERMINTAAN BARANG EDIT ISSUE ===\n\n";

// Check if PermintaanBarang model exists and has data
echo "1. Checking Database Data:\n";

try {
    // Simple database check
    $pdo = new PDO("mysql:host=localhost;dbname=tofu", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'permintaan_barang'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'permintaan_barang' exists\n";
        
        // Check if there's data
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM permintaan_barang");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Found {$result['count']} records in permintaan_barang table\n";
        
        // Get sample data
        $stmt = $pdo->query("SELECT id, nomor_permintaan, judul, status FROM permintaan_barang LIMIT 3");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nSample records:\n";
        foreach ($records as $record) {
            echo "- ID: {$record['id']}, No: {$record['nomor_permintaan']}, Judul: {$record['judul']}, Status: {$record['status']}\n";
        }
        
    } else {
        echo "❌ Table 'permintaan_barang' not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n2. Checking Route Files:\n";

// Check if route file has correct routes
if (file_exists('routes/web.php')) {
    $webContent = file_get_contents('routes/web.php');
    
    if (strpos($webContent, 'PermintaanBarangController') !== false) {
        echo "✅ PermintaanBarangController routes found in web.php\n";
    } else {
        echo "❌ PermintaanBarangController routes not found in web.php\n";
    }
    
    if (strpos($webContent, "Route::put('/{id}'") !== false) {
        echo "✅ PUT route for update found\n";
    } else {
        echo "❌ PUT route for update not found\n";
    }
} else {
    echo "❌ routes/web.php not found\n";
}

echo "\n3. Checking Controller:\n";

if (file_exists('app/Http/Controllers/PermintaanBarangController.php')) {
    $controllerContent = file_get_contents('app/Http/Controllers/PermintaanBarangController.php');
    
    if (strpos($controllerContent, 'public function update') !== false) {
        echo "✅ Update method found in controller\n";
    } else {
        echo "❌ Update method not found in controller\n";
    }
    
    if (strpos($controllerContent, 'public function show') !== false) {
        echo "✅ Show method found in controller\n";
    } else {
        echo "❌ Show method not found in controller\n";
    }
} else {
    echo "❌ PermintaanBarangController.php not found\n";
}

echo "\n4. Common Issues and Solutions:\n";
echo "Issue: URL shows /admin/supply-chain/permintaan-barang without ID\n";
echo "Cause: form.id is undefined or null\n";
echo "Solutions:\n";
echo "1. Check if data loading is successful\n";
echo "2. Check if form.id is properly set in handleModalOpened\n";
echo "3. Check browser console for 'Form populated:' log\n";
echo "4. Verify selectedItem has valid ID when modal opens\n";

echo "\n5. Debug Steps:\n";
echo "1. Open browser console (F12)\n";
echo "2. Click edit button on any item\n";
echo "3. Look for these logs:\n";
echo "   - 'Edit modal opened with item:'\n";
echo "   - 'Edit data loaded:'\n";
echo "   - 'Form populated:'\n";
echo "4. Check if form.id has a value\n";
echo "5. Try to save and check 'Form data before submit:' log\n";

echo "\n6. Manual Test URLs:\n";
echo "Test these URLs manually in browser:\n";
echo "- GET: https://poshan.my.id/tofu/admin/supply-chain/permintaan-barang/1\n";
echo "- Should return JSON data for item with ID 1\n";

echo "\n=== NEXT ACTIONS ===\n";
echo "1. Clear all caches: php artisan cache:clear\n";
echo "2. Clear browser cache completely\n";
echo "3. Test edit modal with console open\n";
echo "4. Check if form.id is populated correctly\n";
echo "5. If form.id is null, check data loading process\n";