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

class EquityChangeController extends Controller
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
                return redirect()->route('financial.equity-change.index', [
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
    
            // Calculate equity data
            $equityData = $this->calculateEquityData($accountingBook, $dateFrom, $dateTo);
    
            return view('financial.equity-change.index', [
                'accountingBook' => $accountingBook,
                'books' => $books,
                'equityData' => $equityData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'selectedBookId' => $bookId
            ]);
    
        } catch (\Exception $e) {
            return redirect()->route('financial.journal.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function calculateEquityData($accountingBook, $dateFrom, $dateTo)
    {
        $equityAccounts = $this->coaService->getAccountsByType('equity');
        
        // Ambil data laba rugi dari fungsi yang sudah ada
        $profitLossData = $this->calculateProfitLossData($accountingBook, $dateFrom, $dateTo);
        $netProfit = $profitLossData['net_profit'];

        $data = [
            'beginning_equity' => 0,
            'additional_investment' => 0,
            'profit_loss' => $netProfit,
            'owner_withdrawal' => 0,
            'ending_equity' => 0,
            'equity_items' => [],
            'profit_loss_detail' => $profitLossData // Simpan detail laba rugi untuk ditampilkan
        ];

        foreach ($equityAccounts as $account) {
            $balance = $this->calculateAccountBalance($account['code'], $accountingBook, $dateFrom, $dateTo);
            
            // Skip zero balance accounts
            if (abs($balance) < 0.01) continue;

            $item = [
                'code' => $account['code'],
                'name' => $account['name'],
                'amount' => $balance,
                'is_beginning' => false,
                'is_additional' => false,
                'is_withdrawal' => false
            ];

            // Klasifikasi berdasarkan kode akun
            $codeParts = explode('.', $account['code']);
            
            if (count($codeParts) >= 2) {
                $mainGroup = $codeParts[1];
                
                switch ($mainGroup) {
                    case '01': // Modal awal (3.01.xx)
                        $item['is_beginning'] = true;
                        $data['beginning_equity'] += $balance;
                        break;
                    case '02': // Perubahan modal (3.02.xx)
                        $item['is_additional'] = true;
                        $data['additional_investment'] += $balance;
                        break;
                    case '03': // Prive (3.03.xx)
                        $item['is_withdrawal'] = true;
                        $data['owner_withdrawal'] += abs($balance);
                        break;
                }
            }

            $data['equity_items'][] = $item;
        }

        // Hitung modal akhir
        $data['ending_equity'] = $data['beginning_equity'] 
                            + $data['additional_investment'] 
                            + $data['profit_loss'] 
                            - $data['owner_withdrawal'];

        // \Log::info('beginning_equity: ' . number_format($data['beginning_equity'], 6, '.', ''));
        // \Log::info('additional_investment: ' . number_format($data['additional_investment'], 6, '.', ''));
        // \Log::info('profit_loss: ' . number_format($data['profit_loss'], 6, '.', ''));
        // \Log::info('owner_withdrawal: ' . number_format($data['owner_withdrawal'], 6, '.', ''));
        // \Log::info('ending_equity: ' . number_format($data['ending_equity'], 6, '.', ''));


        return $data;
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

        // Determine balance based on account type
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

            // Get accounting book data
            $accountingBook = AccountingBook::findOrFail($bookId);

            // Calculate equity data
            $equityData = $this->calculateEquityData($accountingBook, $dateFrom, $dateTo);

            // Prepare common data
            $exportData = [
                'accountingBook' => $accountingBook,
                'equityData' => $equityData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'exportDate' => now()->format('d/m/Y H:i:s'),
                'preparedBy' => auth()->user()->name,
            ];

            // Generate filename
            $filename = 'Laporan_Perubahan_Modal_' . $accountingBook->name;
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
        $pdf = \PDF::loadView('financial.equity-change.export_pdf', $data);
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
                    $rows[] = ['LAPORAN PERUBAHAN MODAL'];
                    $rows[] = ['Perusahaan:', config('app.name')];
                    $rows[] = ['Tahun Buku:', $this->data['accountingBook']->name];
                    $rows[] = ['Periode:', $this->data['dateFrom'] ? 
                        $this->data['dateFrom']->format('d/m/Y') . ' - ' . $this->data['dateTo']->format('d/m/Y') : 
                        'Semua Periode'];
                    $rows[] = ['Dicetak oleh:', $this->data['preparedBy']];
                    $rows[] = ['Tanggal Cetak:', $this->data['exportDate']];
                    $rows[] = []; // Empty row

                    // Beginning Equity
                    $rows[] = ['MODAL AWAL'];
                    $rows[] = ['Kode Akun', 'Nama Akun', 'Jumlah'];

                    foreach ($this->data['equityData']['equity_items'] as $item) {
                        if ($item['is_beginning']) {
                            $rows[] = [$item['code'], $item['name'], $item['amount']];
                        }
                    }

                    $rows[] = ['', 'TOTAL MODAL AWAL', $this->data['equityData']['beginning_equity']];
                    $rows[] = []; // Empty row

                    // Additional Investment
                    $rows[] = ['TAMBAHAN MODAL'];
                    $rows[] = ['Kode Akun', 'Nama Akun', 'Jumlah'];

                    foreach ($this->data['equityData']['equity_items'] as $item) {
                        if ($item['is_additional']) {
                            $rows[] = [$item['code'], $item['name'], $item['amount']];
                        }
                    }

                    $rows[] = ['', 'TOTAL TAMBAHAN MODAL', $this->data['equityData']['additional_investment']];
                    $rows[] = []; // Empty row

                    // Profit/Loss
                    $rows[] = ['LABA/RUGI BERSIH'];
                    $rows[] = ['', 'Pendapatan - Beban', $this->data['equityData']['profit_loss']];
                    $rows[] = []; // Empty row

                    // Owner Withdrawal
                    $rows[] = ['PENGAMBILAN PRIVE'];
                    $rows[] = ['Kode Akun', 'Nama Akun', 'Jumlah'];

                    foreach ($this->data['equityData']['equity_items'] as $item) {
                        if ($item['is_withdrawal']) {
                            $rows[] = [$item['code'], $item['name'], abs($item['amount'])];
                        }
                    }

                    $rows[] = ['', 'TOTAL PENGAMBILAN PRIVE', $this->data['equityData']['owner_withdrawal']];
                    $rows[] = []; // Empty row

                    // Ending Equity Calculation
                    $rows[] = ['PERHITUNGAN MODAL AKHIR'];
                    $rows[] = ['', 'Modal Awal', $this->data['equityData']['beginning_equity']];
                    $rows[] = ['', 'Tambahan Modal', $this->data['equityData']['additional_investment']];
                    $rows[] = ['', 'Laba/Rugi Bersih', $this->data['equityData']['profit_loss']];
                    $rows[] = ['', 'Pengambilan Prive', -$this->data['equityData']['owner_withdrawal']];
                    $rows[] = ['', 'MODAL AKHIR', $this->data['equityData']['ending_equity']];

                    return $rows;
                }

                public function title(): string
                {
                    return 'Perubahan Modal';
                }

                public function styles(Worksheet $sheet)
                {
                    // Apply styles to the sheet
                    $sheet->mergeCells('A1:C1');
                    $sheet->mergeCells('A2:C2');
                    $sheet->mergeCells('A3:C3');
                    $sheet->mergeCells('A4:C4');
                    $sheet->mergeCells('A5:C5');
                    $sheet->mergeCells('A6:C6');
                    
                    // Style for header
                    $sheet->getStyle('A1:C8')->applyFromArray([
                        'font' => [
                            'bold' => true
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ]
                    ]);

                    // Style for data
                    $lastRow = count($this->data['equityData']['equity_items']) + 20;
                    $sheet->getStyle('A8:C'.$lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                    // Style for totals
                    $totalRows = [12, 19, 22, 28, 31];
                    foreach ($totalRows as $row) {
                        $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
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
                }

                public function columnFormats(): array
                {
                    return [
                        'C' => '#,##0.00',
                    ];
                }
            }, 
            $filename . '.xlsx'
        );
    }
}