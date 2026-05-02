<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Traits\HasOutletFilter;
use App\Traits\HasCompanySettings;
use App\Services\Finance\ReportTemplateService;
use App\Services\Finance\FinancialRatioService;
use App\Models\Outlet;
use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialRatioController extends Controller
{
    use HasOutletFilter, HasCompanySettings;

    protected $ratioService;
    protected $templateService;

    public function __construct(FinancialRatioService $ratioService, ReportTemplateService $templateService)
    {
        $this->ratioService = $ratioService;
        $this->templateService = $templateService;
    }

    /**
     * Display financial ratio analysis
     */
    public function index(Request $request)
    {
        $outlets = $this->getUserOutlets();
        $selectedOutlet = $request->get('outlet_id', $outlets->first()->id_outlet ?? null);
        
        // Default date range (current year)
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-12-31'));
        
        $reportData = null;
        $trendData = null;
        
        if ($request->has('generate')) {
            $reportData = $this->generateRatioAnalysis($selectedOutlet, $startDate, $endDate);
            
            // Generate trend data if requested
            if ($request->get('include_trend')) {
                $trendData = $this->generateTrendAnalysis($selectedOutlet, $endDate);
            }
        }

        return view('admin.finance.financial-ratio.index', compact(
            'outlets',
            'selectedOutlet',
            'startDate',
            'endDate',
            'reportData',
            'trendData'
        ));
    }

    /**
     * Generate financial ratio analysis
     */
    public function generateRatioAnalysis($outletId, $startDate, $endDate)
    {
        try {
            // Calculate all ratios
            $ratios = $this->ratioService->calculateAllRatios($outletId, $startDate, $endDate);

            // Add interpretations
            foreach ($ratios as $category => &$categoryRatios) {
                foreach ($categoryRatios as $ratioName => &$ratioData) {
                    $ratioData['interpretation'] = $this->ratioService->interpretRatio(
                        $ratioName, 
                        $ratioData['value'], 
                        $ratioData['benchmark']
                    );
                    
                    $ratioData['status'] = $this->evaluateRatioStatus($ratioData['value'], $ratioData['benchmark']);
                }
            }

            // Calculate overall financial health score
            $healthScore = $this->calculateFinancialHealthScore($ratios);

            return [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'formatted' => DateHelper::formatDate($startDate) . ' - ' . DateHelper::formatDate($endDate)
                ],
                'ratios' => $ratios,
                'health_score' => $healthScore,
                'summary' => $this->generateRatioSummary($ratios),
                'recommendations' => $this->generateRecommendations($ratios)
            ];

        } catch (\Exception $e) {
            \Log::error('Error generating financial ratio analysis: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate trend analysis for multiple periods
     */
    public function generateTrendAnalysis($outletId, $endDate)
    {
        $periods = [];
        $currentYear = date('Y', strtotime($endDate));
        
        // Generate 3 years of data
        for ($i = 0; $i < 3; $i++) {
            $year = $currentYear - $i;
            $periods[] = [
                'label' => $year,
                'start_date' => $year . '-01-01',
                'end_date' => $year . '-12-31'
            ];
        }

        return $this->ratioService->getRatioTrend($outletId, $periods);
    }

    /**
     * Evaluate ratio status against benchmark
     */
    private function evaluateRatioStatus($value, $benchmark)
    {
        if (!$benchmark) return 'neutral';
        
        $difference = abs($value - $benchmark) / $benchmark;
        
        if ($difference <= 0.1) return 'good';
        if ($difference <= 0.25) return 'fair';
        return 'poor';
    }

    /**
     * Calculate overall financial health score
     */
    private function calculateFinancialHealthScore($ratios)
    {
        $totalScore = 0;
        $totalRatios = 0;

        foreach ($ratios as $category => $categoryRatios) {
            foreach ($categoryRatios as $ratioName => $ratioData) {
                if (isset($ratioData['benchmark']) && $ratioData['benchmark']) {
                    $score = $this->getRatioScore($ratioData['value'], $ratioData['benchmark']);
                    $totalScore += $score;
                    $totalRatios++;
                }
            }
        }

        $averageScore = $totalRatios > 0 ? $totalScore / $totalRatios : 0;
        
        return [
            'score' => round($averageScore, 1),
            'grade' => $this->getHealthGrade($averageScore),
            'description' => $this->getHealthDescription($averageScore)
        ];
    }

    /**
     * Get individual ratio score (0-100)
     */
    private function getRatioScore($value, $benchmark)
    {
        $difference = abs($value - $benchmark) / $benchmark;
        
        if ($difference <= 0.1) return 100;
        if ($difference <= 0.25) return 75;
        if ($difference <= 0.5) return 50;
        return 25;
    }

    /**
     * Get health grade based on score
     */
    private function getHealthGrade($score)
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    /**
     * Get health description
     */
    private function getHealthDescription($score)
    {
        if ($score >= 90) return 'Sangat Baik - Kondisi keuangan sangat sehat';
        if ($score >= 80) return 'Baik - Kondisi keuangan sehat dengan beberapa area yang bisa ditingkatkan';
        if ($score >= 70) return 'Cukup - Kondisi keuangan stabil namun perlu perhatian';
        if ($score >= 60) return 'Kurang - Kondisi keuangan memerlukan perbaikan';
        return 'Buruk - Kondisi keuangan memerlukan perhatian segera';
    }

    /**
     * Generate ratio summary
     */
    private function generateRatioSummary($ratios)
    {
        $summary = [];

        foreach ($ratios as $category => $categoryRatios) {
            $goodRatios = 0;
            $totalRatios = count($categoryRatios);

            foreach ($categoryRatios as $ratioData) {
                if (isset($ratioData['status']) && $ratioData['status'] === 'good') {
                    $goodRatios++;
                }
            }

            $summary[$category] = [
                'total' => $totalRatios,
                'good' => $goodRatios,
                'percentage' => $totalRatios > 0 ? round(($goodRatios / $totalRatios) * 100, 1) : 0
            ];
        }

        return $summary;
    }

    /**
     * Generate recommendations based on ratios
     */
    private function generateRecommendations($ratios)
    {
        $recommendations = [];

        // Liquidity recommendations
        if (isset($ratios['liquidity']['current_ratio']) && $ratios['liquidity']['current_ratio']['status'] === 'poor') {
            $recommendations[] = [
                'category' => 'Likuiditas',
                'issue' => 'Current Ratio rendah',
                'recommendation' => 'Tingkatkan kas dan aset lancar, atau kurangi kewajiban jangka pendek',
                'priority' => 'high'
            ];
        }

        // Profitability recommendations
        if (isset($ratios['profitability']['net_profit_margin']) && $ratios['profitability']['net_profit_margin']['status'] === 'poor') {
            $recommendations[] = [
                'category' => 'Profitabilitas',
                'issue' => 'Net Profit Margin rendah',
                'recommendation' => 'Tingkatkan efisiensi operasional dan kontrol biaya',
                'priority' => 'high'
            ];
        }

        // Leverage recommendations
        if (isset($ratios['leverage']['debt_to_equity']) && $ratios['leverage']['debt_to_equity']['status'] === 'poor') {
            $recommendations[] = [
                'category' => 'Leverage',
                'issue' => 'Debt-to-Equity Ratio tinggi',
                'recommendation' => 'Kurangi hutang atau tingkatkan modal',
                'priority' => 'medium'
            ];
        }

        // Efficiency recommendations
        if (isset($ratios['efficiency']['inventory_turnover']) && $ratios['efficiency']['inventory_turnover']['status'] === 'poor') {
            $recommendations[] = [
                'category' => 'Efisiensi',
                'issue' => 'Inventory Turnover rendah',
                'recommendation' => 'Optimalkan manajemen persediaan dan tingkatkan penjualan',
                'priority' => 'medium'
            ];
        }

        return $recommendations;
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $includeTrend = $request->get('include_trend', false);

        $reportData = $this->generateRatioAnalysis($outletId, $startDate, $endDate);
        $trendData = $includeTrend ? $this->generateTrendAnalysis($outletId, $endDate) : null;
        
        // Get company data
        $companyData = $this->templateService->getCompanyHeader($outletId);
        $outlet = Outlet::find($outletId);
        
        // Get report metadata
        $reportMetadata = $this->templateService->getReportMetadata(
            'ANALISIS RASIO KEUANGAN',
            $reportData['period']['formatted'],
            $outlet->nama_outlet ?? 'Semua Outlet'
        );

        $pdf = Pdf::loadView('admin.finance.financial-ratio.pdf', compact(
            'reportData',
            'trendData',
            'companyData',
            'reportMetadata'
        ));

        $filename = 'Analisis_Rasio_Keuangan_' . str_replace(['/', ' '], ['_', '_'], $reportData['period']['formatted']) . '.pdf';
        
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
        $includeTrend = $request->get('include_trend', false);

        $reportData = $this->generateRatioAnalysis($outletId, $startDate, $endDate);
        $trendData = $includeTrend ? $this->generateTrendAnalysis($outletId, $endDate) : null;
        
        // Get company data
        $companyData = $this->templateService->getCompanyHeader($outletId);
        $outlet = Outlet::find($outletId);
        
        // Get report metadata
        $reportMetadata = $this->templateService->getReportMetadata(
            'ANALISIS RASIO KEUANGAN',
            $reportData['period']['formatted'],
            $outlet->nama_outlet ?? 'Semua Outlet'
        );

        $filename = 'Analisis_Rasio_Keuangan_' . str_replace(['/', ' '], ['_', '_'], $reportData['period']['formatted']) . '.xlsx';

        return Excel::download(new FinancialRatioExport($reportData, $trendData, $companyData, $reportMetadata), $filename);
    }

    /**
     * Get ratio data for AJAX
     */
    public function getRatioData(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        try {
            $reportData = $this->generateRatioAnalysis($outletId, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data rasio: ' . $e->getMessage()
            ], 500);
        }
    }
}

/**
 * Excel Export Class for Financial Ratios
 */
class FinancialRatioExport implements FromArray, WithTitle, WithStyles
{
    protected $reportData;
    protected $trendData;
    protected $companyData;
    protected $reportMetadata;
    protected $templateService;

    public function __construct($reportData, $trendData, $companyData, $reportMetadata)
    {
        $this->reportData = $reportData;
        $this->trendData = $trendData;
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

        // Add financial health score
        $data[] = ['SKOR KESEHATAN KEUANGAN'];
        $data[] = ['Skor: ' . $this->reportData['health_score']['score'] . '/100'];
        $data[] = ['Grade: ' . $this->reportData['health_score']['grade']];
        $data[] = ['Deskripsi: ' . $this->reportData['health_score']['description']];
        $data[] = [''];

        // Add ratio table
        $ratioTable = $this->templateService->generateRatioTable($this->reportData['ratios']);
        $data = array_merge($data, $ratioTable);

        // Add recommendations
        if (!empty($this->reportData['recommendations'])) {
            $data[] = [''];
            $data[] = ['REKOMENDASI'];
            $data[] = ['Kategori', 'Masalah', 'Rekomendasi', 'Prioritas'];
            
            foreach ($this->reportData['recommendations'] as $rec) {
                $data[] = [
                    $rec['category'],
                    $rec['issue'],
                    $rec['recommendation'],
                    strtoupper($rec['priority'])
                ];
            }
        }

        // Add trend data if available
        if ($this->trendData) {
            $data[] = [''];
            $data[] = ['ANALISIS TREND (3 TAHUN)'];
            
            // Get first category ratios for trend headers
            $firstCategory = array_keys($this->trendData)[0];
            $ratioNames = array_keys($this->trendData[$firstCategory]);
            
            $trendHeader = ['Rasio'];
            foreach (array_keys($this->trendData) as $period) {
                $trendHeader[] = $period;
            }
            $data[] = $trendHeader;
            
            // Add trend data for each ratio
            foreach ($ratioNames as $ratioName) {
                $row = [ucfirst(str_replace('_', ' ', $ratioName))];
                foreach ($this->trendData as $periodData) {
                    $value = 0;
                    foreach ($periodData as $category => $ratios) {
                        if (isset($ratios[$ratioName])) {
                            $value = $ratios[$ratioName]['value'];
                            break;
                        }
                    }
                    $row[] = number_format($value, 2);
                }
                $data[] = $row;
            }
        }

        return $data;
    }

    public function title(): string
    {
        return 'Analisis Rasio Keuangan';
    }

    public function styles(Worksheet $sheet)
    {
        return $this->templateService->applyExcelStyling($sheet);
    }
}