<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\InvestorAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class InvestorAccountController extends Controller
{
    public function index(Investor $investor)
    {
        return response()->json($investor->accounts);
    }

    public function store(Request $request, Investor $investor)
    {
        Log::debug('Account Store Request', [
            'data' => $request->all(),
            'investor_id' => $investor->id
        ]);

        $validated = $request->validate([
            'account_number' => 'required|string',
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'initial_balance' => 'required|numeric|min:0',
            'profit_percentage' => 'required|numeric|min:0|max:100',
            'date' => 'required|date',
            'tempo' => 'nullable|date'
        ]);

        try {
            $account = $investor->accounts()->create([
                'account_number' => $validated['account_number'],
                'bank_name' => $validated['bank_name'],
                'account_name' => $validated['account_name'],
                'initial_balance' => $validated['initial_balance'],
                'current_balance' => 0,
                'profit_percentage' => $validated['profit_percentage'],
                'date' => $validated['date'],
                'tempo' => $validated['tempo'],
                'status' => 'active'
                
            ]);

            // Catat investasi awal
            $account->investments()->create([
                'date' => $request->date,
                'type' => 'investment',
                'amount' => $request->initial_balance,
                'description' => 'Investasi awal'
            ]);

            return redirect()->back()
                ->with('success', 'Rekening berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan rekening: '.$e->getMessage())
                ->withInput();
        }
    }

    public function show($investorId, $accountId)
    {
        $investor = Investor::findOrFail($investorId);
        $account = $investor->accounts()->findOrFail($accountId);
        
        return view('irp.investor.account_show', compact('investor', 'account'));
    }

    public function edit($investorId, $accountId)
    {
        $investor = Investor::findOrFail($investorId);
        $account = $investor->accounts()->findOrFail($accountId);
        
        return view('irp.investor.account_edit', compact('investor', 'account'));
    }

    public function update(Request $request, Investor $investor, $accountId)
    {
        $account = $investor->accounts()->findOrFail($accountId);
        
        $validated = $request->validate([
            'account_number' => 'required|unique:investor_accounts,account_number,'.$account->id,
            'bank_name' => 'required',
            'account_name' => 'required',
            'initial_balance' => 'required|numeric|min:0',
            'profit_percentage' => 'required|numeric|min:0|max:100',
            'saldo_tertahan' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'date' => 'required|date',
            'tempo' => 'nullable|date',
            'notes' => 'nullable'
        ]);

        $account->update($validated);

        return redirect()->route('irp.investor.show', [
            'investor' => $investor->id,
            'account' => $account->id
        ])->with('success', 'Data rekening berhasil diperbarui');
    }

    public function destroy(Investor $investor, $accountId)
    {
        $account = $investor->accounts()->findOrFail($accountId);
        
        // Tambahkan pengecekan jika ada transaksi terkait
        if ($account->investments()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus rekening karena memiliki transaksi terkait');
        }
        
        $account->delete();
        
        return redirect()->back()
            ->with('success', 'Rekening berhasil dihapus');
    }
}