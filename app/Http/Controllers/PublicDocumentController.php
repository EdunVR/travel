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
            'passport_image' => 'required|mimes:jpeg,jpg,png,pdf|max:5120'
        ]);

        try {
            $file = $request->file('passport_image');
            $path = $file->store('temp/passport_ocr', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Use Google Vision (primary) or Tesseract (fallback)
            $vision = new \App\Services\GoogleVisionService();
            $text = '';

            if ($vision->isAvailable()) {
                $text = $vision->extractText($fullPath) ?? '';
            }

            // Fallback to Tesseract for images only
            if (empty($text) && $file->getMimeType() !== 'application/pdf' && $this->isTesseractAvailable()) {
                $processedPath = $this->preprocessImage($fullPath);
                $results = [];
                foreach ([3, 4, 6] as $psm) {
                    try {
                        $ocrText = \OnePointHub\LaravelOcr\Facades\Ocr::scan($processedPath, 'eng', $psm);
                        $results[] = ['text' => $ocrText, 'length' => strlen($ocrText)];
                    } catch (\Exception $e) {}
                }
                if (!empty($results)) {
                    usort($results, fn($a, $b) => $b['length'] - $a['length']);
                    $text = $results[0]['text'];
                }
                if ($processedPath !== $fullPath) @unlink($processedPath);
            }

            \Storage::disk('public')->delete($path);

            if (empty($text)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OCR tidak dapat mengekstrak teks dari passport. Silakan isi data secara manual.',
                ]);
            }

            $parsedData = $this->parsePassportTextNew($text);

            return response()->json([
                'success' => true,
                'data' => [
                    'passport_nomor' => $parsedData['nomor'] ?? '',
                    'passport_nama' => $parsedData['nama'] ?? '',
                    'passport_tanggal_lahir' => $parsedData['tanggal_lahir'] ?? '',
                    'passport_tanggal_kadaluarsa' => $parsedData['tanggal_kadaluarsa'] ?? '',
                    'passport_kewarganegaraan' => $parsedData['kewarganegaraan'] ?? '',
                    'passport_title' => $parsedData['title'] ?? '',
                    'passport_gender' => $parsedData['gender'] ?? '',
                    'passport_tanggal_terbit' => $parsedData['tanggal_terbit'] ?? '',
                    'passport_kantor_terbit' => $parsedData['kantor_terbit'] ?? '',
                    'passport_tempat_lahir' => $parsedData['tempat_lahir'] ?? '',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('OCR Passport error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR. Silakan isi data passport secara manual.',
            ]);
        }
    }

    /**
     * OCR KTP endpoint for public manifest form
     */
    public function ocrKtp(Request $request)
    {
        $request->validate([
            'ktp_image' => 'required|mimes:jpeg,jpg,png,pdf|max:5120'
        ]);

        try {
            $file = $request->file('ktp_image');
            $path = $file->store('temp/ktp_ocr', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Use Google Vision (primary) or Tesseract (fallback)
            $vision = new \App\Services\GoogleVisionService();
            $text = '';

            if ($vision->isAvailable()) {
                $text = $vision->extractText($fullPath) ?? '';
            }

            // Fallback to Tesseract for images only
            if (empty($text) && $file->getMimeType() !== 'application/pdf' && $this->isTesseractAvailable()) {
                $processedPath = $this->preprocessImage($fullPath);
                $results = [];
                foreach ([3, 4, 6] as $psm) {
                    try {
                        $ocrText = \OnePointHub\LaravelOcr\Facades\Ocr::scan($processedPath, 'ind', $psm);
                        $results[] = ['text' => $ocrText, 'length' => strlen($ocrText)];
                    } catch (\Exception $e) {}
                }
                if (!empty($results)) {
                    usort($results, fn($a, $b) => $b['length'] - $a['length']);
                    $text = $results[0]['text'];
                }
                if ($processedPath !== $fullPath) @unlink($processedPath);
            }

            \Storage::disk('public')->delete($path);

            if (empty($text)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OCR tidak dapat mengekstrak teks dari KTP. Silakan isi data secara manual.',
                ]);
            }

            $parsedData = $this->parseKtpTextNew($text);

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
                'message' => 'Gagal memproses OCR. Silakan isi data KTP secara manual.',
            ]);
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
     * Save manifest data (text fields) via AJAX — no file upload.
     * Saves jamaah utama to Member table, anggota keluarga to booking.family_members_booking.
     */
    public function saveManifestData(Request $request, $bookingId)
    {
        $booking = JamaahBooking::with('jamaah')->findOrFail($bookingId);

        $request->validate([
            'jamaah_data'   => 'required|array',
            'jamaah_data.*.index' => 'required|integer',
        ]);

        try {
            \DB::beginTransaction();

            $allData = $request->input('jamaah_data', []);

            // Separate main jamaah (index 0) from family members
            $mainData   = null;
            $familyData = [];

            foreach ($allData as $item) {
                if ((int)$item['index'] === 0) {
                    $mainData = $item;
                } else {
                    $familyData[] = $item;
                }
            }

            // --- Update jamaah utama (Member table) ---
            if ($mainData && $booking->jamaah) {
                $member = $booking->jamaah;
                $updateFields = [
                    'passport_nomor'             => $mainData['passport_nomor'] ?? null,
                    'passport_nama'              => $mainData['passport_nama'] ?? null,
                    'passport_tanggal_lahir'     => $mainData['passport_tanggal_lahir'] ?? null,
                    'passport_tanggal_kadaluarsa'=> $mainData['passport_tanggal_kadaluarsa'] ?? null,
                    'passport_kewarganegaraan'   => $mainData['passport_kewarganegaraan'] ?? null,
                    'passport_title'             => $mainData['passport_title'] ?? null,
                    'passport_gender'            => $mainData['passport_gender'] ?? null,
                    'passport_tanggal_terbit'    => $mainData['passport_tanggal_terbit'] ?? null,
                    'passport_kantor_terbit'     => $mainData['passport_kantor_terbit'] ?? null,
                    'passport_tempat_lahir'      => $mainData['passport_tempat_lahir'] ?? null,
                    'ktp_nik'                    => $mainData['ktp_nik'] ?? null,
                    'ktp_nama'                   => $mainData['ktp_nama'] ?? null,
                    'ktp_tempat_lahir'           => $mainData['ktp_tempat_lahir'] ?? null,
                    'ktp_tanggal_lahir'          => $mainData['ktp_tanggal_lahir'] ?? null,
                    'ktp_alamat'                 => $mainData['ktp_alamat'] ?? null,
                ];
                // Only update non-empty fields
                $updateFields = array_filter($updateFields, fn($v) => $v !== null && $v !== '');
                if (!empty($updateFields)) {
                    $member->update($updateFields);
                }
            }

            // --- Update anggota keluarga (family_members_booking) ---
            if (!empty($familyData)) {
                $existingFamily = $booking->family_members_booking;
                if (is_string($existingFamily)) {
                    $existingFamily = json_decode($existingFamily, true) ?? [];
                }
                if (!is_array($existingFamily)) $existingFamily = [];

                foreach ($familyData as $item) {
                    $idx = (int)$item['index'] - 1; // family index starts at 1 in JS
                    if (isset($existingFamily[$idx])) {
                        // Merge manifest data into existing family member
                        $existingFamily[$idx] = array_merge($existingFamily[$idx], [
                            'passport_nomor'              => $item['passport_nomor'] ?? ($existingFamily[$idx]['passport_nomor'] ?? ''),
                            'passport_nama'               => $item['passport_nama'] ?? ($existingFamily[$idx]['passport_nama'] ?? ''),
                            'passport_tanggal_lahir'      => $item['passport_tanggal_lahir'] ?? ($existingFamily[$idx]['passport_tanggal_lahir'] ?? ''),
                            'passport_tanggal_kadaluarsa' => $item['passport_tanggal_kadaluarsa'] ?? ($existingFamily[$idx]['passport_tanggal_kadaluarsa'] ?? ''),
                            'passport_kewarganegaraan'    => $item['passport_kewarganegaraan'] ?? ($existingFamily[$idx]['passport_kewarganegaraan'] ?? ''),
                            'passport_title'              => $item['passport_title'] ?? ($existingFamily[$idx]['passport_title'] ?? ''),
                            'passport_gender'             => $item['passport_gender'] ?? ($existingFamily[$idx]['passport_gender'] ?? ''),
                            'passport_tanggal_terbit'     => $item['passport_tanggal_terbit'] ?? ($existingFamily[$idx]['passport_tanggal_terbit'] ?? ''),
                            'passport_kantor_terbit'      => $item['passport_kantor_terbit'] ?? ($existingFamily[$idx]['passport_kantor_terbit'] ?? ''),
                            'passport_tempat_lahir'       => $item['passport_tempat_lahir'] ?? ($existingFamily[$idx]['passport_tempat_lahir'] ?? ''),
                            'ktp_nik'                     => $item['ktp_nik'] ?? ($existingFamily[$idx]['ktp_nik'] ?? ''),
                            'ktp_nama'                    => $item['ktp_nama'] ?? ($existingFamily[$idx]['ktp_nama'] ?? ''),
                            'ktp_tempat_lahir'            => $item['ktp_tempat_lahir'] ?? ($existingFamily[$idx]['ktp_tempat_lahir'] ?? ''),
                            'ktp_tanggal_lahir'           => $item['ktp_tanggal_lahir'] ?? ($existingFamily[$idx]['ktp_tanggal_lahir'] ?? ''),
                            'ktp_alamat'                  => $item['ktp_alamat'] ?? ($existingFamily[$idx]['ktp_alamat'] ?? ''),
                        ]);
                    }
                }

                $booking->update(['family_members_booking' => json_encode($existingFamily)]);

                // Also update member's family_members field
                if ($booking->jamaah) {
                    $booking->jamaah->update(['family_members' => $existingFamily]);
                }
            }

            \DB::commit();

            Log::info('Manifest data saved', ['booking_id' => $bookingId, 'jamaah_count' => count($allData)]);

            return response()->json(['success' => true, 'message' => 'Data manifest berhasil disimpan']);

        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Failed to save manifest data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Upload satu dokumen manifest via AJAX (langsung simpan ke storage & DB).
     * POST /booking/{bookingId}/manifest/upload-doc
     * Body: doc_type (string), file (file), member_index (int, 0=jamaah utama)
     */
    public function uploadManifestDocument(Request $request, $bookingId)
    {
        $booking = JamaahBooking::with('jamaah')->findOrFail($bookingId);

        $request->validate([
            'doc_type'     => 'required|string|in:passport_foto,ktp_foto,akta_lahir_foto,kartu_keluarga_foto,buku_nikah_foto,vaksin_foto,bpjs_foto,pas_foto',
            'file'         => 'required|mimes:jpg,jpeg,png,pdf|max:5120',
            'member_index' => 'nullable|integer|min:0',
        ]);

        $docType     = $request->input('doc_type');
        $memberIndex = (int) $request->input('member_index', 0);

        // Map doc_type → storage folder
        $folderMap = [
            'passport_foto'        => 'pelanggan/passport',
            'ktp_foto'             => 'pelanggan/ktp',
            'akta_lahir_foto'      => 'pelanggan/akta_lahir',
            'kartu_keluarga_foto'  => 'pelanggan/kartu_keluarga',
            'buku_nikah_foto'      => 'pelanggan/buku_nikah',
            'vaksin_foto'          => 'pelanggan/vaksin',
            'bpjs_foto'            => 'pelanggan/bpjs',
            'pas_foto'             => 'pelanggan/pas_foto',
        ];
        $folder = $folderMap[$docType] ?? 'pelanggan/dokumen';

        try {
            \DB::beginTransaction();

            if ($memberIndex === 0) {
                // Jamaah utama → simpan ke Member
                $member = $booking->jamaah;
                if (!$member) {
                    return response()->json(['success' => false, 'message' => 'Member tidak ditemukan'], 404);
                }

                // Hapus file lama jika ada
                if ($member->$docType && \Storage::disk('public')->exists($member->$docType)) {
                    \Storage::disk('public')->delete($member->$docType);
                }

                $path = $request->file('file')->store($folder, 'public');
                $member->update([$docType => $path]);

                \DB::commit();
                return response()->json([
                    'success'  => true,
                    'message'  => 'Dokumen berhasil diupload',
                    'file_url' => asset('storage/' . $path),
                    'file_path'=> $path,
                    'is_pdf'   => strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf',
                ]);

            } else {
                // Anggota keluarga → simpan ke family_members_booking
                $familyMembers = $booking->family_members_booking;
                if (is_string($familyMembers)) {
                    $familyMembers = json_decode($familyMembers, true) ?? [];
                }
                if (!is_array($familyMembers)) $familyMembers = [];

                $familyIdx = $memberIndex - 1;
                if (!isset($familyMembers[$familyIdx])) {
                    return response()->json(['success' => false, 'message' => 'Anggota keluarga tidak ditemukan'], 404);
                }

                // Hapus file lama jika ada
                $oldPath = $familyMembers[$familyIdx][$docType] ?? null;
                if ($oldPath && \Storage::disk('public')->exists($oldPath)) {
                    \Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('file')->store($folder, 'public');
                $familyMembers[$familyIdx][$docType] = $path;
                $booking->update(['family_members_booking' => json_encode($familyMembers)]);

                \DB::commit();
                return response()->json([
                    'success'  => true,
                    'message'  => 'Dokumen berhasil diupload',
                    'file_url' => asset('storage/' . $path),
                    'file_path'=> $path,
                    'is_pdf'   => strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf',
                ]);
            }

        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('uploadManifestDocument error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal upload: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hapus satu dokumen manifest via AJAX.
     * DELETE /booking/{bookingId}/manifest/delete-doc
     * Body: doc_type (string), member_index (int)
     */
    public function deleteManifestDocument(Request $request, $bookingId)
    {
        $booking = JamaahBooking::with('jamaah')->findOrFail($bookingId);

        $request->validate([
            'doc_type'     => 'required|string|in:passport_foto,ktp_foto,akta_lahir_foto,kartu_keluarga_foto,buku_nikah_foto,vaksin_foto,bpjs_foto,pas_foto',
            'member_index' => 'nullable|integer|min:0',
        ]);

        $docType     = $request->input('doc_type');
        $memberIndex = (int) $request->input('member_index', 0);

        try {
            \DB::beginTransaction();

            if ($memberIndex === 0) {
                $member = $booking->jamaah;
                if (!$member) {
                    return response()->json(['success' => false, 'message' => 'Member tidak ditemukan'], 404);
                }
                if ($member->$docType && \Storage::disk('public')->exists($member->$docType)) {
                    \Storage::disk('public')->delete($member->$docType);
                }
                $member->update([$docType => null]);
            } else {
                $familyMembers = $booking->family_members_booking;
                if (is_string($familyMembers)) {
                    $familyMembers = json_decode($familyMembers, true) ?? [];
                }
                if (!is_array($familyMembers)) $familyMembers = [];

                $familyIdx = $memberIndex - 1;
                if (!isset($familyMembers[$familyIdx])) {
                    return response()->json(['success' => false, 'message' => 'Anggota keluarga tidak ditemukan'], 404);
                }

                $oldPath = $familyMembers[$familyIdx][$docType] ?? null;
                if ($oldPath && \Storage::disk('public')->exists($oldPath)) {
                    \Storage::disk('public')->delete($oldPath);
                }
                $familyMembers[$familyIdx][$docType] = null;
                $booking->update(['family_members_booking' => json_encode($familyMembers)]);
            }

            \DB::commit();
            return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus']);

        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('deleteManifestDocument error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal hapus: ' . $e->getMessage()], 500);
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
            'passport_foto' => 'required|mimes:jpeg,jpg,png,pdf|max:5120',
            'passport_nomor' => 'required|string|max:50',
            'passport_nama' => 'nullable|string|max:255',
            'passport_tanggal_lahir' => 'nullable|date',
            'passport_tanggal_kadaluarsa' => 'required|date',
            'passport_kewarganegaraan' => 'nullable|string|max:100',
            'ktp_foto' => 'nullable|mimes:jpeg,jpg,png,pdf|max:5120',
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
