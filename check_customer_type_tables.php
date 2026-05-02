<?php
/**
 * Check Customer Type Tables Structure
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Checking Customer Type Tables\n";
echo "===============================\n\n";

// Cek semua tabel yang ada
echo "📋 1. Mencari tabel yang berkaitan dengan customer type...\n";
$tables = DB::select('SHOW TABLES');
$tableColumn = 'Tables_in_' . env('DB_DATABASE');

$relevantTables = [];
foreach ($tables as $table) {
    $tableName = $table->$tableColumn;
    if (stripos($tableName, 'tipe') !== false || 
        stripos($tableName, 'type') !== false || 
        stripos($tableName, 'customer') !== false ||
        stripos($tableName, 'produk') !== false) {
        $relevantTables[] = $tableName;
        echo "   - {$tableName}\n";
    }
}

// Cek struktur tabel yang relevan
foreach ($relevantTables as $tableName) {
    echo "\n📋 Struktur tabel: {$tableName}\n";
    try {
        $columns = DB::select("DESCRIBE {$tableName}");
        foreach ($columns as $column) {
            echo "   - {$column->Field} ({$column->Type})\n";
        }
        
        // Jika ini tabel tipe customer, tampilkan datanya
        if (stripos($tableName, 'tipe') !== false && stripos($tableName, 'customer') !== false) {
            echo "   Data:\n";
            $data = DB::table($tableName)->limit(5)->get();
            foreach ($data as $row) {
                echo "   - " . json_encode($row) . "\n";
            }
        }
        
        // Jika ini tabel produk_tipe, tampilkan beberapa data
        if ($tableName === 'produk_tipe') {
            echo "   Sample data (5 records):\n";
            $data = DB::table($tableName)->limit(5)->get();
            foreach ($data as $row) {
                echo "   - " . json_encode($row) . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n";
?>