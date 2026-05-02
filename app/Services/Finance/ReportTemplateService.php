<?php

namespace App\Services\Finance;

use App\Models\CompanySetting;
use App\Helpers\DateHelper;
use Illuminate\Support\Facades\Storage;

class ReportTemplateService
{
    /**
     * Get company header data for reports
     */
    public function getCompanyHeader($outletId = null)
    {
        $companySetting = CompanySetting::where('outlet_id', $outletId)->first();
        
        if (!$companySetting) {
            // Fallback to default company setting
            $companySetting = CompanySetting::where('is_active', true)->first();
        }

        return [
            'company_name' => $companySetting->company_name ?? 'MORRA ERP',
            'company_address' => $companySetting->company_address ?? '',
            'company_phone' => $companySetting->company_phone ?? '',
            'company_email' => $companySetting->company_email ?? '',
            'company_website' => $companySetting->company_website ?? '',
            'npwp' => $companySetting->npwp ?? '',
            'nib' => $companySetting->nib ?? '',
            'siup' => $companySetting->siup ?? '',
            'logo_url' => $companySetting->logo_url ?? null,
            'currency' => $companySetting->currency ?? 'IDR',
            'tax_rate' => $companySetting->tax_rate ?? 11.00,
        ];
    }

    /**
     * Get report metadata
     */
    public function getReportMetadata($reportTitle, $period = null, $outletName = null, $additionalInfo = [])
    {
        return [
            'report_title' => $reportTitle,
            'period' => $period,
            'outlet_name' => $outletName,
            'generated_at' => DateHelper::now(),
            'generated_by' => auth()->user()->name ?? 'System',
            'generated_date' => DateHelper::today(),
            'additional_info' => $additionalInfo
        ];
    }

    /**
     * Format currency for display
     */
    public function formatCurrency($amount, $currency = 'IDR')
    {
        if ($currency === 'IDR') {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }
        
        return number_format($amount, 2, '.', ',');
    }

    /**
     * Format percentage for display
     */
    public function formatPercentage($value, $decimals = 2)
    {
        return number_format($value, $decimals, ',', '.') . '%';
    }

    /**
     * Get PDF page settings
     */
    public function getPdfSettings($orientation = 'portrait', $size = 'a4')
    {
        return [
            'orientation' => $orientation,
            'size' => $size,
            'margin' => [
                'top' => 20,
                'right' => 15,
                'bottom' => 20,
                'left' => 15
            ],
            'font' => [
                'family' => 'Arial',
                'size' => 10
            ]
        ];
    }

    /**
     * Generate PDF header HTML
     */
    public function generatePdfHeader($companyData, $reportMetadata)
    {
        $logoHtml = '';
        if ($companyData['logo_url']) {
            $logoHtml = '<img src="' . $companyData['logo_url'] . '" style="height: 60px; float: left; margin-right: 15px;">';
        }

        return '
        <div style="border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px;">
            <table width="100%" style="border: none;">
                <tr>
                    <td style="width: 80px; vertical-align: top;">
                        ' . $logoHtml . '
                    </td>
                    <td style="vertical-align: top;">
                        <h2 style="margin: 0; font-size: 18px; font-weight: bold; color: #333;">
                            ' . $companyData['company_name'] . '
                        </h2>
                        <p style="margin: 5px 0; font-size: 10px; line-height: 1.4;">
                            ' . $companyData['company_address'] . '<br>
                            Telp: ' . $companyData['company_phone'] . ' | Email: ' . $companyData['company_email'] . '<br>
                            NPWP: ' . $companyData['npwp'] . ' | Website: ' . $companyData['company_website'] . '
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase;">
                ' . $reportMetadata['report_title'] . '
            </h3>
            <p style="margin: 5px 0; font-size: 12px;">
                Periode: ' . $reportMetadata['period'] . '
            </p>
            ' . ($reportMetadata['outlet_name'] ? '<p style="margin: 5px 0; font-size: 12px;">Outlet: ' . $reportMetadata['outlet_name'] . '</p>' : '') . '
        </div>';
    }

    /**
     * Generate PDF footer HTML
     */
    public function generatePdfFooter($reportMetadata)
    {
        return '
        <div style="border-top: 1px solid #ccc; padding-top: 10px; margin-top: 20px; font-size: 9px; color: #666;">
            <table width="100%" style="border: none;">
                <tr>
                    <td style="width: 50%;">
                        Dicetak: ' . $reportMetadata['generated_at'] . '<br>
                        User: ' . $reportMetadata['generated_by'] . '
                    </td>
                    <td style="width: 50%; text-align: right;">
                        Halaman: <span class="pagenum"></span> dari <span class="pagecount"></span><br>
                        Sistem: MORRA ERP
                    </td>
                </tr>
            </table>
        </div>';
    }

