<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Kategori;
use OnePointHub\LaravelOcr\Facades\Ocr;
use Log;
use Illuminate\Support\Str;

class JemaahController extends Controller
{
    public function show($id)
    {
        $member = Member::with(['jemaahData' => function($query) {
            $query->latest()->first(); // Get the most recent record
        }])->findOrFail($id);
        $categories = Kategori::all();

        return view('jemaah.show', compact('member', 'categories'));
    }

    public function processKtp(Request $request)
    {
        try {
            // 1. Upload gambar
            $image = $request->file('ktp_image');
            $path = $image->store('ktp_uploads');
            $fullPath = storage_path('app/'.$path);

            // 2. Preprocessing
            $processedPath = $this->preprocessImage($fullPath);

            // 3. OCR
            $text = $this->runTesseract($processedPath);

            // 4. Parsing
            $data = $this->enhancedKtpParsing($text);
            
            // 5. Validasi
            $this->validateKtpData($data);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            // Fallback ke Google Vision jika tersedia
            if (config('services.google_cloud.key')) {
                try {
                    $data = $this->googleVisionOcr($fullPath);
                    return response()->json([
                        'success' => true,
                        'data' => $data,
                        'note' => 'Used Google Cloud Vision'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Google Vision failed: '.$e->getMessage());
                }
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'debug' => env('APP_DEBUG') ? [
                    'ocr_text' => $text ?? null,
                    'error' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    private function preprocessImage($imagePath) 
    {
        $outputPath = storage_path('app/ktp_processed_'.time().'.png');
        
        $command = "/usr/local/bin/magick " . escapeshellarg($imagePath) . " " .
                "-colorspace gray " .       // Grayscale
                "-negate " .                // Invert colors (untuk KTP dark background)
                "-lat 20x20+5% " .         // Local Adaptive Thresholding
                "-sharpen 0x2 " .          // Sharpening
                "-despeckle " .            // Noise reduction
                "-contrast-stretch 1% " .  // Contrast enhancement
                "-morphology close square:2 " . // Menghubungkan karakter terputus
                escapeshellarg($outputPath);

        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception("Image processing failed: ".implode("\n", $output));
        }
        
        return $outputPath;
    }

    private function runTesseract($imagePath)
    {
        $config = [
            'tessedit_pageseg_mode' => '6',  // PSM_SINGLE_BLOCK
            'tessedit_ocr_engine_mode' => '2', // OEM_LSTM_ONLY
            'preserve_interword_spaces' => '1',
            'tessedit_char_whitelist' => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ ',
            'user_defined_dpi' => '300'      // Untuk gambar high-res
        ];

        $configStr = '';
        foreach ($config as $k => $v) {
            $configStr .= "-c $k=$v ";
        }

        $command = "tesseract ".escapeshellarg($imagePath)." stdout -l ind+eng $configStr 2>&1";
        $text = shell_exec($command);

        return $this->cleanOcrText($text);
    }

    private function cleanOcrText($text)
    {
        // Normalisasi teks
        $text = preg_replace('/[^\w\s,-]/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        return $text;
    }

    private function parseKtpText($text)
    {
        $result = [
            'nik' => null,
            'nama' => null,
            'ttl' => null,
            'alamat' => null
        ];

        // Normalisasi teks
        $text = strtoupper($text);
        $lines = explode("\n", $text);

        // Pola spesifik untuk NIK 16 digit
        foreach ($lines as $line) {
            if (preg_match('/NIK.*?(\d{16})/', $line, $matches)) {
                $result['nik'] = $matches[1];
                break;
            }
        }

        // Ekstraksi nama (setelah baris PROVINSI)
        $foundProvinsi = false;
        foreach ($lines as $line) {
            if (str_contains($line, 'PROVINSI')) {
                $foundProvinsi = true;
                continue;
            }
            
            if ($foundProvinsi && preg_match('/^[A-Z\s]{5,}$/', $line)) {
                $result['nama'] = trim($line);
                break;
            }
        }

        // Ekstraksi TTL (format: TEMPAT, DD-MM-YYYY)
        foreach ($lines as $line) {
            if (preg_match('/(?:TEMPAT|TMP\.?)\s*\/?\s*TGL\s*\.?\s*LAHIR\s*:\s*([^,]+),\s*(\d{2}-\d{2}-\d{4})/i', $line, $matches)) {
                $result['ttl'] = [
                    'tempat' => trim($matches[1]),
                    'tanggal' => $matches[2]
                ];
                break;
            }
        }

        return $result;
    }

    private function enhancedKtpParsing($text)
    {
        $result = $this->parseKtpText($text);
        
        // Jika NIK tidak ditemukan, cari 16 digit berurutan
        if (empty($result['nik']) && preg_match('/\d{16}/', $text, $matches)) {
            $result['nik'] = $matches[0];
        }

        // Jika nama tidak ditemukan, cari kata terpanjang dalam teks
        if (empty($result['nama'])) {
            $words = preg_split('/\s+/', $text);
            usort($words, function($a, $b) {
                return strlen($b) - strlen($a);
            });
            $result['nama'] = $words[0] ?? null;
        }

        return $result;
    }

    private function tryAlternativeParsing($text, $imagePath)
    {
        // 1. Coba dengan PSM berbeda (Segmentasi Halaman)
        $alternateText = shell_exec("/usr/local/bin/tesseract ".escapeshellarg($imagePath)." stdout -l ind --psm 4 2>&1");
        
        // 2. Coba ekstraksi per region (jika layout konsisten)
        $regionText = $this->extractByRegions($imagePath);
        
        // Gabungkan semua teks untuk parsing
        $combinedText = $text . "\n---\n" . $alternateText . "\n---\n" . $regionText;
        
        return $this->parseKtpText($combinedText);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:member,id_member',
            'items' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            $transaction = new Transaction();
            $transaction->member_id = $request->member_id;
            $transaction->status = 'pending';
            $transaction->total = collect($request->items)->sum(function($item) {
                return $item['price'] * $item['qty'];
            });
            $transaction->save();

            foreach ($request->items as $item) {
                $transactionItem = new TransactionItem();
                $transactionItem->transaction_id = $transaction->id;
                $transactionItem->product_id = $item['id'];
                $transactionItem->variant_id = $item['variantId'] ?? null;
                $transactionItem->quantity = $item['qty'];
                $transactionItem->price = $item['price'];
                $transactionItem->save();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateIdentitas(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'status_pernikahan' => 'required|string',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'no_ktp' => 'required|string|max:20',
            'no_telepon' => 'required|string|max:20',
            'ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'passport' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'visa' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $member = Member::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update basic member info
            $member->update([
                'nama' => $request->nama_lengkap,
                'telepon' => $request->no_telepon,
            ]);

            // Get or create jemaah data
            $jemaahData = $member->jemaahData()->firstOrNew([]);

            // Update jemaah data
            $jemaahData->fill([
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'status_pernikahan' => $request->status_pernikahan,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'no_ktp' => $request->no_ktp,
                'no_telepon' => $request->no_telepon,
            ]);

            // Handle file uploads
            if ($request->hasFile('ktp')) {
                $path = $request->file('ktp')->store('jemaah/ktp', 'public');
                $jemaahData->ktp_path = $path;
            }

            if ($request->hasFile('passport')) {
                $path = $request->file('passport')->store('jemaah/passport', 'public');
                $jemaahData->passport_path = $path;
            }

            if ($request->hasFile('visa')) {
                $path = $request->file('visa')->store('jemaah/visa', 'public');
                $jemaahData->visa_path = $path;
            }

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('jemaah/photo', 'public');
                $jemaahData->photo_path = $path;
            }

            $jemaahData->save();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateKeluarga(Request $request, $id)
    {
        $request->validate([
            'keluarga_data' => 'required|json',
        ]);

        $member = Member::findOrFail($id);
        $keluargaData = json_decode($request->keluarga_data, true);

        // Process and save keluarga data here
        // This will depend on your database structure

        return response()->json(['success' => true]);
    }
}