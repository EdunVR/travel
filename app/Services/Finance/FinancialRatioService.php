<?php

namespace App\Services\Finance;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use App\Services\ChartOfAccountService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialRatioService
{
    protected $coaService;

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }

    /**
     * Calculate all financial ratios
     */
    public function calculateAllRatios($outletId, $startDate, $endDate)
    {
        $balanceSheetData = $this->getBalanceSheetData($outletId, $endDate);
        $profitLossData = $this->getProfitLossData($outletId, $startDate, $endDate);

        return [
            'liquidity' => $this->calculateLiquidityRatios($balanceSheetData),
            'profitability' => $this->calculateProfitabilityRatios($balanceSheetData, $profitLossData),
            'efficiency' => $this->calculateEfficiencyRatios($balanceSheetData, $profitLossData),
            'leverage' => $this->calculateLeverageRatios($balanceSheetData),
            'market' => $this->calculateMarketRatios($balanceSheetData, $profitLossData),
        ];
    }

    /**
     * Calculate liquidity ratios
     */
    public function calculateLiquidityRatios($balanceSheetData)
    {
        $currentAssets = $balanceSheetData['current_assets'] ?? 0;
        $inventory = $balanceSheetData['inventory'] ?? 0;
        $cash = $balanceSheetData['cash'] ?? 0;
        $currentLiabilities = $balanceSheetData['current_liabilities'] ?? 0;

        $ratios = [];

        // Current Ratio = Current Assets / Current Liabilities
        $ratios['current_ratio'] = [
            'value' => $currentLiabilities > 0 ? $currentAssets / $currentLiabilities : 0,
            'benchmark' => 2.0,
            'type' => 'ratio',
            'description' => 'Kemampuan perusahaan memenuhi kewajiban jangka pendek',
            'formula' => 'Current Assets / Current Liabilities'
        ];

        // Quick Ratio = (Current Assets - Inventory) / Current Liabilities
        $quickAssets = $currentAssets - $inventory;
        $ratios['quick_ratio'] = [
            'value' => $currentLiabilities > 0 ? $quickAssets / $currentLiabilities : 0,
            'benchmark' => 1.0,
            'type' => 'ratio',
            'description' => 'Kemampuan perusahaan memenuhi kewajiban tanpa mengandalkan persediaan',
            'formula' => '(Current Assets - Inventory) / Current Liabilities'
        ];

        // Cash Ratio = Cash / Current Liabilities
        $ratios['cash_ratio'] = [
            'value' => $currentLiabilities > 0 ? $cash / $currentLiabilities : 0,
            'benchmark' => 0.2,
            'type' => 'ratio',
            'description' => 'Kemampuan perusahaan memenuhi kewajiban dengan kas',
            'formula' => 'Cash / Current Liabilities'
        ];

        return $ratios;
    }

    /**
     * Calculate profitability ratios
     */
    public function calculateProfitabilityRatios($balanceSheetData, $profitLossData)
    {
        $revenue = $profitLossData['revenue'] ?? 0;
        $grossProfit = $profitLossData['gross_profit'] ?? 0;
        $operatingProfit = $profitLossData['operating_profit'] ?? 0;
        $netProfit = $profitLossData['net_profit'] ?? 0;
        $totalAssets = $balanceSheetData['total_assets'] ?? 0;
        $totalEquity = $balanceSheetData['total_equity'] ?? 0;

        $ratios = [];

        // Gross Profit Margin = Gross Profit / Revenue
        $ratios['gross_profit_margin'] = [
            'value' => $revenue > 0 ? ($grossProfit / $revenue) : 0,
            'benchmark' => 0.30,
            'type' => 'percentage',
            'description' => 'Persentase laba kotor terhadap penjualan',
            'formula' => 'Gross Profit / Revenue'
        ];

        // Operating Profit Margin = Operating Profit / Revenue
        $ratios['operating_profit_margin'] = [
            'value' => $revenue > 0 ? ($operatingProfit / $revenue) : 0,
            'benchmark' => 0.15,
            'type' => 'percentage',
            'description' => 'Persentase laba operasional terhadap penjualan',
            'formula' => 'Operating Profit / Revenue'
        ];

        // Net Profit Margin = Net Profit / Revenue
        $ratios['net_profit_margin'] = [
            'value' => $revenue > 0 ? ($netProfit / $revenue) : 0,
            'benchmark' => 0.10,
            'type' => 'percentage',
            'description' => 'Persentase laba bersih terhadap penjualan',
            'formula' => 'Net Profit / Revenue'
        ];

        // Return on Assets (ROA) = Net Profit / Total Assets
        $ratios['return_on_assets'] = [
            'value' => $totalAssets > 0 ? ($netProfit / $totalAssets) : 0,
            'benchmark' => 0.05,
            'type' => 'percentage',
            'description' => 'Kemampuan perusahaan menghasilkan laba dari aset',
            'formula' => 'Net Profit / Total Assets'
        ];

        // Return on Equity (ROE) = Net Profit / Total Equity
        $ratios['return_on_equity'] = [
            'value' => $totalEquity > 0 ? ($netProfit / $totalEquity) : 0,
            'benchmark' => 0.15,
            'type' => 'percentage',
            'description' => 'Kemampuan perusahaan menghasilkan laba dari modal',
            'formula' => 'Net Profit / Total Equity'
        ];

        return $ratios;
    }

    /**
     * Calculate efficiency ratios
     */
    public function calculateEfficiencyRatios($balanceSheetData, $profitLossData)
    {
        $revenue = $profitLossData['revenue'] ?? 0;
        $cogs = $profitLossData['cogs'] ?? 0;
        $totalAssets = $balanceSheetData['total_assets'] ?? 0;
        $inventory = $balanceSheetData['inventory'] ?? 0;
        $receivables = $balanceSheetData['receivables'] ?? 0;

        $ratios = [];

        // Asset Turnover = Revenue / Total Assets
        $ratios['asset_turnover'] = [
            'value' => $totalAssets > 0 ? ($revenue / $totalAssets) : 0,
            'benchmark' => 1.0,
            'type' => 'times',
            'description' => 'Efisiensi penggunaan aset untuk menghasilkan penjualan',
            'formula' => 'Revenue / Total Assets'
        ];

        // Inventory Turnover = COGS / Average Inventory
        $ratios['inventory_turnover'] = [
            'value' => $inventory > 0 ? ($cogs / $inventory) : 0,
            'benchmark' => 6.0,
            'type' => 'times',
            'description' => 'Kecepatan perputaran persediaan',
            'formula' => 'COGS / Average Inventory'
        ];

        // Receivables Turnover = Revenue / Average Receivables
        $ratios['receivables_turnover'] = [
            'value' => $receivables > 0 ? ($revenue / $receivables) : 0,
            'benchmark' => 12.0,
            'type' => 'times',
            'description' => 'Kecepatan penagihan piutang',
            'formula' => 'Revenue / Average Receivables'
        ];

        // Days Sales Outstanding (DSO) = 365 / Receivables Turnover
        $receivablesTurnover = $ratios['receivables_turnover']['value'];
        $ratios['days_sales_outstanding'] = [
            'value' => $receivablesTurnover > 0 ? (365 / $receivablesTurnover) : 0,
            'benchmark' => 30,
            'type' => 'days',
            'description' => 'Rata-rata hari penagihan piutang',
            'formula' => '365 / Receivables Turnover'
        ];

        // Days Inventory Outstanding (DIO) = 365 / Inventory Turnover
        $inventoryTurnover = $ratios['inventory_turnover']['value'];
        $ratios['days_inventory_outstanding'] = [
            'value' => $inventoryTurnover > 0 ? (365 / $inventoryTurnover) : 0,
            'benchmark' => 60,
            'type' => 'days',
            'description' => 'Rata-rata hari penyimpanan persediaan',
            'formula' => '365 / Inventory Turnover'
        ];

        return $ratios;
    }

    /**
     * Calculate leverage ratios
     */
    public function calculateLeverageRatios($balanceSheetData)
    {
        $totalDebt = $balanceSheetData['total_liabilities'] ?? 0;
        $totalEquity = $balanceSheetData['total_equity'] ?? 0;
        $totalAssets = $balanceSheetData['total_assets'] ?? 0;
        $longTermDebt = $balanceSheetData['long_term_debt'] ?? 0;

        $ratios = [];

        // Debt-to-Equity Ratio = Total Debt / Total Equity
        $ratios['debt_to_equity'] = [
            'value' => $totalEquity > 0 ? ($totalDebt / $totalEquity) : 0,
            'benchmark' => 1.0,
            'type' => 'ratio',
            'description' => 'Perbandingan hutang dengan modal',
            'formula' => 'Total Debt / Total Equity'
        ];

        // Debt-to-Assets Ratio = Total Debt / Total Assets
        $ratios['debt_to_assets'] = [
            'value' => $totalAssets > 0 ? ($totalDebt / $totalAssets) : 0,
            'benchmark' => 0.5,
            'type' => 'percentage',
            'description' => 'Persentase aset yang dibiayai hutang',
            'formula' => 'Total Debt / Total Assets'
        ];

        // Equity Ratio = Total Equity / Total Assets
        $ratios['equity_ratio'] = [
            'value' => $totalAssets > 0 ? ($totalEquity / $totalAssets) : 0,
            'benchmark' => 0.5,
            'type' => 'percentage',
            'description' => 'Persentase aset yang dibiayai modal',
            'formula' => 'Total Equity / Total Assets'
        ];

        // Long-term Debt to Equity = Long-term Debt / Total Equity
        $ratios['long_term_debt_to_equity'] = [
            'value' => $totalEquity > 0 ? ($longTermDebt / $totalEquity) : 0,
            'benchmark' => 0.5,
            'type' => 'ratio',
            'description' => 'Perbandingan hutang jangka panjang dengan modal',
            'formula' => 'Long-term Debt / Total Equity'
        ];

        return $ratios;
    }

    /**
     * Calculate market ratios (if applicable)
     */
    public function calculateMarketRatios($balanceSheetData, $profitLossData)
    {
        $netProfit = $profitLossData['net_profit'] ?? 0;
        $totalEquity = $balanceSheetData['total_equity'] ?? 0;
        $revenue = $profitLossData['revenue'] ?? 0;

        $ratios = [];

        // Book Value per Share (if shares data available)
        // For now, we'll use simplified calculations
        $ratios['book_value_per_share'] = [
            'value' => $totalEquity, // Simplified - assume 1 share
            'benchmark' => null,
            'type' => 'currency',
            'description' => 'Nilai buku per saham',
            'formula' => 'Total Equity / Number of Shares'
        ];

        // Earnings per Share (simplified)
        $ratios['earnings_per_share'] = [
            'value' => $netProfit, // Simplified - assume 1 share
            'benchmark' => null,
            'type' => 'currency',
            'description' => 'Laba per saham',
            'formula' => 'Net Profit / Number of Shares'
        ];

        return $ratios;
    }

    /**
     * Get balance sheet data for ratio calculations
     */
    private function getBalanceSheetData($outletId, $date)
    {
        // Get account balances as of the date
        $accounts = ChartOfAccount::where('outlet_id', $outletId)->get();
        $data = [];

        foreach ($accounts as $account) {
            $balance = $this->coaService->calculateAccountBalance($account->id, null, $date);
            
            switch ($account->type) {
                case 'asset':
                    if (in_array(strtolower($account->name), ['kas', 'cash', 'bank'])) {
                        $data['cash'] = ($data['cash'] ?? 0) + $balance;
                    }
                    if (in_array(strtolower($account->name), ['piutang', 'receivable', 'accounts receivable'])) {
                        $data['receivables'] = ($data['receivables'] ?? 0) + $balance;
                    }
                    if (in_array(strtolower($account->name), ['persediaan', 'inventory', 'stock'])) {
                        $data['inventory'] = ($data['inventory'] ?? 0) + $balance;
                    }
                    
                    // Classify as current or fixed asset based on account name/category
                    if ($this->isCurrentAsset($account)) {
                        $data['current_assets'] = ($data['current_assets'] ?? 0) + $balance;
                    } else {
                        $data['fixed_assets'] = ($data['fixed_assets'] ?? 0) + $balance;
                    }
                    
                    $data['total_assets'] = ($data['total_assets'] ?? 0) + $balance;
                    break;
                    
                case 'liability':
                    if ($this->isCurrentLiability($account)) {
                        $data['current_liabilities'] = ($data['current_liabilities'] ?? 0) + $balance;
                    } else {
                        $data['long_term_debt'] = ($data['long_term_debt'] ?? 0) + $balance;
                    }
                    
                    $data['total_liabilities'] = ($data['total_liabilities'] ?? 0) + $balance;
                    break;
                    
                case 'equity':
                    $data['total_equity'] = ($data['total_equity'] ?? 0) + $balance;
                    break;
            }
        }

        return $data;
    }

    /**
     * Get profit & loss data for ratio calculations
     */
    private function getProfitLossData($outletId, $startDate, $endDate)
    {
        $accounts = ChartOfAccount::where('outlet_id', $outletId)->get();
        $data = [];

        foreach ($accounts as $account) {
            $balance = $this->coaService->calculateAccountBalance($account->id, $startDate, $endDate);
            
            switch ($account->type) {
                case 'revenue':
                    $data['revenue'] = ($data['revenue'] ?? 0) + $balance;
                    break;
                    
                case 'expense':
                    if ($this->isCOGS($account)) {
                        $data['cogs'] = ($data['cogs'] ?? 0) + $balance;
                    } else {
                        $data['operating_expense'] = ($data['operating_expense'] ?? 0) + $balance;
                    }
                    break;
                    
                case 'otherrevenue':
                    $data['other_revenue'] = ($data['other_revenue'] ?? 0) + $balance;
                    break;
                    
                case 'otherexpense':
                    $data['other_expense'] = ($data['other_expense'] ?? 0) + $balance;
                    break;
            }
        }

        // Calculate derived values
        $data['gross_profit'] = ($data['revenue'] ?? 0) - ($data['cogs'] ?? 0);
        $data['operating_profit'] = $data['gross_profit'] - ($data['operating_expense'] ?? 0);
        $data['net_profit'] = $data['operating_profit'] + ($data['other_revenue'] ?? 0) - ($data['other_expense'] ?? 0);

        return $data;
    }

    /**
     * Determine if account is current asset
     */
    private function isCurrentAsset($account)
    {
        $currentAssetKeywords = ['kas', 'bank', 'piutang', 'persediaan', 'inventory', 'cash', 'receivable', 'prepaid'];
        
        foreach ($currentAssetKeywords as $keyword) {
            if (stripos($account->name, $keyword) !== false || stripos($account->category, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Determine if account is current liability
     */
    private function isCurrentLiability($account)
    {
        $currentLiabilityKeywords = ['hutang', 'payable', 'utang', 'accrued', 'short', 'pendek'];
        
        foreach ($currentLiabilityKeywords as $keyword) {
            if (stripos($account->name, $keyword) !== false || stripos($account->category, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Determine if account is COGS
     */
    private function isCOGS($account)
    {
        $cogsKeywords = ['hpp', 'cogs', 'cost of goods', 'harga pokok', 'beban pokok'];
        
        foreach ($cogsKeywords as $keyword) {
            if (stripos($account->name, $keyword) !== false || stripos($account->category, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get ratio trend analysis
     */
    public function getRatioTrend($outletId, $periods)
    {
        $trends = [];
        
        foreach ($periods as $period) {
            $startDate = $period['start_date'];
            $endDate = $period['end_date'];
            
            $ratios = $this->calculateAllRatios($outletId, $startDate, $endDate);
            $trends[$period['label']] = $ratios;
        }
        
        return $trends;
    }

    /**
     * Generate ratio interpretation
     */
    public function interpretRatio($ratioName, $value, $benchmark = null)
    {
        $interpretations = [
            'current_ratio' => [
                'good' => 'Likuiditas baik, mampu memenuhi kewajiban jangka pendek',
                'fair' => 'Likuiditas cukup, perlu perhatian pada manajemen kas',
                'poor' => 'Likuiditas rendah, kesulitan memenuhi kewajiban jangka pendek'
            ],
            'debt_to_equity' => [
                'good' => 'Struktur modal seimbang antara hutang dan modal',
                'fair' => 'Ketergantungan pada hutang cukup tinggi',
                'poor' => 'Ketergantungan pada hutang sangat tinggi, berisiko'
            ],
            'net_profit_margin' => [
                'good' => 'Profitabilitas baik, efisiensi operasional tinggi',
                'fair' => 'Profitabilitas cukup, masih bisa ditingkatkan',
                'poor' => 'Profitabilitas rendah, perlu perbaikan operasional'
            ]
        ];

        if (!$benchmark || !isset($interpretations[$ratioName])) {
            return 'Tidak ada interpretasi tersedia';
        }

        $difference = abs($value - $benchmark) / $benchmark;
        
        if ($difference <= 0.1) {
            return $interpretations[$ratioName]['good'];
        } elseif ($difference <= 0.25) {
            return $interpretations[$ratioName]['fair'];
        } else {
            return $interpretations[$ratioName]['poor'];
        }
    }
}