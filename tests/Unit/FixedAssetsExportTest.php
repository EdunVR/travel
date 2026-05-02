<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Exports\FixedAssetsExport;
use Illuminate\Support\Collection;

class FixedAssetsExportTest extends TestCase
{
    /**
     * Test that FixedAssetsExport returns correct collection
     */
    public function test_fixed_assets_export_returns_collection(): void
    {
        $data = [
            (object)[
                'code' => 'FA-001',
                'name' => 'Laptop Dell',
                'category' => 'computer',
                'location' => 'Office',
                'acquisition_date' => '2024-01-01',
                'acquisition_cost' => 15000000,
                'salvage_value' => 1500000,
                'depreciation_method' => 'straight_line',
                'useful_life' => 5,
                'accumulated_depreciation' => 3000000,
                'book_value' => 12000000,
                'status' => 'active',
                'outlet' => (object)['nama_outlet' => 'Outlet A']
            ]
        ];

        $export = new FixedAssetsExport($data);
        $collection = $export->collection();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(1, $collection);
    }

    /**
     * Test that FixedAssetsExport has correct headings
     */
    public function test_fixed_assets_export_has_correct_headings(): void
    {
        $export = new FixedAssetsExport([]);
        $headings = $export->headings();

        $expectedHeadings = [
            'Kode Aset',
            'Nama Aset',
            'Kategori',
            'Lokasi',
            'Tanggal Perolehan',
            'Harga Perolehan',
            'Nilai Residu',
            'Metode Penyusutan',
            'Umur Ekonomis (Tahun)',
            'Akumulasi Penyusutan',
            'Nilai Buku',
            'Status',
            'Outlet'
        ];

        $this->assertEquals($expectedHeadings, $headings);
    }

    /**
     * Test that FixedAssetsExport maps data correctly
     */
    public function test_fixed_assets_export_maps_data_correctly(): void
    {
        $asset = (object)[
            'code' => 'FA-001',
            'name' => 'Laptop Dell',
            'category' => 'computer',
            'location' => 'Office',
            'acquisition_date' => '2024-01-01',
            'acquisition_cost' => 15000000,
            'salvage_value' => 1500000,
            'depreciation_method' => 'straight_line',
            'useful_life' => 5,
            'accumulated_depreciation' => 3000000,
            'book_value' => 12000000,
            'status' => 'active',
            'outlet' => (object)['nama_outlet' => 'Outlet A']
        ];

        $export = new FixedAssetsExport([]);
        $mapped = $export->map($asset);

        $this->assertEquals('FA-001', $mapped[0]);
        $this->assertEquals('Laptop Dell', $mapped[1]);
        $this->assertEquals('Komputer & IT', $mapped[2]);
        $this->assertEquals('Office', $mapped[3]);
        $this->assertEquals('01/01/2024', $mapped[4]);
        $this->assertEquals(15000000.0, $mapped[5]);
        $this->assertEquals(1500000.0, $mapped[6]);
        $this->assertEquals('Garis Lurus', $mapped[7]);
        $this->assertEquals(5, $mapped[8]);
        $this->assertEquals(3000000.0, $mapped[9]);
        $this->assertEquals(12000000.0, $mapped[10]);
        $this->assertEquals('Aktif', $mapped[11]);
        $this->assertEquals('Outlet A', $mapped[12]);
    }

    /**
     * Test category formatting
     */
    public function test_fixed_assets_export_category_formatting(): void
    {
        $categories = [
            'building' => 'Bangunan',
            'vehicle' => 'Kendaraan',
            'equipment' => 'Peralatan',
            'furniture' => 'Furniture',
            'electronics' => 'Elektronik',
            'computer' => 'Komputer & IT',
            'land' => 'Tanah',
            'other' => 'Lainnya'
        ];

        foreach ($categories as $category => $expected) {
            $asset = (object)[
                'code' => 'FA-001',
                'name' => 'Test Asset',
                'category' => $category,
                'location' => 'Office',
                'acquisition_date' => '2024-01-01',
                'acquisition_cost' => 1000000,
                'salvage_value' => 100000,
                'depreciation_method' => 'straight_line',
                'useful_life' => 5,
                'accumulated_depreciation' => 0,
                'book_value' => 1000000,
                'status' => 'active',
                'outlet' => (object)['nama_outlet' => 'Outlet A']
            ];

            $export = new FixedAssetsExport([]);
            $mapped = $export->map($asset);

            $this->assertEquals($expected, $mapped[2]);
        }
    }

