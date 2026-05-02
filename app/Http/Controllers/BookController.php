<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChartOfAccountService;
use App\Models\AccountingBook;
use App\Models\AccountOpeningBalance;
use App\Models\SubClass;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\JournalEntry;
use App\Models\Journal;
use App\Models\YearEndReport;
use App\Models\ActivityLog;
use PDF;
use Storage;

class BookController extends Controller
{
    protected $coaService;
    protected $creditAccounts = ['liability', 'equity', 'revenue'];

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }

    // 1. Inisiasi Buku Baru
    public function createBook()
    {
        return view('financial.book.create');
    }

    public function storeBook(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'currency' => 'required|string|in:IDR,USD',
        ]);

        $book = AccountingBook::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'currency' => $request->currency,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('financial.book.list')->with('success', 'Buku berhasil dibuat!');
    }

    // 2. Daftar Buku
    public function listBooks()
    {
        $books = AccountingBook::with('creator')
                ->orderBy('status')
                ->orderBy('start_date', 'desc')
                ->get();
                
        return view('financial.book.list', compact('books'));
    }

    // 3. Akun Buku dan Saldo Awal
    public function openingBalances($bookId)
    {
        $book = AccountingBook::findOrFail($bookId);
        $accounts = $this->coaService->getAllAccounts();
        
        // Ambil semua saldo awal untuk buku ini
        $openingBalances = AccountOpeningBalance::where('accounting_book_id', $bookId)
            ->get()
            ->keyBy('account_code'); // Ubah menjadi keyBy untuk akses lebih mudah

        return view('financial.book.opening_balances', [
            'book' => $book,
            'accounts' => $accounts,
            'openingBalances' => $openingBalances // Pastikan variabel ini dikirim ke view
        ]);
    }


    public function updateOpeningBalances(Request $request, $bookId)
{
    // Validasi dasar
    $request->validate([
        'balances' => 'required|array',
    ]);

    // Format ulang nilai input
    $formattedBalances = [];
    foreach ($request->balances as $accountCode => $balance) {
        $formattedBalances[$accountCode] = [
            'debit' => isset($balance['debit']) ? (float)str_replace('.', '', $balance['debit']) : 0,
            'credit' => isset($balance['credit']) ? (float)str_replace('.', '', $balance['credit']) : 0,
        ];
    }

    // Hitung total
    $totalDebit = 0;
    $totalCredit = 0;
    $validBalances = [];

    foreach ($formattedBalances as $accountCode => $balance) {
        $debit = (float)($balance['debit'] ?? 0);
        $credit = (float)($balance['credit'] ?? 0);
        
        if ($debit > 0 || $credit > 0) {
            $totalDebit += $debit;
            $totalCredit += $credit;
            
            $validBalances[] = [
                'account_code' => $accountCode,
                'debit' => $debit,
                'credit' => $credit
            ];
        }
    }

    // Validasi balance
    if (abs($totalDebit - $totalCredit) > 0.01) {
        return response()->json([
            'success' => false,
            'message' => 'Total debit ('.number_format($totalDebit, 0, ',', '.').') dan kredit ('.number_format($totalCredit, 0, ',', '.').') tidak balance! Selisih: '.number_format(abs($totalDebit - $totalCredit), 0, ',', '.')
        ], 422);
    }

    // Proses penyimpanan
    DB::beginTransaction();
    try {
        // Hapus saldo sebelumnya
        AccountOpeningBalance::where('accounting_book_id', $bookId)->delete();

        // Simpan saldo baru
        foreach ($validBalances as $balance) {
            AccountOpeningBalance::create([
                'accounting_book_id' => $bookId,
                'account_code' => $balance['account_code'],
                'debit' => $balance['debit'],
                'credit' => $balance['credit']
            ]);
        }

        // Update status buku
        AccountingBook::where('id', $bookId)->update(['status' => 'active']);

        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Saldo awal berhasil disimpan!',
            'redirect' => route('financial.book.list')
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        logger()->error('Error saving opening balances: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan: '.$e->getMessage()
        ], 500);
    }
}

    // 4. Sub Kelas
    public function subClasses()
    {
        $subClasses = SubClass::with('accountingBook')->get();
        $books = AccountingBook::all();
        return view('financial.book.sub_classes', compact('subClasses', 'books'));
    }

    public function createSubClass()
    {
        $books = AccountingBook::all();
        return view('financial.book.sub_class_form', compact('books'));
    }

    public function storeSubClass(Request $request)
    {
        $request->validate([
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'code' => 'required|string|max:20|unique:sub_classes,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        SubClass::create($request->all());

        return redirect()->route('financial.book.sub_classes')
            ->with('success', 'Subclass berhasil ditambahkan');
    }

    public function editSubClass($id)
    {
        $subClass = SubClass::findOrFail($id);
        $books = AccountingBook::all();
        return view('financial.book.sub_class_form', compact('subClass', 'books'));
    }

    public function updateSubClass(Request $request, $id)
    {
        $request->validate([
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'code' => 'required|string|max:20|unique:sub_classes,code,'.$id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $subClass = SubClass::findOrFail($id);
        $subClass->update($request->all());

        return redirect()->route('financial.book.sub_classes')
            ->with('success', 'Subclass berhasil diperbarui');
    }

    public function deleteSubClass($id)
    {
        $subClass = SubClass::findOrFail($id);
        $subClass->delete();

        return back()->with('success', 'Subclass berhasil dihapus');
    }

    // 6. Tutup Buku
    public function closeBook(Request $request, $bookId)
    {
        try {
            $book = AccountingBook::findOrFail($bookId);
            
            $validation = $this->validateBookForClosing($book);
            if (!$validation['success']) {
                throw new \Exception($validation['message']);
            }

            DB::beginTransaction();
            
            // 1. Generate laporan akhir tahun
            $reportData = $this->generateYearEndReports($bookId);
            
            // 2. Update status buku
            $book->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => auth()->id()
            ]);

            // 3. Buat saldo awal untuk buku tahun berikutnya (jika ada)
            $newBook = $this->createNextYearOpeningBalances($book);

            // Catat aktivitas
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'close_book',
                'description' => 'Menutup buku '.$book->name,
                'data' => [
                    'book_id' => $book->id,
                    'new_book_id' => $newBook ? $newBook->id : null
                ]
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Buku berhasil ditutup! Laporan akhir tahun telah dibuat dan buku baru untuk periode berikutnya telah disiapkan.'
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Error closing book: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup buku: '.$e->getMessage()
            ], 500);
        }
    }

    private function generateYearEndReports($bookId)
    {
        // Generate laporan yang ada saja
        $reports = [
            'profit_loss' => $this->generateProfitLossReport($bookId),
            'balance_sheet' => $this->generateBalanceSheet($bookId)
        ];
        
        // Coba generate cash flow report jika tidak error
        try {
            $reports['cash_flow'] = $this->generateCashFlowReport($bookId);
        } catch (\Exception $e) {
            logger()->error('Error generating cash flow report: ' . $e->getMessage());
            $reports['cash_flow_error'] = 'Gagal generate laporan arus kas: ' . $e->getMessage();
        }
        
        // Simpan ke database
        YearEndReport::create([
            'accounting_book_id' => $bookId,
            'report_data' => json_encode($reports),
            'generated_by' => auth()->id()
        ]);
        
        return $reports;
    }

    private function createNextYearOpeningBalances(AccountingBook $book)
    {
        // Cek apakah sudah ada buku untuk tahun berikutnya
        $nextYear = Carbon::parse($book->end_date)->addYear();
        $nextBookExists = AccountingBook::where('start_date', $nextYear->startOfYear())
            ->where('end_date', $nextYear->endOfYear())
            ->exists();
            
        if ($nextBookExists) {
            return null;
        }
        
        // Buat buku baru untuk tahun berikutnya
        $newBook = AccountingBook::create([
            'name' => $book->name.' '.$nextYear->year,
            'start_date' => $nextYear->startOfYear(),
            'end_date' => $nextYear->endOfYear(),
            'currency' => $book->currency,
            'status' => 'draft',
            'created_by' => auth()->id()
        ]);
        
        // Salin saldo akhir sebagai saldo awal tahun berikutnya
        $closingBalances = $this->getClosingBalances($book->id);
        
        foreach ($closingBalances as $balance) {
            // Pastikan tidak ada nilai negatif
            $debit = max(0, $balance['debit']);
            $credit = max(0, $balance['credit']);
            
            // Hanya simpan jika ada saldo
            if ($debit > 0 || $credit > 0) {
                AccountOpeningBalance::create([
                    'accounting_book_id' => $newBook->id,
                    'account_code' => $balance['account_code'],
                    'debit' => $debit,
                    'credit' => $credit
                ]);
            }
        }
        
        return $newBook;
    }

    private function getClosingBalances($bookId)
{
    $closingBalances = [];
    
    // Ambil semua akun yang memiliki saldo (baik dari saldo awal atau transaksi)
    $accountsWithBalance = $this->getAccountsWithBalance($bookId);
    
    foreach ($accountsWithBalance as $account) {
        $accountCode = $account['code'];
        $balance = $this->calculateAccountBalance($bookId, $accountCode);
        $accountType = $account['type'];
        
        $isCreditAccount = in_array($accountType, $this->creditAccounts);
        
        if ($isCreditAccount) {
            $closingBalances[] = [
                'account_code' => $accountCode,
                'debit' => 0,
                'credit' => max(0, $balance)
            ];
        } else {
            $closingBalances[] = [
                'account_code' => $accountCode,
                'debit' => max(0, $balance),
                'credit' => 0
            ];
        }
    }
    
    return $closingBalances;
}

