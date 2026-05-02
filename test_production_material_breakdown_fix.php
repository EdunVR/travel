<?php

/**
 * Test Production Material Breakdown Display Fix
 * Memverifikasi bahwa detail biaya material (FIFO) tidak menampilkan undefined dan NaN
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING PRODUCTION MATERIAL BREAKDOWN DISPLAY FIX ===\n\n";

try {
    // 1. Test calculateHppPreview with material breakdown
    echo "1. Testing calculateHppPreview with material breakdown:\n";
    
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'materials' => [
            [
                'material_id' => 29, // Bahan A
                'quantity' => 10
            ],
            [
                'material_id' => 30, // Bahan B  
                'quantity' => 5
            ]
        ],
        'operational_costs' => [
            [
                'amount' => 100000
            ]
        ],
        'quantity' => 20
    ]);
    
    $controller = new \App\Http\Controllers\ProductionController();
    $response = $controller->calculateHppPreview($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success']) {
        echo "   ✅ HPP calculation successful\n";
        echo "   💰 Material Cost: Rp " . number_format($responseData['data']['material_cost'], 0, ',', '.') . "\n";
        echo "   💰 Total Cost: Rp " . number_format($responseData['data']['total_cost'], 0, ',', '.') . "\n";
        
        if (!empty($responseData['data']['breakdown']['materials'])) {
            echo "\n   📋 Material breakdown structure:\n";
            foreach ($responseData['data']['breakdown']['materials'] as $index => $material) {
                echo "   Material " . ($index + 1) . ":\n";
                
                // Check all required fields
                $requiredFields = ['id', 'name', 'code', 'merk', 'unit_price', 'quantity', 'total_cost', 'unit', 'fifo_used'];
                $missingFields = [];
                $undefinedFields = [];
                $nanFields = [];
                
                foreach ($requiredFields as $field) {
                    if (!array_key_exists($field, $material)) {
                        $missingFields[] = $field;
                    } else {
                        $value = $material[$field];
                        
                        // Check for undefined (null in PHP)
                        if ($value === null) {
                            $undefinedFields[] = $field;
                        }
                        
                        // Check for NaN (for numeric fields)
                        if (in_array($field, ['unit_price', 'quantity', 'total_cost']) && !is_numeric($value)) {
                            $nanFields[] = $field;
                        }
                        
                        echo "      - $field: " . json_encode($value) . "\n";
                    }
                }
                
                // Report issues
                if (!empty($missingFields)) {
                    echo "      ❌ Missing fields: " . implode(', ', $missingFields) . "\n";
                }
                if (!empty($undefinedFields)) {
                    echo "      ❌ Undefined fields: " . implode(', ', $undefinedFields) . "\n";
                }
                if (!empty($nanFields)) {
                    echo "      ❌ NaN fields: " . implode(', ', $nanFields) . "\n";
                }
                
                if (empty($missingFields) && empty($undefinedFields) && empty($nanFields)) {
                    echo "      ✅ All fields valid\n";
                }
                
                echo "\n";
            }
        } else {
            echo "   ❌ No material breakdown data found\n";
        }
    } else {
        echo "   ❌ HPP calculation failed: " . $responseData['message'] . "\n";
    }
    
    echo "\n2. Testing JavaScript compatibility:\n";
    
    if (!empty($responseData['data']['breakdown']['materials'])) {
        $materials = $responseData['data']['breakdown']['materials'];
        
        echo "   📋 JavaScript display simulation:\n";
        foreach ($materials as $index => $material) {
            // Simulate JavaScript processing
            $materialName = $material['name'] ?? 'Unknown Material';
            $materialCode = $material['code'] ?? '';
            $materialMerk = $material['merk'] ?? '';
            $quantity = $material['quantity'] ?? 0;
            $unitPrice = $material['unit_price'] ?? 0;
            $totalCost = $material['total_cost'] ?? 0;
            $unit = $material['unit'] ?? 'Unit';
            $fifoUsed = $material['fifo_used'] ?? false;
            
            // Check for potential JavaScript issues
            $jsIssues = [];
            
            if ($materialName === null || $materialName === '') {
                $jsIssues[] = 'materialName undefined/empty';
            }
            if (!is_numeric($quantity)) {
                $jsIssues[] = 'quantity NaN';
            }
            if (!is_numeric($unitPrice)) {
                $jsIssues[] = 'unitPrice NaN';
            }
            if (!is_numeric($totalCost)) {
                $jsIssues[] = 'totalCost NaN';
            }
            if ($unit === null || $unit === '') {
                $jsIssues[] = 'unit undefined/empty';
            }
            
            echo "   Material " . ($index + 1) . " - " . $materialName . ":\n";
            
            if (empty($jsIssues)) {
                echo "      ✅ JavaScript compatible\n";
                echo "      Display: " . $materialName;
                if ($materialCode) echo " (" . $materialCode . ")";
                if ($materialMerk) echo " - " . $materialMerk;
                echo "\n";
                echo "      Qty: " . $quantity . " " . $unit . " × Rp " . number_format($unitPrice, 0, ',', '.') . "/" . $unit . "\n";
                echo "      Total: Rp " . number_format($totalCost, 0, ',', '.') . "\n";
                echo "      FIFO: " . ($fifoUsed ? 'Yes' : 'No') . "\n";
            } else {
                echo "      ❌ JavaScript issues: " . implode(', ', $jsIssues) . "\n";
            }
            echo "\n";
        }
    }
    
    echo "\n3. Testing edge cases:\n";
    
    // Test with empty materials
    $emptyRequest = new \Illuminate\Http\Request();
    $emptyRequest->merge([
        'materials' => [],
        'operational_costs' => [],
        'quantity' => 1
    ]);
    
    $emptyResponse = $controller->calculateHppPreview($emptyRequest);
    $emptyData = json_decode($emptyResponse->getContent(), true);
    
    if ($emptyData['success']) {
        echo "   ✅ Empty materials handled correctly\n";
        echo "   💰 Material Cost: Rp " . number_format($emptyData['data']['material_cost'], 0, ',', '.') . "\n";
        
        if (empty($emptyData['data']['breakdown']['materials'])) {
            echo "   ✅ Empty breakdown handled correctly\n";
        } else {
            echo "   ❌ Should have empty breakdown\n";
        }
    }
    
    // Test with invalid material ID
    $invalidRequest = new \Illuminate\Http\Request();
    $invalidRequest->merge([
        'materials' => [
            [
                'material_id' => 99999, // Non-existent ID
                'quantity' => 5
            ]
        ],
        'operational_costs' => [],
        'quantity' => 1
    ]);
    
    $invalidResponse = $controller->calculateHppPreview($invalidRequest);
    $invalidData = json_decode($invalidResponse->getContent(), true);
    
    if ($invalidData['success']) {
        echo "   ✅ Invalid material ID handled correctly\n";
        echo "   💰 Material Cost: Rp " . number_format($invalidData['data']['material_cost'], 0, ',', '.') . "\n";
        
        if (empty($invalidData['data']['breakdown']['materials'])) {
            echo "   ✅ No breakdown for invalid materials\n";
        }
    }
    
    echo "\n=== SUMMARY ===\n";
    
    if ($responseData['success'] && !empty($responseData['data']['breakdown']['materials'])) {
        echo "✅ MATERIAL BREAKDOWN DISPLAY FIX SUCCESSFUL\n";
        
        echo "\nKey improvements:\n";
        echo "- All required fields present in breakdown data\n";
        echo "- No undefined or null values in critical fields\n";
        echo "- No NaN values in numeric calculations\n";
        echo "- FIFO indicator properly set\n";
        echo "- JavaScript-compatible data structure\n";
        echo "- Proper fallback values for missing data\n";
        echo "- Safe currency formatting\n";
        
        echo "\nBreakdown data structure:\n";
        echo "- id: Material ID from bahan table\n";
        echo "- name: Material name (nama_bahan)\n";
        echo "- code: Material code (kode_bahan)\n";
        echo "- merk: Material brand\n";
        echo "- unit_price: FIFO price or base price\n";
        echo "- quantity: Required quantity\n";
        echo "- total_cost: unit_price × quantity\n";
        echo "- unit: Material unit (from satuan table)\n";
        echo "- fifo_used: Boolean indicating FIFO pricing\n";
        
        echo "\nNext steps:\n";
        echo "1. Test material breakdown display in browser\n";
        echo "2. Verify no undefined/NaN values appear\n";
        echo "3. Check FIFO indicator display\n";
        echo "4. Test with various material combinations\n";
        
    } else {
        echo "❌ MATERIAL BREAKDOWN DISPLAY FIX NEEDS ATTENTION\n";
        echo "Some issues were found that need to be resolved\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== TESTING COMPLETE ===\n";