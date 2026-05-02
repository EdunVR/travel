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

class FixedAssetDisposalTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $outlet;
    protected $asset;
    protected $assetAccount;
    protected $paymentAccount;
    protected $depreciationExpenseAccount;
    protected $accumulatedDepreciationAccount;
    protected $gainAccount;
    protected $lossAccount;

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

        $this->gainAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'otherrevenue',
            'code' => '8100',
            'name' => 'Gain on Disposal',
            'balance' => 0,
            'status' => 'active'
        ]);

        $this->lossAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'otherexpense',
            'code' => '9100',
            'name' => 'Loss on Disposal',
            'balance' => 0,
            'status' => 'active'
        ]);

        $this->asset = FixedAsset::create([
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
            'accumulated_depreciation' => 18000000, // 2 years depreciation
            'book_value' => 82000000,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function it_can_dispose_asset_with_gain()
    {
        $this->actingAs($this->user);

        // Disposal value > book value = gain
        $disposalData = [
            'disposal_date' => now()->format('Y-m-d'),
            'disposal_value' => 90000000, // Higher than book value (82M)
            'disposal_notes' => 'Sold to another company',
            'gain_loss_account_id' => $this->gainAccount->id
        ];

        $response = $this->postJson("/finance/fixed-assets/{$this->asset->id}/dispose", $disposalData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->asset->refresh();

        $this->assertEquals('sold', $this->asset->status);
        $this->assertEquals(90000000, $this->asset->disposal_value);
    }

    /** @test */
    public function it_creates_correct_journal_entry_for_disposal_with_gain()
    {
        $this->actingAs($this->user);

        $disposalData = [
            'disposal_date' => now()->format('Y-m-d'),
            'disposal_value' => 90000000,
            'disposal_notes' => 'Sold with gain',
            'gain_loss_account_id' => $this->gainAccount->id
        ];

        $response = $this->postJson("/finance/fixed-assets/{$this->asset->id}/dispose", $disposalData);

        $response->assertStatus(200);

        // Verify journal entry created
        $journalEntry = JournalEntry::where('reference_type', 'fixed_asset_disposal')
            ->where('reference_number', 'AST-001')
            ->first();

        $this->assertNotNull($journalEntry);

        // Debit: Payment Account (90M)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->paymentAccount->id,
            'debit' => 90000000,
            'credit' => 0
        ]);

        // Debit: Accumulated Depreciation (18M)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->accumulatedDepreciationAccount->id,
            'debit' => 18000000,
            'credit' => 0
        ]);

        // Credit: Asset Account (100M)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->assetAccount->id,
            'debit' => 0,
            'credit' => 100000000
        ]);

        // Credit: Gain on Disposal (8M = 90M - 82M)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->gainAccount->id,
            'debit' => 0,
            'credit' => 8000000
        ]);
    }

    /** @test */
    public function it_can_dispose_asset_with_loss()
    {
        $this->actingAs($this->user);

        // Disposal value < book value = loss
        $disposalData = [
            'disposal_date' => now()->format('Y-m-d'),
            'disposal_value' => 70000000, // Lower than book value (82M)
            'disposal_notes' => 'Sold at loss',
            'gain_loss_account_id' => $this->lossAccount->id
        ];

        $response = $this->postJson("/finance/fixed-assets/{$this->asset->id}/dispose", $disposalData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->asset->refresh();

        $this->assertEquals('sold', $this->asset->status);
        $this->assertEquals(70000000, $this->asset->disposal_value);
    }

    /** @test */
    public function it_creates_correct_journal_entry_for_disposal_with_loss()
    {
        $this->actingAs($this->user);

        $disposalData = [
            'disposal_date' => now()->format('Y-m-d'),
            'disposal_value' => 70000000,
            'disposal_notes' => 'Sold with loss',
            'gain_loss_account_id' => $this->lossAccount->id
        ];

        $response = $this->postJson("/finance/fixed-assets/{$this->asset->id}/dispose", $disposalData);

        $response->assertStatus(200);

        $journalEntry = JournalEntry::where('reference_type', 'fixed_asset_disposal')
            ->where('reference_number', 'AST-001')
            ->first();

        $this->assertNotNull($journalEntry);

        // Debit: Payment Account (70M)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->paymentAccount->id,
            'debit' => 70000000,
            'credit' => 0
        ]);

        // Debit: Accumulated Depreciation (18M)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->accumulatedDepreciationAccount->id,
            'debit' => 18000000,
            'credit' => 0
        ]);

        // Debit: Loss on Disposal (12M = 82M - 70M)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->lossAccount->id,
            'debit' => 12000000,
            'credit' => 0
        ]);

        // Credit: Asset Account (100M)
        $this->assertDatabaseHas('journal_entry_details', [
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $this->assetAccount->id,
            'debit' => 0,
            'credit' => 100000000
        ]);
    }

    /** @test */
    public function it_updates_account_balances_after_disposal()
    {
        $this->actingAs($this->user);

        $initialPaymentBalance = $this->paymentAccount->balance;
        $initialAssetBalance = $this->assetAccount->balance;
        $initialAccumulatedBalance = $this->accumulatedDepreciationAccount->balance;

        $disposalData = [
            'disposal_date' => now()->format('Y-m-d'),
            'disposal_value' => 90000000,
            'disposal_notes' => 'Sold',
            'gain_loss_account_id' => $this->gainAccount->id
        ];

        $response = $this->postJson("/finance/fixed-assets/{$this->asset->id}/dispose", $disposalData);

        $response->assertStatus(200);

        // Refresh balances
        $this->paymentAccount->refresh();
        $this->assetAccount->refresh();
        $this->accumulatedDepreciationAccount->refresh();
        $this->gainAccount->refresh();

        // Payment account increased by disposal value
        $this->assertEquals($initialPaymentBalance + 90000000, $this->paymentAccount->balance);

        // Asset account decreased by acquisition cost
        $this->assertEquals($initialAssetBalance - 100000000, $this->assetAccount->balance);

        // Accumulated depreciation decreased
        $this->assertEquals($initialAccumulatedBalance - 18000000, $this->accumulatedDepreciationAccount->balance);

        // Gain account increased
        $this->assertEquals(8000000, $this->gainAccount->balance);
    }

    /** @test */
    public function it_handles_disposal_at_exact_book_value()
    {
        $this->actingAs($this->user);

        // Disposal value = book value = no gain/loss
        $disposalData = [
            'disposal_date' => now()->format('Y-m-d'),
            'disposal_value' => 82000000, // Exact book value
            'disposal_notes' => 'Sold at book value'
        ];

        $response = $this->postJson("/finance/fixed-assets/{$this->asset->id}/dispose", $disposalData);

        $response->assertStatus(200);

        $journalEntry = JournalEntry::where('reference_type', 'fixed_asset_disposal')
            ->where('reference_number', 'AST-001')
            ->first();

        $this->assertNotNull($journalEntry);

        // Should have exactly 3 entries (no gain/loss entry)
        $entryCount = JournalEntryDetail::where('journal_entry_id', $journalEntry->id)->count();
        $this->assertEquals(3, $entryCount);
    }
}
