<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\FixedAsset;
use App\Models\FixedAssetDepreciation;
use App\Models\ChartOfAccount;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FixedAssetFrontendIntegrationTest extends TestCase
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
            'balance' => 10000000000,
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
    public function it_can_load_fixed_assets_data_from_api()
    {
        $this->actingAs($this->user);

        // Create test assets
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

        $response = $this->getJson('/finance/fixed-assets/data');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'code',
                        'name',
                        'category',
                        'acquisition_cost',
                        'book_value',
                        'status'
                    ]
                ],
                'stats' => [
                    'totalAssets',
                    'activeAssets',
                    'totalAcquisitionCost',
                    'totalDepreciation',
                    'totalBookValue'
                ]
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_load_depreciation_history_from_api()
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

        // Create depreciation records
        for ($i = 1; $i <= 3; $i++) {
            FixedAssetDepreciation::create([
                'fixed_asset_id' => $asset->id,
                'period' => $i,
                'depreciation_date' => now()->subMonths(3 - $i),
                'amount' => 750000,
                'accumulated_depreciation' => 750000 * $i,
                'book_value' => 100000000 - (750000 * $i),
                'status' => 'posted'
            ]);
        }

        $response = $this->getJson('/finance/fixed-assets/depreciation/history?asset_id=' . $asset->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'period',
                        'depreciation_date',
                        'amount',
                        'accumulated_depreciation',
                        'book_value',
                        'status'
                    ]
                ]
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_create_asset_via_form_submission()
    {
        $this->actingAs($this->user);

        $formData = [
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-NEW',
            'name' => 'New Asset from Form',
            'category' => 'equipment',
            'location' => 'Office',
            'acquisition_date' => now()->format('Y-m-d'),
            'acquisition_cost' => 150000000,
            'salvage_value' => 15000000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'description' => 'Created via form'
        ];

        $response = $this->postJson('/finance/fixed-assets', $formData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('fixed_assets', [
            'code' => 'AST-NEW',
            'name' => 'New Asset from Form'
        ]);
    }

    /** @test */
    public function it_can_update_asset_via_form_submission()
    {
        $this->actingAs($this->user);

        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-001',
            'name' => 'Original Name',
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

        $updateData = [
            'name' => 'Updated Name',
            'location' => 'New Location',
            'description' => 'Updated description'
        ];

        $response = $this->putJson("/finance/fixed-assets/{$asset->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('fixed_assets', [
            'id' => $asset->id,
            'name' => 'Updated Name',
            'location' => 'New Location'
        ]);
    }

    /** @test */
    public function it_can_calculate_depreciation_via_api()
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

        $calculateData = [
            'outlet_id' => $this->outlet->id_outlet,
            'period_month' => now()->month,
            'period_year' => now()->year
        ];

        $response = $this->postJson('/finance/fixed-assets/calculate-depreciation', $calculateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_assets_processed',
                    'total_depreciation_amount'
                ]
            ]);
    }

    /** @test */
    public function it_can_post_depreciation_via_api()
    {
        $this->actingAs($this->user);

        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-001',
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

        $depreciation = FixedAssetDepreciation::create([
            'fixed_asset_id' => $asset->id,
            'period' => 1,
            'depreciation_date' => now(),
            'amount' => 750000,
            'accumulated_depreciation' => 750000,
            'book_value' => 99250000,
            'status' => 'draft'
        ]);

        $response = $this->postJson("/finance/fixed-assets/depreciation/{$depreciation->id}/post");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $depreciation->refresh();
        $this->assertEquals('posted', $depreciation->status);
    }

    /** @test */
    public function it_can_dispose_asset_via_form()
    {
        $this->actingAs($this->user);

        $gainAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'otherrevenue',
            'status' => 'active'
        ]);

        $asset = FixedAsset::create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'AST-001',
            'name' => 'Test Asset',
            'category' => 'equipment',
            'acquisition_date' => now()->subYears(2),
            'acquisition_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $this->assetAccount->id,
            'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
            'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
            'payment_account_id' => $this->paymentAccount->id,
            'accumulated_depreciation' => 18000000,
            'book_value' => 82000000,
            'status' => 'active'
        ]);

        $disposalData = [
            'disposal_date' => now()->format('Y-m-d'),
            'disposal_value' => 90000000,
            'disposal_notes' => 'Sold via form',
            'gain_loss_account_id' => $gainAccount->id
        ];

        $response = $this->postJson("/finance/fixed-assets/{$asset->id}/dispose", $disposalData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $asset->refresh();
        $this->assertEquals('sold', $asset->status);
    }

    /** @test */
    public function it_can_load_chart_data_for_asset_value()
    {
        $this->actingAs($this->user);

        // Create assets with different acquisition years
        for ($i = 0; $i < 3; $i++) {
            FixedAsset::create([
                'outlet_id' => $this->outlet->id_outlet,
                'code' => 'AST-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'name' => 'Test Asset ' . ($i + 1),
                'category' => 'equipment',
                'acquisition_date' => now()->subYears($i),
                'acquisition_cost' => 100000000,
                'salvage_value' => 10000000,
                'useful_life' => 10,
                'depreciation_method' => 'straight_line',
                'asset_account_id' => $this->assetAccount->id,
                'depreciation_expense_account_id' => $this->depreciationExpenseAccount->id,
                'accumulated_depreciation_account_id' => $this->accumulatedDepreciationAccount->id,
                'payment_account_id' => $this->paymentAccount->id,
                'accumulated_depreciation' => 9000000 * $i,
                'book_value' => 100000000 - (9000000 * $i),
                'status' => 'active'
            ]);
        }

        $response = $this->getJson('/finance/fixed-assets/chart/value?outlet_id=' . $this->outlet->id_outlet);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'labels',
                    'datasets'
                ]
            ]);
    }

    /** @test */
    public function it_can_load_chart_data_for_asset_distribution()
    {
        $this->actingAs($this->user);

        // Create assets with different categories
        $categories = ['equipment', 'vehicle', 'furniture'];
        foreach ($categories as $index => $category) {
            FixedAsset::create([
                'outlet_id' => $this->outlet->id_outlet,
                'code' => 'AST-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'name' => ucfirst($category) . ' Asset',
                'category' => $category,
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
        }

        $response = $this->getJson('/finance/fixed-assets/chart/distribution?outlet_id=' . $this->outlet->id_outlet);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'labels',
                    'datasets'
                ]
            ]);
    }
}
