<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Exports\JournalExport;
use Illuminate\Support\Collection;

class JournalExportTest extends TestCase
{
    /**
     * Test that JournalExport returns correct collection
     */
    public function test_journal_export_returns_collection(): void
    {
        $data = [
            (object)[
                'transaction_date' => '2024-01-15',
                'transaction_number' => 'JRN-001',
                'account_code' => '1010',
                'account_name' => 'Kas',
                'description' => 'Test transaction',
                'debit' => 100000,
                'credit' => 0,
                'outlet_name' => 'Outlet A',
                'book_name' => 'General',
                'status' => 'posted'
            ]
        ];

        $export = new JournalExport($data);
        $collection = $export->collection();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(1, $collection);
    }

    /**
     * Test that JournalExport has correct headings
     */
    public function test_journal_export_has_correct_headings(): void
    {
        $export = new JournalExport([]);
        $headings = $export->headings();

        $expectedHeadings = [
            'Tanggal',
            'No. Transaksi',
            'Kode Akun',
            'Nama Akun',
            'Deskripsi',
            'Debit',
            'Kredit',
            'Outlet',
            'Buku',
            'Status'
        ];

        $this->assertEquals($expectedHeadings, $headings);
    }

    /**
     * Test that JournalExport maps data correctly
     */
    public function test_journal_export_maps_data_correctly(): void
    {
        $row = (object)[
            'transaction_date' => '2024-01-15',
            'transaction_number' => 'JRN-001',
            'account_code' => '1010',
            'account_name' => 'Kas',
            'description' => 'Test transaction',
            'debit' => 100000,
            'credit' => 0,
            'outlet_name' => 'Outlet A',
            'book_name' => 'General',
            'status' => 'posted'
        ];

        $export = new JournalExport([]);
        $mapped = $export->map($row);

        $this->assertEquals('2024-01-15', $mapped[0]);
        $this->assertEquals('JRN-001', $mapped[1]);
        $this->assertEquals('1010', $mapped[2]);
        $this->assertEquals('Kas', $mapped[3]);
        $this->assertEquals('Test transaction', $mapped[4]);
        $this->assertEquals(100000, $mapped[5]);
        $this->assertEquals(0, $mapped[6]);
        $this->assertEquals('Outlet A', $mapped[7]);
        $this->assertEquals('General', $mapped[8]);
        $this->assertEquals('Diposting', $mapped[9]);
    }

    /**
     * Test status name mapping
     */
    public function test_journal_export_status_mapping(): void
    {
        $testCases = [
            ['status' => 'draft', 'expected' => 'Draft'],
            ['status' => 'posted', 'expected' => 'Diposting'],
            ['status' => 'void', 'expected' => 'Dibatalkan'],
        ];

        foreach ($testCases as $testCase) {
            $row = (object)[
                'transaction_date' => '2024-01-15',
                'transaction_number' => 'JRN-001',
                'account_code' => '1010',
                'account_name' => 'Kas',
                'description' => 'Test',
                'debit' => 0,
                'credit' => 0,
                'outlet_name' => 'Outlet A',
                'book_name' => 'General',
                'status' => $testCase['status']
            ];

            $export = new JournalExport([]);
            $mapped = $export->map($row);

            $this->assertEquals($testCase['expected'], $mapped[9]);
        }
    }

    /**
     * Test that JournalExport handles empty data
     */
    public function test_journal_export_handles_empty_data(): void
    {
        $export = new JournalExport([]);
        $collection = $export->collection();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(0, $collection);
    }

    /**
     * Test that JournalExport handles null values
     */
    public function test_journal_export_handles_null_values(): void
    {
        $row = (object)[
            'transaction_date' => null,
            'transaction_number' => null,
            'account_code' => null,
            'account_name' => null,
            'description' => null,
            'debit' => null,
            'credit' => null,
            'outlet_name' => null,
            'book_name' => null,
            'status' => null
        ];

        $export = new JournalExport([]);
        $mapped = $export->map($row);

        $this->assertEquals('', $mapped[0]);
        $this->assertEquals('', $mapped[1]);
        $this->assertEquals('', $mapped[2]);
        $this->assertEquals('', $mapped[3]);
        $this->assertEquals('', $mapped[4]);
        $this->assertEquals(0, $mapped[5]);
        $this->assertEquals(0, $mapped[6]);
        $this->assertEquals('', $mapped[7]);
        $this->assertEquals('', $mapped[8]);
        $this->assertEquals('Draft', $mapped[9]); // Default status
    }
}
