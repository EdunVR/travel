<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🧹 FORCE CLEANING DUPLICATES\n";
echo "============================\n\n";

// Get duplicate production codes
$duplicates = DB::select("
    SELECT production_code, COUNT(*) as count 
    FROM productions 
    GROUP BY production_code 
    HAVING COUNT(*) > 1
");

foreach ($duplicates as $duplicate) {
    echo "🧹 Cleaning {$duplicate->production_code} ({$duplicate->count} records)...\n";
    
    // Get all records with this production code
    $records = DB::select("
        SELECT id, created_at 
        FROM productions 
        WHERE production_code = ? 
        ORDER BY created_at ASC
    ", [$duplicate->production_code]);
    
    // Keep the first one, delete the rest
    $keepFirst = true;
    foreach ($records as $record) {
        if ($keepFirst) {
            echo "   ✅ Keeping ID: {$record->id} (created: {$record->created_at})\n";
            $keepFirst = false;
        } else {
            echo "   🗑️ Deleting ID: {$record->id} (created: {$record->created_at})\n";
            
            // Delete related records
            DB::delete("DELETE FROM hpp_produk WHERE production_id = ?", [$record->id]);
            DB::delete("DELETE FROM production_materials WHERE production_id = ?", [$record->id]);
            DB::delete("DELETE FROM production_labor_costs WHERE production_id = ?", [$record->id]);
            DB::delete("DELETE FROM production_operational_costs WHERE production_id = ?", [$record->id]);
            DB::delete("DELETE FROM production_realizations WHERE production_id = ?", [$record->id]);
            
            // Delete the production record
            DB::delete("DELETE FROM productions WHERE id = ?", [$record->id]);
            echo "   ✅ Deleted successfully\n";
        }
    }
    echo "\n";
}

echo "✅ Cleanup completed\n";