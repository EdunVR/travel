<?php

require_once 'vendor/autoload.php';

echo "=== CHECKING ASSET ACCOUNTS BY OUTLET ===\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=demo', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check all outlets
    echo "1. Available outlets:\n";
    $stmt = $pdo->query("SELECT id_outlet, nama_outlet, kode_outlet FROM outlets ORDER BY id_outlet");
    $outlets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($outlets as $outlet) {
        echo "   Outlet {$outlet['id_outlet']}: {$outlet['nama_outlet']} ({$outlet['kode_outlet']})\n";
    }
    
    echo "\n2. Asset accounts by outlet:\n";
    
    foreach ($outlets as $outlet) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN parent_id IS NULL THEN 1 ELSE 0 END) as parents,
                   SUM(CASE WHEN parent_id IS NOT NULL THEN 1 ELSE 0 END) as children
            FROM chart_of_accounts 
            WHERE type = 'asset' 
            AND status = 'active' 
            AND outlet_id = ?
        ");
        $stmt->execute([$outlet['id_outlet']]);
        $counts = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "   Outlet {$outlet['id_outlet']} ({$outlet['nama_outlet']}): {$counts['total']} total ({$counts['parents']} parents, {$counts['children']} children)\n";
        
        if ($counts['total'] > 0) {
            // Show sample accounts for this outlet
            $stmt = $pdo->prepare("
                SELECT id, code, name, parent_id, level
                FROM chart_of_accounts 
                WHERE type = 'asset' 
                AND status = 'active' 
                AND outlet_id = ?
                ORDER BY code
                LIMIT 5
            ");
            $stmt->execute([$outlet['id_outlet']]);
            $sampleAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($sampleAccounts as $account) {
                $parentInfo = $account['parent_id'] ? "(Child of {$account['parent_id']})" : "(Parent)";
                echo "     - {$account['code']} - {$account['name']} {$parentInfo}\n";
            }
        }
    }
    
    echo "\n3. Finding outlet with most asset accounts:\n";
    $stmt = $pdo->query("
        SELECT outlet_id, COUNT(*) as account_count
        FROM chart_of_accounts 
        WHERE type = 'asset' 
        AND status = 'active'
        GROUP BY outlet_id
        ORDER BY account_count DESC
        LIMIT 3
    ");
    $topOutlets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($topOutlets as $outlet) {
        echo "   Outlet {$outlet['outlet_id']}: {$outlet['account_count']} asset accounts\n";
    }
    
    // Use the outlet with most accounts for testing
    if (!empty($topOutlets)) {
        $testOutletId = $topOutlets[0]['outlet_id'];
        echo "\n4. Testing with outlet {$testOutletId} (has most accounts):\n";
        
        $stmt = $pdo->prepare("
            SELECT 
                id, 
                code, 
                name, 
                parent_id, 
                level,
                (SELECT COUNT(*) FROM chart_of_accounts c2 WHERE c2.parent_id = c1.id) as children_count
            FROM chart_of_accounts c1 
            WHERE type = 'asset' 
            AND status = 'active' 
            AND outlet_id = ?
            ORDER BY code
        ");
        $stmt->execute([$testOutletId]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "   Building hierarchical structure:\n";
        
        // Build hierarchical structure
        $parentAccounts = array_filter($accounts, function($account) {
            return $account['parent_id'] === null;
        });
        
        foreach ($parentAccounts as $parent) {
            $children = array_filter($accounts, function($account) use ($parent) {
                return $account['parent_id'] == $parent['id'];
            });
            
            if (count($children) > 0) {
                echo "   📁 {$parent['code']} - {$parent['name']} (PARENT - DISABLED, " . count($children) . " children)\n";
                
                foreach ($children as $child) {
                    echo "       📄 {$child['code']} - {$child['name']} (CHILD - SELECTABLE)\n";
                }
            } else {
                echo "   📄 {$parent['code']} - {$parent['name']} (NO CHILDREN - SELECTABLE)\n";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\nCheck completed!\n";