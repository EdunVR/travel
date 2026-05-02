<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi - {{ $payment->receipt_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 5px;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table td {
            font-size: 11px;
            padding: 2px;
            vertical-align: top;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        /* Header */
        .header {
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .company-logo {
            width: 120px;
            height: auto;
            display: block;
        }
        
        .logo-box {
            border: 2px solid #000;
            padding: 5px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
            width: 120px;
        }
        
        .company-info {
            overflow: hidden;
        }
        
        /* Body */
        .body {
            margin: 10px 0;
        }
        
        .info-section {
            margin: 8px 0;
            padding: 8px;
            background: #f8f9fa;
            border: 1px solid #ddd;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
            color: #4A7C59;
            border-bottom: 2px solid #4A7C59;
            padding-bottom: 3px;
        }
        
        /* Amount Box */
        .amount-box {
            border: 2px solid #4A7C59;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
            background: #f0f7f0;
        }
        
        .amount-label {
            font-size: 12px;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4A7C59;
        }
        
        .amount-value {
            font-size: 24px;
            font-weight: bold;
            color: #4A7C59;
        }
        
        .amount-words {
            font-style: italic;
            margin-top: 8px;
            font-size: 11px;
            color: #666;
        }
        
        /* Signature */
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-box {
            text-align: center;
            display: inline-block;
            position: relative;
        }
        
        .signature-image {
            height: 50px;
            width: auto;
        }
        
        .stamp-image {
            position: absolute;
            height: 60px;
            width: auto;
            opacity: 0.8;
            margin-left: -160px;
            margin-top: -10px;
        }
        
        /* Footer */
        .footer {
            margin-top: 10px;
            font-size: 10px;
            color: #666;
        }
        
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 11px;
                margin: 0;
                padding: 5px;
            }
            .signature-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @php
        // Get company settings using HasCompanySettings trait
        $companySettings = $payment->jamaahBooking->getCompanySettings();
        
        // Get bank accounts if available
        $bankAccounts = collect();
        if (class_exists('App\Models\BankAccount')) {
            $bankAccounts = \App\Models\BankAccount::where('is_active', true)->get();
        }
    @endphp

    <!-- Header / Kop -->
    <div class="header">
        <table>
            <tr>
                <!-- Kolom Kiri: Logo -->
                <td style="width: 20%; vertical-align: middle;">
                    @php
                        $logoPathRH = $companySettings->company_logo ?? null;
                        $logoAbsRH = null;
                        if ($logoPathRH) {
                            $logoPathRH = preg_replace('#^(https?://[^/]+)?/?(storage/)?#', '', $logoPathRH);
                            $logoPathRH = ltrim($logoPathRH, '/');
                            $logoAbsRH = storage_path('app/public/' . $logoPathRH);
                        }
                    @endphp
                    @if($logoAbsRH && file_exists($logoAbsRH))
                    <img src="{{ $logoAbsRH }}" 
                         alt="Logo" 
                         class="company-logo">
                    @else
                    <div class="logo-box">
                        {{ strtoupper(substr($companySettings->company_name ?? 'TRAVEL', 0, 8)) }}
                    </div>
                    @endif
                </td>
                <!-- Kolom Tengah: Informasi Perusahaan -->
                <td style="width: 55%; text-align: center; vertical-align: middle; padding: 0 8px;">
                    <div style="font-size: 15px; font-weight: bold; margin-bottom: 3px;">
                        {{ strtoupper($companySettings->company_name ?? 'PT. TRAVEL UMROH & HAJI') }}
                    </div>
                    @if($companySettings && $companySettings->company_address)
                    <div style="font-size: 9px; line-height: 1.4; margin-bottom: 2px; word-wrap: break-word;">
                        {!! $companySettings->formatted_address ?? $companySettings->company_address !!}
                    </div>
                    @endif
                    <div style="font-size: 9px;">
                        @if($companySettings && $companySettings->company_phone)
                            TELP/WA: {{ $companySettings->formatted_phone ?? $companySettings->company_phone }}
                        @endif
                        @if($companySettings && $companySettings->company_email)
                            | {{ $companySettings->company_email }}
                        @endif
                    </div>
                </td>
                <!-- Kolom Kanan: QR Code -->
                <td style="width: 25%; text-align: right; vertical-align: middle;">
                    @php
                        $receiptToken = hash('sha256', $payment->id . $payment->id_jamaah_booking . config('app.key'));
                        $receiptUrl = url('doc/receipt/' . $payment->id . '/' . $receiptToken);
                    @endphp
                    @if(class_exists('Milon\Barcode\Facades\DNS2DFacade'))
                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($receiptUrl, 'QRCODE', 3, 3) }}"
                         alt="QR Kwitansi" style="width: 70px; height: 70px;">
                    <div style="font-size: 8px; color: #666; margin-top: 2px; text-align: center;">Scan untuk kwitansi digital</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Judul Kwitansi (di luar kop) -->
    <div style="text-align: center; padding: 6px 0; margin-bottom: 8px;">
        <span style="font-size: 13px; font-weight: bold; letter-spacing: 1px;">KWITANSI PEMBAYARAN</span>
    </div>

    <!-- Body: Informasi Kwitansi -->
    <div class="body">
        <!-- Receipt Number -->
        <div class="text-center" style="margin-bottom: 15px;">
            <strong style="font-size: 13px;">No. {{ $payment->receipt_number }}</strong>
        </div>

        <!-- Informasi Pembayaran -->
        <div class="info-section">
            <div class="section-title">INFORMASI PEMBAYARAN</div>
            <table>
                <tr>
                    <td style="width: 25%;"><strong>Sudah Terima Dari</strong></td>
                    <td style="width: 25%;">: {{ $payment->jamaahBooking->jamaah->nama }}</td>
                    <td style="width: 25%;"><strong>Kode Booking</strong></td>
                    <td style="width: 25%;">: {{ $payment->jamaahBooking->booking_code }}</td>
                </tr>
                <tr>
                    <td><strong>Untuk Pembayaran</strong></td>
                    <td colspan="3">: Paket {{ $payment->jamaahBooking->travelPackage->package_name }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Pembayaran</strong></td>
                    <td>: {{ $payment->payment_date->format('d F Y') }}</td>
                    <td><strong>Metode Pembayaran</strong></td>
                    <td>: {{ $payment->formatted_payment_method }}</td>
                </tr>
                @if($payment->reference_number)
                <tr>
                    <td><strong>Nomor Referensi</strong></td>
                    <td colspan="3">: {{ $payment->reference_number }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Amount Box -->
        <div class="amount-box">
            <div class="amount-label">JUMLAH PEMBAYARAN</div>
            <div class="amount-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
            <div class="amount-words">
                {{ ucwords(terbilang($payment->amount)) }} Rupiah
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="info-section">
            <div class="section-title">RINGKASAN PEMBAYARAN</div>
            @php
                $receiptGrandTotal = $payment->jamaahBooking->getGrandTotal();
                $receiptRemaining = $payment->jamaahBooking->getRemainingBalance();
            @endphp
            <table>
                <tr>
                    <td style="width: 50%;"><strong>Total Harga Paket</strong></td>
                    <td style="width: 50%;">: Rp {{ number_format($receiptGrandTotal, 0, ',', '.') }}</td>
                </tr>
                <tr style="background: #d4edda;">
                    <td><strong>Total Sudah Dibayar</strong></td>
                    <td style="color: #28a745;"><strong>: Rp {{ number_format($payment->jamaahBooking->paid_amount, 0, ',', '.') }}</strong></td>
                </tr>
                <tr style="background: #fff3cd;">
                    <td><strong>Sisa Pembayaran</strong></td>
                    <td style="color: #dc3545;"><strong>: Rp {{ number_format($receiptRemaining, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

        @if($payment->notes)
        <div style="margin-top: 8px; padding: 8px; background: #f8f9fa; border-left: 3px solid #4A7C59; font-size: 10px;">
            <strong>Keterangan:</strong><br>
            {{ $payment->notes }}
        </div>
        @endif
    </div>

    <!-- Footer dengan Tanda Tangan -->
    <div class="signature-section">
        <table>
            <tr>
                <!-- Kolom Kiri: Tanda Terima Jamaah -->
                <td style="width: 33%; text-align: center;">
                    <b>Yang Menerima</b><br><br><br><br>
                    ( {{ $payment->jamaahBooking->jamaah->nama }} )
                </td>
                <!-- Kolom Tengah: Pesan -->
                <td style="width: 34%; text-align: center; font-size: 10px; vertical-align: bottom;">
                    <b>Mohon simpan kwitansi ini sebagai bukti pembayaran yang sah</b>
                </td>
                <!-- Kolom Kanan: Hormat Kami dengan Tanda Tangan & Cap -->
                <td style="width: 33%; text-align: center;">
                    <b>Hormat Kami</b><br>
                    <div class="signature-box" style="position: relative; display: inline-block;">
                        @php
                            // Tanda tangan: dari recordedBy, atau fallback ke user pertama yang punya signature
                            $sigUserReceipt = ($payment->recordedBy && $payment->recordedBy->signature_path)
                                ? $payment->recordedBy
                                : \App\Models\User::whereNotNull('signature_path')->where('signature_path','!=','')->first();
                            // Cap/stamp logo
                            $stampPathR = $companySettings->company_logo ?? null;
                            $stampAbsR = null;
                            if ($stampPathR) {
                                $stampPathR = preg_replace('#^(https?://[^/]+)?/?(storage/)?#', '', $stampPathR);
                                $stampPathR = ltrim($stampPathR, '/');
                                $stampAbsR = storage_path('app/public/' . $stampPathR);
                            }
                        @endphp
                        @if($sigUserReceipt && $sigUserReceipt->signature_path && file_exists(public_path($sigUserReceipt->signature_path)))
                        <img src="{{ public_path($sigUserReceipt->signature_path) }}" 
                             alt="Tanda Tangan" 
                             class="signature-image">
                        @endif
                        
                        @if($stampAbsR && file_exists($stampAbsR))
                        <img src="{{ $stampAbsR }}" 
                             alt="Cap" 
                             class="stamp-image">
                        @endif
                    </div><br>
                    ( Muhammad Abdul Aziz, S.E.)
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p style="text-align: center; margin-top: 10px;">
            Dicetak pada: {{ now()->format('d F Y H:i:s') }} | Dokumen ini sah tanpa tanda tangan basah
        </p>
    </div>
</body>
</html>

{{-- Fungsi terbilang() sudah tersedia di app/Http/Helpers/helpers.php --}}
