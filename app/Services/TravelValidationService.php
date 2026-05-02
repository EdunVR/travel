<?php

namespace App\Services;

use App\Models\TravelPackage;
use App\Models\Member;
use App\Models\JamaahBooking;
use App\Models\Keberangkatan;
use App\Models\Flight;
use App\Models\Hotel;
use Carbon\Carbon;

/**
 * Centralized validation service for travel management system
 * Implements all validation rules from Requirements 17.1-17.10
 */
class TravelValidationService
{
    /**
     * Validate departure date is in the future
     * Requirement 17.1
     */
    public function validateDepartureDate($departureDate): array
    {
        $date = Carbon::parse($departureDate);
        
        if ($date->isPast() || $date->isToday()) {
            return [
                'valid' => false,
                'message' => 'Tanggal keberangkatan harus di masa depan',
                'code' => 'INVALID_DEPARTURE_DATE'
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate jamaah age requirements
     * Requirement 17.2
     */
    public function validateJamaahAge(Member $member, string $packageType): array
    {
        if (!$member->ktp_tanggal_lahir) {
            return [
                'valid' => false,
                'message' => 'Tanggal lahir jamaah harus diisi',
                'code' => 'MISSING_BIRTH_DATE'
            ];
        }

        $age = Carbon::parse($member->ktp_tanggal_lahir)->age;
        
        if ($packageType === 'umrah' && $age < 12) {
            return [
                'valid' => false,
                'message' => 'Jamaah harus berusia minimal 12 tahun untuk Umrah',
                'code' => 'AGE_REQUIREMENT_NOT_MET',
                'data' => ['current_age' => $age, 'required_age' => 12]
            ];
        }
        
        if ($packageType === 'hajj' && $age < 18) {
            return [
                'valid' => false,
                'message' => 'Jamaah harus berusia minimal 18 tahun untuk Hajj',
                'code' => 'AGE_REQUIREMENT_NOT_MET',
                'data' => ['current_age' => $age, 'required_age' => 18]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate mahram requirement for female jamaah under 45
     * Requirement 17.3
     */
    public function validateMahramRequirement(Member $member): array
    {
        if ($member->gender !== 'female' || !$member->ktp_tanggal_lahir) {
            return ['valid' => true];
        }

        $age = Carbon::parse($member->ktp_tanggal_lahir)->age;
        
        if ($age < 45 && empty($member->mahram_name)) {
            return [
                'valid' => false,
                'message' => 'Jamaah wanita di bawah 45 tahun harus memiliki mahram terdaftar',
                'code' => 'MAHRAM_REQUIRED',
                'data' => ['age' => $age]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate package pricing against HPP
     * Requirement 17.4
     */
    public function validatePackagePricing(TravelPackage $package): array
    {
        if (!$package->hpp || $package->hpp == 0) {
            return ['valid' => true]; // No HPP to compare against
        }

        if ($package->price <= $package->hpp) {
            return [
                'valid' => false,
                'message' => 'Peringatan: Harga jual lebih rendah atau sama dengan HPP. Margin keuntungan rendah atau negatif.',
                'code' => 'LOW_PROFIT_MARGIN',
                'warning' => true, // This is a warning, not a blocking error
                'data' => [
                    'price' => $package->price,
                    'hpp' => $package->hpp,
                    'margin' => $package->profit_margin
                ]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate payment amount doesn't exceed remaining balance
     * Requirement 17.5
     */
    public function validatePaymentAmount(JamaahBooking $booking, float $amount): array
    {
        if ($amount <= 0) {
            return [
                'valid' => false,
                'message' => 'Jumlah pembayaran harus lebih dari 0',
                'code' => 'INVALID_PAYMENT_AMOUNT'
            ];
        }

        if ($amount > $booking->remaining_amount) {
            return [
                'valid' => false,
                'message' => 'Jumlah pembayaran melebihi sisa tagihan',
                'code' => 'PAYMENT_EXCEEDS_BALANCE',
                'data' => [
                    'requested_amount' => $amount,
                    'remaining_balance' => $booking->remaining_amount
                ]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate flight capacity
     * Requirement 17.6
     */
    public function validateFlightCapacity(Flight $flight, int $requestedSeats): array
    {
        $availableSeats = $flight->getAvailableSeats();
        
        if ($requestedSeats > $availableSeats) {
            return [
                'valid' => false,
                'message' => "Tidak dapat memesan {$requestedSeats} kursi. Hanya {$availableSeats} kursi tersedia.",
                'code' => 'INSUFFICIENT_FLIGHT_CAPACITY',
                'data' => [
                    'requested' => $requestedSeats,
                    'available' => $availableSeats,
                    'total_capacity' => $flight->capacity
                ]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate hotel room capacity
     * Requirement 17.6
     */
    public function validateHotelCapacity(Hotel $hotel, int $requestedRooms, $checkIn, $checkOut): array
    {
        // Calculate available rooms for the date range
        $existingBookings = \App\Models\HotelBooking::where('id_hotel', $hotel->id)
            ->where(function($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                  ->orWhere(function($q2) use ($checkIn, $checkOut) {
                      $q2->where('check_in_date', '<=', $checkIn)
                         ->where('check_out_date', '>=', $checkOut);
                  });
            })
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->sum('room_count');

        $availableRooms = $hotel->total_rooms - $existingBookings;
        
        if ($requestedRooms > $availableRooms) {
            return [
                'valid' => false,
                'message' => "Tidak dapat memesan {$requestedRooms} kamar. Hanya {$availableRooms} kamar tersedia untuk tanggal yang dipilih.",
                'code' => 'INSUFFICIENT_HOTEL_CAPACITY',
                'data' => [
                    'requested' => $requestedRooms,
                    'available' => $availableRooms,
                    'total_capacity' => $hotel->total_rooms
                ]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate deletion of package with active bookings
     * Requirement 17.7
     */
    public function validatePackageDeletion(TravelPackage $package): array
    {
        $activeBookings = $package->jamaahBookings()
            ->whereNotIn('status', ['cancelled'])
            ->count();

        if ($activeBookings > 0) {
            return [
                'valid' => false,
                'message' => 'Tidak dapat menghapus paket: terdapat booking aktif',
                'code' => 'ACTIVE_BOOKINGS_EXIST',
                'data' => ['active_bookings_count' => $activeBookings]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate deletion of flight with active bookings
     * Requirement 17.7
     */
    public function validateFlightDeletion(Flight $flight): array
    {
        $activeBookings = $flight->bookings()
            ->whereIn('status', ['confirmed', 'ticketed'])
            ->count();

        if ($activeBookings > 0) {
            return [
                'valid' => false,
                'message' => 'Tidak dapat menghapus penerbangan: terdapat booking aktif',
                'code' => 'ACTIVE_BOOKINGS_EXIST',
                'data' => ['active_bookings_count' => $activeBookings]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate deletion of hotel with active bookings
     * Requirement 17.7
     */
    public function validateHotelDeletion(Hotel $hotel): array
    {
        $activeBookings = $hotel->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        if ($activeBookings > 0) {
            return [
                'valid' => false,
                'message' => 'Tidak dapat menghapus hotel: terdapat booking aktif',
                'code' => 'ACTIVE_BOOKINGS_EXIST',
                'data' => ['active_bookings_count' => $activeBookings]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate deletion of keberangkatan with confirmed jamaah
     * Requirement 17.8
     */
    public function validateKeberangkatanDeletion(Keberangkatan $keberangkatan): array
    {
        $confirmedJamaah = $keberangkatan->jamaahBookings()
            ->whereIn('status', ['confirmed', 'paid', 'departed'])
            ->count();

        if ($confirmedJamaah > 0) {
            return [
                'valid' => false,
                'message' => 'Tidak dapat menghapus keberangkatan: terdapat jamaah yang sudah dikonfirmasi',
                'code' => 'CONFIRMED_JAMAAH_EXIST',
                'data' => ['confirmed_jamaah_count' => $confirmedJamaah]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate document completion before stage completion
     * Requirement 17.9
     */
    public function validateDocumentCompletion(Keberangkatan $keberangkatan): array
    {
        $requiredDocTypes = ['passport', 'visa', 'ticket', 'insurance', 'health_certificate'];
        $incompleteJamaah = [];

        foreach ($keberangkatan->jamaahBookings as $booking) {
            $missingDocs = [];
            
            foreach ($requiredDocTypes as $docType) {
                $doc = $booking->documents()->where('document_type', $docType)->first();
                if (!$doc || $doc->status !== 'approved') {
                    $missingDocs[] = $docType;
                }
            }
            
            if (!empty($missingDocs)) {
                $incompleteJamaah[] = [
                    'jamaah_name' => $booking->jamaah->nama,
                    'missing_documents' => $missingDocs
                ];
            }
        }

        if (!empty($incompleteJamaah)) {
            return [
                'valid' => false,
                'message' => 'Tidak semua jamaah memiliki dokumen lengkap yang disetujui',
                'code' => 'INCOMPLETE_DOCUMENTS',
                'data' => ['incomplete_jamaah' => $incompleteJamaah]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate passport expiry (6 months rule)
     * Requirement 17.10
     */
    public function validatePassportExpiry(Member $member, $departureDate): array
    {
        if (!$member->passport_tanggal_kadaluarsa) {
            return [
                'valid' => false,
                'message' => 'Tanggal kadaluarsa passport harus diisi',
                'code' => 'MISSING_PASSPORT_EXPIRY'
            ];
        }

        $expiryDate = Carbon::parse($member->passport_tanggal_kadaluarsa);
        $minExpiryDate = Carbon::parse($departureDate)->addMonths(6);
        
        if ($expiryDate->lt($minExpiryDate)) {
            return [
                'valid' => false,
                'message' => 'Passport harus berlaku minimal 6 bulan dari tanggal keberangkatan',
                'code' => 'PASSPORT_EXPIRY_TOO_SOON',
                'data' => [
                    'expiry_date' => $expiryDate->format('Y-m-d'),
                    'required_expiry_date' => $minExpiryDate->format('Y-m-d')
                ]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Validate KTP NIK format (16 digits)
     * Requirement 17.10
     */
    public function validateKtpNik(?string $nik): array
    {
        if (empty($nik)) {
            return [
                'valid' => false,
                'message' => 'NIK KTP harus diisi',
                'code' => 'MISSING_KTP_NIK'
            ];
        }

        if (!preg_match('/^\d{16}$/', $nik)) {
            return [
                'valid' => false,
                'message' => 'NIK KTP harus terdiri dari 16 digit angka',
                'code' => 'INVALID_KTP_NIK_FORMAT',
                'data' => ['current_length' => strlen($nik)]
            ];
        }
        
        return ['valid' => true];
    }

    /**
     * Comprehensive jamaah validation
     * Combines multiple validation rules
     */
    public function validateJamaahForBooking(Member $member, TravelPackage $package): array
    {
        $errors = [];

        // Age validation
        $ageValidation = $this->validateJamaahAge($member, $package->package_type);
        if (!$ageValidation['valid']) {
            $errors[] = $ageValidation;
        }

        // Mahram validation
        $mahramValidation = $this->validateMahramRequirement($member);
        if (!$mahramValidation['valid']) {
            $errors[] = $mahramValidation;
        }

        // Passport expiry validation
        $passportValidation = $this->validatePassportExpiry($member, $package->departure_date);
        if (!$passportValidation['valid']) {
            $errors[] = $passportValidation;
        }

        // KTP NIK validation
        $ktpValidation = $this->validateKtpNik($member->ktp_nik);
        if (!$ktpValidation['valid']) {
            $errors[] = $ktpValidation;
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'errors' => $errors
            ];
        }

        return ['valid' => true];
    }
}
