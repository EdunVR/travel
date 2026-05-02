<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING SUPPLY CHAIN & SDM PERMISSIONS ===\n\n";

// Define all required permissions
$requiredPermissions = [
    // Supply Chain - Transfer Gudang
    'inventaris.transfer-gudang.view' => 'View Transfer Gudang',
    'inventaris.transfer-gudang.create' => 'Create Transfer Gudang',
    'inventaris.transfer-gudang.edit' => 'Edit Transfer Gudang',
    'inventaris.transfer-gudang.delete' => 'Delete Transfer Gudang',
    'inventaris.transfer-gudang.approve' => 'Approve Transfer Gudang',
    
    // SDM - Kepegawaian & Rekrutmen
    'hrm.karyawan.view' => 'View Kepegawaian',
    'hrm.karyawan.create' => 'Create Kepegawaian',
    'hrm.karyawan.edit' => 'Edit Kepegawaian',
    'hrm.karyawan.delete' => 'Delete Kepegawaian',
    
    // SDM - Payroll
    'hrm.payroll.view' => 'View Payroll',
    'hrm.payroll.create' => 'Create Payroll',
    'hrm.payroll.edit' => 'Edit Payroll',
    'hrm.payroll.delete' => 'Delete Payroll',
    'hrm.payroll.approve' => 'Approve Payroll',
    
    // SDM - Absensi
    'hrm.absensi.view' => 'View Absensi',
    'hrm.absensi.create' => 'Create Absensi',
    'hrm.absensi.edit' => 'Edit Absensi',
    'hrm.absensi.delete' => 'Delete Absensi',
    'hrm.absensi.approve' => 'Approve Absensi',
    
    // SDM - Kinerja
    'hrm.kinerja.view' => 'View Kinerja',
    'hrm.kinerja.create' => 'Create Kinerja',
    'hrm.kinerja.edit' => 'Edit Kinerja',
    'hrm.kinerja.delete' => 'Delete Kinerja',
    
    // SDM - Kontrak & Dokumen
    'hrm.kontrak.view' => 'View Kontrak & Dokumen',
    'hrm.kontrak.create' => 'Create Kontrak & Dokumen',
    'hrm.kontrak.edit' => 'Edit Kontrak & Dokumen',
    'hrm.kontrak.delete' => 'Delete Kontrak & Dokumen',
];

$missing = [];
$existing = [];

foreach ($requiredPermissions as $name => $description) {
    $permission = DB::table('permissions')->where('name', $name)->first();
    
    if ($permission) {
        $existing[] = $name;
        echo "✓ EXISTS: {$name}\n";
    } else {
        $missing[] = ['name' => $name, 'description' => $description];
        echo "✗ MISSING: {$name} - {$description}\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total Required: " . count($requiredPermissions) . "\n";
echo "Existing: " . count($existing) . "\n";
echo "Missing: " . count($missing) . "\n";

if (count($missing) > 0) {
    echo "\n=== MISSING PERMISSIONS ===\n";
    foreach ($missing as $perm) {
        echo "- {$perm['name']} ({$perm['description']})\n";
    }
}

echo "\n=== DONE ===\n";
