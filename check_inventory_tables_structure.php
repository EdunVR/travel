<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request instance
$request = Illuminate\Http\Request::capture();
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CHECKING INVENTORY TABLES STRUCTURE ===\n\n";

try {
    // Check harga_bahan table structure
    echo "1. HARGA_BAHAN TABLE STRUCTURE:\n";
    if (Schema::hasTable('harga_bahan')) {
        $columns = DB::select("DESCRIBE harga_bahan");
        foreach ($columns as $column) {
            echo "   {$column->Field} - {$column->Type} - {$column->Null} - {$column->Key} - {$column->Default}\n";
        }
        
        // Sample data
        echo "\n   Sample data:\n";
        $samples = DB::table('harga_bahan')->take(3)->get();
        foreach ($samples as $sample) {
            echo "   ID: {$sample->id}, Bahan: {$sample->id_bahan}, Stok: {$sample->stok}, Harga: {$sample->harga_beli}, Created: {$sample->created_at}\n";
        }
    } else {
        echo "   ❌ Table harga_bahan not found\n";
    }
    
    // Check hpp_produk table structure
    echo "\n2. HPP_PRODUK TABLE STRUCTURE:\n";
    if (Schema::hasTable('hpp_produk')) {
        $columns = DB::select("DESCRIBE hpp_produk");
        foreach ($columns as $column) {
            echo "   {$column->Field} - {$column->Type} - {$column->Null} - {$column->Key} - {$column->Default}\n";
        }
        
        // Sample data
        echo "\n   Sample data:\n";
        $samples = DB::table('hpp_produk')->take(3)->get();
        foreach ($samples as $sample) {
            echo "   ID: {$sample->id}, Produk: {$sample->id_produk}, Stok: {$sample->stok}, HPP: {$sample->hpp}, Created: {$sample->created_at}\n";
        }
    } else {
        echo "   ❌ Table hpp_produk not found\n";
    }
    
    // Check production_materials table
    echo "\n3. PRODUCTION_MATERIALS TABLE STRUCTURE:\n";
    if (Schema::hasTable('production_materials')) {
        $columns = DB::select("DESCRIBE production_materials");
        foreach ($columns as $column) {
            echo "   {$column->Field} - {$column->Type} - {$column->Null} - {$column->Key} - {$column->Default}\n";
        }
        
        // Sample data with production info
        echo "\n   Sample data with production info:\n";
        $samples = DB::table('production_materials')
            ->join('productions', 'production_materials.production_id', '=', 'productions.id')
            ->select('production_materials.*', 'productions.production_code')
            ->take(3)->get();
        foreach ($samples as $sample) {
            echo "   Production: {$sample->production_code}, Material: {$sample->material_id}, Type: {$sample->material_type}, Qty: {$sample->quantity_required}\n";
        }
    } else {
        echo "   ❌ Table production_materials not found\n";
    }
    
    // Check current FIFO logic
    echo "\n4. CURRENT FIFO LOGIC TEST:\n";
    echo "   Testing FIFO for bahan ID 1...\n";
    $fifoTest = DB::table('harga_bahan')
        ->where('id_bahan', 1)
        ->where('stok', '>', 0)
        ->orderBy('created_at', 'asc') // FIFO: oldest first
        ->get();
    
    if ($fifoTest->isNotEmpty()) {
        echo "   FIFO order for bahan ID 1:\n";
        foreach ($fifoTest as $batch) {
            echo "     Batch ID: {$batch->id}, Stok: {$batch->stok}, Harga: {$batch->harga_beli}, Date: {$batch->created_at}\n";
        }
    } else {
        echo "   No stock found for bahan ID 1\n";
    }
    
    echo "\n✅ INVENTORY TABLES STRUCTURE CHECK COMPLETED\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}