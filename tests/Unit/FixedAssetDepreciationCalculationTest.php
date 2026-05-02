<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\FixedAsset;
use App\Models\ChartOfAccount;
use App\Models\Outlet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FixedAssetDepreciationCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected $outlet;
    protected $assetAccount;
    protected $paymentAccount;
    protected $depreciationExpenseAccount;
    protected $accumulatedDepreciationAccount;

    protected function setUp(): void
    {
        parent::setUp();

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
    }

    /** @test */
    public function it_calculates_straight_line_depreciation_correctly()
    {
        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-001',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now(),
            'acquisition_cost' => 120000000,
            'salvage_value' => 12000000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'accumulated_depreciation' => 0,
            'book_value' => 120000000,
            'status' => 'active'
        ]);

        $monthlyDepreciation = $asset->calculateMonthlyDepreciation();

        // (120,000,000 - 12,000,000) / 10 / 12 = 900,000
        $this->assertEquals(900000, $monthlyDepreciation);
    }

    /** @test */
    public function it_calculates_declining_balance_depreciation_correctly()
    {
        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-002',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now(),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 5,
            'depreciation_method' => 'declining_balance',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'accumulated_depreciation' => 0,
            'book_value' => 100000000,
            'status' => 'active'
        ]);

        $monthlyDepreciation = $asset->calculateMonthlyDepreciation();

        // rate = 1.5 / 5 = 0.3
        // monthly = 100,000,000 * 0.3 / 12 = 2,500,000
        $this->assertEquals(2500000, $monthlyDepreciation);
    }

    /** @test */
    public function it_calculates_double_declining_depreciation_correctly()
    {
        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-003',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now(),
            'acquisition_cost' => 60000000,
            'salvage_value' => 6000000,
            'useful_life' => 3,
            'depreciation_method' => 'double_declining',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'accumulated_depreciation' => 0,
            'book_value' => 60000000,
            'status' => 'active'
        ]);

        $monthlyDepreciation = $asset->calculateMonthlyDepreciation();

        // rate = 2 / 3 = 0.6667
        // monthly = 60,000,000 * 0.6667 / 12 = 3,333,500
        $this->assertEqualsWithDelta(3333500, $monthlyDepreciation, 1000);
    }

    /** @test */
    public function it_does_not_exceed_depreciable_amount()
    {
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
            'accumulated_depreciation' => 89500000, // Almost fully depreciated
            'book_value' => 10500000, // Close to salvage value
            'status' => 'active'
        ]);

        $monthlyDepreciation = $asset->calculateMonthlyDepreciation();

        // Should not exceed remaining depreciable amount
        // book_value - salvage_value = 10,500,000 - 10,000,000 = 500,000
        $this->assertEquals(500000, $monthlyDepreciation);
    }

    /** @test */
    public function it_stops_depreciation_at_salvage_value()
    {
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
            'accumulated_depreciation' => 90000000, // Fully depreciated
            'book_value' => 10000000, // At salvage value
            'status' => 'active'
        ]);

        $monthlyDepreciation = $asset->calculateMonthlyDepreciation();

        // Should return 0 when book_value equals salvage_value
        $this->assertEquals(0, $monthlyDepreciation);
    }

    /** @test */
    public function it_stops_depreciation_below_salvage_value()
    {
        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-006',
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
            'accumulated_depreciation' => 95000000,
            'book_value' => 5000000, // Below salvage value (shouldn't happen but test it)
            'status' => 'active'
        ]);

        $monthlyDepreciation = $asset->calculateMonthlyDepreciation();

        // Should return 0 when book_value is below salvage_value
        $this->assertEquals(0, $monthlyDepreciation);
    }

    /** @test */
    public function declining_balance_respects_salvage_value()
    {
        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-007',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now(),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 5,
            'depreciation_method' => 'declining_balance',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'accumulated_depreciation' => 89500000,
            'book_value' => 10500000,
            'status' => 'active'
        ]);

        $monthlyDepreciation = $asset->calculateMonthlyDepreciation();

        // Should not depreciate below salvage value
        // Remaining: 10,500,000 - 10,000,000 = 500,000
        $this->assertEquals(500000, $monthlyDepreciation);
    }
}
