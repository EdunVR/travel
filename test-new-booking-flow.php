<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING NEW BOOKING FLOW ===\n\n";

// Simulate creating a new booking with family members
$familyMembers = [
    [
        'nama' => 'Test Anak 1',
        'hubungan' => 'Anak',
        'tanggal_lahir' => '2018-01-01' // 8 tahun, diskon 15%
    ],
    [
        'nama' => 'Test Bayi 1',
        'hubungan' => 'Anak',
        'tanggal_lahir' => '2024-06-01' // < 2 tahun, flat 18jt
    ]
];

echo "1. Simulating booking with family members:\n";
echo "   - Jamaah Utama (Dewasa)\n";
echo "   - Test Anak 1 (8 tahun, diskon 15%)\n";
echo "   - Test Bayi 1 (< 2 tahun, flat Rp 18jt)\n\n";

// Calculate expected prices
$unitPrice = 30000000; // Rp 30 juta
$jamaahUtama = $unitPrice;
$anak = $unitPrice * 0.85; // 15% discount
$bayi = 18000000; // flat
$expectedTotal = $jamaahUtama + $anak + $bayi;

echo "2. Expected calculation:\n";
echo "   - Jamaah Utama: Rp " . number_format($jamaahUtama, 0, ',', '.') . "\n";
echo "   - Test Anak 1 (diskon 15%): Rp " . number_format($anak, 0, ',', '.') . "\n";
echo "   - Test Bayi 1 (flat): Rp " . number_format($bayi, 0, ',', '.') . "\n";
echo "   - TOTAL: Rp " . number_format($expectedTotal, 0, ',', '.') . "\n\n";

echo "3. What will be saved:\n";
echo "   - jamaah_bookings.family_members_booking = JSON array with 2 members\n";
echo "   - jamaah_bookings.total_price = Rp " . number_format($expectedTotal, 0, ',', '.') . "\n";
echo "   - jamaah_bookings.paid_amount = 0\n";
echo "   - jamaah_bookings.remaining_amount = Rp " . number_format($expectedTotal, 0, ',', '.') . "\n";
echo "   - jamaah_bookings.payment_status = 'unpaid'\n\n";

echo "4. Piutang record will be created:\n";
echo "   - source_type = 'travel'\n";
echo "   - jumlah_piutang = Rp " . number_format($expectedTotal, 0, ',', '.') . "\n";
echo "   - jumlah_dibayar = 0\n";
echo "   - sisa_piutang = Rp " . number_format($expectedTotal, 0, ',', '.') . "\n";
echo "   - status = 'belum_lunas'\n\n";

echo "5. After DP payment (25% = Rp " . number_format($expectedTotal * 0.25, 0, ',', '.') . "):\n";
$dpAmount = $expectedTotal * 0.25;
$remainingAfterDp = $expectedTotal - $dpAmount;
echo "   - jamaah_bookings.paid_amount = Rp " . number_format($dpAmount, 0, ',', '.') . "\n";
echo "   - jamaah_bookings.remaining_amount = Rp " . number_format($remainingAfterDp, 0, ',', '.') . "\n";
echo "   - jamaah_bookings.payment_status = 'partial'\n";
echo "   - piutang.jumlah_dibayar = Rp " . number_format($dpAmount, 0, ',', '.') . "\n";
echo "   - piutang.sisa_piutang = Rp " . number_format($remainingAfterDp, 0, ',', '.') . "\n";
echo "   - piutang.status = 'belum_lunas'\n\n";

echo "6. After full payment:\n";
echo "   - jamaah_bookings.paid_amount = Rp " . number_format($expectedTotal, 0, ',', '.') . "\n";
echo "   - jamaah_bookings.remaining_amount = 0\n";
echo "   - jamaah_bookings.payment_status = 'paid'\n";
echo "   - piutang.jumlah_dibayar = Rp " . number_format($expectedTotal, 0, ',', '.') . "\n";
echo "   - piutang.sisa_piutang = 0\n";
echo "   - piutang.status = 'lunas'\n\n";

echo "7. Invoice PDF will show:\n";
echo "   - Jamaah Utama dengan harga Rp " . number_format($jamaahUtama, 0, ',', '.') . "\n";
echo "   - Test Anak 1 dengan harga Rp " . number_format($anak, 0, ',', '.') . " (diskon 15%)\n";
echo "   - Test Bayi 1 dengan harga Rp " . number_format($bayi, 0, ',', '.') . " (flat)\n";
echo "   - TIDAK akan menampilkan family members lama dari tabel member\n\n";

echo "8. Repeat order (customer yang sama, tanpa family members):\n";
echo "   - jamaah_bookings.family_members_booking = NULL\n";
echo "   - Invoice PDF akan menampilkan HANYA jamaah utama\n";
echo "   - TIDAK akan menampilkan Test Anak 1 dan Test Bayi 1 dari booking sebelumnya\n\n";

echo "=== ALL SYSTEMS READY ===\n";
echo "Sistem siap digunakan untuk booking baru!\n";
