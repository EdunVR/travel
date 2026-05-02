<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Exports\GeneralLedgerExport;
use Illuminate\Support\Collection;

class GeneralLedgerExportTest extends TestCase
{
    /**
     * Test that GeneralLedgerExport returns correct collection
     */
    public function test_general_ledger_export_returns_collection(): void
    {
        $data = [
            'ledger_entries' => [
                [
                    'account_code' => '1010',
                    'account_name' => 'Kas',
                    'opening_balance' => 1000000,
                    'transactions' => [
                        [
                            'date' => '2024-01-15',
                            'reference' => 'JRN-001',
                            'description' => 'Test transaction',
                            'debit' => 500000,
                            'credit' => 0,
                            'balance' => 1500000
                        ]
                    ],
                    'total_debit' => 500000,
                    'total_credit' => 0,
                    'ending_balance' => 1500000
                ]
            ],
            'summary' => [
                'total_debit' => 500000,
                'total_credit' => 0,
                'balance' => 1500000
            ]
        ];

        $export = new GeneralLedgerExport($data);
        $collection = $export->collection();

        $this->assertInstanceOf(Collection::class, $collection);
        // Should have: opening balance + 1 transaction + account total + spacer + grand total = 5 rows
        $this->assertCount(5, $collection);
    }

    /**
     * Test that GeneralLedgerExport has correct headings
     */
    public function test_general_ledger_export_has_correct_headings(): void
    {
        $export = new GeneralLedgerExport([]);
        $headings = $export->headings();

        $expectedHeadings = [
            'Kode Akun',
            'Nama Akun',
            'Tanggal',
            'Referensi',
            'Keterangan',
            'Debit',
            'Kredit',
            'Saldo'
        ];

        $this->assertEquals($expectedHeadings, $headings);
    }

    /**
     * Test that GeneralLedgerExport maps data correctly
     */
    public function test_general_ledger_export_maps_data_correctly(): void
    {
        $row = (object)[
            'account_code' => '1010',
            'account_name' => 'Kas',
            'date' => '2024-01-15',
            'reference' => 'JRN-001',
            'description' => 'Test transaction',
            'debit' => 500000,
            'credit' => 0,
            'balance' => 1500000
        ];

        $export = new GeneralLedgerExport([]);
        $mapped = $export->map($row);

        $this->assertEquals('1010', $mapped[0]);
        $this->assertEquals('Kas', $mapped[1]);
        $this->assertEquals('15/01/2024', $mapped[2]);
        $this->assertEquals('JRN-001', $mapped[3]);
        $this->assertEquals('Test transaction', $mapped[4]);
        $this->assertEquals(500000, $mapped[5]);
        $this->assertEquals(0, $mapped[6]);
        $this->assertEquals(1500000, $mapped[7]);
    }

    /**
     * Test that GeneralLedgerExport flattens nested structure correctly
     */
    public function test_general_ledger_export_flattens_structure(): void
    {
        $data = [
            'ledger_entries' => [
                [
                    'account_code' => '1010',
                    'account_name' => 'Kas',
                    'opening_balance' => 1000000,
                    'transactions' => [
                        [
                            'date' => '2024-01-15',
                            'reference' => 'JRN-001',
                            'description' => 'Transaction 1',
                            'debit' => 500000,
                            'credit' => 0,
                            'balance' => 1500000
                        ],
                        [
                            'date' => '2024-01-16',
                            'reference' => 'JRN-002',
                            'description' => 'Transaction 2',
                            'debit' => 0,
                            'credit' => 200000,
                            'balance' => 1300000
                        ]
                    ],
                    'total_debit' => 500000,
                    'total_credit' => 200000,
                    'ending_balance' => 1300000
                ]
            ],
            'summary' => [
                'total_debit' => 500000,
                'total_credit' => 200000,
                'balance' => 1300000
            ]
        ];

        $export = new GeneralLedgerExport($data);
        $collection = $export->collection();

        // Should have: opening balance + 2 transactions + account total + spacer + grand total = 6 rows
        $this->assertCount(6, $collection);

        // Check first row is opening balance
        $firstRow = $collection->first();
        $this->assertEquals('opening_balance', $firstRow->type);
        $this->assertEquals('SALDO-AWAL', $firstRow->reference);

        // Check last row is grand total
        $lastRow = $collection->last();
        $this->assertEquals('grand_total', $lastRow->type);
        $this->assertEquals('TOTAL BUKU BESAR', $lastRow->description);
    }

