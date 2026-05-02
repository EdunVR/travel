<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Outlet;
use App\Models\AccountingBook;
use App\Models\ChartOfAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FinanceImportIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $outlet;
    protected $book;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        // Create test user and outlet
        $this->user = User::factory()->create();
        $this->outlet = Outlet::factory()->create();
        
        // Create accounting book
        $this->book = AccountingBook::factory()->create([
            'outlet_id' => $this->outlet->id,
            'type' => 'general',
            'status' => 'active'
        ]);

        // Create chart of accounts
        ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id,
            'code' => '1010',
            'name' => 'Kas',
            'type' => 'asset'
        ]);

        ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id,
            'code' => '5010',
            'name' => 'Beban Operasional',
            'type' => 'expense'
        ]);
    }

    /**
     * Test journal import with valid file
     */
    public function test_journal_import_with_valid_file(): void
    {
        $this->actingAs($this->user);

        // Create a mock Excel file
        $file = UploadedFile::fake()->create('journal_import.xlsx', 100);

        $response = $this->post(route('finance.journal.import'), [
            'file' => $file,
            'outlet_id' => $this->outlet->id,
            'book_id' => $this->book->id
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'imported_count',
            'skipped_count'
        ]);
    }

    /**
     * Test journal import without file
     */
    public function test_journal_import_without_file(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('finance.journal.import'), [
            'outlet_id' => $this->outlet->id,
            'book_id' => $this->book->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    /**
     * Test journal import with invalid file type
     */
    public function test_journal_import_with_invalid_file_type(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('journal_import.txt', 100);

        $response = $this->post(route('finance.journal.import'), [
            'file' => $file,
            'outlet_id' => $this->outlet->id,
            'book_id' => $this->book->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    /**
     * Test fixed assets import with valid file
     */
    public function test_fixed_assets_import_with_valid_file(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('fixed_assets_import.xlsx', 100);

        $response = $this->post(route('finance.fixed-assets.import'), [
            'file' => $file,
            'outlet_id' => $this->outlet->id
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'imported_count',
            'skipped_count'
        ]);
    }

    /**
     * Test fixed assets import without file
     */
    public function test_fixed_assets_import_without_file(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('finance.fixed-assets.import'), [
            'outlet_id' => $this->outlet->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    /**
     * Test import requires authentication
     */
    public function test_import_requires_authentication(): void
    {
        $file = UploadedFile::fake()->create('journal_import.xlsx', 100);

        $response = $this->post(route('finance.journal.import'), [
            'file' => $file,
            'outlet_id' => $this->outlet->id,
            'book_id' => $this->book->id
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test import with large file
     */
    public function test_import_with_large_file(): void
    {
        $this->actingAs($this->user);

        // Create a file larger than allowed size (e.g., 10MB)
        $file = UploadedFile::fake()->create('large_import.xlsx', 10240);

        $response = $this->post(route('finance.journal.import'), [
            'file' => $file,
            'outlet_id' => $this->outlet->id,
            'book_id' => $this->book->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    /**
     * Test download journal template
     */
    public function test_download_journal_template(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.journal.template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test download fixed assets template
     */
    public function test_download_fixed_assets_template(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('finance.fixed-assets.template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
