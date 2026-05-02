<?php

namespace App\Http\Controllers;

use App\Models\JamaahBooking;
use App\Models\JamaahPayment;
use App\Models\SalesInvoice;
use App\Services\InvoiceIntegrationService;
use App\Services\NotificationService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\HasOutletFilter;
use App\Traits\HasCompanySettings;

class PaymentController extends Controller
{
    use HasOutletFilter, HasCompanySettings;
    
    protected $invoiceService;
    protected $notificationService;
    protected $auditService;

    public function __construct(
        InvoiceIntegrationService $invoiceService,
        NotificationService $notificationService,
        AuditService $auditService
    ) {
        $this->invoiceService = $invoiceService;
        $this->notificationService = $notificationService;
        $this->auditService = $auditService;
        $this->middleware('permission:travel.payment.view')->only(['index', 'show', 'receipt']);
        $this->middleware('permission:travel.payment.create')->only(['store', 'recordPayment']);
        $this->middleware('permission:travel.payment.approve')->only(['approve']);
        $this->middleware('permission:travel.payment.receipt')->only(['generateReceipt']);
        $this->middleware('permission:travel.payment.delete')->only(['destroy']);
    }

    /**
     * Show form to create a new payment
     */
    public function create(JamaahBooking $booking)
    {
        $booking->load(['jamaah', 'travelPackage', 'payments']);
        return view('admin.travel.payment.create', compact('booking'));
    }

