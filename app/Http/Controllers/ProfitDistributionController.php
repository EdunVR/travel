<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\ProfitDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitDistributionController extends Controller
{
    public function store(Request $request, Investor $investor)
    {
        $validated = $request->validate([
            'period' => 'required|string|max:50',
            'total_profit' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'accounts' => 'required|array',
            'accounts.*' => 'exists:investor_accounts,id'
        ]);

        DB::beginTransaction();
        try {
            // Hitung total investasi dari semua akun yang dipilih
            $totalInvestment = $investor->accounts()
                ->whereIn('id', $validated['accounts'])
                ->sum('total_investment');

            // Buat distribusi profit utama
            $distribution = $investor->profitDistributions()->create([
                'period' => $validated['period'],
                'total_profit' => $validated['total_profit'],
                'payment_date' => $validated['payment_date'],
                'status' => 'pending'
            ]);

            // Buat detail distribusi untuk setiap akun
            foreach ($validated['accounts'] as $accountId) {
                $account = $investor->accounts()->findOrFail($accountId);
                
                $profitAmount = $validated['total_profit'] * 
                              ($account->total_investment / $totalInvestment) * 
                              ($account->profit_percentage / 100);

                $distribution->details()->create([
                    'account_id' => $accountId,
                    'amount' => $profitAmount,
                    'status' => 'pending'
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Pembagian keuntungan berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan pembagian keuntungan: ' . $e->getMessage());
        }
    }

    public function markAsPaid(Investor $investor, $account, $distribution)
    {
        // Implementasi untuk menandai sebagai sudah dibayar
        // ...
    }
}