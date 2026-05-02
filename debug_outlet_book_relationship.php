<?php

$host = 'localhost';
$dbname = 'demo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUGGING OUTLET-BOOK RELATIONSHIP ===\n\n";
    
    // Test 1: Check outlets
    echo "1. Checking outlets...\n";
    $stmt = $pdo->query("SELECT id_outlet, nama_outlet FROM outlets ORDER BY id_outlet");
    $outlets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($outlets as $outlet) {
        echo "   Outlet ID: {$outlet['id_outlet']}, Name: {$outlet['nama_outlet']}\n";
    }
    
    // Test 2: Check accounting books
    echo "\n2. Checking accounting books...\n";
    $stmt = $pdo->query("SELECT id, name, status, start_date FROM accounting_books ORDER BY id");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($books as $book) {
        $status = $book['status'] === 'active' ? '✓ ACTIVE' : '✗ INACTIVE';
        echo "   Book ID: {$book['id']}, Name: {$book['name']}, Status: $status\n";
    }
    
    // Test 3: Check if there's outlet-book relationship
    echo "\n3. Checking outlet-book relationships...\n";
    
    // Check if accounting_books table has outlet_id column
    $stmt = $pdo->query("DESCRIBE accounting_books");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasOutletId = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'outlet_id') {
            $hasOutletId = true;
            break;
        }
    }
    
    if ($hasOutletId) {
        echo "   ✓ accounting_books has outlet_id column\n";
        
        // Check books per outlet
        foreach ($outlets as $outlet) {
            $stmt = $pdo->prepare("SELECT id, name, status FROM accounting_books WHERE outlet_id = ? AND status = 'active'");
            $stmt->execute([$outlet['id_outlet']]);
            $outletBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "   Outlet {$outlet['nama_outlet']} (ID: {$outlet['id_outlet']}):\n";
            if ($outletBooks) {
                foreach ($outletBooks as $book) {
                    echo "     - Book ID: {$book['id']}, Name: {$book['name']}\n";
                }
            } else {
                echo "     - No active books found\n";
            }
        }
    } else {
        echo "   ✗ accounting_books does NOT have outlet_id column\n";
        echo "   → Books are global, not outlet-specific\n";
    }
    
    // Test 4: Simulate controller logic
    echo "\n4. Simulating controller logic for each outlet...\n";
    
    foreach ($outlets as $outlet) {
        echo "   Testing outlet: {$outlet['nama_outlet']} (ID: {$outlet['id_outlet']})\n";
        
        if ($hasOutletId) {
            // Outlet-specific books
            $stmt = $pdo->prepare("SELECT id, name FROM accounting_books WHERE outlet_id = ? AND status = 'active' ORDER BY id");
            $stmt->execute([$outlet['id_outlet']]);
        } else {
            // Global books
            $stmt = $pdo->query("SELECT id, name FROM accounting_books WHERE status = 'active' ORDER BY id");
        }
        
        $availableBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $bookCount = count($availableBooks);
        
        echo "     Available books: $bookCount\n";
        
        if ($bookCount === 0) {
            echo "     ⚠ No books available - will show 'Pilih Tahun Buku'\n";
        } elseif ($bookCount === 1) {
            $book = $availableBooks[0];
            echo "     ✓ Single book: {$book['name']} (ID: {$book['id']}) - should auto-select\n";
        } else {
            echo "     ✓ Multiple books available:\n";
            foreach ($availableBooks as $book) {
                echo "       - {$book['name']} (ID: {$book['id']})\n";
            }
        }
        echo "\n";
    }
    
    // Test 5: Check current user's outlet (if possible)
    echo "5. Checking user-outlet relationship...\n";
    
    $stmt = $pdo->query("DESCRIBE users");
    $userColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasUserOutlet = false;
    
    foreach ($userColumns as $column) {
        if ($column['Field'] === 'outlet_id') {
            $hasUserOutlet = true;
            break;
        }
    }
    
    if ($hasUserOutlet) {
        echo "   ✓ users table has outlet_id column\n";
        
        $stmt = $pdo->query("SELECT id, name, outlet_id FROM users WHERE outlet_id IS NOT NULL LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($users as $user) {
            echo "   User: {$user['name']} → Outlet ID: {$user['outlet_id']}\n";
        }
    } else {
        echo "   ✗ users table does NOT have outlet_id column\n";
    }
    
    echo "\n=== DIAGNOSIS ===\n";
    
    if (!$hasOutletId) {
        echo "ISSUE FOUND: Books are global, not outlet-specific\n";
        echo "SOLUTION: Controller should filter books by outlet or implement outlet-book relationship\n";
    } else {
        echo "Books are outlet-specific - check controller filtering logic\n";
    }
    
    echo "\nRECOMMENDATIONS:\n";
    echo "1. Verify controller filters books by current user's outlet\n";
    echo "2. Check JavaScript gets correct book list for current outlet\n";
    echo "3. Ensure modal dropdown populated with outlet-specific books\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}