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

class FixedAssetBatchProcessingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $outlet;
    protected $assetAccount;
    protected $paymentAccount;
    protected $depreciationExpenseAccount;
    protected $accumulatedDepreciationAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->outlet = Outlet::factory()->create();

        $this->assetAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'asset',
            'balance' => 0,
            'status' => 'active'
        ]);

        $this->paymentAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'asset',
            'balance' => 0,
            'status' => 'active'
        ]);

        $this->depreciationExpenseAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'expense',
            'balance' => 0,
            'status' => 'active'
        ]);

        $this->accumulatedDepreciationAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'asset',
            'category' => 'contra_asset',
            'balance' => 0,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function it_can_batch_calculate_depreciation_for_multiple_assets()
    {
        $this->actingAs($this->user);

        // Create multiple assets
        $assets = [];
        for ($i = 1; $i <= 5; $i++) {
            $assets[] = FixedAsset::create([
                'outlet_id' => $this->outlet->id_outlet,
                'code' => 'AST-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Test Asset ' . $i,
                'category' => 'equipment',
                'acquisition_date' => now()->subMonths(6),
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
        }

        $batchData = [
            'outlet_id' => $this->outlet->id_outlet,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'auto_post' => false
        ];

        $response = $this->postJson('/finance/fixed-assets/batch-depreciation', $batchData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'data' => [
                    'total_assets_processed',
                    'total_depreciation_amount'
                ]
            ]);

        // Verify depreciation records created
        $this->assertEquals(5, FixedAssetDepreciation::count());
    }

    /** @test */
    public function it_can_batch_post_depreciation_with_auto_post()
    {
        $this->actingAs($this->user);

        // Create assets
        for ($i = 1; $i <= 3; $i++) {
            FixedAsset::create([
                'outlet_id' => $this->outlet->id_outlet,
                'code' => 'AST-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Test Asset ' . $i,
                'category' => 'equipment',
                'acquisition_date' => now()->subMonths(6),
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
        }

        $batchData = [
            'outlet_id' => $this->outlet->id_outlet,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'auto_post' => true
        ];

        $response = $this->postJson('/finance/fixed-assets/batch-depreciation', $batchData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_assets_processed',
                    'total_journals_created',
                    'total_depreciation_amount'
                ]
            ]);

        // Verify all depreciations are posted
        $this->assertEquals(3, FixedAssetDepreciation::where('status', 'posted')->count());

        // Verify journal entries created
        $this->assertEquals(3, JournalEntry::where('reference_type', 'fixed_asset_depreciation')->count());
    }

    /** @test */
    public function it_handles_errors_in_batch_processing()
    {
        $this->actingAs($this->user);

        // Create valid asset
        FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-001',
            'name' => 'Valid Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->subMonths(6),
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

        // Create asset that's fully depreciated (should be skipped)
        FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-002',
            'name' => 'Fully Depreciated Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->subYears(10),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'accumulated_depreciation' => 90000000,
            'book_value' => 10000000, // At salvage value
            'status' => 'active'
        ]);

        $batchData = [
            'outlet_id' => $this->outlet->id_outlet,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'auto_post' => false
        ];

        $response = $this->postJson('/finance/fixed-assets/batch-depreciation', $batchData);

        $response->assertStatus(200);

        // Only 1 depreciation should be created (for the valid asset)
        $this->assertEquals(1, FixedAssetDepreciation::count());
    }

    /** @test */
    public function it_returns_correct_summary_after_batch_processing()
    {
        $this->actingAs($this->user);

        // Create 3 assets with different depreciation amounts
        $expectedTotal = 0;
        for ($i = 1; $i <= 3; $i++) {
            $cost = 100000000 * $i;
            $salvage = 10000000 * $i;
            $monthlyDep = ($cost - $salvage) / 10 / 12;
            $expectedTotal += $monthlyDep;

            FixedAsset::create([
                'outlet_id' => $this->outlet->id_outlet,
                'code' => 'AST-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Test Asset ' . $i,
                'category' => 'equipment',
                'acquisition_date' => now()->subMonths(6),
                'acquisition_cost' => $cost,
                'salvage_value' => $salvage,
                'useful_life' => 10,
                'depreciation_method' => 'straight_line',
                'asset_account_id' => $this->assetAccount->id,
                'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
                'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
                'payment_account_id' => $this->paymentAccount->id,
                'accumulated_depreciation' => 0,
                'book_value' => $cost,
                'status' => 'active'
            ]);
        }

        $batchData = [
            'outlet_id' => $this->outlet->id_outlet,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'auto_post' => false
        ];

        $response = $this->postJson('/finance/fixed-assets/batch-depreciation', $batchData);

        $response->assertStatus(200);

        $data = $response->json('data');

        $this->assertEquals(3, $data['total_assets_processed']);
        $this->assertEqualsWithDelta($expectedTotal, $data['total_depreciation_amount'], 1);
    }

    /** @test */
    public function it_skips_inactive_assets_in_batch_processing()
    {
        $this->actingAs($this->user);

        // Create active asset
        FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-001',
            'name' => 'Active Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->subMonths(6),
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

        // Create inactive asset
        FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-002',
            'name' => 'Inactive Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->subMonths(6),
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
            'status' => 'inactive'
        ]);

        $batchData = [
            'outlet_id' => $this->outlet->id_outlet,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'auto_post' => false
        ];

        $response = $this->postJson('/finance/fixed-assets/batch-depreciation', $batchData);

        $response->assertStatus(200);

        // Only 1 depreciation should be created (for active asset)
        $this->assertEquals(1, FixedAssetDepreciation::count());
        
        $data = $response->json('data');
        $this->assertEquals(1, $data['total_assets_processed']);
    }

    /** @test */
    public function it_prevents_duplicate_depreciation_for_same_period()
    {
        $this->actingAs($this->user);

        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-001',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->subMonths(6),
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

        $batchData = [
            'outlet_id' => $this->outlet->id_outlet,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'auto_post' => false
        ];

        // First batch
        $response1 = $this->postJson('/finance/fixed-assets/batch-depreciation', $batchData);
        $response1->assertStatus(200);

        // Second batch for same period
        $response2 = $this->postJson('/finance/fixed-assets/batch-depreciation', $batchData);
        $response2->assertStatus(200);

        // Should still only have 1 depreciation record
        $this->assertEquals(1, FixedAssetDepreciation::where('fixed_asset_id', $asset->id)->count());
    }
}
