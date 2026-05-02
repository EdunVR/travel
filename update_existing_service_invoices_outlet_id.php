<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\ServiceInvoice;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== UPDATE EXISTING SERVICE INVOICES WITH OUTLET_ID ===\n\n";

try {
    // Get all service invoices without outlet_id
    $invoicesWithoutOutlet = ServiceInvoice::whereNull('outlet_id')->get();
    
    echo "Found {$invoicesWithoutOutlet->count()} service invoices without outlet_id\n\n";
    
    $updated = 0;
    $skipped = 0;
    
    foreach ($invoicesWithoutOutlet as $invoice) {
        echo "Processing Invoice ID: {$invoice->id_service_invoice} - {$invoice->no_invoice}\n";
        
        // Try to get outlet_id from mesin customer's ongkos kirim
        if ($invoice->id_mesin_customer && $invoice->mesinCustomer && $invoice->mesinCustomer->ongkosKirim) {
            $outletId = $invoice->mesinCustomer->ongkosKirim->id_outlet;
            
            $invoice->update(['outlet_id' => $outletId]);
            echo "  ✅ Updated with outlet_id: {$outletId}\n";
            $updated++;
        } else {
            // If no mesin customer, try to get from user's outlet or default to 1
            $outletId = $invoice->user->outlet_id ?? 1;
            
            $invoice->update(['outlet_id' => $outletId]);
            echo "  ⚠️  Updated with default outlet_id: {$outletId} (no mesin customer)\n";
            $updated++;
        }
    }
    
    echo "\n=== UPDATE COMPLETE ===\n";
    echo "Updated: {$updated} invoices\n";
    echo "Skipped: {$skipped} invoices\n";
    
    // Verify the update
    echo "\n=== VERIFICATION ===\n";
    $totalInvoices = ServiceInvoice::count();
    $invoicesWithOutlet = ServiceInvoice::whereNotNull('outlet_id')->count();
    $invoicesWithoutOutlet = ServiceInvoice::whereNull('outlet_id')->count();
    
    echo "Total service invoices: {$totalInvoices}\n";
    echo "With outlet_id: {$invoicesWithOutlet}\n";
    echo "Without outlet_id: {$invoicesWithoutOutlet}\n";
    
    if ($invoicesWithoutOutlet === 0) {
        echo "✅ SUCCESS: All service invoices now have outlet_id!\n";
    } else {
        echo "⚠️  WARNING: {$invoicesWithoutOutlet} invoices still missing outlet_id\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}