<?php

namespace App\Http\Controllers;

use App\Models\AffiliateVoucher;
use App\Models\VoucherUsage;
use App\Models\JamaahBooking;
use App\Models\Affiliator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    /**
     * Validate and apply voucher code
     */
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $voucher = AffiliateVoucher::where('code', strtoupper($request->code))
            ->with('affiliator')
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak ditemukan'
            ], 404);
        }

        if (!$voucher->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak valid atau sudah kadaluarsa'
            ], 400);
        }

        if (!$voucher->canBeUsed($request->amount)) {
            $minTransaction = number_format($voucher->min_transaction, 0, ',', '.');
            return response()->json([
                'success' => false,
                'message' => "Minimal transaksi untuk voucher ini adalah Rp {$minTransaction}"
            ], 400);
        }

        $discount = $voucher->calculateDiscount($request->amount);
        $finalAmount = max(0, $request->amount - $discount);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil divalidasi',
            'data' => [
                'voucher_id' => $voucher->id,
                'code' => $voucher->code,
                'discount_type' => $voucher->discount_type,
                'discount_value' => $voucher->discount_value,
                'discount_amount' => $discount,
                'original_amount' => $request->amount,
                'final_amount' => $finalAmount,
                'description' => $voucher->description,
                'affiliator_name' => $voucher->affiliator->full_name ?? 'Unknown',
            ]
        ]);
    }

    /**
     * Apply voucher to booking
     */
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:jamaah_bookings,id',
            'voucher_code' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $booking = JamaahBooking::findOrFail($request->booking_id);
            $voucher = AffiliateVoucher::where('code', strtoupper($request->voucher_code))->firstOrFail();

            // Validate voucher
            if (!$voucher->isValid()) {
                throw new \Exception('Voucher tidak valid atau sudah kadaluarsa');
            }

            $amount = $booking->getGrandTotal();
            
            if (!$voucher->canBeUsed($amount)) {
                $minTransaction = number_format($voucher->min_transaction, 0, ',', '.');
                throw new \Exception("Minimal transaksi untuk voucher ini adalah Rp {$minTransaction}");
            }

            // Calculate discount
            $discount = $voucher->calculateDiscount($amount);
            $finalAmount = max(0, $amount - $discount);

            // Update booking
            $booking->update([
                'id_voucher' => $voucher->id,
                'voucher_discount' => $discount,
            ]);

            // Record usage
            VoucherUsage::create([
                'id_voucher' => $voucher->id,
                'id_jamaah_booking' => $booking->id,
                'discount_amount' => $discount,
                'original_amount' => $amount,
                'final_amount' => $finalAmount,
                'used_at' => now(),
            ]);

            // Increment usage count
            $voucher->incrementUsage();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil diterapkan',
                'data' => [
                    'discount_amount' => $discount,
                    'final_amount' => $finalAmount,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generate voucher code
     */
    public function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (AffiliateVoucher::where('code', $code)->exists());

        return response()->json([
            'success' => true,
            'code' => $code
        ]);
    }

    /**
     * Store new voucher (for affiliator dashboard)
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:affiliate_vouchers,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'description' => 'nullable|string',
        ]);

        $affiliatorId = $request->affiliator_id ?? auth()->user()->affiliator->id ?? null;

        if (!$affiliatorId) {
            return response()->json([
                'success' => false,
                'message' => 'Affiliator tidak ditemukan'
            ], 400);
        }

        $voucher = AffiliateVoucher::create([
            'id_affiliator' => $affiliatorId,
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount' => $request->max_discount,
            'min_transaction' => $request->min_transaction ?? 0,
            'usage_limit' => $request->usage_limit,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dibuat',
            'data' => $voucher
        ]);
    }

    /**
     * Update voucher
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'discount_type' => 'sometimes|in:percentage,fixed',
            'discount_value' => 'sometimes|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $voucher = AffiliateVoucher::findOrFail($id);

        // Check ownership for affiliator
        if (!auth()->user()->hasRole('admin')) {
            $affiliatorId = auth()->user()->affiliator->id ?? null;
            if ($voucher->id_affiliator != $affiliatorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        $voucher->update($request->only([
            'discount_type',
            'discount_value',
            'max_discount',
            'min_transaction',
            'usage_limit',
            'valid_from',
            'valid_until',
            'description',
            'is_active',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diupdate',
            'data' => $voucher
        ]);
    }

    /**
     * Delete voucher
     */
    public function destroy($id)
    {
        $voucher = AffiliateVoucher::findOrFail($id);

        // Check ownership for affiliator
        if (!auth()->user()->hasRole('admin')) {
            $affiliatorId = auth()->user()->affiliator->id ?? null;
            if ($voucher->id_affiliator != $affiliatorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        // Check if voucher has been used
        if ($voucher->usage_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak dapat dihapus karena sudah pernah digunakan'
            ], 400);
        }

        $voucher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dihapus'
        ]);
    }

    /**
     * Get vouchers for affiliator
     */
    public function index(Request $request)
    {
        $affiliatorId = $request->affiliator_id ?? auth()->user()->affiliator->id ?? null;

        if (!$affiliatorId) {
            return response()->json([
                'success' => false,
                'message' => 'Affiliator tidak ditemukan'
            ], 400);
        }

        $vouchers = AffiliateVoucher::where('id_affiliator', $affiliatorId)
            ->with('usages')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vouchers
        ]);
    }
}
