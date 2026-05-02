<?php
/**
 * Script untuk membuat permissions Edit dan Delete untuk semua modul
 * Run: php create_edit_delete_permissions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

$permissions = [
    // Finance Module
    ['name' => 'finance.rab.edit', 'display_name' => 'Edit RAB', 'module' => 'finance', 'menu' => 'rab', 'action' => 'edit'],
    ['name' => 'finance.rab.delete', 'display_name' => 'Delete RAB', 'module' => 'finance', 'menu' => 'rab', 'action' => 'delete'],
    ['name' => 'finance.biaya.edit', 'display_name' => 'Edit Biaya', 'module' => 'finance', 'menu' => 'biaya', 'action' => 'edit'],
    ['name' => 'finance.biaya.delete', 'display_name' => 'Delete Biaya', 'module' => 'finance', 'menu' => 'biaya', 'action' => 'delete'],
    ['name' => 'finance.jurnal.edit', 'display_name' => 'Edit Jurnal', 'module' => 'finance', 'menu' => 'jurnal', 'action' => 'edit'],
    ['name' => 'finance.jurnal.delete', 'display_name' => 'Delete Jurnal', 'module' => 'finance', 'menu' => 'jurnal', 'action' => 'delete'],
    ['name' => 'finance.rekonsiliasi.edit', 'display_name' => 'Edit Rekonsiliasi', 'module' => 'finance', 'menu' => 'rekonsiliasi', 'action' => 'edit'],
    ['name' => 'finance.rekonsiliasi.delete', 'display_name' => 'Delete Rekonsiliasi', 'module' => 'finance', 'menu' => 'rekonsiliasi', 'action' => 'delete'],
    ['name' => 'finance.akun.edit', 'display_name' => 'Edit Akun', 'module' => 'finance', 'menu' => 'akun', 'action' => 'edit'],
    ['name' => 'finance.akun.delete', 'display_name' => 'Delete Akun', 'module' => 'finance', 'menu' => 'akun', 'action' => 'delete'],
    ['name' => 'finance.aktiva.edit', 'display_name' => 'Edit Aktiva', 'module' => 'finance', 'menu' => 'aktiva', 'action' => 'edit'],
    ['name' => 'finance.aktiva.delete', 'display_name' => 'Delete Aktiva', 'module' => 'finance', 'menu' => 'aktiva', 'action' => 'delete'],
    ['name' => 'finance.buku.edit', 'display_name' => 'Edit Buku', 'module' => 'finance', 'menu' => 'buku', 'action' => 'edit'],
    ['name' => 'finance.buku.delete', 'display_name' => 'Delete Buku', 'module' => 'finance', 'menu' => 'buku', 'action' => 'delete'],
    ['name' => 'finance.saldo-awal.edit', 'display_name' => 'Edit Saldo Awal', 'module' => 'finance', 'menu' => 'saldo-awal', 'action' => 'edit'],
    ['name' => 'finance.saldo-awal.delete', 'display_name' => 'Delete Saldo Awal', 'module' => 'finance', 'menu' => 'saldo-awal', 'action' => 'delete'],
    
    // Sales Module
    ['name' => 'sales.invoice.edit', 'display_name' => 'Edit Invoice', 'module' => 'sales', 'menu' => 'invoice', 'action' => 'edit'],
    ['name' => 'sales.invoice.delete', 'display_name' => 'Delete Invoice', 'module' => 'sales', 'menu' => 'invoice', 'action' => 'delete'],
    
    // Procurement Module
    ['name' => 'procurement.purchase-order.edit', 'display_name' => 'Edit PO', 'module' => 'procurement', 'menu' => 'purchase-order', 'action' => 'edit'],
    ['name' => 'procurement.purchase-order.delete', 'display_name' => 'Delete PO', 'module' => 'procurement', 'menu' => 'purchase-order', 'action' => 'delete'],
    
    // Production Module
    ['name' => 'production.produksi.edit', 'display_name' => 'Edit Produksi', 'module' => 'production', 'menu' => 'produksi', 'action' => 'edit'],
    ['name' => 'production.produksi.delete', 'display_name' => 'Delete Produksi', 'module' => 'production', 'menu' => 'produksi', 'action' => 'delete'],
    
    // HRM Module
    ['name' => 'hrm.karyawan.edit', 'display_name' => 'Edit Karyawan', 'module' => 'hrm', 'menu' => 'karyawan', 'action' => 'edit'],
    ['name' => 'hrm.karyawan.delete', 'display_name' => 'Delete Karyawan', 'module' => 'hrm', 'menu' => 'karyawan', 'action' => 'delete'],
    ['name' => 'hrm.payroll.edit', 'display_name' => 'Edit Payroll', 'module' => 'hrm', 'menu' => 'payroll', 'action' => 'edit'],
    ['name' => 'hrm.payroll.delete', 'display_name' => 'Delete Payroll', 'module' => 'hrm', 'menu' => 'payroll', 'action' => 'delete'],
    ['name' => 'hrm.absensi.edit', 'display_name' => 'Edit Absensi', 'module' => 'hrm', 'menu' => 'absensi', 'action' => 'edit'],
    ['name' => 'hrm.absensi.delete', 'display_name' => 'Delete Absensi', 'module' => 'hrm', 'menu' => 'absensi', 'action' => 'delete'],
    ['name' => 'hrm.kinerja.edit', 'display_name' => 'Edit Kinerja', 'module' => 'hrm', 'menu' => 'kinerja', 'action' => 'edit'],
    ['name' => 'hrm.kinerja.delete', 'display_name' => 'Delete Kinerja', 'module' => 'hrm', 'menu' => 'kinerja', 'action' => 'delete'],
    ['name' => 'hrm.kontrak.edit', 'display_name' => 'Edit Kontrak', 'module' => 'hrm', 'menu' => 'kontrak', 'action' => 'edit'],
    ['name' => 'hrm.kontrak.delete', 'display_name' => 'Delete Kontrak', 'module' => 'hrm', 'menu' => 'kontrak', 'action' => 'delete'],
    
    // Service Module
    ['name' => 'service.invoice.edit', 'display_name' => 'Edit Service Invoice', 'module' => 'service', 'menu' => 'invoice', 'action' => 'edit'],
    ['name' => 'service.invoice.delete', 'display_name' => 'Delete Service Invoice', 'module' => 'service', 'menu' => 'invoice', 'action' => 'delete'],
    ['name' => 'service.mesin.edit', 'display_name' => 'Edit Mesin', 'module' => 'service', 'menu' => 'mesin', 'action' => 'edit'],
    ['name' => 'service.mesin.delete', 'display_name' => 'Delete Mesin', 'module' => 'service', 'menu' => 'mesin', 'action' => 'delete'],
    ['name' => 'service.ongkir.edit', 'display_name' => 'Edit Ongkir', 'module' => 'service', 'menu' => 'ongkir', 'action' => 'edit'],
    ['name' => 'service.ongkir.delete', 'display_name' => 'Delete Ongkir', 'module' => 'service', 'menu' => 'ongkir', 'action' => 'delete'],
    
    // CRM Module
    ['name' => 'crm.tipe.edit', 'display_name' => 'Edit Tipe Customer', 'module' => 'crm', 'menu' => 'tipe', 'action' => 'edit'],
    ['name' => 'crm.tipe.delete', 'display_name' => 'Delete Tipe Customer', 'module' => 'crm', 'menu' => 'tipe', 'action' => 'delete'],
    ['name' => 'crm.pelanggan.edit', 'display_name' => 'Edit Pelanggan', 'module' => 'crm', 'menu' => 'pelanggan', 'action' => 'edit'],
    ['name' => 'crm.pelanggan.delete', 'display_name' => 'Delete Pelanggan', 'module' => 'crm', 'menu' => 'pelanggan', 'action' => 'delete'],
    
    // Inventaris Module
    ['name' => 'inventaris.sparepart.edit', 'display_name' => 'Edit Sparepart', 'module' => 'inventaris', 'menu' => 'sparepart', 'action' => 'edit'],
    ['name' => 'inventaris.sparepart.delete', 'display_name' => 'Delete Sparepart', 'module' => 'inventaris', 'menu' => 'sparepart', 'action' => 'delete'],
    ['name' => 'inventaris.produk.edit', 'display_name' => 'Edit Produk', 'module' => 'inventaris', 'menu' => 'produk', 'action' => 'edit'],
    ['name' => 'inventaris.produk.delete', 'display_name' => 'Delete Produk', 'module' => 'inventaris', 'menu' => 'produk', 'action' => 'delete'],
    ['name' => 'inventaris.bahan.edit', 'display_name' => 'Edit Bahan', 'module' => 'inventaris', 'menu' => 'bahan', 'action' => 'edit'],
    ['name' => 'inventaris.bahan.delete', 'display_name' => 'Delete Bahan', 'module' => 'inventaris', 'menu' => 'bahan', 'action' => 'delete'],
];

echo "Creating Edit & Delete permissions...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$created = 0;
$existing = 0;

foreach ($permissions as $permData) {
    $permission = Permission::firstOrCreate(
        ['name' => $permData['name']],
        [
            'display_name' => $permData['display_name'],
            'module' => $permData['module'],
            'menu' => $permData['menu'],
            'action' => $permData['action'],
            'description' => $permData['display_name']
        ]
    );
    
    if ($permission->wasRecentlyCreated) {
        echo "✅ Created: {$permData['name']}\n";
        $created++;
    } else {
        echo "⏭️  Exists: {$permData['name']}\n";
        $existing++;
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Summary:\n";
echo "  Created: {$created}\n";
echo "  Existing: {$existing}\n";
echo "  Total: " . ($created + $existing) . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n✅ Done! Edit & Delete permissions are ready.\n";
echo "\nNext steps:\n";
echo "1. Clear cache: php artisan config:clear && php artisan view:clear\n";
echo "2. Add @hasPermission wrappers to Edit/Delete buttons in views\n";
echo "3. Test permission in Role Management modal\n";
