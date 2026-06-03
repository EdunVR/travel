<?php

namespace App\Http\Controllers;

use App\Models\JamaahBooking;
use App\Models\TravelPackage;
use App\Models\Member;
use App\Models\Keberangkatan;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\HasOutletFilter;

class BookingController extends Controller
{
    use HasOutletFilter;
    
    public function __construct()
    {
        $this->middleware('permission:travel.booking.view')->only(['index', 'getData', 'show']);
        $this->middleware('permission:travel.booking.create')->only(['store']);
        $this->middleware('permission:travel.booking.update')->only(['update', 'updateStatus']);
        $this->middleware('permission:travel.booking.delete')->only(['destroy']);
    }

    /**
     * Display booking list page
     */
    public function index()
    {
        $outlets = Outlet::all();
        
        // Get pending payment verification count
        $pendingPaymentCount = \App\Models\JamaahPayment::pendingVerification()->count();
        
        return view('admin.travel.booking.index', compact('outlets', 'pendingPaymentCount'));
    }

    /**
     * Get bookings data for Alpine.js
     */
    public function getData(Request $request)
    {
        $query = JamaahBooking::with(['travelPackage.hppCalculation', 'jamaah', 'keberangkatan', 'outlet', 'addons', 'hotelBookings']);

        // Filter by outlet if user has outlet restriction
        if (Auth::user()->id_outlet) {
            $query->where('id_outlet', Auth::user()->id_outlet);
        }

        // Apply filters
        if ($request->filled('outlet_id')) {
            $query->where('id_outlet', $request->outlet_id);
        }

        if ($request->filled('package_id')) {
            $query->where('id_travel_package', $request->package_id);
        }

        if ($request->filled('keberangkatan_id')) {
            $query->where('id_keberangkatan', $request->keberangkatan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhereHas('jamaah', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('passport_nomor', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $data = $bookings->map(function($booking) {
            // Get realtime departure date from package
            $departureDate = $booking->travelPackage->departure_date 
                ? \Carbon\Carbon::parse($booking->travelPackage->departure_date)->format('d M Y')
                : '-';
            
            // Hitung grand total termasuk anggota keluarga
            $grandTotal = $this->calculateGrandTotal($booking);
            $voucherDiscount = $booking->voucher_discount ?? 0;
            $adminDiscount = $booking->admin_discount ?? 0;
            $finalTotal = $grandTotal - $voucherDiscount - $adminDiscount;
            $remaining = max(0, $finalTotal - $booking->paid_amount);
            
            // Calculate handling fee
            $handlingFee = 0;
            $basePrice = $grandTotal;
            if ($booking->travelPackage && $booking->travelPackage->include_handling_lounge_fee && $booking->travelPackage->handling_lounge_fee_amount > 0) {
                $handlingFee = $booking->travelPackage->handling_lounge_fee_amount;
                $basePrice = $grandTotal - $handlingFee; // Base price without handling fee
            }
            
            // Status badges
            $statusBadges = [
                'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Pending</span>',
                'confirmed' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Confirmed</span>',
                'paid' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Paid</span>',
                'departed' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">Departed</span>',
                'completed' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Completed</span>',
                'cancelled' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Cancelled</span>'
            ];
            
            $paymentBadges = [
                'unpaid' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Unpaid</span>',
                'partial' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Partial</span>',
                'paid' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Paid</span>'
            ];

            // HPP Dasar dari package hpp_calculation (flight + extras, tanpa hotel)
            // Dikalikan total pax (jamaah utama + anggota keluarga)
            $hppCalc = $booking->travelPackage->hppCalculation ?? null;
            $hppDasarPerOrang = $hppCalc
                ? (($hppCalc->flight_cost ?? 0) + ($hppCalc->transportation_cost ?? 0) + ($hppCalc->meal_cost ?? 0)
                   + ($hppCalc->visa_cost ?? 0) + ($hppCalc->guide_cost ?? 0) + ($hppCalc->insurance_cost ?? 0)
                   + ($hppCalc->operational_overhead ?? 0) + ($hppCalc->contingency ?? 0))
                : 0;

            // Family members count
            $familyMembers = $booking->jamaah->family_members ?? [];
            if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true) ?? [];
            $familyCount = is_array($familyMembers) ? count($familyMembers) : 0;
            $totalPax = 1 + $familyCount; // jamaah utama + anggota keluarga

            // HPP Dasar = per orang × total pax
            $hppDasar = $hppDasarPerOrang * $totalPax;

            return [
                'id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'jamaah_name' => $booking->jamaah->nama ?? '-',
                'jamaah_phone' => $booking->jamaah->telepon ?? '',
                'package_name' => $booking->travelPackage->package_name ?? '-',
                'id_travel_package' => $booking->id_travel_package,
                'price_package_name' => $booking->price_package_name ?? '',
                'price_variant' => $booking->price_variant ?? '',
                'room_type' => $booking->room_type ?? '',
                'keberangkatan_name' => $departureDate,
                'id_keberangkatan' => $booking->id_keberangkatan,
                'booking_date' => $booking->booking_date->format('d M Y'),
                'base_price' => $basePrice,
                'base_price_formatted' => 'Rp ' . number_format($basePrice, 0, ',', '.'),
                'handling_fee' => $handlingFee,
                'handling_fee_formatted' => 'Rp ' . number_format($handlingFee, 0, ',', '.'),
                'total_price' => $grandTotal,
                'total_price_formatted' => 'Rp ' . number_format($grandTotal, 0, ',', '.'),
                'voucher_code' => $booking->voucher_code ?? null,
                'voucher_discount' => $voucherDiscount,
                'voucher_discount_formatted' => 'Rp ' . number_format($voucherDiscount, 0, ',', '.'),
                'admin_discount' => $booking->admin_discount ?? 0,
                'admin_discount_formatted' => 'Rp ' . number_format($booking->admin_discount ?? 0, 0, ',', '.'),
                'custom_payment_amount' => $booking->custom_payment_amount ?? null,
                'custom_payment_amount_formatted' => $booking->custom_payment_amount ? 'Rp ' . number_format($booking->custom_payment_amount, 0, ',', '.') : null,
                'final_total' => $finalTotal,
                'final_total_formatted' => 'Rp ' . number_format($finalTotal, 0, ',', '.'),
                'paid_amount' => $booking->paid_amount,
                'total_pax' => $totalPax, // jamaah utama + anggota keluarga
                'paid_amount_formatted' => 'Rp ' . number_format($booking->paid_amount, 0, ',', '.'),
                'remaining_amount_formatted' => 'Rp ' . number_format($remaining, 0, ',', '.'),
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'status_badge' => $statusBadges[$booking->status] ?? '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">' . ucfirst($booking->status) . '</span>',
                'payment_status_badge' => $paymentBadges[$booking->payment_status] ?? '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">' . ucfirst($booking->payment_status) . '</span>',
                'hpp_dasar' => $hppDasar,
                'hpp_dasar_per_orang' => $hppDasarPerOrang,
                'total_pax' => $totalPax,
                'family_members_count' => $familyCount,
                'family_members_list' => is_array($familyMembers) ? $familyMembers : [],
                'hotel_bookings' => $booking->hotelBookings ? $booking->hotelBookings->map(fn($hb) => [
                    'id' => $hb->id,
                    'city_type' => $hb->city_type,
                    'is_charged' => $hb->is_charged,
                    'total_cost' => $hb->total_cost,
                    'price_per_night' => $hb->price_per_night,
                    'nights' => $hb->nights,
                ])->toArray() : [],
                'addons' => $booking->addons->map(fn($a) => [
                    'id' => $a->id, 'nama' => $a->nama, 'harga' => $a->harga,
                    'qty' => $a->qty, 'masuk_hpp' => $a->masuk_hpp
                ])->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a new booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_travel_package' => 'required|exists:travel_packages,id',
            'id_member' => 'required|exists:member,id_member',
            'id_keberangkatan' => 'nullable|exists:keberangkatan,id',
            'booking_date' => 'required|date',
            'total_price' => 'required|numeric|min:0',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'price_package_name' => 'nullable|string|max:100',
            'price_variant' => 'nullable|string|max:50',
            'room_type' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Get package and validate capacity
            $package = TravelPackage::findOrFail($validated['id_travel_package']);
            
            // Validate package capacity
            if ($package->isFull()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket sudah penuh. Tidak ada kursi tersedia.',
                    'code' => 'PACKAGE_FULL'
                ], 400);
            }

            // Get member and validate jamaah rules
            $member = Member::findOrFail($validated['id_member']);
            
            if (!$member->is_jamaah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member yang dipilih belum terdaftar sebagai jamaah.',
                    'code' => 'NOT_JAMAAH'
                ], 400);
            }

            // Validate age requirements based on package type
            if ($member->ktp_tanggal_lahir) {
                $age = \Carbon\Carbon::parse($member->ktp_tanggal_lahir)->age;
                
                if ($package->package_type === 'umrah' && $age < 12) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jamaah harus berusia minimal 12 tahun untuk Umrah',
                        'code' => 'AGE_REQUIREMENT_NOT_MET',
                        'data' => ['current_age' => $age, 'required_age' => 12]
                    ], 400);
                }
                
                if ($package->package_type === 'hajj' && $age < 18) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jamaah harus berusia minimal 18 tahun untuk Hajj',
                        'code' => 'AGE_REQUIREMENT_NOT_MET',
                        'data' => ['current_age' => $age, 'required_age' => 18]
                    ], 400);
                }
            }

            // Validate mahram requirement for female under 45
            if ($member->gender === 'female' && $member->ktp_tanggal_lahir) {
                $age = \Carbon\Carbon::parse($member->ktp_tanggal_lahir)->age;
                if ($age < 45 && empty($member->mahram_name)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jamaah wanita di bawah 45 tahun harus memiliki mahram terdaftar',
                        'code' => 'MAHRAM_REQUIRED',
                        'data' => ['age' => $age]
                    ], 400);
                }
            }

            // Validate jamaah business rules (passport expiry, KTP format, etc.)
            $validationErrors = $member->validateJamaahRules($package->departure_date);
            if (!empty($validationErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi jamaah gagal',
                    'code' => 'JAMAAH_VALIDATION_FAILED',
                    'errors' => $validationErrors
                ], 422);
            }

            // Validate keberangkatan capacity if specified
            if ($validated['id_keberangkatan']) {
                $keberangkatan = Keberangkatan::findOrFail($validated['id_keberangkatan']);
                
                if ($keberangkatan->isFull()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Keberangkatan sudah penuh. Tidak ada kapasitas tersedia.',
                        'code' => 'KEBERANGKATAN_FULL'
                    ], 400);
                }
            }

            // Generate booking code
            $bookingCode = JamaahBooking::generateBookingCode();

            // Create booking
            $booking = JamaahBooking::create([
                'booking_code' => $bookingCode,
                'id_travel_package' => $validated['id_travel_package'],
                'id_member' => $validated['id_member'],
                'id_keberangkatan' => $validated['id_keberangkatan'] ?? null,
                'booking_date' => $validated['booking_date'],
                'status' => 'pending',
                'total_price' => $validated['total_price'],
                'payment_status' => 'unpaid',
                'paid_amount' => 0,
                'remaining_amount' => $validated['total_price'],
                'id_outlet' => $validated['id_outlet'],
                'price_package_name' => $validated['price_package_name'] ?? null,
                'price_variant' => $validated['price_variant'] ?? null,
                'room_type' => $validated['price_variant'] ?? null, // room_type = variant (quad/triple/double)
            ]);

            // Update package status if full
            if ($package->isFull()) {
                $package->update(['status' => 'full']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => $booking->load(['travelPackage', 'jamaah', 'keberangkatan'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display booking details
     */
    public function show($id)
    {
        $booking = JamaahBooking::with([
            'travelPackage.hppCalculation',
            'travelPackage.flightDeparture',
            'travelPackage.flightReturn',
            'travelPackage.hotelMakkah',
            'travelPackage.hotelMadinah',
            'jamaah',
            'keberangkatan',
            'payments.recordedBy',
            'documents',
            'outlet',
            'invoice',
            'addons',
            'hotelBookings.hotel'
        ])->findOrFail($id);

        // Check outlet access
        if (Auth::user()->id_outlet && $booking->id_outlet != Auth::user()->id_outlet) {
            abort(403, 'Unauthorized access to this booking');
        }

        return view('admin.travel.booking.show', compact('booking'));
    }

    /**
     * Update booking
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_keberangkatan' => 'nullable|exists:keberangkatan,id',
            'status' => 'required|in:pending,confirmed,paid,departed,completed,cancelled',
            'total_price' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $booking = JamaahBooking::findOrFail($id);

            // Check outlet access
            if (Auth::user()->id_outlet && $booking->id_outlet != Auth::user()->id_outlet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this booking'
                ], 403);
            }

            // Validate status transition to departed requires payment completion
            if ($validated['status'] === 'departed' && $booking->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menandai sebagai berangkat. Pembayaran harus lunas terlebih dahulu.',
                    'code' => 'PAYMENT_NOT_COMPLETE',
                    'data' => [
                        'payment_status' => $booking->payment_status,
                        'remaining_amount' => $booking->remaining_amount
                    ]
                ], 400);
            }

            // Validate keberangkatan capacity if changing
            if ($validated['id_keberangkatan'] && $validated['id_keberangkatan'] != $booking->id_keberangkatan) {
                $keberangkatan = Keberangkatan::findOrFail($validated['id_keberangkatan']);
                
                if ($keberangkatan->isFull()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Keberangkatan sudah penuh. Tidak ada kapasitas tersedia.',
                        'code' => 'KEBERANGKATAN_FULL'
                    ], 400);
                }
            }

            // Update booking
            $booking->update($validated);

            // Update remaining amount if price changed
            if ($validated['total_price'] != $booking->total_price) {
                $booking->remaining_amount = $validated['total_price'] - $booking->paid_amount;
                $booking->save();
                $booking->updatePaymentStatus();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'data' => $booking->load(['travelPackage', 'jamaah', 'keberangkatan'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel booking
     */
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $booking = JamaahBooking::findOrFail($id);

            // Check outlet access
            if (Auth::user()->id_outlet && $booking->id_outlet != Auth::user()->id_outlet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this booking'
                ], 403);
            }

            // Check if booking can be cancelled
            if (in_array($booking->status, ['departed', 'completed', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel booking with status: ' . $booking->status
                ], 400);
            }

            // Update booking status
            $booking->update(['status' => 'cancelled']);

            // Update package status if it was full
            $package = $booking->travelPackage;
            if ($package->status === 'full' && !$package->isFull()) {
                $package->update(['status' => 'active']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete booking permanently with all related data
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Use find() instead of findOrFail() to handle missing records gracefully
            $booking = JamaahBooking::with(['payments', 'addons', 'hotelBookings'])->find($id);

            // Check if booking exists
            if (!$booking) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak ditemukan. Mungkin sudah dihapus sebelumnya.'
                ], 404);
            }

            // Check outlet access
            if (Auth::user()->id_outlet && $booking->id_outlet != Auth::user()->id_outlet) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this booking'
                ], 403);
            }

            // 1. Delete all payments and their bukti transfer files
            if ($booking->payments) {
                foreach ($booking->payments as $payment) {
                    if ($payment->bukti_transfer) {
                        $filePath = storage_path('app/public/' . $payment->bukti_transfer);
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }
                    }
                    $payment->delete();
                }
            }

            // 2. Delete invoice if exists
            if ($booking->id_invoice) {
                $invoice = \App\Models\SalesInvoice::find($booking->id_invoice);
                if ($invoice) {
                    // Delete invoice items first (correct table name: sales_invoice_item, not sales_invoice_items)
                    \DB::table('sales_invoice_item')->where('id_sales_invoice', $invoice->id_sales_invoice)->delete();
                    // Delete invoice
                    $invoice->delete();
                }
            }

            // 3. Delete piutang
            \App\Models\Piutang::where('id_jamaah_booking', $booking->id)->delete();

            // 4. Delete addons
            if ($booking->addons) {
                foreach ($booking->addons as $addon) {
                    $addon->delete();
                }
            }

            // 5. Delete hotel bookings
            if ($booking->hotelBookings) {
                foreach ($booking->hotelBookings as $hotelBooking) {
                    // Delete room assignments first
                    \DB::table('hotel_room_assignments')->where('id_hotel_booking', $hotelBooking->id)->delete();
                    $hotelBooking->delete();
                }
            }

            // 6. Flight bookings are per keberangkatan, not per jamaah booking
            // So we don't delete them here

            // 7. Delete documents
            $documents = \App\Models\JamaahDocument::where('id_jamaah_booking', $booking->id)->get();
            foreach ($documents as $doc) {
                if ($doc->file_path) {
                    $filePath = storage_path('app/public/' . $doc->file_path);
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
                $doc->delete();
            }

            // 8. Delete affiliate referral tracking if exists
            \DB::table('affiliate_referrals')->where('booking_id', $booking->id)->delete();

            // 9. Delete voucher usage if exists
            \DB::table('voucher_usages')->where('id_jamaah_booking', $booking->id)->delete();

            // 10. Delete bukti pembayaran file if exists (old bookings)
            if ($booking->bukti_pembayaran) {
                $filePath = storage_path('app/public/' . $booking->bukti_pembayaran);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // 11. Finally, delete the booking itself
            $booking->delete();

            // 12. Update package status if it was full
            if ($booking->id_travel_package) {
                $package = \App\Models\TravelPackage::find($booking->id_travel_package);
                if ($package && $package->status === 'full' && !$package->isFull()) {
                    $package->update(['status' => 'active']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking dan semua data terkait berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to delete booking: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booking by ID for editing
     */
    public function edit($id)
    {
        $booking = JamaahBooking::with([
            'travelPackage.hppCalculation',
            'travelPackage.flightDeparture',
            'travelPackage.flightReturn',
            'travelPackage.hotelMakkah',
            'travelPackage.hotelMadinah',
            'jamaah',
            'keberangkatan',
            'payments.recordedBy',
            'documents',
            'outlet',
            'invoice',
            'addons',
            'hotelBookings.hotel',
            'voucher.affiliator'
        ])->findOrFail($id);

        // Check outlet access
        if (Auth::user()->id_outlet && $booking->id_outlet != Auth::user()->id_outlet) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this booking'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $booking
        ]);
    }

    /**
     * Get available packages for booking
     */
    public function getAvailablePackages(Request $request)
    {
        $query = TravelPackage::where('status', 'active')
            ->where('departure_date', '>=', now());

        if (Auth::user()->id_outlet) {
            $query->where('id_outlet', Auth::user()->id_outlet);
        }

        if ($request->filled('outlet_id')) {
            $query->where('id_outlet', $request->outlet_id);
        }

        $packages = $query->get()->map(function($package) {
            return [
                'id' => $package->id,
                'text' => $package->package_name . ' - ' . $package->departure_date->format('d M Y') . ' (Available: ' . $package->getAvailableSeats() . ')',
                'price' => $package->price,
                'selling_price' => $package->price,
                'departure_date' => $package->departure_date->format('Y-m-d'),
                'available_seats' => $package->getAvailableSeats(),
                'price_packages' => $package->price_packages ?? []
            ];
        });

        return response()->json($packages);
    }

    /**
     * Get available keberangkatan for a package
     */
    public function getAvailableKeberangkatan(Request $request, $packageId)
    {
        $query = Keberangkatan::where('id_travel_package', $packageId)
            ->whereIn('status', ['planning', 'confirmed']);

        if (Auth::user()->id_outlet) {
            $query->where('id_outlet', Auth::user()->id_outlet);
        }

        $keberangkatan = $query->get()->map(function($k) {
            return [
                'id' => $k->id,
                'text' => $k->keberangkatan_name . ' - ' . $k->departure_date->format('d M Y') . ' (Available: ' . $k->getAvailableCapacity() . ')',
                'available_capacity' => $k->getAvailableCapacity()
            ];
        });

        return response()->json($keberangkatan);
    }

    /**
     * Get jamaah members for booking
     */
    public function getJamaahMembers(Request $request)
    {
        $query = Member::where('is_jamaah', true);

        if (Auth::user()->id_outlet) {
            $query->where('id_outlet', Auth::user()->id_outlet);
        }

        if ($request->filled('outlet_id')) {
            $query->where('id_outlet', $request->outlet_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('passport_nomor', 'like', "%{$search}%")
                  ->orWhere('ktp_nik', 'like', "%{$search}%");
            });
        }

        $members = $query->limit(50)->get()->map(function($member) {
            return [
                'id' => $member->id_member,
                'text' => $member->nama . ' - ' . ($member->passport_nomor ?? 'No Passport'),
                'passport_nomor' => $member->passport_nomor,
                'ktp_nik' => $member->ktp_nik
            ];
        });

        return response()->json($members);
    }

    /**
     * Hitung grand total booking termasuk anggota keluarga dengan diskon usia
     */
    private function calculateGrandTotal(JamaahBooking $booking): float
    {
        return $booking->getGrandTotal();
    }

    // ===== BOOKING ADD-ONS =====

    public function getAddons($id)
    {
        $booking = JamaahBooking::findOrFail($id);
        return response()->json(['success' => true, 'data' => $booking->addons]);
    }

    public function createAddon($id)
    {
        $booking = JamaahBooking::with(['jamaah', 'travelPackage'])->findOrFail($id);
        return view('admin.travel.booking.addon-form', [
            'booking' => $booking,
            'addon' => null,
            'isEdit' => false
        ]);
    }

    public function editAddon($id, $addonId)
    {
        $booking = JamaahBooking::with(['jamaah', 'travelPackage'])->findOrFail($id);
        $addon = \App\Models\BookingAddon::where('id_jamaah_booking', $id)->findOrFail($addonId);
        return view('admin.travel.booking.addon-form', [
            'booking' => $booking,
            'addon' => $addon,
            'isEdit' => true
        ]);
    }

    public function storeAddon(Request $request, $id)
    {
        $booking = JamaahBooking::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'masuk_hpp' => 'boolean',
        ]);
        $addon = $booking->addons()->create($validated);
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Add-on berhasil ditambahkan', 'data' => $addon]);
        }
        
        return redirect()->route('admin.inventaris.booking.show', $id)
            ->with('success', 'Add-on berhasil ditambahkan');
    }

    public function updateAddon(Request $request, $id, $addonId)
    {
        $addon = \App\Models\BookingAddon::where('id_jamaah_booking', $id)->findOrFail($addonId);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'masuk_hpp' => 'boolean',
        ]);
        $addon->update($validated);
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Add-on berhasil diupdate', 'data' => $addon]);
        }
        
        return redirect()->route('admin.inventaris.booking.show', $id)
            ->with('success', 'Add-on berhasil diupdate');
    }

    public function destroyAddon($id, $addonId)
    {
        \App\Models\BookingAddon::where('id_jamaah_booking', $id)->findOrFail($addonId)->delete();
        return response()->json(['success' => true, 'message' => 'Add-on berhasil dihapus']);
    }

    // ===== JAMAAH HOTEL BOOKINGS =====

    public function getHotelBookings($id)
    {
        $bookings = \App\Models\JamaahHotelBooking::with('hotel')
            ->where('id_jamaah_booking', $id)->get()
            ->map(fn($hb) => [
                'id'             => $hb->id,
                'id_hotel'       => $hb->id_hotel,
                'hotel_name'     => $hb->hotel ? $hb->hotel->hotel_name : '-',
                'city_type'      => $hb->city_type,
                'room_type'      => $hb->room_type,
                'check_in_date'  => $hb->check_in_date?->format('Y-m-d'),
                'check_out_date' => $hb->check_out_date?->format('Y-m-d'),
                'nights'         => $hb->nights,
                'price_per_night'=> $hb->price_per_night,
                'total_cost'     => $hb->total_cost,
                'is_charged'     => $hb->is_charged,
                'notes'          => $hb->notes,
            ]);
        return response()->json(['data' => $bookings]);
    }

    public function createHotelBooking($id)
    {
        $booking = JamaahBooking::with(['jamaah', 'travelPackage'])->findOrFail($id);
        $hotels = \App\Models\Hotel::with('roomTypes')->get();
        return view('admin.travel.booking.hotel-booking-form', [
            'booking' => $booking,
            'hotelBooking' => null,
            'hotels' => $hotels,
            'isEdit' => false
        ]);
    }

    public function editHotelBooking($id, $hbId)
    {
        $booking = JamaahBooking::with(['jamaah', 'travelPackage'])->findOrFail($id);
        $hotelBooking = \App\Models\JamaahHotelBooking::where('id_jamaah_booking', $id)->findOrFail($hbId);
        $hotels = \App\Models\Hotel::with('roomTypes')->get();
        return view('admin.travel.booking.hotel-booking-form', [
            'booking' => $booking,
            'hotelBooking' => $hotelBooking,
            'hotels' => $hotels,
            'isEdit' => true
        ]);
    }

    public function storeHotelBooking(Request $request, $id)
    {
        $hb = \App\Models\JamaahHotelBooking::create(array_merge(
            $request->all(), ['id_jamaah_booking' => $id]
        ));
        
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Hotel booking berhasil disimpan', 'data' => $hb], 200);
        }
        
        return redirect()->route('admin.inventaris.booking.show', $id)
            ->with('success', 'Hotel booking berhasil disimpan');
    }

    public function updateHotelBooking(Request $request, $id, $hbId)
    {
        $hb = \App\Models\JamaahHotelBooking::where('id_jamaah_booking', $id)->findOrFail($hbId);
        $hb->update($request->all());
        
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Hotel booking berhasil diupdate', 'data' => $hb], 200);
        }
        
        return redirect()->route('admin.inventaris.booking.show', $id)
            ->with('success', 'Hotel booking berhasil diupdate');
    }

    public function destroyHotelBooking($id, $hbId)
    {
        \App\Models\JamaahHotelBooking::where('id_jamaah_booking', $id)->findOrFail($hbId)->delete();
        return response()->json(['message' => 'Hotel booking berhasil dihapus']);
    }

    /**
     * Set custom payment amount for payment link
     */
    public function setPaymentAmount(Request $request, $id)
    {
        $validated = $request->validate([
            'custom_payment_amount' => 'required|numeric|min:0',
            'admin_discount' => 'nullable|numeric|min:0',
        ]);

        try {
            $booking = JamaahBooking::findOrFail($id);

            // Check outlet access
            if (Auth::user()->id_outlet && $booking->id_outlet != Auth::user()->id_outlet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this booking'
                ], 403);
            }

            // Calculate final total after voucher discount
            $grandTotal = $booking->getGrandTotal();
            $voucherDiscount = $booking->voucher_discount ?? 0;
            $adminDiscount = $validated['admin_discount'] ?? 0;
            $finalTotal = $grandTotal - $voucherDiscount - $adminDiscount;
            $remainingAfterDiscounts = max(0, $finalTotal - $booking->paid_amount);

            // Validate amount doesn't exceed remaining after discounts
            if ($validated['custom_payment_amount'] > $remainingAfterDiscounts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran tidak boleh melebihi sisa tagihan setelah diskon (Rp ' . number_format($remainingAfterDiscounts, 0, ',', '.') . ')'
                ], 400);
            }

            // Update booking with custom payment amount and admin discount
            $updateData = [
                'custom_payment_amount' => $validated['custom_payment_amount']
            ];
            
            if (isset($validated['admin_discount']) && $validated['admin_discount'] > 0) {
                $updateData['admin_discount'] = $validated['admin_discount'];
            }
            
            $booking->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Jumlah pembayaran berhasil diatur',
                'data' => [
                    'custom_payment_amount' => $booking->custom_payment_amount,
                    'custom_payment_amount_formatted' => 'Rp ' . number_format($booking->custom_payment_amount, 0, ',', '.'),
                    'admin_discount' => $booking->admin_discount ?? 0,
                    'admin_discount_formatted' => 'Rp ' . number_format($booking->admin_discount ?? 0, 0, ',', '.'),
                    'final_total' => $booking->getFinalTotal(),
                    'final_total_formatted' => 'Rp ' . number_format($booking->getFinalTotal(), 0, ',', '.'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengatur jumlah pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear custom payment amount
     */
    public function clearPaymentAmount($id)
    {
        try {
            $booking = JamaahBooking::findOrFail($id);

            // Check outlet access
            if (Auth::user()->id_outlet && $booking->id_outlet != Auth::user()->id_outlet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this booking'
                ], 403);
            }

            $booking->update(['custom_payment_amount' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Jumlah pembayaran custom berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jumlah pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
