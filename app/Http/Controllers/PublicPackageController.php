<?php

namespace App\Http\Controllers;

use App\Models\TravelPackage;
use App\Models\Keberangkatan;
use App\Models\Member;
use App\Models\JamaahBooking;
use App\Models\JamaahPayment;
use App\Models\Outlet;
use App\Services\InvoiceIntegrationService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicPackageController extends Controller
{
    /**
     * Display all packages page
     */
    public function index(Request $request)
    {
        $query = TravelPackage::active()
            ->with(['outlet', 'flightDeparture', 'hotelMakkah', 'hotelMadinah']);

        // Filter by outlet
        if ($request->filled('outlet_id')) {
            $query->where('id_outlet', $request->outlet_id);
        }

        // Filter by package type
        if ($request->filled('package_type')) {
            $query->where('package_type', $request->package_type);
        }

        // Filter by month
        if ($request->filled('bulan')) {
            $query->whereMonth('departure_date', $request->bulan);
        }

        // Sort by departure date
        $query->orderBy('departure_date', 'asc');

        $packages = $query->paginate(12);

        // Get outlets for filter
        $outlets = Outlet::active()->orderBy('nama_outlet')->get(['id_outlet', 'nama_outlet', 'kota']);

        // Get package types for filter
        $packageTypes = TravelPackage::active()
            ->select('package_type')
            ->distinct()
            ->pluck('package_type')
            ->filter();

        return view('public.paket-list', compact('packages', 'outlets', 'packageTypes'));
    }

    public function show($id)
    {
        $package = TravelPackage::with([
            'outlet', 'flightDeparture', 'flightReturn', 'hotelMakkah', 'hotelMadinah', 'tourPlans.activities'
        ])->findOrFail($id);

        // Pastikan package_photos selalu array
        if ($package->package_photos && is_string($package->package_photos)) {
            $decoded = json_decode($package->package_photos, true);
            $package->package_photos = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($package->package_photos)) $package->package_photos = [];

        // Pastikan price_packages selalu array
        $pricePackages = $package->price_packages;
        if (is_string($pricePackages)) $pricePackages = json_decode($pricePackages, true);
        if (!is_array($pricePackages)) $pricePackages = [];

        // Keberangkatan tersedia
        $keberangkatanList = Keberangkatan::where('id_travel_package', $package->id)
            ->where('departure_date', '>=', now())
            ->whereIn('status', ['planning', 'confirmed'])
            ->orderBy('departure_date')
            ->get();

        // Paket terkait
        $relatedPackages = TravelPackage::active()
            ->where('id', '!=', $id)
            ->where('package_type', $package->package_type)
            ->limit(3)->get();

        $package->increment('view_count');

        return view('public.paket-detail', compact(
            'package', 'pricePackages', 'keberangkatanList', 'relatedPackages'
        ));
    }

    public function order(Request $request, $id)
    {
        $package = TravelPackage::with('outlet')->findOrFail($id);

        $validated = $request->validate([
            'nama'                    => 'required|string|max:255',
            'telepon'                 => 'required|string|max:20',
            'email'                   => 'nullable|email|max:255',
            'alamat'                  => 'nullable|string|max:500',
            'price_package_name'      => 'nullable|string|max:255',
            'price_variant'           => 'nullable|string|max:100',
            'selected_price'          => 'nullable|numeric|min:0',
            'id_keberangkatan'        => 'nullable|integer|exists:keberangkatan,id',
            'catatan'                 => 'nullable|string|max:500',
            'payment_type'            => 'required|in:full,dp',
            'dp_option'               => 'required_if:payment_type,dp|in:25_percent,10_million',
            'family_members'          => 'nullable|array',
            'family_members.*.nama'   => 'required_with:family_members|string|max:255',
            'family_members.*.hubungan' => 'nullable|string|max:50',
            'family_members.*.tanggal_lahir' => 'nullable|date',
        ]);

        $unitPrice    = (float)($validated['selected_price'] ?? $package->price ?? 0);
        $familyMembers = $validated['family_members'] ?? [];

        // Hitung total dengan logika usia
        [$grandTotal, $priceBreakdown] = $this->calculateTotal($unitPrice, $familyMembers, $package);

        // Cari member by telepon, atau by nama jika ada
        $member = Member::where('telepon', $validated['telepon'])->first();
        if (!$member) {
            $member = Member::where('nama', $validated['nama'])->first();
        }

        if (!$member) {
            $lastMember = Member::orderByRaw('CAST(kode_member AS UNSIGNED) DESC')
                ->whereNotNull('kode_member')->where('kode_member', '!=', '')->first();
            $nextNumber = $lastMember ? (intval($lastMember->kode_member) + 1) : 1;

            $member = Member::create([
                'nama'         => $validated['nama'],
                'telepon'      => $validated['telepon'],
                'alamat'       => $validated['alamat'] ?? null,
                'id_outlet'    => $package->id_outlet,
                'kode_member'  => str_pad($nextNumber, 6, '0', STR_PAD_LEFT),
                'is_jamaah'    => true,
                'jamaah_type'  => 'umrah',
                'family_members' => !empty($familyMembers) ? json_encode($familyMembers) : null,
            ]);
        } else {
            // Jangan update family_members jika member sudah ada
            // Family members hanya diupdate jika memang ditambahkan di form pemesanan
            // Ini mencegah family members lama terhitung di booking baru
        }

        // Buat Booking
        // Simpan family_members di booking, bukan di member
        // Ini memastikan setiap booking punya data family members sendiri
        
        // AUTO-ASSIGN KE KEBERANGKATAN DEFAULT
        // Jika tidak ada id_keberangkatan yang dipilih, assign ke keberangkatan default paket
        $keberangkatanId = $validated['id_keberangkatan'] ?? null;
        
        if (!$keberangkatanId) {
            // Cari keberangkatan default untuk paket ini (yang paling dekat dengan tanggal keberangkatan paket)
            $defaultKeberangkatan = Keberangkatan::where('id_travel_package', $package->id)
                ->where('status', 'planning')
                ->where('departure_date', '>=', now())
                ->orderBy('departure_date', 'asc')
                ->first();
            
            if ($defaultKeberangkatan) {
                $keberangkatanId = $defaultKeberangkatan->id;
                \Log::info('Auto-assigned booking to default keberangkatan', [
                    'package_id' => $package->id,
                    'keberangkatan_id' => $keberangkatanId,
                    'keberangkatan_code' => $defaultKeberangkatan->keberangkatan_code
                ]);
            }
        }
        
        $booking = JamaahBooking::create([
            'booking_code'       => JamaahBooking::generateBookingCode(),
            'id_travel_package'  => $package->id,
            'id_member'          => $member->id_member,
            'id_keberangkatan'   => $keberangkatanId,
            'booking_date'       => now()->toDateString(),
            'status'             => 'pending',
            'total_price'        => $grandTotal,
            'payment_status'     => 'unpaid',
            'paid_amount'        => 0,
            'remaining_amount'   => $grandTotal,
            'id_outlet'          => $package->id_outlet,
            'price_package_name' => $validated['price_package_name'] ?? null,
            'price_variant'      => $validated['price_variant'] ?? null,
            'seller_name'        => 'Website',
            'closing_source'     => 'digital_marketing',
            'family_members_booking' => !empty($familyMembers) ? json_encode($familyMembers) : null,
            'payment_type'       => $validated['payment_type'],
            'dp_option'          => $validated['dp_option'] ?? null,
        ]);
        
        // Update total jamaah di keberangkatan jika ada
        if ($keberangkatanId) {
            $keberangkatan = Keberangkatan::find($keberangkatanId);
            if ($keberangkatan) {
                $keberangkatan->updateTotalJamaah();
            }
        }

        // Sync piutang saat booking dibuat (belum bayar)
        $this->syncPiutang($booking);

        // Ambil info keberangkatan
        $keberangkatan = null;
        if ($keberangkatanId) {
            $keberangkatan = Keberangkatan::find($keberangkatanId);
        }

        // Generate invoice PDF URL (link ke halaman invoice booking)
        $invoicePdfUrl = url('/paket/' . $id . '/invoice/' . $booking->id);

        // Susun pesan WhatsApp
        $msg  = "Assalamu'alaikum, saya telah melakukan pemesanan paket:\n\n";
        $msg .= "📦 *{$package->package_name}*\n";
        $msg .= "🔖 Kode Booking: *{$booking->booking_code}*\n";

        if ($validated['price_package_name'] ?? null) {
            $msg .= "📋 Paket: {$validated['price_package_name']}";
            if ($validated['price_variant'] ?? null) $msg .= " ({$validated['price_variant']})";
            $msg .= "\n";
        }

        if ($keberangkatan) {
            $msg .= "✈️ Keberangkatan: " . Carbon::parse($keberangkatan->departure_date)->format('d M Y');
            if ($keberangkatan->keberangkatan_name) $msg .= " ({$keberangkatan->keberangkatan_name})";
            $msg .= "\n";
        } elseif ($package->departure_date) {
            $msg .= "✈️ Keberangkatan: " . Carbon::parse($package->departure_date)->format('d M Y') . "\n";
        }

        // Rincian harga
        $msg .= "\n💰 *Rincian Harga:*\n";
        foreach ($priceBreakdown as $item) {
            $msg .= "  • {$item['label']}: Rp " . number_format($item['amount'], 0, ',', '.') . "\n";
        }
        $msg .= "💳 *Total: Rp " . number_format($grandTotal, 0, ',', '.') . "*\n";

        $msg .= "\n👤 *Data Pemesan:*\n";
        $msg .= "Nama: {$validated['nama']}\n";
        $msg .= "Telepon: {$validated['telepon']}\n";
        if ($validated['email'] ?? null) $msg .= "Email: {$validated['email']}\n";

        if (!empty($familyMembers)) {
            $msg .= "\n👨‍👩‍👧 *Anggota Keluarga:*\n";
            foreach ($familyMembers as $fm) {
                $msg .= "  • {$fm['nama']}";
                if (!empty($fm['hubungan'])) $msg .= " ({$fm['hubungan']})";
                if (!empty($fm['tanggal_lahir'])) {
                    $age = Carbon::parse($fm['tanggal_lahir'])->age;
                    $msg .= " - {$age} tahun";
                }
                $msg .= "\n";
            }
        }

        if ($validated['catatan'] ?? null) $msg .= "\n📝 Catatan: {$validated['catatan']}\n";

        $msg .= "\n📄 *Invoice:* {$invoicePdfUrl}\n";
        $msg .= "\nMohon konfirmasi ketersediaan. Terima kasih 🙏";

        // AUTO-SEND WhatsApp DARI ADMIN KE JAMAAH via OpenWA
        $jamaahPhone = $validated['telepon']; // Nomor jamaah yang pesan
        
        \Log::info('Order created, preparing WhatsApp notification from admin to jamaah', [
            'booking_code' => $booking->booking_code,
            'jamaah_phone' => $jamaahPhone,
            'jamaah_name' => $validated['nama']
        ]);
        
        try {
            $whatsappService = new \App\Services\WhatsAppService();
            $result = $whatsappService->sendMessage($jamaahPhone, $msg);
            
            \Log::info('OpenWA sendMessage result for order', [
                'booking_code' => $booking->booking_code,
                'success' => $result['success'] ?? false,
                'error' => $result['error'] ?? null
            ]);
            
            if ($result['success']) {
                // Message sent successfully via OpenWA from admin to jamaah
                \Log::info('WhatsApp auto-sent successfully from admin to jamaah', [
                    'booking_code' => $booking->booking_code,
                    'jamaah_phone' => $jamaahPhone,
                    'message_id' => $result['messageId'] ?? null
                ]);
                
                // Redirect to invoice page with success message
                return redirect()->route('public.paket.invoice', [
                    'packageId' => $id, 
                    'bookingId' => $booking->id
                ])->with('success', 'Pemesanan berhasil! Notifikasi WhatsApp telah dikirim ke ' . $jamaahPhone);
            } else {
                // OpenWA failed, use fallback wa.me link
                \Log::warning('OpenWA failed for order, using fallback wa.me link', [
                    'booking_code' => $booking->booking_code,
                    'jamaah_phone' => $jamaahPhone,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
                
                // Use fallback URL from service or generate manually
                $waUrl = $result['fallback_url'] ?? 'https://wa.me/' . $whatsappService->formatPhone($jamaahPhone) . '?text=' . urlencode($msg);
                
                return redirect($waUrl);
            }
        } catch (\Exception $e) {
            \Log::error('Exception while sending WhatsApp for order', [
                'booking_code' => $booking->booking_code,
                'jamaah_phone' => $jamaahPhone,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback to wa.me
            $waUrl = 'https://wa.me/' . $whatsappService->formatPhone($jamaahPhone) . '?text=' . urlencode($msg);
            return redirect($waUrl);
        }
    }

    /**
     * Halaman invoice booking publik (untuk link di WA)
     */
    public function invoice($packageId, $bookingId)
    {
        $booking = JamaahBooking::with([
            'travelPackage.outlet',
            'travelPackage.flightDeparture',
            'travelPackage.flightReturn',
            'travelPackage.hotelMakkah',
            'travelPackage.hotelMadinah',
            'jamaah',
            'invoice', // Load invoice relationship
        ])->findOrFail($bookingId);

        // Pastikan booking milik paket ini
        abort_if($booking->id_travel_package != $packageId, 404);

        $package = $booking->travelPackage;

        // Hitung ulang total
        $pricePackages = $package->price_packages;
        if (is_string($pricePackages)) $pricePackages = json_decode($pricePackages, true);
        if (!is_array($pricePackages)) $pricePackages = [];

        $unitPrice = 0;
        $selectedVariant = $booking->price_variant ?? 'double';
        $selectedPkgName = $booking->price_package_name;
        if (!empty($pricePackages)) {
            $targetPkg = null;
            if ($selectedPkgName) {
                foreach ($pricePackages as $pp) {
                    if (strtolower($pp['name'] ?? '') === strtolower($selectedPkgName)) { $targetPkg = $pp; break; }
                }
            }
            if (!$targetPkg) $targetPkg = $pricePackages[0] ?? null;
            if ($targetPkg) {
                foreach ($targetPkg['variants'] ?? [] as $v) {
                    if (strtolower($v['type'] ?? '') === strtolower($selectedVariant)) { $unitPrice = (float)($v['price'] ?? 0); break; }
                }
                if ($unitPrice == 0 && !empty($targetPkg['variants'])) {
                    $unitPrice = (float)($targetPkg['variants'][0]['price'] ?? 0);
                }
            }
        }
        if ($unitPrice == 0) $unitPrice = (float)$booking->total_price;

        // Ambil family members dari booking, BUKAN dari member
        // Ini memastikan hanya family members yang ditambahkan saat booking yang terhitung
        $familyMembers = $booking->family_members_booking ?? [];
        if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true);
        if (!is_array($familyMembers)) $familyMembers = [];

        [$grandTotal, $priceBreakdown] = $this->calculateTotal($unitPrice, $familyMembers, $package);
        
        // Apply admin discount if exists
        $adminDiscount = $booking->admin_discount ?? 0;
        if ($adminDiscount > 0) {
            $grandTotal = max(0, $grandTotal - $adminDiscount);
        }

        // Ambil bank accounts dari CompanyBankAccount
        $bankAccounts = \App\Models\CompanyBankAccount::where('id_outlet', $package->id_outlet)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        // Fallback: jika tidak ada CompanyBankAccount, ambil dari CompanySetting
        if ($bankAccounts->isEmpty()) {
            $companySetting = \App\Models\CompanySetting::where('outlet_id', $package->id_outlet)->first();
            if (!$companySetting) {
                $companySetting = \App\Models\CompanySetting::first();
            }
            
            if ($companySetting && $companySetting->bank_name && $companySetting->bank_account_number) {
                // Convert CompanySetting to collection format yang sama dengan CompanyBankAccount
                $bankAccounts = collect([
                    (object)[
                        'id' => 0,
                        'bank_name' => $companySetting->bank_name,
                        'account_number' => $companySetting->bank_account_number,
                        'account_holder_name' => $companySetting->bank_account_name ?? $companySetting->company_name,
                    ]
                ]);
            }
        }

        // Hitung DP amounts untuk display
        $dp25Percent = round($grandTotal * 0.25);
        $dp5Million = 10000000;

        return view('public.invoice-booking', compact(
            'booking', 'package', 'unitPrice', 'familyMembers', 'priceBreakdown', 'grandTotal', 
            'bankAccounts', 'dp25Percent', 'dp5Million', 'adminDiscount'
        ));
    }

    /**
     * Proses pembayaran dari halaman invoice booking
     */
    public function pay(Request $request, $packageId, $bookingId)
    {
        $booking = JamaahBooking::with(['travelPackage', 'jamaah'])->findOrFail($bookingId);
        abort_if($booking->id_travel_package != $packageId, 404);

        $validated = $request->validate([
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_type'   => 'required|in:full,dp',
            'dp_option'      => 'required_if:payment_type,dp|in:25_percent,10_million',
            'payment_method' => 'nullable|string|in:transfer,cash',
            'notes'          => 'nullable|string|max:500',
            'voucher_code'   => 'nullable|string',
            'voucher_discount' => 'nullable|numeric|min:0',
        ]);

        // Handle voucher if provided and not already applied to booking
        $voucherData = null;
        $voucherDiscount = 0;
        
        if ($request->filled('voucher_code') && empty($booking->id_voucher)) {
            $voucher = \App\Models\AffiliateVoucher::where('code', strtoupper($request->voucher_code))
                ->first();
            
            if ($voucher && $voucher->isValid() && $voucher->canBeUsed($booking->total_price)) {
                $voucherDiscount = $voucher->calculateDiscount($booking->total_price);
                $voucherData = [
                    'voucher' => $voucher,
                    'discount' => $voucherDiscount
                ];
                
                // Update booking with voucher
                $booking->id_voucher = $voucher->id;
                $booking->voucher_code = $voucher->code;
                $booking->voucher_discount = $voucherDiscount;
                $booking->total_price = $booking->total_price - $voucherDiscount;
                $booking->remaining_amount = $booking->total_price - ($booking->paid_amount ?? 0);
                $booking->save();
                
                // Record voucher usage
                \App\Models\VoucherUsage::create([
                    'id_voucher' => $voucher->id,
                    'id_jamaah_booking' => $booking->id,
                    'discount_amount' => $voucherDiscount,
                    'original_amount' => $booking->total_price + $voucherDiscount,
                    'final_amount' => $booking->total_price,
                    'used_at' => now(),
                ]);
                
                // Increment voucher usage count
                $voucher->incrementUsage();
            }
        }
        
        // Hitung amount berdasarkan prioritas: custom_payment_amount > remaining_amount > payment_type
        $totalPrice = $booking->total_price;
        $remainingAmount = $totalPrice - ($booking->paid_amount ?? 0);
        
        // Prioritas 1: Cek apakah admin sudah set custom_payment_amount
        if (!empty($booking->custom_payment_amount)) {
            $amount = min($booking->custom_payment_amount, $remainingAmount);
        } 
        // Prioritas 2: Gunakan logic payment_type (full/dp)
        elseif ($validated['payment_type'] === 'dp') {
            $dpOption = $validated['dp_option'] ?? '25_percent';
            if ($dpOption === '25_percent') {
                $amount = round($totalPrice * 0.25);
            } else {
                $amount = 10000000;
            }
            $amount = min($amount, $remainingAmount);
        } 
        // Prioritas 3: Bayar penuh (sisa tagihan)
        else {
            $amount = $remainingAmount;
        }

        // Upload bukti transfer
        $file = $request->file('bukti_transfer');
        $fileName = 'bukti_' . time() . '_' . $booking->booking_code . '.' . $file->getClientOriginalExtension();
        $file->storeAs('bukti-transfer', $fileName, 'public');

        // Generate receipt number
        $lastPayment = JamaahPayment::orderByRaw('CAST(SUBSTRING(receipt_number, 5) AS UNSIGNED) DESC')
            ->whereNotNull('receipt_number')
            ->where('receipt_number', '!=', '')
            ->first();
        $nextNumber = $lastPayment ? (intval(substr($lastPayment->receipt_number, 4)) + 1) : 1;
        $receiptNumber = 'KWT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        // Simpan payment - save full path with folder
        $payment = JamaahPayment::create([
            'id_jamaah_booking' => $booking->id,
            'payment_date'      => now()->toDateString(),
            'amount'            => $amount,
            'payment_method'    => $validated['payment_method'] ?? 'transfer',
            'receipt_number'    => $receiptNumber,
            'reference_number'  => $booking->booking_code,
            'notes'             => $validated['notes'] ?? 'Pembayaran dari website publik',
            'recorded_by'       => null, // Public payment, no user
            'bukti_transfer'    => 'bukti-transfer/' . $fileName, // Save with folder path
            'payment_type'      => $validated['payment_type'],
        ]);

        // Update booking
        $booking->paid_amount = ($booking->paid_amount ?? 0) + $amount;
        $booking->remaining_amount = $booking->total_price - $booking->paid_amount;
        
        if ($booking->paid_amount >= $booking->total_price) {
            $booking->payment_status = 'paid';
        } elseif ($booking->paid_amount > 0) {
            $booking->payment_status = 'partial';
        }
        $booking->save();

        // Sync piutang ke tabel piutang
        $this->syncPiutang($booking);

        // Verify affiliate sale (jika ada referral pending untuk booking ini)
        $affiliateService = new \App\Services\AffiliateTrackingService();
        try {
            $verified = $affiliateService->verifySale($booking->id);
            if ($verified) {
                \Log::info('Affiliate commission verified and credited for booking: ' . $booking->booking_code);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to verify affiliate sale: ' . $e->getMessage());
            // Don't fail the payment if affiliate verification fails
        }

        // Buat atau update SalesInvoice agar link invoice PDF bisa diakses
        $invoiceService = new InvoiceIntegrationService();
        if (!$booking->id_invoice) {
            // Buat invoice baru
            $paymentTerm = $validated['payment_type'] === 'full' ? 'full' : 'installment';
            try {
                $invoice = $invoiceService->createInvoiceForJamaah($booking, $paymentTerm, $amount);
            } catch (\Exception $e) {
                \Log::error('Failed to create invoice for public booking: ' . $e->getMessage());
                $invoice = null;
            }
        } else {
            // Update invoice yang sudah ada
            $invoice = \App\Models\SalesInvoice::find($booking->id_invoice);
            if ($invoice) {
                try {
                    $invoiceService->updateInvoicePayment($invoice, $amount);
                } catch (\Exception $e) {
                    \Log::error('Failed to update invoice payment: ' . $e->getMessage());
                }
            }
        }

        // Reload booking untuk mendapatkan id_invoice terbaru
        $booking->refresh();

        // Generate tokens untuk PDF links (public routes - sama seperti QR code)
        $invoiceToken = hash('sha256', $booking->id . ($booking->id_invoice ?? '') . config('app.key'));
        $receiptToken = hash('sha256', $payment->id . $payment->id_jamaah_booking . config('app.key'));

        // Gunakan route public yang sama dengan QR code di invoice/kwitansi
        // Jika invoice belum ada, kirim link ke halaman invoice booking saja
        if ($booking->id_invoice) {
            $invoiceUrl = route('public.invoice', ['bookingId' => $booking->id, 'token' => $invoiceToken]);
        } else {
            $invoiceUrl = url('/paket/' . $packageId . '/invoice/' . $booking->id);
        }
        $receiptUrl = route('public.receipt', ['paymentId' => $payment->id, 'token' => $receiptToken]);

        // Get company settings untuk info bank
        // Gunakan outlet_id (bukan id_outlet) sesuai schema tabel
        $package = $booking->travelPackage;
        $companySetting = null;
        if ($package->id_outlet) {
            $companySetting = \App\Models\CompanySetting::where('outlet_id', $package->id_outlet)->first();
        }
        // Fallback ke setting pertama jika tidak ada setting untuk outlet ini
        if (!$companySetting) {
            $companySetting = \App\Models\CompanySetting::first();
        }

        // Get family members untuk rincian pax - ambil dari booking, bukan member
        $familyMembers = $booking->family_members_booking;
        if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true);
        if (!is_array($familyMembers)) $familyMembers = [];

        // Hitung pax berdasarkan kategori
        $dewasaCount = 1; // Jamaah utama
        $infantCount = 0;
        $anakCount = 0;
        
        foreach ($familyMembers as $fm) {
            if (empty($fm['tanggal_lahir'])) {
                $dewasaCount++;
            } else {
                $age = \Carbon\Carbon::parse($fm['tanggal_lahir'])->age;
                if ($age < 2) {
                    $infantCount++;
                } elseif ($age <= 8) {
                    $anakCount++;
                } else {
                    $dewasaCount++;
                }
            }
        }

        // Susun pesan WA dengan informasi lengkap
        $package = $booking->travelPackage;
        $jamaah = $booking->jamaah;
        
        $msg  = "Assalamu'alaikum, pembayaran telah berhasil diproses:\n\n";
        
        // 1. Info Transfer ke Rekening
        if ($companySetting && $companySetting->bank_name && $companySetting->bank_account_number) {
            $msg .= "💳 *Transfer ke:*\n";
            $msg .= "Bank: {$companySetting->bank_name}\n";
            $msg .= "No. Rek: {$companySetting->bank_account_number}\n";
            $msg .= "a/n: " . ($companySetting->bank_account_name ?? $companySetting->company_name) . "\n\n";
        }
        
        // 2. Data Customer
        $msg .= "👤 *Data Customer:*\n";
        $msg .= "Nama: {$jamaah->nama}\n";
        if ($jamaah->alamat) $msg .= "Alamat: {$jamaah->alamat}\n";
        if ($jamaah->email) $msg .= "Email: {$jamaah->email}\n";
        if ($jamaah->telepon) $msg .= "No. HP: {$jamaah->telepon}\n";
        $msg .= "\n";
        
        // 3. Order & Payment Status
        $msg .= "📋 *Status Pesanan:*\n";
        $msg .= "Kode Booking: *{$booking->booking_code}*\n";
        $msg .= "Order Status: " . strtoupper($booking->status) . "\n";
        $msg .= "Payment Status: " . strtoupper($booking->payment_status ?? 'pending') . "\n";
        
        // 4. Tanggal
        $msg .= "Tanggal Booking: " . $booking->booking_date->format('d M Y') . "\n";
        $msg .= "Tanggal Bayar: " . now()->format('d M Y H:i') . "\n";
        $msg .= "\n";
        
        // 5-8. Jumlah Total, Dibayar, Sisa
        $msg .= "💰 *Rincian Pembayaran:*\n";
        $msg .= "No. Kwitansi: *{$receiptNumber}*\n";
        $msg .= "Jenis: " . ($validated['payment_type'] === 'dp' ? 'DP (25% atau min Rp 10jt)' : 'Lunas') . "\n";
        
        // Show original price if there are discounts
        if ($booking->voucher_discount > 0 || $booking->admin_discount > 0) {
            $originalTotal = $booking->total_price + ($booking->voucher_discount ?? 0) + ($booking->admin_discount ?? 0);
            $msg .= "Harga Paket: Rp " . number_format($originalTotal, 0, ',', '.') . "\n";
            
            if ($booking->voucher_discount > 0) {
                $msg .= "Diskon Voucher ({$booking->voucher_code}): -Rp " . number_format($booking->voucher_discount, 0, ',', '.') . "\n";
            }
            
            if ($booking->admin_discount > 0) {
                $msg .= "Diskon Admin: -Rp " . number_format($booking->admin_discount, 0, ',', '.') . "\n";
            }
            
            $msg .= "Total Setelah Diskon: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
        } else {
            $msg .= "Total Harga: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
        }
        
        $msg .= "Jumlah Dibayar: Rp " . number_format($amount, 0, ',', '.') . "\n";
        $msg .= "Total Terbayar: Rp " . number_format($booking->paid_amount, 0, ',', '.') . "\n";
        $msg .= "Sisa Tagihan: Rp " . number_format($booking->remaining_amount, 0, ',', '.') . "\n";
        $msg .= "\n";
        
        // 9. Rincian Pesanan
        $msg .= "📦 *Rincian Pesanan:*\n";
        $msg .= "Paket: *{$package->package_name}*\n";
        $msg .= "Tgl Berangkat: " . $package->departure_date->format('d M Y') . "\n";
        $msg .= "Tgl Pulang: " . $package->return_date->format('d M Y') . "\n";
        $msg .= "Durasi: {$package->duration_days} Hari\n";
        $msg .= "\n";
        
        // Rincian Pax
        $msg .= "👥 *Jumlah Jamaah:*\n";
        $msg .= "Dewasa: {$dewasaCount} Pax\n";
        if ($anakCount > 0) $msg .= "Anak (2-8th): {$anakCount} Pax\n";
        if ($infantCount > 0) $msg .= "Infant (<2th): {$infantCount} Pax\n";
        $msg .= "\n";
        
        // Paket Harga & Variant
        if ($booking->price_package_name) {
            $msg .= "💎 *Paket Harga:*\n";
            $msg .= "Nama: {$booking->price_package_name}\n";
            if ($booking->price_variant) {
                $msg .= "Variant: " . ucfirst($booking->price_variant) . "\n";
            }
            $msg .= "\n";
        }
        
        // Link Dokumen
        $msg .= "📄 *Dokumen:*\n";
        $msg .= "Invoice: {$invoiceUrl}\n";
        $msg .= "Kwitansi: {$receiptUrl}\n\n";
        
        $msg .= "Terima kasih atas pembayaran Anda. Tim kami akan segera menghubungi untuk proses selanjutnya 🙏";

        // AUTO-SEND WhatsApp via OpenWA
        $jamaahPhone = $jamaah->telepon ?? $jamaah->whatsapp ?? null;
        
        \Log::info('Payment completed, preparing WhatsApp notification', [
            'booking_code' => $booking->booking_code,
            'jamaah_phone' => $jamaahPhone,
            'has_phone' => !empty($jamaahPhone)
        ]);
        
        if ($jamaahPhone) {
            // Try to send via OpenWA
            \Log::info('Attempting to send WhatsApp via OpenWA', [
                'booking_code' => $booking->booking_code,
                'phone' => $jamaahPhone
            ]);
            
            try {
                $whatsappService = new \App\Services\WhatsAppService();
                $result = $whatsappService->sendMessage($jamaahPhone, $msg);
                
                \Log::info('OpenWA sendMessage result', [
                    'booking_code' => $booking->booking_code,
                    'success' => $result['success'] ?? false,
                    'error' => $result['error'] ?? null
                ]);
                
                if ($result['success']) {
                    // Message sent successfully via OpenWA
                    \Log::info('WhatsApp auto-sent successfully', [
                        'booking_code' => $booking->booking_code,
                        'phone' => $jamaahPhone,
                        'message_id' => $result['messageId'] ?? null
                    ]);
                    
                    // Redirect to receipt page after successful payment
                    return redirect()->route('public.receipt', [
                        'paymentId' => $payment->id, 
                        'token' => $receiptToken
                    ])->with('success', 'Pembayaran berhasil! Notifikasi WhatsApp telah dikirim ke ' . $jamaahPhone);
                } else {
                    // OpenWA failed, use fallback wa.me link
                    \Log::warning('OpenWA failed, using fallback wa.me link', [
                        'booking_code' => $booking->booking_code,
                        'phone' => $jamaahPhone,
                        'error' => $result['error'] ?? 'Unknown error'
                    ]);
                    
                    // Use fallback URL from service or generate manually
                    $waUrl = $result['fallback_url'] ?? 'https://wa.me/' . $whatsappService->formatPhone($jamaahPhone) . '?text=' . urlencode($msg);
                    
                    return redirect($waUrl);
                }
            } catch (\Exception $e) {
                \Log::error('Exception while sending WhatsApp', [
                    'booking_code' => $booking->booking_code,
                    'phone' => $jamaahPhone,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Fallback to wa.me
                $waUrl = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $jamaahPhone) . '?text=' . urlencode($msg);
                return redirect($waUrl);
            }
        } else {
            // No phone number, redirect to admin for notification
            \Log::warning('No jamaah phone number, redirecting to admin', [
                'booking_code' => $booking->booking_code
            ]);
            
            $waUrl = 'https://wa.me/628976688800?text=' . urlencode($msg);
            return redirect($waUrl);
        }
    }

    /**
     * Hitung total harga berdasarkan unit price dan anggota keluarga
     * Logika: infant (<2th) = Rp 18jt, anak (2-8th) = 85% harga, dewasa = full
     * + Handling & Lounge Fee jika diaktifkan di paket
     */
    private function calculateTotal(float $unitPrice, array $familyMembers, $package = null): array
    {
        $breakdown = [];
        $total = 0;

        // Jamaah utama
        $breakdown[] = ['label' => 'Jamaah Utama (Dewasa)', 'amount' => $unitPrice, 'pax' => 1];
        $total += $unitPrice;

        foreach ($familyMembers as $fm) {
            $nama = $fm['nama'] ?? 'Anggota';
            if (empty($fm['tanggal_lahir'])) {
                // Tanpa tgl lahir = dewasa
                $breakdown[] = ['label' => "{$nama} (Dewasa)", 'amount' => $unitPrice, 'pax' => 1];
                $total += $unitPrice;
            } else {
                $age = Carbon::parse($fm['tanggal_lahir'])->age;
                if ($age < 2) {
                    $price = 18000000;
                    $breakdown[] = ['label' => "{$nama} (Infant <2th, flat Rp 18jt)", 'amount' => $price, 'pax' => 1];
                } elseif ($age <= 8) {
                    $price = round($unitPrice * 0.85);
                    $breakdown[] = ['label' => "{$nama} (Anak {$age}th, diskon 15%)", 'amount' => $price, 'pax' => 1];
                } else {
                    $price = $unitPrice;
                    $breakdown[] = ['label' => "{$nama} (Dewasa {$age}th)", 'amount' => $price, 'pax' => 1];
                }
                $total += $price;
            }
        }

        // Add Handling & Lounge Fee if enabled in package
        if ($package && $package->include_handling_lounge_fee && $package->handling_lounge_fee_amount > 0) {
            $handlingFee = $package->handling_lounge_fee_amount;
            $description = $package->handling_lounge_fee_description ?? 'Handling & Lounge Fee Wajib';
            $breakdown[] = ['label' => $description, 'amount' => $handlingFee, 'pax' => 1];
            $total += $handlingFee;
        }

        return [$total, $breakdown];
    }

    /**
     * Sync piutang untuk travel booking
     */
    private function syncPiutang(JamaahBooking $booking)
    {
        try {
            $piutang = \App\Models\Piutang::updateOrCreate(
                [
                    'id_jamaah_booking' => $booking->id
                ],
                [
                    'id_member' => $booking->id_member,
                    'id_outlet' => $booking->id_outlet,
                    'tanggal_piutang' => $booking->booking_date,
                    'jumlah_piutang' => $booking->total_price,
                    'jumlah_dibayar' => $booking->paid_amount ?? 0,
                    'sisa_piutang' => $booking->remaining_amount ?? $booking->total_price,
                    'status' => ($booking->paid_amount >= $booking->total_price) ? 'lunas' : 'belum_lunas',
                    'keterangan' => 'Piutang Travel - ' . $booking->booking_code . ' - ' . ($booking->travelPackage->package_name ?? ''),
                ]
            );

            \Log::info('Piutang synced for booking', [
                'booking_id' => $booking->id,
                'piutang_id' => $piutang->id,
                'total' => $booking->total_price,
                'paid' => $booking->paid_amount,
                'remaining' => $booking->remaining_amount
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to sync piutang: ' . $e->getMessage());
        }
    }

    /**
     * Submit booking - save to session and return token for WhatsApp redirect
     */
    public function submitBooking(Request $request)
    {
        try {
            $validated = $request->validate([
                'package_id' => 'required|exists:travel_packages,id',
                'jamaah_name' => 'required|string|max:255',
                'jamaah_phone' => 'required|string|max:20',
                'jamaah_email' => 'nullable|email|max:255',  // Made optional
                'room_type' => 'required|string|max:50',
                'payment_type' => 'required|in:full,dp',
                'dp_option' => 'nullable|in:25_percent,10_million',
                'price_package_name' => 'nullable|string|max:255',
                'price_variant' => 'nullable|string|max:100',
                'selected_price' => 'required|numeric|min:0',
                'family_members' => 'nullable|json',
                'equipment' => 'nullable|json',
                'total_price' => 'required|numeric|min:0',
            ]);

            // Parse JSON fields
            $familyMembers = !empty($validated['family_members']) ? json_decode($validated['family_members'], true) : [];
            $equipment = !empty($validated['equipment']) ? json_decode($validated['equipment'], true) : [];

            \DB::beginTransaction();

            // 1. Create or get member with outlet_id
            $package = TravelPackage::find($validated['package_id']);
            
            $member = Member::firstOrCreate(
                ['telepon' => $validated['jamaah_phone']],
                [
                    'nama' => $validated['jamaah_name'],
                    'email' => $validated['jamaah_email'],
                    'id_tipe' => 1,
                    'is_jamaah' => true,
                    'id_outlet' => $package->id_outlet ?? 1, // Set outlet from package
                ]
            );
            
            // Update outlet if member already exists but has no outlet
            if (!$member->id_outlet && $package->id_outlet) {
                $member->update(['id_outlet' => $package->id_outlet]);
            }

            // 2. Create booking with status "unpaid"
            $booking = JamaahBooking::create([
                'booking_code' => JamaahBooking::generateBookingCode(),
                'id_travel_package' => $validated['package_id'],
                'id_member' => $member->id_member,
                'id_outlet' => $package->id_outlet ?? 1,
                'booking_date' => now()->toDateString(),
                'status' => 'pending',
                'total_price' => $validated['total_price'],
                'payment_status' => 'unpaid',
                'paid_amount' => 0,
                'remaining_amount' => $validated['total_price'],
                'room_type' => $validated['room_type'],
                'price_package_name' => $validated['price_package_name'] ?? null,
                'price_variant' => $validated['price_variant'] ?? null,
                'family_members_booking' => !empty($familyMembers) ? json_encode($familyMembers) : null,
                'seller_name' => 'Website',
                'closing_source' => 'digital_marketing',
                'payment_type' => $validated['payment_type'],
                'dp_option' => $validated['dp_option'] ?? '25_percent',
            ]);

            // 3. Create add-ons (perlengkapan)
            if (!empty($equipment)) {
                foreach ($equipment as $eq) {
                    \App\Models\BookingAddon::create([
                        'id_jamaah_booking' => $booking->id,
                        'id_produk' => $eq['id'] ?? null,
                        'nama' => $eq['name'],
                        'keterangan' => 'Perlengkapan tambahan dari website',
                        'harga' => $eq['price'],
                        'qty' => $eq['qty'],
                        'masuk_hpp' => false,
                    ]);
                }
            }

            // 4. Create piutang
            \App\Models\Piutang::create([
                'id_jamaah_booking' => $booking->id,
                'id_member' => $member->id_member,
                'id_outlet' => $package->id_outlet ?? 1,
                'tanggal_piutang' => now(),
                'jumlah_piutang' => $validated['total_price'],
                'jumlah_dibayar' => 0,
                'sisa_piutang' => $validated['total_price'],
                'status' => 'belum_lunas',
                'keterangan' => 'Booking Travel - ' . $booking->booking_code,
            ]);

            // 5. Track affiliate sale (jika ada cookie affiliate)
            $affiliateService = new \App\Services\AffiliateTrackingService();
            try {
                // Track the sale (creates pending referral based on cookie)
                // Pass voucher discount to reduce affiliate commission
                $voucherDiscount = $bookingData['voucher_discount'] ?? 0;
                $referral = $affiliateService->trackSale(
                    $booking->id,
                    $validated['package_id'],
                    $validated['total_price'], // Commission based on total price
                    $booking->booking_code,
                    $voucherDiscount // Voucher discount reduces affiliate commission
                );
                
                if ($referral) {
                    \Log::info('Affiliate referral tracked for booking: ' . $booking->booking_code . ' (voucher discount: ' . $voucherDiscount . ')');
                }
            } catch (\Exception $e) {
                \Log::error('Failed to track affiliate sale: ' . $e->getMessage());
                // Don't fail the booking if affiliate tracking fails
            }

            // 6. Create invoice automatically (like admin booking)
            $invoiceService = new \App\Services\InvoiceIntegrationService();
            try {
                $paymentTerm = $validated['payment_type'] === 'full' ? 'full' : 'installment';
                $invoice = $invoiceService->createInvoiceForJamaah($booking, $paymentTerm, 0); // 0 = belum bayar
                \Log::info('Invoice created automatically for booking: ' . $booking->booking_code);
            } catch (\Exception $e) {
                \Log::error('Failed to create invoice for booking: ' . $e->getMessage());
                // Don't fail the booking if invoice creation fails
            }

            \DB::commit();

            // AUTO-SEND WhatsApp DARI ADMIN KE JAMAAH via OpenWA
            $jamaahPhone = $validated['jamaah_phone'];
            
            \Log::info('Booking created, preparing WhatsApp notification from admin to jamaah', [
                'booking_code' => $booking->booking_code,
                'jamaah_phone' => $jamaahPhone,
                'jamaah_name' => $validated['jamaah_name']
            ]);
            
            // Generate invoice URL
            $invoiceUrl = url('/paket/' . $validated['package_id'] . '/invoice/' . $booking->id);
            
            // Build WhatsApp message
            $msg  = "Assalamu'alaikum, terima kasih telah melakukan pemesanan paket:\n\n";
            $msg .= "📦 *{$package->package_name}*\n";
            $msg .= "🔖 Kode Booking: *{$booking->booking_code}*\n";
            
            if ($validated['price_package_name'] ?? null) {
                $msg .= "📋 Paket: {$validated['price_package_name']}";
                if ($validated['price_variant'] ?? null) $msg .= " ({$validated['price_variant']})";
                $msg .= "\n";
            }
            
            $msg .= "\n💰 *Rincian Harga:*\n";
            $msg .= "  • Jamaah Utama (Dewasa): Rp " . number_format($validated['selected_price'], 0, ',', '.') . "\n";
            
            // Family members
            if (!empty($familyMembers)) {
                foreach ($familyMembers as $fm) {
                    $msg .= "  • {$fm['nama']}";
                    if (!empty($fm['kategori'])) $msg .= " ({$fm['kategori']})";
                    $msg .= "\n";
                }
            }
            
            // Equipment
            if (!empty($equipment)) {
                $msg .= "\n📦 *Perlengkapan Tambahan:*\n";
                foreach ($equipment as $eq) {
                    $msg .= "  • {$eq['name']} (x{$eq['qty']}) = Rp " . number_format($eq['subtotal'], 0, ',', '.') . "\n";
                }
            }
            
            $msg .= "\n💳 *Total: Rp " . number_format($validated['total_price'], 0, ',', '.') . "*\n";
            
            $msg .= "\n👤 *Data Pemesan:*\n";
            $msg .= "Nama: {$validated['jamaah_name']}\n";
            $msg .= "Telepon: {$validated['jamaah_phone']}\n";
            if ($validated['jamaah_email'] ?? null) $msg .= "Email: {$validated['jamaah_email']}\n";
            
            $msg .= "\n📄 *Invoice:* {$invoiceUrl}\n";
            $msg .= "\nMohon konfirmasi ketersediaan. Terima kasih 🙏";
            
            try {
                $whatsappService = new \App\Services\WhatsAppService();
                $result = $whatsappService->sendMessage($jamaahPhone, $msg);
                
                \Log::info('OpenWA sendMessage result for booking', [
                    'booking_code' => $booking->booking_code,
                    'success' => $result['success'] ?? false,
                    'error' => $result['error'] ?? null
                ]);
                
                if ($result['success']) {
                    // Message sent successfully via OpenWA from admin to jamaah
                    \Log::info('WhatsApp auto-sent successfully from admin to jamaah', [
                        'booking_code' => $booking->booking_code,
                        'jamaah_phone' => $jamaahPhone,
                        'message_id' => $result['messageId'] ?? null
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'booking_id' => $booking->id,
                        'booking_code' => $booking->booking_code,
                        'message' => 'Pemesanan berhasil! Notifikasi WhatsApp telah dikirim.',
                        'whatsapp_sent' => true,
                        'redirect_url' => $invoiceUrl
                    ]);
                } else {
                    // OpenWA failed, return fallback info
                    \Log::warning('OpenWA failed for booking, will use fallback', [
                        'booking_code' => $booking->booking_code,
                        'jamaah_phone' => $jamaahPhone,
                        'error' => $result['error'] ?? 'Unknown error'
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'booking_id' => $booking->id,
                        'booking_code' => $booking->booking_code,
                        'message' => 'Pemesanan berhasil disimpan',
                        'whatsapp_sent' => false,
                        'fallback_url' => $result['fallback_url'] ?? null,
                        'fallback_message' => $msg
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Exception while sending WhatsApp for booking', [
                    'booking_code' => $booking->booking_code,
                    'jamaah_phone' => $jamaahPhone,
                    'exception' => $e->getMessage()
                ]);
                
                // Return success with fallback
                $whatsappService = new \App\Services\WhatsAppService();
                $fallbackUrl = 'https://wa.me/' . $whatsappService->formatPhone($jamaahPhone) . '?text=' . urlencode($msg);
                
                return response()->json([
                    'success' => true,
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'message' => 'Pemesanan berhasil disimpan',
                    'whatsapp_sent' => false,
                    'fallback_url' => $fallbackUrl,
                    'fallback_message' => $msg
                ]);
            }

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to submit booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show payment page for booking
     */
    public function showPaymentPage($token)
    {
        // Validate token
        if (!session('booking_token') || session('booking_token') !== $token) {
            return redirect()->route('homepage')
                ->withErrors(['error' => 'Link pembayaran tidak valid atau sudah kadaluarsa.']);
        }

        // Check token expiry
        $expiresAt = session('booking_token_expires');
        if ($expiresAt && now()->greaterThan(Carbon::parse($expiresAt))) {
            session()->forget(['booking_data', 'booking_token', 'booking_token_expires']);
            return redirect()->route('homepage')
                ->withErrors(['error' => 'Link pembayaran sudah kadaluarsa (24 jam).']);
        }

        $bookingData = session('booking_data');
        if (!$bookingData) {
            return redirect()->route('homepage')
                ->withErrors(['error' => 'Data booking tidak ditemukan.']);
        }

        $package = TravelPackage::findOrFail($bookingData['package_id']);

        return view('public.booking-payment', compact('bookingData', 'package', 'token'));
    }

    /**
     * Process payment and save booking to database
     */
    public function processPayment(Request $request, $token)
    {
        // Validate token
        if (!session('booking_token') || session('booking_token') !== $token) {
            return back()->withErrors(['error' => 'Link pembayaran tidak valid.']);
        }

        // Check token expiry
        $expiresAt = session('booking_token_expires');
        if ($expiresAt && now()->greaterThan(Carbon::parse($expiresAt))) {
            session()->forget(['booking_data', 'booking_token', 'booking_token_expires']);
            return back()->withErrors(['error' => 'Link pembayaran sudah kadaluarsa.']);
        }

        $bookingData = session('booking_data');
        if (!$bookingData) {
            return back()->withErrors(['error' => 'Data booking tidak ditemukan.']);
        }

        // Validate bukti pembayaran
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,jpg,png|max:5120', // Max 5MB
            'voucher_code' => 'nullable|string',
            'voucher_discount' => 'nullable|numeric|min:0',
        ]);

        \DB::beginTransaction();
        try {
            // Handle voucher if provided
            $voucherData = null;
            $voucherDiscount = 0;
            
            if ($request->filled('voucher_code')) {
                $voucher = \App\Models\AffiliateVoucher::where('code', strtoupper($request->voucher_code))
                    ->first();
                
                if ($voucher && $voucher->isValid() && $voucher->canBeUsed($bookingData['total_price'])) {
                    $voucherDiscount = $voucher->calculateDiscount($bookingData['total_price']);
                    $voucherData = [
                        'voucher' => $voucher,
                        'discount' => $voucherDiscount
                    ];
                }
            }
            
            // Calculate final total after voucher discount
            $finalTotal = $bookingData['total_price'] - $voucherDiscount;
            
            // 1. Handle image upload with compression
            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $fileName = 'bukti_' . time() . '_' . uniqid() . '.jpg';
                
                // Load image
                $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
                if ($image === false) {
                    throw new \Exception('Gagal memproses gambar');
                }
                
                // Get original dimensions
                $originalWidth = imagesx($image);
                $originalHeight = imagesy($image);
                
                // Calculate new dimensions (max 1200px width)
                $maxWidth = 1200;
                if ($originalWidth > $maxWidth) {
                    $ratio = $maxWidth / $originalWidth;
                    $newWidth = $maxWidth;
                    $newHeight = (int)($originalHeight * $ratio);
                } else {
                    $newWidth = $originalWidth;
                    $newHeight = $originalHeight;
                }
                
                // Create new image with new dimensions
                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                
                // Save compressed image
                $storagePath = storage_path('app/public/bukti-pembayaran');
                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }
                $fullPath = $storagePath . '/' . $fileName;
                imagejpeg($newImage, $fullPath, 75); // 75% quality
                
                // Free memory
                imagedestroy($image);
                imagedestroy($newImage);
                
                $buktiPath = 'bukti-pembayaran/' . $fileName;
            }

            // 2. Create or get member
            $member = Member::firstOrCreate(
                ['telepon' => $bookingData['jamaah_phone']],
                [
                    'nama' => $bookingData['jamaah_name'],
                    'email' => $bookingData['jamaah_email'],
                    'id_tipe' => 1,
                    'is_jamaah' => true,
                ]
            );

            // 3. Get package to get outlet_id
            $package = TravelPackage::find($bookingData['package_id']);

            // 4. Create booking (without bukti_pembayaran - will be saved to payment instead)
            $booking = JamaahBooking::create([
                'booking_code' => JamaahBooking::generateBookingCode(),
                'id_travel_package' => $bookingData['package_id'],
                'id_member' => $member->id_member,
                'id_outlet' => $package->id_outlet ?? 1, // Add outlet_id from package
                'booking_date' => now()->toDateString(),
                'status' => 'pending',
                'total_price' => $finalTotal, // Use final total after voucher
                'payment_status' => 'unpaid',
                'paid_amount' => 0,
                'remaining_amount' => $finalTotal,
                'room_type' => $bookingData['room_type'],
                'price_package_name' => $bookingData['price_package_name'] ?? null,
                'price_variant' => $bookingData['price_variant'] ?? null,
                'family_members_booking' => !empty($bookingData['family_members']) ? json_encode($bookingData['family_members']) : null,
                'seller_name' => 'Website',
                'closing_source' => 'digital_marketing',
                'id_voucher' => $voucherData ? $voucherData['voucher']->id : null,
                'voucher_code' => $voucherData ? $voucherData['voucher']->code : null,
                'voucher_discount' => $voucherDiscount,
            ]);

            // Record voucher usage if applied
            if ($voucherData) {
                \App\Models\VoucherUsage::create([
                    'id_voucher' => $voucherData['voucher']->id,
                    'id_jamaah_booking' => $booking->id,
                    'discount_amount' => $voucherDiscount,
                    'original_amount' => $bookingData['total_price'],
                    'final_amount' => $finalTotal,
                    'used_at' => now(),
                ]);
                
                // Increment voucher usage count
                $voucherData['voucher']->incrementUsage();
            }

            // 5. Calculate payment amount based on payment type
            $totalPrice = $finalTotal; // Use final total after voucher
            $paymentAmount = 0;
            
            if ($bookingData['payment_type'] === 'dp') {
                $dpOption = $bookingData['dp_option'] ?? '25_percent';
                if ($dpOption === '25_percent') {
                    $paymentAmount = round($totalPrice * 0.25);
                } else {
                    $paymentAmount = 10000000;
                }
                $paymentAmount = min($paymentAmount, $totalPrice); // Tidak boleh lebih dari total
            } else {
                $paymentAmount = $totalPrice;
            }

            // 6. Generate receipt number
            $lastPayment = JamaahPayment::orderByRaw('CAST(SUBSTRING(receipt_number, 5) AS UNSIGNED) DESC')
                ->whereNotNull('receipt_number')
                ->where('receipt_number', '!=', '')
                ->first();
            $nextNumber = $lastPayment ? (intval(substr($lastPayment->receipt_number, 4)) + 1) : 1;
            $receiptNumber = 'KWT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // 7. Create payment record with bukti transfer
            $payment = JamaahPayment::create([
                'id_jamaah_booking' => $booking->id,
                'payment_date' => now()->toDateString(),
                'amount' => $paymentAmount,
                'payment_method' => 'transfer',
                'receipt_number' => $receiptNumber,
                'reference_number' => $booking->booking_code,
                'notes' => 'Pembayaran dari website publik',
                'recorded_by' => null, // Public payment, no user
                'bukti_transfer' => $buktiPath,
                'payment_type' => $bookingData['payment_type'],
            ]);

            // 8. Update booking payment status
            $booking->paid_amount = $paymentAmount;
            $booking->remaining_amount = $totalPrice - $paymentAmount;
            
            if ($booking->paid_amount >= $totalPrice) {
                $booking->payment_status = 'paid';
            } elseif ($booking->paid_amount > 0) {
                $booking->payment_status = 'partial';
            }
            $booking->save();

            // 9. Create add-ons (perlengkapan)
            if (!empty($bookingData['equipment'])) {
                foreach ($bookingData['equipment'] as $eq) {
                    \App\Models\BookingAddon::create([
                        'id_jamaah_booking' => $booking->id,
                        'id_produk' => $eq['id'],
                        'nama' => $eq['name'],
                        'keterangan' => 'Perlengkapan tambahan dari website',
                        'harga' => $eq['price'],
                        'qty' => $eq['qty'],
                        'masuk_hpp' => false,
                    ]);
                }
            }

            // 10. Create piutang
            \App\Models\Piutang::create([
                'id_jamaah_booking' => $booking->id,
                'id_member' => $member->id_member,
                'id_outlet' => $package->id_outlet ?? 1,
                'tanggal_piutang' => now(),
                'jumlah_piutang' => $totalPrice,
                'jumlah_dibayar' => $paymentAmount,
                'sisa_piutang' => $totalPrice - $paymentAmount,
                'status' => ($paymentAmount >= $totalPrice) ? 'lunas' : 'belum_lunas',
                'keterangan' => 'Booking Travel - ' . $booking->booking_code,
            ]);

            // 11. Create or update invoice
            $invoiceService = new InvoiceIntegrationService();
            $paymentTerm = $bookingData['payment_type'] === 'full' ? 'full' : 'installment';
            try {
                $invoice = $invoiceService->createInvoiceForJamaah($booking, $paymentTerm, $paymentAmount);
            } catch (\Exception $e) {
                \Log::error('Failed to create invoice for public booking: ' . $e->getMessage());
                $invoice = null;
            }

            // 12. Verify affiliate commission (referral sudah dibuat saat submitBooking)
            $affiliateService = new \App\Services\AffiliateTrackingService();
            try {
                // Verify the sale (converts pending to verified and credits balance)
                $verified = $affiliateService->verifySale($booking->id);
                if ($verified) {
                    \Log::info('Affiliate commission verified and credited for booking: ' . $booking->booking_code);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to verify affiliate sale: ' . $e->getMessage());
                // Don't fail the booking if affiliate verification fails
            }

            \DB::commit();

            // Clear session
            session()->forget(['booking_data', 'booking_token', 'booking_token_expires']);

            // Build WhatsApp message with payment details
            $waNumber = '628976688800';
            
            $message = "*KONFIRMASI BOOKING & PEMBAYARAN BERHASIL*\n";
            $message .= "================================\n\n";
            $message .= "*INFORMASI BOOKING*\n";
            $message .= "Kode Booking: *{$booking->booking_code}*\n";
            $message .= "No. Kwitansi: *{$receiptNumber}*\n";
            $message .= "Paket: {$package->package_name}\n";
            $message .= "Tipe Kamar: " . strtoupper($bookingData['room_type']) . "\n";
            $message .= "Pembayaran: " . ($bookingData['payment_type'] === 'full' ? 'Bayar Penuh' : 'Bayar DP') . "\n\n";
            
            $message .= "*DATA PEMESAN*\n";
            $message .= "Nama: {$bookingData['jamaah_name']}\n";
            $message .= "Telepon: {$bookingData['jamaah_phone']}\n";
            if (!empty($bookingData['jamaah_email'])) {
                $message .= "Email: {$bookingData['jamaah_email']}\n";
            }
            $message .= "\n";
            
            $message .= "*RINCIAN PEMBAYARAN*\n";
            if ($voucherDiscount > 0) {
                $message .= "Harga Asli: Rp " . number_format($bookingData['total_price'], 0, ',', '.') . "\n";
                $message .= "Diskon Voucher: -Rp " . number_format($voucherDiscount, 0, ',', '.') . "\n";
                $message .= "Total Harga: Rp " . number_format($totalPrice, 0, ',', '.') . "\n";
            } else {
                $message .= "Total Harga: Rp " . number_format($totalPrice, 0, ',', '.') . "\n";
            }
            $message .= "Jumlah Dibayar: Rp " . number_format($paymentAmount, 0, ',', '.') . "\n";
            $message .= "Sisa Tagihan: Rp " . number_format($totalPrice - $paymentAmount, 0, ',', '.') . "\n";
            $message .= "Status: " . ($booking->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS') . "\n\n";
            
            $message .= "Bukti pembayaran telah diupload dan tercatat dalam sistem.\n";
            $message .= "Tim kami akan segera memverifikasi pembayaran Anda.\n\n";
            $message .= "Terima kasih! 🙏";
            
            $waUrl = 'https://wa.me/' . $waNumber . '?text=' . urlencode($message);
            
            return redirect($waUrl);

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Failed to process booking payment: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Get products for equipment selection (AJAX)
     */
    public function getProducts(Request $request)
    {
        $search = $request->get('search', '');
        
        $products = \DB::table('produk')
            ->leftJoin('hpp_produk', 'produk.id_produk', '=', 'hpp_produk.id_produk')
            ->select(
                'produk.id_produk', 
                'produk.nama_produk', 
                'produk.harga_jual',
                \DB::raw('COALESCE(SUM(hpp_produk.stok), 0) as stok')
            )
            ->where('produk.is_active', 1)
            ->when($search, function($q) use ($search) {
                return $q->where('produk.nama_produk', 'like', "%{$search}%");
            })
            ->groupBy('produk.id_produk', 'produk.nama_produk', 'produk.harga_jual')
            ->having('stok', '>', 0)
            ->orderBy('produk.nama_produk')
            ->limit(50)
            ->get();
        
        return response()->json($products);
    }
}
