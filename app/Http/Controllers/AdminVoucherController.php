<?php

namespace App\Http\Controllers;

use App\Models\AffiliateVoucher;
use App\Models\Affiliator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminVoucherController extends Controller
{
    /**
     * Display voucher management page
     */
    public function index()
    {
        $affiliators = Affiliator::where('status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('admin.affiliate.vouchers', compact('affiliators'));
    }

    /**
     * Get vouchers list (AJAX)
     */
    public function list(Request $request)
    {
        $query = AffiliateVoucher::with('affiliator');

        // Filter by affiliator
        if ($request->filled('affiliator_id')) {
            $query->where('id_affiliator', $request->affiliator_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active()->valid();
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where(function($q) {
                        $q->where('valid_until', '<', now())
                          ->orWhere(function($subQ) {
                              $subQ->whereNotNull('usage_limit')
                                   ->whereRaw('usage_count >= usage_limit');
                          });
                    });
                    break;
            }
        }

        // Search by code
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $vouchers = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $vouchers
        ]);
    }

    /**
     * Generate unique voucher code
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
     * Store new voucher
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_affiliator' => 'required|exists:affiliators,id',
            'code' => 'required|string|max:50|unique:affiliate_vouchers,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $voucher = AffiliateVoucher::create([
            'id_affiliator' => $request->id_affiliator,
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount' => $request->max_discount,
            'min_transaction' => $request->min_transaction ?? 0,
            'usage_limit' => $request->usage_limit,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
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
        $voucher = AffiliateVoucher::findOrFail($id);

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
}
