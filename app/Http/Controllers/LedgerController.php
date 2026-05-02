<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalEntry;
use App\Models\AccountingBook;
use App\Models\AccountOpeningBalance;
use App\Services\ChartOfAccountService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LedgerController extends Controller
{
    protected $coaService;

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }

    public function index(Request $request)
    {
        // Set default values
        $bookId = $request->book_id ?? AccountingBook::active()->first()->id ?? null;
        $accountCode = $request->account_code ?? null;
        
        // Validate if book_id exists
        if (!$bookId) {
            return redirect()->route('financial.journal.index')
                ->with('error', 'Tidak ada tahun buku aktif. Silakan buat tahun buku terlebih dahulu.');
        }

        try {
            // Get accounting book data
            $accountingBook = AccountingBook::findOrFail($bookId);
            $books = AccountingBook::orderBy('start_date', 'desc')->get();

            // Get all accounts for dropdown
            $accounts = $this->coaService->getAllAccounts();
            $accountOptions = collect($accounts)
                ->filter(fn($account) => !isset($account['children']))
                ->mapWithKeys(fn($account) => [$account['code'] => $account['code'] . ' - ' . $account['name']])
                ->toArray();

            // Parse dates
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : null;
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : null;

            // If account code is selected
            if ($accountCode) {
                if ($accountCode === 'all') {
                    return $this->showAllAccountsLedger(
                        $accountingBook, 
                        $books, 
                        $accountOptions, 
                        $dateFrom, 
                        $dateTo
                    );
                }
                $account = $this->coaService->getAccountByCode($accountCode);
                
                if (!$account) {
                    return redirect()->back()
                        ->with('error', 'Akun tidak ditemukan')
                        ->withInput();
                }

                return $this->showLedgerForAccount(
                    $account, 
                    $accountingBook, 
                    $books, 
                    $accountOptions, 
                    $dateFrom, 
                    $dateTo,
                    $request
                );
            }

            // Default view when no account selected
            return view('financial.ledger.index', [
                'accountingBook' => $accountingBook,
                'books' => $books,
                'accountOptions' => $accountOptions,
                'selectedBookId' => $bookId,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('financial.journal.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show ledger for specific account
     */
    protected function showLedgerForAccount($account, $accountingBook, $books, $accountOptions, $dateFrom, $dateTo, $request)
    {
        // Calculate opening balance
        $openingBalance = AccountOpeningBalance::where('accounting_book_id', $accountingBook->id)
            ->where('account_code', $account['code'])
            ->first();

        $initialDebit = $openingBalance ? $openingBalance->debit : 0;
        $initialCredit = $openingBalance ? $openingBalance->credit : 0;

        $isCreditAccount = in_array($account['type'], ['liability', 'equity', 'revenue']);
        $initialBalance = $isCreditAccount ? ($initialCredit - $initialDebit) : ($initialDebit - $initialCredit);

        // Query for transactions
        $query = JournalEntry::with(['journal', 'subClass'])
            ->where('account_code', $account['code'])
            ->whereHas('journal', function($q) use ($accountingBook) {
                $q->where('accounting_book_id', $accountingBook->id);
            });

        // Apply date filters if exists
        if ($dateFrom) {
            $query->whereHas('journal', function($q) use ($dateFrom) {
                $q->where('transaction_date', '>=', $dateFrom);
            });
        }

        if ($dateTo) {
            $query->whereHas('journal', function($q) use ($dateTo) {
                $q->where('transaction_date', '<=', $dateTo);
            });
        }

        // Order by transaction date
        $entries = $query->orderBy(
            DB::raw('(SELECT transaction_date FROM journals WHERE journals.id = journal_entries.journal_id)')
        )->get();

        // Calculate running balance
        $runningBalance = $initialBalance;
        $ledgerEntries = [];

        foreach ($entries as $entry) {
            $amount = $entry->debit > 0 ? $entry->debit : $entry->credit;
            $isDebit = $entry->debit > 0;

            if ($isCreditAccount) {
                $runningBalance += $isDebit ? -$amount : $amount;
            } else {
                $runningBalance += $isDebit ? $amount : -$amount;
            }

            $ledgerEntries[] = [
                'date' => $entry->journal->transaction_date,
                'journal_number' => $entry->journal->journal_number,
                'reference_number' => $entry->journal->reference_number,
                'description' => $entry->journal->description,
                'sub_class' => $entry->subClass ? $entry->subClass->name : null,
                'debit' => $entry->debit,
                'credit' => $entry->credit,
                'balance' => $runningBalance,
            ];
        }

        // Calculate totals
        $totalDebit = $entries->sum('debit');
        $totalCredit = $entries->sum('credit');
        $endingBalance = $initialBalance + ($isCreditAccount ? ($totalCredit - $totalDebit) : ($totalDebit - $totalCredit));

        return view('financial.ledger.index', [
            'account' => $account,
            'accountingBook' => $accountingBook,
            'books' => $books,
            'accountOptions' => $accountOptions,
            'initialBalance' => $initialBalance,
            'ledgerEntries' => $ledgerEntries,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'endingBalance' => $endingBalance,
            'isCreditAccount' => $isCreditAccount,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'selectedAccountCode' => $account['code'],
            'selectedBookId' => $accountingBook->id,
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'account_code' => 'required|string',
            'book_id' => 'required|exists:accounting_books,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        try {
            // Get accounting book
            $accountingBook = AccountingBook::findOrFail($request->book_id);
            
            // Get account data
            $account = $this->coaService->getAccountByCode($request->account_code);
            if (!$account) {
                throw new \Exception('Akun tidak ditemukan');
            }

            // Parse dates
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : null;
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : null;

            // Get opening balance
            $openingBalance = AccountOpeningBalance::where('accounting_book_id', $accountingBook->id)
                ->where('account_code', $account['code'])
                ->first();

            $initialDebit = $openingBalance ? $openingBalance->debit : 0;
            $initialCredit = $openingBalance ? $openingBalance->credit : 0;

            $isCreditAccount = in_array($account['type'], ['liability', 'equity', 'revenue']);
            $initialBalance = $isCreditAccount ? ($initialCredit - $initialDebit) : ($initialDebit - $initialCredit);

            // Query for transactions
            $query = JournalEntry::with(['journal', 'subClass'])
                ->where('account_code', $account['code'])
                ->whereHas('journal', function($q) use ($accountingBook) {
                    $q->where('accounting_book_id', $accountingBook->id);
                });

            if ($dateFrom) {
                $query->whereHas('journal', function($q) use ($dateFrom) {
                    $q->where('transaction_date', '>=', $dateFrom);
                });
            }

            if ($dateTo) {
                $query->whereHas('journal', function($q) use ($dateTo) {
                    $q->where('transaction_date', '<=', $dateTo);
                });
            }

            $entries = $query->orderBy(
                DB::raw('(SELECT transaction_date FROM journals WHERE journals.id = journal_entries.journal_id)')
            )->get();

            // Prepare ledger data
            $runningBalance = $initialBalance;
            $ledgerEntries = [];

            foreach ($entries as $entry) {
                $amount = $entry->debit > 0 ? $entry->debit : $entry->credit;
                $isDebit = $entry->debit > 0;

                if ($isCreditAccount) {
                    $runningBalance += $isDebit ? -$amount : $amount;
                } else {
                    $runningBalance += $isDebit ? $amount : -$amount;
                }

                $ledgerEntries[] = [
                    'date' => $entry->journal->transaction_date->format('d/m/Y'),
                    'journal_number' => $entry->journal->journal_number,
                    'reference_number' => $entry->journal->reference_number,
                    'description' => $entry->journal->description,
                    'sub_class' => $entry->subClass ? $entry->subClass->name : null,
                    'debit' => $entry->debit,
                    'credit' => $entry->credit,
                    'balance' => $runningBalance,
                ];
            }

            // Calculate totals
            $totalDebit = $entries->sum('debit');
            $totalCredit = $entries->sum('credit');
            $endingBalance = $initialBalance + ($isCreditAccount ? ($totalCredit - $totalDebit) : ($totalDebit - $totalCredit));

            // Generate PDF
            $pdf = \PDF::loadView('financial.ledger.export', [
                'account' => $account,
                'accountingBook' => $accountingBook,
                'initialBalance' => $initialBalance,
                'ledgerEntries' => $ledgerEntries,
                'totalDebit' => $totalDebit,
                'totalCredit' => $totalCredit,
                'endingBalance' => $endingBalance,
                'isCreditAccount' => $isCreditAccount,
                'dateFrom' => $dateFrom ? $dateFrom->format('d/m/Y') : null,
                'dateTo' => $dateTo ? $dateTo->format('d/m/Y') : null,
                'exportDate' => now()->format('d/m/Y H:i:s'),
            ]);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'landscape');

            // Download PDF
            $filename = 'Buku_Besar_' . $account['code'] . '_' . $accountingBook->name . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengekspor: ' . $e->getMessage());
        }
    }

    protected function showAllAccountsLedger($accountingBook, $books, $accountOptions, $dateFrom, $dateTo)
    {
        // Get all accounts with transactions in the period
        $accountsWithTransactions = JournalEntry::select('account_code')
            ->whereHas('journal', function($q) use ($accountingBook, $dateFrom, $dateTo) {
                $q->where('accounting_book_id', $accountingBook->id);
                if ($dateFrom) {
                    $q->where('transaction_date', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $q->where('transaction_date', '<=', $dateTo);
                }
            })
            ->groupBy('account_code')
            ->pluck('account_code');
        
        // Get opening balances for all accounts
        $openingBalances = AccountOpeningBalance::where('accounting_book_id', $accountingBook->id)
            ->get()
            ->keyBy('account_code');
        
        // Prepare ledger data for each account
        $allLedgers = [];
        
        foreach ($accountsWithTransactions as $accountCode) {
            $account = $this->coaService->getAccountByCode($accountCode);
            if (!$account) continue;
            
            $isCreditAccount = in_array($account['type'], ['liability', 'equity', 'revenue']);
            
            // Get opening balance
            $openingBalance = $openingBalances[$accountCode] ?? null;
            $initialDebit = $openingBalance ? $openingBalance->debit : 0;
            $initialCredit = $openingBalance ? $openingBalance->credit : 0;
            $initialBalance = $isCreditAccount ? ($initialCredit - $initialDebit) : ($initialDebit - $initialCredit);
            
            // Query transactions
            $query = JournalEntry::with(['journal', 'subClass'])
                ->where('account_code', $accountCode)
                ->whereHas('journal', function($q) use ($accountingBook) {
                    $q->where('accounting_book_id', $accountingBook->id);
                });
            
            if ($dateFrom) {
                $query->whereHas('journal', function($q) use ($dateFrom) {
                    $q->where('transaction_date', '>=', $dateFrom);
                });
            }
            
            if ($dateTo) {
                $query->whereHas('journal', function($q) use ($dateTo) {
                    $q->where('transaction_date', '<=', $dateTo);
                });
            }
            
            $entries = $query->orderBy(
                DB::raw('(SELECT transaction_date FROM journals WHERE journals.id = journal_entries.journal_id)')
            )->get();
            
            // Calculate running balance
            $runningBalance = $initialBalance;
            $ledgerEntries = [];
            $totalDebit = 0;
            $totalCredit = 0;
            
            foreach ($entries as $entry) {
                $amount = $entry->debit > 0 ? $entry->debit : $entry->credit;
                $isDebit = $entry->debit > 0;
                
                if ($isCreditAccount) {
                    $runningBalance += $isDebit ? -$amount : $amount;
                } else {
                    $runningBalance += $isDebit ? $amount : -$amount;
                }
                
                $totalDebit += $entry->debit;
                $totalCredit += $entry->credit;
                
                $ledgerEntries[] = [
                    'date' => $entry->journal->transaction_date,
                    'journal_number' => $entry->journal->journal_number,
                    'reference_number' => $entry->journal->reference_number,
                    'description' => $entry->journal->description,
                    'sub_class' => $entry->subClass ? $entry->subClass->name : null,
                    'debit' => $entry->debit,
                    'credit' => $entry->credit,
                    'balance' => $runningBalance,
                ];
            }
            
            $allLedgers[] = [
                'account' => $account,
                'initialBalance' => $initialBalance,
                'entries' => $ledgerEntries,
                'totalDebit' => $totalDebit,
                'totalCredit' => $totalCredit,
                'endingBalance' => $runningBalance,
                'isCreditAccount' => $isCreditAccount,
            ];
        }
        
        return view('financial.ledger.all_accounts', [
            'accountingBook' => $accountingBook,
            'books' => $books,
            'accountOptions' => $accountOptions,
            'allLedgers' => $allLedgers,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'selectedBookId' => $accountingBook->id,
        ]);
    }


    public function exportAll(Request $request)
    {
        \Log::info('ExportAll method called', $request->all()); // Log masuk ke method
        
        $request->validate([
            'book_id' => 'required|exists:accounting_books,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        try {
            \Log::info('Starting export process'); // Log proses dimulai
            
            $accountingBook = AccountingBook::findOrFail($request->book_id);
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : null;
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : null;

            \Log::info('Getting accounts with transactions'); // Log proses query
            $accountsWithTransactions = JournalEntry::select('account_code')
                ->whereHas('journal', function($q) use ($accountingBook, $dateFrom, $dateTo) {
                    $q->where('accounting_book_id', $accountingBook->id);
                    if ($dateFrom) {
                        $q->where('transaction_date', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $q->where('transaction_date', '<=', $dateTo);
                    }
                })
                ->groupBy('account_code')
                ->pluck('account_code');
            
            \Log::info('Found ' . $accountsWithTransactions->count() . ' accounts with transactions');
            
            $openingBalances = AccountOpeningBalance::where('accounting_book_id', $accountingBook->id)
                ->get()
                ->keyBy('account_code');
            
            $allLedgers = [];
            
            foreach ($accountsWithTransactions as $accountCode) {
                \Log::info('Processing account: ' . $accountCode); // Log proses per akun
                
                $account = $this->coaService->getAccountByCode($accountCode);
                if (!$account) {
                    \Log::warning('Account not found: ' . $accountCode);
                    continue;
                }
                
                $isCreditAccount = in_array($account['type'], ['liability', 'equity', 'revenue']);
                
                $openingBalance = $openingBalances[$accountCode] ?? null;
                $initialDebit = $openingBalance ? $openingBalance->debit : 0;
                $initialCredit = $openingBalance ? $openingBalance->credit : 0;
                $initialBalance = $isCreditAccount ? ($initialCredit - $initialDebit) : ($initialDebit - $initialCredit);
                
                $query = JournalEntry::with(['journal', 'subClass'])
                    ->where('account_code', $accountCode)
                    ->whereHas('journal', function($q) use ($accountingBook) {
                        $q->where('accounting_book_id', $accountingBook->id);
                    });
                
                if ($dateFrom) {
                    $query->whereHas('journal', function($q) use ($dateFrom) {
                        $q->where('transaction_date', '>=', $dateFrom);
                    });
                }
                
                if ($dateTo) {
                    $query->whereHas('journal', function($q) use ($dateTo) {
                        $q->where('transaction_date', '<=', $dateTo);
                    });
                }
                
                $entries = $query->orderBy(
                    DB::raw('(SELECT transaction_date FROM journals WHERE journals.id = journal_entries.journal_id)')
                )->get();
                
                \Log::info('Found ' . $entries->count() . ' entries for account: ' . $accountCode);
                
                $runningBalance = $initialBalance;
                $ledgerEntries = [];
                $totalDebit = 0;
                $totalCredit = 0;
                
                foreach ($entries as $entry) {
                    $amount = $entry->debit > 0 ? $entry->debit : $entry->credit;
                    $isDebit = $entry->debit > 0;
                    
                    if ($isCreditAccount) {
                        $runningBalance += $isDebit ? -$amount : $amount;
                    } else {
                        $runningBalance += $isDebit ? $amount : -$amount;
                    }
                    
                    $totalDebit += $entry->debit;
                    $totalCredit += $entry->credit;
                    
                    $ledgerEntries[] = [
                        'date' => $entry->journal->transaction_date->format('d/m/Y'),
                        'journal_number' => $entry->journal->journal_number,
                        'reference_number' => $entry->journal->reference_number,
                        'description' => $entry->journal->description,
                        'sub_class' => $entry->subClass ? $entry->subClass->name : null,
                        'debit' => $entry->debit,
                        'credit' => $entry->credit,
                        'balance' => $runningBalance,
                    ];
                }
                
                $allLedgers[] = [
                    'account' => $account,
                    'initialBalance' => $initialBalance,
                    'entries' => $ledgerEntries,
                    'totalDebit' => $totalDebit,
                    'totalCredit' => $totalCredit,
                    'endingBalance' => $runningBalance,
                    'isCreditAccount' => $isCreditAccount,
                ];
            }
            
            \Log::info('Generating PDF for ' . count($allLedgers) . ' accounts'); // Log sebelum generate PDF
            
            // Generate PDF
            $pdf = \PDF::loadView('financial.ledger.export_all', [
                'accountingBook' => $accountingBook,
                'allLedgers' => $allLedgers,
                'dateFrom' => $dateFrom ? $dateFrom->format('d/m/Y') : null,
                'dateTo' => $dateTo ? $dateTo->format('d/m/Y') : null,
                'exportDate' => now()->format('d/m/Y H:i:s'),
            ]);
            
            $pdf->setPaper('A4', 'portrait');
            
            // Download PDF
            $filename = 'Buku_Besar_Semua_Akun_' . $accountingBook->name . '.pdf';
            
            \Log::info('Export completed successfully. Filename: ' . $filename); // Log sukses
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Export failed: ' . $e->getMessage()); // Log error
            \Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Gagal mengekspor: ' . $e->getMessage());
        }
    }
}