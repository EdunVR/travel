<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\CompanyBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyBankAccountController extends Controller
{
    use \App\Traits\HasOutletFilter;

    public function index(Request $request)
    {
        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        $bankAccounts = CompanyBankAccount::byOutlet($outletId)
            ->orderBy('sort_order')
            ->orderBy('bank_name')
            ->get();
            
        return response()->json([
            'success' => true,
            'bank_accounts' => $bankAccounts
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:100',
            'currency' => 'required|string|max:10',
            'sort_order' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bankAccount = CompanyBankAccount::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil ditambahkan',
                'data' => $bankAccount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error creating bank account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan rekening'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:100',
            'currency' => 'required|string|max:10',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bankAccount = CompanyBankAccount::findOrFail($id);
            $bankAccount->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil diupdate',
                'data' => $bankAccount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating bank account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate rekening'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $bankAccount = CompanyBankAccount::findOrFail($id);
            $bankAccount->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error deleting bank account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus rekening'
            ], 500);
        }
    }
}