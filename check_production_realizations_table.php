<?php

require_once 'vendor/autoload.php';

// Check production_realizations table structure
echo "=== CHECKING PRODUCTION_REALIZATIONS TABLE STRUCTURE ===\n\n";

try {
    // Load Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Get database connection
    $pdo = DB::connection()->getPdo();
    
    echo "1. Checking production_realizations table structure...\n";
    
    // Get table structure
    $stmt = $pdo->prepare("DESCRIBE production_realizations");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Table columns:\n";
    foreach ($columns as $column) {
        $nullable = $column['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
        $default = $column['Default'] !== null ? "DEFAULT '{$column['Default']}'" : 'NO DEFAULT';
        echo "   - {$column['Field']}: {$column['Type']} {$nullable} {$default}\n";
    }
    
    echo "\n2. Checking for material_cost column specifically...\n";
    $materialCostColumn = array_filter($columns, function($col) {
        return $col['Field'] === 'material_cost';
    });
    
    if (!empty($materialCostColumn)) {
        $materialCost = array_values($materialCostColumn)[0];
        echo "   ✅ material_cost column found:\n";
        echo "      - Type: {$materialCost['Type']}\n";
        echo "      - Nullable: {$materialCost['Null']}\n";
        echo "      - Default: " . ($materialCost['Default'] !== null ? $materialCost['Default'] : 'NO DEFAULT') . "\n";
    } else {
        echo "   ❌ material_cost column NOT found\n";
    }
    
    echo "\n3. Checking current ProductionRealization model...\n";
    
    // Check if ProductionRealization model exists
    if (class_exists('App\Models\ProductionRealization')) {
        echo "   ✅ ProductionRealization model exists\n";
        
        $model = new App\Models\ProductionRealization();
        $fillable = $model->getFillable();
        echo "   - Fillable fields: " . implode(', ', $fillable) . "\n";
        
        if (!in_array('material_cost', $fillable)) {
            echo "   ❌ 'material_cost' is not in fillable array\n";
        } else {
            echo "   ✅ 'material_cost' is in fillable array\n";
        }
    } else {
        echo "   ❌ ProductionRealization model not found\n";
    }
    
    echo "\n4. Sample data from production_realizations table...\n";
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM production_realizations LIMIT 3");
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
            echo "   No records found in production_realizations table\n";
        }
    } catch (Exception $e) {
        echo "   Error reading sample data: " . $e->getMessage() . "\n";
    }
    
    echo "\n5. Recommended fixes...\n";
    
    if (empty($materialCostColumn)) {
        echo "   🔧 OPTION 1: Remove material_cost from ProductionRealization::create()\n";
        echo "      - Update addMultiProductRealization() method\n";
        echo "      - Remove 'material_cost' => 0 from create array\n";
        echo "\n";
        echo "   🔧 OPTION 2: Add material_cost column to table\n";
        echo "      ALTER TABLE production_realizations ADD COLUMN material_cost DECIMAL(15,2) DEFAULT 0.00;\n";
        echo "\n";
        echo "   🔧 OPTION 3: Make material_cost nullable\n";
        echo "      ALTER TABLE production_realizations ADD COLUMN material_cost DECIMAL(15,2) NULL;\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== CHECK COMPLETED ===\n";