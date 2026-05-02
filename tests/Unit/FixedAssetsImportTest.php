<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Imports\FixedAssetsImport;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FixedAssetsImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that FixedAssetsImport validates required fields
     */
    public function test_fixed_assets_import_validates_required_fields(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        $row = [
            'kode_aset' => '',
            'nama_aset' => '',
            'tanggal_perolehan' => '',
            'harga_perolehan' => '',
            'umur_ekonomis' => ''
        ];

        $validation = $this->invokeMethod($import, 'validateRow', [$row, 1]);

        $this->assertFalse($validation['valid']);
        $this->assertNotEmpty($validation['errors']);
    }

    /**
     * Test that FixedAssetsImport validates numeric fields
     */
    public function test_fixed_assets_import_validates_numeric_fields(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        $row = [
            'kode_aset' => 'FA-001',
            'nama_aset' => 'Test Asset',
            'tanggal_perolehan' => '2024-01-01',
            'harga_perolehan' => 'invalid',
            'umur_ekonomis' => 'invalid'
        ];

        $validation = $this->invokeMethod($import, 'validateRow', [$row, 1]);

        $this->assertFalse($validation['valid']);
        $this->assertContains('Baris 1: Harga Perolehan harus berupa angka', $validation['errors']);
        $this->assertContains('Baris 1: Umur Ekonomis harus berupa angka', $validation['errors']);
    }

    /**
     * Test that FixedAssetsImport validates date format
     */
    public function test_fixed_assets_import_validates_date_format(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        // Valid date formats
        $validDates = ['2024-01-15', '15/01/2024', '15-01-2024'];
        foreach ($validDates as $date) {
            $result = $this->invokeMethod($import, 'isValidDate', [$date]);
            $this->assertTrue($result, "Date {$date} should be valid");
        }

        // Invalid date format
        $invalidDate = 'invalid-date';
        $result = $this->invokeMethod($import, 'isValidDate', [$invalidDate]);
        $this->assertFalse($result);
    }

    /**
     * Test that FixedAssetsImport validates category
     */
    public function test_fixed_assets_import_validates_category(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        // Valid categories
        $validCategories = ['building', 'vehicle', 'equipment', 'furniture', 'electronics', 'computer', 'land', 'other'];
        foreach ($validCategories as $category) {
            $result = $this->invokeMethod($import, 'isValidCategory', [$category]);
            $this->assertTrue($result, "Category {$category} should be valid");
        }

        // Valid Indonesian categories
        $validIndonesianCategories = ['bangunan', 'kendaraan', 'peralatan', 'elektronik', 'komputer', 'tanah', 'lainnya'];
        foreach ($validIndonesianCategories as $category) {
            $result = $this->invokeMethod($import, 'isValidCategory', [$category]);
            $this->assertTrue($result, "Category {$category} should be valid");
        }

        // Invalid category
        $invalidCategory = 'invalid_category';
        $result = $this->invokeMethod($import, 'isValidCategory', [$invalidCategory]);
        $this->assertFalse($result);
    }

    /**
     * Test that FixedAssetsImport validates depreciation method
     */
    public function test_fixed_assets_import_validates_depreciation_method(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        // Valid methods
        $validMethods = ['straight_line', 'declining_balance', 'double_declining', 'units_of_production'];
        foreach ($validMethods as $method) {
            $result = $this->invokeMethod($import, 'isValidDepreciationMethod', [$method]);
            $this->assertTrue($result, "Method {$method} should be valid");
        }

        // Valid Indonesian methods
        $validIndonesianMethods = ['garis lurus', 'saldo menurun', 'saldo menurun ganda', 'unit produksi'];
        foreach ($validIndonesianMethods as $method) {
            $result = $this->invokeMethod($import, 'isValidDepreciationMethod', [$method]);
            $this->assertTrue($result, "Method {$method} should be valid");
        }

        // Invalid method
        $invalidMethod = 'invalid_method';
        $result = $this->invokeMethod($import, 'isValidDepreciationMethod', [$invalidMethod]);
        $this->assertFalse($result);
    }

    /**
     * Test that FixedAssetsImport parses category correctly
     */
    public function test_fixed_assets_import_parses_category(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        $testCases = [
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
            'lainnya' => 'other',
            'other' => 'other',
        ];

        foreach ($testCases as $input => $expected) {
            $result = $this->invokeMethod($import, 'parseCategory', [$input]);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * Test that FixedAssetsImport parses depreciation method correctly
     */
    public function test_fixed_assets_import_parses_depreciation_method(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        $testCases = [
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

        foreach ($testCases as $input => $expected) {
            $result = $this->invokeMethod($import, 'parseDepreciationMethod', [$input]);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * Test that FixedAssetsImport parses dates correctly
     */
    public function test_fixed_assets_import_parses_dates(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        $testCases = [
            '2024-01-15' => '2024-01-15',
            '15/01/2024' => '2024-01-15',
            '15-01-2024' => '2024-01-15',
        ];

        foreach ($testCases as $input => $expected) {
            $result = $this->invokeMethod($import, 'parseDate', [$input]);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * Test that FixedAssetsImport tracks imported count
     */
    public function test_fixed_assets_import_tracks_imported_count(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        $this->assertEquals(0, $import->getImportedCount());
        $this->assertEquals(0, $import->getSkippedCount());
        $this->assertEmpty($import->getErrors());
    }

    /**
     * Test that FixedAssetsImport generates result message
     */
    public function test_fixed_assets_import_generates_result_message(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        $message = $import->getResultMessage();

        $this->assertStringContainsString('Berhasil mengimpor', $message);
        $this->assertStringContainsString('aset tetap', $message);
    }

    /**
     * Test that FixedAssetsImport handles both Indonesian and English field names
     */
    public function test_fixed_assets_import_handles_bilingual_fields(): void
    {
        $import = new FixedAssetsImport(['outlet_id' => 1]);

        // Test with Indonesian field names
        $rowIndonesian = [
            'kode_aset' => 'FA-001',
            'nama_aset' => 'Test Asset',
            'tanggal_perolehan' => '2024-01-01',
            'harga_perolehan' => 1000000,
            'umur_ekonomis' => 5
        ];

        $validationIndonesian = $this->invokeMethod($import, 'validateRow', [$rowIndonesian, 1]);
        $this->assertTrue($validationIndonesian['valid']);

        // Test with English field names
        $rowEnglish = [
            'code' => 'FA-001',
            'name' => 'Test Asset',
            'acquisition_date' => '2024-01-01',
            'acquisition_cost' => 1000000,
            'useful_life' => 5
        ];

        $validationEnglish = $this->invokeMethod($import, 'validateRow', [$rowEnglish, 1]);
        $this->assertTrue($validationEnglish['valid']);
    }

    /**
     * Helper method to invoke private/protected methods
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Helper method to get private/protected properties
     */
    protected function getProperty(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
