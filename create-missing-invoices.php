<?php
/**
 * Script to create invoices for bookings that don't have one
 * Run this after fixing the AUTO_INCREMENT issues
 * 
 * Usage: php create-missing-invoices.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JamaahBooking;
use App\Services\InvoiceIntegrationService;

echo "=== Create Missing Invoices ===\n\n";

// Get bookings without invoice
$bookings = JamaahBooking::whereNull('id_invoice')
    ->with(['travelPackage', 'jamaah'])
    ->get();

if ($bookings->isEmpty()) {
    echo "✅ All bookings already have invoices!\n";
    exit(0);
}

echo "Found {$bookings->count()} bookings without invoice:\n\n";

$invoiceService = new InvoiceIntegrationService();
$success = 0;
$failed = 0;

foreach ($bookings as $booking) {
    echo "Processing booking: {$booking->booking_code}\n";
    echo "  - Package: {$booking->travelPackage->package_name}\n";
    echo "  - Jamaah: {$booking->jamaah->nama}\n";
    echo "  - Total: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
    
    try {
        $paymentTerm = $booking->payment_type === 'full' ? 'full' : 'installment';
        $invoice = $invoiceService->createInvoiceForJamaah($booking, $paymentTerm, $booking->paid_amount ?? 0);
        
        echo "  ✅ Invoice created: {$invoice->no_invoice}\n";
        $success++;
    } catch (\Exception $e) {
        echo "  ❌ Failed: {$e->getMessage()}\n";
        $failed++;
    }
    
    echo "\n";
}

echo "=== Summary ===\n";
echo "Success: {$success}\n";
echo "Failed: {$failed}\n";
echo "Total: {$bookings->count()}\n";