    /**
     * Generate Excel header data
     */
    public function generateExcelHeader($companyData, $reportMetadata)
    {
        return [
            // Row 1: Company Name
            [$companyData['company_name']],
            // Row 2: Company Address & Contact
            [$companyData['company_address'] . ' | Telp: ' . $companyData['company_phone'] . ' | Email: ' . $companyData['company_email']],
            // Row 3: NPWP & Website
            ['NPWP: ' . $companyData['npwp'] . ' | Website: ' . $companyData['company_website']],
            // Row 4: Empty
            [''],
            // Row 5: Report Title
            [$reportMetadata['report_title']],
            // Row 6: Period
            ['Periode: ' . $reportMetadata['period']],
            // Row 7: Outlet (if applicable)
            $reportMetadata['outlet_name'] ? ['Outlet: ' . $reportMetadata['outlet_name']] : [''],
            // Row 8: Generated info
            ['Dicetak: ' . $reportMetadata['generated_at'] . ' | User: ' . $reportMetadata['generated_by']],
            // Row 9: Empty
            [''],
        ];
    }

    /**
     * Apply Excel styling
     */
    public function applyExcelStyling($sheet, $headerRowCount = 9)
    {
        // Company name styling
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => 'center']
        ]);

        // Report title styling
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center']
        ]);

        // Header rows styling
        for ($i = 1; $i <= $headerRowCount; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }

        // Auto-width for all columns
        foreach (range('A', 'Z') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $sheet;
    }

    /**
     * Format number for Excel
     */
    public function formatExcelNumber($value, $type = 'currency')
    {
        switch ($type) {
            case 'currency':
                return [
                    'value' => $value,
                    'format' => '#,##0'
                ];
            case 'percentage':
                return [
                    'value' => $value / 100,
                    'format' => '0.00%'
                ];
            case 'decimal':
                return [
                    'value' => $value,
                    'format' => '#,##0.00'
                ];
            default:
                return $value;
        }
    }

    /**
     * Generate comparison table for multi-period reports
     */
    public function generateComparisonTable($data, $periods, $accountName = 'Account')
    {
        $table = [];
        
        // Header row
        $header = [$accountName];
        foreach ($periods as $period) {
            $header[] = $period;
        }
        $header[] = 'Variance';
        $header[] = 'Variance %';
        $table[] = $header;

        // Data rows
        foreach ($data as $item) {
            $row = [$item['name']];
            $values = [];
            
            foreach ($periods as $period) {
                $value = $item['periods'][$period] ?? 0;
                $row[] = $this->formatCurrency($value);
                $values[] = $value;
            }
            
            // Calculate variance (latest - previous)
            if (count($values) >= 2) {
                $variance = $values[0] - $values[1];
                $variancePercent = $values[1] != 0 ? ($variance / abs($values[1])) * 100 : 0;
                
                $row[] = $this->formatCurrency($variance);
                $row[] = $this->formatPercentage($variancePercent);
            } else {
                $row[] = '-';
                $row[] = '-';
            }
            
            $table[] = $row;
        }

        return $table;
    }

    /**
     * Generate ratio analysis table
     */
    public function generateRatioTable($ratios)
    {
        $table = [
            ['Ratio Category', 'Ratio Name', 'Value', 'Benchmark', 'Status']
        ];

        foreach ($ratios as $category => $categoryRatios) {
            foreach ($categoryRatios as $ratioName => $ratioData) {
                $status = $this->evaluateRatio($ratioData['value'], $ratioData['benchmark'] ?? null);
                
                $table[] = [
                    ucfirst(str_replace('_', ' ', $category)),
                    ucfirst(str_replace('_', ' ', $ratioName)),
                    $this->formatRatioValue($ratioData['value'], $ratioData['type'] ?? 'ratio'),
                    $ratioData['benchmark'] ?? '-',
                    $status
                ];
            }
        }

        return $table;
    }

    /**
     * Format ratio value based on type
     */
    private function formatRatioValue($value, $type)
    {
        switch ($type) {
            case 'percentage':
                return $this->formatPercentage($value * 100);
            case 'currency':
                return $this->formatCurrency($value);
            case 'times':
                return number_format($value, 2) . 'x';
            default:
                return number_format($value, 2);
        }
    }

    /**
     * Evaluate ratio against benchmark
     */
    private function evaluateRatio($value, $benchmark)
    {
        if (!$benchmark) return 'N/A';
        
        $difference = abs($value - $benchmark) / $benchmark;
        
        if ($difference <= 0.1) return 'Good';
        if ($difference <= 0.25) return 'Fair';
        return 'Poor';
    }
}