    /**
     * Test opening balance row formatting
     */
    public function test_general_ledger_export_opening_balance_formatting(): void
    {
        $data = [
            'ledger_entries' => [
                [
                    'account_code' => '1010',
                    'account_name' => 'Kas',
                    'opening_balance' => 1000000,
                    'transactions' => [],
                    'total_debit' => 0,
                    'total_credit' => 0,
                    'ending_balance' => 1000000
                ]
            ]
        ];

        $export = new GeneralLedgerExport($data, ['start_date' => '2024-01-01']);
        $collection = $export->collection();

        $openingBalanceRow = $collection->first();
        $this->assertEquals('opening_balance', $openingBalanceRow->type);
        $this->assertEquals('1010', $openingBalanceRow->account_code);
        $this->assertEquals('Kas', $openingBalanceRow->account_name);
        $this->assertEquals('SALDO-AWAL', $openingBalanceRow->reference);
        $this->assertEquals('Saldo Awal Periode', $openingBalanceRow->description);
        $this->assertEquals(1000000, $openingBalanceRow->balance);
    }

    /**
     * Test negative opening balance formatting
     */
    public function test_general_ledger_export_negative_opening_balance(): void
    {
        $data = [
            'ledger_entries' => [
                [
                    'account_code' => '2010',
                    'account_name' => 'Hutang',
                    'opening_balance' => -500000,
                    'transactions' => [],
                    'total_debit' => 0,
                    'total_credit' => 0,
                    'ending_balance' => -500000
                ]
            ]
        ];

        $export = new GeneralLedgerExport($data);
        $collection = $export->collection();

        $openingBalanceRow = $collection->first();
        $this->assertEquals(0, $openingBalanceRow->debit);
        $this->assertEquals(500000, $openingBalanceRow->credit);
        $this->assertEquals(-500000, $openingBalanceRow->balance);
    }

    /**
     * Test that GeneralLedgerExport handles empty data
     */
    public function test_general_ledger_export_handles_empty_data(): void
    {
        $export = new GeneralLedgerExport([]);
        $collection = $export->collection();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(0, $collection);
    }

    /**
     * Test multiple accounts in ledger
     */
    public function test_general_ledger_export_multiple_accounts(): void
    {
        $data = [
            'ledger_entries' => [
                [
                    'account_code' => '1010',
                    'account_name' => 'Kas',
                    'opening_balance' => 1000000,
                    'transactions' => [
                        [
                            'date' => '2024-01-15',
                            'reference' => 'JRN-001',
                            'description' => 'Transaction 1',
                            'debit' => 500000,
                            'credit' => 0,
                            'balance' => 1500000
                        ]
                    ],
                    'total_debit' => 500000,
                    'total_credit' => 0,
                    'ending_balance' => 1500000
                ],
                [
                    'account_code' => '2010',
                    'account_name' => 'Hutang',
                    'opening_balance' => -500000,
                    'transactions' => [
                        [
                            'date' => '2024-01-15',
                            'reference' => 'JRN-001',
                            'description' => 'Transaction 1',
                            'debit' => 0,
                            'credit' => 500000,
                            'balance' => -1000000
                        ]
                    ],
                    'total_debit' => 0,
                    'total_credit' => 500000,
                    'ending_balance' => -1000000
                ]
            ],
            'summary' => [
                'total_debit' => 500000,
                'total_credit' => 500000,
                'balance' => 0
            ]
        ];

        $export = new GeneralLedgerExport($data);
        $collection = $export->collection();

        // Each account: opening + 1 transaction + total + spacer = 4 rows per account
        // Plus grand total = 4 + 4 + 1 = 9 rows
        $this->assertCount(9, $collection);
    }
}
