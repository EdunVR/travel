<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Traits\HasOutletFilter;
use App\Traits\HasCompanySettings;
use App\Services\Finance\ReportTemplateService;
use App\Services\ChartOfAccountService;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use App\Models\Outlet;
use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquityChangeController extends Controller
{
    use HasOutletFilter, HasCompanySettings;

    protected $coaService;
    protected $templateService;

    public function __construct(ChartOfAccountService $coaService, ReportTemplateService $templateService)
    {
        $this->coaService = $coaService;
        $this->templateService = $templateService;
    }

    /**
     * Display equity change report
     */
    public function index(Request $request)
    {
        $outlets = $this->getUserOutlets();
        $selectedOutlet = $request->get('outlet_id', $outlets->first()->id_outlet ?? null);
        
        // Default date range (current year)
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-12-31'));
        
        $reportData = null;
        if ($request->has('generate')) {
            $reportData = $this->generateEquityChangeReport($selectedOutlet, $startDate, $endDate);
        }

        return view('admin.finance.equity-change.index', compact(
            'outlets',
            'selectedOutlet',
            'startDate',
            'endDate',
            'reportData'
        ));
    }

    /**
     * Generate equity change report data
     */
    public function generateEquityChangeReport($outletId, $startDate, $endDate)
    {
        try {
            // Get equity accounts
            $equityAccounts = ChartOfAccount::where('outlet_id', $outletId)
                ->where('type', 'equity')
                ->where('status', 'active')
                ->get();

            // Calculate opening balance (as of start date - 1 day)
            $openingDate = date('Y-m-d', strtotime($startDate . ' -1 day'));
            $openingBalance = $this->calculateEquityBalance($equityAccounts, null, $openingDate);

            // Calculate closing balance (as of end date)
            $closingBalance = $this->calculateEquityBalance($equityAccounts, null, $endDate);

            // Get equity movements during the period
            $equityMovements = $this->getEquityMovements($equityAccounts, $startDate, $endDate);

            // Calculate net income for the period
            $netIncome = $this->calculateNetIncome($outletId, $startDate, $endDate);

            // Calculate dividends/withdrawals
            $dividends = $this->calculateDividends($equityAccounts, $startDate, $endDate);

            // Calculate additional capital
            $additionalCapital = $this->calculateAdditionalCapital($equityAccounts, $startDate, $endDate);

            // Prepare report data
            $reportData = [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'formatted' => DateHelper::formatDate($startDate) . ' - ' . DateHelper::formatDate($endDate)
                ],
                'opening_balance' => $openingBalance,
                'net_income' => $netIncome,
                'additional_capital' => $additionalCapital,
                'dividends' => $dividends,
                'other_changes' => $equityMovements['other_changes'],
                'closing_balance' => $closingBalance,
                'total_changes' => $closingBalance - $openingBalance,
                'equity_accounts' => $equityAccounts,
                'movements_detail' => $equityMovements['details']
            ];

            // Validation: Opening + Changes should equal Closing
            $calculatedClosing = $openingBalance + $netIncome + $additionalCapital - $dividends + $equityMovements['other_changes'];
            $reportData['is_balanced'] = abs($calculatedClosing - $closingBalance) < 0.01;
            $reportData['variance'] = $closingBalance - $calculatedClosing;

            return $reportData;

        } catch (\Exception $e) {
            \Log::error('Error generating equity change report: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate total equity balance
     */
    private function calculateEquityBalance($equityAccounts, $startDate = null, $endDate = null)
    {
        $totalBalance = 0;

        foreach ($equityAccounts as $account) {
            $balance = $this->coaService->calculateAccountBalance($account->id, $startDate, $endDate);
            $totalBalance += $balance;
        }

        return $totalBalance;
    }

    /**
     * Get equity movements during period
     */
    private function getEquityMovements($equityAccounts, $startDate, $endDate)
    {
        $accountIds = $equityAccounts->pluck('id')->toArray();
        
        $movements = JournalEntryDetail::whereIn('account_id', $accountIds)
            ->whereHas('journalEntry', function($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate])
                      ->where('status', 'posted');
            })
            ->with(['journalEntry', 'account'])
            ->get();

        $details = [];
        $otherChanges = 0;

        foreach ($movements as $movement) {
            $amount = $movement->credit - $movement->debit;
            
            // Exclude net income and dividends (they're calculated separately)
            if (!$this->isNetIncomeEntry($movement) && !$this->isDividendEntry($movement)) {
                $otherChanges += $amount;
                
                $details[] = [
                    'date' => $movement->journalEntry->transaction_date,
                    'account' => $movement->account->name,
                    'description' => $movement->journalEntry->description,
                    'amount' => $amount,
                    'type' => $amount > 0 ? 'increase' : 'decrease'
                ];
            }
        }

        return [
            'other_changes' => $otherChanges,
            'details' => $details
        ];
    }

    /**
     * Calculate net income for the period
     */
    private function calculateNetIncome($outletId, $startDate, $endDate)
    {
        // Get revenue accounts
        $revenueAccounts = ChartOfAccount::where('outlet_id', $outletId)
            ->whereIn('type', ['revenue', 'otherrevenue'])
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        // Get expense accounts
        $expenseAccounts = ChartOfAccount::where('outlet_id', $outletId)
            ->whereIn('type', ['expense', 'otherexpense'])
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        // Calculate total revenue
        $totalRevenue = JournalEntryDetail::whereIn('account_id', $revenueAccounts)
            ->whereHas('journalEntry', function($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate])
                      ->where('status', 'posted');
            })
            ->sum('credit');

        // Calculate total expenses
        $totalExpenses = JournalEntryDetail::whereIn('account_id', $expenseAccounts)
            ->whereHas('journalEntry', function($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate])
                      ->where('status', 'posted');
            })
            ->sum('debit');

        return $totalRevenue - $totalExpenses;
    }

    /**
     * Calculate dividends/withdrawals
     */
    private function calculateDividends($equityAccounts, $startDate, $endDate)
    {
        $accountIds = $equityAccounts->pluck('id')->toArray();
        
        $dividends = JournalEntryDetail::whereIn('account_id', $accountIds)
            ->whereHas('journalEntry', function($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate])
                      ->where('status', 'posted')
                      ->where(function($q) {
                          $q->where('description', 'like', '%dividen%')
                            ->orWhere('description', 'like', '%dividend%')
                            ->orWhere('description', 'like', '%prive%')
                            ->orWhere('description', 'like', '%withdrawal%');
                      });
            })
            ->sum('debit');

        return $dividends;
    }

    /**
     * Calculate additional capital
     */
    private function calculateAdditionalCapital($equityAccounts, $startDate, $endDate)
    {
        $accountIds = $equityAccounts->pluck('id')->toArray();
        
        $additionalCapital = JournalEntryDetail::whereIn('account_id', $accountIds)
            ->whereHas('journalEntry', function($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate])
                      ->where('status', 'posted')
                      ->where(function($q) {
                          $q->where('description', 'like', '%setoran%')
                            ->orWhere('description', 'like', '%modal%')
                            ->orWhere('description', 'like', '%capital%')
                            ->orWhere('description', 'like', '%investment%');
                      });
            })
            ->sum('credit');

        return $additionalCapital;
    }

    /**
     * Check if journal entry is net income posting
     */
    private function isNetIncomeEntry($movement)
    {
        $description = strtolower($movement->journalEntry->description);
        return strpos($description, 'laba') !== false || 
               strpos($description, 'rugi') !== false ||
               strpos($description, 'net income') !== false ||
               strpos($description, 'profit') !== false;
    }

    /**
     * Check if journal entry is dividend
     */
    private function isDividendEntry($movement)
    {
        $description = strtolower($movement->journalEntry->description);
        return strpos($description, 'dividen') !== false || 
               strpos($description, 'dividend') !== false ||
               strpos($description, 'prive') !== false ||
               strpos($description, 'withdrawal') !== false;
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $reportData = $this->generateEquityChangeReport($outletId, $startDate, $endDate);
        
        // Get company data
        $companyData = $this->templateService->getCompanyHeader($outletId);
        $outlet = Outlet::find($outletId);
        
        // Get report metadata
        $reportMetadata = $this->templateService->getReportMetadata(
            'LAPORAN PERUBAHAN MODAL',
            $reportData['period']['formatted'],
            $outlet->nama_outlet ?? 'Semua Outlet'
        );

        $pdf = Pdf::loadView('admin.finance.equity-change.pdf', compact(
            'reportData',
            'companyData',
            'reportMetadata'
        ));

        $filename = 'Laporan_Perubahan_Modal_' . str_replace(['/', ' '], ['_', '_'], $reportData['period']['formatted']) . '.pdf';
        
        return $pdf->stream($filename);
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $reportData = $this->generateEquityChangeReport($outletId, $startDate, $endDate);
        
        // Get company data
        $companyData = $this->templateService->getCompanyHeader($outletId);
        $outlet = Outlet::find($outletId);
        
        // Get report metadata
        $reportMetadata = $this->templateService->getReportMetadata(
            'LAPORAN PERUBAHAN MODAL',
            $reportData['period']['formatted'],
            $outlet->nama_outlet ?? 'Semua Outlet'
        );

        $filename = 'Laporan_Perubahan_Modal_' . str_replace(['/', ' '], ['_', '_'], $reportData['period']['formatted']) . '.xlsx';

        return Excel::download(new EquityChangeExport($reportData, $companyData, $reportMetadata), $filename);
    }
}

