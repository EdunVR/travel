<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Outlet;
use App\Models\Piutang;
use App\Models\Hutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceDashboardController extends Controller
{
    use \App\Traits\HasOutletFilter;

    public function index()
    {
        $outlets = $this->getAccessibleOutlets();
        return view('admin.finance.index', compact('outlets'));
    }

    public function getData(Request $request)
    {
        try {
            $outletIds = $request->input('outlet_ids', []);
            $startDate = $request->start_date ?? Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

            $data = [
                'kpi' => $this->getKPI($outletIds, $startDate, $endDate),
                'cashflow_summary' => $this->getCashflowSummary($outletIds, $startDate, $endDate),
                'profit_loss_summary' => $this->getProfitLossSummary($outletIds, $startDate, $endDate),
                'balance_sheet_summary' => $this->getBalanceSheetSummary($outletIds, $endDate),
                'piutang_aging' => $this->getPiutangAging($outletIds),
                'hutang_aging' => $this->getHutangAging($outletIds),
                'monthly_trend' => $this->getMonthlyTrend($outletIds, $startDate, $endDate),
                'recent_transactions' => $this->getRecentTransactions($outletIds, 10),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Finance Dashboard Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getKPI($outletIds, $startDate, $endDate)
    {
        // Total Transactions
        $query = JournalEntry::whereBetween('transaction_date', [$startDate, $endDate]);
        if (!empty($outletIds)) {
            $query->whereIn('outlet_id', $outletIds);
        }
        $totalTransactions = $query->count();

        // Total Revenue
        $revenueQuery = ChartOfAccount::where('type', 'revenue');
        if (!empty($outletIds)) {
            $revenueQuery->whereIn('outlet_id', $outletIds);
        }
        $revenueAccounts = $revenueQuery->pluck('id')->toArray();

        $totalRevenue = JournalEntryDetail::whereIn('account_id', $revenueAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('credit');

        // Total Expense
        $expenseQuery = ChartOfAccount::where('type', 'expense');
        if (!empty($outletIds)) {
            $expenseQuery->whereIn('outlet_id', $outletIds);
        }
        $expenseAccounts = $expenseQuery->pluck('id')->toArray();

        $totalExpense = JournalEntryDetail::whereIn('account_id', $expenseAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('debit');

        $netProfit = $totalRevenue - $totalExpense;

        // Cash & Bank Balance
        $cashBankQuery = ChartOfAccount::where(function($q) {
            $q->where('category', 'LIKE', '%Kas%')
              ->orWhere('category', 'LIKE', '%Bank%')
              ->orWhere('category', 'LIKE', '%Cash%');
        });
        if (!empty($outletIds)) {
            $cashBankQuery->whereIn('outlet_id', $outletIds);
        }
        $cashBankBalance = $cashBankQuery->sum('balance');

        // Piutang
        $piutangQuery = Piutang::where('status', '!=', 'lunas');
        if (!empty($outletIds)) {
            $piutangQuery->whereIn('id_outlet', $outletIds);
        }
        $totalPiutang = $piutangQuery->sum('sisa_piutang');

        // Hutang
        $hutangQuery = Hutang::where('status', '!=', 'lunas');
        if (!empty($outletIds)) {
            $hutangQuery->whereIn('id_outlet', $outletIds);
        }
        $totalHutang = $hutangQuery->sum('sisa_hutang');

        return [
            'total_transactions' => $totalTransactions,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_profit' => $netProfit,
            'profit_margin' => $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0,
            'cash_bank_balance' => $cashBankBalance,
            'total_piutang' => $totalPiutang,
            'total_hutang' => $totalHutang,
            'working_capital' => $cashBankBalance + $totalPiutang - $totalHutang,
        ];
    }

    private function getCashflowSummary($outletIds, $startDate, $endDate)
    {
        // Operating Activities
        $operatingQuery = ChartOfAccount::whereIn('type', ['revenue', 'expense']);
        if (!empty($outletIds)) {
            $operatingQuery->whereIn('outlet_id', $outletIds);
        }
        $operatingAccounts = $operatingQuery->pluck('id')->toArray();

        $operatingInflow = JournalEntryDetail::whereIn('account_id', $operatingAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('credit');

        $operatingOutflow = JournalEntryDetail::whereIn('account_id', $operatingAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('debit');

        // Investing Activities
        $investingQuery = ChartOfAccount::where(function($q) {
            $q->where('category', 'LIKE', '%Aktiva Tetap%')
              ->orWhere('category', 'LIKE', '%Fixed Asset%');
        });
        if (!empty($outletIds)) {
            $investingQuery->whereIn('outlet_id', $outletIds);
        }
        $investingAccounts = $investingQuery->pluck('id')->toArray();

        $investingOutflow = JournalEntryDetail::whereIn('account_id', $investingAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('debit');

        // Financing Activities
        $financingQuery = ChartOfAccount::whereIn('type', ['liability', 'equity']);
        if (!empty($outletIds)) {
            $financingQuery->whereIn('outlet_id', $outletIds);
        }
        $financingAccounts = $financingQuery->pluck('id')->toArray();

        $financingInflow = JournalEntryDetail::whereIn('account_id', $financingAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('credit');

        $financingOutflow = JournalEntryDetail::whereIn('account_id', $financingAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('debit');

        $netOperating = $operatingInflow - $operatingOutflow;
        $netInvesting = -$investingOutflow;
        $netFinancing = $financingInflow - $financingOutflow;
        $netCashflow = $netOperating + $netInvesting + $netFinancing;

        return [
            'operating' => [
                'inflow' => $operatingInflow,
                'outflow' => $operatingOutflow,
                'net' => $netOperating
            ],
            'investing' => [
                'inflow' => 0,
                'outflow' => $investingOutflow,
                'net' => $netInvesting
            ],
            'financing' => [
                'inflow' => $financingInflow,
                'outflow' => $financingOutflow,
                'net' => $netFinancing
            ],
            'net_cashflow' => $netCashflow
        ];
    }

    private function getProfitLossSummary($outletIds, $startDate, $endDate)
    {
        // Revenue
        $revenueQuery = ChartOfAccount::where('type', 'revenue');
        if (!empty($outletIds)) {
            $revenueQuery->whereIn('outlet_id', $outletIds);
        }
        $revenueAccounts = $revenueQuery->pluck('id')->toArray();

        $revenue = JournalEntryDetail::whereIn('account_id', $revenueAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('credit');

        // COGS
        $cogsQuery = ChartOfAccount::where(function($q) {
            $q->where('category', 'LIKE', '%HPP%')
              ->orWhere('category', 'LIKE', '%COGS%')
              ->orWhere('name', 'LIKE', '%Harga Pokok%');
        });
        if (!empty($outletIds)) {
            $cogsQuery->whereIn('outlet_id', $outletIds);
        }
        $cogsAccounts = $cogsQuery->pluck('id')->toArray();

        $cogs = JournalEntryDetail::whereIn('account_id', $cogsAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('debit');

        $grossProfit = $revenue - $cogs;

        // Operating Expenses
        $expenseQuery = ChartOfAccount::where('type', 'expense')
            ->where('category', 'NOT LIKE', '%HPP%')
            ->where('category', 'NOT LIKE', '%COGS%');
        if (!empty($outletIds)) {
            $expenseQuery->whereIn('outlet_id', $outletIds);
        }
        $expenseAccounts = $expenseQuery->pluck('id')->toArray();

        $operatingExpense = JournalEntryDetail::whereIn('account_id', $expenseAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('debit');

        $operatingProfit = $grossProfit - $operatingExpense;

        // Other Income/Expense
        $otherRevenueQuery = ChartOfAccount::where('type', 'otherrevenue');
        if (!empty($outletIds)) {
            $otherRevenueQuery->whereIn('outlet_id', $outletIds);
        }
        $otherRevenueAccounts = $otherRevenueQuery->pluck('id')->toArray();

        $otherRevenue = JournalEntryDetail::whereIn('account_id', $otherRevenueAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('credit');

        $otherExpenseQuery = ChartOfAccount::where('type', 'otherexpense');
        if (!empty($outletIds)) {
            $otherExpenseQuery->whereIn('outlet_id', $outletIds);
        }
        $otherExpenseAccounts = $otherExpenseQuery->pluck('id')->toArray();

        $otherExpense = JournalEntryDetail::whereIn('account_id', $otherExpenseAccounts)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate, $outletIds) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
                if (!empty($outletIds)) {
                    $q->whereIn('outlet_id', $outletIds);
                }
            })
            ->sum('debit');

        $netProfit = $operatingProfit + $otherRevenue - $otherExpense;

        return [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'gross_margin' => $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0,
            'operating_expense' => $operatingExpense,
            'operating_profit' => $operatingProfit,
            'other_revenue' => $otherRevenue,
            'other_expense' => $otherExpense,
            'net_profit' => $netProfit,
            'net_margin' => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0,
        ];
    }

    private function getBalanceSheetSummary($outletIds, $endDate)
    {
        // Assets
        $assetQuery = ChartOfAccount::where('type', 'asset');
        if (!empty($outletIds)) {
            $assetQuery->whereIn('outlet_id', $outletIds);
        }
        $totalAssets = $assetQuery->sum('balance');

        // Current Assets
        $currentAssetQuery = ChartOfAccount::where('type', 'asset')
            ->where(function($q) {
                $q->where('category', 'LIKE', '%Kas%')
                  ->orWhere('category', 'LIKE', '%Bank%')
                  ->orWhere('category', 'LIKE', '%Piutang%')
                  ->orWhere('category', 'LIKE', '%Persediaan%')
                  ->orWhere('category', 'LIKE', '%Inventory%');
            });
        if (!empty($outletIds)) {
            $currentAssetQuery->whereIn('outlet_id', $outletIds);
        }
        $currentAssets = $currentAssetQuery->sum('balance');

        // Fixed Assets
        $fixedAssetQuery = ChartOfAccount::where('type', 'asset')
            ->where(function($q) {
                $q->where('category', 'LIKE', '%Aktiva Tetap%')
                  ->orWhere('category', 'LIKE', '%Fixed Asset%');
            });
        if (!empty($outletIds)) {
            $fixedAssetQuery->whereIn('outlet_id', $outletIds);
        }
        $fixedAssets = $fixedAssetQuery->sum('balance');

        // Liabilities
        $liabilityQuery = ChartOfAccount::where('type', 'liability');
        if (!empty($outletIds)) {
            $liabilityQuery->whereIn('outlet_id', $outletIds);
        }
        $totalLiabilities = $liabilityQuery->sum('balance');

        // Current Liabilities
        $currentLiabilityQuery = ChartOfAccount::where('type', 'liability')
            ->where(function($q) {
                $q->where('category', 'LIKE', '%Hutang Usaha%')
                  ->orWhere('category', 'LIKE', '%Hutang Jangka Pendek%')
                  ->orWhere('category', 'LIKE', '%Current Liability%');
            });
        if (!empty($outletIds)) {
            $currentLiabilityQuery->whereIn('outlet_id', $outletIds);
        }
        $currentLiabilities = $currentLiabilityQuery->sum('balance');

        // Equity
        $equityQuery = ChartOfAccount::where('type', 'equity');
        if (!empty($outletIds)) {
            $equityQuery->whereIn('outlet_id', $outletIds);
        }
        $totalEquity = $equityQuery->sum('balance');

        return [
            'assets' => [
                'current' => $currentAssets,
                'fixed' => $fixedAssets,
                'total' => $totalAssets
            ],
            'liabilities' => [
                'current' => $currentLiabilities,
                'long_term' => $totalLiabilities - $currentLiabilities,
                'total' => $totalLiabilities
            ],
            'equity' => $totalEquity,
            'total_liabilities_equity' => $totalLiabilities + $totalEquity,
            'current_ratio' => $currentLiabilities > 0 ? $currentAssets / $currentLiabilities : 0,
            'debt_to_equity' => $totalEquity > 0 ? $totalLiabilities / $totalEquity : 0,
        ];
    }

    private function getPiutangAging($outletIds)
    {
        $query = Piutang::where('status', '!=', 'lunas');
        if (!empty($outletIds)) {
            $query->whereIn('id_outlet', $outletIds);
        }

        $piutangs = $query->get();
        $aging = [
            'current' => 0,
            'overdue_30' => 0,
            'overdue_60' => 0,
            'overdue_90' => 0,
        ];

        $now = Carbon::now();
        foreach ($piutangs as $piutang) {
            if (!$piutang->tanggal_jatuh_tempo) continue;
            
            $dueDate = Carbon::parse($piutang->tanggal_jatuh_tempo);
            $daysOverdue = $now->diffInDays($dueDate, false);

            if ($daysOverdue >= 0) {
                $aging['current'] += $piutang->sisa_piutang;
            } elseif ($daysOverdue >= -30) {
                $aging['overdue_30'] += $piutang->sisa_piutang;
            } elseif ($daysOverdue >= -60) {
                $aging['overdue_60'] += $piutang->sisa_piutang;
            } else {
                $aging['overdue_90'] += $piutang->sisa_piutang;
            }
        }

        return $aging;
    }

    private function getHutangAging($outletIds)
    {
        $query = Hutang::where('status', '!=', 'lunas');
        if (!empty($outletIds)) {
            $query->whereIn('id_outlet', $outletIds);
        }

        $hutangs = $query->get();
        $aging = [
            'current' => 0,
            'overdue_30' => 0,
            'overdue_60' => 0,
            'overdue_90' => 0,
        ];

        $now = Carbon::now();
        foreach ($hutangs as $hutang) {
            if (!$hutang->tanggal_jatuh_tempo) continue;
            
            $dueDate = Carbon::parse($hutang->tanggal_jatuh_tempo);
            $daysOverdue = $now->diffInDays($dueDate, false);

            if ($daysOverdue >= 0) {
                $aging['current'] += $hutang->sisa_hutang;
            } elseif ($daysOverdue >= -30) {
                $aging['overdue_30'] += $hutang->sisa_hutang;
            } elseif ($daysOverdue >= -60) {
                $aging['overdue_60'] += $hutang->sisa_hutang;
            } else {
                $aging['overdue_90'] += $hutang->sisa_hutang;
            }
        }

        return $aging;
    }

    private function getMonthlyTrend($outletIds, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $months = [];
        $current = $start->copy()->startOfMonth();
        $maxIterations = 24; // Safety limit: max 24 months
        $iteration = 0;
        
        while ($current <= $end && $iteration < $maxIterations) {
            $monthStart = $current->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $current->copy()->endOfMonth()->format('Y-m-d');
            
            // Revenue - Get accounts WITHOUT outlet filter (COA is shared across outlets)
            $revenueAccounts = ChartOfAccount::where('type', 'revenue')->pluck('id')->toArray();

            $revenue = 0;
            if (!empty($revenueAccounts)) {
                $revenue = JournalEntryDetail::whereIn('account_id', $revenueAccounts)
                    ->whereHas('journalEntry', function($q) use ($monthStart, $monthEnd, $outletIds) {
                        $q->whereBetween('transaction_date', [$monthStart, $monthEnd]);
                        if (!empty($outletIds)) {
                            $q->whereIn('outlet_id', $outletIds);
                        }
                    })
                    ->sum('credit');
            }

            // Expense - Get accounts WITHOUT outlet filter (COA is shared across outlets)
            $expenseAccounts = ChartOfAccount::where('type', 'expense')->pluck('id')->toArray();

            $expense = 0;
            if (!empty($expenseAccounts)) {
                $expense = JournalEntryDetail::whereIn('account_id', $expenseAccounts)
                    ->whereHas('journalEntry', function($q) use ($monthStart, $monthEnd, $outletIds) {
                        $q->whereBetween('transaction_date', [$monthStart, $monthEnd]);
                        if (!empty($outletIds)) {
                            $q->whereIn('outlet_id', $outletIds);
                        }
                    })
                    ->sum('debit');
            }

            $months[] = [
                'month' => $current->format('M Y'),
                'revenue' => (float) $revenue,
                'expense' => (float) $expense,
                'profit' => (float) ($revenue - $expense)
            ];

            $current->addMonth();
            $iteration++;
        }

        return $months;
    }

    private function getRecentTransactions($outletIds, $limit = 10)
    {
        $query = JournalEntry::with(['outlet', 'details.account'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');
        
        if (!empty($outletIds)) {
            $query->whereIn('outlet_id', $outletIds);
        }

        return $query->limit($limit)->get()->map(function($entry) {
            return [
                'id' => $entry->id,
                'date' => $entry->transaction_date,
                'reference' => $entry->reference_number ?? '-',
                'description' => $entry->description ?? '-',
                'outlet' => $entry->outlet->nama_outlet ?? '-',
                'debit' => $entry->details->sum('debit'),
                'credit' => $entry->details->sum('credit'),
                'source' => $entry->source_type ?? 'Manual',
            ];
        });
    }

    public function exportPdf(Request $request)
    {
        try {
            $outletIds = $request->input('outlet_ids', []);
            $startDate = $request->start_date ?? Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

            $data = [
                'kpi' => $this->getKPI($outletIds, $startDate, $endDate),
                'cashflow_summary' => $this->getCashflowSummary($outletIds, $startDate, $endDate),
                'profit_loss_summary' => $this->getProfitLossSummary($outletIds, $startDate, $endDate),
                'balance_sheet_summary' => $this->getBalanceSheetSummary($outletIds, $endDate),
                'piutang_aging' => $this->getPiutangAging($outletIds),
                'hutang_aging' => $this->getHutangAging($outletIds),
                'monthly_trend' => $this->getMonthlyTrend($outletIds, $startDate, $endDate),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'outlets' => !empty($outletIds) ? Outlet::whereIn('id_outlet', $outletIds)->get() : collect(),
            ];

            $pdf = Pdf::loadView('admin.finance.dashboard-pdf', $data)
                ->setPaper('a4', 'portrait');

            return $pdf->stream('finance-dashboard-' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Finance Dashboard PDF Export Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }
}
