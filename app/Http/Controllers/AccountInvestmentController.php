<?php

namespace App\Http\Controllers;

use App\Models\InvestorAccount;
use App\Models\AccountInvestment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountInvestmentController extends Controller
{
    public function store(Request $request, $investorId, $accountId)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:investment,deposit,withdrawal,penarikan',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $account = InvestorAccount::findOrFail($accountId);
        
        DB::beginTransaction();
        try {
            $investment = $account->investments()->create($validated);

            if ($request->hasFile('document')) {
                $path = $request->file('document')->store('account-investments', 'public');
                $investment->update(['document' => $path]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($investorId, $accountId, $investmentId)
    {
        DB::beginTransaction();
        
        try {
            $account = InvestorAccount::findOrFail($accountId);
            $investment = $account->investments()->findOrFail($investmentId);
            
            // Simpan nilai amount untuk update balance
            $amount = $investment->amount;
            $type = $investment->type;
            
            // Hapus transaksi
            $investment->delete();
            
            // Update saldo rekening
            if ($type === 'deposit' || $type === 'investment') {
                $account->decrement('current_balance', $amount);
            } elseif ($type === 'withdrawal') {
                $account->increment('current_balance', $amount);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete transaction error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: '.$e->getMessage()
            ], 500);
        }
    }

    public function edit($investorId, $accountId, $investmentId)
    {
        $transaction = AccountInvestment::where('account_id', $accountId)
                        ->findOrFail($investmentId);
        
        return response()->json([
            'date' => $transaction->date->format('Y-m-d'), // Pastikan format Y-m-d
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'description' => $transaction->description
        ]);
    }

    public function update(Request $request, $investorId, $accountId, $investmentId)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:investment,deposit,withdrawal',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $investment = AccountInvestment::findOrFail($investmentId);
        $investment->update($validated);

        return back()->with('success', 'Transaksi berhasil diperbarui');
    }
}