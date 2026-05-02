<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Outlet;
use App\Models\JournalEntry;
use App\Models\FixedAsset;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinanceExportIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $outlet;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user and outlet
        $this->user = User::factory()->create();
        $this->outlet = Outlet::factory()->create();
    }

    /**
     * Test journal export to XLSX
     */
    public function test_journal_export_to_xlsx(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.journal.export.xlsx', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test journal export to PDF
     */
    public function test_journal_export_to_pdf(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.journal.export.pdf', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test fixed assets export to XLSX
     */
    public function test_fixed_assets_export_to_xlsx(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.fixed-assets.export.xlsx', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test fixed assets export to PDF
     */
    public function test_fixed_assets_export_to_pdf(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.fixed-assets.export.pdf', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test general ledger export to XLSX
     */
    public function test_general_ledger_export_to_xlsx(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.general-ledger.export.xlsx', [
            'outlet_id' => $this->outlet->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test general ledger export to PDF
     */
    public function test_general_ledger_export_to_pdf(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.general-ledger.export.pdf', [
            'outlet_id' => $this->outlet->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test accounting book export to XLSX
     */
    public function test_accounting_book_export_to_xlsx(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.accounting-book.export.xlsx', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test accounting book export to PDF
     */
    public function test_accounting_book_export_to_pdf(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.accounting-book.export.pdf', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test export with filters applied
     */
    public function test_export_with_filters(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.journal.export.xlsx', [
            'outlet_id' => $this->outlet->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'status' => 'posted'
        ]));

        $response->assertStatus(200);
    }

    /**
     * Test export requires authentication
     */
    public function test_export_requires_authentication(): void
    {
        $response = $this->get(route('finance.journal.export.xlsx', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertRedirect(route('login'));
    }
}