/**
 * Excel Export Class
 */
class EquityChangeExport implements FromArray, WithTitle, WithStyles
{
    protected $reportData;
    protected $companyData;
    protected $reportMetadata;
    protected $templateService;

    public function __construct($reportData, $companyData, $reportMetadata)
    {
        $this->reportData = $reportData;
        $this->companyData = $companyData;
        $this->reportMetadata = $reportMetadata;
        $this->templateService = new ReportTemplateService();
    }

    public function array(): array
    {
        $data = [];
        
        // Add header
        $header = $this->templateService->generateExcelHeader($this->companyData, $this->reportMetadata);
        $data = array_merge($data, $header);

        // Add table header
        $data[] = ['Keterangan', 'Jumlah (Rp)'];
        $data[] = ['', '']; // Empty row

        // Add equity change data
        $data[] = ['Modal Awal (per ' . DateHelper::formatDate($this->reportData['period']['start_date']) . ')', 
                   $this->templateService->formatCurrency($this->reportData['opening_balance'])];
        
        $data[] = ['', ''];
        $data[] = ['Perubahan Modal:', ''];
        
        $data[] = ['  Laba Bersih Periode', 
                   $this->templateService->formatCurrency($this->reportData['net_income'])];
        
        $data[] = ['  Setoran Modal Tambahan', 
                   $this->templateService->formatCurrency($this->reportData['additional_capital'])];
        
        $data[] = ['  Dividen/Prive', 
                   '(' . $this->templateService->formatCurrency($this->reportData['dividends']) . ')'];
        
        $data[] = ['  Perubahan Lainnya', 
                   $this->templateService->formatCurrency($this->reportData['other_changes'])];
        
        $data[] = ['', ''];
        $data[] = ['Total Perubahan Modal', 
                   $this->templateService->formatCurrency($this->reportData['total_changes'])];
        
        $data[] = ['', ''];
        $data[] = ['Modal Akhir (per ' . DateHelper::formatDate($this->reportData['period']['end_date']) . ')', 
                   $this->templateService->formatCurrency($this->reportData['closing_balance'])];

        // Add movements detail if any
        if (!empty($this->reportData['movements_detail'])) {
            $data[] = ['', ''];
            $data[] = ['', ''];
            $data[] = ['Detail Perubahan Lainnya:', ''];
            $data[] = ['Tanggal', 'Akun', 'Keterangan', 'Jumlah'];
            
            foreach ($this->reportData['movements_detail'] as $movement) {
                $data[] = [
                    DateHelper::formatDate($movement['date']),
                    $movement['account'],
                    $movement['description'],
                    $this->templateService->formatCurrency($movement['amount'])
                ];
            }
        }

        return $data;
    }

    public function title(): string
    {
        return 'Laporan Perubahan Modal';
    }

    public function styles(Worksheet $sheet)
    {
        return $this->templateService->applyExcelStyling($sheet);
    }
}