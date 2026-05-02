<?php

$host = 'localhost';
$dbname = 'demo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUGGING FIXED ASSET MODAL BOOKS ISSUE ===\n\n";
    
    // Test 1: Check what books are available for each outlet
    echo "1. Checking books availability by outlet\n";
    
    $outlets = [1 => 'PBU', 3 => 'Dahana'];
    
    foreach ($outlets as $outletId => $outletName) {
        echo "   Outlet: $outletName (ID: $outletId)\n";
        
        // All books for outlet
        $stmt = $pdo->prepare("SELECT id, name, status FROM accounting_books WHERE outlet_id = ? ORDER BY id");
        $stmt->execute([$outletId]);
        $allBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Active books for outlet
        $stmt = $pdo->prepare("SELECT id, name FROM accounting_books WHERE outlet_id = ? AND status = 'active' ORDER BY id");
        $stmt->execute([$outletId]);
        $activeBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "     All books: " . count($allBooks) . "\n";
        foreach ($allBooks as $book) {
            $status = $book['status'] === 'active' ? '✓ ACTIVE' : '✗ INACTIVE';
            echo "       - {$book['name']} (ID: {$book['id']}) - $status\n";
        }
        
        echo "     Active books (for modal): " . count($activeBooks) . "\n";
        foreach ($activeBooks as $book) {
            echo "       - {$book['name']} (ID: {$book['id']})\n";
        }
        
        if (count($activeBooks) === 0) {
            echo "     ⚠ WARNING: No active books found for modal dropdown!\n";
        }
        echo "\n";
    }
    
    // Test 2: Simulate controller logic
    echo "2. Simulating FixedAssetController logic\n";
    
    // Simulate session outlet detection
    $sessionScenarios = [
        ['outlet_id' => 1, 'name' => 'PBU'],
        ['outlet_id' => 3, 'name' => 'Dahana']
    ];
    
    foreach ($sessionScenarios as $scenario) {
        $outletId = $scenario['outlet_id'];
        $outletName = $scenario['name'];
        
        echo "   Scenario: Current outlet = $outletName (ID: $outletId)\n";
        
        // Controller logic: Get books for outlet
        $stmt = $pdo->prepare("SELECT id, name, status FROM accounting_books WHERE outlet_id = ?");
        $stmt->execute([$outletId]);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filter active books
        $booksActive = array_filter($books, function($book) {
            return $book['status'] === 'active';
        });
        
        echo "     \$books (all): " . count($books) . "\n";
        echo "     \$booksActive (modal): " . count($booksActive) . "\n";
        
        if (count($booksActive) === 0) {
            echo "     ❌ PROBLEM: \$booksActive is empty - modal will show no options!\n";
        } else {
            echo "     ✅ Modal will show " . count($booksActive) . " book(s)\n";
            foreach ($booksActive as $book) {
                echo "       - {$book['name']} (ID: {$book['id']})\n";
            }
        }
        echo "\n";
    }
    
    // Test 3: Check if there's a session/outlet detection issue
    echo "3. Checking potential outlet detection issues\n";
    
    // Check if there are any inactive books that should be active
    $stmt = $pdo->query("SELECT outlet_id, COUNT(*) as total, 
                                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count
                         FROM accounting_books 
                         GROUP BY outlet_id");
    $bookStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($bookStats as $stat) {
        $outletName = $stat['outlet_id'] == 1 ? 'PBU' : ($stat['outlet_id'] == 3 ? 'Dahana' : 'Unknown');
        echo "   Outlet $outletName (ID: {$stat['outlet_id']}): {$stat['active_count']}/{$stat['total']} books active\n";
        
        if ($stat['active_count'] == 0) {
            echo "     ❌ CRITICAL: No active books for this outlet!\n";
        }
    }
    
    // Test 4: Check if outlet detection is working
    echo "\n4. Testing outlet detection methods\n";
    
    echo "   Method 1: Check if users table has outlet_id\n";
    $stmt = $pdo->query("DESCRIBE users");
    $userColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasOutletId = false;
    
    foreach ($userColumns as $column) {
        if ($column['Field'] === 'outlet_id') {
            $hasOutletId = true;
            break;
        }
    }
    
    if ($hasOutletId) {
        echo "     ✅ users.outlet_id exists\n";
    } else {
        echo "     ❌ users.outlet_id does NOT exist - relies on session\n";
    }
    
    echo "   Method 2: Check session-based outlet detection\n";
    echo "     - HasOutletFilter trait should handle session detection\n";
    echo "     - getSelectedOutlet() should return valid outlet ID\n";
    echo "     - If session is empty, should default to first accessible outlet\n";
    
    // Test 5: Verify the fix should work
    echo "\n5. Expected behavior after proper outlet detection\n";
    
    foreach ($sessionScenarios as $scenario) {
        $outletId = $scenario['outlet_id'];
        $outletName = $scenario['name'];
        
        $stmt = $pdo->prepare("SELECT id, name FROM accounting_books WHERE outlet_id = ? AND status = 'active'");
        $stmt->execute([$outletId]);
        $activeBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "   $outletName outlet:\n";
        echo "     Modal dropdown should show: " . count($activeBooks) . " book(s)\n";
        
        if (count($activeBooks) === 1) {
            echo "     ✅ Should auto-select: {$activeBooks[0]['name']}\n";
        } elseif (count($activeBooks) > 1) {
            echo "     ✅ Should show dropdown with options\n";
        } else {
            echo "     ❌ Will show empty dropdown - need to fix book status\n";
        }
    }
    
    echo "\n=== DIAGNOSIS ===\n";
    echo "If modal is empty, possible causes:\n";
    echo "1. \$booksActive is empty due to outlet detection failure\n";
    echo "2. All books for current outlet are inactive\n";
    echo "3. Controller not passing \$booksActive to view\n";
    echo "4. JavaScript not targeting correct modal dropdown\n";
    echo "5. Session outlet context is wrong or missing\n";
    
    echo "\n=== RECOMMENDED FIXES ===\n";
    echo "1. Verify HasOutletFilter trait is working in controller\n";
    echo "2. Add debug logging to controller to check \$currentOutletId\n";
    echo "3. Add debug logging to check \$booksActive count\n";
    echo "4. Ensure all books have status = 'active'\n";
    echo "5. Test with different outlet contexts\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}