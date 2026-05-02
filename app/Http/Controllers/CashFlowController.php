<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Outlet;
use App\Models\AccountingBook;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\FixedAsset;
use App\Traits\HasCompanySettings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class CashFlowController extends Controller
{
    use HasCompanySettings;

    /**
     * Cash Flow Index Page
     */
    public function index(Request $request)
    {
        return view('admin.finance.cashflow.index');
    }

    /**
     * Get Cash Flow Data (Both Direct and Indirect Methods)
     */
    public function getData(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', null);
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $method = $request->get('method', 'direct'); // direct or indirect
            
            if (!$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal mulai dan akhir harus diisi'
                ], 422);
            }
            
            // Calculate cash flow components
            $operating = $method === 'indirect' 
                ? $this->calculateOperatingCashFlowIndirect($outletId, $bookId, $startDate, $endDate)
                : $this->calculateOperatingCashFlowDirect($outletId, $bookId, $startDate, $endDate);
                
            $investing = $this->calculateInvestingCashFlow($outletId, $bookId, $startDate, $endDate);
            $financing = $this->calculateFinancingCashFlow($outletId, $bookId, $startDate, $endDate);
            
            // Calculate totals
            $netCashFlow = $operating['total'] + $investing['total'] + $financing['total'];
            
            // Get beginning and ending cash
            $beginningCash = $this->getBeginningCash($outletId, $bookId, $startDate);
            $endingCash = $beginningCash + $netCashFlow;
            
            // Calculate ratios
            $ratios = $this->calculateCashFlowRatios($outletId, $bookId, $startDate, $endDate, $operating['total']);
            
            // Calculate forecast
            $forecast = $this->calculateCashFlowForecast($outletId, $bookId, $endDate);
            
            // Get trend data for charts
            $trendData = $this->getCashFlowTrend($outletId, $bookId, $endDate, 6);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'method' => $method,
                    'operating' => $operating,
                    'investing' => $investing,
                    'financing' => $financing,
                    'net_cash_flow' => $netCashFlow,
                    'beginning_cash' => $beginningCash,
                    'ending_cash' => $endingCash,
                    'stats' => [
                        'netCashFlow' => $netCashFlow,
                        'operatingCash' => $operating['total'],
                        'investingCash' => $investing['total'],
                        'financingCash' => $financing['total'],
                        'cashAtBeginning' => $beginningCash,
                        'cashAtEnd' => $endingCash
                    ],
                    'ratios' => $ratios,
                    'forecast' => $forecast,
                    'trend' => $trendData
                ],
                'message' => 'Data arus kas berhasil diambil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting cash flow data: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data arus kas: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateOperatingCashFlowDirect($outletId, $bookId, $startDate, $endDate)
    {
        // Get revenue accounts (cash inflows) with hierarchy
        $revenueAccounts = $this->getAccountDetailsWithHierarchy($outletId, $bookId, $startDate, $endDate, ['revenue', 'otherrevenue']);
        $cashReceipts = array_sum(array_column($revenueAccounts, 'amount'));
        
        // Get expense accounts (cash outflows) with hierarchy
        $expenseAccounts = $this->getAccountDetailsWithHierarchy($outletId, $bookId, $startDate, $endDate, ['expense', 'otherexpense']);
        $cashPayments = array_sum(array_column($expenseAccounts, 'amount'));
        
        $items = [];
        
        // Cash receipts from customers with hierarchy - FIXED
        if (count($revenueAccounts) > 0) {
            $items[] = [
                'id' => 'revenue_header',
                'name' => 'Penerimaan Kas dari Pelanggan',
                'amount' => $cashReceipts,
                'level' => 0,
                'is_header' => true,
                'is_parent' => false,
                'children' => $this->filterParentAccounts($revenueAccounts) // Filter untuk hapus parent yang double
            ];
        }
        
        // Cash payments to suppliers and employees with hierarchy - FIXED
        // Note: $cashPayments is already negative (credit - debit for expenses)
        // So we keep it negative to show as cash outflow
        if (count($expenseAccounts) > 0) {
            $items[] = [
                'id' => 'expense_header',
                'name' => 'Pembayaran Kas kepada Pemasok dan Karyawan',
                'amount' => $cashPayments, // Keep negative (cash outflow)
                'level' => 0,
                'is_header' => true,
                'is_parent' => false,
                'children' => $this->filterParentAccounts($expenseAccounts) // Filter untuk hapus parent yang double
            ];
        }
        
        // Total: cashReceipts (positive) + cashPayments (negative) = net operating cash flow
        // Since $cashPayments is already negative, we add them
        $total = $cashReceipts + $cashPayments;
        
        return [
            'items' => $items,
            'total' => $total
        ];
    }

    private function filterParentAccounts($accounts)
    {
        $filtered = [];
        
        foreach ($accounts as $account) {
            // Jika account punya children, kita hanya tampilkan children-nya saja
            if (isset($account['has_children']) && $account['has_children'] && !empty($account['children'])) {
                // Tambahkan semua children
                foreach ($account['children'] as $child) {
                    $filtered[] = $child;
                }
            } else {
                // Account tanpa children, tambahkan seperti biasa
                $filtered[] = $account;
            }
        }
        
        return $filtered;
    }

    /**
     * Calculate Operating Cash Flow (Indirect Method)
     */
    private function calculateOperatingCashFlowIndirect($outletId, $bookId, $startDate, $endDate)
    {
        // 1. Get Net Income (Laba Bersih)
        $revenue = $this->getCashFlowByAccountType($outletId, $bookId, $startDate, $endDate, ['revenue', 'otherrevenue']);
        $expense = $this->getCashFlowByAccountType($outletId, $bookId, $startDate, $endDate, ['expense', 'otherexpense']);
        $netIncome = $revenue - $expense;
        
        // 2. Adjustments (Penyesuaian)
        $adjustments = [];
        
        // a. Depreciation (Penyusutan) - Add back non-cash expense
        $depreciation = \App\Models\FixedAssetDepreciation::whereHas('fixedAsset', function($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            })
            ->whereBetween('depreciation_date', [$startDate, $endDate])
            ->where('status', 'posted') // Only posted depreciations
            ->sum('amount'); // Column name is 'amount' not 'depreciation_amount'
        
        // Get depreciation expense account
        $depreciationAccount = ChartOfAccount::where('outlet_id', $outletId)
            ->where('status', 'active')
            ->where(function($q) {
                $q->where('name', 'like', '%penyusutan%')
                  ->orWhere('name', 'like', '%depreciation%');
            })
            ->first();
        
        if ($depreciation > 0) {
            $adjustments[] = [
                'id' => 'depreciation',
                'account_id' => $depreciationAccount ? $depreciationAccount->id : null,
                'code' => $depreciationAccount ? $depreciationAccount->code : null,
                'description' => 'Penyusutan',
                'amount' => $depreciation,
                'note' => 'Beban non-kas',
                'level' => 1
            ];
        }
        
        // b. Changes in Working Capital (Perubahan Modal Kerja)
        // Get previous period for comparison
        $previousStart = date('Y-m-d', strtotime($startDate . ' -1 year'));
        $previousEnd = date('Y-m-d', strtotime($endDate . ' -1 year'));
        
        // Accounts Receivable (Piutang Usaha)
        $currentAR = $this->getAccountBalanceByType($outletId, $bookId, $endDate, 'asset', 'piutang');
        $previousAR = $this->getAccountBalanceByType($outletId, $bookId, $previousEnd, 'asset', 'piutang');
        $arChange = $currentAR - $previousAR;
        
        // Get AR account
        $arAccount = ChartOfAccount::where('outlet_id', $outletId)
            ->where('status', 'active')
            ->where('type', 'asset')
            ->where(function($q) {
                $q->where('name', 'like', '%piutang%')
                  ->orWhere('name', 'like', '%receivable%');
            })
            ->first();
        
        if (abs($arChange) > 0.01) {
            $adjustments[] = [
                'id' => 'ar_change',
                'account_id' => $arAccount ? $arAccount->id : null,
                'code' => $arAccount ? $arAccount->code : null,
                'description' => 'Perubahan Piutang Usaha',
                'amount' => -$arChange, // Increase in AR decreases cash
                'note' => $arChange > 0 ? 'Peningkatan' : 'Penurunan',
                'level' => 1
            ];
        }
        
        // Inventory (Persediaan)
        $currentInventory = $this->getAccountBalanceByType($outletId, $bookId, $endDate, 'asset', 'persediaan');
        $previousInventory = $this->getAccountBalanceByType($outletId, $bookId, $previousEnd, 'asset', 'persediaan');
        $inventoryChange = $currentInventory - $previousInventory;
        
        // Get inventory account
        $inventoryAccount = ChartOfAccount::where('outlet_id', $outletId)
            ->where('status', 'active')
            ->where('type', 'asset')
            ->where(function($q) {
                $q->where('name', 'like', '%persediaan%')
                  ->orWhere('name', 'like', '%inventory%');
            })
            ->first();
        
        if (abs($inventoryChange) > 0.01) {
            $adjustments[] = [
                'id' => 'inventory_change',
                'account_id' => $inventoryAccount ? $inventoryAccount->id : null,
                'code' => $inventoryAccount ? $inventoryAccount->code : null,
                'description' => 'Perubahan Persediaan',
                'amount' => -$inventoryChange, // Increase in inventory decreases cash
                'note' => $inventoryChange > 0 ? 'Peningkatan' : 'Penurunan',
                'level' => 1
            ];
        }
        
        // Accounts Payable (Hutang Usaha)
        $currentAP = $this->getAccountBalanceByType($outletId, $bookId, $endDate, 'liability', 'hutang');
        $previousAP = $this->getAccountBalanceByType($outletId, $bookId, $previousEnd, 'liability', 'hutang');
        $apChange = $currentAP - $previousAP;
        
        // Get AP account
        $apAccount = ChartOfAccount::where('outlet_id', $outletId)
            ->where('status', 'active')
            ->where('type', 'liability')
            ->where(function($q) {
                $q->where('name', 'like', '%hutang%')
                  ->orWhere('name', 'like', '%payable%');
            })
            ->first();
        
        if (abs($apChange) > 0.01) {
            $adjustments[] = [
                'id' => 'ap_change',
                'account_id' => $apAccount ? $apAccount->id : null,
                'code' => $apAccount ? $apAccount->code : null,
                'description' => 'Perubahan Hutang Usaha',
                'amount' => $apChange, // Increase in AP increases cash
                'note' => $apChange > 0 ? 'Peningkatan' : 'Penurunan',
                'level' => 1
            ];
        }
        
        // Calculate total adjustments
        $totalAdjustments = array_sum(array_column($adjustments, 'amount'));
        
        // Net operating cash flow = Net Income + Adjustments
        $total = $netIncome + $totalAdjustments;
        
        return [
            'net_income' => $netIncome,
            'adjustments' => $adjustments,
            'total_adjustments' => $totalAdjustments,
            'items' => [], // For consistency with direct method
            'total' => $total
        ];
    }

    /**
     * Get account balance by type and keyword
     */
    private function getAccountBalanceByType($outletId, $bookId, $endDate, $type, $keyword)
    {
        $accountIds = ChartOfAccount::where('outlet_id', $outletId)
            ->where('type', $type)
            ->where('status', 'active')
            ->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('code', 'like', "%{$keyword}%");
            })
            ->pluck('id');
        
        $balance = 0;
        foreach ($accountIds as $accountId) {
            $balance += abs($this->calculateAccountBalanceUpToDate($accountId, $outletId, $endDate, $bookId));
        }
        
        return $balance;
    }

    /**
     * Calculate Investing Cash Flow
     */
    private function calculateInvestingCashFlow($outletId, $bookId, $startDate, $endDate)
    {
        // Get investment-related accounts (typically asset accounts for investments)
        $investmentAccounts = $this->getAccountDetailsWithHierarchy($outletId, $bookId, $startDate, $endDate, ['asset']);
        
        $items = [];
        $total = 0;
        
        // Filter only investment-related accounts (exclude cash, receivables, inventory)
        foreach ($investmentAccounts as $account) {
            $accountName = strtolower($account['name']);
            $accountCode = $account['code'] ?? '';
            
            // Include if it's related to fixed assets, investments, or long-term assets
            if (strpos($accountName, 'aset tetap') !== false || 
                strpos($accountName, 'investasi') !== false ||
                strpos($accountName, 'penyertaan') !== false ||
                strpos($accountCode, '1-2') === 0) { // Assuming 1-2xxx is fixed assets
                
                $items[] = $account;
                $total += $account['amount'];
            }
        }
        
        // If no specific investment accounts found, create generic items from fixed assets
        if (empty($items)) {
            // Try to find fixed asset account
            $fixedAssetAccount = ChartOfAccount::where('outlet_id', $outletId)
                ->where('status', 'active')
                ->where('type', 'asset')
                ->where(function($q) {
                    $q->where('name', 'like', '%aset tetap%')
                      ->orWhere('name', 'like', '%fixed asset%')
                      ->orWhere('code', 'like', '1-2%');
                })
                ->first();
            
            // Get fixed asset purchases (cash outflows)
            $assetPurchases = FixedAsset::where('outlet_id', $outletId)
                ->whereBetween('acquisition_date', [$startDate, $endDate])
                ->when($bookId, function($query) use ($bookId) {
                    $query->where('book_id', $bookId);
                })
                ->sum('acquisition_cost');
            
            // Get asset disposals (cash inflows)
            $assetDisposals = FixedAsset::where('outlet_id', $outletId)
                ->where('status', 'disposed')
                ->whereBetween('disposal_date', [$startDate, $endDate])
                ->when($bookId, function($query) use ($bookId) {
                    $query->where('book_id', $bookId);
                })
                ->sum('disposal_value');
            
            if ($assetPurchases > 0) {
                $items[] = [
                    'id' => $fixedAssetAccount ? $fixedAssetAccount->id : 'asset_purchase',
                    'account_id' => $fixedAssetAccount ? $fixedAssetAccount->id : 'fixed_asset_purchase', // Use special ID if no account
                    'code' => $fixedAssetAccount ? $fixedAssetAccount->code : '1-2',
                    'name' => 'Pembelian Aset Tetap',
                    'amount' => -$assetPurchases,
                    'level' => 1,
                    'is_header' => false,
                    'children' => [],
                    'description' => 'Pembelian Aset Tetap'
                ];
            }
            
            if ($assetDisposals > 0) {
                $items[] = [
                    'id' => $fixedAssetAccount ? $fixedAssetAccount->id : 'asset_disposal',
                    'account_id' => $fixedAssetAccount ? $fixedAssetAccount->id : null,
                    'code' => $fixedAssetAccount ? $fixedAssetAccount->code : null,
                    'name' => 'Penjualan Aset Tetap',
                    'amount' => $assetDisposals,
                    'level' => 1,
                    'is_header' => false,
                    'children' => [],
                    'description' => 'Penjualan Aset Tetap'
                ];
            }
            
            $total = $assetDisposals - $assetPurchases;
        }
        
        return [
            'items' => $items,
            'total' => $total
        ];
    }

    /**
     * Calculate Financing Cash Flow
     */
    private function calculateFinancingCashFlow($outletId, $bookId, $startDate, $endDate)
    {
        // Get liability and equity accounts with hierarchy
        $liabilityAccounts = $this->getAccountDetailsWithHierarchy($outletId, $bookId, $startDate, $endDate, ['liability']);
        $equityAccounts = $this->getAccountDetailsWithHierarchy($outletId, $bookId, $startDate, $endDate, ['equity']);
        
        $items = [];
        
        // Add liability accounts (loans, etc.)
        foreach ($liabilityAccounts as $account) {
            $accountName = strtolower($account['name']);
            
            // Filter only long-term liabilities (exclude short-term payables)
            if (strpos($accountName, 'hutang jangka panjang') !== false || 
                strpos($accountName, 'pinjaman') !== false ||
                strpos($accountName, 'obligasi') !== false ||
                strpos($accountName, 'kredit') !== false) {
                
                $items[] = $account;
            }
        }
        
        // Add equity accounts (capital, dividends)
        foreach ($equityAccounts as $account) {
            $items[] = $account;
        }
        
        // If no specific accounts found, create generic items
        if (empty($items)) {
            // Try to find loan/liability account
            $loanAccount = ChartOfAccount::where('outlet_id', $outletId)
                ->where('status', 'active')
                ->where('type', 'liability')
                ->where(function($q) {
                    $q->where('name', 'like', '%pinjaman%')
                      ->orWhere('name', 'like', '%hutang jangka panjang%')
                      ->orWhere('name', 'like', '%loan%');
                })
                ->first();
            
            // Try to find equity/capital account
            $equityAccount = ChartOfAccount::where('outlet_id', $outletId)
                ->where('status', 'active')
                ->where('type', 'equity')
                ->where(function($q) {
                    $q->where('name', 'like', '%modal%')
                      ->orWhere('name', 'like', '%capital%')
                      ->orWhere('name', 'like', '%ekuitas%');
                })
                ->first();
            
            // Get liability accounts (loans, etc.)
            $loanProceeds = $this->getCashFlowByAccountType($outletId, $bookId, $startDate, $endDate, ['liability'], 'credit');
            $loanRepayments = $this->getCashFlowByAccountType($outletId, $bookId, $startDate, $endDate, ['liability'], 'debit');
            
            // Get equity accounts (capital contributions, dividends)
            $capitalContributions = $this->getCashFlowByAccountType($outletId, $bookId, $startDate, $endDate, ['equity'], 'credit');
            $dividends = $this->getCashFlowByAccountType($outletId, $bookId, $startDate, $endDate, ['equity'], 'debit');
            
            if ($loanProceeds > 0) {
                $items[] = [
                    'id' => $loanAccount ? $loanAccount->id : 'loan_proceeds',
                    'account_id' => $loanAccount ? $loanAccount->id : null,
                    'code' => $loanAccount ? $loanAccount->code : null,
                    'name' => 'Penerimaan Pinjaman',
                    'amount' => $loanProceeds,
                    'level' => 1,
                    'is_header' => false,
                    'children' => [],
                    'description' => 'Penerimaan Pinjaman'
                ];
            }
            
            if ($loanRepayments > 0) {
                $items[] = [
                    'id' => $loanAccount ? $loanAccount->id : 'loan_repayments',
                    'account_id' => $loanAccount ? $loanAccount->id : null,
                    'code' => $loanAccount ? $loanAccount->code : null,
                    'name' => 'Pembayaran Pinjaman',
                    'amount' => -$loanRepayments,
                    'level' => 1,
                    'is_header' => false,
                    'children' => [],
                    'description' => 'Pembayaran Pinjaman'
                ];
            }
            
            if ($capitalContributions > 0) {
                $items[] = [
                    'id' => $equityAccount ? $equityAccount->id : 'capital_contributions',
                    'account_id' => $equityAccount ? $equityAccount->id : null,
                    'code' => $equityAccount ? $equityAccount->code : null,
                    'name' => 'Setoran Modal',
                    'amount' => $capitalContributions,
                    'level' => 1,
                    'is_header' => false,
                    'children' => [],
                    'description' => 'Setoran Modal'
                ];
            }
            
            if ($dividends > 0) {
                $items[] = [
                    'id' => $equityAccount ? $equityAccount->id : 'dividends',
                    'account_id' => $equityAccount ? $equityAccount->id : null,
                    'code' => $equityAccount ? $equityAccount->code : null,
                    'name' => 'Pembayaran Dividen',
                    'amount' => -$dividends,
                    'level' => 1,
                    'is_header' => false,
                    'children' => [],
                    'description' => 'Pembayaran Dividen'
                ];
            }
            
            $total = ($loanProceeds + $capitalContributions) - ($loanRepayments + $dividends);
        } else {
            // Calculate total from items
            $total = array_sum(array_column($items, 'amount'));
        }
        
        return [
            'items' => $items,
            'total' => $total
        ];
    }

    /**
     * Get cash flow by account type
     */
    private function getCashFlowByAccountType($outletId, $bookId, $startDate, $endDate, $types, $side = null)
    {
        $accountIds = ChartOfAccount::where('outlet_id', $outletId)
            ->whereIn('type', $types)
            ->where('status', 'active')
            ->pluck('id');
        
        $query = JournalEntryDetail::whereHas('journalEntry', function($q) use ($outletId, $bookId, $startDate, $endDate) {
                $q->where('outlet_id', $outletId)
                    ->where('status', 'posted')
                    ->whereBetween('transaction_date', [$startDate, $endDate]);
                
                if ($bookId) {
                    $q->where('book_id', $bookId);
                }
            })
            ->whereIn('account_id', $accountIds);
        
        if ($side === 'debit') {
            return $query->sum('debit');
        } elseif ($side === 'credit') {
            return $query->sum('credit');
        } else {
            // Net amount (credit - debit for revenue, debit - credit for expense)
            $debit = $query->sum('debit');
            $credit = $query->sum('credit');
            
            // For revenue accounts, credit is positive
            if (in_array('revenue', $types) || in_array('otherrevenue', $types)) {
                return $credit - $debit;
            }
            // For expense accounts, debit is positive
            return $debit - $credit;
        }
    }

    /**
     * Get account details for cash flow items (private helper)
     */
    private function getAccountDetailsForItems($outletId, $bookId, $startDate, $endDate, $types)
    {
        $accounts = ChartOfAccount::where('outlet_id', $outletId)
            ->whereIn('type', $types)
            ->where('status', 'active')
            ->get();
        
        $details = [];
        
        foreach ($accounts as $account) {
            $amount = JournalEntryDetail::whereHas('journalEntry', function($q) use ($outletId, $bookId, $startDate, $endDate) {
                    $q->where('outlet_id', $outletId)
                        ->where('status', 'posted')
                        ->whereBetween('transaction_date', [$startDate, $endDate]);
                    
                    if ($bookId) {
                        $q->where('book_id', $bookId);
                    }
                })
                ->where('account_id', $account->id)
                ->selectRaw('SUM(credit - debit) as amount')
                ->value('amount') ?? 0;
            
            if (abs($amount) > 0.01) {
                $details[] = [
                    'account_id' => $account->id,
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'amount' => $amount
                ];
            }
        }
        
        return $details;
    }

    /**
     * Get account details with hierarchy support
     */
    private function getAccountDetailsWithHierarchy($outletId, $bookId, $startDate, $endDate, $types)
    {
        // Get all parent accounts for these types
        $parentAccounts = ChartOfAccount::where('outlet_id', $outletId)
            ->whereIn('type', $types)
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->orderBy('code')
            ->get();
        
        $details = [];
        
        foreach ($parentAccounts as $parent) {
            $accountData = $this->buildAccountHierarchy($parent, $outletId, $bookId, $startDate, $endDate);
            
            if ($accountData && abs($accountData['amount']) > 0.01) {
                $details[] = $accountData;
            }
        }
        
        return $details;
    }

    private function buildAccountHierarchy($account, $outletId, $bookId, $startDate, $endDate, $level = 1)
    {
        // Calculate amount for this account
        $amount = JournalEntryDetail::whereHas('journalEntry', function($q) use ($outletId, $bookId, $startDate, $endDate) {
                $q->where('outlet_id', $outletId)
                    ->where('status', 'posted')
                    ->whereBetween('transaction_date', [$startDate, $endDate]);
                
                if ($bookId) {
                    $q->where('book_id', $bookId);
                }
            })
            ->where('account_id', $account->id)
            ->selectRaw('SUM(credit - debit) as amount')
            ->value('amount') ?? 0;

        // Get children
        $children = ChartOfAccount::where('outlet_id', $outletId)
            ->where('parent_id', $account->id)
            ->where('status', 'active')
            ->orderBy('code')
            ->get();

        $childrenData = [];
        $childrenTotal = 0;
        
        foreach ($children as $child) {
            $childData = $this->buildAccountHierarchy($child, $outletId, $bookId, $startDate, $endDate, $level + 1);
            
            if ($childData && abs($childData['amount']) > 0.01) {
                $childrenData[] = $childData;
                $childrenTotal += $childData['amount'];
            }
        }

        // FIX: If account has children, ONLY show children (hide parent amount to avoid double counting)
        if (count($childrenData) > 0) {
            // Return parent as header only (no amount)
            return [
                'id' => $account->id,
                'account_id' => $account->id,
                'name' => $account->name,
                'code' => $account->code,
                'amount' => $childrenTotal, // Total dari children saja
                'level' => $level,
                'is_header' => true,
                'is_parent' => true, // Flag baru untuk identifikasi parent
                'children' => $childrenData,
                'has_children' => true
            ];
        } else {
            // No children, show account with its own amount
            if (abs($amount) < 0.01) {
                return null;
            }
            
            return [
                'id' => $account->id,
                'account_id' => $account->id,
                'name' => $account->name,
                'code' => $account->code,
                'amount' => $amount,
                'level' => $level,
                'is_header' => false,
                'is_parent' => false,
                'children' => [],
                'has_children' => false
            ];
        }
    }

    /**
     * Get beginning cash balance
     */
    private function getBeginningCash($outletId, $bookId, $startDate)
    {
        // Get cash and bank accounts
        $cashAccountIds = ChartOfAccount::where('outlet_id', $outletId)
            ->where('type', 'asset')
            ->where('status', 'active')
            ->where(function($q) {
                $q->where('code', 'like', '1-1%')
                  ->orWhere('name', 'like', '%kas%')
                  ->orWhere('name', 'like', '%bank%');
            })
            ->pluck('id');
        
        $balance = 0;
        $previousDate = date('Y-m-d', strtotime($startDate . ' -1 day'));
        
        foreach ($cashAccountIds as $accountId) {
            $balance += $this->calculateAccountBalanceUpToDate($accountId, $outletId, $previousDate, $bookId);
        }
        
        return $balance;
    }

    /**
     * Calculate account balance up to specific date
     */
    private function calculateAccountBalanceUpToDate($accountId, $outletId, $endDate, $bookId = null)
    {
        // Get opening balance (debit - credit)
        $openingBalanceRecord = \App\Models\OpeningBalance::where('account_id', $accountId)
            ->where('outlet_id', $outletId)
            ->where('effective_date', '<=', $endDate)
            ->when($bookId, function($query) use ($bookId) {
                $query->where('book_id', $bookId);
            })
            ->orderBy('effective_date', 'desc')
            ->first();
        
        $openingBalance = 0;
        if ($openingBalanceRecord) {
            $openingBalance = floatval($openingBalanceRecord->debit) - floatval($openingBalanceRecord->credit);
        }
        
        // Get journal entries balance up to end date
        $journalBalance = JournalEntryDetail::whereHas('journalEntry', function($query) use ($outletId, $endDate, $bookId) {
                $query->where('outlet_id', $outletId)
                    ->where('status', 'posted')
                    ->where('transaction_date', '<=', $endDate);
                
                if ($bookId) {
                    $query->where('book_id', $bookId);
                }
            })
            ->where('account_id', $accountId)
            ->selectRaw('SUM(debit - credit) as balance')
            ->value('balance') ?? 0;
        
        return floatval($openingBalance) + floatval($journalBalance);
    }

    /**
     * Get cash flow item details (breakdown of specific cash flow items)
     */
    public function getItemDetails(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $itemType = $request->get('item_type'); // 'asset_purchase', 'depreciation', 'revenue', etc.
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $bookId = $request->get('book_id', null);
            
            $data = [];
            
            switch ($itemType) {
                case 'asset_purchase':
                    // Get fixed assets purchased in period
                    $assets = FixedAsset::where('outlet_id', $outletId)
                        ->whereBetween('acquisition_date', [$startDate, $endDate])
                        ->when($bookId, function($q) use ($bookId) {
                            $q->where('book_id', $bookId);
                        })
                        ->get()
                        ->map(function($asset) {
                            return [
                                'id' => $asset->id,
                                'name' => $asset->name,
                                'code' => $asset->code,
                                'category' => $asset->category,
                                'acquisition_date' => $asset->acquisition_date,
                                'acquisition_cost' => $asset->acquisition_cost,
                                'useful_life' => $asset->useful_life,
                                'depreciation_method' => $asset->depreciation_method,
                            ];
                        });
                    
                    $data = [
                        'title' => 'Detail Pembelian Aset Tetap',
                        'type' => 'fixed_assets',
                        'items' => $assets,
                        'summary' => [
                            'total_items' => $assets->count(),
                            'total_amount' => $assets->sum('acquisition_cost')
                        ]
                    ];
                    break;
                    
                case 'asset_disposal':
                    // Get fixed assets disposed in period
                    $assets = FixedAsset::where('outlet_id', $outletId)
                        ->where('status', 'disposed')
                        ->whereBetween('disposal_date', [$startDate, $endDate])
                        ->when($bookId, function($q) use ($bookId) {
                            $q->where('book_id', $bookId);
                        })
                        ->get()
                        ->map(function($asset) {
                            return [
                                'id' => $asset->id,
                                'name' => $asset->name,
                                'code' => $asset->code,
                                'category' => $asset->category,
                                'disposal_date' => $asset->disposal_date,
                                'disposal_value' => $asset->disposal_value,
                                'book_value' => $asset->book_value,
                                'gain_loss' => $asset->disposal_value - $asset->book_value,
                            ];
                        });
                    
                    $data = [
                        'title' => 'Detail Penjualan Aset Tetap',
                        'type' => 'fixed_assets_disposal',
                        'items' => $assets,
                        'summary' => [
                            'total_items' => $assets->count(),
                            'total_amount' => $assets->sum('disposal_value')
                        ]
                    ];
                    break;
                    
                case 'depreciation':
                    // Get depreciation details
                    $depreciations = \App\Models\FixedAssetDepreciation::whereHas('fixedAsset', function($q) use ($outletId) {
                            $q->where('outlet_id', $outletId);
                        })
                        ->whereBetween('depreciation_date', [$startDate, $endDate])
                        ->where('status', 'posted')
                        ->with('fixedAsset')
                        ->get()
                        ->map(function($dep) {
                            return [
                                'id' => $dep->id,
                                'asset_name' => $dep->fixedAsset->name ?? '-',
                                'asset_code' => $dep->fixedAsset->code ?? '-',
                                'depreciation_date' => $dep->depreciation_date,
                                'amount' => $dep->amount,
                                'accumulated_depreciation' => $dep->accumulated_depreciation,
                                'book_value' => $dep->book_value,
                            ];
                        });
                    
                    $data = [
                        'title' => 'Detail Penyusutan Aset Tetap',
                        'type' => 'depreciation',
                        'items' => $depreciations,
                        'summary' => [
                            'total_items' => $depreciations->count(),
                            'total_amount' => $depreciations->sum('amount')
                        ]
                    ];
                    break;
                    
                default:
                    // For account-based items, get account breakdown
                    $accountId = $request->get('account_id');
                    if ($accountId) {
                        return $this->getAccountDetails($accountId, $request);
                    }
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Item type tidak dikenali'
                    ], 400);
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting cash flow item details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cash flow account transaction details
     */
    public function getAccountDetails($accountId, Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', null);
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            $account = ChartOfAccount::find($accountId);
            
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }
            
            // Get transactions
            $transactions = JournalEntry::where('outlet_id', $outletId)
                ->where('status', 'posted')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->when($bookId, function($q) use ($bookId) {
                    $q->where('book_id', $bookId);
                })
                ->whereHas('journalEntryDetails', function($q) use ($accountId) {
                    $q->where('account_id', $accountId);
                })
                ->with(['journalEntryDetails' => function($q) use ($accountId) {
                    $q->where('account_id', $accountId);
                }, 'book'])
                ->orderBy('transaction_date', 'asc')
                ->get()
                ->map(function($entry) {
                    $detail = $entry->journalEntryDetails->first();
                    
                    return [
                        'id' => $entry->id,
                        'transaction_date' => $entry->transaction_date,
                        'transaction_number' => $entry->transaction_number,
                        'description' => $entry->description,
                        'debit' => $detail->debit ?? 0,
                        'credit' => $detail->credit ?? 0,
                        'book_name' => $entry->book->name ?? '-',
                    ];
                });
            
            $totalDebit = $transactions->sum('debit');
            $totalCredit = $transactions->sum('credit');
            $netCashFlow = $totalCredit - $totalDebit;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'account' => [
                        'id' => $account->id,
                        'code' => $account->code,
                        'name' => $account->name,
                        'type' => $account->type,
                    ],
                    'transactions' => $transactions,
                    'summary' => [
                        'total_transactions' => $transactions->count(),
                        'total_debit' => $totalDebit,
                        'total_credit' => $totalCredit,
                        'net_cash_flow' => $netCashFlow,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting cash flow account details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Cash Flow to PDF
     */
    public function exportPDF(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', null);
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $method = $request->get('method', 'direct');
            
            if (!$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal mulai dan akhir harus diisi'
                ], 422);
            }
            
            // Calculate cash flow
            $operating = $this->calculateOperatingCashFlowDirect($outletId, $bookId, $startDate, $endDate);
            $investing = $this->calculateInvestingCashFlow($outletId, $bookId, $startDate, $endDate);
            $financing = $this->calculateFinancingCashFlow($outletId, $bookId, $startDate, $endDate);
            
            $netCashFlow = $operating['total'] + $investing['total'] + $financing['total'];
            $beginningCash = $this->getBeginningCash($outletId, $bookId, $startDate);
            $endingCash = $beginningCash + $netCashFlow;
            
            // Get outlet and book info
            $outlet = Outlet::find($outletId);
            $book = $bookId ? AccountingBook::find($bookId) : null;
            
            // Get company settings
            $companySettings = $this->getCompanySettings();
            
            $data = [
                'operatingActivities' => $operating['items'],
                'investingActivities' => $investing['items'],
                'financingActivities' => $financing['items'],
                'netOperating' => $operating['total'],
                'netInvesting' => $investing['total'],
                'netFinancing' => $financing['total'],
                'netCashFlow' => $netCashFlow,
                'beginningCash' => $beginningCash,
                'endingCash' => $endingCash,
                'startDate' => date('d/m/Y', strtotime($startDate)),
                'endDate' => date('d/m/Y', strtotime($endDate)),
                'method' => $method,
                'outletName' => $outlet->nama_outlet ?? 'Semua Outlet',
                'bookName' => $book->name ?? 'Semua Buku',
                'companyName' => config('app.name', 'PT. NAMA PERUSAHAAN'),
                'companySettings' => $companySettings
            ];
            
            $pdf = Pdf::loadView('admin.finance.cashflow.pdf', $data)
                ->setPaper('a4', 'portrait');
            
            $filename = 'arus_kas_' . str_replace(' ', '_', $outlet->nama_outlet ?? 'outlet') . '_' . $startDate . '_' . $endDate . '.pdf';
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Error exporting cash flow to PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor arus kas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Cash Flow to XLSX
     */
    public function exportXLSX(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', null);
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $method = $request->get('method', 'direct');
            
            if (!$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal mulai dan akhir harus diisi'
                ], 422);
            }
            
            // Calculate cash flow
            $operating = $this->calculateOperatingCashFlowDirect($outletId, $bookId, $startDate, $endDate);
            $investing = $this->calculateInvestingCashFlow($outletId, $bookId, $startDate, $endDate);
            $financing = $this->calculateFinancingCashFlow($outletId, $bookId, $startDate, $endDate);
            
            $netCashFlow = $operating['total'] + $investing['total'] + $financing['total'];
            $beginningCash = $this->getBeginningCash($outletId, $bookId, $startDate);
            $endingCash = $beginningCash + $netCashFlow;
            
            // Get outlet and book info
            $outlet = Outlet::find($outletId);
            $book = $bookId ? AccountingBook::find($bookId) : null;
            
            $data = [
                'operating' => $operating['items'],
                'investing' => $investing['items'],
                'financing' => $financing['items'],
                'netOperating' => $operating['total'],
                'netInvesting' => $investing['total'],
                'netFinancing' => $financing['total'],
                'netCashFlow' => $netCashFlow,
                'beginningCash' => $beginningCash,
                'endingCash' => $endingCash
            ];
            
            $filters = [
                'start_date' => date('d/m/Y', strtotime($startDate)),
                'end_date' => date('d/m/Y', strtotime($endDate)),
                'method' => $method,
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'book_name' => $book->name ?? 'Semua Buku'
            ];
            
            $filename = 'arus_kas_' . str_replace(' ', '_', $outlet->nama_outlet ?? 'outlet') . '_' . $startDate . '_' . $endDate . '.xlsx';
            
            return Excel::download(new \App\Exports\CashFlowExport($data, $filters), $filename);
            
        } catch (\Exception $e) {
            \Log::error('Error exporting cash flow to XLSX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor arus kas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate Cash Flow Ratios
     */
    private function calculateCashFlowRatios($outletId, $bookId, $startDate, $endDate, $operatingCash)
    {
        // Get current liabilities
        $liabilityAccountIds = ChartOfAccount::where('outlet_id', $outletId)
            ->where('type', 'liability')
            ->where('status', 'active')
            ->where(function($q) {
                $q->where('code', 'like', '2-1%')
                  ->orWhere('name', 'like', '%lancar%')
                  ->orWhere('name', 'like', '%jangka pendek%');
            })
            ->pluck('id');
        
        $currentLiabilities = 0;
        foreach ($liabilityAccountIds as $accountId) {
            $currentLiabilities += abs($this->calculateAccountBalanceUpToDate($accountId, $outletId, $endDate, $bookId));
        }
        
        // Operating Cash Flow Ratio = Operating Cash Flow / Current Liabilities
        $operatingRatio = $currentLiabilities > 0 ? round($operatingCash / $currentLiabilities, 2) : 0;
        
        // Get revenue for Cash Flow Margin
        $revenue = $this->getCashFlowByAccountType($outletId, $bookId, $startDate, $endDate, ['revenue', 'otherrevenue']);
        
        // Cash Flow Margin = Operating Cash Flow / Revenue * 100
        $cashFlowMargin = $revenue > 0 ? round(($operatingCash / $revenue) * 100, 1) : 0;
        
        // Free Cash Flow = Operating Cash Flow - Capital Expenditures
        $capex = FixedAsset::where('outlet_id', $outletId)
            ->whereBetween('acquisition_date', [$startDate, $endDate])
            ->when($bookId, function($query) use ($bookId) {
                $query->where('book_id', $bookId);
            })
            ->sum('acquisition_cost');
        
        $freeCashFlow = $operatingCash - $capex;
        
        return [
            'operatingRatio' => number_format($operatingRatio, 2),
            'cashFlowMargin' => number_format($cashFlowMargin, 1),
            'freeCashFlow' => $freeCashFlow,
            'currentLiabilities' => $currentLiabilities,
            'revenue' => $revenue,
            'capex' => $capex
        ];
    }

    /**
     * Calculate Cash Flow Forecast (3 months)
     */
    private function calculateCashFlowForecast($outletId, $bookId, $endDate)
    {
        $forecast = [];
        
        // Get historical average for the last 3 months
        $historicalMonths = [];
        for ($i = 3; $i >= 1; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months", strtotime($endDate)));
            $monthEnd = date('Y-m-t', strtotime("-$i months", strtotime($endDate)));
            
            $operating = $this->calculateOperatingCashFlowDirect($outletId, $bookId, $monthStart, $monthEnd);
            $investing = $this->calculateInvestingCashFlow($outletId, $bookId, $monthStart, $monthEnd);
            $financing = $this->calculateFinancingCashFlow($outletId, $bookId, $monthStart, $monthEnd);
            
            $historicalMonths[] = $operating['total'] + $investing['total'] + $financing['total'];
        }
        
        $avgCashFlow = count($historicalMonths) > 0 ? array_sum($historicalMonths) / count($historicalMonths) : 0;
        
        // Project next 3 months with 5% growth assumption
        $growthRate = 1.05;
        $projectedAmount = $avgCashFlow;
        
        for ($i = 1; $i <= 3; $i++) {
            $forecastDate = date('Y-m-01', strtotime("+$i months", strtotime($endDate)));
            $monthName = date('F Y', strtotime($forecastDate));
            $quarter = 'Q' . ceil(date('n', strtotime($forecastDate)) / 3) . ' ' . date('Y', strtotime($forecastDate));
            
            $projectedAmount = $projectedAmount * $growthRate;
            
            // Prevent division by zero
            if ($avgCashFlow != 0) {
                $trend = $i == 1 ? '+5%' : '+' . round((($projectedAmount / $avgCashFlow) - 1) * 100, 1) . '%';
            } else {
                $trend = 'N/A';
            }
            
            $forecast[] = [
                'month' => $monthName,
                'period' => $quarter,
                'amount' => round($projectedAmount, 2),
                'trend' => $trend
            ];
        }
        
        return $forecast;
    }

    /**
     * Get Cash Flow Trend Data for Charts
     */
    private function getCashFlowTrend($outletId, $bookId, $endDate, $months = 6)
    {
        $trendData = [
            'labels' => [],
            'operating' => [],
            'investing' => [],
            'financing' => []
        ];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months", strtotime($endDate)));
            $monthEnd = date('Y-m-t', strtotime("-$i months", strtotime($endDate)));
            
            $operating = $this->calculateOperatingCashFlowDirect($outletId, $bookId, $monthStart, $monthEnd);
            $investing = $this->calculateInvestingCashFlow($outletId, $bookId, $monthStart, $monthEnd);
            $financing = $this->calculateFinancingCashFlow($outletId, $bookId, $monthStart, $monthEnd);
            
            $trendData['labels'][] = date('M', strtotime($monthStart));
            $trendData['operating'][] = $operating['total'];
            $trendData['investing'][] = $investing['total'];
            $trendData['financing'][] = $financing['total'];
        }
        
        return $trendData;
    }

    /**
     * Get Fixed Asset Purchase Details for Cash Flow
     */
    public function getFixedAssetPurchases(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->id_outlet ?? 1);
            $bookId = $request->get('book_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Get fixed asset purchases in the period
            $purchases = FixedAsset::with(['outlet', 'book', 'assetAccount'])
                ->where('outlet_id', $outletId)
                ->whereBetween('acquisition_date', [$startDate, $endDate])
                ->when($bookId, function($query) use ($bookId) {
                    $query->where('book_id', $bookId);
                })
                ->orderBy('acquisition_date', 'desc')
                ->get();

            $transactions = $purchases->map(function($asset) {
                return [
                    'id' => $asset->id,
                    'transaction_date' => $asset->acquisition_date->format('Y-m-d'),
                    'transaction_number' => $asset->code,
                    'description' => $asset->name . ' - ' . $asset->category,
                    'book_name' => $asset->book->name ?? '-',
                    'debit' => 0,
                    'credit' => floatval($asset->acquisition_cost),
                    'amount' => floatval($asset->acquisition_cost),
                    'asset_details' => [
                        'name' => $asset->name,
                        'code' => $asset->code,
                        'category' => $asset->category,
                        'location' => $asset->location,
                        'useful_life' => $asset->useful_life,
                        'depreciation_method' => $asset->depreciation_method,
                    ]
                ];
            });

            $summary = [
                'total_transactions' => $transactions->count(),
                'total_debit' => 0,
                'total_credit' => $transactions->sum('credit'),
                'net_cash_flow' => -$transactions->sum('credit'), // Negative because it's cash outflow
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'account' => [
                        'code' => '1-2',
                        'name' => 'Pembelian Aset Tetap',
                    ],
                    'transactions' => $transactions,
                    'summary' => $summary,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching fixed asset purchases: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembelian aset tetap: ' . $e->getMessage()
            ], 500);
        }
    }
}
