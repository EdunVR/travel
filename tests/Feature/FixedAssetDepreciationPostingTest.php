<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\FixedAsset;
use App\Models\FixedAssetDepreciation;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FixedAssetDepreciationPostingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $outlet;
    protected $asset;
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
            'balance' => 100000000,
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

        $this->asset = FixedAsset::create([
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
    }

    /** @test */
    public function it_creates_journal_entry_when_posting_depreciation()
    {
        $this->actingAs($this->user);

        // Create a draft depreciation
        $depreciation = FixedAssetDepreciation::create([
            'fixed_asset_id' => $this->asset->id,
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

        // Verify journal entry was created
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'fixed_asset_depreciation',
            'status' => 'posted'
        ]);
    }

    /** @test */
    public function it_creates_correct_journal_entry_format()
    {
        $this->actingAs($this->user);

        $depreciation = FixedAssetDepreciation::create([
            'fixed_asset_id' => $this->asset->id,
            'period' => 1,
            'depreciation_date' => now(),
            'amount' => 750000,
            'accumulated_depreciation' => 750000,
            'book_value' => 99250000,
            'status' => 'draft'
        ]);

        $response = $this->postJson("/finance/fixed-assets/depreciation/{$depreciation->id}/post");

        $response->assertStatus(200);

        $journalEntry = JournalEntry::where('reference_type', 'fixed_asset_depreciation')
            ->where('reference_number', 'LIKE', '%AST-001%')
            ->first();

        $this->assertNotNull($journalEntry);

        // Verify debit entry (Depreciation Expense)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->depreciationExpenseAccount->id,
            'debit' => 750000,
            'credit' => 0
        ]);

        // Verify credit entry (Accumulated Depreciation)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->accumulatedDepreciationAccount->id,
            'debit' => 0,
            'credit' => 750000
        ]);
    }

    /** @test */
    public function it_updates_account_balances_after_posting()
    {
        $this->actingAs($this->user);

        $initialExpenseBalance = $this->depreciationExpenseAccount->balance;
        $initialAccumulatedBalance = $this->accumulatedDepreciationAccount->balance;

        $depreciation = FixedAssetDepreciation::create([
            'fixed_asset_id' => $this->asset->id,
            'period' => 1,
            'depreciation_date' => now(),
            'amount' => 750000,
            'accumulated_depreciation' => 750000,
            'book_value' => 99250000,
            'status' => 'draft'
        ]);

        $response = $this->postJson("/finance/fixed-assets/depreciation/{$depreciation->id}/post");

        $response->assertStatus(200);

        // Refresh account balances
        $this->depreciationExpenseAccount->refresh();
        $this->accumulatedDepreciationAccount->refresh();

        // Verify expense account increased (debit)
        $this->assertEquals($initialExpenseBalance + 750000, $this->depreciationExpenseAccount->balance);

        // Verify accumulated depreciation increased (credit for contra-asset)
        $this->assertEquals($initialAccumulatedBalance + 750000, $this->accumulatedDepreciationAccount->balance);
    }

    /** @test */
    public function it_prevents_double_posting_of_depreciation()
    {
        $this->actingAs($this->user);

        $depreciation = FixedAssetDepreciation::create([
            'fixed_asset_id' => $this->asset->id,
            'period' => 1,
            'depreciation_date' => now(),
            'amount' => 750000,
            'accumulated_depreciation' => 750000,
            'book_value' => 99250000,
            'status' => 'draft'
        ]);

        // Post first time
        $response1 = $this->postJson("/finance/fixed-assets/depreciation/{$depreciation->id}/post");
        $response1->assertStatus(200);

        // Try to post again
        $response2 = $this->postJson("/finance/fixed-assets/depreciation/{$depreciation->id}/post");
        
        $response2->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
    }

    /** @test */
    public function it_updates_depreciation_status_to_posted()
    {
        $this->actingAs($this->user);

        $depreciation = FixedAssetDepreciation::create([
            'fixed_asset_id' => $this->asset->id,
            'period' => 1,
            'depreciation_date' => now(),
            'amount' => 750000,
            'accumulated_depreciation' => 750000,
            'book_value' => 99250000,
            'status' => 'draft'
        ]);

        $response = $this->postJson("/finance/fixed-assets/depreciation/{$depreciation->id}/post");

        $response->assertStatus(200);

        $depreciation->refresh();

        $this->assertEquals('posted', $depreciation->status);
        $this->assertNotNull($depreciation->journal_entry_id);
    }

    /** @test */
    public function it_updates_fixed_asset_accumulated_depreciation()
    {
        $this->actingAs($this->user);

        $initialAccumulated = $this->asset->accumulated_depreciation;
        $initialBookValue = $this->asset->book_value;

        $depreciation = FixedAssetDepreciation::create([
            'fixed_asset_id' => $this->asset->id,
            'period' => 1,
            'depreciation_date' => now(),
            'amount' => 750000,
            'accumulated_depreciation' => 750000,
            'book_value' => 99250000,
            'status' => 'draft'
        ]);

        $response = $this->postJson("/finance/fixed-assets/depreciation/{$depreciation->id}/post");

        $response->assertStatus(200);

        $this->asset->refresh();

        $this->assertEquals($initialAccumulated + 750000, $this->asset->accumulated_depreciation);
        $this->assertEquals($initialBookValue - 750000, $this->asset->book_value);
    }
}
