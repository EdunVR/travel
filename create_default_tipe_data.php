<?php

/**
 * Script untuk membuat data default tipe customer dengan outlet
 * 
 * Jalankan dengan: php artisan tinker
 * Kemudian: include 'create_default_tipe_data.php';
 */

use App\Models\Tipe;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;

echo "=== Membuat Data Default Tipe Customer ===\n\n";

try {
    DB::beginTransaction();

    // Ambil outlet yang ada
    $outlets = Outlet::where('is_active', true)->get();
    
    if ($outlets->isEmpty()) {
        echo "⚠️  Tidak ada outlet aktif ditemukan. Membuat tipe global...\n";
        
        // Buat tipe global (tanpa outlet spesifik)
        $defaultTypes = [
            [
                'nama_tipe' => 'Member Regular',
                'keterangan' => 'Pelanggan biasa tanpa diskon khusus',
                'id_outlet' => null
            ],
            [
                'nama_tipe' => 'Member VIP',
                'keterangan' => 'Pelanggan VIP dengan diskon khusus',
                'id_outlet' => null
            ],
            [
                'nama_tipe' => 'Reseller',
                'keterangan' => 'Pelanggan reseller dengan harga khusus',
                'id_outlet' => null
            ]
        ];
        
        foreach ($defaultTypes as $typeData) {
            $existing = Tipe::where('nama_tipe', $typeData['nama_tipe'])->first();
            if (!$existing) {
                Tipe::create($typeData);
                echo "✓ Tipe '{$typeData['nama_tipe']}' berhasil dibuat (Global)\n";
            } else {
                echo "- Tipe '{$typeData['nama_tipe']}' sudah ada\n";
            }
        }
    } else {
        echo "Ditemukan " . $outlets->count() . " outlet aktif\n\n";
        
        // Buat tipe untuk setiap outlet
        foreach ($outlets as $outlet) {
            echo "Membuat tipe untuk outlet: {$outlet->nama_outlet}\n";
            
            $outletTypes = [
                [
                    'nama_tipe' => "Member Regular - {$outlet->nama_outlet}",
                    'keterangan' => "Pelanggan biasa untuk outlet {$outlet->nama_outlet}",
                    'id_outlet' => $outlet->id_outlet
                ],
                [
                    'nama_tipe' => "Member VIP - {$outlet->nama_outlet}",
                    'keterangan' => "Pelanggan VIP untuk outlet {$outlet->nama_outlet}",
                    'id_outlet' => $outlet->id_outlet
                ]
            ];
            
            foreach ($outletTypes as $typeData) {
                $existing = Tipe::where('nama_tipe', $typeData['nama_tipe'])->first();
                if (!$existing) {
                    Tipe::create($typeData);
                    echo "  ✓ Tipe '{$typeData['nama_tipe']}' berhasil dibuat\n";
                } else {
                    echo "  - Tipe '{$typeData['nama_tipe']}' sudah ada\n";
                }
            }
            echo "\n";
        }
        
        // Buat juga beberapa tipe global
        echo "Membuat tipe global (berlaku untuk semua outlet):\n";
        $globalTypes = [
            [
                'nama_tipe' => 'Reseller',
                'keterangan' => 'Pelanggan reseller dengan harga khusus (berlaku semua outlet)',
                'id_outlet' => null
            ],
            [
                'nama_tipe' => 'Karyawan',
                'keterangan' => 'Diskon khusus untuk karyawan (berlaku semua outlet)',
                'id_outlet' => null
            ]
        ];
        
        foreach ($globalTypes as $typeData) {
            $existing = Tipe::where('nama_tipe', $typeData['nama_tipe'])->first();
            if (!$existing) {
                Tipe::create($typeData);
                echo "  ✓ Tipe '{$typeData['nama_tipe']}' berhasil dibuat (Global)\n";
            } else {
                echo "  - Tipe '{$typeData['nama_tipe']}' sudah ada\n";
            }
        }
    }

    DB::commit();
    
    // Tampilkan statistik
    echo "\n=== Statistik Akhir ===\n";
    $totalTypes = Tipe::count();
    $globalTypes = Tipe::whereNull('id_outlet')->count();
    $outletTypes = Tipe::whereNotNull('id_outlet')->count();
    
    echo "Total tipe customer: {$totalTypes}\n";
    echo "Tipe global: {$globalTypes}\n";
    echo "Tipe per outlet: {$outletTypes}\n";
    
    echo "\n✅ Data default tipe customer berhasil dibuat!\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Selesai ===\n";