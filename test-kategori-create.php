<?php

/**
 * Test script untuk create kategori
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

echo "=== TEST KATEGORI CREATE ===\n\n";

try {
    // 1. Check model configuration
    echo "1. Checking model configuration...\n";
    $model = new Kategori;
    echo "   Primary Key: " . $model->getKeyName() . "\n";
    echo "   Incrementing: " . ($model->getIncrementing() ? 'YES' : 'NO') . "\n";
    echo "   Key Type: " . $model->getKeyType() . "\n";
    
    if (!$model->getIncrementing()) {
        echo "   ✗ ERROR: Model incrementing is FALSE!\n";
        exit(1);
    }
    echo "   ✓ Model configuration OK\n";
    
    // 2. Check database structure
    echo "\n2. Checking database structure...\n";
    $columns = DB::select("SHOW COLUMNS FROM kategori WHERE Field = 'id_kategori'");
    $column = $columns[0];
    echo "   Column: {$column->Field}\n";
    echo "   Type: {$column->Type}\n";
    echo "   Key: {$column->Key}\n";
    echo "   Extra: {$column->Extra}\n";
    
    if (stripos($column->Extra, 'auto_increment') === false) {
        echo "   ✗ ERROR: Column does not have AUTO_INCREMENT!\n";
        exit(1);
    }
    echo "   ✓ Database structure OK\n";
    
    // 3. Test create using Eloquent
    echo "\n3. Testing create using Eloquent...\n";
    
    $kategori = Kategori::create([
        'kode_kategori' => Kategori::generateKodeKategori(),
        'nama_kategori' => 'Test Category ' . time(),
        'kelompok' => 'Produk',
        'id_outlet' => 1,
        'deskripsi' => 'Test description',
        'is_active' => true
    ]);
    
    echo "   ✓ Create successful!\n";
    echo "   ID: {$kategori->id_kategori}\n";
    echo "   Kode: {$kategori->kode_kategori}\n";
    echo "   Nama: {$kategori->nama_kategori}\n";
    
    // 4. Verify in database
    echo "\n4. Verifying in database...\n";
    $check = DB::table('kategori')->where('id_kategori', $kategori->id_kategori)->first();
    
    if (!$check) {
        echo "   ✗ ERROR: Record not found in database!\n";
        exit(1);
    }
    
    echo "   ✓ Record found in database\n";
    echo "   ID: {$check->id_kategori}\n";
    echo "   Kode: {$check->kode_kategori}\n";
    echo "   Nama: {$check->nama_kategori}\n";
    
    // 5. Clean up
    echo "\n5. Cleaning up test data...\n";
    $kategori->delete();
    echo "   ✓ Test record deleted\n";
    
    echo "\n=== ALL TESTS PASSED ===\n";
    echo "\nKategori creation is working correctly!\n";
    echo "You can now create categories from the admin panel.\n";
    
} catch (\Exception $e) {
    echo "\n✗ TEST FAILED: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
