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

class WorksheetController extends Controller
{
    protected $coaService;

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }

    /**
     * Display the worksheet index page
     */
    public function index(Request $request)
    {
        // Set default book if not exists
        if (!$request->has('book_id')) {
            $defaultBook = AccountingBook::active()->first();
            if ($defaultBook) {
                return redirect()->route('financial.worksheet.index', [
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

            // Get all accounts
            $accounts = $this->coaService->getAllAccounts();
            $accountList = collect($accounts)->filter(fn($account) => !isset($account['children']));

            // Calculate worksheet data
            $worksheetData = $this->calculateWorksheetData($accountList, $accountingBook, $dateFrom, $dateTo);

            return view('financial.worksheet.index', [
                'accountingBook' => $accountingBook,
                'books' => $books,
                'worksheetData' => $worksheetData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'selectedBookId' => $bookId,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('financial.journal.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function calculateWorksheetData($accountList, $accountingBook, $dateFrom, $dateTo)
    {
        $worksheetData = [];
        $totals = [
            'trial_balance' => ['debit' => 0, 'credit' => 0],
            'income_statement' => ['debit' => 0, 'credit' => 0],
            'balance_sheet' => ['debit' => 0, 'credit' => 0],
        ];

        foreach ($accountList as $account) {
            $accountCode = $account['code'];
            $accountType = $account['type'];

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

            // Calculate totals
            $totalDebit = $initialDebit + ($entries->total_debit ?? 0);
            $totalCredit = $initialCredit + ($entries->total_credit ?? 0);

            // Prepare worksheet row
            $row = [
                'account_code' => $accountCode,
                'account_name' => $account['name'],
                'account_type' => $accountType,
                'trial_balance' => [
                    'debit' => $totalDebit,
                    'credit' => $totalCredit,
                ],
                'income_statement' => [
                    'debit' => 0,
                    'credit' => 0,
                ],
                'balance_sheet' => [
                    'debit' => 0,
                    'credit' => 0,
                ],
            ];

            // Classify to Balance Sheet or Income Statement
            if (in_array($accountType, ['revenue', 'expense'])) {
                // Income Statement accounts
                if ($accountType === 'revenue') {
                    // Pendapatan bisa di debit atau kredit tergantung transaksi
                    if ($totalCredit > $totalDebit) {
                        $row['income_statement']['credit'] = $totalCredit - $totalDebit;
                    } else {
                        $row['income_statement']['debit'] = $totalDebit - $totalCredit;
                    }
                } else {
                    // Beban selalu di debit
                    $row['income_statement']['debit'] = $totalDebit - $totalCredit;
                }
            } else {
                // Balance Sheet accounts
                if (in_array($accountType, ['liability', 'equity'])) {
                    // Kewajiban dan modal normalnya kredit
                    $row['balance_sheet']['credit'] = $totalCredit - $totalDebit;
                } else {
                    // Aset normalnya debit
                    $row['balance_sheet']['debit'] = $totalDebit - $totalCredit;
                }
            }

            // Add to totals
            $totals['trial_balance']['debit'] += $row['trial_balance']['debit'];
            $totals['trial_balance']['credit'] += $row['trial_balance']['credit'];
            $totals['income_statement']['debit'] += $row['income_statement']['debit'];
            $totals['income_statement']['credit'] += $row['income_statement']['credit'];
            $totals['balance_sheet']['debit'] += $row['balance_sheet']['debit'];
            $totals['balance_sheet']['credit'] += $row['balance_sheet']['credit'];

            $worksheetData[] = $row;
        }

        // Calculate net income
        $netIncome = $totals['income_statement']['credit'] - $totals['income_statement']['debit'];
        
        // Add net income to balance sheet (equity)
        if ($netIncome > 0) {
            $totals['balance_sheet']['credit'] += $netIncome;
        } else {
            $totals['balance_sheet']['debit'] += abs($netIncome);
        }

        return [
            'accounts' => $worksheetData,
            'totals' => $totals,
            'net_income' => $netIncome,
            'is_profit' => $netIncome >= 0,
        ];
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

            // Get all accounts
            $accounts = $this->coaService->getAllAccounts();
            $accountList = collect($accounts)->filter(fn($account) => !isset($account['children']));

            // Calculate worksheet data
            $worksheetData = $this->calculateWorksheetData($accountList, $accountingBook, $dateFrom, $dateTo);

            // Prepare common data
            $exportData = [
                'accountingBook' => $accountingBook,
                'worksheetData' => $worksheetData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'exportDate' => now()->format('d/m/Y H:i:s'),
                'preparedBy' => auth()->user()->name,
            ];

            // Generate filename
            $filename = 'Neraca_Lajur_' . $accountingBook->name;
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

    /**
     * Export to PDF
     */
    protected function exportToPdf($data, $filename)
    {
        $pdf = \PDF::loadView('financial.worksheet.export_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'.pdf"');
    }

    /**
     * Export to Excel
     */
    protected function exportToExcel($data, $filename)
    {
        // Pastikan class Excel di-import di bagian atas file
        // use Maatwebsite\Excel\Facades\Excel;
        
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
                    $rows[] = ['NERACA LAJUR'];
                    $rows[] = ['Perusahaan:', config('app.name')];
                    $rows[] = ['Tahun Buku:', $this->data['accountingBook']->name];
                    $rows[] = ['Periode:', $this->data['dateFrom'] ? 
                        $this->data['dateFrom']->format('d/m/Y') . ' - ' . $this->data['dateTo']->format('d/m/Y') : 
                        'Semua Periode'];
                    $rows[] = ['Dicetak oleh:', $this->data['preparedBy']];
                    $rows[] = ['Tanggal Cetak:', $this->data['exportDate']];
                    $rows[] = []; // Empty row

                    // Table header
                    $rows[] = [
                        'No',
                        'Kode Akun',
                        'Nama Akun',
                        'Neraca Saldo (Debit)',
                        'Neraca Saldo (Kredit)',
                        'Laba Rugi (Debit)',
                        'Laba Rugi (Kredit)',
                        'Neraca (Debit)',
                        'Neraca (Kredit)'
                    ];

                    // Data rows
                    foreach ($this->data['worksheetData']['accounts'] as $index => $account) {
                        $rows[] = [
                            $index + 1,
                            $account['account_code'],
                            $account['account_name'],
                            $account['trial_balance']['debit'],
                            $account['trial_balance']['credit'],
                            $account['income_statement']['debit'],
                            $account['income_statement']['credit'],
                            $account['balance_sheet']['debit'],
                            $account['balance_sheet']['credit'],
                        ];
                    }

                    // Total row
                    $rows[] = [
                        '',
                        '',
                        'TOTAL',
                        $this->data['worksheetData']['totals']['trial_balance']['debit'],
                        $this->data['worksheetData']['totals']['trial_balance']['credit'],
                        $this->data['worksheetData']['totals']['income_statement']['debit'],
                        $this->data['worksheetData']['totals']['income_statement']['credit'],
                        $this->data['worksheetData']['totals']['balance_sheet']['debit'],
                        $this->data['worksheetData']['totals']['balance_sheet']['credit'],
                    ];

                    // Total Laba Rugi Seimbang
                    $rows[] = [
                        '',
                        '',
                        'TOTAL LABA/RUGI SEIMBANG',
                        '',
                        '',
                        $this->data['worksheetData']['totals']['income_statement']['debit'],
                        $this->data['worksheetData']['totals']['income_statement']['credit'],
                        '',
                        '',
                    ];

                    // Net income row
                    $netIncomeLabel = $this->data['worksheetData']['net_income'] >= 0 ? 'Laba Bersih' : 'Rugi Bersih';
                    $rows[] = [
                        '',
                        '',
                        $netIncomeLabel,
                        '',
                        '',
                        '',
                        '',
                        $this->data['worksheetData']['net_income'] < 0 ? abs($this->data['worksheetData']['net_income']) : '',
                        $this->data['worksheetData']['net_income'] >= 0 ? $this->data['worksheetData']['net_income'] : '',
                    ];

                    // Total Neraca Seimbang
                    $rows[] = [
                        '',
                        '',
                        'TOTAL NERACA SEIMBANG',
                        '',
                        '',
                        '',
                        '',
                        $this->data['worksheetData']['totals']['balance_sheet']['debit'] + ($this->data['worksheetData']['net_income'] < 0 ? abs($this->data['worksheetData']['net_income']) : 0),
                        $this->data['worksheetData']['totals']['balance_sheet']['credit'] + ($this->data['worksheetData']['net_income'] >= 0 ? $this->data['worksheetData']['net_income'] : 0),
                    ];

                    return $rows;
                }

                public function title(): string
                {
                    return 'Neraca Lajur';
                }

                public function styles(Worksheet $sheet)
                {
                    // Apply styles to the sheet
                    $sheet->mergeCells('A1:I1');
                    $sheet->mergeCells('A2:I2');
                    $sheet->mergeCells('A3:I3');
                    $sheet->mergeCells('A4:I4');
                    $sheet->mergeCells('A5:I5');
                    $sheet->mergeCells('A6:I6');
                    
                    // Style for header
                    $sheet->getStyle('A1:I8')->applyFromArray([
                        'font' => [
                            'bold' => true
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ]
                    ]);

                    // Style for data
                    $lastRow = count($this->data['worksheetData']['accounts']) + 9;
                    $sheet->getStyle('A8:I'.$lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                    // Style for totals
                    $sheet->getStyle('A'.$lastRow.':I'.$lastRow)->applyFromArray([
                        'font' => [
                            'bold' => true
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'FFD9D9D9',
                            ],
                        ],
                    ]);
                }

                public function columnFormats(): array
                {
                    return [
                        'D' => '#,##0.00',
                        'E' => '#,##0.00',
                        'F' => '#,##0.00',
                        'G' => '#,##0.00',
                        'H' => '#,##0.00',
                        'I' => '#,##0.00',
                    ];
                }
            }, 
            $filename . '.xlsx'
        );
    }
}