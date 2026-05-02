@echo off
echo ============================================================
echo DEPLOYING: PDF Header and Logo Integration
echo ============================================================
echo.

echo CHANGES BEING DEPLOYED:
echo 1. Bulk production PDF header updated to match QC Tofu format
echo 2. Logo integration from company settings
echo 3. Professional document header with 3 sections
echo.

echo ============================================================
echo Step 1: Testing Header and Logo
echo ============================================================
php test_pdf_header_logo.php
if errorlevel 1 (
    echo ERROR: Test failed!
    pause
    exit /b 1
)
echo.

echo ============================================================
echo Step 2: Clear View Cache
echo ============================================================
php artisan view:clear
echo View cache cleared successfully!
echo.

echo ============================================================
echo DEPLOYMENT SUMMARY
echo ============================================================
echo.
echo FILES MODIFIED:
echo 1. app/Http/Controllers/ProductionController.php
echo    - exportBulkProductionPdf() method
echo    - Added company settings retrieval
echo    - Pass companyName and companyLogo to view
echo.
echo 2. resources/views/admin/produksi/produksi/bulk-production-pdf.blade.php
echo    - Complete header redesign
echo    - 3-section header: Logo ^| Company Info ^| Document Info
echo    - Logo displays from company_settings
echo    - Fallback to placeholder if logo not available
echo    - Uses Times New Roman font (professional)
echo.
echo 3. resources/views/admin/produksi/produksi/qc-tofu-mentah-pdf.blade.php
echo    - Logo already integrated (no changes needed)
echo    - Uses same header format
echo.

echo ============================================================
echo HEADER STRUCTURE
echo ============================================================
echo.
echo ┌──────────────────────────────────────────────────────────┐
echo │         │                              │                  │
echo │  LOGO   │     COMPANY NAME             │  Doc No: LP-001  │
echo │         │  LAPORAN PRODUKSI BULK       │  Rev: 00         │
echo │         │                              │  Date: xx/xx/xx  │
echo │         │                              │  Page: 1 of 1    │
echo └──────────────────────────────────────────────────────────┘
echo.

echo ============================================================
echo LOGO CONFIGURATION
echo ============================================================
echo.
echo Logo Source: company_settings.company_logo
echo Logo Path: public/storage/company/logos/[filename]
echo Fallback: "LOGO" placeholder if file not found
echo.
echo To upload logo:
echo 1. Go to Company Settings
echo 2. Upload logo image
echo 3. Logo will automatically appear in all PDFs
echo.

echo ============================================================
echo TESTING INSTRUCTIONS
echo ============================================================
echo.
echo 1. Open browser and navigate to Production page
echo 2. Click "Export PDF" -^> "Laporan Produksi"
echo 3. Verify PDF header has 3 sections:
echo    - Left: Company logo (or LOGO placeholder)
echo    - Center: Company name and document title
echo    - Right: Document info (number, revision, date, page)
echo.
echo 4. Click "Export PDF" -^> "QC Egg Tofu Mentah"
echo 5. Verify logo also displays in QC PDF
echo.
echo 6. If logo shows as placeholder:
echo    - Check if logo file exists in storage
echo    - Run: php artisan storage:link
echo    - Upload logo via Company Settings
echo.

echo ============================================================
echo DEPLOYMENT COMPLETED SUCCESSFULLY!
echo ============================================================
echo.
echo Both PDFs now have professional headers with company logo.
echo Please test the PDF exports in the browser.
echo.
pause
