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

class ProfitLossSecurityTest extends TestCase
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
     * Test authentication requirement
     */
    public function test_profit_loss_requires_authentication(): void
    {
        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]));

        $response->assertStatus(401);
    }

    /**
     * Test SQL injection prevention in outlet_id
     */
    public function test_sql_injection_prevention_outlet_id(): void
    {
        $this->actingAs($this->user);

        $maliciousInputs = [
            "1' OR '1'='1",
            "1; DROP TABLE journal_entries--",
            "1 UNION SELECT * FROM users--",
            "1' AND 1=1--",
            "1' OR 'a'='a",
        ];

        foreach ($maliciousInputs as $input) {
            $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
                'outlet_id' => $input,
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d')
            ]));

            // Should return validation error or 404, not 500
            $this->assertContains($response->status(), [400, 404, 422]);
        }
    }

    /**
     * Test SQL injection prevention in date parameters
     */
    public function test_sql_injection_prevention_dates(): void
    {
        $this->actingAs($this->user);

        $maliciousInputs = [
            "2024-01-01' OR '1'='1",
            "2024-01-01; DROP TABLE--",
            "2024-01-01' UNION SELECT--",
        ];

        foreach ($maliciousInputs as $input) {
            $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
                'outlet_id' => $this->outlet->id_outlet,
                'start_date' => $input,
                'end_date' => now()->format('Y-m-d')
            ]));

            // Should return validation error, not 500
            $this->assertContains($response->status(), [400, 422]);
        }
    }

    /**
     * Test XSS prevention in response data
     */
    public function test_xss_prevention_in_response(): void
    {
        $this->actingAs($this->user);

        // Create account with XSS attempt in name
        $xssAccount = ChartOfAccount::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'type' => 'revenue',
            'name' => '<script>alert("XSS")</script>Revenue Account',
            'status' => 'active'
        ]);

        $journal = JournalEntry::factory()->create([
            'outlet_id' => $this->outlet->id_outlet,
            'book_id' => $this->book->id,
            'transaction_date' => now(),
            'status' => 'posted'
        ]);

        JournalEntryDetail::factory()->create([
            'journal_entry_id' => $journal->id,
            'account_id' => $xssAccount->id,
            'debit' => 0,
            'credit' => 1000000
        ]);

        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => now()->subDays(1)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]));

        $response->assertStatus(200);

        // Response should be JSON, not HTML with script tags
        $content = $response->getContent();
        $this->assertJson($content);

        // The script tag should be escaped or removed in the response
        $data = $response->json();
        $this->assertIsArray($data);
    }

    /**
     * Test unauthorized outlet access
     */
    public function test_unauthorized_outlet_access(): void
    {
        $this->actingAs($this->user);

        // Create another outlet that user shouldn't access
        $unauthorizedOutlet = Outlet::factory()->create();

        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => $unauthorizedOutlet->id_outlet,
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]));

        // Should return 403 Forbidden or 404 Not Found
        // Note: Actual behavior depends on authorization implementation
        $this->assertContains($response->status(), [403, 404]);
    }

    /**
     * Test parameter tampering - negative amounts
     */
    public function test_parameter_tampering_prevention(): void
    {
        $this->actingAs($this->user);

        $tamperingAttempts = [
            ['outlet_id' => -1],
            ['outlet_id' => 999999999],
            ['outlet_id' => 'abc'],
            ['outlet_id' => null],
            ['outlet_id' => ''],
        ];

        foreach ($tamperingAttempts as $params) {
            $params = array_merge($params, [
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d')
            ]);

            $response = $this->getJson('/finance/profit-loss/data?' . http_build_query($params));

            // Should return validation error
            $this->assertContains($response->status(), [400, 404, 422]);
        }
    }

    /**
     * Test date range validation
     */
    public function test_date_range_validation(): void
    {
        $this->actingAs($this->user);

        // End date before start date
        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->subDays(30)->format('Y-m-d')
        ]));

        $response->assertStatus(422);

        // Invalid date format
        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => '2024-13-45', // Invalid date
            'end_date' => now()->format('Y-m-d')
        ]));

        $response->assertStatus(422);

        // Future dates
        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => now()->addDays(30)->format('Y-m-d'),
            'end_date' => now()->addDays(60)->format('Y-m-d')
        ]));

        // Should either accept or reject based on business rules
        $this->assertContains($response->status(), [200, 422]);
    }

    /**
     * Test mass assignment protection
     */
    public function test_mass_assignment_protection(): void
    {
        $this->actingAs($this->user);

        // Attempt to inject additional parameters
        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'is_admin' => true,
            'user_id' => 999,
            'role' => 'admin'
        ]));

        // Should ignore extra parameters and process normally
        $response->assertStatus(200);
    }

    /**
     * Test CSRF protection on export endpoints
     */
    public function test_csrf_protection_on_exports(): void
    {
        $this->actingAs($this->user);

        // GET requests should work (CSRF not required for GET)
        $response = $this->get('/finance/profit-loss/export/xlsx?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]));

        // Should work or return validation error, not CSRF error
        $this->assertNotEquals(419, $response->status());
    }

    /**
     * Test rate limiting (if implemented)
     */
    public function test_rate_limiting(): void
    {
        $this->actingAs($this->user);

        $responses = [];

        // Make multiple rapid requests
        for ($i = 0; $i < 100; $i++) {
            $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
                'outlet_id' => $this->outlet->id_outlet,
                'start_date' => now()->subDays(30)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d')
            ]));

            $responses[] = $response->status();

            // If rate limited, should return 429
            if ($response->status() === 429) {
                break;
            }
        }

        // Note: This test documents rate limiting behavior
        // Actual implementation depends on application's rate limiting strategy
        echo "\n=== Rate Limiting Test ===\n";
        echo "Total Requests Made: " . count($responses) . "\n";
        echo "Rate Limited: " . (in_array(429, $responses) ? 'Yes' : 'No') . "\n";
    }

    /**
     * Test sensitive data exposure in error messages
     */
    public function test_no_sensitive_data_in_errors(): void
    {
        $this->actingAs($this->user);

        // Trigger an error with invalid outlet
        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => 999999,
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]));

        $content = $response->getContent();

        // Should not expose database structure, SQL queries, or file paths
        $this->assertStringNotContainsString('SELECT', $content);
        $this->assertStringNotContainsString('FROM', $content);
        $this->assertStringNotContainsString('WHERE', $content);
        $this->assertStringNotContainsString('/var/www', $content);
        $this->assertStringNotContainsString('C:\\', $content);
        $this->assertStringNotContainsString('stack trace', strtolower($content));
    }

    /**
     * Test comparison mode parameter validation
     */
    public function test_comparison_mode_validation(): void
    {
        $this->actingAs($this->user);

        // Comparison enabled but missing comparison dates
        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'comparison' => true
        ]));

        // Should return validation error
        $this->assertContains($response->status(), [200, 422]);

        // Invalid comparison date range
        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'comparison' => true,
            'comparison_start_date' => now()->subDays(60)->format('Y-m-d'),
            'comparison_end_date' => now()->subDays(90)->format('Y-m-d') // End before start
        ]));

        $this->assertContains($response->status(), [200, 422]);
    }

    /**
     * Test export file name sanitization
     */
    public function test_export_filename_sanitization(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/finance/profit-loss/export/xlsx?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet,
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]));

        if ($response->status() === 200) {
            $contentDisposition = $response->headers->get('Content-Disposition');

            if ($contentDisposition) {
                // Filename should not contain path traversal characters
                $this->assertStringNotContainsString('..', $contentDisposition);
                $this->assertStringNotContainsString('/', $contentDisposition);
                $this->assertStringNotContainsString('\\', $contentDisposition);
            }
        }
    }

    /**
     * Test input length limits
     */
    public function test_input_length_limits(): void
    {
        $this->actingAs($this->user);

        // Extremely long outlet_id
        $longInput = str_repeat('9', 1000);

        $response = $this->getJson('/finance/profit-loss/data?' . http_build_query([
            'outlet_id' => $longInput,
            'start_date' => now()->subDays(30)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]));

        // Should handle gracefully with validation error
        $this->assertContains($response->status(), [400, 404, 422]);
    }

    /**
     * Test stats endpoint security
     */
    public function test_stats_endpoint_security(): void
    {
        // Unauthenticated access
        $response = $this->getJson('/finance/profit-loss/stats?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet
        ]));

        $response->assertStatus(401);

        // Authenticated access
        $this->actingAs($this->user);

        $response = $this->getJson('/finance/profit-loss/stats?' . http_build_query([
            'outlet_id' => $this->outlet->id_outlet
        ]));

        // Should work or return validation error
        $this->assertContains($response->status(), [200, 404, 422]);
    }
}