private function getAccountsWithBalance($bookId)
{
    // Ambil semua akun yang memiliki saldo awal
    $openingAccounts = AccountOpeningBalance::where('accounting_book_id', $bookId)
        ->pluck('account_code')
        ->toArray();
    
    // Ambil semua akun yang memiliki transaksi - PERBAIKAN DI SINI
    $transactionAccounts = Journal::where('accounting_book_id', $bookId)
        ->join('journal_entries', 'journals.id', '=', 'journal_entries.journal_id')
        ->distinct()
        ->pluck('journal_entries.account_code')
        ->toArray();
    
    // Gabungkan dan ambil unik
    $allAccounts = array_unique(array_merge($openingAccounts, $transactionAccounts));
    
    // Ambil detail akun
    $accounts = [];
    foreach ($allAccounts as $accountCode) {
        $account = $this->coaService->getAccountByCode($accountCode);
        if ($account) {
            $accounts[] = $account;
        }
    }
    
    return $accounts;
}

    private function generateProfitLossReport($bookId)
    {
        // Ambil data dari jurnal untuk akun pendapatan dan biaya
        $revenues = $this->getAccountBalances($bookId, 'revenue');
        $expenses = $this->getAccountBalances($bookId, 'expense');
        
        // Hitung laba rugi
        $totalRevenue = $revenues->sum('balance');
        $totalExpense = $expenses->sum('balance');
        $netProfit = $totalRevenue - $totalExpense;
        
        return [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_profit' => $netProfit,
            'generated_at' => now()
        ];
    }

    private function generateBalanceSheet($bookId)
    {
        $assets = $this->getAccountBalances($bookId, 'asset');
        $liabilities = $this->getAccountBalances($bookId, 'liability');
        $equities = $this->getAccountBalances($bookId, 'equity');
        
        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquities = $equities->sum('balance');
        
        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equities' => $equities,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equities' => $totalEquities,
            'generated_at' => now()
        ];
    }

    private function getAccountBalances($bookId, $accountType)
{
    // Ambil semua akun dengan tipe tertentu
    $accounts = $this->coaService->getAccountsByType($accountType);
    
    $result = collect();
    
    foreach ($accounts as $account) {
        // Hitung saldo untuk setiap akun
        $balance = $this->calculateAccountBalance($bookId, $account['code']);
        
        if ($balance != 0) {
            $result->push([
                'code' => $account['code'],
                'name' => $account['name'],
                'balance' => $balance
            ]);
        }
    }
    
    return $result;
}

    private function calculateAccountBalance($bookId, $accountCode)
    {
        // Ambil saldo awal
        $opening = AccountOpeningBalance::where('accounting_book_id', $bookId)
            ->where('account_code', $accountCode)
            ->first();
        
        $debit = $opening ? (float)$opening->debit : 0;
        $credit = $opening ? (float)$opening->credit : 0;
        
        // Ambil semua transaksi untuk akun ini - PERBAIKAN DI SINI
        $journals = Journal::where('accounting_book_id', $bookId)
            ->whereHas('entries', function($query) use ($accountCode) {
                $query->where('account_code', $accountCode);
            })
            ->select(
                DB::raw('COALESCE(SUM(journal_entries.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(journal_entries.credit), 0) as total_credit')
            )
            ->join('journal_entries', 'journals.id', '=', 'journal_entries.journal_id')
            ->first();
        
        $debit += (float)$journals->total_debit;
        $credit += (float)$journals->total_credit;
        
        // Tentukan apakah ini akun debit atau kredit
        $account = $this->coaService->getAccountByCode($accountCode);
        $isCreditAccount = in_array($account['type'], $this->creditAccounts);
        
        return $isCreditAccount ? ($credit - $debit) : ($debit - $credit);
    }

    private function validateBookBalances($bookId)
    {
        // Ambil semua akun
        $accounts = $this->coaService->getAllAccounts();
        
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($accounts as $account) {
            $balance = $this->calculateAccountBalance($bookId, $account['code']);
            
            if (in_array($account['type'], ['asset', 'expense'])) {
                $totalDebit += $balance;
            } else {
                $totalCredit += $balance;
            }
        }
        
        return abs($totalDebit - $totalCredit) < 0.01; // Toleransi kecil untuk floating point
    }


    private function validateBookForClosing(AccountingBook $book)
    {
        $errors = [];
        
        // Validasi status buku
        if ($book->status !== 'active') {
            $errors[] = 'Buku sudah ditutup atau dalam status draft';
        }

        // Validasi periode buku
        if (now()->lt($book->end_date)) {
            $errors[] = 'Buku belum mencapai tanggal akhir periode';
        }

        // Validasi transaksi yang belum divalidasi - PERBAIKAN DI SINI
        $unvalidatedEntries = Journal::where('accounting_book_id', $book->id)
            ->whereHas('entries', function($query) {
                $query->where('is_validated', false);
            })
            ->exists();
            
        if ($unvalidatedEntries) {
            $errors[] = 'Terdapat transaksi yang belum divalidasi';
        }
        
        // Validasi balance semua akun
        if (!$this->validateBookBalances($book->id)) {
            $errors[] = 'Buku tidak balance';
        }

        if (count($errors) > 0) {
            return [
                'success' => false,
                'message' => 'Buku tidak dapat ditutup karena:<br>- ' . implode('<br>- ', $errors)
            ];
        }

        return ['success' => true];
    }

    public function closeConfirmation($bookId)
    {
        try {
            $book = AccountingBook::findOrFail($bookId);
            
            $validation = $this->validateBookForClosing($book);
            if (!$validation['success']) {
                throw new \Exception($validation['message']);
            }
            
            return view('financial.book.close_confirmation_modal', compact('book'));
                
        } catch (\Exception $e) {
            // Return view kosong karena error akan ditangani oleh JavaScript
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    // 7. Backup Data
    public function backupData($bookId)
    {
        $book = AccountingBook::findOrFail($bookId);
        
        // Generate PDF laporan
        $pdf = PDF::loadView('financial.book.reports.full_report', [
            'book' => $book,
            'profitLoss' => $this->generateProfitLossReport($bookId),
            'balanceSheet' => $this->generateBalanceSheet($bookId),
            'cashFlow' => $this->generateCashFlowReport($bookId)
        ]);
        
        $filename = 'backup_'.$book->name.'_'.now()->format('YmdHis').'.pdf';
        
        // Simpan ke storage
        Storage::put('backups/'.$filename, $pdf->output());
        
        // Catat aktivitas backup
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'backup',
            'description' => 'Backup data buku '.$book->name,
            'data' => ['filename' => $filename]
        ]);
        
        return $pdf->download($filename);
    }

    public function deleteBook($id)
    {
        try {
            $book = AccountingBook::findOrFail($id);
            
            // Cek jika buku sudah memiliki transaksi
            $hasTransactions = Journal::where('accounting_book_id', $id)->exists();
            
            if ($hasTransactions) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku tidak bisa dihapus karena sudah memiliki transaksi'
                ]);
            }

            $book->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Buku berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus buku: ' . $e->getMessage()
            ], 500);
        }
    }

    private function isCreditAccount($accountType)
    {
        return in_array($accountType, $this->creditAccounts);
    }
    
    public function getAccountType(string $accountCode): string
    {
        $account = $this->getAccountByCode($accountCode);
        return $account['type'] ?? 'asset'; // Default to asset if not found
    }

    public function editBook($id)
    {
        $book = AccountingBook::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $book,
            'html' => view('financial.book.edit_modal', compact('book'))->render()
        ]);
    }

    public function updateBook(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'currency' => 'required|string|in:IDR,USD',
        ]);

        $book = AccountingBook::findOrFail($id);

        // Validasi bahwa buku belum ditutup
        if ($book->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Buku yang sudah ditutup tidak dapat diubah'
            ], 403);
        }

        try {
            $book->update($request->only(['name', 'start_date', 'end_date', 'currency']));
            
            return response()->json([
                'success' => true,
                'message' => 'Buku berhasil diperbarui',
                'data' => $book
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui buku: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateCashFlowReport($bookId)
    {
        // Aktivitas Operasi
        $operatingActivities = $this->getCashFlowByType($bookId, 'operating');
        
        // Aktivitas Investasi
        $investingActivities = $this->getCashFlowByType($bookId, 'investing');
        
        // Aktivitas Pendanaan
        $financingActivities = $this->getCashFlowByType($bookId, 'financing');
        
        // Total arus kas bersih
        $netCashFlow = $operatingActivities['net'] + $investingActivities['net'] + $financingActivities['net'];
        
        // Saldo awal kas
        $openingCash = AccountOpeningBalance::where('accounting_book_id', $bookId)
            ->where('account_code', 'like', '1.01.01%') // Kas dan setara kas
            ->sum('debit') - AccountOpeningBalance::where('accounting_book_id', $bookId)
            ->where('account_code', 'like', '1.01.01%')
            ->sum('credit');
        
        // Saldo akhir kas
        $endingCash = $openingCash + $netCashFlow;
        
        return [
            'operating_activities' => $operatingActivities,
            'investing_activities' => $investingActivities,
            'financing_activities' => $financingActivities,
            'net_cash_flow' => $netCashFlow,
            'opening_cash' => $openingCash,
            'ending_cash' => $endingCash,
            'generated_at' => now()
        ];
    }

    /**
     * Get cash flow by activity type
     */
    private function getCashFlowByType($bookId, $activityType)
    {
        // Mapping akun berdasarkan jenis aktivitas
        $accountMappings = $this->getCashFlowAccountMappings($activityType);
        
        $cashIn = 0;
        $cashOut = 0;
        
        foreach ($accountMappings as $accountCode => $type) {
            $balance = $this->calculateAccountBalance($bookId, $accountCode);
            
            if ($type === 'in') {
                $cashIn += $balance;
            } else {
                $cashOut += abs($balance);
            }
        }
        
        return [
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'net' => $cashIn - $cashOut
        ];
    }

    /**
     * Mapping akun untuk laporan arus kas
     */
    private function getCashFlowAccountMappings($activityType)
    {
        $mappings = [
            'operating' => [
                // Pendapatan
                '4.01.01' => 'in', '4.01.02' => 'in', '4.01.03' => 'in',
                '4.02.01' => 'out', '4.02.02' => 'out', '4.02.03' => 'out',
                '4.03.01' => 'out', '4.03.02' => 'out', '4.03.03' => 'out',
                // Biaya
                '5.01.01' => 'out', '5.01.02' => 'out', '5.01.03' => 'out',
                '6.01.01.01' => 'out', '6.01.01.02' => 'out', '6.01.01.03' => 'out',
                '6.01.01.04' => 'out', '6.01.01.05' => 'out', '6.01.01.06' => 'out',
                '6.01.01.07' => 'out', '6.01.02.01' => 'out', '6.01.02.02' => 'out',
                '6.01.02.03' => 'out', '6.01.02.04' => 'out', '6.01.03.01.01' => 'out',
                '6.01.03.01.02.01' => 'out', '6.01.03.01.02.02' => 'out',
                '6.01.03.02' => 'out', '6.01.04' => 'out', '6.01.05' => 'out',
                '6.01.06.01.01' => 'out', '6.01.06.01.02' => 'out',
                '6.01.06.01.03.01' => 'out', '6.01.06.01.03.02' => 'out',
                '6.01.06.01.03.03' => 'out', '6.01.06.01.03.04' => 'out',
                '6.01.06.01.04.01' => 'out', '6.01.06.01.04.02' => 'out',
                '6.01.06.01.04.03' => 'out', '6.01.06.02.01' => 'out',
                '6.01.06.02.02' => 'out', '6.01.06.02.03' => 'out',
                '6.01.06.02.04' => 'out', '6.01.06.03' => 'out',
                '6.01.06.04' => 'out', '6.01.06.05' => 'out',
                '6.01.06.06' => 'out', '6.01.07' => 'out',
                '6.01.08' => 'out', '6.01.09.01' => 'out',
                '6.01.09.02' => 'out', '6.01.09.03' => 'out',
                '6.01.09.04' => 'out', '6.01.09.05' => 'out',
                '6.02.01' => 'out', '6.02.02' => 'out',
                // Pajak
                '9.01' => 'out', '9.02' => 'out', '9.03' => 'out'
            ],
            'investing' => [
                // Pembelian aset tetap
                '1.02.02.01' => 'out', '1.02.02.02' => 'out',
                '1.02.03.01' => 'out', '1.02.03.02' => 'out',
                // Penjualan aset tetap
                '7.01.02' => 'in',
                // Investasi
                '1.02.05' => 'out', '1.02.06' => 'out'
            ],
            'financing' => [
                // Pendanaan
                '2.01.07' => 'in', '2.02.01' => 'in',
                // Pembayaran utang
                '2.01.07' => 'out', '2.02.01' => 'out',
                // Dividen
                '2.01.05' => 'out', '3.02.02' => 'out'
            ]
        ];
        
        return $mappings[$activityType] ?? [];
    }

    public function accountList()
    {
        $accounts = $this->coaService->getAccountTree();
        $parentAccounts = $this->coaService->getParentAccounts();
        
        return view('financial.book.accounts', [
            'accounts' => $accounts,
            'parentAccounts' => $parentAccounts
        ]);
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'parent_code' => 'nullable|string',
            'code' => 'required|string',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
        ]);

        try {
            // Generate kode otomatis jika tidak disediakan
            $code = $request->code;
            if (empty($code)) {
                $code = $this->coaService->generateAccountCode($request->parent_code);
            }

            $accountData = [
                'code' => $code,
                'name' => $request->name,
                'type' => $request->type,
                'is_active' => true,
            ];

            $configPath = config_path('accounts.php');
            
            // Check if file is writable
            if (!is_writable($configPath)) {
                throw new \Exception("File config tidak dapat ditulis. Pastikan file config/accounts.php memiliki permission yang tepat.");
            }

            $accounts = config('accounts.accounts');

            if ($request->parent_code) {
                $this->addAccountToParent($accounts, $request->parent_code, $accountData);
            } else {
                $accounts[] = $accountData;
            }

            // Backup current config
            $backupContent = file_get_contents($configPath);

            // Try to write new config
            $content = "<?php\n\nreturn [\n    'accounts' => ".$this->arrayToString($accounts).",\n];";
            
            if (file_put_contents($configPath, $content) === false) {
                // Restore backup if write fails
                file_put_contents($configPath, $backupContent);
                throw new \Exception("Gagal menulis ke file config.");
            }

            return response()->json(['success' => true, 'message' => 'Akun berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan akun: '.$e->getMessage()], 500);
        }
    }

    public function deleteAccount($code)
    {
        try {
            $configPath = config_path('accounts.php');
            $accounts = config('accounts.accounts');

            $this->removeAccount($accounts, $code);

            // Write back to config file
            $content = "<?php\n\nreturn [\n    'accounts' => ".$this->arrayToString($accounts).",\n];";
            file_put_contents($configPath, $content);

            return response()->json(['success' => true, 'message' => 'Akun berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus akun: '.$e->getMessage()], 500);
        }
    }

    // Helper methods
    private function addAccountToParent(&$accounts, $parentCode, $accountData)
    {
        foreach ($accounts as &$account) {
            if ($account['code'] === $parentCode) {
                if (!isset($account['children'])) {
                    $account['children'] = [];
                }
                $account['children'][] = $accountData;
                return true;
            }

            if (!empty($account['children'])) {
                if ($this->addAccountToParent($account['children'], $parentCode, $accountData)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function removeAccount(&$accounts, $code)
    {
        foreach ($accounts as $key => &$account) {
            if ($account['code'] === $code) {
                unset($accounts[$key]);
                return true;
            }

            if (!empty($account['children'])) {
                if ($this->removeAccount($account['children'], $code)) {
                    if (empty($account['children'])) {
                        unset($account['children']);
                    }
                    return true;
                }
            }
        }

        return false;
    }

    private function arrayToString($array, $indent = 4)
    {
        $out = "[\n";
        foreach ($array as $key => $value) {
            $out .= str_repeat(' ', $indent);
            
            if (is_string($key)) {
                $out .= "'$key' => ";
            }

            if (is_array($value)) {
                $out .= $this->arrayToString($value, $indent + 4);
            } elseif (is_bool($value)) {
                $out .= $value ? 'true' : 'false';
            } elseif (is_string($value)) {
                $out .= "'$value'";
            } elseif (is_null($value)) {
                $out .= 'null';
            } else {
                $out .= $value;
            }

            $out .= ",\n";
        }
        $out .= str_repeat(' ', $indent - 4) . "]";
        return $out;
    }

    public function getParentAccounts()
    {
        $parents = [];
        $this->collectParentAccounts($this->accounts, $parents);
        return $parents;
    }

    protected function collectParentAccounts($accounts, &$result)
    {
        foreach ($accounts as $account) {
            if (!empty($account['children'])) {
                $result[] = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'type' => $account['type']
                ];
                $this->collectParentAccounts($account['children'], $result);
            }
        }
    }

    public function generateCode(Request $request)
    {
        try {
            $parentCode = $request->parent_code;
            $code = $this->coaService->generateAccountCode($parentCode);
            
            return response()->json([
                'success' => true,
                'code' => $code
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}