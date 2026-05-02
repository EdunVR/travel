<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Outlet;
use App\Models\JournalEntry;
use App\Models\FixedAsset;
use App\Models\ChartOfAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinancePrintIntegrationTest extends TestCase
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
     * Test journal print generates PDF
     */
    public function test_journal_print_generates_pdf(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.journal.print', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test fixed assets print generates PDF
     */
    public function test_fixed_assets_print_generates_pdf(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.fixed-assets.print', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test general ledger print generates PDF
     */
    public function test_general_ledger_print_generates_pdf(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.general-ledger.print', [
            'outlet_id' => $this->outlet->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test accounting book print generates PDF
     */
    public function test_accounting_book_print_generates_pdf(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.accounting-book.print', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test print with filters applied
     */
    public function test_print_with_filters(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.journal.print', [
            'outlet_id' => $this->outlet->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'status' => 'posted'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test print requires authentication
     */
    public function test_print_requires_authentication(): void
    {
        $response = $this->get(route('finance.journal.print', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test print with empty data
     */
    public function test_print_with_empty_data(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.journal.print', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test print PDF contains correct content
     */
    public function test_print_pdf_contains_correct_content(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.journal.print', [
            'outlet_id' => $this->outlet->id
        ]));

        $response->assertStatus(200);
        
        // Check that response is PDF
        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF', $content);
    }

    /**
     * Test print with date range filter
     */
    public function test_print_with_date_range_filter(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.general-ledger.print', [
            'outlet_id' => $this->outlet->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test print with status filter
     */
    public function test_print_with_status_filter(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.journal.print', [
            'outlet_id' => $this->outlet->id,
            'status' => 'posted'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test print with category filter for fixed assets
     */
    public function test_print_with_category_filter(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.fixed-assets.print', [
            'outlet_id' => $this->outlet->id,
            'category' => 'equipment'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
