<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JamaahBooking;
use Illuminate\Support\Facades\DB;

echo "=== CHECKING BOOKING ID 48 ===\n\n";

// Check if booking exists
$booking = JamaahBooking::find(48);

if ($booking) {
    echo "✓ Booking ID 48 FOUND\n";
    echo "  Booking Code: {$booking->booking_code}\n";
    echo "  Status: {$booking->status}\n";
    echo "  Payment Status: {$booking->payment_status}\n";
    
    $jamaahName = $booking->jamaah ? $booking->jamaah->nama : 'N/A';
    echo "  Jamaah: {$jamaahName}\n";
    
    $packageName = $booking->travelPackage ? $booking->travelPackage->package_name : 'N/A';
    echo "  Package: {$packageName}\n";
    
    // Check related data
    echo "\n--- Related Data ---\n";
    
    $payments = DB::table('jamaah_payments')->where('id_jamaah_booking', 48)->count();
    echo "  Payments: {$payments}\n";
    
    $addons = DB::table('booking_addons')->where('id_jamaah_booking', 48)->count();
    echo "  Addons: {$addons}\n";
    
    $hotelBookings = DB::table('jamaah_hotel_bookings')->where('id_jamaah_booking', 48)->count();
    echo "  Hotel Bookings: {$hotelBookings}\n";
    
    $documents = DB::table('jamaah_documents')->where('id_jamaah_booking', 48)->count();
    echo "  Documents: {$documents}\n";
    
    $piutang = DB::table('piutang')->where('id_jamaah_booking', 48)->count();
    echo "  Piutang: {$piutang}\n";
    
    $referrals = DB::table('affiliate_referrals')->where('id_jamaah_booking', 48)->count();
    echo "  Affiliate Referrals: {$referrals}\n";
    
    $voucherUsages = DB::table('voucher_usages')->where('id_jamaah_booking', 48)->count();
    echo "  Voucher Usages: {$voucherUsages}\n";
    
} else {
    echo "✗ Booking ID 48 NOT FOUND in database\n";
    echo "  This booking has been permanently deleted or never existed.\n\n";
    
    // Check if there are orphaned related records
    echo "--- Checking for Orphaned Records ---\n";
    
    $payments = DB::table('jamaah_payments')->where('id_jamaah_booking', 48)->count();
    if ($payments > 0) {
        echo "  ⚠️ Found {$payments} orphaned payments\n";
    }
    
    $addons = DB::table('booking_addons')->where('id_jamaah_booking', 48)->count();
    if ($addons > 0) {
        echo "  ⚠️ Found {$addons} orphaned addons\n";
    }
    
    $hotelBookings = DB::table('jamaah_hotel_bookings')->where('id_jamaah_booking', 48)->count();
    if ($hotelBookings > 0) {
        echo "  ⚠️ Found {$hotelBookings} orphaned hotel bookings\n";
    }
    
    $documents = DB::table('jamaah_documents')->where('id_jamaah_booking', 48)->count();
    if ($documents > 0) {
        echo "  ⚠️ Found {$documents} orphaned documents\n";
    }
    
    $piutang = DB::table('piutang')->where('id_jamaah_booking', 48)->count();
    if ($piutang > 0) {
        echo "  ⚠️ Found {$piutang} orphaned piutang\n";
    }
    
    $referrals = DB::table('affiliate_referrals')->where('booking_id', 48)->count();
    if ($referrals > 0) {
        echo "  ⚠️ Found {$referrals} orphaned affiliate referrals\n";
    }
    
    $voucherUsages = DB::table('voucher_usages')->where('id_jamaah_booking', 48)->count();
    if ($voucherUsages > 0) {
        echo "  ⚠️ Found {$voucherUsages} orphaned voucher usages\n";
    }
    
    if ($payments + $addons + $hotelBookings + $documents + $piutang + $referrals + $voucherUsages == 0) {
        echo "  ✓ No orphaned records found\n";
    }
}

echo "\n=== CHECK COMPLETE ===\n";
