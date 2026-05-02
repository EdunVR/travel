<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\AccountingBook;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JournalEntryService
{
    /**
     * Create automatic journal entry
     */
    public function createAutomaticJournal(
        string $referenceType,
        int $referenceId,
        string $transactionDate,
        string $description,
        array $entries,
        int $bookId,
        int $outletId = null
    ): ?JournalEntry {
        try {
            DB::beginTransaction();

            // Set outlet_id jika tidak disediakan
            if (!$outletId) {
                $outletId = auth()->user()->outlet_id ?? 1;
            }

            // Validate accounting book
            $accountingBook = AccountingBook::find($bookId);
            if (!$accountingBook) {
                throw new \Exception("Accounting book dengan ID {$bookId} tidak ditemukan");
            }

            // Calculate totals
            $totalDebit = 0;
            $totalCredit = 0;
            
            foreach ($entries as $entry) {
                $totalDebit += $entry['debit'] ?? 0;
                $totalCredit += $entry['credit'] ?? 0;
                
                // Validate account exists
                $account = ChartOfAccount::find($entry['account_id']);
                if (!$account) {
                    throw new \Exception("Account dengan ID {$entry['account_id']} tidak ditemukan");
                }
            }

            // Check if journal is balanced
            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception("Jurnal tidak balance: Debit {$totalDebit} vs Credit {$totalCredit}");
            }

            // Generate transaction number
            $transactionNumber = JournalEntry::generateTransactionNumber($bookId);

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'book_id' => $bookId,
                'outlet_id' => $outletId,
                'transaction_number' => $transactionNumber,
                'transaction_date' => $transactionDate,
                'description' => $description,
                'status' => 'posted', // Auto post untuk jurnal otomatis
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'reference_type' => $referenceType,
                'reference_number' => $this->generateReferenceNumber($referenceType, $referenceId),
                'posted_at' => now(),
            ]);

            // Create journal entry details
            foreach ($entries as $entry) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'description' => $entry['memo'] ?? '',
                    'reference_type' => $referenceType,
                    'reference_number' => $this->generateReferenceNumber($referenceType, $referenceId),
                ]);
            }

            DB::commit();

            Log::info("Automatic journal created successfully", [
                'journal_entry_id' => $journalEntry->id,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'outlet_id' => $outletId
            ]);

            return $journalEntry;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Failed to create automatic journal for {$referenceType}: " . $e->getMessage(), [
                'reference_id' => $referenceId,
                'outlet_id' => $outletId,
                'book_id' => $bookId
            ]);
            
            throw new \Exception("Gagal membuat jurnal otomatis: " . $e->getMessage());
        }
    }

    public function createSalesInvoiceJournal(
        $invoice,
        string $status,
        string $oldStatus = null
    ): ?JournalEntry {
        try {
            $outletId = $invoice->id_outlet ?? auth()->user()->outlet_id ?? 1;
            $setting = \App\Models\SettingCOASales::getByOutlet($outletId);
            
            if (!$setting) {
                Log::info('Setting COA untuk penjualan belum diatur untuk outlet ' . $outletId . ', skip jurnal otomatis');
                return null;
            }

            $transactionDate = ($status === 'lunas') ? now() : ($invoice->tanggal ?? now());
            $journalData = $this->prepareSalesInvoiceJournalData($invoice, $status, $setting, $oldStatus);
            
            if (empty($journalData['entries'])) {
                Log::info('Tidak ada entri jurnal untuk status: ' . $status);
                return null;
            }

            return $this->createAutomaticJournal(
                'penjualan',
                $invoice->id_sales_invoice,
                $transactionDate,
                $journalData['description'],
                $journalData['entries'],
                $setting->accounting_book_id,
                $outletId
            );

        } catch (\Exception $e) {
            Log::error('Gagal membuat jurnal penjualan: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id_sales_invoice ?? 'unknown',
                'status' => $status,
                'outlet_id' => $outletId ?? 'unknown'
            ]);
            return null;
        }
    }

    /**
     * Prepare journal data for sales invoice based on status
     */
    private function prepareSalesInvoiceJournalData($invoice, string $status, $setting, string $oldStatus = null): array
    {
        $description = "Invoice Penjualan {$invoice->no_invoice} - " . strtoupper($status);
        $customerName = $invoice->member ? $invoice->member->nama : ($invoice->prospek ? $invoice->prospek->nama : 'Customer');
        $entries = [];

        // Hitung total HPP dari items produk
        $totalHpp = 0;
        if ($invoice->items) {
            foreach ($invoice->items as $item) {
                if ($item->tipe === 'produk' && $item->id_produk) {
                    $produk = \App\Models\Produk::find($item->id_produk);
                    if ($produk) {
                        $hpp = $produk->calculateHppBarangDagang();
                        $totalHpp += $hpp * $item->kuantitas;
                    }
                }
            }
        }

        switch ($status) {
            case 'menunggu':
                // Jurnal: Piutang Usaha (D) vs Pendapatan Penjualan (K)
                $entries = [
                    [
                        'account_id' => $this->getAccountIdByCode($setting->akun_piutang_usaha, $invoice->id_outlet),
                        'debit' => $invoice->total,
                        'credit' => 0,
                        'memo' => 'Piutang usaha dari ' . $customerName
                    ],
                    [
                        'account_id' => $this->getAccountIdByCode($setting->akun_pendapatan_penjualan, $invoice->id_outlet),
                        'debit' => 0,
                        'credit' => $invoice->total,
                        'memo' => 'Pendapatan penjualan'
                    ]
                ];

                // Tambahkan jurnal HPP dan persediaan jika ada produk
                if ($totalHpp > 0 && !empty($setting->akun_hpp) && !empty($setting->akun_persediaan)) {
                    $entries[] = [
                        'account_id' => $this->getAccountIdByCode($setting->akun_hpp, $invoice->id_outlet),
                        'debit' => $totalHpp,
                        'credit' => 0,
                        'memo' => 'HPP penjualan'
                    ];
                    $entries[] = [
                        'account_id' => $this->getAccountIdByCode($setting->akun_persediaan, $invoice->id_outlet),
                        'debit' => 0,
                        'credit' => $totalHpp,
                        'memo' => 'Pengurangan persediaan'
                    ];
                }
                break;

            case 'lunas':
                // Jurnal: Kas/Bank (D) vs Piutang Usaha (K)
                $akunKasBank = $invoice->jenis_pembayaran == 'cash' 
                    ? $this->getAccountIdByCode($setting->akun_kas, $invoice->id_outlet)
                    : $this->getAccountIdByCode($setting->akun_bank, $invoice->id_outlet);

                $entries = [
                    [
                        'account_id' => $akunKasBank,
                        'debit' => $invoice->total,
                        'credit' => 0,
                        'memo' => 'Penerimaan pembayaran dari ' . $customerName
                    ],
                    [
                        'account_id' => $this->getAccountIdByCode($setting->akun_piutang_usaha, $invoice->id_outlet),
                        'debit' => 0,
                        'credit' => $invoice->total,
                        'memo' => 'Pelunasan piutang usaha'
                    ]
                ];
                break;

            case 'gagal':
                // Jika sebelumnya sudah ada jurnal, kita perlu reverse
                if ($oldStatus === 'menunggu') {
                    // Reverse jurnal penjualan
                    $entries = [
                        [
                            'account_id' => $this->getAccountIdByCode($setting->akun_pendapatan_penjualan, $invoice->id_outlet),
                            'debit' => $invoice->total,
                            'credit' => 0,
                            'memo' => 'Pembatalan penjualan'
                        ],
                        [
                            'account_id' => $this->getAccountIdByCode($setting->akun_piutang_usaha, $invoice->id_outlet),
                            'debit' => 0,
                            'credit' => $invoice->total,
                            'memo' => 'Pembatalan piutang usaha'
                        ]
                    ];

                    // Reverse jurnal HPP dan persediaan jika ada
                    if ($totalHpp > 0 && !empty($setting->akun_hpp) && !empty($setting->akun_persediaan)) {
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode($setting->akun_hpp, $invoice->id_outlet),
                            'debit' => 0,
                            'credit' => $totalHpp,
                            'memo' => 'Pembatalan HPP penjualan'
                        ];
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode($setting->akun_persediaan, $invoice->id_outlet),
                            'debit' => $totalHpp,
                            'credit' => 0,
                            'memo' => 'Pengembalian persediaan'
                        ];
                    }
                }
                break;
        }

        return [
            'description' => $description,
            'entries' => $entries
        ];
    }

    /**
     * Get account ID by code and outlet
     */
    private function getAccountIdByCode(string $code, int $outletId = null): int
    {
        if (!$outletId) {
            $outletId = auth()->user()->outlet_id ?? 1;
        }
        
        $account = ChartOfAccount::where('code', $code)
            ->where('outlet_id', $outletId)
            ->first();
        
        if (!$account) {
            throw new \Exception("Akun dengan kode {$code} tidak ditemukan untuk outlet {$outletId}");
        }
        
        return $account->id;
    }

    /**
     * Generate reference number
     */
    private function generateReferenceNumber(string $referenceType, int $referenceId): string
    {
        $prefix = strtoupper(substr($referenceType, 0, 3));
        return $prefix . '-' . str_pad($referenceId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Void journal entry by reference
     */
    public function voidJournalByReference(string $referenceType, int $referenceId): bool
    {
        try {
            $journalEntry = JournalEntry::where('reference_type', $referenceType)
                ->where('reference_number', $this->generateReferenceNumber($referenceType, $referenceId))
                ->first();

            if ($journalEntry) {
                return $journalEntry->void();
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Gagal void jurnal: ' . $e->getMessage(), [
                'reference_type' => $referenceType,
                'reference_id' => $referenceId
            ]);
            return false;
        }
    }

    /**
     * Get journal entries by reference
     */
    public function getJournalByReference(string $referenceType, int $referenceId): ?JournalEntry
    {
        return JournalEntry::where('reference_type', $referenceType)
            ->where('reference_number', $this->generateReferenceNumber($referenceType, $referenceId))
            ->with(['journalEntryDetails.account'])
            ->first();
    }

    /**
     * Create journal entry for invoice payment (installment or full)
     */
    public function createInvoicePaymentJournal(
        $invoice,
        $paymentHistory
    ): ?JournalEntry {
        try {
            $outletId = $invoice->id_outlet ?? auth()->user()->outlet_id ?? 1;
            $setting = \App\Models\SettingCOASales::getByOutlet($outletId);
            
            if (!$setting) {
                Log::info('Setting COA untuk penjualan belum diatur untuk outlet ' . $outletId . ', skip jurnal otomatis');
                return null;
            }

            $customerName = $invoice->member ? $invoice->member->nama : ($invoice->prospek ? $invoice->prospek->nama : 'Customer');
            $description = "Pembayaran Invoice {$invoice->no_invoice} - {$customerName}";
            
            // Determine account based on payment type
            $akunKasBank = $paymentHistory->jenis_pembayaran == 'cash' 
                ? $this->getAccountIdByCode($setting->akun_kas, $invoice->id_outlet)
                : $this->getAccountIdByCode($setting->akun_bank, $invoice->id_outlet);

            // Journal entry: Kas/Bank (D) vs Piutang Usaha (K)
            $entries = [
                [
                    'account_id' => $akunKasBank,
                    'debit' => $paymentHistory->jumlah_bayar,
                    'credit' => 0,
                    'memo' => 'Penerimaan pembayaran dari ' . $customerName . ' (' . $paymentHistory->jenis_pembayaran . ')'
                ],
                [
                    'account_id' => $this->getAccountIdByCode($setting->akun_piutang_usaha, $invoice->id_outlet),
                    'debit' => 0,
                    'credit' => $paymentHistory->jumlah_bayar,
                    'memo' => 'Pengurangan piutang usaha'
                ]
            ];

            return $this->createAutomaticJournal(
                'pembayaran_penjualan',
                $paymentHistory->id,
                $paymentHistory->tanggal_bayar,
                $description,
                $entries,
                $setting->accounting_book_id,
                $outletId
            );

        } catch (\Exception $e) {
            Log::error('Gagal membuat jurnal pembayaran penjualan: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id_sales_invoice ?? 'unknown',
                'payment_id' => $paymentHistory->id ?? 'unknown',
                'outlet_id' => $outletId ?? 'unknown'
            ]);
            return null;
        }
    }
}