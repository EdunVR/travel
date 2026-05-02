<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountingBook;
use App\Models\AccountOpeningBalance;
use App\Models\JournalEntry;
use App\Services\ChartOfAccountService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossController extends Controller
{
    protected $coaService;

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }

    public function index(Request $request)
    {
        // Set default book if not exists
        if (!$request->has('book_id')) {
            $defaultBook = AccountingBook::active()->first();
            if ($defaultBook) {
                return redirect()->route('financial.profit-loss.index', [
                    'book_id' => $defaultBook->id
                ]);
            }
            
            return redirect()->route('financial.journal.index')
                ->with('error', 'Tidak ada tahun buku aktif. Silakan buat tahun buku terlebih dahulu.');
        }

        // Validate input
        $request->validate([
            'book_id' => 'required|exists:accounting_books,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        try {
            $bookId = $request->book_id;
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : null;
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : null;

            // Get accounting book data
            $accountingBook = AccountingBook::findOrFail($bookId);
            $books = AccountingBook::orderBy('start_date', 'desc')->get();

            // Calculate profit loss data
            $profitLossData = $this->calculateProfitLossData($accountingBook, $dateFrom, $dateTo);

            return view('financial.profit-loss.index', [
                'accountingBook' => $accountingBook,
                'books' => $books,
                'profitLossData' => $profitLossData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'selectedBookId' => $bookId,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('financial.journal.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function calculateProfitLossData($accountingBook, $dateFrom, $dateTo)
    {
        // Dapatkan semua akun terlebih dahulu
        $allAccounts = $this->coaService->getAllAccounts();
        
        // Filter akun berdasarkan jenis
        $revenueAccounts = array_filter($allAccounts, fn($a) => $a['type'] === 'revenue');
        $expenseAccounts = array_filter($allAccounts, fn($a) => $a['type'] === 'expense');
        
        // Filter akun HPP (asumsi kode dimulai dengan '5')
        $cogsAccounts = array_filter($allAccounts, function($a) {
            return $a['type'] === 'expense' && strpos($a['code'], '5') === 0;
        });

        $data = [
            'revenues' => [],
            'cogs' => [],
            'operating_expenses' => [],
            'other_income' => [],
            'other_expenses' => [],
            'gross_profit' => 0,
            'operating_profit' => 0,
            'profit_before_tax' => 0,
            'tax_expense' => 0,
            'net_profit' => 0,
            'totals' => [
                'revenue' => 0,
                'cogs' => 0,
                'operating_expenses' => 0,
                'other_income' => 0,
                'other_expenses' => 0
            ]
        ];

        // 1. Hitung Pendapatan Utama (bisa positif atau negatif)
        foreach ($revenueAccounts as $account) {
            $balance = $this->calculateAccountBalance($account['code'], $accountingBook, $dateFrom, $dateTo);
            
            // Skip jika balance 0
            if (abs($balance) < 0.01) continue;
            
            // Pisahkan pendapatan utama dan pendapatan lain-lain
            if (strpos($account['code'], '4.01') === 0) { // Asumsi pendapatan utama kode 4.01.xx
                $data['revenues'][] = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'amount' => $balance
                ];
                $data['totals']['revenue'] += $balance;
            } else {
                $data['other_income'][] = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'amount' => $balance
                ];
                $data['totals']['other_income'] += $balance;
            }
        }

        // 2. Hitung HPP (selalu positif)
        foreach ($cogsAccounts as $account) {
            $balance = $this->calculateAccountBalance($account['code'], $accountingBook, $dateFrom, $dateTo);
            if (abs($balance) < 0.01) continue;
            
            $data['cogs'][] = [
                'code' => $account['code'],
                'name' => $account['name'],
                'amount' => abs($balance) // HPP selalu positif
            ];
            $data['totals']['cogs'] += abs($balance);
        }

        // 3. Hitung Beban Operasional (selalu positif)
        foreach ($expenseAccounts as $account) {
            // Skip akun yang sudah termasuk dalam HPP
            if (strpos($account['code'], '5') === 0) continue;
            
            $balance = $this->calculateAccountBalance($account['code'], $accountingBook, $dateFrom, $dateTo);
            if (abs($balance) < 0.01) continue;
            
            // Pisahkan beban operasional dan beban lain-lain
            if (strpos($account['code'], '6.01') === 0) { // Asumsi beban operasional kode 6.01.xx
                $data['operating_expenses'][] = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'amount' => abs($balance) // Beban selalu positif
                ];
                $data['totals']['operating_expenses'] += abs($balance);
            } else {
                $data['other_expenses'][] = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'amount' => abs($balance) // Beban selalu positif
                ];
                $data['totals']['other_expenses'] += abs($balance);
            }
        }

        // 4. Hitung Laba/Rugi sesuai standar akuntansi
        $data['gross_profit'] = $data['totals']['revenue'] - $data['totals']['cogs'];
        $data['operating_profit'] = $data['gross_profit'] - $data['totals']['operating_expenses'];
        $data['profit_before_tax'] = $data['operating_profit'] 
                                + $data['totals']['other_income'] 
                                - $data['totals']['other_expenses'];
        
        // Hitung pajak (opsional, 10%)
        $data['tax_expense'] = max(0, $data['profit_before_tax']) * 0.1;
        $data['net_profit'] = $data['profit_before_tax'] - $data['tax_expense'];

        return $data;
    }

    protected function calculateAccountBalance($accountCode, $accountingBook, $dateFrom, $dateTo)
    {
        // Get opening balance
        $openingBalance = AccountOpeningBalance::where('accounting_book_id', $accountingBook->id)
            ->where('account_code', $accountCode)
            ->first();

        $initialDebit = $openingBalance ? $openingBalance->debit : 0;
        $initialCredit = $openingBalance ? $openingBalance->credit : 0;

        // Get journal entries
        $query = JournalEntry::where('account_code', $accountCode)
            ->whereHas('journal', function($q) use ($accountingBook) {
                $q->where('accounting_book_id', $accountingBook->id)
                ->where('is_validated', true);
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

        $entries = $query->select(
            DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
            DB::raw('COALESCE(SUM(credit), 0) as total_credit')
        )->first();

        $totalDebit = $initialDebit + ($entries->total_debit ?? 0);
        $totalCredit = $initialCredit + ($entries->total_credit ?? 0);

        // Untuk laporan laba rugi, kita perlu menyesuaikan berdasarkan jenis akun
        $account = $this->coaService->getAccountByCode($accountCode);
        
        if ($account['type'] === 'revenue') {
            // Pendapatan: kredit - debit (kredit adalah peningkatan pendapatan)
            return $totalCredit - $totalDebit;
        } else {
            // Beban/HPP: debit - kredit (debit adalah peningkatan beban)
            return $totalDebit - $totalCredit;
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:accounting_books,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'export_type' => 'required|in:pdf,excel'
        ]);

        try {
            $bookId = $request->book_id;
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : null;
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : null;
            $exportType = $request->export_type;

            // Get accounting book data
            $accountingBook = AccountingBook::findOrFail($bookId);

            // Calculate profit loss data
            $profitLossData = $this->calculateProfitLossData($accountingBook, $dateFrom, $dateTo);

            // Prepare common data
            $exportData = [
                'accountingBook' => $accountingBook,
                'profitLossData' => $profitLossData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'exportDate' => now()->format('d/m/Y H:i:s'),
                'preparedBy' => auth()->user()->name,
            ];

            // Generate filename
            $filename = 'Laporan_Laba_Rugi_' . $accountingBook->name;
            if ($dateFrom && $dateTo) {
                $filename .= '_' . $dateFrom->format('Ymd') . '-' . $dateTo->format('Ymd');
            }
            $filename .= '_' . now()->format('YmdHis');

            if ($exportType === 'pdf') {
                return $this->exportToPdf($exportData, $filename);
            }

            return $this->exportToExcel($exportData, $filename);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengekspor: ' . $e->getMessage());
        }
    }

    protected function exportToPdf($data, $filename)
    {
        $pdf = \PDF::loadView('financial.profit-loss.export_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'.pdf"');
    }

    protected function exportToExcel($data, $filename)
{
    return Excel::download(
        new class($data) implements FromArray, WithTitle, WithStyles, WithColumnFormatting {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                $rows = [];

                // Header
                $rows[] = ['LAPORAN LABA RUGI'];
                $rows[] = [config('app.name')];
                $rows[] = ['Tanggal Export: ' . $this->data['exportDate']];
                $rows[] = []; // Empty row

                // Company Info Table
                $rows[] = ['Tahun Buku', ': ' . $this->data['accountingBook']->name, 'Dicetak oleh', ': ' . $this->data['preparedBy']];
                $rows[] = ['Periode', ': ' . ($this->data['dateFrom'] ? $this->data['dateFrom']->format('d/m/Y') . ' - ' . $this->data['dateTo']->format('d/m/Y') : 'Semua Periode'), 
                          'Tanggal Cetak', ': ' . $this->data['exportDate']];
                $rows[] = []; // Empty row

                // Revenue Section
                $rows[] = ['PENDAPATAN USAHA'];
                $rows[] = ['Kode Akun', 'Nama Akun', 'Jumlah'];
                
                foreach ($this->data['profitLossData']['revenues'] as $revenue) {
                    $rows[] = [
                        $revenue['code'],
                        $revenue['name'],
                        $revenue['amount'] >= 0 ? $revenue['amount'] : '('.abs($revenue['amount']).')'
                    ];
                }
                
                $rows[] = [
                    '',
                    'TOTAL PENDAPATAN USAHA',
                    $this->data['profitLossData']['totals']['revenue'] >= 0 
                        ? $this->data['profitLossData']['totals']['revenue'] 
                        : '('.abs($this->data['profitLossData']['totals']['revenue']).')'
                ];
                $rows[] = []; // Empty row

                // COGS Section
                $rows[] = ['HARGA POKOK PENJUALAN'];
                $rows[] = ['Kode Akun', 'Nama Akun', 'Jumlah'];
                
                foreach ($this->data['profitLossData']['cogs'] as $cogs) {
                    $rows[] = [
                        $cogs['code'],
                        $cogs['name'],
                        '('.abs($cogs['amount']).')'
                    ];
                }
                
                $rows[] = [
                    '',
                    'TOTAL HPP',
                    '('.abs($this->data['profitLossData']['totals']['cogs']).')'
                ];
                $rows[] = []; // Empty row

                // Gross Profit
                $rows[] = [
                    '',
                    'LABA KOTOR',
                    $this->data['profitLossData']['gross_profit'] >= 0 
                        ? $this->data['profitLossData']['gross_profit'] 
                        : '('.abs($this->data['profitLossData']['gross_profit']).')'
                ];
                $rows[] = []; // Empty row

                // Operating Expenses Section
                $rows[] = ['BEBAN OPERASIONAL'];
                $rows[] = ['Kode Akun', 'Nama Akun', 'Jumlah'];
                
                foreach ($this->data['profitLossData']['operating_expenses'] as $expense) {
                    $rows[] = [
                        $expense['code'],
                        $expense['name'],
                        '('.abs($expense['amount']).')'
                    ];
                }
                
                $rows[] = [
                    '',
                    'TOTAL BEBAN OPERASIONAL',
                    '('.abs($this->data['profitLossData']['totals']['operating_expenses']).')'
                ];
                $rows[] = []; // Empty row

                // Operating Profit
                $rows[] = [
                    '',
                    'LABA OPERASI',
                    $this->data['profitLossData']['operating_profit'] >= 0 
                        ? $this->data['profitLossData']['operating_profit'] 
                        : '('.abs($this->data['profitLossData']['operating_profit']).')'
                ];

                // Other Income/Expenses
                if (isset($this->data['profitLossData']['other_income'])) {
                    $rows[] = []; // Empty row
                    $rows[] = ['PENDAPATAN/BEBAN LAIN-LAIN'];
                    $rows[] = ['Kode Akun', 'Nama Akun', 'Jumlah'];
                    
                    foreach ($this->data['profitLossData']['other_income'] as $income) {
                        $rows[] = [
                            $income['code'],
                            $income['name'],
                            $income['amount'] >= 0 ? $income['amount'] : '('.abs($income['amount']).')'
                        ];
                    }
                    
                    foreach ($this->data['profitLossData']['other_expenses'] as $expense) {
                        $rows[] = [
                            $expense['code'],
                            $expense['name'],
                            '('.abs($expense['amount']).')'
                        ];
                    }
                    
                    if (count($this->data['profitLossData']['other_income'])) {
                        $rows[] = [
                            '',
                            'TOTAL PENDAPATAN LAIN',
                            $this->data['profitLossData']['totals']['other_income'] >= 0 
                                ? $this->data['profitLossData']['totals']['other_income'] 
                                : '('.abs($this->data['profitLossData']['totals']['other_income']).')'
                        ];
                    }
                    
                    if (count($this->data['profitLossData']['other_expenses'])) {
                        $rows[] = [
                            '',
                            'TOTAL BEBAN LAIN',
                            '('.abs($this->data['profitLossData']['totals']['other_expenses']).')'
                        ];
                    }
                }

                // Profit Before Tax
                $rows[] = []; // Empty row
                $rows[] = [
                    '',
                    'LABA SEBELUM PAJAK',
                    isset($this->data['profitLossData']['profit_before_tax']) 
                        ? ($this->data['profitLossData']['profit_before_tax'] >= 0 
                            ? $this->data['profitLossData']['profit_before_tax'] 
                            : '('.abs($this->data['profitLossData']['profit_before_tax']).')')
                        : ''
                ];

                // Tax Expense
                if (isset($this->data['profitLossData']['tax_expense']) && $this->data['profitLossData']['tax_expense'] > 0) {
                    $rows[] = [
                        '',
                        'PAJAK PENGHASILAN (10%)',
                        '('.abs($this->data['profitLossData']['tax_expense']).')'
                    ];
                }

                // Net Profit
                $rows[] = [
                    '',
                    'LABA BERSIH',
                    $this->data['profitLossData']['net_profit'] >= 0 
                        ? $this->data['profitLossData']['net_profit'] 
                        : '('.abs($this->data['profitLossData']['net_profit']).')'
                ];

                // Footer
                $rows[] = []; // Empty row
                $rows[] = ['Dicetak oleh ' . $this->data['preparedBy'] . ' pada ' . $this->data['exportDate']];

                return $rows;
            }

            public function title(): string
            {
                return 'Laba Rugi';
            }

            public function styles(Worksheet $sheet)
            {
                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(20);

                // Header styles
                $sheet->mergeCells('A1:C1');
                $sheet->mergeCells('A2:C2');
                $sheet->mergeCells('A3:C3');
                
                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ]
                ]);

                // Section title styles
                $sectionTitles = ['PENDAPATAN USAHA', 'HARGA POKOK PENJUALAN', 'BEBAN OPERASIONAL', 'PENDAPATAN/BEBAN LAIN-LAIN'];
                
                foreach ($sheet->getRowIterator() as $row) {
                    $cellValue = $sheet->getCell('A'.$row->getRowIndex())->getValue();
                    if (in_array($cellValue, $sectionTitles)) {
                        $sheet->mergeCells('A'.$row->getRowIndex().':C'.$row->getRowIndex());
                        $sheet->getStyle('A'.$row->getRowIndex())->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 11
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F2F2F2']
                            ]
                        ]);
                    }
                }

                // Table header styles
                $sheet->getStyle('A5:C6')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);

                // Amount column alignment
                $sheet->getStyle('C:C')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ]
                ]);

                // Revenue account rows
                $sheet->getStyle('A9:C'.(8 + count($this->data['profitLossData']['revenues']) + 1))->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F5E9']
                    ]
                ]);

                // COGS and Expense rows
                $sheet->getStyle('A'.(11 + count($this->data['profitLossData']['revenues']).':C'.(10 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + 1)))->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFEBEE']
                    ]
                ]);

                $sheet->getStyle('A'.(15 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']).':C'.(14 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + count($this->data['profitLossData']['operating_expenses']) + 1)))->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFEBEE']
                    ]
                ]);

                // Total rows style
                $totalRows = [
                    8 + count($this->data['profitLossData']['revenues']) + 1,
                    10 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + 1,
                    12 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']),
                    14 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + count($this->data['profitLossData']['operating_expenses']) + 1
                ];

                if (isset($this->data['profitLossData']['other_income'])) {
                    $totalRows[] = 16 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + count($this->data['profitLossData']['operating_expenses']) + count($this->data['profitLossData']['other_income']) + count($this->data['profitLossData']['other_expenses']) + 1;
                    $totalRows[] = 16 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + count($this->data['profitLossData']['operating_expenses']) + count($this->data['profitLossData']['other_income']) + count($this->data['profitLossData']['other_expenses']) + 2;
                }

                $totalRows[] = 16 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + count($this->data['profitLossData']['operating_expenses']) + (isset($this->data['profitLossData']['other_income']) ? count($this->data['profitLossData']['other_income']) + count($this->data['profitLossData']['other_expenses']) + 4 : 0);
                $totalRows[] = 17 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + count($this->data['profitLossData']['operating_expenses']) + (isset($this->data['profitLossData']['other_income']) ? count($this->data['profitLossData']['other_income']) + count($this->data['profitLossData']['other_expenses']) + 4 : 0);
                $totalRows[] = 18 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + count($this->data['profitLossData']['operating_expenses']) + (isset($this->data['profitLossData']['other_income']) ? count($this->data['profitLossData']['other_income']) + count($this->data['profitLossData']['other_expenses']) + 4 : 0);

                foreach ($totalRows as $row) {
                    $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
                        'font' => [
                            'bold' => true
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F5F5F5']
                        ]
                    ]);
                }

                // Net profit row style
                $netProfitRow = 18 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + count($this->data['profitLossData']['operating_expenses']) + (isset($this->data['profitLossData']['other_income']) ? count($this->data['profitLossData']['other_income']) + count($this->data['profitLossData']['other_expenses']) + 4 : 0);
                $sheet->getStyle('A'.$netProfitRow.':C'.$netProfitRow)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F5E9']
                    ]
                ]);

                // Borders for all data
                $lastRow = 19 + count($this->data['profitLossData']['revenues']) + count($this->data['profitLossData']['cogs']) + count($this->data['profitLossData']['operating_expenses']) + (isset($this->data['profitLossData']['other_income']) ? count($this->data['profitLossData']['other_income']) + count($this->data['profitLossData']['other_expenses']) + 4 : 0);
                $sheet->getStyle('A5:C'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
            }

            public function columnFormats(): array
            {
                return [
                    'C' => '"Rp"#,##0.00_);("Rp"#,##0.00)',
                ];
            }
        }, 
        $filename . '.xlsx'
    );
}
}