    /**
     * Store a new payment
     */
    public function store(Request $request, JamaahBooking $booking)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,transfer,credit_card,debit_card,other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'bukti_transfer' => 'nullable|image|mimes:jpeg,jpg,png|max:10240', // max 10MB
            'payment_type' => 'nullable|in:full,dp'
        ], [
            'amount.min' => 'Jumlah pembayaran harus lebih dari 0',
            'bukti_transfer.image' => 'File harus berupa gambar',
            'bukti_transfer.mimes' => 'Format gambar harus jpeg, jpg, atau png',
            'bukti_transfer.max' => 'Ukuran file maksimal 10MB'
        ]);

        // Validate payment amount doesn't exceed remaining balance
        $remainingBalance = $booking->getRemainingBalance();
        if ($request->amount > $remainingBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pembayaran melebihi sisa tagihan',
                'code' => 'PAYMENT_EXCEEDS_BALANCE',
                'data' => [
                    'requested_amount' => $request->amount,
                    'remaining_balance' => $remainingBalance
                ]
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Generate receipt number
            $receiptNumber = $this->generateReceiptNumber();

            // Handle bukti transfer upload with compression
            $buktiTransferPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $buktiTransferPath = $this->compressAndStoreImage(
                    $request->file('bukti_transfer'),
                    'bukti_transfer'
                );
            }

            // Create payment record
            $payment = JamaahPayment::create([
                'id_jamaah_booking' => $booking->id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'receipt_number' => $receiptNumber,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'recorded_by' => auth()->id(),
                'bukti_transfer' => $buktiTransferPath,
                'payment_type' => $request->payment_type ?? 'dp'
            ]);

            // Update booking payment status
            $booking->paid_amount += $request->amount;
            $booking->updatePaymentStatus();

            // Sync piutang
            $this->syncPiutang($booking);

            // Update invoice if exists
            if ($booking->id_invoice) {
                $invoice = SalesInvoice::find($booking->id_invoice);
                if ($invoice) {
                    $this->invoiceService->updateInvoicePayment($invoice, $request->amount);
                }
            }

            // Send notification to finance team
            $this->notificationService->notifyPaymentReceived($payment);

            // Log payment transaction to audit trail
            $this->auditService->logPaymentTransaction(
                $booking->id,
                $request->amount,
                $request->payment_method,
                $booking->booking_code
            );

            DB::commit();

            Log::info("Payment recorded for jamaah booking", [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'amount' => $request->amount
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil dicatat',
                    'payment' => $payment->load('recordedBy'),
                    'booking' => $booking->fresh()
                ]);
            }

            return redirect()->route('admin.inventaris.booking.show', $booking->id)
                ->with('success', 'Pembayaran berhasil dicatat');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to record payment", [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mencatat pembayaran: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Compress and store image
     */
    private function compressAndStoreImage($file, $folder = 'bukti_transfer')
    {
        try {
            $image = \Intervention\Image\Facades\Image::make($file);
            
            // Resize if width > 1200px
            if ($image->width() > 1200) {
                $image->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Compress quality to 75%
            $image->encode('jpg', 75);
            
            // Generate filename
            $filename = time() . '_' . uniqid() . '.jpg';
            $path = $folder . '/' . $filename;
            
            // Store to storage/app/public
            \Storage::disk('public')->put($path, (string) $image);
            
            return $path;
            
        } catch (\Exception $e) {
            Log::error('Image compression failed: ' . $e->getMessage());
            // Fallback to normal store
            return $file->store($folder, 'public');
        }
    }

    /**
     * Sync piutang for travel booking
     */
    private function syncPiutang(JamaahBooking $booking)
    {
        try {
            $grandTotal = $booking->getGrandTotal();
            $paidAmount = $booking->paid_amount;
            $sisaPiutang = max(0, $grandTotal - $paidAmount);

            // Find or create piutang
            $piutang = \App\Models\Piutang::firstOrNew([
                'id_jamaah_booking' => $booking->id
            ]);

            $piutang->fill([
                'id_member' => $booking->id_member,
                'id_outlet' => $booking->id_outlet,
                'nama' => 'Booking Travel - ' . $booking->booking_code,
                'tanggal_tempo' => $booking->booking_date,
                'tanggal_jatuh_tempo' => $booking->travelPackage->departure_date ?? now()->addDays(30),
                'jumlah_piutang' => $grandTotal,
                'jumlah_dibayar' => $paidAmount,
                'sisa_piutang' => $sisaPiutang,
                'piutang' => $sisaPiutang, // legacy field
                'status' => $sisaPiutang <= 0 ? 'lunas' : 'belum_lunas',
            ]);

            $piutang->save();

            Log::info('Piutang synced for booking', [
                'booking_id' => $booking->id,
                'piutang_id' => $piutang->id_piutang,
                'sisa' => $sisaPiutang
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to sync piutang: ' . $e->getMessage());
            // Don't throw, just log - piutang sync shouldn't block payment
        }
    }

    /**
     * Get payment history for a booking
     */
    public function index(JamaahBooking $booking)
    {
        $payments = $booking->payments()
            ->with('recordedBy')
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }

    /**
     * Generate receipt PDF
     */
    public function generateReceipt(JamaahPayment $payment)
    {
        $payment->load(['jamaahBooking.jamaah', 'jamaahBooking.travelPackage', 'recordedBy']);

        $pdf = PDF::loadView('admin.travel.payment.receipt-pdf', compact('payment'))
            ->setPaper('a4', 'portrait');

        $filename = "Kwitansi-{$payment->receipt_number}.pdf";

        return $pdf->stream($filename);
    }

    /**
     * Download receipt PDF
     */
    public function downloadReceipt(JamaahPayment $payment)
    {
        $payment->load(['jamaahBooking.jamaah', 'jamaahBooking.travelPackage', 'recordedBy']);

        $pdf = PDF::loadView('admin.travel.payment.receipt-pdf', compact('payment'))
            ->setPaper('a4', 'portrait');

        $filename = "Kwitansi-{$payment->receipt_number}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Get overdue payments (bookings with incomplete payment)
     */
    public function overduePayments()
    {
        $overdueBookings = JamaahBooking::where('payment_status', '!=', 'paid')
            ->whereHas('travelPackage', function($query) {
                $query->where('departure_date', '<=', now()->addDays(30));
            })
            ->with(['jamaah', 'travelPackage'])
            ->get();

        return response()->json([
            'success' => true,
            'overdue_bookings' => $overdueBookings
        ]);
    }

    /**
     * Send payment reminder
     */
    public function sendReminder(JamaahBooking $booking)
    {
        // TODO: Implement notification system integration
        // For now, just return success
        
        Log::info("Payment reminder sent", [
            'booking_id' => $booking->id,
            'jamaah_id' => $booking->id_member
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengingat pembayaran berhasil dikirim'
        ]);
    }

    /**
     * Check if booking can depart (payment complete)
     */
    public function checkDepartureEligibility(JamaahBooking $booking)
    {
        $eligible = $booking->payment_status === 'paid';

        return response()->json([
            'success' => true,
            'eligible' => $eligible,
            'message' => $eligible 
                ? 'Jamaah dapat berangkat' 
                : 'Pembayaran belum lunas, jamaah tidak dapat berangkat'
        ]);
    }

    /**
     * Delete a payment and reverse the booking's paid amount
     */
    public function destroy(JamaahPayment $payment)
    {
        $booking = $payment->jamaahBooking;

        DB::beginTransaction();
        try {
            $amount = $payment->amount;

            // Reverse paid amount on booking
            $booking->paid_amount = max(0, $booking->paid_amount - $amount);
            $booking->updatePaymentStatus();

            // Reset invoice status to unpaid so it can be deleted/recreated
            if ($booking->id_invoice) {
                $invoice = SalesInvoice::find($booking->id_invoice);
                if ($invoice) {
                    $newTotalDibayar = max(0, $invoice->total_dibayar - $amount);
                    $newStatus = $newTotalDibayar <= 0 ? 'unpaid' : ($newTotalDibayar < $invoice->total ? 'partial' : 'paid');
                    $invoice->update([
                        'total_dibayar' => $newTotalDibayar,
                        'sisa_tagihan'  => $invoice->total - $newTotalDibayar,
                        'status'        => $newStatus,
                    ]);
                }
            }

            $payment->delete();

            DB::commit();

            Log::info('Payment deleted', ['payment_id' => $payment->id, 'booking_id' => $booking->id, 'amount' => $amount]);

            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting payment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus pembayaran'], 500);
        }
    }

    /**
     * Generate unique receipt number
     */
    private function generateReceiptNumber(): string
    {
        $prefix = 'KWIT-JMH';
        $date = now()->format('Ymd');
        
        $lastPayment = JamaahPayment::where('receipt_number', 'like', "{$prefix}-{$date}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPayment) {
            $parts = explode('-', $lastPayment->receipt_number);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf("%s-%s-%04d", $prefix, $date, $sequence);
    }

    /**
     * Create invoice for booking
     */
    public function createInvoice(Request $request, JamaahBooking $booking)
    {
        $request->validate([
            'discount_amount' => 'nullable|numeric|min:0',
            'room_type' => 'nullable|in:single,double,triple,quad',
            'terms_conditions' => 'nullable|string',
            'seller_name' => 'nullable|string|max:255',
            'closing_source' => 'nullable|in:kantor,alumni,digital_marketing,event'
        ]);

        // Check if invoice already exists
        if ($booking->id_invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice sudah dibuat untuk booking ini'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Update booking with adjustments
            $booking->update([
                'discount_amount' => $request->discount_amount ?? 0,
                'room_type' => $request->room_type ?? $booking->room_type,
                'terms_conditions' => $request->terms_conditions,
                'seller_name' => $request->seller_name ?? auth()->user()->name,
                'closing_source' => $request->closing_source
            ]);

            // Recalculate total price with discount
            $originalTotal = $booking->total_price + ($request->discount_amount ?? 0);
            $booking->total_price = $originalTotal - ($request->discount_amount ?? 0);
            $booking->remaining_amount = $booking->total_price - $booking->paid_amount;
            $booking->save();
            $booking->updatePaymentStatus();

            // Create invoice
            $invoice = $this->invoiceService->createInvoiceForJamaah(
                $booking,
                'installment', // Default to installment
                0 // No down payment specified
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dibuat',
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing invoice for booking
     */
    public function updateInvoice(Request $request, JamaahBooking $booking)
    {
        $request->validate([
            'discount_amount' => 'nullable|numeric|min:0',
            'room_type' => 'nullable|in:single,double,triple,quad',
            'terms_conditions' => 'nullable|string',
            'seller_name' => 'nullable|string|max:255',
            'closing_source' => 'nullable|in:kantor,alumni,digital_marketing,event'
        ]);

        // Check if invoice exists
        if (!$booking->id_invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice belum dibuat untuk booking ini'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Calculate price difference if discount changed
            $oldDiscount = $booking->discount_amount ?? 0;
            $newDiscount = $request->discount_amount ?? 0;
            $discountDiff = $newDiscount - $oldDiscount;

            // Update booking with new adjustments
            $booking->update([
                'discount_amount' => $newDiscount,
                'room_type' => $request->room_type ?? $booking->room_type,
                'terms_conditions' => $request->terms_conditions,
                'seller_name' => $request->seller_name ?? $booking->seller_name,
                'closing_source' => $request->closing_source ?? $booking->closing_source
            ]);

            // Recalculate total price with new discount
            if ($discountDiff != 0) {
                $booking->total_price = $booking->total_price - $discountDiff;
                $booking->remaining_amount = $booking->total_price - $booking->paid_amount;
                $booking->save();
                $booking->updatePaymentStatus();

                // Update invoice total
                if ($booking->invoice) {
                    $booking->invoice->update([
                        'total' => $booking->total_price,
                        'sisa' => $booking->remaining_amount
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil diupdate',
                'invoice' => $booking->invoice
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview invoice before saving
     */
    public function previewInvoice(Request $request, JamaahBooking $booking)
    {
        try {
            // Get preview parameters
            $discountAmount = $request->get('discount_amount', 0);
            $roomType = $request->get('room_type', $booking->room_type ?? 'double');
            $termsConditions = $request->get('terms_conditions', '');
            $sellerName = $request->get('seller_name', auth()->user()->name);
            $closingSource = $request->get('closing_source', $booking->closing_source ?? 'kantor');

            // Create temporary booking clone for preview
            $previewBooking = clone $booking;
            $previewBooking->discount_amount = $discountAmount;
            $previewBooking->room_type = $roomType;
            $previewBooking->terms_conditions = $termsConditions;
            $previewBooking->seller_name = $sellerName;
            $previewBooking->closing_source = $closingSource;
            
            // Recalculate total for preview
            $originalTotal = $booking->total_price + $discountAmount;
            $previewBooking->total_price = $originalTotal - $discountAmount;
            $previewBooking->remaining_amount = $previewBooking->total_price - $booking->paid_amount;

            // Create temporary invoice for preview
            $invoice = new SalesInvoice();
            $invoice->no_invoice = 'PREVIEW-' . $booking->booking_code;
            $invoice->tanggal = now();
            $invoice->due_date = now()->addDays(30);
            $invoice->status = 'draft';
            $invoice->id_member = $booking->id_member;
            $invoice->id_outlet = $booking->id_outlet;
            $invoice->total = $previewBooking->total_price;
            $invoice->total_dibayar = $booking->paid_amount;
            $invoice->sisa = $previewBooking->remaining_amount;
            $invoice->keterangan = 'Preview Invoice';
            $invoice->user = auth()->user();

            $previewBooking->load(['travelPackage.flightDeparture', 'travelPackage.flightReturn', 'travelPackage.hotelMakkah', 'travelPackage.hotelMadinah', 'jamaah', 'keberangkatan', 'closedBy', 'outlet', 'payments', 'addons', 'hotelBookings.hotel']);

            // Get company settings
            $companySettings = $this->getCompanySettingsForPrint();

            // Get bank accounts
            $outletId = $booking->id_outlet ?? 1;
            $bankAccounts = \App\Models\CompanyBankAccount::where('id_outlet', $outletId)
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('bank_name')
                ->get();

            if ($bankAccounts->count() == 0) {
                $bankAccounts = \App\Models\CompanyBankAccount::whereNull('id_outlet')
                    ->where('is_active', 1)
                    ->orderBy('sort_order')
                    ->orderBy('bank_name')
                    ->get();
            }
            
            if ($bankAccounts->count() == 0 && 
                ($companySettings['bank_name'] || $companySettings['bank_account_number'])) {
                $bankAccounts = collect([
                    (object) [
                        'bank_name' => $companySettings['bank_name'],
                        'account_number' => $companySettings['bank_account_number'],
                        'account_holder' => $companySettings['bank_account_name']
                    ]
                ]);
            }

            // Generate PDF preview
            $pdf = PDF::loadView('admin.travel.payment.jamaah-invoice-preview-pdf', [
                'invoice' => $invoice,
                'booking' => $previewBooking,
                'companySettings' => $companySettings,
                'bankAccounts' => $bankAccounts,
                'termsConditions' => $termsConditions
            ])->setPaper('a4', 'portrait');

            return $pdf->stream('Preview-Invoice.pdf');

        } catch (\Exception $e) {
            Log::error('Error generating invoice preview: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal generate preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
    /**
     * Delete invoice for booking (force=1 also deletes all payments)
     */
    public function deleteInvoice(JamaahBooking $booking)
    {
        if (!$booking->id_invoice) {
            return response()->json(['success' => false, 'message' => 'Booking ini tidak memiliki invoice'], 400);
        }

        $force = request()->boolean('force');

        try {
            $invoice = SalesInvoice::find($booking->id_invoice);
            if (!$invoice) {
                return response()->json(['success' => false, 'message' => 'Invoice tidak ditemukan'], 404);
            }

            DB::beginTransaction();

            // Force: delete all payments first and reset booking paid_amount
            if ($force) {
                $payments = $booking->payments()->get();
                foreach ($payments as $payment) {
                    $booking->paid_amount = max(0, $booking->paid_amount - $payment->amount);
                    $payment->delete();
                }
                $booking->updatePaymentStatus();
            } elseif ($invoice->total_dibayar > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak dapat dihapus karena sudah ada pembayaran. Gunakan force delete.'
                ], 400);
            }

            // Delete invoice items
            $invoice->items()->delete();
            $invoice->delete();

            // Remove invoice reference from booking
            $booking->update(['id_invoice' => null]);

            DB::commit();

            Log::info("Invoice deleted for jamaah booking", [
                'booking_id' => $booking->id,
                'invoice_id' => $invoice->id_sales_invoice,
                'invoice_number' => $invoice->no_invoice
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete invoice", [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate jamaah invoice PDF (custom template for travel)
     */
    public function generateJamaahInvoice(JamaahBooking $booking)
    {
        if (!$booking->id_invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Booking ini belum memiliki invoice'
            ], 404);
        }

        try {
            $invoice = SalesInvoice::with(['member', 'items', 'outlet'])
                ->findOrFail($booking->id_invoice);

            $booking->load([
                'travelPackage.flightDeparture',
                'travelPackage.flightReturn',
                'travelPackage.hotelMakkah',
                'travelPackage.hotelMadinah',
                'jamaah',
                'keberangkatan',
                'closedBy',
                'outlet',
                'payments',
                'addons',
                'hotelBookings.hotel'
            ]);

            // Get company settings - use object instead of array
            $companySettings = $booking->getCompanySettings();

            // Get bank accounts for outlet
            $outletId = $booking->id_outlet ?? 1;
            $bankAccounts = \App\Models\CompanyBankAccount::where('id_outlet', $outletId)
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('bank_name')
                ->get();

            if ($bankAccounts->count() == 0) {
                $bankAccounts = \App\Models\CompanyBankAccount::whereNull('id_outlet')
                    ->where('is_active', 1)
                    ->orderBy('sort_order')
                    ->orderBy('bank_name')
                    ->get();
            }
            
            // If still no bank accounts from company_bank_accounts table,
            // use bank info from company_settings
            if ($bankAccounts->count() == 0 && 
                ($companySettings['bank_name'] || $companySettings['bank_account_number'])) {
                // Create a collection with bank info from company_settings
                $bankAccounts = collect([
                    (object) [
                        'bank_name' => $companySettings['bank_name'],
                        'account_number' => $companySettings['bank_account_number'],
                        'account_holder' => $companySettings['bank_account_name']
                    ]
                ]);
            }

            $download = request()->get('download', false);

            $pdf = PDF::loadView('admin.travel.payment.jamaah-invoice-pdf', compact(
                'invoice',
                'booking',
                'companySettings',
                'bankAccounts'
            ))->setPaper('a4', 'portrait');

            $filename = "Invoice-Jamaah-{$invoice->no_invoice}.pdf";

            if ($download) {
                return $pdf->download($filename);
            }

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            Log::error('Error generating jamaah invoice PDF: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
