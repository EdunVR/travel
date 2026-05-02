<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\FixedAsset;
use App\Models\FixedAssetDepreciation;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FixedAssetValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $outlet;
    protected $assetAccount;
    protected $paymentAccount;
    protected $depreciationExpenseAccount;
    protected $accumulatedDepreciationAccount;
    protected $expenseAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->outlet = Outlet::factory()->create();

        $this->assetAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'asset',
            'status' => 'active'
        ]);

        $this->paymentAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'asset',
            'status' => 'active'
        ]);

        $this->depreciationExpenseAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'expense',
            'status' => 'active'
        ]);

        $this->accumulatedDepreciationAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'asset',
            'category' => 'contra_asset',
            'status' => 'active'
        ]);

        $this->expenseAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'expense',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function it_rejects_invalid_asset_account_type()
    {
        $this->actingAs($this->user);

        $assetData = [
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-001',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->format('Y-m-d'),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->expenseAccount->id, // Wrong type!
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id
        ];

        $response = $this->postJson('/finance/fixed-assets', $assetData);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
    }

    /** @test */
    public function it_rejects_salvage_value_greater_than_acquisition_cost()
    {
        $this->actingAs($this->user);

        $assetData = [
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-002',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->format('Y-m-d'),
            'acquisition_cost' => 100000000,
            'salvage_value' => 150000000, // Greater than acquisition cost!
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id
        ];

        $response = $this->postJson('/finance/fixed-assets', $assetData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['salvage_value']);
    }

    /** @test */
    public function it_prevents_deleting_asset_with_posted_journal()
    {
        $this->actingAs($this->user);

        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-003',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now(),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'accumulated_depreciation' => 0,
            'book_value' => 100000000,
            'status' => 'active'
        ]);

        // Create a posted depreciation
        $depreciation = FixedAssetDepreciation::create([
            'fixed_asset_id' => $asset->id,
            'period' => 1,
            'depreciation_date' => now(),
            'amount' => 750000,
            'accumulated_depreciation' => 750000,
            'book_value' => 99250000,
            'status' => 'posted',
            'journal_entry_id' => 1 // Simulating posted journal
        ]);

        $response = $this->deleteJson("/finance/fixed-assets/{$asset->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);

        // Asset should still exist
        $this->assertDatabaseHas('fixed_assets', [
            'id' => $asset->id
        ]);
    }

    /** @test */
    public function it_prevents_posting_already_posted_depreciation()
    {
        $this->actingAs($this->user);

        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-004',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now(),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'accumulated_depreciation' => 0,
            'book_value' => 100000000,
            'status' => 'active'
        ]);

        // Create already posted depreciation
        $depreciation = FixedAssetDepreciation::create([
            'fixed_asset_id' => $asset->id,
            'period' => 1,
            'depreciation_date' => now(),
            'amount' => 750000,
            'accumulated_depreciation' => 750000,
            'book_value' => 99250000,
            'status' => 'posted',
            'journal_entry_id' => 1
        ]);

        $response = $this->postJson("/finance/fixed-assets/depreciation/{$depreciation->id}/post");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
    }

    /** @test */
    public function it_prevents_reversing_draft_depreciation()
    {
        $this->actingAs($this->user);

        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-005',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now(),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'accumulated_depreciation' => 0,
            'book_value' => 100000000,
            'status' => 'active'
        ]);

        // Create draft depreciation
        $depreciation = FixedAssetDepreciation::create([
            'fixed_asset_id' => $asset->id,
            'period' => 1,
            'depreciation_date' => now(),
            'amount' => 750000,
            'accumulated_depreciation' => 750000,
            'book_value' => 99250000,
            'status' => 'draft'
        ]);

        $response = $this->postJson("/finance/fixed-assets/depreciation/{$depreciation->id}/reverse");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
    }

    /** @test */
    public function it_validates_useful_life_minimum()
    {
        $this->actingAs($this->user);

        $assetData = [
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-006',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->format('Y-m-d'),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 0, // Invalid: less than 1
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id
        ];

        $response = $this->postJson('/finance/fixed-assets', $assetData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['useful_life']);
    }

    /** @test */
    public function it_validates_acquisition_date_not_in_future()
    {
        $this->actingAs($this->user);

        $assetData = [
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-007',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->addDays(10)->format('Y-m-d'), // Future date
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

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['acquisition_date']);
    }

    /** @test */
    public function it_validates_inactive_accounts_are_rejected()
    {
        $this->actingAs($this->user);

        $inactiveAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'asset',
            'status' => 'inactive' // Inactive account
        ]);

        $assetData = [
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-008',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->format('Y-m-d'),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $inactiveAccount->id, // Inactive!
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id
        ];

        $response = $this->postJson('/finance/fixed-assets', $assetData);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
    }

    /** @test */
    public function it_prevents_updating_acquisition_cost_with_existing_depreciation()
    {
        $this->actingAs($this->user);

        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-009',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now(),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'accumulated_depreciation' => 10000000, // Has depreciation
            'book_value' => 90000000,
            'status' => 'active'
        ]);

        $updateData = [
            'acquisition_cost' => 120000000 // Trying to change cost
        ];

        $response = $this->putJson("/finance/fixed-assets/{$asset->id}", $updateData);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
    }
}
