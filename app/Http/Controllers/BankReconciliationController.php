<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\BankReconciliation;
use App\Models\BankReconciliationItem;
use App\Models\CompanyBankAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class BankReconciliationController extends Controller
{
    use \App\Traits\HasOutletFilter;

    public function index()
    {
        return view('admin.finance.rekonsiliasi.index');
    }

    public function getData(Request $request): JsonResponse
    {
        try {
            $query = BankReconciliation::with(['outlet', 'account'])
                ->orderBy('reconciliation_date', 'desc');

            if ($request->outlet_id) {
                $query->where('outlet_id', $request->outlet_id);
            }

            if ($request->status && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->period_month) {
                $query->where('period_month', $request->period_month);
            }

            if ($request->bank_account_id) {
                $query->where('account_id', $request->bank_account_id);
            }

            $reconciliations = $query->get()->map(function ($recon) {
                return [
                    'id' => $recon->id,
                    'outlet_name' => $recon->outlet->nama_outlet ?? '-',
                    'bank_name' => $recon->account->name ?? '-',
                    'account_number' => $recon->account->code ?? '-',
                    'period_month' => $recon->period_month,
                    'reconciliation_date' => $recon->reconciliation_date->format('Y-m-d'),
                    'bank_statement_balance' => $recon->bank_statement_balance,
                    'book_balance' => $recon->book_balance,
                    'difference' => $recon->difference,
                    'status' => $recon->status,
                    'reconciled_by' => $recon->reconciled_by,
                    'approved_by' => $recon->approved_by,
                    'approved_at' => $recon->approved_at ? $recon->approved_at->format('Y-m-d H:i') : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $reconciliations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $query = BankReconciliation::query();

            if ($request->outlet_id) {
                $query->where('outlet_id', $request->outlet_id);
            }

            $stats = [
                'total_reconciliations' => $query->count(),
                'draft' => (clone $query)->where('status', 'draft')->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
                'approved' => (clone $query)->where('status', 'approved')->count(),
                'total_difference' => (clone $query)->sum('difference'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBankAccounts(Request $request): JsonResponse
    {
        try {
            // Get Chart of Accounts with type 'asset' (bank accounts)
            $query = \App\Models\ChartOfAccount::where('type', 'asset')
                ->where('status', 'active')
                ->where(function($q) {
                    $q->where('name', 'like', '%bank%')
                      ->orWhere('name', 'like', '%kas%')
                      ->orWhere('category', 'like', '%bank%')
                      ->orWhere('category', 'like', '%kas%');
                })
                ->orderBy('code');

            if ($request->outlet_id) {
                $query->where('outlet_id', $request->outlet_id);
            }

            $allAccounts = $query->get();

            // Filter: Only show leaf accounts (accounts without children)
            // If parent has children, don't show parent, only show children
            $accounts = $allAccounts->filter(function ($account) use ($allAccounts) {
                // Check if this account has children
                $hasChildren = $allAccounts->where('parent_id', $account->id)->count() > 0;
                
                // Only include if it doesn't have children (leaf node)
                return !$hasChildren;
            })->map(function ($account) {
                return [
                    'id' => $account->id,
                    'code' => $account->code,
                    'name' => $account->name,
                    'category' => $account->category,
                    'level' => $account->level,
                    'balance' => $account->balance,
                    'outlet_id' => $account->outlet_id,
                    'full_info' => $account->code . ' - ' . $account->name . ($account->category ? ' (' . $account->category . ')' : '')
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $accounts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data akun bank: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUnreconciledTransactions(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'bank_account_id' => 'required|exists:chart_of_accounts,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get journal entries for the specific bank account (COA)
            $transactions = JournalEntryDetail::with(['journalEntry', 'account'])
                ->where('account_id', $request->bank_account_id)
                ->whereHas('journalEntry', function ($q) use ($request) {
                    $q->where('outlet_id', $request->outlet_id)
                      ->where('status', 'posted')
                      ->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
                })
                ->orderBy('id')
                ->get()
                ->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'journal_entry_id' => $detail->journal_entry_id,
                        'transaction_date' => $detail->journalEntry->transaction_date->format('Y-m-d'),
                        'transaction_number' => $detail->journalEntry->transaction_number,
                        'description' => $detail->journalEntry->description,
                        'account_name' => $detail->account->name,
                        'debit' => $detail->debit,
                        'credit' => $detail->credit,
                        'amount' => $detail->debit > 0 ? $detail->debit : $detail->credit,
                        'type' => $detail->debit > 0 ? 'debit' : 'credit',
                        'reference_type' => $detail->journalEntry->reference_type,
                        'reference_number' => $detail->journalEntry->reference_number,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'bank_account_id' => 'required|exists:chart_of_accounts,id',
                'reconciliation_date' => 'required|date',
                'period_month' => 'required|string|size:7',
                'bank_statement_balance' => 'required|numeric',
                'book_balance' => 'required|numeric',
                'notes' => 'nullable|string',
                'items' => 'nullable|array',
                'items.*.journal_entry_id' => 'nullable|exists:journal_entries,id',
                'items.*.transaction_date' => 'required|date',
                'items.*.description' => 'required|string',
                'items.*.amount' => 'required|numeric',
                'items.*.type' => 'required|in:debit,credit',
                'items.*.category' => 'nullable|in:deposit_in_transit,outstanding_check,bank_charge,bank_interest,error,other',
                'items.*.status' => 'required|in:unreconciled,reconciled,pending'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $adjustedBalance = $request->book_balance;
            $difference = $request->bank_statement_balance - $adjustedBalance;

            $reconciliation = BankReconciliation::create([
                'outlet_id' => $request->outlet_id,
                'account_id' => $request->bank_account_id,
                'reconciliation_date' => $request->reconciliation_date,
                'period_month' => $request->period_month,
                'bank_statement_balance' => $request->bank_statement_balance,
                'book_balance' => $request->book_balance,
                'adjusted_balance' => $adjustedBalance,
                'difference' => $difference,
                'status' => 'draft',
                'notes' => $request->notes,
                'reconciled_by' => Auth::user()->name ?? 'System'
            ]);

            // Save reconciliation items
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    BankReconciliationItem::create([
                        'reconciliation_id' => $reconciliation->id,
                        'journal_entry_id' => $item['journal_entry_id'] ?? null,
                        'transaction_date' => $item['transaction_date'],
                        'transaction_number' => $item['transaction_number'] ?? null,
                        'description' => $item['description'],
                        'amount' => $item['amount'],
                        'type' => $item['type'],
                        'status' => $item['status'],
                        'category' => $item['category'] ?? null,
                        'notes' => $item['notes'] ?? null
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rekonsiliasi bank berhasil disimpan',
                'data' => $reconciliation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan rekonsiliasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $reconciliation = BankReconciliation::with(['outlet', 'account', 'items.journalEntry'])
                ->findOrFail($id);

            $data = [
                'id' => $reconciliation->id,
                'outlet_id' => $reconciliation->outlet_id,
                'outlet_name' => $reconciliation->outlet->nama_outlet ?? '-',
                'bank_account_id' => $reconciliation->account_id,
                'bank_name' => $reconciliation->account->name ?? '-',
                'account_number' => $reconciliation->account->code ?? '-',
                'reconciliation_date' => $reconciliation->reconciliation_date->format('Y-m-d'),
                'period_month' => $reconciliation->period_month,
                'bank_statement_balance' => $reconciliation->bank_statement_balance,
                'book_balance' => $reconciliation->book_balance,
                'adjusted_balance' => $reconciliation->adjusted_balance,
                'difference' => $reconciliation->difference,
                'status' => $reconciliation->status,
                'notes' => $reconciliation->notes,
                'reconciled_by' => $reconciliation->reconciled_by,
                'approved_by' => $reconciliation->approved_by,
                'approved_at' => $reconciliation->approved_at ? $reconciliation->approved_at->format('Y-m-d H:i') : null,
                'items' => $reconciliation->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'journal_entry_id' => $item->journal_entry_id,
                        'transaction_date' => $item->transaction_date->format('Y-m-d'),
                        'transaction_number' => $item->transaction_number,
                        'description' => $item->description,
                        'amount' => $item->amount,
                        'type' => $item->type,
                        'status' => $item->status,
                        'category' => $item->category,
                        'notes' => $item->notes
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $reconciliation = BankReconciliation::findOrFail($id);

            if ($reconciliation->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Rekonsiliasi yang sudah disetujui tidak dapat diubah'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'bank_statement_balance' => 'required|numeric',
                'book_balance' => 'required|numeric',
                'notes' => 'nullable|string',
                'items' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $adjustedBalance = $request->book_balance;
            $difference = $request->bank_statement_balance - $adjustedBalance;

            $reconciliation->update([
                'bank_statement_balance' => $request->bank_statement_balance,
                'book_balance' => $request->book_balance,
                'adjusted_balance' => $adjustedBalance,
                'difference' => $difference,
                'notes' => $request->notes
            ]);

            // Update items if provided
            if ($request->has('items')) {
                // Delete existing items
                $reconciliation->items()->delete();

                // Create new items
                foreach ($request->items as $item) {
                    BankReconciliationItem::create([
                        'reconciliation_id' => $reconciliation->id,
                        'journal_entry_id' => $item['journal_entry_id'] ?? null,
                        'transaction_date' => $item['transaction_date'],
                        'transaction_number' => $item['transaction_number'] ?? null,
                        'description' => $item['description'],
                        'amount' => $item['amount'],
                        'type' => $item['type'],
                        'status' => $item['status'],
                        'category' => $item['category'] ?? null,
                        'notes' => $item['notes'] ?? null
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rekonsiliasi berhasil diperbarui',
                'data' => $reconciliation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui rekonsiliasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function complete($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $reconciliation = BankReconciliation::findOrFail($id);

            if ($reconciliation->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya rekonsiliasi draft yang dapat diselesaikan'
                ], 422);
            }

            $reconciliation->update([
                'status' => 'completed'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rekonsiliasi berhasil diselesaikan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan rekonsiliasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $reconciliation = BankReconciliation::findOrFail($id);

            if ($reconciliation->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya rekonsiliasi yang sudah diselesaikan yang dapat disetujui'
                ], 422);
            }

            $reconciliation->update([
                'status' => 'approved',
                'approved_by' => Auth::user()->name ?? 'System',
                'approved_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rekonsiliasi berhasil disetujui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui rekonsiliasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $reconciliation = BankReconciliation::findOrFail($id);

            if ($reconciliation->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Rekonsiliasi yang sudah disetujui tidak dapat dihapus'
                ], 422);
            }

            $reconciliation->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rekonsiliasi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus rekonsiliasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf($id)
    {
        try {
            $reconciliation = BankReconciliation::with(['outlet', 'account', 'items'])
                ->findOrFail($id);

            $pdf = Pdf::loadView('admin.finance.rekonsiliasi.pdf', [
                'reconciliation' => $reconciliation
            ]);

            $filename = 'rekonsiliasi-bank-' . $reconciliation->period_month . '.pdf';
            
            // Stream PDF in browser instead of download
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
