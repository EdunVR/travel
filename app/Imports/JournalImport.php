<?php

namespace App\Imports;

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\ChartOfAccount;
use App\Models\AccountingBook;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

/**
 * Journal Import Class
 * 
 * Imports journal entries from Excel/CSV files with comprehensive validation.
 * Handles multi-line journal entries (one transaction with multiple details)
 * and ensures debit/credit balance validation.
 * 
 * Required columns:
 * - tanggal (Date): Transaction date in YYYY-MM-DD or DD/MM/YYYY format
 * - no_transaksi (Transaction Number): Unique identifier for the journal entry
 * - kode_akun (Account Code): Must exist in Chart of Accounts
 * - debit: Debit amount (numeric)
 * - kredit (Credit): Credit amount (numeric)
 * 
 * Optional columns:
 * - deskripsi (Description): Transaction description
 * - keterangan (Notes): Additional notes
 * 
 * Validation rules:
 * - All required fields must be present
 * - Account codes must exist in the system
 * - Either debit or credit must be greater than 0
 * - Total debit must equal total credit for each transaction
 * - Transaction numbers must be unique
 * 
 * @package App\Imports
 * @author ERP System
 * @version 1.0.0
 */
class JournalImport implements ToCollection, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    protected $outletId;
    protected $bookId;
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $errors = [];
    protected $journalGroups = [];

    public function __construct($additionalData = [])
    {
        $this->outletId = $additionalData['outlet_id'] ?? null;
        $this->bookId = $additionalData['book_id'] ?? null;
    }

    /**
     * Process the collection of rows
     */
    public function collection(Collection $rows)
    {
        // Group rows by transaction_number to handle journal entries with multiple details
        $this->groupJournalEntries($rows);

        DB::beginTransaction();
        try {
            foreach ($this->journalGroups as $transactionNumber => $group) {
                $this->processJournalEntry($transactionNumber, $group);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = "Transaction failed: " . $e->getMessage();
        }
    }

    /**
     * Group rows by transaction number
     */
    protected function groupJournalEntries(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index
            
            // Validate required fields
            $validation = $this->validateRow($row, $rowNumber);
            if (!$validation['valid']) {
                $this->skippedCount++;
                continue;
            }

            $transactionNumber = $row['no_transaksi'] ?? $row['transaction_number'] ?? null;
            
            if (!$transactionNumber) {
                $this->errors[] = "Baris {$rowNumber}: Nomor transaksi tidak boleh kosong";
                $this->skippedCount++;
                continue;
            }

            if (!isset($this->journalGroups[$transactionNumber])) {
                $this->journalGroups[$transactionNumber] = [
                    'header' => $row,
                    'details' => []
                ];
            }

            $this->journalGroups[$transactionNumber]['details'][] = [
                'row' => $row,
                'row_number' => $rowNumber
            ];
        }
    }

    /**
     * Validate a single row
     */
    protected function validateRow($row, $rowNumber): array
    {
        $errors = [];

        // Check required fields
        $requiredFields = [
            'tanggal' => 'Tanggal',
            'no_transaksi' => 'No. Transaksi',
            'kode_akun' => 'Kode Akun',
        ];

        foreach ($requiredFields as $field => $label) {
            $value = $row[$field] ?? $row[str_replace('_', ' ', $field)] ?? null;
            if (empty($value)) {
                $errors[] = "Baris {$rowNumber}: {$label} tidak boleh kosong";
            }
        }

        // Validate at least one of debit or credit is filled
        $debit = $row['debit'] ?? 0;
        $credit = $row['kredit'] ?? $row['credit'] ?? 0;
        
        if (empty($debit) && empty($credit)) {
            $errors[] = "Baris {$rowNumber}: Debit atau Kredit harus diisi";
        }

        // Validate debit and credit are numeric
        if (!empty($debit) && !is_numeric($debit)) {
            $errors[] = "Baris {$rowNumber}: Debit harus berupa angka";
        }
        if (!empty($credit) && !is_numeric($credit)) {
            $errors[] = "Baris {$rowNumber}: Kredit harus berupa angka";
        }

        // Validate date format
        $date = $row['tanggal'] ?? $row['transaction_date'] ?? null;
        if ($date && !$this->isValidDate($date)) {
            $errors[] = "Baris {$rowNumber}: Format tanggal tidak valid (gunakan YYYY-MM-DD atau DD/MM/YYYY)";
        }

        if (!empty($errors)) {
            $this->errors = array_merge($this->errors, $errors);
            return ['valid' => false, 'errors' => $errors];
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Process a journal entry with its details
     */
    protected function processJournalEntry($transactionNumber, $group)
    {
        try {
            $header = $group['header'];
            $details = $group['details'];

            // Parse date
            $date = $this->parseDate($header['tanggal'] ?? $header['transaction_date']);
            
            // Get or create accounting book
            $bookId = $this->bookId;
            if (!$bookId) {
                $book = AccountingBook::where('outlet_id', $this->outletId)
                    ->where('type', 'general')
                    ->where('status', 'active')
                    ->first();
                
                if (!$book) {
                    throw new \Exception("Tidak ada buku akuntansi aktif untuk outlet ini");
                }
                $bookId = $book->id;
            }

            // Check if journal entry already exists
            $existingJournal = JournalEntry::where('outlet_id', $this->outletId)
                ->where('transaction_number', $transactionNumber)
                ->first();

            if ($existingJournal) {
                $this->errors[] = "Nomor transaksi {$transactionNumber} sudah ada, dilewati";
                $this->skippedCount += count($details);
                return;
            }

            // Calculate totals
            $totalDebit = 0;
            $totalCredit = 0;
            $validDetails = [];

            foreach ($details as $detail) {
                $row = $detail['row'];
                $rowNumber = $detail['row_number'];

                $accountCode = $row['kode_akun'] ?? $row['account_code'];
                $account = ChartOfAccount::where('outlet_id', $this->outletId)
                    ->where('code', $accountCode)
                    ->first();

                if (!$account) {
                    $this->errors[] = "Baris {$rowNumber}: Kode akun {$accountCode} tidak ditemukan";
                    $this->skippedCount++;
                    continue;
                }

                $debit = floatval($row['debit'] ?? 0);
                $credit = floatval($row['kredit'] ?? $row['credit'] ?? 0);

                $totalDebit += $debit;
                $totalCredit += $credit;

                $validDetails[] = [
                    'account_id' => $account->id,
                    'debit' => $debit,
                    'credit' => $credit,
                    'description' => $row['keterangan'] ?? $row['description'] ?? '',
                ];
            }

            // Validate balanced entry
            if (abs($totalDebit - $totalCredit) > 0.01) {
                $this->errors[] = "Transaksi {$transactionNumber}: Total Debit ({$totalDebit}) tidak sama dengan Total Kredit ({$totalCredit})";
                $this->skippedCount += count($details);
                return;
            }

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'book_id' => $bookId,
                'outlet_id' => $this->outletId,
                'transaction_number' => $transactionNumber,
                'transaction_date' => $date,
                'description' => $header['deskripsi'] ?? $header['description'] ?? '',
                'status' => 'draft',
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'notes' => $header['catatan'] ?? $header['notes'] ?? null,
            ]);

            // Create journal entry details
            foreach ($validDetails as $detailData) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $detailData['account_id'],
                    'debit' => $detailData['debit'],
                    'credit' => $detailData['credit'],
                    'description' => $detailData['description'],
                ]);
            }

            $this->importedCount += count($validDetails);

        } catch (\Exception $e) {
            $this->errors[] = "Transaksi {$transactionNumber}: " . $e->getMessage();
            $this->skippedCount += count($group['details']);
        }
    }

    /**
     * Parse date from various formats
     */
    protected function parseDate($dateString)
    {
        if (empty($dateString)) {
            return now()->format('Y-m-d');
        }

        // Try to parse Excel date number
        if (is_numeric($dateString)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Not an Excel date, continue
            }
        }

        // Try common date formats
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        return now()->format('Y-m-d');
    }

    /**
     * Check if date is valid
     */
    protected function isValidDate($dateString): bool
    {
        if (is_numeric($dateString)) {
            return true; // Excel date number
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle errors during import
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = "Error: " . $e->getMessage();
    }

    /**
     * Handle validation failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            $this->skippedCount++;
        }
    }

    /**
     * Get imported count
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Get skipped count
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get result message
     */
    public function getResultMessage(): string
    {
        $message = "Berhasil mengimpor {$this->importedCount} detail jurnal";
        
        if ($this->skippedCount > 0) {
            $message .= ", {$this->skippedCount} baris dilewati";
        }
        
        if (count($this->errors) > 0) {
            $message .= " dengan " . count($this->errors) . " error";
        }
        
        return $message;
    }
}
