<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TravelPackage;
use App\Models\Member;
use App\Models\JamaahBooking;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Keberangkatan;
use App\Services\TravelValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ValidationRulesTest extends TestCase
{
    use RefreshDatabase;

    protected $validationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationService = new TravelValidationService();
    }

    /** @test */
    public function test_departure_date_must_be_in_future()
    {
        // Test with past date
        $pastDate = Carbon::yesterday();
        $result = $this->validationService->validateDepartureDate($pastDate);
        $this->assertFalse($result['valid']);
        $this->assertEquals('INVALID_DEPARTURE_DATE', $result['code']);

        // Test with today
        $today = Carbon::today();
        $result = $this->validationService->validateDepartureDate($today);
        $this->assertFalse($result['valid']);

        // Test with future date
        $futureDate = Carbon::tomorrow();
        $result = $this->validationService->validateDepartureDate($futureDate);
        $this->assertTrue($result['valid']);
    }

    /** @test */
    public function test_age_validation_for_umrah()
    {
        $member = new Member([
            'ktp_tanggal_lahir' => Carbon::now()->subYears(10)->format('Y-m-d')
        ]);

        $result = $this->validationService->validateJamaahAge($member, 'umrah');
        $this->assertFalse($result['valid']);
        $this->assertEquals('AGE_REQUIREMENT_NOT_MET', $result['code']);
        $this->assertEquals(10, $result['data']['current_age']);
        $this->assertEquals(12, $result['data']['required_age']);

        // Test valid age
        $member->ktp_tanggal_lahir = Carbon::now()->subYears(15)->format('Y-m-d');
        $result = $this->validationService->validateJamaahAge($member, 'umrah');
        $this->assertTrue($result['valid']);
    }

    /** @test */
    public function test_age_validation_for_hajj()
    {
        $member = new Member([
            'ktp_tanggal_lahir' => Carbon::now()->subYears(16)->format('Y-m-d')
        ]);

        $result = $this->validationService->validateJamaahAge($member, 'hajj');
        $this->assertFalse($result['valid']);
        $this->assertEquals('AGE_REQUIREMENT_NOT_MET', $result['code']);
        $this->assertEquals(16, $result['data']['current_age']);
        $this->assertEquals(18, $result['data']['required_age']);

        // Test valid age
        $member->ktp_tanggal_lahir = Carbon::now()->subYears(20)->format('Y-m-d');
        $result = $this->validationService->validateJamaahAge($member, 'hajj');
        $this->assertTrue($result['valid']);
    }

    /** @test */
    public function test_mahram_requirement_for_female_under_45()
    {
        $member = new Member([
            'gender' => 'female',
            'ktp_tanggal_lahir' => Carbon::now()->subYears(30)->format('Y-m-d'),
            'mahram_name' => null
        ]);

        $result = $this->validationService->validateMahramRequirement($member);
        $this->assertFalse($result['valid']);
        $this->assertEquals('MAHRAM_REQUIRED', $result['code']);

        // Test with mahram
        $member->mahram_name = 'John Doe';
        $result = $this->validationService->validateMahramRequirement($member);
        $this->assertTrue($result['valid']);

        // Test female over 45 (no mahram required)
        $member->ktp_tanggal_lahir = Carbon::now()->subYears(50)->format('Y-m-d');
        $member->mahram_name = null;
        $result = $this->validationService->validateMahramRequirement($member);
        $this->assertTrue($result['valid']);

        // Test male (no mahram required)
        $member->gender = 'male';
        $member->ktp_tanggal_lahir = Carbon::now()->subYears(30)->format('Y-m-d');
        $result = $this->validationService->validateMahramRequirement($member);
        $this->assertTrue($result['valid']);
    }

    /** @test */
    public function test_package_pricing_validation()
    {
        $package = new TravelPackage([
            'price' => 10000000,
            'hpp' => 12000000
        ]);

        $result = $this->validationService->validatePackagePricing($package);
        $this->assertFalse($result['valid']);
        $this->assertEquals('LOW_PROFIT_MARGIN', $result['code']);
        $this->assertTrue($result['warning']); // This is a warning, not blocking

        // Test valid pricing
        $package->price = 15000000;
        $result = $this->validationService->validatePackagePricing($package);
        $this->assertTrue($result['valid']);
    }

    /** @test */
    public function test_payment_amount_validation()
    {
        $booking = new JamaahBooking([
            'total_price' => 10000000,
            'paid_amount' => 3000000,
            'remaining_amount' => 7000000
        ]);

        // Test exceeding balance
        $result = $this->validationService->validatePaymentAmount($booking, 8000000);
        $this->assertFalse($result['valid']);
        $this->assertEquals('PAYMENT_EXCEEDS_BALANCE', $result['code']);

        // Test valid payment
        $result = $this->validationService->validatePaymentAmount($booking, 5000000);
        $this->assertTrue($result['valid']);

        // Test zero payment
        $result = $this->validationService->validatePaymentAmount($booking, 0);
        $this->assertFalse($result['valid']);
        $this->assertEquals('INVALID_PAYMENT_AMOUNT', $result['code']);
    }

    /** @test */
    public function test_ktp_nik_format_validation()
    {
        // Test invalid length
        $result = $this->validationService->validateKtpNik('123456789');
        $this->assertFalse($result['valid']);
        $this->assertEquals('INVALID_KTP_NIK_FORMAT', $result['code']);

        // Test non-numeric
        $result = $this->validationService->validateKtpNik('123456789012345A');
        $this->assertFalse($result['valid']);

        // Test valid NIK
        $result = $this->validationService->validateKtpNik('1234567890123456');
        $this->assertTrue($result['valid']);

        // Test empty NIK
        $result = $this->validationService->validateKtpNik(null);
        $this->assertFalse($result['valid']);
        $this->assertEquals('MISSING_KTP_NIK', $result['code']);
    }

    /** @test */
    public function test_passport_expiry_validation()
    {
        $departureDate = Carbon::now()->addMonths(3);
        
        $member = new Member([
            'passport_tanggal_kadaluarsa' => Carbon::now()->addMonths(5)->format('Y-m-d')
        ]);

        // Passport expires in 5 months, but departure is in 3 months
        // So passport will be valid for only 2 months after departure (< 6 months required)
        $result = $this->validationService->validatePassportExpiry($member, $departureDate);
        $this->assertFalse($result['valid']);
        $this->assertEquals('PASSPORT_EXPIRY_TOO_SOON', $result['code']);

        // Test valid passport (expires in 12 months)
        $member->passport_tanggal_kadaluarsa = Carbon::now()->addMonths(12)->format('Y-m-d');
        $result = $this->validationService->validatePassportExpiry($member, $departureDate);
        $this->assertTrue($result['valid']);
    }

    /** @test */
    public function test_comprehensive_jamaah_validation()
    {
        $package = new TravelPackage([
            'package_type' => 'umrah',
            'departure_date' => Carbon::now()->addMonths(3)
        ]);

        $member = new Member([
            'gender' => 'female',
            'ktp_tanggal_lahir' => Carbon::now()->subYears(30)->format('Y-m-d'),
            'ktp_nik' => '1234567890123456',
            'passport_tanggal_kadaluarsa' => Carbon::now()->addMonths(12)->format('Y-m-d'),
            'mahram_name' => 'John Doe'
        ]);

        $result = $this->validationService->validateJamaahForBooking($member, $package);
        $this->assertTrue($result['valid']);

        // Test with missing mahram
        $member->mahram_name = null;
        $result = $this->validationService->validateJamaahForBooking($member, $package);
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
    }
}
