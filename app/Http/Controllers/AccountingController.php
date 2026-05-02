<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Journal;
use Illuminate\Http\Request;
use DB;
use Log;
use App\Services\ChartOfAccountService;

class AccountingController extends Controller
{
    protected $coaService;

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }
    public function index()
    {
        $accounts = $this->coaService->getAllAccounts();
        $journals = Journal::with('entries')->latest()->paginate(10);

        // Get transaction types for filtering
        $transactionTypes = Journal::select('transaction_type')
            ->distinct()
            ->pluck('transaction_type');

        return view('financial.accounting.index', compact(
            'accounts', 
            'journals', 
            'transactionTypes'
        ));
        
    }

    public function edit(ChartOfAccount $account)
    {
        return response()->json([
            'success' => true,
            'data' => $account,
            'accounts' => ChartOfAccount::where('id', '!=', $account->id)->get()
        ]);
    }

    public function update(Request $request, ChartOfAccount $account)
    {
        $request->validate([
            'code' => 'required|unique:chart_of_accounts,code,'.$account->id,
            'name' => 'required',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id'
        ]);

        $account->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil diperbarui'
        ]);
    }

    public function show(Journal $journal)
    {
        
        return response()->json([
            'success' => true,
            'data' => [
                'journal' => [
                    'reference' => $journal->reference,
                    'date_formatted' => $journal->date->format('d/m/Y'),
                    'description' => $journal->description,
                    'entries' => $journal->entries->map(function($entry) {
                        return [
                            'account' => [
                                'code' => $entry->account->code,
                                'name' => $entry->account->name
                            ],
                            'debit' => $entry->debit,
                            'credit' => $entry->credit,
                            'debit_formatted' => number_format($entry->debit, 2),
                            'credit_formatted' => number_format($entry->credit, 2),
                            'memo' => $entry->memo
                        ];
                    })
                ]
            ]
        ]);
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:chart_of_accounts',
            'name' => 'required',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id'
        ]);

        ChartOfAccount::create($request->all());

        return back()->with('success', 'Akun berhasil ditambahkan');
    }

    public function storeJournal(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit' => 'required_without:entries.*.credit|numeric|min:0',
            'entries.*.credit' => 'required_without:entries.*.debit|numeric|min:0'
        ]);

        // Validate total debit = total credit
        $totalDebit = collect($validated['entries'])->sum('debit');
        $totalCredit = collect($validated['entries'])->sum('credit');

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return back()->withErrors(['entries' => 'Total debit dan credit harus balance'])->withInput();
        }

        DB::transaction(function () use ($validated) {
            $journal = Journal::create([
                'date' => $validated['date'],
                'description' => $validated['description'],
                'transaction_type' => 'manual',
                'transaction_id' => DB::table('journals')->max('id') + 1
            ]);

            foreach ($validated['entries'] as $entry) {
                $journal->entries()->create([
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0
                ]);

            }
        });

        return back()->with('success', 'Jurnal berhasil diposting');
    }

    public function destroyAccount(ChartOfAccount $account)
    {
        DB::beginTransaction();
        try {
            // Cek referensi integrity
            $hasTransactions = $account->ledgerEntries()->exists() 
                || $account->journalEntries()->exists()
                || $account->children()->exists();

            if ($hasTransactions) {
                throw new \Exception('Akun tidak dapat dihapus karena memiliki transaksi atau sub-akun');
            }

            $account->delete();
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function showJournal(Journal $journal)
    {
        Log::info('jurnal: ' . $journal);
        // Return view instead of JSON
        return view('financial.accounting.journal_detail', [
            'journal' => $journal->load('entries.account')
        ]);
    }

    public function updateAccount(Request $request, ChartOfAccount $account)
    {
        $request->validate([
            'code' => 'required|unique:chart_of_accounts,code,'.$account->id,
            'name' => 'required',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id'
        ]);

        $account->update($request->all());

        return response()->json(['success' => true]);
    }

    // Add a new method to filter journals
    public function filterJournals(Request $request)
    {
        $query = Journal::with('entries');
        
        if ($request->type) {
            $query->where('transaction_type', $request->type);
        }
        
        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        
        if ($request->account_id) {
            $query->whereHas('entries', function($q) use ($request) {
                $q->where('account_id', $request->account_id);
            });
        }
        
        $journals = $query->latest()->paginate(10);

        return view('financial.accounting.index', [
            'journals' => $journals,
            'accounts' => ChartOfAccount::all(),
            'transactionTypes' => Journal::select('transaction_type')->distinct()->pluck('transaction_type')
        ]);
    }

    public function showJournalDetail($id)
    {
        $journal = Journal::with(['entries.account'])
            ->findOrFail($id);

        return view('financial.accounting.journal_detail_ledger', compact('journal'));
    }

    public function editJournal(Journal $journal)
    {
        $journal->load('entries.account');
        $accounts = ChartOfAccount::all();
        
        return view('financial.accounting.edit_journal', compact('journal', 'accounts'));
    }

    public function updateJournal(Request $request, Journal $journal)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit' => 'required_without:entries.*.credit|numeric|min:0',
            'entries.*.credit' => 'required_without:entries.*.debit|numeric|min:0',
            'entries.*.memo' => 'nullable|string|max:255'
        ]);

        // Validate total debit = total credit
        $totalDebit = collect($validated['entries'])->sum('debit');
        $totalCredit = collect($validated['entries'])->sum('credit');

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return back()->withErrors(['entries' => 'Total debit dan credit harus balance'])->withInput();
        }

        DB::transaction(function () use ($journal, $validated) {
            // Update journal header
            $journal->update([
                'date' => $validated['date'],
                'description' => $validated['description']
            ]);

            // Delete old entries
            $journal->entries()->delete();

            // Create new entries
            foreach ($validated['entries'] as $entry) {
                $journal->entries()->create([
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'memo' => $entry['memo'] ?? null
                ]);
            }
        });

        return redirect()->route('financial.accounting.index')->with('success', 'Jurnal berhasil diperbarui');
    }

    public function destroyJournal(Journal $journal)
    {
        DB::transaction(function () use ($journal) {
            $journal->entries()->delete();
            $journal->delete();
        });

        return redirect()->route('financial.accounting.index')->with('success', 'Jurnal berhasil dihapus');
    }
}