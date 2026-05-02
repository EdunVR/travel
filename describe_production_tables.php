<?php

/**
 * Describe Production Related Tables
 * Untuk memahami struktur tabel sebelum memperbaiki method
 */

echo "=== DESCRIBING PRODUCTION RELATED TABLES ===\n\n";

try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST', '127.0.0.1') . ';port=' . env('DB_PORT', '3306') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 1. Describe productions table
    echo "1. PRODUCTIONS TABLE STRUCTURE:\n";
    echo str_repeat('-', 50) . "\n";
    
    $stmt = $pdo->query("DESCRIBE productions");
    $productionsColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($productionsColumns as $column) {
        echo sprintf("%-25s %-15s %-10s %-10s %-15s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key'], 
            $column['Default'] ?? 'NULL',
            $column['Extra']
        );
    }
    
    // 2. Describe produk table
    echo "\n2. PRODUK TABLE STRUCTURE:\n";
    echo str_repeat('-', 50) . "\n";
    
    $stmt = $pdo->query("DESCRIBE produk");
    $produkColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($produkColumns as $column) {
        echo sprintf("%-25s %-15s %-10s %-10s %-15s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key'], 
            $column['Default'] ?? 'NULL',
            $column['Extra']
        );
    }
    
    // 3. Describe hpp_produk table
    echo "\n3. HPP_PRODUK TABLE STRUCTURE:\n";
    echo str_repeat('-', 50) . "\n";
    
    $stmt = $pdo->query("DESCRIBE hpp_produk");
    $hppProdukColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($hppProdukColumns as $column) {
        echo sprintf("%-25s %-15s %-10s %-10s %-15s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'], 
            $column['Key'], 
            $column['Default'] ?? 'NULL',
            $column['Extra']
        );
    }
    
    // 4. Check related tables
    echo "\n4. RELATED TABLES CHECK:\n";
    echo str_repeat('-', 50) . "\n";
    
    $relatedTables = [
        'production_materials',
        'production_labor_costs', 
        'production_operational_costs',
        'monthly_production_costs',
        'production_realizations'
    ];
    
    foreach ($relatedTables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            $exists = $stmt->fetch();
            if ($exists) {
                echo "✅ $table - EXISTS\n";
                
                // Show key columns
                $stmt = $pdo->query("DESCRIBE $table");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $keyColumns = array_filter($columns, function($col) {
                    return strpos($col['Field'], 'total') !== false || 
                           strpos($col['Field'], 'cost') !== false ||
                           strpos($col['Field'], 'biaya') !== false ||
                           strpos($col['Field'], 'harga') !== false;
                });
                
                if ($keyColumns) {
                    echo "   Key cost/total columns:\n";
                    foreach ($keyColumns as $col) {
                        echo "   - {$col['Field']} ({$col['Type']})\n";
                    }
                }
            } else {
                echo "❌ $table - NOT EXISTS\n";
            }
        } catch (Exception $e) {
            echo "⚠️  $table - ERROR: " . $e->getMessage() . "\n";
        }
    }
    
    // 5. Sample data analysis
    echo "\n5. SAMPLE DATA ANALYSIS:\n";
    echo str_repeat('-', 50) . "\n";
    
    // Check productions sample
    $stmt = $pdo->query("SELECT * FROM productions LIMIT 3");
    $sampleProductions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($sampleProductions) {
        echo "Productions sample columns:\n";
        echo "Available columns: " . implode(', ', array_keys($sampleProductions[0])) . "\n";
    } else {
        echo "No sample data in productions table\n";
    }
    
    // Check produk sample
    $stmt = $pdo->query("SELECT * FROM produk LIMIT 3");
    $sampleProduk = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($sampleProduk) {
        echo "\nProduk sample columns:\n";
        echo "Available columns: " . implode(', ', array_keys($sampleProduk[0])) . "\n";
    } else {
        echo "No sample data in produk table\n";
    }
    
    // Check hpp_produk sample
    $stmt = $pdo->query("SELECT * FROM hpp_produk LIMIT 3");
    $sampleHpp = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($sampleHpp) {
        echo "\nHpp_produk sample columns:\n";
        echo "Available columns: " . implode(', ', array_keys($sampleHpp[0])) . "\n";
    } else {
        echo "No sample data in hpp_produk table\n";
    }
    
    echo "\n=== ANALYSIS COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // Fallback: try to read from env file
    echo "\nTrying to read database config from .env...\n";
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        preg_match('/DB_DATABASE=(.+)/', $envContent, $dbMatches);
        preg_match('/DB_USERNAME=(.+)/', $envContent, $userMatches);
        
        if ($dbMatches && $userMatches) {
            echo "Database: " . trim($dbMatches[1]) . "\n";
            echo "Username: " . trim($userMatches[1]) . "\n";
            echo "Please check database connection manually\n";
        }
    }
}

// Helper function to load env
function env($key, $default = null) {
    static $env = null;
    
    if ($env === null) {
        $env = [];
        if (file_exists('.env')) {
            $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
                    list($key, $value) = explode('=', $line, 2);
                    $env[trim($key)] = trim($value, '"\'');
                }
            }
        }
    }
    
    return $env[$key] ?? $default;
}