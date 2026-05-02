<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Outlet;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\AccountingBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class ProfitLossLoadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $outlet;
    protected $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->outlet = Outlet::factory()->create();
        $this->book = AccountingBook::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'code' => 'BKU',
            'name' => 'Buku Umum'
        ]);
    }

    /**
     * Test concurrent requests to profit loss endpoint
     */
    public function test_concurrent_profit_loss_requests(): void
    {
        $this->actingAs($this->user);

        // Setup test data
        $this->setupTestData();

        $concurrentRequests = 10;
        $results = [];
        $startTime = microtime(true);

        // Simulate concurrent requests
        for ($i = 0; $i < $concurrentRequests; $i++) {
            $requestStart = microtime(true);

            $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
                'outlet_id' => $this->outlet->id_outlet,
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d')
            ]));

            $requestTime = microtime(true) - $requestStart;

            $results[] = [
                'status' => $response->status(),
                'time' => $requestTime
            ];

            $response->assertStatus(200);
        }

        $totalTime = microtime(true) - $startTime;
        $avgTime = array_sum(array_column($results, 'time')) / count($results);
        $maxTime = max(array_column($results, 'time'));
        $minTime = min(array_column($results, 'time'));

        echo "\n=== Concurrent Load Test Results ===\n";
        echo "Total Requests: " . $concurrentRequests . "\n";
        echo "Total Time: " . round($totalTime, 2) . " seconds\n";
        echo "Average Response Time: " . round($avgTime, 2) . " seconds\n";
        echo "Min Response Time: " . round($minTime, 2) . " seconds\n";
        echo "Max Response Time: " . round($maxTime, 2) . " seconds\n";
        echo "Requests per Second: " . round($concurrentRequests / $totalTime, 2) . "\n";

        // All requests should succeed
        $successCount = count(array_filter($results, fn($r) => $r['status'] === 200));
        $this->assertEquals($concurrentRequests, $successCount);

        // Average response time should be reasonable
        $this->assertLessThan(5, $avgTime, 'Average response time should be less than 5 seconds');
    }

    /**
     * Test multiple users accessing different outlets simultaneously
     */
    public function test_multi_user_multi_outlet_load(): void
    {
        // Create multiple users and outlets
        $users = User::factory()->count(5)->create();
        $outlets = Outlet::factory()->count(5)->create();

        foreach ($outlets as $outlet) {
            $book = AccountingBook::factory()->create([
                'outlet_id' => $outlet->id_outlet,
                'code' => 'BKU',
                'name' => 'Buku Umum'
            ]);

            // Create accounts for each outlet
            $revenueAccount = ChartOfAccount::factory()->create([
                'outlet_id' => $outlet->id_outlet,
                'type' => 'revenue',
                'status' => 'active'
            ]);

            $expenseAccount = ChartOfAccount::factory()->create([
                'outlet_id' => $outlet->id_outlet,
                'type' => 'expense',
                'status' => 'active'
            ]);

            // Create journals
            for ($i = 1; $i <= 50; $i++) {
                $journal = JournalEntry::factory()->create([
                    'outlet_id' => $outlet->id_outlet,
                    'book_id' => $book->id,
                    'transaction_date' => now()->subDays(rand(1, 30)),
                    'status' => 'posted'
                ]);

                JournalEntryDetail::factory()->create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $revenueAccount->id,
                    'debit' => 0,
                    'credit' => rand(100000, 1000000)
                ]);

                JournalEntryDetail::factory()->create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $expenseAccount->id,
                    'debit' => rand(50000, 500000),
                    'credit' => 0
                ]);
            }
        }

        $results = [];
        $startTime = microtime(true);

        // Each user accesses their outlet
        foreach ($users as $index => $user) {
            $this->actingAs($user);
            $outlet = $outlets[$index];

            $requestStart = microtime(true);

            $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
                'outlet_id' => $outlet->id_outlet,
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d')
            ]));

            $requestTime = microtime(true) - $requestStart;

            $results[] = [
                'user_id' => $user->id,
                'outlet_id' => $outlet->id_outlet,
                'status' => $response->status(),
                'time' => $requestTime
            ];

            $response->assertStatus(200);
        }

        $totalTime = microtime(true) - $startTime;

        echo "\n=== Multi-User Multi-Outlet Load Test ===\n";
        echo "Total Users: " . count($users) . "\n";
        echo "Total Outlets: " . count($outlets) . "\n";
        echo "Total Time: " . round($totalTime, 2) . " seconds\n";

        foreach ($results as $result) {
            echo "User {$result['user_id']} - Outlet {$result['outlet_id']}: " . 
                 round($result['time'], 2) . "s\n";
        }

        // All requests should succeed
        $successCount = count(array_filter($results, fn($r) => $r['status'] === 200));
        $this->assertEquals(count($users), $successCount);
    }

    /**
     * Test stress with rapid filter changes
     */
    public function test_rapid_filter_changes(): void
    {
        $this->actingAs($this->user);
        $this->setupTestData();

        $periods = [
            ['days' => 7, 'label' => 'Last 7 days'],
            ['days' => 30, 'label' => 'Last 30 days'],
            ['days' => 90, 'label' => 'Last 90 days'],
            ['days' => 180, 'label' => 'Last 180 days'],
            ['days' => 365, 'label' => 'Last 365 days'],
        ];

        $results = [];
        $startTime = microtime(true);

        foreach ($periods as $period) {
            $requestStart = microtime(true);

            $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
                'outlet_id' => $this->outlet->id_outlet,
                'start_date' => now()->subDays($period['days'])->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d')
            ]));

            $requestTime = microtime(true) - $requestStart;

            $results[] = [
                'period' => $period['label'],
                'time' => $requestTime,
                'status' => $response->status()
            ];

            $response->assertStatus(200);
        }

        $totalTime = microtime(true) - $startTime;

        echo "\n=== Rapid Filter Changes Test ===\n";
        echo "Total Periods Tested: " . count($periods) . "\n";
        echo "Total Time: " . round($totalTime, 2) . " seconds\n";

        foreach ($results as $result) {
            echo "{$result['period']}: " . round($result['time'], 2) . "s\n";
        }

        // All requests should succeed
        $successCount = count(array_filter($results, fn($r) => $r['status'] === 200));
        $this->assertEquals(count($periods), $successCount);
    }

    /**
     * Test cache effectiveness under load
     */
    public function test_cache_effectiveness_under_load(): void
    {
        $this->actingAs($this->user);
        $this->setupTestData();

        $params = [
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ];

        // First request (cache miss)
        Cache::flush();
        $firstRequestStart = microtime(true);
        $firstResponse = $this->getJson('/finance/profit-loss/data?' . http_build_query($params));
        $firstRequestTime = microtime(true) - $firstRequestStart;

        $firstResponse->assertStatus(200);

        // Subsequent requests (should be faster if cached)
        $cachedTimes = [];
        for ($i = 0; $i < 5; $i++) {
            $cachedStart = microtime(true);
            $cachedResponse = $this->getJson('/finance/profit-loss/data?' . http_build_query($params));
            $cachedTimes[] = microtime(true) - $cachedStart;

            $cachedResponse->assertStatus(200);
        }

        $avgCachedTime = array_sum($cachedTimes) / count($cachedTimes);

        echo "\n=== Cache Effectiveness Test ===\n";
        echo "First Request (Cache Miss): " . round($firstRequestTime, 4) . " seconds\n";
        echo "Average Cached Request: " . round($avgCachedTime, 4) . " seconds\n";
        echo "Speed Improvement: " . round(($firstRequestTime / $avgCachedTime), 2) . "x\n";

        // Note: This test shows cache potential, but actual caching implementation
        // depends on the application's caching strategy
    }

    /**
     * Test export under load
     */
    public function test_export_under_load(): void
    {
        $this->actingAs($this->user);
        $this->setupTestData();

        $exportTypes = ['xlsx', 'pdf'];
        $results = [];

        foreach ($exportTypes as $type) {
            $requestStart = microtime(true);

            $response = $this->get('/finance/profit-loss/export/' . $type . '?' . http_build_query([
                'outlet_id' => $this->outlet->id_outlet,
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d')
            ]));

            $requestTime = microtime(true) - $requestStart;

            $results[] = [
                'type' => strtoupper($type),
                'time' => $requestTime,
                'status' => $response->status()
            ];

            $response->assertStatus(200);
        }

        echo "\n=== Export Under Load Test ===\n";
        foreach ($results as $result) {
            echo "{$result['type']} Export: " . round($result['time'], 2) . " seconds\n";
        }

        // All exports should succeed
        $successCount = count(array_filter($results, fn($r) => $r['status'] === 200));
        $this->assertEquals(count($exportTypes), $successCount);
    }

    /**
     * Helper method to setup test data
     */
    private function setupTestData(): void
    {
        // Create accounts
        $revenueAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'revenue',
            'status' => 'active'
        ]);

        $expenseAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'expense',
            'status' => 'active'
        ]);

        // Create journals
        for ($i = 1; $i <= 100; $i++) {
            $journal = JournalEntry::factory()->create([
                'outlet_id' => $this->outlet->id_outlet,
                'book_id' => $this->book->id,
                'transaction_date' => now()->subDays(rand(1, 30)),
                'status' => 'posted'
            ]);

            JournalEntryDetail::factory()->create([
                'journal_entry_id' => $journal->id,
                'account_id' => $revenueAccount->id,
                'debit' => 0,
                'credit' => rand(100000, 1000000)
            ]);

            JournalEntryDetail::factory()->create([
                'journal_entry_id' => $journal->id,
                'account_id' => $expenseAccount->id,
                'debit' => rand(50000, 500000),
                'credit' => 0
            ]);
        }
    }
}
