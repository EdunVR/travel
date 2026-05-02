<?php

require_once 'vendor/autoload.php';

// Check production_materials table structure
echo "=== CHECKING PRODUCTION_MATERIALS TABLE STRUCTURE ===\n\n";

try {
    // Load Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Get database connection
    $pdo = DB::connection()->getPdo();
    
    echo "1. Checking production_materials table structure...\n";
    
    // Get table structure
    $stmt = $pdo->prepare("DESCRIBE production_materials");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Table columns:\n";
    foreach ($columns as $column) {
        $nullable = $column['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
        $default = $column['Default'] !== null ? "DEFAULT '{$column['Default']}'" : 'NO DEFAULT';
        echo "   - {$column['Field']}: {$column['Type']} {$nullable} {$default}\n";
    }
    
    echo "\n2. Checking for unit column specifically...\n";
    $unitColumn = array_filter($columns, function($col) {
        return $col['Field'] === 'unit';
    });
    
    if (!empty($unitColumn)) {
        $unit = array_values($unitColumn)[0];
        echo "   ✅ Unit column found:\n";
        echo "      - Type: {$unit['Type']}\n";
        echo "      - Nullable: {$unit['Null']}\n";
        echo "      - Default: " . ($unit['Default'] !== null ? $unit['Default'] : 'NO DEFAULT') . "\n";
        
        if ($unit['Null'] === 'NO' && $unit['Default'] === null) {
            echo "   ❌ PROBLEM: Unit column is NOT NULL but has NO DEFAULT value\n";
        }
    } else {
        echo "   ❌ Unit column not found\n";
    }
    
    echo "\n3. Checking current ProductionMaterial model...\n";
    
    // Check if ProductionMaterial model exists
    if (class_exists('App\Models\ProductionMaterial')) {
        echo "   ✅ ProductionMaterial model exists\n";
        
        $model = new App\Models\ProductionMaterial();
        $fillable = $model->getFillable();
        echo "   - Fillable fields: " . implode(', ', $fillable) . "\n";
        
        if (!in_array('unit', $fillable)) {
            echo "   ❌ 'unit' is not in fillable array\n";
        } else {
            echo "   ✅ 'unit' is in fillable array\n";
        }
    } else {
        echo "   ❌ ProductionMaterial model not found\n";
    }
    
    echo "\n4. Sample data from production_materials table...\n";
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM production_materials LIMIT 3");
        $stmt->execute();
        $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($samples)) {
            echo "   Sample records:\n";
            foreach ($samples as $i => $sample) {
                echo "   Record " . ($i + 1) . ":\n";
                foreach ($sample as $key => $value) {
                    echo "      - $key: " . ($value ?? 'NULL') . "\n";
                }
                echo "\n";
            }
        } else {
            echo "   No records found in production_materials table\n";
        }
    } catch (Exception $e) {
        echo "   Error reading sample data: " . $e->getMessage() . "\n";
    }
    
    echo "\n5. Recommended fixes...\n";
    
    if (!empty($unitColumn)) {
        $unit = array_values($unitColumn)[0];
        if ($unit['Null'] === 'NO' && $unit['Default'] === null) {
            echo "   🔧 OPTION 1: Add default value to unit column\n";
            echo "      ALTER TABLE production_materials MODIFY COLUMN unit VARCHAR(50) DEFAULT 'unit';\n";
            echo "\n";
            echo "   🔧 OPTION 2: Make unit column nullable\n";
            echo "      ALTER TABLE production_materials MODIFY COLUMN unit VARCHAR(50) NULL;\n";
            echo "\n";
            echo "   🔧 OPTION 3: Update ProductionController to provide unit value\n";
            echo "      - Get unit from bahan table when creating material record\n";
            echo "      - Add 'unit' field to the create array\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== CHECK COMPLETED ===\n";