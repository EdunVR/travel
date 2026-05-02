<?php

namespace App\Imports;

use App\Models\FixedAsset;
use App\Models\ChartOfAccount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

/**
 * Fixed Assets Import Class
 * 
 * Imports fixed asset data from Excel/CSV files with comprehensive validation
 * and business rule enforcement. Supports both Indonesian and English column names.
 * 
 * Required columns:
 * - kode_aset/code: Unique asset code
 * - nama_aset/name: Asset name/description
 * - tanggal_perolehan/acquisition_date: Date in YYYY-MM-DD or DD/MM/YYYY format
 * - harga_perolehan/acquisition_cost: Acquisition cost (must be > 0)
 * - umur_ekonomis/useful_life: Useful life in years (1-50)
 * 
 * Optional columns:
 * - kategori/category: Asset category (building, vehicle, equipment, etc.)
 * - lokasi/location: Physical location
 * - nilai_residu/salvage_value: Salvage value (must be < acquisition cost)
 * - metode_penyusutan/depreciation_method: Depreciation method
 * - akumulasi_penyusutan/accumulated_depreciation: Current accumulated depreciation
 * - kode_akun_aset/asset_account_code: Asset account code
 * - kode_akun_beban/depreciation_expense_account_code: Expense account code
 * - kode_akun_akumulasi/accumulated_depreciation_account_code: Accumulated depreciation account code
 * 
 * Validation rules:
 * - Asset code must be unique per outlet
 * - Acquisition cost must be greater than 0
 * - Salvage value must be less than acquisition cost
 * - Useful life must be between 1 and 50 years
 * - Category must be valid (if provided)
 * - Depreciation method must be valid (if provided)
 * - Account codes must exist in Chart of Accounts (if provided)
 * 
 * @package App\Imports
 * @author ERP System
 * @version 1.0.0
 */
