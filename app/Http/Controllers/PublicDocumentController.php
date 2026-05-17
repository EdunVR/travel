<?php

namespace App\Http\Controllers;

use App\Models\JamaahBooking;
use App\Models\JamaahPayment;
use App\Models\SalesInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicDocumentController extends Controller
{
    use KtpParserHelper;
    use PassportParserHelper;
    use VisaParserHelper;

    /**
     * Display full history page
     */
    public function sejarah()
    {
        return view('public.sejarah');
    }

    /**
     * Display all legality documents page
     */
    public function legalitas()
    {
        return view('public.legalitas');
    }

    /**
     * Display all hotel partners page
     */
    public function hotelPartner()
    {
        return view('public.hotel-partner');
    }

    /**
     * Public invoice PDF — accessible without login via signed token.
     * URL: /public/invoice/{booking}/{token}
     */
    public function invoice($bookingId, $token)
    {
        $booking = JamaahBooking::find($bookingId);

        if (!$booking || !$booking->id_invoice) {
            abort(404, 'Invoice tidak ditemukan');
        }

        // Verify token: sha256(booking_id + invoice_id + APP_KEY)
        $expected = hash('sha256', $booking->id . $booking->id_invoice . config('app.key'));
        if (!hash_equals($expected, $token)) {
            abort(403, 'Token tidak valid');
        }

        try {
            $invoice = SalesInvoice::with(['member', 'items', 'outlet'])
                ->findOrFail($booking->id_invoice);

            $booking->load([
                'travelPackage.flightDeparture',
                'travelPackage.flightReturn',
                'travelPackage.hotelMakkah',
                'travelPackage.hotelMadinah',
                'jamaah',
                'keberangkatan',
                'closedBy',
                'outlet',
                'payments',
                'addons',
                'hotelBookings.hotel'
            ]);

            // Use same method as receipt for consistency
            $companySettings = $booking->getCompanySettings();

            // Bank accounts
            $bankAccounts = collect();
            try {
                $bankAccounts = \App\Models\CompanyBankAccount::where('id_outlet', $booking->id_outlet)
                    ->where('is_active', 1)->orderBy('sort_order')->get();
                if ($bankAccounts->isEmpty()) {
                    $bankAccounts = \App\Models\CompanyBankAccount::whereNull('id_outlet')
                        ->where('is_active', 1)->orderBy('sort_order')->get();
                }
            } catch (\Exception $e) {}

            $termsConditions = $booking->terms_conditions ?? null;

            $pdf = PDF::loadView('admin.travel.payment.jamaah-invoice-pdf', [
                'invoice'          => $invoice,
                'booking'          => $booking,
                'companySettings'  => $companySettings,
                'bankAccounts'     => $bankAccounts,
                'termsConditions'  => $termsConditions,
            ])->setPaper('a4', 'portrait');

            return $pdf->stream('Invoice-' . $invoice->no_invoice . '.pdf');

        } catch (\Exception $e) {
            Log::error('Public invoice error: ' . $e->getMessage());
            abort(500, 'Gagal memuat invoice');
        }
    }

    /**
     * Public receipt PDF — accessible without login via signed token.
     * URL: /public/receipt/{payment}/{token}
     */
    public function receipt($paymentId, $token)
    {
        $payment = JamaahPayment::with(['jamaahBooking.jamaah', 'jamaahBooking.travelPackage', 'recordedBy'])
            ->find($paymentId);

        if (!$payment) {
            abort(404, 'Kwitansi tidak ditemukan');
        }

        // Verify token: sha256(payment_id + booking_id + APP_KEY)
        $expected = hash('sha256', $payment->id . $payment->id_jamaah_booking . config('app.key'));
        if (!hash_equals($expected, $token)) {
            abort(403, 'Token tidak valid');
        }

        try {
            $companySettings = $payment->jamaahBooking->getCompanySettings();

            $pdf = PDF::loadView('admin.travel.payment.receipt-pdf', compact('payment', 'companySettings'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('Kwitansi-' . $payment->receipt_number . '.pdf');

        } catch (\Exception $e) {
            Log::error('Public receipt error: ' . $e->getMessage());
            abort(500, 'Gagal memuat kwitansi');
        }
    }

    /**
     * Helper: get company settings for a given outlet (static-ish, no auth needed)
     */
    protected function getCompanySettingsForPrint($outletId = null)
    {
        try {
            // Try outlet-specific settings first
            $settings = null;
            if ($outletId) {
                $settings = \App\Models\CompanySetting::where('outlet_id', $outletId)->first();
            }
            if (!$settings) {
                $settings = \App\Models\CompanySetting::first();
            }
            if (!$settings) return $this->getDefaultSettings();

            return [
                'company_name'        => $settings->company_name ?? config('app.name'),
                'company_address'     => $settings->company_address ?? '',
                'formatted_address'   => $settings->formatted_address ?? $settings->company_address ?? '',
                'company_phone'       => $settings->company_phone ?? '',
                'formatted_phone'     => $settings->formatted_phone ?? $settings->company_phone ?? '',
                'company_email'       => $settings->company_email ?? '',
                'company_logo'        => $settings->company_logo ?? null,
                'bank_name'           => $settings->bank_name ?? '',
                'bank_account_number' => $settings->bank_account_number ?? '',
                'bank_account_name'   => $settings->bank_account_name ?? '',
            ];
        } catch (\Exception $e) {
            return $this->getDefaultSettings();
        }
    }

    private function getDefaultSettings(): array
    {
        return [
            'company_name'       => config('app.name'),
            'company_address'    => '',
            'formatted_address'  => '',
            'company_phone'      => '',
            'formatted_phone'    => '',
            'company_email'      => '',
            'company_logo'       => null,
            'bank_name'          => '',
            'bank_account_number'=> '',
            'bank_account_name'  => '',
        ];
    }

    /**
     * Show public manifest form for customer to fill
     */
    public function manifestForm($bookingId)
    {
        $booking = JamaahBooking::with(['travelPackage', 'jamaah', 'keberangkatan'])
            ->findOrFail($bookingId);

        // Check if booking has at least one payment
        if ($booking->payments()->count() == 0) {
            return view('public.manifest-not-available', [
                'message' => 'Form manifest hanya tersedia setelah melakukan pembayaran pertama.'
            ]);
        }

        return view('public.manifest-multi-tab', compact('booking'));
    }

    /**
     * OCR Passport endpoint for public manifest form
     */
    public function ocrPassport(Request $request)
    {
        $request->validate([
            'passport_image' => 'required|image|mimes:jpeg,jpg,png|max:5120'
        ]);

        try {
            $file = $request->file('passport_image');
            $path = $file->store('temp/passport_ocr', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Check if Tesseract is available
            if (!$this->isTesseractAvailable()) {
                \Storage::disk('public')->delete($path);
                
                return response()->json([
                    'success' => false,
                    'message' => 'OCR tidak tersedia. Silakan isi data secara manual.'
                ], 500);
            }

            // Preprocess image
            $processedPath = $this->preprocessImage($fullPath);

            // Perform OCR with multiple PSM modes
            $text = '';
            $results = [];
            
            foreach ([3, 4, 6] as $psm) {
                try {
                    $ocrText = \OnePointHub\LaravelOcr\Facades\Ocr::scan($processedPath, 'eng', $psm);
                    $results[] = [
                        'text' => $ocrText,
                        'length' => strlen($ocrText),
                        'psm' => $psm
                    ];
                } catch (\Exception $e) {
                    \Log::warning("PSM $psm failed: " . $e->getMessage());
                }
            }

            // Pick best result (longest text with MRZ pattern)
            if (!empty($results)) {
                usort($results, function($a, $b) {
                    $scoreA = $a['length'] + (preg_match('/P<[A-Z]{3}/', $a['text']) ? 300 : 0);
                    $scoreB = $b['length'] + (preg_match('/P<[A-Z]{3}/', $b['text']) ? 300 : 0);
                    return $scoreB - $scoreA;
                });
                $text = $results[0]['text'];
            }

            // Parse passport data using trait method
            $parsedData = $this->parsePassportTextNew($text);

            // Clean up temp files
            if ($processedPath !== $fullPath) {
                @unlink($processedPath);
            }
            \Storage::disk('public')->delete($path);

            return response()->json([
                'success' => true,
                'data' => [
                    'passport_nomor' => $parsedData['nomor'] ?? '',
                    'passport_nama' => $parsedData['nama'] ?? '',
                    'passport_tanggal_lahir' => $parsedData['tanggal_lahir'] ?? '',
                    'passport_tanggal_kadaluarsa' => $parsedData['tanggal_kadaluarsa'] ?? '',
                    'passport_kewarganegaraan' => $parsedData['kewarganegaraan'] ?? '',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('OCR Passport error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * OCR KTP endpoint for public manifest form
     */
    public function ocrKtp(Request $request)
    {
        $request->validate([
            'ktp_image' => 'required|image|mimes:jpeg,jpg,png|max:5120'
        ]);

        try {
            $file = $request->file('ktp_image');
            $path = $file->store('temp/ktp_ocr', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Check if Tesseract is available
            if (!$this->isTesseractAvailable()) {
                \Storage::disk('public')->delete($path);
                
                return response()->json([
                    'success' => false,
                    'message' => 'OCR tidak tersedia. Silakan isi data secara manual.'
                ], 500);
            }

            // Preprocess image
            $processedPath = $this->preprocessImage($fullPath);

            // Perform OCR with multiple PSM modes
            $text = '';
            $results = [];
            
            foreach ([3, 4, 6] as $psm) {
                try {
                    $ocrText = \OnePointHub\LaravelOcr\Facades\Ocr::scan($processedPath, 'ind', $psm);
                    $results[] = [
                        'text' => $ocrText,
                        'length' => strlen($ocrText),
                        'psm' => $psm
                    ];
                } catch (\Exception $e) {
                    \Log::warning("PSM $psm failed: " . $e->getMessage());
                }
            }

            // Pick best result (longest text)
            if (!empty($results)) {
                usort($results, function($a, $b) {
                    return $b['length'] - $a['length'];
                });
                $text = $results[0]['text'];
            }

            // Parse KTP data using trait method
            $parsedData = $this->parseKtpTextNew($text);

            // Clean up temp files
            if ($processedPath !== $fullPath) {
                @unlink($processedPath);
            }
            \Storage::disk('public')->delete($path);

            return response()->json([
                'success' => true,
                'data' => [
                    'ktp_nik' => $parsedData['nik'] ?? '',
                    'ktp_nama' => $parsedData['nama'] ?? '',
                    'ktp_tempat_lahir' => $parsedData['tempat_lahir'] ?? '',
                    'ktp_tanggal_lahir' => $parsedData['tanggal_lahir'] ?? '',
                    'ktp_alamat' => $parsedData['alamat'] ?? '',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('OCR KTP error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if Tesseract is available
     */
    private function isTesseractAvailable()
    {
        try {
            $tesseractPaths = [
                'tesseract',
                'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
                'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
            ];
            
            foreach ($tesseractPaths as $path) {
                $output = [];
                $returnVar = 0;
                @exec('"' . $path . '" --version 2>&1', $output, $returnVar);
                
                if ($returnVar === 0) {
                    config(['ocr.engines.tesseract.executable' => $path]);
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Preprocess image for better OCR
     */
    private function preprocessImage($imagePath)
    {
        try {
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($imagePath);
            
            // Resize if too large
            if ($image->width() > 2000) {
                $image->scale(width: 2000);
            }
            
            // Increase contrast and brightness
            $image->brightness(10);
            $image->contrast(15);
            
            // Convert to grayscale
            $image->greyscale();
            
            // Save processed image
            $processedPath = sys_get_temp_dir() . '/processed_' . basename($imagePath);
            $image->save($processedPath);
            
            return $processedPath;
        } catch (\Exception $e) {
            \Log::warning('Image preprocessing failed: ' . $e->getMessage());
            return $imagePath;
        }
    }

    /**
     * Submit manifest data from customer
     * Saves to Member table (same as admin manifest tab)
     */
    public function submitManifest(Request $request, $bookingId)
    {
        $booking = JamaahBooking::with('jamaah')->findOrFail($bookingId);

        // Validate: only passport (required) + KTP (optional)
        $validated = $request->validate([
            'passport_foto' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'passport_nomor' => 'required|string|max:50',
            'passport_nama' => 'nullable|string|max:255',
            'passport_tanggal_lahir' => 'nullable|date',
            'passport_tanggal_kadaluarsa' => 'required|date',
            'passport_kewarganegaraan' => 'nullable|string|max:100',
            'ktp_foto' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'ktp_nik' => 'nullable|string|regex:/^\d{16}$/',
            'ktp_nama' => 'nullable|string|max:255',
            'ktp_tempat_lahir' => 'nullable|string|max:255',
            'ktp_tanggal_lahir' => 'nullable|date',
            'ktp_alamat' => 'nullable|string',
        ]);

        try {
            \DB::beginTransaction();

            // Get member from booking
            $member = $booking->jamaah;
            if (!$member) {
                throw new \Exception('Member tidak ditemukan untuk booking ini');
            }

            $updateData = [];

            // Handle passport photo upload
            if ($request->hasFile('passport_foto')) {
                // Delete old file if exists
                if ($member->passport_foto && \Storage::disk('public')->exists($member->passport_foto)) {
                    \Storage::disk('public')->delete($member->passport_foto);
                }
                $updateData['passport_foto'] = $request->file('passport_foto')->store('pelanggan/passport', 'public');
            }

            // Handle KTP photo upload (optional)
            if ($request->hasFile('ktp_foto')) {
                // Delete old file if exists
                if ($member->ktp_foto && \Storage::disk('public')->exists($member->ktp_foto)) {
                    \Storage::disk('public')->delete($member->ktp_foto);
                }
                $updateData['ktp_foto'] = $request->file('ktp_foto')->store('pelanggan/ktp', 'public');
            }

            // Update passport data
            $updateData['passport_nomor'] = $validated['passport_nomor'];
            $updateData['passport_tanggal_kadaluarsa'] = $validated['passport_tanggal_kadaluarsa'];
            
            if (!empty($validated['passport_nama'])) {
                $updateData['passport_nama'] = $validated['passport_nama'];
            }
            if (!empty($validated['passport_tanggal_lahir'])) {
                $updateData['passport_tanggal_lahir'] = $validated['passport_tanggal_lahir'];
            }
            if (!empty($validated['passport_kewarganegaraan'])) {
                $updateData['passport_kewarganegaraan'] = $validated['passport_kewarganegaraan'];
            }

            // Update KTP data (optional)
            if (!empty($validated['ktp_nik'])) {
                $updateData['ktp_nik'] = $validated['ktp_nik'];
            }
            if (!empty($validated['ktp_nama'])) {
                $updateData['ktp_nama'] = $validated['ktp_nama'];
            }
            if (!empty($validated['ktp_tempat_lahir'])) {
                $updateData['ktp_tempat_lahir'] = $validated['ktp_tempat_lahir'];
            }
            if (!empty($validated['ktp_tanggal_lahir'])) {
                $updateData['ktp_tanggal_lahir'] = $validated['ktp_tanggal_lahir'];
            }
            if (!empty($validated['ktp_alamat'])) {
                $updateData['ktp_alamat'] = $validated['ktp_alamat'];
            }

            // Update member table (same as admin manifest tab)
            $member->update($updateData);

            \DB::commit();

            Log::info('Manifest submitted for booking', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'member_id' => $member->id_member
            ]);

            return redirect()->route('public.booking.manifest', $bookingId)
                ->with('success', 'Data manifest berhasil disimpan! Terima kasih.');

        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Failed to submit manifest: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data manifest: ' . $e->getMessage());
        }
    }
}
