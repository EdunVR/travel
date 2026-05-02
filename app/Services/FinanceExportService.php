<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Finance Export Service
 * 
 * Handles export functionality for finance modules including Journal List,
 * Accounting Book, Fixed Assets, and General Ledger. Supports both XLSX
 * and PDF export formats with filtering capabilities.
 * 
 * @package App\Services
 * @author ERP System
 * @version 1.0.0
 */
class FinanceExportService
{
    /**
     * Export data to XLSX format
     *
     * @param string $module The module name (journal, accounting-book, fixed-assets, general-ledger)
     * @param array $data The data to export
     * @param array $filters Applied filters
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToXLSX(string $module, array $data, array $filters = [])
    {
        $exportClass = $this->getExportClass($module);
        $filename = $this->generateFilename($module, 'xlsx');
        
        return Excel::download(new $exportClass($data, $filters), $filename);
    }

    /**
     * Export data to PDF format (stream for preview)
     *
     * @param string $module The module name
     * @param array $data The data to export
     * @param array $filters Applied filters
     * @return \Illuminate\Http\Response
     */
    public function exportToPDF(string $module, array $data, array $filters = [])
    {
        $view = $this->getPDFView($module);
        $filename = $this->generateFilename($module, 'pdf');
        
        $pdf = Pdf::loadView($view, compact('data', 'filters'))
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', '10mm')
            ->setOption('margin-right', '10mm')
            ->setOption('margin-bottom', '10mm')
            ->setOption('margin-left', '10mm');
        
        // Stream PDF untuk preview (bukan langsung download)
        return $pdf->stream($filename);
    }

    /**
     * Get the export class for a given module
     *
     * @param string $module
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getExportClass(string $module): string
    {
        return match($module) {
            'journal' => \App\Exports\JournalExport::class,
            'accounting-book' => \App\Exports\AccountingBookExport::class,
            'fixed-assets' => \App\Exports\FixedAssetsExport::class,
            'general-ledger' => \App\Exports\GeneralLedgerExport::class,
            'neraca' => \App\Exports\NeracaExport::class,
            'neraca-saldo' => \App\Exports\TrialBalanceExport::class,
            'expenses' => \App\Exports\ExpensesExport::class,
            default => throw new \InvalidArgumentException("Unknown module: {$module}")
        };
    }

    /**
     * Get the PDF view for a given module
     *
     * @param string $module
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getPDFView(string $module): string
    {
        return match($module) {
            'journal' => 'admin.finance.jurnal.pdf',
            'accounting-book' => 'admin.finance.buku.pdf',
            'fixed-assets' => 'admin.finance.aktiva-tetap.pdf',
            'general-ledger' => 'admin.finance.buku-besar.pdf',
            'neraca' => 'admin.finance.neraca.pdf',
            'neraca-saldo' => 'admin.finance.neraca-saldo.pdf',
            'expenses' => 'admin.finance.biaya.pdf',
            default => throw new \InvalidArgumentException("Unknown module: {$module}")
        };
    }

    /**
     * Generate filename for export
     *
     * @param string $module
     * @param string $extension
     * @return string
     */
    private function generateFilename(string $module, string $extension): string
    {
        $timestamp = now()->format('Y-m-d_His');
        return "{$module}_export_{$timestamp}.{$extension}";
    }
}