class FixedAssetsImport implements ToCollection, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    protected $outletId;
    protected $bookId;
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $errors = [];

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
        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because of header row and 0-based index
                $this->processAsset($row, $rowNumber);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = "Transaction failed: " . $e->getMessage();
        }
    }

    /**
     * Process a single asset row
     */
    protected function processAsset($row, $rowNumber)
    {
        try {
            // Validate required fields
            $validation = $this->validateRow($row, $rowNumber);
            if (!$validation['valid']) {
                $this->skippedCount++;
                return;
            }

            // Parse and normalize data
            $code = $row['kode_aset'] ?? $row['code'] ?? null;
            $name = $row['nama_aset'] ?? $row['name'] ?? null;
            $category = $this->parseCategory($row['kategori'] ?? $row['category'] ?? 'equipment');
            $location = $row['lokasi'] ?? $row['location'] ?? '';
            $acquisitionDate = $this->parseDate($row['tanggal_perolehan'] ?? $row['acquisition_date'] ?? now());
            $acquisitionCost = floatval($row['harga_perolehan'] ?? $row['acquisition_cost'] ?? 0);
            $salvageValue = floatval($row['nilai_residu'] ?? $row['salvage_value'] ?? 0);
            $usefulLife = intval($row['umur_ekonomis'] ?? $row['useful_life'] ?? 0);
            $depreciationMethod = $this->parseDepreciationMethod($row['metode_penyusutan'] ?? $row['depreciation_method'] ?? 'straight_line');

            // Validate business rules
            if ($acquisitionCost <= 0) {
                $this->errors[] = "Baris {$rowNumber}: Harga perolehan harus lebih besar dari 0";
                $this->skippedCount++;
                return;
            }

            if ($salvageValue >= $acquisitionCost) {
                $this->errors[] = "Baris {$rowNumber}: Nilai residu harus lebih kecil dari harga perolehan";
                $this->skippedCount++;
                return;
            }

            if ($usefulLife < 1) {
                $this->errors[] = "Baris {$rowNumber}: Umur ekonomis minimal 1 tahun";
                $this->skippedCount++;
                return;
            }

            // Check if asset code already exists
            $existingAsset = FixedAsset::where('outlet_id', $this->outletId)
                ->where('code', $code)
                ->first();

            if ($existingAsset) {
                $this->errors[] = "Baris {$rowNumber}: Kode aset {$code} sudah ada, dilewati";
                $this->skippedCount++;
                return;
            }

            // Get account IDs (optional in import, can be set later)
            $assetAccountId = $this->getAccountId($row['kode_akun_aset'] ?? $row['asset_account_code'] ?? null);
            $depreciationExpenseAccountId = $this->getAccountId($row['kode_akun_beban'] ?? $row['depreciation_expense_account_code'] ?? null);
            $accumulatedDepreciationAccountId = $this->getAccountId($row['kode_akun_akumulasi'] ?? $row['accumulated_depreciation_account_code'] ?? null);
            $paymentAccountId = $this->getAccountId($row['kode_akun_pembayaran'] ?? $row['payment_account_code'] ?? null);

            // Calculate initial book value
            $bookValue = $acquisitionCost;
            $accumulatedDepreciation = 0;

            // If accumulated depreciation is provided in import, use it
            if (isset($row['akumulasi_penyusutan']) || isset($row['accumulated_depreciation'])) {
                $accumulatedDepreciation = floatval($row['akumulasi_penyusutan'] ?? $row['accumulated_depreciation'] ?? 0);
                $bookValue = $acquisitionCost - $accumulatedDepreciation;
            }

            // Create fixed asset with draft status
            FixedAsset::create([
                'outlet_id' => $this->outletId,
                'book_id' => $this->bookId,
                'code' => $code,
                'name' => $name,
                'category' => $category,
                'location' => $location,
                'acquisition_date' => $acquisitionDate,
                'acquisition_cost' => $acquisitionCost,
                'salvage_value' => $salvageValue,
                'useful_life' => $usefulLife,
                'depreciation_method' => $depreciationMethod,
                'asset_account_id' => $assetAccountId,
                'depreciation_expense_account_id' => $depreciationExpenseAccountId,
                'accumulated_depreciation_account_id' => $accumulatedDepreciationAccountId,
                'payment_account_id' => $paymentAccountId,
                'accumulated_depreciation' => $accumulatedDepreciation,
                'book_value' => $bookValue,
                'status' => 'draft', // Set as draft for approval workflow
                'description' => $row['deskripsi'] ?? $row['description'] ?? '',
                'created_by' => auth()->id()
            ]);

            $this->importedCount++;

        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
            $this->skippedCount++;
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
            'kode_aset' => 'Kode Aset',
            'nama_aset' => 'Nama Aset',
            'tanggal_perolehan' => 'Tanggal Perolehan',
            'harga_perolehan' => 'Harga Perolehan',
            'umur_ekonomis' => 'Umur Ekonomis',
        ];

        foreach ($requiredFields as $field => $label) {
            // Try both Indonesian and English field names
            $altField = [
                'kode_aset' => 'code',
                'nama_aset' => 'name',
                'tanggal_perolehan' => 'acquisition_date',
                'harga_perolehan' => 'acquisition_cost',
                'umur_ekonomis' => 'useful_life',
            ][$field] ?? $field;

            $value = $row[$field] ?? $row[$altField] ?? null;
            if (empty($value)) {
                $errors[] = "Baris {$rowNumber}: {$label} tidak boleh kosong";
            }
        }

        // Validate numeric fields
        $numericFields = [
            'harga_perolehan' => 'Harga Perolehan',
            'nilai_residu' => 'Nilai Residu',
            'umur_ekonomis' => 'Umur Ekonomis',
        ];

        foreach ($numericFields as $field => $label) {
            $altField = [
                'harga_perolehan' => 'acquisition_cost',
                'nilai_residu' => 'salvage_value',
                'umur_ekonomis' => 'useful_life',
            ][$field] ?? $field;

            $value = $row[$field] ?? $row[$altField] ?? null;
            if (!empty($value) && !is_numeric($value)) {
                $errors[] = "Baris {$rowNumber}: {$label} harus berupa angka";
            }
        }

        // Validate date format
        $date = $row['tanggal_perolehan'] ?? $row['acquisition_date'] ?? null;
        if ($date && !$this->isValidDate($date)) {
            $errors[] = "Baris {$rowNumber}: Format tanggal tidak valid (gunakan YYYY-MM-DD atau DD/MM/YYYY)";
        }

        // Validate category
        $category = $row['kategori'] ?? $row['category'] ?? null;
        if ($category && !$this->isValidCategory($category)) {
            $errors[] = "Baris {$rowNumber}: Kategori tidak valid";
        }

        // Validate depreciation method
        $method = $row['metode_penyusutan'] ?? $row['depreciation_method'] ?? null;
        if ($method && !$this->isValidDepreciationMethod($method)) {
            $errors[] = "Baris {$rowNumber}: Metode penyusutan tidak valid";
        }

        if (!empty($errors)) {
            $this->errors = array_merge($this->errors, $errors);
            return ['valid' => false, 'errors' => $errors];
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Parse category from various formats
     */
    protected function parseCategory($category): string
    {
        $categoryMap = [
            'bangunan' => 'building',
            'building' => 'building',
            'kendaraan' => 'vehicle',
            'vehicle' => 'vehicle',
            'peralatan' => 'equipment',
            'equipment' => 'equipment',
            'furniture' => 'furniture',
            'elektronik' => 'electronics',
            'electronics' => 'electronics',
            'komputer' => 'computer',
            'komputer & it' => 'computer',
            'computer' => 'computer',
            'tanah' => 'land',
            'land' => 'land',
            'mesin' => 'mesin',
            'machine' => 'mesin',
            'machinery' => 'mesin',
            'lainnya' => 'other',
            'other' => 'other',
        ];

        $normalized = strtolower(trim($category));
        return $categoryMap[$normalized] ?? 'equipment';
    }

    /**
     * Parse depreciation method from various formats
     */
    protected function parseDepreciationMethod($method): string
    {
        $methodMap = [
            'garis lurus' => 'straight_line',
            'straight_line' => 'straight_line',
            'straight line' => 'straight_line',
            'saldo menurun' => 'declining_balance',
            'declining_balance' => 'declining_balance',
            'declining balance' => 'declining_balance',
            'saldo menurun ganda' => 'double_declining',
            'saldo menurun 2x' => 'double_declining',
            'double_declining' => 'double_declining',
            'double declining' => 'double_declining',
            'unit produksi' => 'units_of_production',
            'units_of_production' => 'units_of_production',
            'units of production' => 'units_of_production',
        ];

        $normalized = strtolower(trim($method));
        return $methodMap[$normalized] ?? 'straight_line';
    }

    /**
     * Get account ID by code
     */
    protected function getAccountId($accountCode): ?int
    {
        if (empty($accountCode)) {
            return null;
        }

        $account = ChartOfAccount::where('outlet_id', $this->outletId)
            ->where('code', $accountCode)
            ->first();

        return $account ? $account->id : null;
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
     * Check if category is valid
     */
    protected function isValidCategory($category): bool
    {
        $validCategories = [
            'building', 'bangunan',
            'vehicle', 'kendaraan',
            'equipment', 'peralatan',
            'furniture',
            'electronics', 'elektronik',
            'computer', 'komputer', 'komputer & it',
            'land', 'tanah',
            'mesin', 'machine', 'machinery',
            'other', 'lainnya'
        ];

        return in_array(strtolower(trim($category)), $validCategories);
    }

    /**
     * Check if depreciation method is valid
     */
    protected function isValidDepreciationMethod($method): bool
    {
        $validMethods = [
            'straight_line', 'garis lurus', 'straight line',
            'declining_balance', 'saldo menurun', 'declining balance',
            'double_declining', 'saldo menurun ganda', 'saldo menurun 2x', 'double declining',
            'units_of_production', 'unit produksi', 'units of production'
        ];

        return in_array(strtolower(trim($method)), $validMethods);
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
        $message = "Berhasil mengimpor {$this->importedCount} aset tetap dengan status draft";
        
        if ($this->skippedCount > 0) {
            $message .= ", {$this->skippedCount} baris dilewati";
        }
        
        if (count($this->errors) > 0) {
            $message .= " dengan " . count($this->errors) . " error";
        }
        
        $message .= ". Silakan approve untuk posting jurnal.";
        
        return $message;
    }
}
