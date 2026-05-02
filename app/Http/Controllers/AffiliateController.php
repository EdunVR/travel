<?php

namespace App\Http\Controllers;

use App\Models\Affiliator;
use App\Models\PartnershipProgram;
use App\Models\AffiliateReferral;
use App\Models\AffiliatePayout;
use App\Models\AffiliateSetting;
use App\Services\AffiliateTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AffiliateController extends Controller
{
    protected $trackingService;

    public function __construct(AffiliateTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Halaman pendaftaran affiliator
     */
    public function register()
    {
        $programs = PartnershipProgram::active()->ordered()->get();

        // Data untuk dropdown jenjang
        $hmMasters  = Affiliator::active()
            ->whereHas('partnershipProgram', fn($q) => $q->where('slug', 'hm-master'))
            ->get(['id', 'full_name', 'username']);

        $hmLeaders  = Affiliator::active()
            ->whereHas('partnershipProgram', fn($q) => $q->where('slug', 'hm-leader'))
            ->get(['id', 'full_name', 'username']);

        $hmPartners = Affiliator::active()
            ->whereHas('partnershipProgram', fn($q) => $q->where('slug', 'hm-partner'))
            ->get(['id', 'full_name', 'username']);

        return view('affiliate.register', compact('programs', 'hmMasters', 'hmLeaders', 'hmPartners'));
    }

    /**
     * Proses pendaftaran affiliator - Step 1: Simpan data dan redirect ke pembayaran
     */
    public function storeRegistration(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|unique:affiliators,username|alpha_dash|min:3|max:50',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|unique:affiliators,phone_number',
            'email' => 'required|email|unique:affiliators,email',
            'photo' => 'nullable|image|max:2048',
            'partnership_program_id' => 'required|exists:partnership_programs,id',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'upline_master_id' => 'nullable|exists:affiliators,id',
            'upline_leader_id' => 'nullable|exists:affiliators,id',
            'upline_partner_id' => 'nullable|exists:affiliators,id',
        ]);

        $program = PartnershipProgram::findOrFail($request->partnership_program_id);

        // Validasi jika program memerlukan booking sebelumnya
        if ($program->requires_previous_booking) {
            $hasBooking = \App\Models\JamaahBooking::whereHas('member', function($query) use ($request) {
                    $query->where('telepon', $request->phone_number);
                })
                ->where('status', 'confirmed')
                ->exists();
            
            if (!$hasBooking) {
                return back()->withErrors([
                    'partnership_program_id' => 'Program HM Member hanya untuk alumni jamaah yang sudah pernah order paket.'
                ])->withInput();
            }
        }

        // Simpan data sementara di session untuk digunakan nanti
        session([
            'affiliate_registration_data' => [
                'full_name' => $request->full_name,
                'username' => strtolower($request->username),
                'password' => $request->password, // Will be hashed later
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'partnership_program_id' => $program->id,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_name' => $request->bank_account_name,
                'ppc_commission' => $program->default_ppc_commission ?? 50,
                'min_sale_commission' => $program->min_sale_commission ?? 500000,
                'upline_master_id' => $request->upline_master_id,
                'upline_leader_id' => $request->upline_leader_id,
                'upline_partner_id' => $request->upline_partner_id,
            ]
        ]);

        // Upload dan compress foto jika ada
        if ($request->hasFile('photo')) {
            $photoPath = $this->compressAndUploadImage($request->file('photo'), 'affiliator-photos-temp');
            session(['affiliate_registration_photo' => $photoPath]);
        }

        // Generate unique token untuk link pembayaran
        $token = bin2hex(random_bytes(32));
        session(['affiliate_registration_token' => $token]);

        // Generate payment confirmation link
        $paymentLink = route('affiliate.payment', ['token' => $token]);

        // Generate WhatsApp autotext message
        $companyPhone = env('COMPANY_PHONE', '08976688800');
        $message = "Halo, saya {$request->full_name} ingin mendaftar sebagai mitra {$program->name}.\n\n";
        $message .= "📋 *Data Saya:*\n";
        $message .= "Nama: {$request->full_name}\n";
        $message .= "Username: " . strtolower($request->username) . "\n";
        $message .= "HP: {$request->phone_number}\n";
        $message .= "Email: {$request->email}\n\n";
        $message .= "🎯 *Program:* {$program->name}\n";
        $message .= "💰 *Biaya:* {$program->formatted_fee}\n\n";
        $message .= "🔗 *Link Konfirmasi Pembayaran:*\n";
        $message .= $paymentLink . "\n\n";
        $message .= "Mohon informasi lebih lanjut. Terima kasih! 🙏";
        
        // Redirect to WhatsApp with autotext
        $waUrl = "https://wa.me/{$companyPhone}?text=" . urlencode($message);
        
        return redirect($waUrl);
    }

    /**
     * Halaman pembayaran dengan token
     */
    public function showPaymentPage($token)
    {
        // Validasi token
        if (!session('affiliate_registration_token') || session('affiliate_registration_token') !== $token) {
            return redirect()->route('affiliate.register')
                ->withErrors(['error' => 'Link pembayaran tidak valid atau sudah kadaluarsa.']);
        }

        $registrationData = session('affiliate_registration_data');
        if (!$registrationData) {
            return redirect()->route('affiliate.register')
                ->withErrors(['error' => 'Data pendaftaran tidak ditemukan.']);
        }

        $program = PartnershipProgram::find($registrationData['partnership_program_id']);
        
        return view('affiliate.payment', compact('program', 'registrationData', 'token'));
    }

    /**
     * Proses pembayaran dan simpan ke database
     */
    public function processPayment(Request $request, $token)
    {
        // Validasi token
        if (!session('affiliate_registration_token') || session('affiliate_registration_token') !== $token) {
            return back()->withErrors(['error' => 'Link pembayaran tidak valid atau sudah kadaluarsa.']);
        }

        $registrationData = session('affiliate_registration_data');
        if (!$registrationData) {
            return back()->withErrors(['error' => 'Data pendaftaran tidak ditemukan.']);
        }

        $program = PartnershipProgram::findOrFail($registrationData['partnership_program_id']);

        // Validasi bukti pembayaran jika program berbayar
        if ($program->registration_fee > 0) {
            $request->validate([
                'payment_proof' => 'required|image|max:2048',
            ]);
        }

        // Upload dan compress bukti pembayaran
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $this->compressAndUploadImage($request->file('payment_proof'), 'payment-proofs');
        }

        // Get photo from session
        $photoPath = session('affiliate_registration_photo');

        $autoApprove = $program->registration_fee == 0; // Auto approve untuk program gratis

        $affiliator = Affiliator::create([
            'full_name' => $registrationData['full_name'],
            'username' => $registrationData['username'],
            'password' => Hash::make($registrationData['password']),
            'phone_number' => $registrationData['phone_number'],
            'email' => $registrationData['email'],
            'photo' => $photoPath,
            'partnership_program_id' => $program->id,
            'payment_proof' => $paymentProofPath,
            'ppc_commission' => $registrationData['ppc_commission'],
            'min_sale_commission' => $registrationData['min_sale_commission'],
            'cookie_lifetime' => 30, // Default 30 hari
            'bank_name' => $registrationData['bank_name'],
            'bank_account_number' => $registrationData['bank_account_number'],
            'bank_account_name' => $registrationData['bank_account_name'],
            'upline_master_id' => $registrationData['upline_master_id'] ?? null,
            'upline_leader_id' => $registrationData['upline_leader_id'] ?? null,
            'upline_partner_id' => $registrationData['upline_partner_id'] ?? null,
            'status' => $autoApprove ? 'active' : 'pending',
            'approved_at' => $autoApprove ? now() : null,
        ]);

        // Clear session data
        session()->forget(['affiliate_registration_data', 'affiliate_registration_token', 'affiliate_registration_photo']);

        // Kirim notifikasi WA ke mitra (konfirmasi pembayaran)
        $this->sendAffiliatorPaymentConfirmation($affiliator, $program, $autoApprove);

        // Generate receipt number
        $receiptNumber = 'RCP-' . strtoupper($affiliator->username) . '-' . now()->format('Ymd');

        // Convert amount to words (Indonesian)
        $amountInWords = $this->numberToWords($program->registration_fee);

        // Redirect ke halaman kwitansi
        return view('affiliate.receipt', compact('affiliator', 'program', 'receiptNumber', 'amountInWords'));
    }

    /**
     * Kirim konfirmasi pembayaran ke affiliator
     */
    private function sendAffiliatorPaymentConfirmation($affiliator, $program, $autoApprove)
    {
        $message = "*SELAMAT DATANG DI HM TOUR!* 🎉\n\n";
        $message .= "Halo *{$affiliator->full_name}*,\n\n";
        
        if ($autoApprove) {
            $message .= "✅ Pendaftaran Anda sebagai *{$program->name}* telah *BERHASIL!*\n\n";
            $message .= "📋 *Informasi Akun:*\n";
            $message .= "Username: `{$affiliator->username}`\n";
            $message .= "Link Referral: {$affiliator->referral_link}\n\n";
            $message .= "🔐 Login di: " . route('affiliate.login') . "\n\n";
            $message .= "💵 *Komisi Anda:*\n";
            $message .= "• PPC (Per Klik): Rp " . number_format($affiliator->ppc_commission, 0, ',', '.') . "\n";
            $message .= "• Minimal (Per Penjualan): Rp " . number_format($affiliator->min_sale_commission, 0, ',', '.') . "\n\n";
            $message .= "Mulai bagikan link referral Anda dan raih penghasilan! 💰\n\n";
        } else {
            $message .= "✅ Pembayaran Anda telah *DITERIMA!*\n\n";
            $message .= "📋 *Program:* {$program->name}\n";
            $message .= "💰 *Biaya:* {$program->formatted_fee}\n\n";
            $message .= "⏳ Pembayaran Anda sedang dalam proses verifikasi.\n";
            $message .= "Kami akan mengirimkan konfirmasi dalam *1x24 jam*.\n\n";
            $message .= "Setelah diverifikasi, Anda akan menerima:\n";
            $message .= "• Username & Password\n";
            $message .= "• Link Referral\n";
            $message .= "• Akses Dashboard Mitra\n\n";
        }
        
        $message .= "Terima kasih telah bergabung! 🙏\n\n";
        $message .= "_HM Tour - Your Trusted Travel Partner_";
        
        $this->sendWhatsApp($affiliator->phone_number, $message);
    }

    /**
     * Convert number to Indonesian words
     */
    private function numberToWords($number)
    {
        if ($number == 0) {
            return 'Nol Rupiah';
        }

        $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        $teens = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        
        $result = '';
        
        // Milyar
        if ($number >= 1000000000) {
            $billions = floor($number / 1000000000);
            $result .= ($billions == 1 ? 'Satu' : $this->numberToWords($billions)) . ' Milyar ';
            $number %= 1000000000;
        }
        
        // Juta
        if ($number >= 1000000) {
            $millions = floor($number / 1000000);
            $result .= ($millions == 1 ? 'Satu' : $this->numberToWords($millions)) . ' Juta ';
            $number %= 1000000;
        }
        
        // Ribu
        if ($number >= 1000) {
            $thousands = floor($number / 1000);
            $result .= ($thousands == 1 ? 'Seribu' : $this->numberToWords($thousands) . ' Ribu') . ' ';
            $number %= 1000;
        }
        
        // Ratus
        if ($number >= 100) {
            $hundreds = floor($number / 100);
            $result .= ($hundreds == 1 ? 'Seratus' : $words[$hundreds] . ' Ratus') . ' ';
            $number %= 100;
        }
        
        // Puluhan dan satuan
        if ($number >= 20) {
            $tens = floor($number / 10);
            $result .= $words[$tens] . ' Puluh ';
            $number %= 10;
        } elseif ($number >= 10) {
            $result .= $teens[$number - 10] . ' ';
            $number = 0;
        }
        
        if ($number > 0) {
            $result .= $words[$number] . ' ';
        }
        
        return trim($result) . ' Rupiah';
    }

    /**
     * Compress dan upload gambar menggunakan GD Library
     */
    private function compressAndUploadImage($file, $folder)
    {
        // Get file extension
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        
        // Create image resource based on mime type
        if (strpos($mimeType, 'jpeg') !== false || strpos($mimeType, 'jpg') !== false) {
            $image = imagecreatefromjpeg($file->getRealPath());
        } elseif (strpos($mimeType, 'png') !== false) {
            $image = imagecreatefrompng($file->getRealPath());
        } elseif (strpos($mimeType, 'gif') !== false) {
            $image = imagecreatefromgif($file->getRealPath());
        } else {
            // Fallback: just store the file without compression
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $path = $file->storeAs($folder, $filename, 'public');
            return $path;
        }
        
        if (!$image) {
            throw new \Exception('Failed to create image resource');
        }
        
        // Get original dimensions
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);
        
        // Calculate new dimensions if width > 1200
        if ($originalWidth > 1200) {
            $newWidth = 1200;
            $newHeight = (int) ($originalHeight * ($newWidth / $originalWidth));
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }
        
        // Create new image with new dimensions
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if (strpos($mimeType, 'png') !== false) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        
        // Resize image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Generate filename and path
        $filename = time() . '_' . uniqid() . '.jpg';
        $fullPath = storage_path('app/public/' . $folder);
        
        // Create directory if not exists
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        
        $filePath = $fullPath . '/' . $filename;
        
        // Save image with compression (quality 80)
        imagejpeg($newImage, $filePath, 80);
        
        // Free memory
        imagedestroy($image);
        imagedestroy($newImage);
        
        return $folder . '/' . $filename;
    }



    /**
     * Kirim WhatsApp via Fonnte API
     */
    private function sendWhatsApp($phone, $message)
    {
        try {
            $token = env('FONNTE_TOKEN');
            
            if (!$token) {
                \Log::error('Fonnte token not configured in .env');
                return false;
            }

            // Format nomor telepon (pastikan format internasional tanpa +)
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            } elseif (substr($phone, 0, 2) !== '62') {
                $phone = '62' . $phone;
            }

            // Fonnte API endpoint
            $url = 'https://api.fonnte.com/send';

            // Prepare data sebagai JSON (bukan form-data)
            $data = [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60, // Increase timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($data), // Use http_build_query for form-data
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $token,
                    'Content-Type: application/x-www-form-urlencoded'
                ],
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);
            
            // Log request untuk debugging
            \Log::info('WhatsApp API Request', [
                'phone' => $phone,
                'message_length' => strlen($message),
                'url' => $url
            ]);
            
            if ($curlError) {
                \Log::error('WhatsApp send failed (cURL error)', [
                    'phone' => $phone,
                    'error' => $curlError,
                    'http_code' => $httpCode
                ]);
                return false;
            }

            $result = json_decode($response, true);
            
            // Fonnte success response check
            if ($httpCode === 200) {
                if (isset($result['status']) && $result['status'] === true) {
                    \Log::info('WhatsApp sent successfully', [
                        'phone' => $phone,
                        'response' => $result
                    ]);
                    return true;
                } elseif (isset($result['status']) && $result['status'] === 'success') {
                    // Alternative success format
                    \Log::info('WhatsApp sent successfully (alt format)', [
                        'phone' => $phone,
                        'response' => $result
                    ]);
                    return true;
                } else {
                    \Log::warning('WhatsApp sent but status unclear', [
                        'phone' => $phone,
                        'http_code' => $httpCode,
                        'response' => $result
                    ]);
                    // Return true anyway if 200 OK
                    return true;
                }
            } else {
                \Log::error('WhatsApp send failed', [
                    'phone' => $phone,
                    'http_code' => $httpCode,
                    'response' => $result,
                    'raw_response' => $response
                ]);
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('WhatsApp send exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Dashboard affiliator
     */
    public function dashboard()
    {
        $username = session('affiliate_username');
        
        if (!$username) {
            return redirect()->route('affiliate.login');
        }

        $affiliator = Affiliator::where('username', $username)->with('partnershipProgram')->firstOrFail();

        if ($affiliator->status !== 'active') {
            return view('affiliate.pending', compact('affiliator'));
        }

        // Statistik
        $stats = [
            'total_clicks' => $affiliator->total_clicks,
            'total_sales' => $affiliator->total_sales,
            'conversion_rate' => $affiliator->conversion_rate,
            'total_earnings' => $affiliator->total_earnings,
            'available_balance' => $affiliator->available_balance,
            'pending_balance' => $affiliator->pending_balance,
        ];

        // Referral terbaru
        $recentReferrals = $affiliator->referrals()
            ->with(['package', 'booking'])
            ->latest()
            ->take(10)
            ->get();

        // Klik terbaru
        $recentClicks = $affiliator->clicks()
            ->with('package')
            ->latest('clicked_at')
            ->take(10)
            ->get();

        // Payout history
        $payouts = $affiliator->payouts()
            ->latest()
            ->take(5)
            ->get();

        return view('affiliate.dashboard', compact(
            'affiliator',
            'stats',
            'recentReferrals',
            'recentClicks',
            'payouts'
        ));
    }

    /**
     * Login affiliator
     */
    public function login()
    {
        return view('affiliate.login');
    }

    public function processLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $affiliator = Affiliator::where('username', $request->username)->first();

        if (!$affiliator || !Hash::check($request->password, $affiliator->password)) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->withInput();
        }

        session(['affiliate_username' => $affiliator->username]);

        return redirect()->route('affiliate.dashboard');
    }

    /**
     * Logout affiliator
     */
    public function logout()
    {
        session()->forget('affiliate_username');
        return redirect()->route('affiliate.login');
    }

    /**
     * Request payout
     */
    public function requestPayout(Request $request)
    {
        $username = session('affiliate_username');
        $affiliator = Affiliator::where('username', $username)->firstOrFail();

        $minimumPayout = AffiliateSetting::getValue('minimum_payout', 100000);

        if ($affiliator->available_balance < $minimumPayout) {
            return back()->withErrors([
                'balance' => 'Saldo minimum untuk penarikan adalah Rp ' . number_format($minimumPayout, 0, ',', '.')
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:' . $minimumPayout . '|max:' . $affiliator->available_balance,
            'payment_method' => 'required|in:bank_transfer,paypal,stripe',
        ]);

        $payout = AffiliatePayout::create([
            'affiliator_id' => $affiliator->id,
            'payout_reference' => AffiliatePayout::generateReference(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        // Kurangi available balance
        $affiliator->decrement('available_balance', $request->amount);

        return back()->with('success', 'Permintaan penarikan berhasil diajukan. Akan diproses dalam 1-3 hari kerja.');
    }

    /**
     * Halaman Referrals
     */
    public function referrals()
    {
        $username = session('affiliate_username');
        
        if (!$username) {
            return redirect()->route('affiliate.login');
        }

        $affiliator = Affiliator::where('username', $username)->firstOrFail();

        if ($affiliator->status !== 'active') {
            return redirect()->route('affiliate.dashboard');
        }

        $stats = [
            'pending' => $affiliator->referrals()->where('status', 'pending')->count(),
            'verified' => $affiliator->referrals()->where('status', 'verified')->count(),
            'rejected' => $affiliator->referrals()->where('status', 'rejected')->count(),
        ];

        $referrals = $affiliator->referrals()
            ->with(['package', 'booking.member'])
            ->latest()
            ->paginate(20);

        return view('affiliate.referrals', compact('affiliator', 'stats', 'referrals'));
    }

    /**
     * Halaman Payments
     */
    public function payments()
    {
        $username = session('affiliate_username');
        
        if (!$username) {
            return redirect()->route('affiliate.login');
        }

        $affiliator = Affiliator::where('username', $username)->firstOrFail();

        if ($affiliator->status !== 'active') {
            return redirect()->route('affiliate.dashboard');
        }

        $payouts = $affiliator->payouts()
            ->latest()
            ->paginate(20);

        $stats = [
            'total_paid' => $affiliator->payouts()->where('status', 'completed')->sum('amount'),
            'pending' => $affiliator->payouts()->where('status', 'pending')->sum('amount'),
            'failed' => $affiliator->payouts()->where('status', 'failed')->count(),
        ];

        return view('affiliate.payments', compact('affiliator', 'payouts', 'stats'));
    }

    /**
     * Halaman Wallet
     */
    public function wallet()
    {
        $username = session('affiliate_username');
        
        if (!$username) {
            return redirect()->route('affiliate.login');
        }

        $affiliator = Affiliator::where('username', $username)->firstOrFail();

        if ($affiliator->status !== 'active') {
            return redirect()->route('affiliate.dashboard');
        }

        $transactions = collect();
        
        // Gabungkan clicks dan referrals sebagai transactions
        $clicks = $affiliator->clicks()->latest('clicked_at')->get()->map(function($click) {
            return [
                'type' => 'click',
                'date' => $click->clicked_at,
                'amount' => $click->commission_amount,
                'description' => 'Komisi PPC',
                'status' => 'completed'
            ];
        });

        $referrals = $affiliator->referrals()->latest('order_date')->get()->map(function($ref) {
            return [
                'type' => 'referral',
                'date' => $ref->order_date,
                'amount' => $ref->commission_amount,
                'description' => 'Komisi Referral - ' . ($ref->package->package_name ?? 'N/A'),
                'status' => $ref->status
            ];
        });

        $payouts = $affiliator->payouts()->latest('requested_at')->get()->map(function($payout) {
            return [
                'type' => 'payout',
                'date' => $payout->requested_at,
                'amount' => -$payout->amount,
                'description' => 'Penarikan Dana',
                'status' => $payout->status
            ];
        });

        $transactions = $clicks->concat($referrals)->concat($payouts)->sortByDesc('date')->take(50);

        return view('affiliate.wallet', compact('affiliator', 'transactions'));
    }

    /**
     * Halaman Profile
     */
    public function profile()
    {
        $username = session('affiliate_username');
        
        if (!$username) {
            return redirect()->route('affiliate.login');
        }

        $affiliator = Affiliator::where('username', $username)->with('partnershipProgram')->firstOrFail();

        return view('affiliate.profile', compact('affiliator'));
    }

    /**
     * Update Profile
     */
    public function updateProfile(Request $request)
    {
        $username = session('affiliate_username');
        $affiliator = Affiliator::where('username', $username)->firstOrFail();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:affiliators,email,' . $affiliator->id,
            'phone_number' => 'required|string|unique:affiliators,phone_number,' . $affiliator->id,
            'photo' => 'nullable|image|max:2048',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['full_name', 'email', 'phone_number', 'bank_name', 'bank_account_number', 'bank_account_name']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->compressAndUploadImage($request->file('photo'), 'affiliator-photos');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $affiliator->update($data);

        return back()->with('success', 'Profile berhasil diupdate!');
    }

    /**
     * Halaman Marketing
     */
    public function marketing()
    {
        $username = session('affiliate_username');
        
        if (!$username) {
            return redirect()->route('affiliate.login');
        }

        $affiliator = Affiliator::where('username', $username)->firstOrFail();

        if ($affiliator->status !== 'active') {
            return redirect()->route('affiliate.dashboard');
        }

        // Get available packages
        $packages = \App\Models\TravelPackage::where('status', 'active')->get();

        return view('affiliate.marketing', compact('affiliator', 'packages'));
    }

    /**
     * Halaman Reports
     */
    public function reports()
    {
        $username = session('affiliate_username');
        
        if (!$username) {
            return redirect()->route('affiliate.login');
        }

        $affiliator = Affiliator::where('username', $username)->firstOrFail();

        if ($affiliator->status !== 'active') {
            return redirect()->route('affiliate.dashboard');
        }

        // Monthly stats
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'clicks' => $affiliator->clicks()->whereYear('clicked_at', $date->year)->whereMonth('clicked_at', $date->month)->count(),
                'sales' => $affiliator->referrals()->where('status', 'verified')->whereYear('order_date', $date->year)->whereMonth('order_date', $date->month)->count(),
                'earnings' => $affiliator->referrals()->where('status', 'verified')->whereYear('order_date', $date->year)->whereMonth('order_date', $date->month)->sum('commission_amount'),
            ];
        }

        return view('affiliate.reports', compact('affiliator', 'monthlyStats'));
    }

    /**
     * Halaman Leaderboard
     */
    public function leaderboard(Request $request)
    {
        $username = session('affiliate_username');
        
        if (!$username) {
            return redirect()->route('affiliate.login');
        }

        $currentAffiliator = Affiliator::where('username', $username)->firstOrFail();

        if ($currentAffiliator->status !== 'active') {
            return redirect()->route('affiliate.dashboard');
        }

        $period = $request->get('period', 'all');
        $type = $request->get('type', 'total');

        $query = Affiliator::where('status', 'active')
            ->withCount(['clicks', 'referrals as total_sales' => function($q) {
                $q->where('status', 'verified');
            }])
            ->withSum(['clicks as ppc_earnings' => function($q) {
                // Apply period filter if needed
            }], 'commission_amount')
            ->withSum(['referrals as referral_earnings' => function($q) {
                $q->where('status', 'verified');
            }], 'commission_amount');

        // Apply period filter
        if ($period == 'month') {
            $query->whereHas('clicks', function($q) {
                $q->whereMonth('clicked_at', now()->month)->whereYear('clicked_at', now()->year);
            })->orWhereHas('referrals', function($q) {
                $q->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year);
            });
        } elseif ($period == 'quarter') {
            $query->whereHas('clicks', function($q) {
                $q->whereBetween('clicked_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
            })->orWhereHas('referrals', function($q) {
                $q->whereBetween('order_date', [now()->startOfQuarter(), now()->endOfQuarter()]);
            });
        } elseif ($period == 'year') {
            $query->whereHas('clicks', function($q) {
                $q->whereYear('clicked_at', now()->year);
            })->orWhereHas('referrals', function($q) {
                $q->whereYear('order_date', now()->year);
            });
        }

        $leaderboard = $query->get()->map(function($aff) {
            $aff->total_earnings = ($aff->ppc_earnings ?? 0) + ($aff->referral_earnings ?? 0);
            return $aff;
        });

        // Sort by type
        if ($type == 'ppc') {
            $leaderboard = $leaderboard->sortByDesc('ppc_earnings')->values();
        } elseif ($type == 'referral') {
            $leaderboard = $leaderboard->sortByDesc('referral_earnings')->values();
        } else {
            $leaderboard = $leaderboard->sortByDesc('total_earnings')->values();
        }

        $topThree = $leaderboard->take(3);
        $yourPosition = $leaderboard->search(function($aff) use ($currentAffiliator) {
            return $aff->id == $currentAffiliator->id;
        }) + 1;

        return view('affiliate.leaderboard', compact('leaderboard', 'topThree', 'currentAffiliator', 'yourPosition'));
    }

    /**
     * Halaman jenjang mitra (lihat upline 1 level + semua downline)
     */
    public function hierarchy()
    {
        $username = session('affiliate_username');
        if (!$username) {
            return redirect()->route('affiliate.login');
        }

        $affiliator = Affiliator::with([
            'partnershipProgram',
            'uplineMaster',
            'uplineLeader',
            'uplinePartner',
            'downlineSellers.partnershipProgram',
            'downlinePartners.partnershipProgram',
            'downlineLeaders.partnershipProgram',
        ])->where('username', $username)->firstOrFail();

        // Tentukan 1 upline langsung
        $directUpline = null;
        $slug = $affiliator->partnershipProgram?->slug;
        if ($slug === 'hm-seller' && $affiliator->upline_partner_id) {
            $directUpline = $affiliator->uplinePartner;
        } elseif ($slug === 'hm-partner' && $affiliator->upline_leader_id) {
            $directUpline = $affiliator->uplineLeader;
        } elseif ($slug === 'hm-leader' && $affiliator->upline_master_id) {
            $directUpline = $affiliator->uplineMaster;
        }

        // Semua downline langsung
        $downlines = collect();
        if ($slug === 'hm-master') {
            $downlines = $affiliator->downlineLeaders;
        } elseif ($slug === 'hm-leader') {
            $downlines = $affiliator->downlinePartners;
        } elseif ($slug === 'hm-partner') {
            $downlines = $affiliator->downlineSellers;
        }

        // Fee distributions yang diterima dari downline
        $feeReceived = \App\Models\AffiliateFeeDistribution::where('to_affiliator_id', $affiliator->id)
            ->with(['fromAffiliator', 'referral'])
            ->latest()
            ->take(20)
            ->get();

        return view('affiliate.hierarchy', compact('affiliator', 'directUpline', 'downlines', 'feeReceived'));
    }

    /**
     * Request payout
     */
    public function requestPayout_OLD(Request $request)
    {
        $username = session('affiliate_username');
        $affiliator = Affiliator::where('username', $username)->firstOrFail();

        $minimumPayout = AffiliateSetting::getValue('minimum_payout', 100000);

        if ($affiliator->available_balance < $minimumPayout) {
            return back()->withErrors([
                'balance' => 'Saldo minimum untuk penarikan adalah Rp ' . number_format($minimumPayout, 0, ',', '.')
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:' . $minimumPayout . '|max:' . $affiliator->available_balance,
            'payment_method' => 'required|in:bank_transfer,paypal,stripe',
        ]);

        $payout = AffiliatePayout::create([
            'affiliator_id' => $affiliator->id,
            'payout_reference' => AffiliatePayout::generateReference(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        // Kurangi available balance
        $affiliator->decrement('available_balance', $request->amount);

        return back()->with('success', 'Permintaan penarikan berhasil diajukan. Akan diproses dalam 1-3 hari kerja.');
    }

    /**
     * Halaman Lupa Password
     */
    public function forgotPassword()
    {
        return view('affiliate.forgot-password');
    }

    /**
     * Kirim link reset password
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:affiliators,email',
            'send_via' => 'required|in:whatsapp,email',
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem.',
        ]);

        $affiliator = Affiliator::where('email', $request->email)->first();

        // Generate token
        $token = bin2hex(random_bytes(32));

        // Simpan token ke database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Generate reset link
        $resetLink = route('affiliate.reset-password', ['token' => $token, 'email' => $request->email]);

        if ($request->send_via === 'whatsapp') {
            // Kirim via WhatsApp
            $message = "*RESET PASSWORD - HM TOUR*\n\n";
            $message .= "Halo {$affiliator->full_name},\n\n";
            $message .= "Anda telah meminta reset password untuk akun mitra Anda.\n\n";
            $message .= "Klik link berikut untuk reset password:\n";
            $message .= $resetLink . "\n\n";
            $message .= "Link ini berlaku selama 1 jam.\n\n";
            $message .= "Jika Anda tidak meminta reset password, abaikan pesan ini.\n\n";
            $message .= "Terima kasih! 🙏";

            $sent = $this->sendWhatsApp($affiliator->phone_number, $message);

            if ($sent) {
                return back()->with('success', 'Link reset password telah dikirim ke WhatsApp Anda.');
            } else {
                return back()->withErrors(['error' => 'Gagal mengirim WhatsApp. Silakan coba lagi atau gunakan opsi Email.']);
            }
        } else {
            // Kirim via Email
            try {
                \Mail::send('emails.reset-password', [
                    'affiliator' => $affiliator,
                    'resetLink' => $resetLink
                ], function($mail) use ($affiliator) {
                    $mail->to($affiliator->email, $affiliator->full_name)
                         ->subject('Reset Password - HM Tour');
                });

                return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
            } catch (\Exception $e) {
                \Log::error('Email send failed', ['error' => $e->getMessage()]);
                return back()->withErrors(['error' => 'Gagal mengirim email. Silakan coba lagi atau gunakan opsi WhatsApp.']);
            }
        }
    }

    /**
     * Halaman Reset Password
     */
    public function resetPassword(Request $request)
    {
        $token = $request->token;
        $email = $request->email;

        // Validasi token
        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return redirect()->route('affiliate.login')
                ->withErrors(['error' => 'Link reset password tidak valid.']);
        }

        // Cek apakah token sudah kadaluarsa (1 jam)
        if (now()->diffInHours($resetRecord->created_at) > 1) {
            \DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('affiliate.login')
                ->withErrors(['error' => 'Link reset password sudah kadaluarsa.']);
        }

        return view('affiliate.reset-password', compact('token', 'email'));
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:affiliators,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Validasi token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['error' => 'Link reset password tidak valid.']);
        }

        // Cek apakah token sudah kadaluarsa (1 jam)
        if (now()->diffInHours($resetRecord->created_at) > 1) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect()->route('affiliate.login')
                ->withErrors(['error' => 'Link reset password sudah kadaluarsa.']);
        }

        // Update password
        $affiliator = Affiliator::where('email', $request->email)->first();
        $affiliator->update([
            'password' => Hash::make($request->password),
        ]);

        // Hapus token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Kirim notifikasi via WhatsApp
        $message = "*PASSWORD BERHASIL DIRESET*\n\n";
        $message .= "Halo {$affiliator->full_name},\n\n";
        $message .= "Password Anda telah berhasil direset.\n\n";
        $message .= "Silakan login dengan password baru Anda di:\n";
        $message .= route('affiliate.login') . "\n\n";
        $message .= "Jika Anda tidak melakukan perubahan ini, segera hubungi kami.\n\n";
        $message .= "Terima kasih! 🙏";

        $this->sendWhatsApp($affiliator->phone_number, $message);

        // Kirim notifikasi via Email juga
        try {
            \Mail::send('emails.password-changed', [
                'affiliator' => $affiliator
            ], function($mail) use ($affiliator) {
                $mail->to($affiliator->email, $affiliator->full_name)
                     ->subject('Password Berhasil Direset - HM Tour');
            });
        } catch (\Exception $e) {
            \Log::error('Email notification failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('affiliate.login')
            ->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    }
}
