<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Imports\JournalImport;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JournalImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that JournalImport validates required fields
     */
    public function test_journal_import_validates_required_fields(): void
    {
        $import = new JournalImport(['outlet_id' => 1, 'book_id' => 1]);

        // Test with missing required fields
        $row = [
            'tanggal' => '',
            'no_transaksi' => '',
            'kode_akun' => '',
            'debit' => 0,
            'kredit' => 0
        ];

        $validation = $this->invokeMethod($import, 'validateRow', [$row, 1]);

        $this->assertFalse($validation['valid']);
        $this->assertNotEmpty($validation['errors']);
    }

    /**
     * Test that JournalImport validates debit or credit is filled
     */
    public function test_journal_import_validates_debit_or_credit(): void
    {
        $import = new JournalImport(['outlet_id' => 1, 'book_id' => 1]);

        $row = [
            'tanggal' => '2024-01-15',
            'no_transaksi' => 'JRN-001',
            'kode_akun' => '1010',
            'debit' => 0,
            'kredit' => 0
        ];

        $validation = $this->invokeMethod($import, 'validateRow', [$row, 1]);

        $this->assertFalse($validation['valid']);
        $this->assertContains('Baris 1: Debit atau Kredit harus diisi', $validation['errors']);
    }

    /**
     * Test that JournalImport validates numeric values
     */
    public function test_journal_import_validates_numeric_values(): void
    {
        $import = new JournalImport(['outlet_id' => 1, 'book_id' => 1]);

        $row = [
            'tanggal' => '2024-01-15',
            'no_transaksi' => 'JRN-001',
            'kode_akun' => '1010',
            'debit' => 'invalid',
            'kredit' => 0
        ];

        $validation = $this->invokeMethod($import, 'validateRow', [$row, 1]);

        $this->assertFalse($validation['valid']);
        $this->assertContains('Baris 1: Debit harus berupa angka', $validation['errors']);
    }

    /**
     * Test that JournalImport validates date format
     */
    public function test_journal_import_validates_date_format(): void
    {
        $import = new JournalImport(['outlet_id' => 1, 'book_id' => 1]);

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
     * Test that JournalImport parses dates correctly
     */
    public function test_journal_import_parses_dates(): void
    {
        $import = new JournalImport(['outlet_id' => 1, 'book_id' => 1]);

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
     * Test that JournalImport groups entries by transaction number
     */
    public function test_journal_import_groups_by_transaction_number(): void
    {
        $import = new JournalImport(['outlet_id' => 1, 'book_id' => 1]);

        $rows = collect([
            [
                'tanggal' => '2024-01-15',
                'no_transaksi' => 'JRN-001',
                'kode_akun' => '1010',
                'debit' => 100000,
                'kredit' => 0
            ],
            [
                'tanggal' => '2024-01-15',
                'no_transaksi' => 'JRN-001',
                'kode_akun' => '5010',
                'debit' => 0,
                'kredit' => 100000
            ]
        ]);

        $this->invokeMethod($import, 'groupJournalEntries', [$rows]);
        $groups = $this->getProperty($import, 'journalGroups');

        $this->assertCount(1, $groups);
        $this->assertArrayHasKey('JRN-001', $groups);
        $this->assertCount(2, $groups['JRN-001']['details']);
    }

    /**
     * Test that JournalImport tracks imported count
     */
    public function test_journal_import_tracks_imported_count(): void
    {
        $import = new JournalImport(['outlet_id' => 1, 'book_id' => 1]);

        $this->assertEquals(0, $import->getImportedCount());
        $this->assertEquals(0, $import->getSkippedCount());
        $this->assertEmpty($import->getErrors());
    }

    /**
     * Test that JournalImport generates result message
     */
    public function test_journal_import_generates_result_message(): void
    {
        $import = new JournalImport(['outlet_id' => 1, 'book_id' => 1]);

        $message = $import->getResultMessage();

        $this->assertStringContainsString('Berhasil mengimpor', $message);
        $this->assertStringContainsString('detail jurnal', $message);
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
