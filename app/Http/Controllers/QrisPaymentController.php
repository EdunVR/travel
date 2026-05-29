<?php

namespace App\Http\Controllers;

use App\Models\JamaahBooking;
use App\Models\JamaahPayment;
use App\Models\QrisTransaction;
use App\Services\QrisPaymentService;
use App\Services\InvoiceIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QrisPaymentController extends Controller
{
    protected QrisPaymentService $qrisService;
    protected InvoiceIntegrationService $invoiceService;

    public function __construct(QrisPaymentService $qrisService, InvoiceIntegrationService $invoiceService)
    {
        $this->qrisService = $qrisService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Generate QRIS for public invoice payment
     * Called via AJAX from the public invoice page
     */
    public function generateQris(Request $request, $packageId, $bookingId)
    {
        $booking = JamaahBooking::with(['travelPackage', 'jamaah'])->findOrFail($bookingId);
        abort_if($booking->id_travel_package != $packageId, 404);

        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $amount = (int) $request->amount;

        // Validate amount doesn't exceed remaining balance
        $remainingBalance = $booking->remaining_amount ?? ($booking->total_price - ($booking->paid_amount ?? 0));
        if ($amount > $remainingBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah melebihi sisa tagihan (Rp ' . number_format($remainingBalance, 0, ',', '.') . ')',
            ], 422);
        }

        // Check if there's already a pending QRIS for this booking with same amount
        $existingQris = QrisTransaction::where('id_jamaah_booking', $bookingId)
            ->where('status', 'pending')
            ->where('amount', $amount)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->first();

        if ($existingQris && $existingQris->qris_content) {
            return response()->json([
                'success' => true,
                'data' => [
                    'qris_content' => $existingQris->qris_content,
                    'qris_invoice_id' => $existingQris->qris_invoice_id,
                    'trx_number' => $existingQris->trx_number,
                    'amount' => $existingQris->amount,
                    'expired_at' => $existingQris->expired_at?->toIso8601String(),
                    'created_at' => $existingQris->created_at->toIso8601String(),
                ],
                'message' => 'QRIS sudah tersedia',
            ]);
        }

        // Generate unique transaction number
        $trxNumber = $this->qrisService->generateTrxNumber($bookingId);

        // Call InterActive QRIS API
        $result = $this->qrisService->createInvoice($trxNumber, $amount);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal membuat QRIS. Silakan coba lagi.',
            ], 500);
        }

        // Save QRIS transaction
        $qrisTransaction = QrisTransaction::create([
            'id_jamaah_booking' => $bookingId,
            'trx_number' => $trxNumber,
            'qris_invoice_id' => $result['qris_invoiceid'] ?? null,
            'amount' => $amount,
            'qris_content' => $result['qris_content'] ?? null,
            'qris_nmid' => $result['nmid'] ?? config('services.qris.nmid'),
            'qris_request_date' => $result['qris_request_date'] ?? now()->toDateTimeString(),
            'status' => 'pending',
            'expired_at' => now()->addMinutes(30), // QRIS typically expires in 30 minutes
            'api_response_create' => $result['data'] ?? $result,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'qris_content' => $qrisTransaction->qris_content,
                'qris_invoice_id' => $qrisTransaction->qris_invoice_id,
                'trx_number' => $qrisTransaction->trx_number,
                'amount' => $qrisTransaction->amount,
                'expired_at' => $qrisTransaction->expired_at?->toIso8601String(),
                'created_at' => $qrisTransaction->created_at->toIso8601String(),
            ],
            'message' => 'QRIS berhasil dibuat',
        ]);
    }

    /**
     * Check QRIS payment status
     * Called via AJAX polling from the payment page
     */
    public function checkStatus(Request $request, $trxNumber)
    {
        $qrisTransaction = QrisTransaction::where('trx_number', $trxNumber)->firstOrFail();

        // If already paid, return immediately
        if ($qrisTransaction->isPaid()) {
            return response()->json([
                'success' => true,
                'paid' => true,
                'status' => 'paid',
                'message' => 'Pembayaran berhasil!',
                'data' => [
                    'payment_customer_name' => $qrisTransaction->payment_customer_name,
                    'payment_method_by' => $qrisTransaction->payment_method_by,
                    'paid_at' => $qrisTransaction->paid_at?->toIso8601String(),
                ],
            ]);
        }

        // If expired
        if ($qrisTransaction->isExpired()) {
            $qrisTransaction->markAsExpired();
            return response()->json([
                'success' => true,
                'paid' => false,
                'status' => 'expired',
                'message' => 'QRIS sudah kadaluarsa. Silakan buat ulang.',
            ]);
        }

        // Call InterActive QRIS Check API
        $trxDate = $qrisTransaction->created_at->format('Y-m-d');
        $result = $this->qrisService->checkInvoice(
            $qrisTransaction->qris_invoice_id,
            $qrisTransaction->trx_number,
            $trxDate
        );

        if ($result['success'] && $result['paid']) {
            // Payment confirmed! Process it
            $this->processQrisPayment($qrisTransaction, $result['data'] ?? $result);

            return response()->json([
                'success' => true,
                'paid' => true,
                'status' => 'paid',
                'message' => 'Pembayaran QRIS berhasil diverifikasi!',
                'data' => [
                    'payment_customer_name' => $result['qris_payment_customername'] ?? null,
                    'payment_method_by' => $result['qris_payment_methodby'] ?? null,
                    'paid_at' => now()->toIso8601String(),
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'paid' => false,
            'status' => 'pending',
            'message' => 'Menunggu pembayaran...',
        ]);
    }

    /**
     * Process confirmed QRIS payment - create JamaahPayment record
     */
    protected function processQrisPayment(QrisTransaction $qrisTransaction, array $checkData): void
    {
        try {
            DB::beginTransaction();

            // Mark QRIS as paid
            $qrisTransaction->markAsPaid($checkData);

            $booking = JamaahBooking::with(['travelPackage', 'jamaah'])->find($qrisTransaction->id_jamaah_booking);
            if (!$booking) {
                DB::rollBack();
                return;
            }

            // Generate receipt number
            $receiptNumber = 'QRIS-' . now()->format('Ymd') . '-' . str_pad(
                JamaahPayment::whereDate('created_at', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            // Create payment record (auto-verified since QRIS is real-time)
            $payment = JamaahPayment::create([
                'id_jamaah_booking' => $booking->id,
                'payment_date' => now()->toDateString(),
                'amount' => $qrisTransaction->amount,
                'payment_method' => 'qris',
                'receipt_number' => $receiptNumber,
                'reference_number' => $qrisTransaction->trx_number,
                'notes' => 'Pembayaran via QRIS InterActive. ' .
                    ($checkData['qris_payment_methodby'] ?? '') . ' - ' .
                    ($checkData['qris_payment_customername'] ?? ''),
                'recorded_by' => null, // System/auto
                'payment_type' => $this->determinePaymentType($booking, $qrisTransaction->amount),
                'verification_status' => 'verified', // QRIS is auto-verified
                'verified_at' => now(),
            ]);

            // Link payment to QRIS transaction
            $qrisTransaction->update(['id_jamaah_payment' => $payment->id]);

            // Update booking payment status
            $booking->paid_amount = ($booking->paid_amount ?? 0) + $qrisTransaction->amount;
            $booking->remaining_amount = $booking->total_price - $booking->paid_amount;

            if ($booking->paid_amount >= $booking->total_price) {
                $booking->payment_status = 'paid';
            } elseif ($booking->paid_amount > 0) {
                $booking->payment_status = 'partial';
            }
            $booking->save();

            // Update invoice if exists
            if ($booking->id_invoice) {
                $invoice = \App\Models\SalesInvoice::find($booking->id_invoice);
                if ($invoice) {
                    $this->invoiceService->updateInvoicePayment($invoice, $qrisTransaction->amount);
                }
            }

            // Sync piutang
            $this->syncPiutang($booking);

            // Trigger fully paid event if applicable
            if ($booking->payment_status === 'paid') {
                event(new \App\Events\BookingFullyPaid($booking));
            }

            DB::commit();

            // Send WhatsApp notification
            $this->sendQrisPaymentNotification($booking, $payment, $qrisTransaction);

            Log::info('QRIS Payment processed successfully', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'qris_trx' => $qrisTransaction->trx_number,
                'amount' => $qrisTransaction->amount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QRIS Payment processing failed', [
                'qris_trx' => $qrisTransaction->trx_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Determine payment type based on booking state
     */
    protected function determinePaymentType(JamaahBooking $booking, int $amount): string
    {
        $remainingAfterPayment = $booking->total_price - ($booking->paid_amount ?? 0) - $amount;
        return $remainingAfterPayment <= 0 ? 'full' : 'dp';
    }

    /**
     * Sync piutang after payment
     */
    protected function syncPiutang(JamaahBooking $booking): void
    {
        try {
            if (class_exists(\App\Models\Piutang::class)) {
                \App\Models\Piutang::updateOrCreate(
                    ['id_jamaah_booking' => $booking->id],
                    [
                        'id_member' => $booking->id_member,
                        'id_outlet' => $booking->id_outlet,
                        'total_tagihan' => $booking->total_price,
                        'total_dibayar' => $booking->paid_amount ?? 0,
                        'sisa_tagihan' => $booking->remaining_amount ?? ($booking->total_price - ($booking->paid_amount ?? 0)),
                        'status' => $booking->payment_status === 'paid' ? 'lunas' : 'belum_lunas',
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::warning('Sync piutang failed for QRIS payment', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send WhatsApp notification after QRIS payment
     */
    protected function sendQrisPaymentNotification(JamaahBooking $booking, JamaahPayment $payment, QrisTransaction $qris): void
    {
        try {
            $jamaah = $booking->jamaah;
            if (!$jamaah || !$jamaah->telepon) return;

            $package = $booking->travelPackage;
            $isFullyPaid = $booking->payment_status === 'paid';
            $remainingBalance = $booking->remaining_amount ?? ($booking->total_price - $booking->paid_amount);

            // Generate receipt URL
            $receiptToken = hash('sha256', $payment->id . $payment->id_jamaah_booking . config('app.key'));
            $receiptUrl = route('public.receipt', ['paymentId' => $payment->id, 'token' => $receiptToken]);

            $msg = "Assalamu'alaikum {$jamaah->nama} ✅\n\n";
            $msg .= "💳 *PEMBAYARAN QRIS BERHASIL!*\n\n";
            $msg .= "📦 Paket: {$package->package_name}\n";
            $msg .= "🔖 Booking: {$booking->booking_code}\n";
            $msg .= "💰 Jumlah: Rp " . number_format($qris->amount, 0, ',', '.') . "\n";
            $msg .= "📱 Via: " . ($qris->payment_method_by ?? 'QRIS') . "\n";

            if ($isFullyPaid) {
                $msg .= "\n✅ *STATUS: LUNAS*\n";
                $msg .= "Alhamdulillah, pembayaran Anda telah lengkap!\n";
            } else {
                $msg .= "\n💳 Sisa Tagihan: Rp " . number_format($remainingBalance, 0, ',', '.') . "\n";
            }

            $msg .= "\n🧾 *Kwitansi:*\n{$receiptUrl}\n\n";
            $msg .= "Terima kasih telah mempercayai HM Tour! 🙏";

            $whatsappService = new \App\Services\WhatsAppService();
            $whatsappService->sendMessage($jamaah->telepon, $msg);

        } catch (\Exception $e) {
            Log::error('Failed to send QRIS WhatsApp notification', ['error' => $e->getMessage()]);
        }
    }

    // ==========================================
    // ADMIN ENDPOINTS
    // ==========================================

    /**
     * Admin: Generate QRIS for a booking payment
     */
    public function adminGenerateQris(Request $request, JamaahBooking $booking)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $amount = (int) $request->amount;

        $remainingBalance = $booking->remaining_amount ?? ($booking->total_price - ($booking->paid_amount ?? 0));

        if ($amount > $remainingBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah melebihi sisa tagihan',
            ], 422);
        }

        $trxNumber = $this->qrisService->generateTrxNumber($booking->id, 'HMADM');
        $result = $this->qrisService->createInvoice($trxNumber, $amount);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal membuat QRIS',
            ], 500);
        }

        $qrisTransaction = QrisTransaction::create([
            'id_jamaah_booking' => $booking->id,
            'trx_number' => $trxNumber,
            'qris_invoice_id' => $result['qris_invoiceid'] ?? null,
            'amount' => $amount,
            'qris_content' => $result['qris_content'] ?? null,
            'qris_nmid' => $result['nmid'] ?? config('services.qris.nmid'),
            'qris_request_date' => $result['qris_request_date'] ?? now()->toDateTimeString(),
            'status' => 'pending',
            'expired_at' => now()->addMinutes(30),
            'api_response_create' => $result['data'] ?? $result,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'qris_content' => $qrisTransaction->qris_content,
                'qris_invoice_id' => $qrisTransaction->qris_invoice_id,
                'trx_number' => $qrisTransaction->trx_number,
                'amount' => $qrisTransaction->amount,
                'expired_at' => $qrisTransaction->expired_at?->toIso8601String(),
            ],
            'message' => 'QRIS berhasil dibuat',
        ]);
    }

    /**
     * Admin: View QRIS transactions for a booking
     */
    public function adminQrisHistory(JamaahBooking $booking)
    {
        $transactions = QrisTransaction::where('id_jamaah_booking', $booking->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }
}