    /**
     * Test depreciation method formatting
     */
    public function test_fixed_assets_export_depreciation_method_formatting(): void
    {
        $methods = [
            'straight_line' => 'Garis Lurus',
            'declining_balance' => 'Saldo Menurun',
            'double_declining' => 'Saldo Menurun Ganda',
            'units_of_production' => 'Unit Produksi'
        ];

        foreach ($methods as $method => $expected) {
            $asset = (object)[
                'code' => 'FA-001',
                'name' => 'Test Asset',
                'category' => 'equipment',
                'location' => 'Office',
                'acquisition_date' => '2024-01-01',
                'acquisition_cost' => 1000000,
                'salvage_value' => 100000,
                'depreciation_method' => $method,
                'useful_life' => 5,
                'accumulated_depreciation' => 0,
                'book_value' => 1000000,
                'status' => 'active',
                'outlet' => (object)['nama_outlet' => 'Outlet A']
            ];

            $export = new FixedAssetsExport([]);
            $mapped = $export->map($asset);

            $this->assertEquals($expected, $mapped[7]);
        }
    }

    /**
     * Test status formatting
     */
    public function test_fixed_assets_export_status_formatting(): void
    {
        $statuses = [
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'disposed' => 'Dijual/Dihapus',
            'sold' => 'Terjual'
        ];

        foreach ($statuses as $status => $expected) {
            $asset = (object)[
                'code' => 'FA-001',
                'name' => 'Test Asset',
                'category' => 'equipment',
                'location' => 'Office',
                'acquisition_date' => '2024-01-01',
                'acquisition_cost' => 1000000,
                'salvage_value' => 100000,
                'depreciation_method' => 'straight_line',
                'useful_life' => 5,
                'accumulated_depreciation' => 0,
                'book_value' => 1000000,
                'status' => $status,
                'outlet' => (object)['nama_outlet' => 'Outlet A']
            ];

            $export = new FixedAssetsExport([]);
            $mapped = $export->map($asset);

            $this->assertEquals($expected, $mapped[11]);
        }
    }

    /**
     * Test that FixedAssetsExport handles null values
     */
    public function test_fixed_assets_export_handles_null_values(): void
    {
        $asset = (object)[
            'code' => null,
            'name' => null,
            'category' => null,
            'location' => null,
            'acquisition_date' => null,
            'acquisition_cost' => null,
            'salvage_value' => null,
            'depreciation_method' => null,
            'useful_life' => null,
            'accumulated_depreciation' => null,
            'book_value' => null,
            'status' => null,
            'outlet' => (object)['nama_outlet' => '']
        ];

        $export = new FixedAssetsExport([]);
        $mapped = $export->map($asset);

        $this->assertEquals('', $mapped[0]);
        $this->assertEquals('', $mapped[1]);
        $this->assertEquals('', $mapped[2]); // Empty category
        $this->assertEquals('', $mapped[3]);
        $this->assertEquals('', $mapped[4]);
        $this->assertEquals(0.0, $mapped[5]);
        $this->assertEquals(0.0, $mapped[6]);
        $this->assertEquals('', $mapped[7]); // Empty method
        $this->assertEquals(0, $mapped[8]);
        $this->assertEquals(0.0, $mapped[9]);
        $this->assertEquals(0.0, $mapped[10]);
        $this->assertEquals('', $mapped[11]); // Empty status
        $this->assertEquals('', $mapped[12]);
    }
}
