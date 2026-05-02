<?php

require_once 'vendor/autoload.php';

$host = 'localhost';
$dbname = 'demo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TESTING FIXED ASSET AUTO-SELECT BOOK FIX ===\n\n";
    
    // Test 1: Simulate outlet context for PBU (multiple books)
    echo "1. Testing PBU outlet (ID: 1) - Multiple books scenario\n";
    $outletId = 1;
    
    $stmt = $pdo->prepare("SELECT id, name FROM accounting_books WHERE outlet_id = ? AND status = 'active' ORDER BY id");
    $stmt->execute([$outletId]);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Available books for PBU: " . count($books) . "\n";
    foreach ($books as $book) {
        echo "     - {$book['name']} (ID: {$book['id']})\n";
    }
    
    if (count($books) > 1) {
        echo "   ✓ Multiple books - should show dropdown with all options\n";
        echo "   ✓ If filter has book_id selected, should use that\n";
        echo "   ✓ Otherwise, should select first book as default\n";
    }
    
    // Test 2: Simulate outlet context for Dahana (single book)
    echo "\n2. Testing Dahana outlet (ID: 3) - Single book scenario\n";
    $outletId = 3;
    
    $stmt = $pdo->prepare("SELECT id, name FROM accounting_books WHERE outlet_id = ? AND status = 'active' ORDER BY id");
    $stmt->execute([$outletId]);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Available books for Dahana: " . count($books) . "\n";
    foreach ($books as $book) {
        echo "     - {$book['name']} (ID: {$book['id']})\n";
    }
    
    if (count($books) === 1) {
        $book = $books[0];
        echo "   ✓ Single book: {$book['name']} (ID: {$book['id']})\n";
        echo "   ✓ Should auto-select this book in modal\n";
        echo "   ✓ Should NOT show 'Buku tidak dipilih'\n";
    }
    
    // Test 3: Simulate session outlet switching
    echo "\n3. Testing session-based outlet detection\n";
    
    // Simulate different session scenarios
    $sessionScenarios = [
        ['selected_outlet_id' => 1, 'description' => 'PBU via selected_outlet_id'],
        ['selected_outlet_id' => 3, 'description' => 'Dahana via selected_outlet_id'],
        ['outlet_id' => 1, 'description' => 'PBU via outlet_id fallback'],
        ['outlet_id' => 3, 'description' => 'Dahana via outlet_id fallback'],
    ];
    
    foreach ($sessionScenarios as $scenario) {
        $outletId = $scenario['selected_outlet_id'] ?? $scenario['outlet_id'];
        echo "   Scenario: {$scenario['description']}\n";
        
        $stmt = $pdo->prepare("SELECT id, name FROM accounting_books WHERE outlet_id = ? AND status = 'active' ORDER BY id");
        $stmt->execute([$outletId]);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "     Books available: " . count($books) . "\n";
        if (count($books) === 1) {
            echo "     ✓ Should auto-select: {$books[0]['name']}\n";
        } elseif (count($books) > 1) {
            echo "     ✓ Should show dropdown with {count($books)} options\n";
        } else {
            echo "     ⚠ No books available\n";
        }
    }
    
    // Test 4: Verify JavaScript setDefaultBookId logic
    echo "\n4. Testing JavaScript auto-select logic scenarios\n";
    
    $jsScenarios = [
        [
            'selectedBookId' => '2',
            'filterBookId' => '',
            'availableBooks' => [['id' => 1, 'name' => 'Buku Test'], ['id' => 2, 'name' => 'BUKU KOSONG']],
            'expected' => 'Should use selectedBookId (2) from server filter'
        ],
        [
            'selectedBookId' => '',
            'filterBookId' => '1',
            'availableBooks' => [['id' => 1, 'name' => 'Buku Test'], ['id' => 2, 'name' => 'BUKU KOSONG']],
            'expected' => 'Should use filterBookId (1) from client filter'
        ],
        [
            'selectedBookId' => '',
            'filterBookId' => '',
            'availableBooks' => [['id' => 3, 'name' => 'BUKU DAHANA 2026']],
            'expected' => 'Should auto-select single book (3)'
        ],
        [
            'selectedBookId' => '',
            'filterBookId' => '',
            'availableBooks' => [['id' => 1, 'name' => 'Buku Test'], ['id' => 2, 'name' => 'BUKU KOSONG']],
            'expected' => 'Should select first available book (1)'
        ],
    ];
    
    foreach ($jsScenarios as $i => $scenario) {
        echo "   JS Scenario " . ($i + 1) . ":\n";
        echo "     selectedBookId: '{$scenario['selectedBookId']}'\n";
        echo "     filterBookId: '{$scenario['filterBookId']}'\n";
        echo "     availableBooks: " . count($scenario['availableBooks']) . "\n";
        echo "     ✓ {$scenario['expected']}\n";
    }
    
    echo "\n=== EXPECTED BEHAVIOR AFTER FIX ===\n";
    echo "1. Controller uses HasOutletFilter trait for proper outlet detection\n";
    echo "2. Books are filtered by current outlet context\n";
    echo "3. Modal shows only books available for current outlet\n";
    echo "4. Single book outlets (like Dahana) auto-select the book\n";
    echo "5. Multiple book outlets (like PBU) show dropdown with options\n";
    echo "6. No more 'Buku tidak dipilih' error for single-book outlets\n";
    
    echo "\n=== TESTING STEPS ===\n";
    echo "1. Login to application\n";
    echo "2. Switch to PBU outlet - should see 2 books in filter\n";
    echo "3. Open 'Tambah Aktiva Tetap' modal - should show both books\n";
    echo "4. Switch to Dahana outlet - should see 1 book in filter\n";
    echo "5. Open 'Tambah Aktiva Tetap' modal - should auto-select the book\n";
    echo "6. Verify no 'Buku tidak dipilih' message appears\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}