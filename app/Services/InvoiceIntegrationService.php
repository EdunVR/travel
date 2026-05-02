<?php

namespace App\Services;

use App\Models\JamaahBooking;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceIntegrationService
{
    /**
     * Create invoice for jamaah booking
     * 
     * @param JamaahBooking $booking
     * @param string $paymentTerm 'full' or 'installment'
     * @param float|null $downPayment Down payment amount for installment
     * @return SalesInvoice
     */
    public function createInvoiceForJamaah(JamaahBooking $booking, string $paymentTerm = 'full', ?float $downPayment = null)
    {
        try {
            DB::beginTransaction();

            // Load relationships
            $booking->load(['travelPackage', 'jamaah', 'outlet']);

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber($booking->id_outlet);

            // Calculate due date based on payment term
            $dueDate = $paymentTerm === 'full' 
                ? now()->addDays(7) 
                : now()->addDays(30);

            // Create invoice
            $invoice = SalesInvoice::create([
                'no_invoice' => $invoiceNumber,
                'tanggal' => now(),
                'id_member' => $booking->id_member,
                'id_outlet' => $booking->id_outlet,
                'id_user' => auth()->id() ?? 1, // fallback ke user sistem jika public
                'total' => $booking->total_price,
                'total_dibayar' => 0,
                'sisa_tagihan' => $booking->total_price,
                'status' => 'menunggu', // Changed from 'belum_lunas' to 'menunggu'
                'due_date' => $dueDate,
                'keterangan' => "Invoice untuk paket {$booking->travelPackage->package_name} - Booking {$booking->booking_code}",
                'jenis_pembayaran' => $paymentTerm === 'full' ? 'lunas' : 'dp',
                'subtotal' => $booking->total_price,
                'total_diskon' => 0
            ]);

            // Create invoice item
            // Note: sales_invoice_item table doesn't have 'nama_produk' column, only 'deskripsi'
            SalesInvoiceItem::create([
                'id_sales_invoice' => $invoice->id_sales_invoice,
                'deskripsi' => "{$booking->travelPackage->package_name} - Paket {$booking->travelPackage->package_type} ({$booking->travelPackage->duration_days} hari)",
                'kuantitas' => 1,
                'harga_normal' => $booking->total_price,
                'diskon' => 0,
                'subtotal' => $booking->total_price
            ]);

            // Link invoice to booking
            $booking->update(['id_invoice' => $invoice->id_sales_invoice]);

            // If installment with down payment, record the down payment
            if ($paymentTerm === 'installment' && $downPayment > 0) {
                $invoice->update([
                    'total_dibayar' => $downPayment,
                    'sisa_tagihan' => $booking->total_price - $downPayment,
                    'status' => 'dibayar_sebagian' // Changed from 'dp' to 'dibayar_sebagian'
                ]);
            }

            DB::commit();

            Log::info("Invoice created for jamaah booking", [
                'booking_id' => $booking->id,
                'invoice_id' => $invoice->id_sales_invoice,
                'invoice_number' => $invoiceNumber
            ]);

            return $invoice;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create invoice for jamaah booking", [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate unique invoice number
     * 
     * @param int|null $outletId
     * @return string
     */
    private function generateInvoiceNumber(?int $outletId): string
    {
        // Fallback to outlet 1 if null
        $outletId = $outletId ?? 1;
        
        $prefix = 'INV-JMH';
        $date = now()->format('Ymd');
        
        // Get last invoice number for today
        $lastInvoice = SalesInvoice::where('no_invoice', 'like', "{$prefix}-{$date}-%")
            ->where('id_outlet', $outletId)
            ->orderBy('id_sales_invoice', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract sequence number and increment
            $parts = explode('-', $lastInvoice->no_invoice);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf("%s-%s-%04d", $prefix, $date, $sequence);
    }

    /**
     * Update invoice payment status
     * 
     * @param SalesInvoice $invoice
     * @param float $paymentAmount
     * @return void
     */
    public function updateInvoicePayment(SalesInvoice $invoice, float $paymentAmount)
    {
        $totalPaid = $invoice->total_dibayar + $paymentAmount;
        $remaining = $invoice->total - $totalPaid;

        // Determine status based on payment
        // Valid enum values: 'draft', 'menunggu', 'dibayar_sebagian', 'lunas', 'gagal'
        $status = 'menunggu';
        if ($remaining <= 0) {
            $status = 'lunas';
            $remaining = 0;
        } elseif ($totalPaid > 0) {
            $status = 'dibayar_sebagian';
        }

        $invoice->update([
            'total_dibayar' => $totalPaid,
            'sisa_tagihan' => $remaining,
            'status' => $status,
            'tanggal_pembayaran' => now()
        ]);
    }

    /**
     * Get invoice for jamaah booking
     * 
     * @param JamaahBooking $booking
     * @return SalesInvoice|null
     */
    public function getInvoiceForBooking(JamaahBooking $booking): ?SalesInvoice
    {
        if (!$booking->id_invoice) {
            return null;
        }

        return SalesInvoice::find($booking->id_invoice);
    }
}
