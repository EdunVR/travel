<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Finance Import Service
 * 
 * Handles import functionality for finance modules including Journal List
 * and Fixed Assets. Validates uploaded files, processes data in batches,
 * and provides detailed error reporting for failed imports.
 * 
 * Features:
 * - File validation (type, size, format)
 * - Row-by-row validation with detailed error messages
 * - Transaction support for data integrity
 * - Template download for correct format guidance
 * 
 * @package App\Services
 * @author ERP System
 * @version 1.0.0
 */
class FinanceImportService
{
    /**
     * Import data from uploaded file
     *
     * @param string $module The module name (journal, fixed-assets)
     * @param \Illuminate\Http\UploadedFile $file The uploaded file
     * @param array $additionalData Additional data needed for import (e.g., outlet_id)
     * @return array Import result with success status, counts, and errors
     */
    public function import(string $module, $file, array $additionalData = []): array
    {
        try {
            // Validate file
            $this->validateFile($file);
            
            // Get import class
            $importClass = $this->getImportClass($module);
            
            // Create import instance
            $import = new $importClass($additionalData);
            
            // Execute import
            Excel::import($import, $file);
            
            return [
                'success' => true,
                'imported_count' => $import->getImportedCount(),
                'skipped_count' => $import->getSkippedCount(),
                'errors' => $import->getErrors(),
                'message' => "Berhasil mengimpor {$import->getImportedCount()} data"
            ];
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            return [
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $errors
            ];
        } catch (\Exception $e) {
            Log::error("Import error for module {$module}: " . $e->getMessage(), [
                'exception' => $e,
                'module' => $module
            ]);
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'errors' => []
            ];
        }
    }

    /**
     * Validate uploaded file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateFile($file): void
    {
        $validator = Validator::make(
            ['file' => $file],
            [
                'file' => 'required|file|mimes:xlsx,xls,csv|max:5120' // Max 5MB
            ],
            [
                'file.required' => 'File harus diupload',
                'file.mimes' => 'File harus berformat Excel (xlsx, xls) atau CSV',
                'file.max' => 'Ukuran file maksimal 5MB'
            ]
        );

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    /**
     * Get the import class for a given module
     *
     * @param string $module
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getImportClass(string $module): string
    {
        return match($module) {
            'journal' => \App\Imports\JournalImport::class,
            'fixed-assets' => \App\Imports\FixedAssetsImport::class,
            default => throw new \InvalidArgumentException("Import not supported for module: {$module}")
        };
    }

    /**
     * Generate a sample template for import
     *
     * @param string $module
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate(string $module)
    {
        $templateClass = $this->getTemplateClass($module);
        $filename = "{$module}_template.xlsx";
        
        return Excel::download(new $templateClass(), $filename);
    }

    /**
     * Get the template class for a given module
     *
     * @param string $module
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getTemplateClass(string $module): string
    {
        return match($module) {
            'journal' => \App\Exports\Templates\JournalTemplateExport::class,
            'fixed-assets' => \App\Exports\Templates\FixedAssetsTemplateExport::class,
            default => throw new \InvalidArgumentException("Template not available for module: {$module}")
        };
    }
}
