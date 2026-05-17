<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PartnershipProgram;

echo "=== UPDATE PARTNERSHIP PROGRAMS ===\n\n";

// 1. Nonaktifkan HM Leader
echo "1. Menonaktifkan HM Leader...\n";
$hmLeader = PartnershipProgram::where('slug', 'hm-leader')->first();
if ($hmLeader) {
    $hmLeader->update(['is_active' => false]);
    echo "   ✓ HM Leader berhasil dinonaktifkan\n";
    echo "   - Name: {$hmLeader->name}\n";
    echo "   - Status: " . ($hmLeader->is_active ? 'Active' : 'Inactive') . "\n\n";
} else {
    echo "   ✗ HM Leader tidak ditemukan\n\n";
}

// 2. Update komisi HM Partner menjadi 1,5 juta
echo "2. Mengubah komisi HM Partner menjadi Rp 1.500.000...\n";
$hmPartner = PartnershipProgram::where('slug', 'hm-partner')->first();
if ($hmPartner) {
    $oldCommission = $hmPartner->min_sale_commission;
    $hmPartner->update(['min_sale_commission' => 1500000]);
    echo "   ✓ Komisi HM Partner berhasil diupdate\n";
    echo "   - Name: {$hmPartner->name}\n";
    echo "   - Old Commission: Rp " . number_format($oldCommission, 0, ',', '.') . "\n";
    echo "   - New Commission: Rp " . number_format($hmPartner->min_sale_commission, 0, ',', '.') . "\n\n";
} else {
    echo "   ✗ HM Partner tidak ditemukan\n\n";
}

// 3. Tampilkan semua program yang aktif
echo "3. Program Kemitraan yang Aktif:\n";
$activePrograms = PartnershipProgram::active()->ordered()->get();
foreach ($activePrograms as $program) {
    echo "   - {$program->name}\n";
    echo "     Slug: {$program->slug}\n";
    echo "     Komisi Closing: Rp " . number_format($program->min_sale_commission, 0, ',', '.') . "\n";
    echo "     Komisi PPC: Rp " . number_format($program->default_ppc_commission, 0, ',', '.') . "\n";
    echo "     Status: " . ($program->is_active ? 'Active' : 'Inactive') . "\n\n";
}

echo "=== SELESAI ===\n";
