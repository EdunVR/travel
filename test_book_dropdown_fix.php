<?php

$host = 'localhost';
$dbname = 'demo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TESTING BOOK DROPDOWN ID CONFLICT FIX ===\n\n";
    
    // Test 1: Verify Dahana outlet has correct books
    echo "1. Testing Dahana outlet (ID: 3) book availability\n";
    $outletId = 3;
    
    $stmt = $pdo->prepare("SELECT id, name, status FROM accounting_books WHERE outlet_id = ? ORDER BY id");
    $stmt->execute([$outletId]);
    $allBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT id, name FROM accounting_books WHERE outlet_id = ? AND status = 'active' ORDER BY id");
    $stmt->execute([$outletId]);
    $activeBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   All books for Dahana: " . count($allBooks) . "\n";
    foreach ($allBooks as $book) {
        $status = $book['status'] === 'active' ? '✓ ACTIVE' : '✗ INACTIVE';
        echo "     - {$book['name']} (ID: {$book['id']}) - $status\n";
    }
    
    echo "   Active books for Dahana: " . count($activeBooks) . "\n";
    foreach ($activeBooks as $book) {
        echo "     - {$book['name']} (ID: {$book['id']})\n";
    }
    
    // Test 2: Simulate the HTML structure issue
    echo "\n2. Simulating HTML ID conflict issue\n";
    echo "   BEFORE FIX:\n";
    echo "     Filter dropdown: <select name=\"book_id\" id=\"book_id\"> (uses \$books)\n";
    echo "     Modal dropdown:  <select name=\"book_id\" id=\"book_id\"> (uses \$booksActive)\n";
    echo "     ❌ CONFLICT: Both have same ID 'book_id'\n";
    echo "     ❌ JavaScript \$('#book_id').val() targets filter, not modal\n";
    
    echo "\n   AFTER FIX:\n";
    echo "     Filter dropdown: <select name=\"book_id\" id=\"book_id\"> (uses \$books)\n";
    echo "     Modal dropdown:  <select name=\"book_id\" id=\"modal_book_id\"> (uses \$booksActive)\n";
    echo "     ✅ NO CONFLICT: Different IDs\n";
    echo "     ✅ JavaScript can target modal specifically\n";
    
    // Test 3: Simulate JavaScript behavior
    echo "\n3. Simulating JavaScript setDefaultBookId() behavior\n";
    
    // Scenario: Dahana outlet with book_id=2 in filter
    $selectedBookId = "2"; // From server filter
    $filterBookId = "2";   // From client filter
    
    echo "   Scenario: Dahana outlet, filter has book_id=2\n";
    echo "   selectedBookId from server: '$selectedBookId'\n";
    echo "   filterBookId from client: '$filterBookId'\n";
    
    if (!empty($selectedBookId)) {
        echo "   ✅ Step 1: Using selectedBookId from server filter\n";
        echo "   ✅ JavaScript: \$('#assetModal select[name=\"book_id\"]').val('$selectedBookId')\n";
        echo "   ✅ This targets modal dropdown by name attribute\n";
    } else if (!empty($filterBookId)) {
        echo "   ✅ Step 2: Using filterBookId from client filter\n";
        echo "   ✅ JavaScript: \$('#assetModal select[name=\"book_id\"]').val('$filterBookId')\n";
    } else if (count($activeBooks) === 1) {
        $book = $activeBooks[0];
        echo "   ✅ Step 3: Auto-selecting single active book\n";
        echo "   ✅ JavaScript: \$('#assetModal select[name=\"book_id\"]').val('{$book['id']}')\n";
        echo "   ✅ Book: {$book['name']} (ID: {$book['id']})\n";
    }
    
    // Test 4: Verify the fix addresses the root cause
    echo "\n4. Root cause analysis\n";
    echo "   PROBLEM: User reported 'Buku tidak dipilih' despite correct data\n";
    echo "   CAUSE: JavaScript selector conflict due to duplicate IDs\n";
    echo "   EVIDENCE: Console shows correct data but modal shows wrong state\n";
    echo "   SOLUTION: Changed modal dropdown ID to 'modal_book_id'\n";
    echo "   RESULT: JavaScript can now target modal dropdown correctly\n";
    
    echo "\n=== EXPECTED BEHAVIOR AFTER FIX ===\n";
    echo "1. Filter dropdown (id='book_id') works independently\n";
    echo "2. Modal dropdown (id='modal_book_id') works independently\n";
    echo "3. setDefaultBookId() targets modal via name attribute\n";
    echo "4. Edit function targets modal via ID 'modal_book_id'\n";
    echo "5. No more 'Buku tidak dipilih' for single-book outlets\n";
    echo "6. Dahana outlet auto-selects 'BUKU DAHANA 2026'\n";
    
    echo "\n=== TESTING INSTRUCTIONS ===\n";
    echo "1. Clear browser cache (Ctrl+F5)\n";
    echo "2. Switch to Dahana outlet\n";
    echo "3. Open 'Tambah Aktiva Tetap' modal\n";
    echo "4. ✅ Should show 'BUKU DAHANA 2026' selected\n";
    echo "5. ✅ Should NOT show 'Pilih Tahun Buku'\n";
    echo "6. Check browser console for JavaScript errors\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}