<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\AccountingBook;
use App\Models\SubClass;
use App\Services\ChartOfAccountService;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\AccountOpeningBalance;
use Illuminate\Support\Facades\Cache;
use Log;

class JournalController extends Controller
{
    protected $coaService;
    protected $creditAccounts = ['liability', 'equity', 'revenue'];

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }

    // Daftar Jurnal
    public function index(Request $request)
    {
        // Query utama untuk data jurnal
        $query = Journal::with(['accountingBook', 'entries', 'creator'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filtering
        if ($request->filled('book_id')) {
            $query->where('accounting_book_id', $request->book_id);
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('account_code')) {
            $query->whereHas('entries', function($q) use ($request) {
                $q->where('account_code', 'like', $request->account_code.'%');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('journal_number', 'like', "%$search%")
                ->orWhere('reference_number', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
            });
        }

        $journals = $query->paginate(25);
        $books = AccountingBook::all();

        $booksActive = $books->where('status', 'active');
        $subClasses = SubClass::all();
        $accounts = $this->coaService->getAllAccounts();
        
        // Query untuk total debit dan kredit
        $totalsQuery = JournalEntry::query();
        
        // Terapkan filter yang sama dengan query utama
        if ($request->filled('book_id')) {
            $totalsQuery->whereHas('journal', function($q) use ($request) {
                $q->where('accounting_book_id', $request->book_id);
            });
        }
        if ($request->filled('date_from')) {
            $totalsQuery->whereHas('journal', function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->date_from);
            });
        }
        if ($request->filled('date_to')) {
            $totalsQuery->whereHas('journal', function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->date_to);
            });
        }
        if ($request->filled('account_code')) {
            $totalsQuery->where('account_code', 'like', $request->account_code.'%');
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $totalsQuery->whereHas('journal', function($q) use ($search) {
                $q->where(function($q2) use ($search) {
                    $q2->where('journal_number', 'like', "%$search%")
                    ->orWhere('reference_number', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
                });
            });
        }

        // Hitung total
        $totals = $totalsQuery->select(
            DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
            DB::raw('COALESCE(SUM(credit), 0) as total_credit')
        )->first();

        // Siapkan account names
        $accountNames = [];
        foreach ($journals as $journal) {
            foreach ($journal->entries as $entry) {
                if (!isset($accountNames[$entry->account_code])) {
                    $accountNames[$entry->account_code] = $this->coaService->getAccountByCode($entry->account_code)['name'] ?? '';
                }
            }
        }

        $accountOptions = collect($this->coaService->getAllAccounts())
            ->filter(fn($account) => !isset($account['children']))
            ->pluck('name', 'code')
            ->toArray();

        return view('financial.journal.index', compact('journals', 'books', 'totals', 'booksActive', 'subClasses', 'accounts', 'accountNames', 'accountOptions'));
    }

    // Simpan Jurnal
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:50',
            'description' => 'required|string|max:255',
            'entries' => 'required|array|min:2',
            'entries.*.account_code' => 'required',
            'entries.*.sub_class_id' => 'nullable|exists:sub_classes,id',
            'entries.*.posting_type' => 'required|in:increase,decrease',
            'entries.*.debit' => 'required_without:entries.*.credit|numeric|min:0',
            'entries.*.credit' => 'required_without:entries.*.debit|numeric|min:0',
            'is_cash_flow' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi balance
        $totalDebit = 0;
        $totalCredit = 0;
        $entries = [];

        foreach ($request->entries as $entry) {
            $account = $this->coaService->getAccountByCode($entry['account_code']);
            $isCreditAccount = in_array($account['type'], $this->creditAccounts);

            if ($entry['posting_type'] === 'increase') {
                if ($isCreditAccount) {
                    $credit = $entry['amount'];
                    $debit = 0;
                } else {
                    $debit = $entry['amount'];
                    $credit = 0;
                }
            } else {
                if ($isCreditAccount) {
                    $debit = $entry['amount'];
                    $credit = 0;
                } else {
                    $credit = $entry['amount'];
                    $debit = 0;
                }
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $entries[] = [
                'account_code' => $entry['account_code'],
                'sub_class_id' => $entry['sub_class_id'] ?? null,
                'posting_type' => $entry['posting_type'],
                'debit' => $debit,
                'credit' => $credit,
                'account_type' => $account['type']
            ];
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Jurnal tidak balance. Total debit: '.number_format($totalDebit, 2).', total kredit: '.number_format($totalCredit, 2)
            ], 422);
        }

        DB::beginTransaction();
        try {
            $book = AccountingBook::findOrFail($request->accounting_book_id);
            $journal = Journal::create([
                'journal_number' => $this->generateJournalNumber($book),
                'transaction_date' => $request->transaction_date,
                'reference_number' => $request->reference_number ?? $this->generateReferenceNumber(),
                'description' => $request->description,
                'accounting_book_id' => $request->accounting_book_id,
                'created_by' => auth()->id(),
                'is_cash_flow' => $request->is_cash_flow
            ]);

            foreach ($entries as $entry) {
                JournalEntry::create([
                    'journal_id' => $journal->id,
                    'account_code' => $entry['account_code'],
                    'sub_class_id' => $entry['sub_class_id'],
                    'posting_type' => $entry['posting_type'],
                    'debit' => $entry['debit'],
                    'credit' => $entry['credit']
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jurnal berhasil disimpan',
                'redirect' => route('financial.journal.index') // Pastikan menggunakan route yang benar
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Journal creation failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jurnal: '.$e->getMessage()
            ], 500);
        }
    }

    public function edit(Journal $journal)
    {
        $entries = $journal->entries->map(function($entry) {
            $account = $this->coaService->getAccountByCode($entry->account_code);
            
            return [
                'account_code' => $entry->account_code,
                'account' => $account,
                'sub_class_id' => $entry->sub_class_id,
                'posting_type' => $entry->posting_type,
                'debit' => (float)$entry->debit,  // Pastikan sebagai float
                'credit' => (float)$entry->credit, // Pastikan sebagai float
                'amount' => $entry->debit > 0 ? (float)$entry->debit : (float)$entry->credit
            ];
        });

        return response()->json([
            'id' => $journal->id,
            'accounting_book_id' => $journal->accounting_book_id,
            'transaction_date' => $journal->transaction_date->format('Y-m-d'),
            'reference_number' => $journal->reference_number,
            'description' => $journal->description,
            'is_cash_flow' => $journal->is_cash_flow,
            'entries' => $entries
        ]);
    }

    public function update(Request $request, Journal $journal)
    {
        \Log::debug('Update Journal Data:', $request->all());

        $validator = Validator::make($request->all(), [
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:50',
            'description' => 'required|string|max:255',
            'entries' => 'required|array|min:2',
            'entries.*.account_code' => 'required',
            'entries.*.sub_class_id' => 'nullable|exists:sub_classes,id',
            'entries.*.posting_type' => 'required|in:increase,decrease',
            'entries.*.amount' => 'required|numeric|min:0',
            'is_cash_flow' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi balance
        $totalDebit = 0;
        $totalCredit = 0;
        $entries = [];

        foreach ($request->entries as $entry) {
            $account = $this->coaService->getAccountByCode($entry['account_code']);
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account dengan kode '.$entry['account_code'].' tidak ditemukan'
                ], 422);
            }

            $isCreditAccount = in_array($account['type'], $this->creditAccounts);

            if ($entry['posting_type'] === 'increase') {
                if ($isCreditAccount) {
                    $credit = $entry['amount'];
                    $debit = 0;
                } else {
                    $debit = $entry['amount'];
                    $credit = 0;
                }
            } else {
                if ($isCreditAccount) {
                    $debit = $entry['amount'];
                    $credit = 0;
                } else {
                    $credit = $entry['amount'];
                    $debit = 0;
                }
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $entries[] = [
                'account_code' => $entry['account_code'],
                'sub_class_id' => $entry['sub_class_id'] ?? null,
                'posting_type' => $entry['posting_type'],
                'debit' => $debit,
                'credit' => $credit,
                'account_type' => $account['type']
            ];
        }

        // Cek balance
        if (abs($totalDebit - $totalCredit) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Jurnal tidak balance. Total debit: '.number_format($totalDebit, 2).', total kredit: '.number_format($totalCredit, 2)
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update journal header
            $journal->update([
                'accounting_book_id' => $request->accounting_book_id,
                'transaction_date' => $request->transaction_date,
                'reference_number' => $request->reference_number,
                'description' => $request->description,
                'is_cash_flow' => $request->is_cash_flow ?? true
            ]);

            // Hapus entries lama
            $journal->entries()->delete();

            // Buat entries baru
            foreach ($entries as $entry) {
                $journal->entries()->create([
                    'account_code' => $entry['account_code'],
                    'sub_class_id' => $entry['sub_class_id'],
                    'posting_type' => $entry['posting_type'],
                    'debit' => $entry['debit'],
                    'credit' => $entry['credit']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jurnal berhasil diperbarui',
                'redirect' => route('financial.journal.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal update jurnal: '.$e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jurnal: '.$e->getMessage()
            ], 500);
        }
    }

    // Generate nomor jurnal
    private function generateJournalNumber($book)
    {
        $prefix = 'JRN-' . $book->start_date->format('Ym') . '-';
        $maxAttempts = 5;
        $attempt = 0;

        do {
            try {
                DB::beginTransaction();

                Log::info("Generating journal number attempt {$attempt} for prefix {$prefix}");

                // Get the last journal number with lock
                $lastEntry = Journal::where('journal_number', 'like', $prefix.'%')
                    ->lockForUpdate()
                    ->orderBy('journal_number', 'desc')
                    ->first();

                $nextNumber = $lastEntry 
                    ? (int)str_replace($prefix, '', $lastEntry->journal_number) + 1
                    : 1;

                $journalNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // Verify uniqueness before returning
                if (!Journal::where('journal_number', $journalNumber)->exists()) {
                    DB::commit();
                    Log::info("Generated unique journal number: {$journalNumber}");
                    return $journalNumber;
                }

                Log::warning("Duplicate detected, retrying: {$journalNumber}");
                DB::rollBack();
                $attempt++;
                usleep(100000); // 100ms delay between attempts

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Journal number generation failed: " . $e->getMessage());
                throw $e;
            }
        } while ($attempt < $maxAttempts);

        throw new \Exception("Failed to generate unique journal number after {$maxAttempts} attempts");
    }

    private function generateReferenceNumber()
    {
        $month = Carbon::now()->format('m');
        $lastJournal = Journal::where('reference_number', 'like', $month.'-%')
            ->orderBy('reference_number', 'desc')
            ->first();

        $nextNumber = $lastJournal 
            ? (int)explode('-', $lastJournal->reference_number)[1] + 1
            : 1;

        return $month . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function generateReference()
    {
        return response()->json([
            'reference_number' => $this->generateReferenceNumber()
        ]);
    }

    // Validasi Jurnal
    public function validateJournal(Request $request, $id)
    {
        $journal = Journal::findOrFail($id); // Ambil journal secara manual
    
        \Log::info('Attempting to validate journal:', [
            'journal_id' => $journal->id, 
            'current_status' => $journal->is_validated,
            'user_id' => auth()->id()
        ]);

        DB::beginTransaction();
        try {
            if ($journal->is_validated) {
                \Log::warning('Journal already validated', ['journal_id' => $journal->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Jurnal sudah divalidasi sebelumnya'
                ], 422);
            }

            $result = $journal->validateJournal();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Jurnal berhasil divalidasi',
                'redirect' => route('financial.journal.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Validation failed:', [
                'error' => $e->getMessage(),
                'journal_id' => $journal->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memvalidasi jurnal: '.$e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $journal = Journal::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Hapus semua entries terkait
            $journal->entries()->delete();
            
            // Hapus journal
            $journal->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Jurnal berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jurnal: '.$e->getMessage()
            ], 500);
        }
    }

    // Hapus Semua Jurnal (dengan filter)
    public function destroyAll(Request $request)
    {
        $query = JournalEntry::query();
        
        if ($request->filled('book_id')) {
            $query->where('accounting_book_id', $request->book_id);
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('is_validated')) {
            $query->where('is_validated', $request->is_validated);
        }

        DB::beginTransaction();
        try {
            $count = $query->count();
            $query->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus $count jurnal"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jurnal: '.$e->getMessage()
            ], 500);
        }
    }

    // Get account balance
    public function getAccountBalance(Request $request)
    {
        $request->validate([
            'account_code' => 'required',
            'book_id' => 'required|exists:accounting_books,id',
            'date' => 'required|date'
        ]);

        $account = $this->coaService->getAccountByCode($request->account_code);
        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan'
            ], 404);
        }

        $isCreditAccount = in_array($account['type'], $this->creditAccounts);

        // Hitung saldo awal
        $openingBalance = AccountOpeningBalance::where('accounting_book_id', $request->book_id)
            ->where('account_code', $request->account_code)
            ->first();

        $debit = $openingBalance ? $openingBalance->debit : 0;
        $credit = $openingBalance ? $openingBalance->credit : 0;

        $totals = JournalEntry::join('journals', 'journal_entries.journal_id', '=', 'journals.id')
            ->where('journals.accounting_book_id', $request->book_id)
            ->where('journal_entries.account_code', $request->account_code)
            ->where('journals.transaction_date', '<=', $request->date)
            ->select(
                DB::raw('COALESCE(SUM(journal_entries.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(journal_entries.credit), 0) as total_credit')
            )->first();

        $debit += $totals->total_debit ?? 0;
        $credit += $totals->total_credit ?? 0;

        $balance = $isCreditAccount ? ($credit - $debit) : ($debit - $credit);

        return response()->json([
            'success' => true,
            'balance' => $balance,
            'formatted_balance' => number_format($balance, 2),
            'is_credit_account' => $isCreditAccount,
            'total_debit' => $totals->total_debit,
            'total_credit' => $totals->total_credit,
            'formatted_debit' => number_format($totals->total_debit, 2),
            'formatted_credit' => number_format($totals->total_credit, 2)
        ]);
    }

    public function validateSelected(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:journals,id'
        ]);

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($request->ids as $id) {
                $journal = Journal::findOrFail($id);
                if (!$journal->is_validated) {
                    $journal->validateJournal();
                    $count++;
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Berhasil memvalidasi $count jurnal"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memvalidasi jurnal: '.$e->getMessage()
            ], 500);
        }
    }

    public function deleteSelected(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:journals,id'
        ]);

        DB::beginTransaction();
        try {
            // Hapus entries terkait
            JournalEntry::whereIn('journal_id', $request->ids)->delete();
            
            // Hapus journals
            Journal::whereIn('id', $request->ids)->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' jurnal berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jurnal: '.$e->getMessage()
            ], 500);
        }
    }

    public function searchAccounts(Request $request)
    {
        $term = strtolower($request->term);
        $allAccounts = $this->coaService->getAllAccounts();
        $results = [];

        // Fungsi rekursif untuk mencari di semua level
        function searchInAccounts($accounts, $term, &$results) {
            foreach ($accounts as $account) {
                if (strpos(strtolower($account['code']), $term) !== false || 
                    strpos(strtolower($account['name']), $term) !== false) {
                    $results[] = [
                        'label' => "{$account['code']} - {$account['name']}",
                        'value' => "{$account['code']} - {$account['name']}",
                        'code' => $account['code'],
                        'type' => $account['type'],
                        'name' => $account['name']
                    ];
                }
                
                if (isset($account['children'])) {
                    searchInAccounts($account['children'], $term, $results);
                }
            }
        }

        searchInAccounts($allAccounts, $term, $results);

        // Hapus duplikat berdasarkan kode akun
        $uniqueResults = [];
        $codes = [];
        
        foreach ($results as $result) {
            if (!in_array($result['code'], $codes)) {
                $codes[] = $result['code'];
                $uniqueResults[] = $result;
            }
        }

        return response()->json($uniqueResults);
    }
}