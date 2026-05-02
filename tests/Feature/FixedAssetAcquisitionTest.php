<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\FixedAsset;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class FixedAssetAcquisitionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $outlet;
    protected $assetAccount;
    protected $paymentAccount;
    protected $depreciationExpenseAccount;
    protected $accumulatedDepreciationAccount;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create();

        // Create test outlet
        $this->outlet = Outlet::factory()->create();

        // Create test chart of accounts
        $this->assetAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'asset',
            'code' => '1300',
            'name' => 'Fixed Assets',
            'balance' => 0,
            'status' => 'active'
        ]);

        $this->paymentAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'asset',
            'code' => '1100',
            'name' => 'Cash',
            'balance' => 10000000,
            'status' => 'active'
        ]);

        $this->depreciationExpenseAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'expense',
            'code' => '6100',
            'name' => 'Depreciation Expense',
            'balance' => 0,
            'status' => 'active'
        ]);

        $this->accumulatedDepreciationAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'asset',
            'category' => 'contra_asset',
            'code' => '1310',
            'name' => 'Accumulated Depreciation',
            'balance' => 0,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function it_can_create_asset_with_valid_data()
    {
        $this->actingAs($this->user);

        $assetData = [
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-001',
            'name' => 'Office Building',
            'category' => 'building',
            'location' => 'Jakarta',
            'acquisition_date' => now()->format('Y-m-d'),
            'acquisition_cost' => 500000000,
            'salvage_value' => 50000000,
            'useful_life' => 20,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'description' => 'Main office building'
        ];

        $response = $this->postJson('/finance/fixed-assets', $assetData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('fixed_assets', [
            'code' => 'AST-001',
            'name' => 'Office Building',
            'acquisition_cost' => 500000000,
            'book_value' => 500000000
        ]);
    }

    /** @test */
    public function it_creates_journal_entry_on_asset_acquisition()
    {
        $this->actingAs($this->user);

        $assetData = [
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-002',
            'name' => 'Company Vehicle',
            'category' => 'vehicle',
            'acquisition_date' => now()->format('Y-m-d'),
            'acquisition_cost' => 300000000,
            'salvage_value' => 30000000,
            'useful_life' => 5,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id
        ];

        $response = $this->postJson('/finance/fixed-assets', $assetData);

        $response->assertStatus(200);

        // Verify journal entry was created
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'fixed_asset_acquisition',
            'reference_number' => 'AST-002',
            'status' => 'posted'
        ]);

        $journalEntry = JournalEntry::where('reference_number', 'AST-002')->first();
        
        // Verify journal entry details
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->assetAccount->id,
            'debit' => 300000000,
            'credit' => 0
        ]);

        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->paymentAccount->id,
            'debit' => 0,
            'credit' => 300000000
        ]);
    }

    /** @test */
    public function it_updates_account_balances_after_acquisition()
    {
        $this->actingAs($this->user);

        $initialAssetBalance = $this->assetAccount->balance;
        $initialPaymentBalance = $this->paymentAccount->balance;

        $assetData = [
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-003',
            'name' => 'Computer Equipment',
            'category' => 'computer',
            'acquisition_date' => now()->format('Y-m-d'),
            'acquisition_cost' => 50000000,
            'salvage_value' => 5000000,
            'useful_life' => 3,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id
        ];

        $response = $this->postJson('/finance/fixed-assets', $assetData);

        $response->assertStatus(200);

        // Refresh account balances
        $this->assetAccount->refresh();
        $this->paymentAccount->refresh();

        // Verify balances updated
        $this->assertEquals($initialAssetBalance + 50000000, $this->assetAccount->balance);
        $this->assertEquals($initialPaymentBalance - 50000000, $this->paymentAccount->balance);
    }

    /** @test */
    public function it_can_create_assets_with_different_categories()
    {
        $this->actingAs($this->user);

        $categories = ['land', 'building', 'vehicle', 'equipment', 'furniture', 'computer'];

        foreach ($categories as $index => $category) {
            $assetData = [
                'outlet_id' => $this->outlet->id_outlet,
                'code' => 'AST-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'name' => ucfirst($category) . ' Asset',
                'category' => $category,
                'acquisition_date' => now()->format('Y-m-d'),
                'acquisition_cost' => 100000000,
                'salvage_value' => 10000000,
                'useful_life' => 10,
                'depreciation_method' => 'straight_line',
                'asset_account_id' => $this->assetAccount->id,
                'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
                'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
                'payment_account_id' => $this->paymentAccount->id
            ];

            $response = $this->postJson('/finance/fixed-assets', $assetData);

            $response->assertStatus(200);

            $this->assertDatabaseHas('fixed_assets', [
                'code' => 'AST-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'category' => $category
            ]);
        }
    }
}
