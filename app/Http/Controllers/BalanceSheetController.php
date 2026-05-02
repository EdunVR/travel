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

class BalanceSheetController extends Controller
{
    protected $coaService;

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }

    public function index(Request $request)
    {
        if (!$request->has('book_id')) {
            $defaultBook = AccountingBook::active()->first();
            if ($defaultBook) {
                return redirect()->route('financial.balance-sheet.index', [
                    'book_id' => $defaultBook->id
                ]);
            }
            
            return redirect()->route('financial.journal.index')
                ->with('error', 'Tidak ada tahun buku aktif. Silakan buat tahun buku terlebih dahulu.');
        }

        $request->validate([
            'book_id' => 'required|exists:accounting_books,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        try {
            $bookId = $request->book_id;
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : null;
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : null;

            $accountingBook = AccountingBook::findOrFail($bookId);
            $books = AccountingBook::orderBy('start_date', 'desc')->get();

            $balanceSheetData = $this->calculateBalanceSheet($accountingBook, $dateFrom, $dateTo);

            return view('financial.balance-sheet.index', [
                'accountingBook' => $accountingBook,
                'books' => $books,
                'balanceSheetData' => $balanceSheetData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'selectedBookId' => $bookId,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('financial.journal.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function calculateBalanceSheet($accountingBook, $dateFrom, $dateTo)
    {
        $assets = $this->coaService->getAccountsByType('asset');
        $liabilities = $this->coaService->getAccountsByType('liability');
        $equities = $this->coaService->getAccountsByType('equity');

        $data = [
            'assets' => [
                'current' => [],
                'fixed' => [],
                'other' => [],
                'total_current' => 0,
                'total_fixed' => 0,
                'total_other' => 0,
                'total_assets' => 0
            ],
            'liabilities' => [
                'current' => [],
                'long_term' => [],
                'total_current' => 0,
                'total_long_term' => 0,
                'total_liabilities' => 0
            ],
            'equities' => [
                'items' => [],
                'total_equities' => 0
            ],
            'total_liabilities_equities' => 0
        ];

        // Calculate assets
        foreach ($assets as $account) {
            $balance = $this->calculateAccountBalance($account['code'], $accountingBook, $dateFrom, $dateTo);
            if ($balance != 0) {
                $accountData = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'amount' => $balance
                ];

                // Classify asset type
                if (str_contains(strtolower($account['name']), 'kas') || 
                    str_contains(strtolower($account['name']), 'piutang') ||
                    str_contains(strtolower($account['name']), 'persediaan')) {
                    $data['assets']['current'][] = $accountData;
                    $data['assets']['total_current'] += $balance;
                } elseif (str_contains(strtolower($account['name']), 'tanah') || 
                         str_contains(strtolower($account['name']), 'bangunan') ||
                         str_contains(strtolower($account['name']), 'peralatan')) {
                    $data['assets']['fixed'][] = $accountData;
                    $data['assets']['total_fixed'] += $balance;
                } else {
                    $data['assets']['other'][] = $accountData;
                    $data['assets']['total_other'] += $balance;
                }
            }
        }

        // Calculate liabilities
        foreach ($liabilities as $account) {
            $balance = $this->calculateAccountBalance($account['code'], $accountingBook, $dateFrom, $dateTo);
            if ($balance != 0) {
                $accountData = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'amount' => $balance
                ];

                // Classify liability type
                if (str_contains(strtolower($account['name']), 'hutang') && 
                   !str_contains(strtolower($account['name']), 'jangka panjang')) {
                    $data['liabilities']['current'][] = $accountData;
                    $data['liabilities']['total_current'] += $balance;
                } else {
                    $data['liabilities']['long_term'][] = $accountData;
                    $data['liabilities']['total_long_term'] += $balance;
                }
            }
        }

        // Calculate equities
        foreach ($equities as $account) {
            $balance = $this->calculateAccountBalance($account['code'], $accountingBook, $dateFrom, $dateTo);
            if ($balance != 0) {
                $data['equities']['items'][] = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'amount' => $balance
                ];
                $data['equities']['total_equities'] += $balance;
            }
        }

        // Calculate totals
        $data['assets']['total_assets'] = $data['assets']['total_current'] + 
                                        $data['assets']['total_fixed'] + 
                                        $data['assets']['total_other'];

        $data['liabilities']['total_liabilities'] = $data['liabilities']['total_current'] + 
                                                  $data['liabilities']['total_long_term'];

        $data['total_liabilities_equities'] = $data['liabilities']['total_liabilities'] + 
                                            $data['equities']['total_equities'];

        return $data;
    }

    protected function calculateAccountBalance($accountCode, $accountingBook, $dateFrom, $dateTo)
    {
        $openingBalance = AccountOpeningBalance::where('accounting_book_id', $accountingBook->id)
            ->where('account_code', $accountCode)
            ->first();

        $initialDebit = $openingBalance ? $openingBalance->debit : 0;
        $initialCredit = $openingBalance ? $openingBalance->credit : 0;

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

        $account = $this->coaService->getAccountByCode($accountCode);
        $isCreditAccount = in_array($account['type'], ['liability', 'equity', 'revenue']);

        return $isCreditAccount ? ($totalCredit - $totalDebit) : ($totalDebit - $totalCredit);
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

            $accountingBook = AccountingBook::findOrFail($bookId);
            $balanceSheetData = $this->calculateBalanceSheet($accountingBook, $dateFrom, $dateTo);

            $exportData = [
                'accountingBook' => $accountingBook,
                'balanceSheetData' => $balanceSheetData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'exportDate' => now()->format('d/m/Y H:i:s'),
                'preparedBy' => auth()->user()->name,
            ];

            $filename = 'Neraca_' . $accountingBook->name;
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
        $pdf = \PDF::loadView('financial.balance-sheet.export_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($filename . '.pdf');
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
                    $rows[] = ['NERACA'];
                    $rows[] = ['Perusahaan:', config('app.name')];
                    $rows[] = ['Tahun Buku:', $this->data['accountingBook']->name];
                    $rows[] = ['Periode:', $this->data['dateFrom'] ? 
                        $this->data['dateFrom']->format('d/m/Y') . ' - ' . $this->data['dateTo']->format('d/m/Y') : 
                        'Semua Periode'];
                    $rows[] = ['Dicetak oleh:', $this->data['preparedBy']];
                    $rows[] = ['Tanggal Cetak:', $this->data['exportDate']];
                    $rows[] = []; // Empty row

                    // Balance Sheet Header
                    $rows[] = ['AKTIVA', '', 'PASIVA'];

                    // Current Assets
                    $rows[] = ['AKTIVA LANCAR', '', 'KEWAJIBAN LANCAR'];
                    foreach ($this->data['balanceSheetData']['assets']['current'] as $asset) {
                        $rows[] = [
                            $asset['code'] . ' ' . $asset['name'],
                            number_format($asset['amount'], 2),
                            ''
                        ];
                    }
                    $rows[] = [
                        'TOTAL AKTIVA LANCAR',
                        number_format($this->data['balanceSheetData']['assets']['total_current'], 2),
                        ''
                    ];
                    $rows[] = [];

                    // Fixed Assets
                    $rows[] = ['AKTIVA TETAP', '', 'KEWAJIBAN JANGKA PANJANG'];
                    foreach ($this->data['balanceSheetData']['assets']['fixed'] as $asset) {
                        $rows[] = [
                            $asset['code'] . ' ' . $asset['name'],
                            number_format($asset['amount'], 2),
                            ''
                        ];
                    }
                    $rows[] = [
                        'TOTAL AKTIVA TETAP',
                        number_format($this->data['balanceSheetData']['assets']['total_fixed'], 2),
                        ''
                    ];
                    $rows[] = [];

                    // Other Assets
                    if (!empty($this->data['balanceSheetData']['assets']['other'])) {
                        $rows[] = ['AKTIVA LAINNYA', '', ''];
                        foreach ($this->data['balanceSheetData']['assets']['other'] as $asset) {
                            $rows[] = [
                                $asset['code'] . ' ' . $asset['name'],
                                number_format($asset['amount'], 2),
                                ''
                            ];
                        }
                        $rows[] = [
                            'TOTAL AKTIVA LAINNYA',
                            number_format($this->data['balanceSheetData']['assets']['total_other'], 2),
                            ''
                        ];
                        $rows[] = [];
                    }

                    // Current Liabilities
                    foreach ($this->data['balanceSheetData']['liabilities']['current'] as $liability) {
                        $rows[] = [
                            '',
                            '',
                            $liability['code'] . ' ' . $liability['name'] . ' ' . number_format($liability['amount'], 2)
                        ];
                    }
                    $rows[] = [
                        '',
                        '',
                        'TOTAL KEWAJIBAN LANCAR ' . number_format($this->data['balanceSheetData']['liabilities']['total_current'], 2)
                    ];
                    $rows[] = [];

                    // Long-term Liabilities
                    foreach ($this->data['balanceSheetData']['liabilities']['long_term'] as $liability) {
                        $rows[] = [
                            '',
                            '',
                            $liability['code'] . ' ' . $liability['name'] . ' ' . number_format($liability['amount'], 2)
                        ];
                    }
                    $rows[] = [
                        '',
                        '',
                        'TOTAL KEWAJIBAN JANGKA PANJANG ' . number_format($this->data['balanceSheetData']['liabilities']['total_long_term'], 2)
                    ];
                    $rows[] = [];

                    // Equities
                    $rows[] = ['', '', 'MODAL'];
                    foreach ($this->data['balanceSheetData']['equities']['items'] as $equity) {
                        $rows[] = [
                            '',
                            '',
                            $equity['code'] . ' ' . $equity['name'] . ' ' . number_format($equity['amount'], 2)
                        ];
                    }
                    $rows[] = [
                        '',
                        '',
                        'TOTAL MODAL ' . number_format($this->data['balanceSheetData']['equities']['total_equities'], 2)
                    ];
                    $rows[] = [];

                    // Totals
                    $rows[] = [
                        'TOTAL AKTIVA',
                        number_format($this->data['balanceSheetData']['assets']['total_assets'], 2),
                        'TOTAL KEWAJIBAN & MODAL ' . number_format($this->data['balanceSheetData']['total_liabilities_equities'], 2)
                    ];

                    return $rows;
                }

                public function title(): string
                {
                    return 'Neraca';
                }

                public function styles(Worksheet $sheet)
                {
                    $sheet->mergeCells('A1:C1');
                    $sheet->mergeCells('A2:C2');
                    $sheet->mergeCells('A3:C3');
                    $sheet->mergeCells('A4:C4');
                    $sheet->mergeCells('A5:C5');
                    $sheet->mergeCells('A6:C6');
                    $sheet->mergeCells('A8:C8');

                    $sheet->getStyle('A1:C8')->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    ]);

                    $lastRow = count($this->data['balanceSheetData']['assets']['current']) + 
                             count($this->data['balanceSheetData']['assets']['fixed']) + 
                             count($this->data['balanceSheetData']['assets']['other']) + 
                             count($this->data['balanceSheetData']['liabilities']['current']) + 
                             count($this->data['balanceSheetData']['liabilities']['long_term']) + 
                             count($this->data['balanceSheetData']['equities']['items']) + 30;

                    $sheet->getStyle('A8:C'.$lastRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    ]);

                    $totalRows = [
                        count($this->data['balanceSheetData']['assets']['current']) + 10,
                        count($this->data['balanceSheetData']['assets']['current']) + 
                            count($this->data['balanceSheetData']['assets']['fixed']) + 13,
                        $lastRow - 1
                    ];

                    foreach ($totalRows as $row) {
                        $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
                        ]);
                    }
                }

                public function columnFormats(): array
                {
                    return [
                        'B' => '#,##0.00',
                    ];
                }
            }, 
            $filename . '.xlsx'
        );
    }
}