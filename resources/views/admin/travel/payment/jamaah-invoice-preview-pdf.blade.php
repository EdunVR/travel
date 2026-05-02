<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Preview Invoice - {{ $invoice->no_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 10mm;
        }
        
        html, body {
            width: 100%;
            height: auto;
            overflow-x: hidden;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            padding: 5px;
            color: #333;
            background: white;
            min-height: 100vh;
        }
        
        .page-main, .page-additional {
            width: 100%;
            max-width: 200mm; /* Reduced from 210mm to prevent cutoff */
            margin: 0 auto;
            background: white;
            padding: 8px; /* Reduced padding */
            min-height: 297mm; /* A4 height */
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
        
        table.data td,
        table.data th {
            border: 1px solid #000;
            padding: 5px;
        }
        
        table.data {
            border-collapse: collapse;
            width: 100%;
            margin: 10px 0;
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
            width: 100px;
            height: auto;
            float: left;
            margin-right: 10px;
        }
        
        .logo-box {
            border: 2px solid #000;
            padding: 5px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
            width: 100px;
            float: left;
            margin-right: 10px;
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
        
        /* Total Section */
        .total-section {
            margin-top: 10px;
        }
        
        /* Bank Info */
        .bank-info {
            margin-top: 8px;
            padding: 8px;
            border: 1px solid #000;
            background: #f8f9fa;
            font-size: 10px;
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
        
        .page-break {
            page-break-before: always;
        }
        
        .preview-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(255, 0, 0, 0.1);
            font-weight: bold;
            z-index: 1000;
            pointer-events: none;
        }
        
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
            }
            
            body {
                font-family: Arial, sans-serif;
                font-size: 11px;
                margin: 0;
                padding: 5px;
            }
            
            .signature-section {
                page-break-inside: avoid;
            }
            
            .preview-watermark {
                display: none;
            }
        }
        
        /* Ensure content is visible in iframe */
        @media screen {
            body {
                overflow-y: auto;
                overflow-x: hidden;
            }
            
            .page-main {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="preview-watermark">PREVIEW</div>
    
    {{-- HALAMAN 1: INVOICE UTAMA --}}
    <div class="page-main">
        <!-- Header -->
        <!-- Header / Kop -->
        <div class="header">
            <table>
                <tr>
                    <!-- Kolom Kiri: Logo/Logo Box -->
                    <td style="width: 20%; vertical-align: middle;">
                        @if(isset($companySettings['company_logo']) && $companySettings['company_logo'])
                        <img src="{{ storage_path('app/public/' . $companySettings['company_logo']) }}" 
                             alt="Logo" 
                             style="width: 100px; height: auto; display: block;">
                        @else
                        <div class="logo-box">
                            {{ strtoupper(substr($companySettings['company_name'] ?? 'TRAVEL', 0, 8)) }}
                        </div>
                        @endif
                    </td>
                    <!-- Kolom Tengah: Informasi Perusahaan -->
                    <td style="width: 55%; text-align: center; vertical-align: middle; padding: 0 8px;">
                        <div style="font-size: 15px; font-weight: bold; margin-bottom: 3px;">
                            {{ strtoupper($companySettings['company_name'] ?? 'PT. TRAVEL UMROH & HAJI') }}
                        </div>
                        @if($companySettings['company_address'])
                        <div style="font-size: 9px; line-height: 1.4; margin-bottom: 2px; word-wrap: break-word;">
                            {!! $companySettings['formatted_address'] ?? $companySettings['company_address'] !!}
                        </div>
                        @endif
                        <div style="font-size: 9px;">
                            @if($companySettings['company_phone'])
                                TELP/WA: {{ $companySettings['formatted_phone'] ?? $companySettings['company_phone'] }}
                            @endif
                            @if($companySettings['company_email'])
                                | {{ $companySettings['company_email'] }}
                            @endif
                        </div>
                    </td>
                    <!-- Kolom Kanan: QR Code -->
                    <td style="width: 25%; text-align: right; vertical-align: middle;">
                        @php
                            $invoiceToken = hash('sha256', $booking->id . $booking->id_invoice . config('app.key'));
                            $invoiceUrl = url('doc/invoice/' . $booking->id . '/' . $invoiceToken);
                        @endphp
                        @if(class_exists('Milon\Barcode\Facades\DNS2DFacade'))
                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($invoiceUrl, 'QRCODE', 3, 3) }}"
                             alt="QR Invoice" style="width: 70px; height: 70px;">
                        <div style="font-size: 8px; color: #666; margin-top: 2px; text-align: center;">Scan untuk invoice digital</div>
                        @else
                        <span style="font-size: 9px; color: #999;">PREVIEW</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Judul Invoice (di luar kop, di bawah garis tebal) -->
        <div style="text-align: center; padding: 6px 0; margin-bottom: 8px;">
            <span style="font-size: 13px; font-weight: bold; letter-spacing: 1px;">INVOICE BOOKING JAMAAH</span>
        </div>

        <!-- Body: Informasi Invoice dan Jamaah -->
        <div class="body">
            <table>
                <tr>
                    <!-- Kolom Kiri: Info Invoice -->
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td style="width: 120px;"><strong>No. Invoice</strong></td>
                                <td>: {{ $invoice->no_invoice }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Invoice</strong></td>
                                <td>: {{ $invoice->tanggal->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jatuh Tempo</strong></td>
                                <td>: {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kode Booking</strong></td>
                                <td>: {{ $booking->booking_code }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status Invoice</strong></td>
                                <td>: {{ strtoupper(str_replace('_', ' ', $invoice->status)) }}</td>
                            </tr>
                        </table>
                    </td>
                    <!-- Kolom Kanan: Info Jamaah -->
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td style="width: 120px;"><strong>Nama Jamaah</strong></td>
                                <td>: {{ $booking->jamaah->nama }}</td>
                            </tr>
                            <tr>
                                <td><strong>No. KTP</strong></td>
                                <td>: {{ $booking->jamaah->ktp_nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>No. Passport</strong></td>
                                <td>: {{ $booking->jamaah->passport_nomor ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Telepon/WA</strong></td>
                                <td>: {{ $booking->jamaah->telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kota Asal</strong></td>
                                <td>: {{ $booking->jamaah->kota ?? $booking->jamaah->alamat ?? '-' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Informasi Tambahan Jamaah -->
        <div class="info-section">
            <div class="section-title">INFORMASI TAMBAHAN</div>
            <table>
                <tr>
                    <td style="width: 33%;">
                        <strong>Usia:</strong>
                        @php
                            $age = null;
                            $ageCategory = 'Dewasa';
                            if ($booking->jamaah->ktp_tanggal_lahir) {
                                $age = \Carbon\Carbon::parse($booking->jamaah->ktp_tanggal_lahir)->age;
                            } elseif ($booking->jamaah->passport_tanggal_lahir) {
                                $age = \Carbon\Carbon::parse($booking->jamaah->passport_tanggal_lahir)->age;
                            }
                            if ($age !== null) {
                                if ($age < 2) { $ageCategory = 'Bayi'; }
                                elseif ($age < 12) { $ageCategory = 'Anak-anak'; }
                                echo $age . ' tahun (' . $ageCategory . ')';
                            } else { echo '-'; }
                        @endphp
                    </td>
                    <td style="width: 33%;">
                        <strong>Seller/Closing:</strong>
                        {{ $booking->seller_name ?? ($booking->closedBy->name ?? '-') }}
                    </td>
                    <td style="width: 34%;">
                        <strong>Sumber Closing:</strong>
                        {{ $booking->closing_source ? ucfirst(str_replace('_', ' ', $booking->closing_source)) : '-' }}
                    </td>
                </tr>
                @php
                    $familyMembersPreview = $booking->family_members_booking;
                    if (is_string($familyMembersPreview)) {
                        $familyMembersPreview = json_decode($familyMembersPreview, true);
                    }
                    if (!is_array($familyMembersPreview)) {
                        $familyMembersPreview = [];
                    }
                @endphp
                @if(!empty($familyMembersPreview) && is_array($familyMembersPreview))
                <tr>
                    <td colspan="4" style="padding-top: 5px; border-top: 1px solid #ddd;">
                        <strong>Anggota Keluarga:</strong>
                        {{ implode(', ', array_map(fn($fm) => ($fm['nama'] ?? '') . ($fm['hubungan'] ? ' (' . $fm['hubungan'] . ')' : ''), $familyMembersPreview)) }}
                    </td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Detail Paket -->
        <div class="info-section">
            <div class="section-title">DETAIL PAKET PERJALANAN</div>
            <table>
                <tr>
                    <td colspan="4" style="font-size: 12px; font-weight: bold; padding-bottom: 5px;">
                        {{ $booking->travelPackage->package_name }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%;">
                        <strong>Jenis Paket:</strong> {{ ucfirst($booking->travelPackage->package_type) }}
                    </td>
                    <td style="width: 25%;">
                        <strong>Durasi:</strong> {{ $booking->travelPackage->duration_days }} Hari
                    </td>
                    <td style="width: 25%;">
                        <strong>Keberangkatan:</strong> {{ $booking->travelPackage->departure_date->format('d/m/Y') }}
                    </td>
                    <td style="width: 25%;">
                        <strong>Kepulangan:</strong> {{ $booking->travelPackage->return_date->format('d/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Maskapai Keberangkatan:</strong>
                        {{ $booking->travelPackage->flightDeparture->airline_name ?? $booking->travelPackage->airline ?? '-' }}
                        @if($booking->travelPackage->flightDeparture)
                            ({{ $booking->travelPackage->flightDeparture->flight_number }})
                            — {{ $booking->travelPackage->flightDeparture->departure_airport ?? '' }} - {{ $booking->travelPackage->flightDeparture->arrival_airport ?? '' }}
                        @endif
                    </td>
                    <td colspan="2">
                        <strong>Maskapai Kepulangan:</strong>
                        {{ $booking->travelPackage->flightReturn->airline_name ?? $booking->travelPackage->airline ?? '-' }}
                        @if($booking->travelPackage->flightReturn)
                            ({{ $booking->travelPackage->flightReturn->flight_number }})
                            — {{ $booking->travelPackage->flightReturn->departure_airport ?? '' }} - {{ $booking->travelPackage->flightReturn->arrival_airport ?? '' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Hotel Mekkah:</strong>
                        @php
                            $hotelMakkahPrev = $booking->hotelBookings ? $booking->hotelBookings->where('city_type', 'makkah')->first() : null;
                        @endphp
                        @if($hotelMakkahPrev)
                            {{ $hotelMakkahPrev->hotel->hotel_name ?? '-' }}
                            @if($hotelMakkahPrev->room_type) ({{ ucfirst($hotelMakkahPrev->room_type) }})@endif
                            @if(!$hotelMakkahPrev->is_charged) <small style="color:#28a745;">Include Paket</small>@else <small style="color:#dc3545;">Charge</small>@endif
                        @elseif($booking->travelPackage->hotelMakkah)
                            {{ $booking->travelPackage->hotelMakkah->hotel_name }}
                            @if($booking->travelPackage->hotelMakkah->star_rating) ({{ $booking->travelPackage->hotelMakkah->star_rating }} bintang)@endif
                            <small style="color:#28a745;">Include Paket</small>
                        @else
                            -
                        @endif
                    </td>
                    <td colspan="2">
                        <strong>Hotel Madinah:</strong>
                        @php
                            $hotelMadinahPrev = $booking->hotelBookings ? $booking->hotelBookings->where('city_type', 'madinah')->first() : null;
                        @endphp
                        @if($hotelMadinahPrev)
                            {{ $hotelMadinahPrev->hotel->hotel_name ?? '-' }}
                            @if($hotelMadinahPrev->room_type) ({{ ucfirst($hotelMadinahPrev->room_type) }})@endif
                            @if(!$hotelMadinahPrev->is_charged) <small style="color:#28a745;">Include Paket</small>@else <small style="color:#dc3545;">Charge</small>@endif
                        @elseif($booking->travelPackage->hotelMadinah)
                            {{ $booking->travelPackage->hotelMadinah->hotel_name }}
                            @if($booking->travelPackage->hotelMadinah->star_rating) ({{ $booking->travelPackage->hotelMadinah->star_rating }} bintang)@endif
                            <small style="color:#28a745;">Include Paket</small>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabel Detail Item -->
        @php
            $itemNo = 1;
            $selectedRoomType2 = $booking->price_variant ?? $booking->room_type ?? 'double';
            $selectedPkgName2 = $booking->price_package_name ?? null;
            $pricePackages2 = $booking->travelPackage->price_packages ?? [];
            if (is_string($pricePackages2)) $pricePackages2 = json_decode($pricePackages2, true);
            $unitPrice = 0;
            if (!empty($pricePackages2) && is_array($pricePackages2)) {
                $targetPkg2 = null;
                if ($selectedPkgName2) { foreach ($pricePackages2 as $pp) { if (strtolower($pp['name'] ?? '') === strtolower($selectedPkgName2)) { $targetPkg2 = $pp; break; } } }
                if (!$targetPkg2) $targetPkg2 = $pricePackages2[0] ?? null;
                if ($targetPkg2) {
                    foreach ($targetPkg2['variants'] ?? [] as $v) { if (strtolower($v['type'] ?? '') === strtolower($selectedRoomType2)) { $unitPrice = (float)($v['price'] ?? 0); break; } }
                    if ($unitPrice == 0) { foreach ($targetPkg2['variants'] ?? [] as $v) { if (strtolower($v['type'] ?? '') === 'double') { $unitPrice = (float)($v['price'] ?? 0); break; } } }
                }
            }
            if ($unitPrice == 0) $unitPrice = (float)($booking->total_price - ($booking->equipment_cost ?? 0) - ($booking->upgrade_cost ?? 0) + $booking->discount_amount);
            $familyMembers2 = $booking->family_members_booking;
            if (is_string($familyMembers2)) $familyMembers2 = json_decode($familyMembers2, true);
            if (!is_array($familyMembers2)) $familyMembers2 = [];
            $familyNormal = []; $familyDiscount = [];
            foreach ($familyMembers2 as $fm) {
                if (empty($fm['tanggal_lahir'])) { $familyNormal[] = array_merge($fm, ['price' => $unitPrice, 'kategori' => 'Dewasa', 'diskon' => '-']); }
                else {
                    $fmAge = \Carbon\Carbon::parse($fm['tanggal_lahir'])->age;
                    if ($fmAge < 2) { $familyDiscount[] = array_merge($fm, ['price' => 18000000, 'kategori' => 'Infant (0-2th)', 'diskon' => 'Flat Rp 18jt', 'age' => $fmAge]); }
                    elseif ($fmAge <= 8) { $familyDiscount[] = array_merge($fm, ['price' => $unitPrice * 0.85, 'kategori' => 'Anak (2-8th)', 'diskon' => 'Diskon 15%', 'age' => $fmAge]); }
                    else { $familyNormal[] = array_merge($fm, ['price' => $unitPrice, 'kategori' => 'Dewasa', 'diskon' => '-', 'age' => $fmAge]); }
                }
            }
            $mainPax = 1 + count($familyNormal);
            $mainSubtotal = $unitPrice * $mainPax;
            $familyDiscountTotal = array_sum(array_column($familyDiscount, 'price'));
            $chargedHotelsTotal = $booking->hotelBookings ? $booking->hotelBookings->where('is_charged', true)->sum('total_cost') : 0;
            $addonsTotal = $booking->addons ? $booking->addons->sum(fn($a) => $a->harga * $a->qty) : 0;
            $grandTotal = $mainSubtotal + $familyDiscountTotal + ($booking->equipment_cost ?? 0) + ($booking->upgrade_cost ?? 0) + $chargedHotelsTotal + $addonsTotal - $booking->discount_amount;
        @endphp
        <table class="data" width="100%">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Deskripsi</th>
                    <th width="10%" class="text-center">Pax</th>
                    <th width="20%" class="text-right">Harga Satuan</th>
                    <th width="20%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <!-- Main Package (jamaah + anggota dewasa) -->
                <tr>
                    <td class="text-center">{{ $itemNo++ }}</td>
                    <td>
                        <strong>Paket {{ $booking->travelPackage->package_name }}</strong><br>
                        <small>{{ ucfirst($booking->travelPackage->package_type) }} - {{ $booking->travelPackage->duration_days }} Hari</small><br>
                        @if($booking->price_package_name)
                        <small>Paket Harga: <strong>{{ $booking->price_package_name }}</strong>@if($booking->price_variant) - {{ ucfirst($booking->price_variant) }}@endif</small><br>
                        @endif
                        <small>Jenis Kamar: {{ $booking->room_type ? ucfirst($booking->room_type) : ($booking->price_variant ? ucfirst($booking->price_variant) : 'Standard') }}</small>
                        @if(count($familyNormal) > 0)
                        <br><small style="color:#555;">Termasuk: {{ $booking->jamaah->nama }}@foreach($familyNormal as $fn), {{ $fn['nama'] }}@endforeach</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $mainPax }} Pax</td>
                    <td class="text-right">Rp {{ number_format($unitPrice, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($mainSubtotal, 0, ',', '.') }}</td>
                </tr>
                @foreach($familyDiscount as $fd)
                <tr>
                    <td class="text-center">{{ $itemNo++ }}</td>
                    <td>
                        <strong>{{ $fd['nama'] }}</strong> - {{ $fd['kategori'] }}<br>
                        <small>{{ $fd['diskon'] }} | Usia: {{ $fd['age'] ?? '-' }} th</small>
                    </td>
                    <td class="text-center">1 Pax</td>
                    <td class="text-right">Rp {{ number_format($fd['price'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($fd['price'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @if(($booking->equipment_cost ?? 0) > 0)
                <tr>
                    <td class="text-center">{{ $itemNo++ }}</td>
                    <td><strong>Perlengkapan Tambahan</strong>@if($booking->equipment_notes)<br><small>{{ $booking->equipment_notes }}</small>@endif</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($booking->equipment_cost, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($booking->equipment_cost, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if(($booking->upgrade_cost ?? 0) > 0)
                <tr>
                    <td class="text-center">{{ $itemNo++ }}</td>
                    <td><strong>Upgrade</strong>@if($booking->upgrade_notes)<br><small>{{ $booking->upgrade_notes }}</small>@endif</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($booking->upgrade_cost, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($booking->upgrade_cost, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($booking->discount_amount > 0)
                <tr>
                    <td class="text-center">-</td>
                    <td colspan="3"><strong>Diskon</strong></td>
                    <td class="text-right" style="color: #dc3545;">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</td>
                </tr>
                @endif

                <!-- Hotel Booking yang di-charge -->
                @if($booking->hotelBookings && $booking->hotelBookings->where('is_charged', true)->count() > 0)
                @foreach($booking->hotelBookings->where('is_charged', true) as $hb)
                <tr>
                    <td class="text-center">{{ $itemNo++ }}</td>
                    <td>
                        <strong>Hotel {{ ucfirst($hb->city_type) }} - {{ $hb->hotel->hotel_name ?? '-' }}</strong><br>
                        <small>{{ $hb->room_type ? ucfirst($hb->room_type) : '-' }} | {{ $hb->check_in_date?->format('d/m/Y') }} - {{ $hb->check_out_date?->format('d/m/Y') }} ({{ $hb->nights }} malam)</small>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($hb->price_per_night, 0, ',', '.') }}/mlm</td>
                    <td class="text-right">Rp {{ number_format($hb->total_cost, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @endif

                <!-- Add-ons -->
                @if($booking->addons && $booking->addons->count() > 0)
                @foreach($booking->addons as $addon)
                <tr>
                    <td class="text-center">{{ $itemNo++ }}</td>
                    <td>
                        <strong>{{ $addon->nama }}</strong>
                        @if($addon->keterangan)<br><small>{{ $addon->keterangan }}</small>@endif
                    </td>
                    <td class="text-center">{{ $addon->qty }}</td>
                    <td class="text-right">Rp {{ number_format($addon->harga, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($addon->harga * $addon->qty, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <table>
                @if(count($familyDiscount) > 0)
                <tr>
                    <td colspan="4" class="text-right">Paket Utama ({{ $mainPax }} Pax × Rp {{ number_format($unitPrice, 0, ',', '.') }})</td>
                    <td class="text-right" style="width: 150px;">Rp {{ number_format($mainSubtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">Anggota Keluarga (Diskon)</td>
                    <td class="text-right">Rp {{ number_format($familyDiscountTotal, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="4" class="text-right"><b>Total Harga</b></td>
                    <td class="text-right" style="width: 150px;"><b>Rp {{ number_format($grandTotal + $booking->discount_amount, 0, ',', '.') }}</b></td>
                </tr>
                @if($booking->discount_amount > 0)
                <tr>
                    <td colspan="4" class="text-right"><b>Diskon</b></td>
                    <td class="text-right" style="color: #dc3545;"><b>- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</b></td>
                </tr>
                @endif
                @if(($booking->admin_discount ?? 0) > 0)
                <tr>
                    <td colspan="4" class="text-right"><b>Diskon Admin</b></td>
                    <td class="text-right" style="color: #007bff;"><b>- Rp {{ number_format($booking->admin_discount, 0, ',', '.') }}</b></td>
                </tr>
                @endif
                @if(($booking->voucher_discount ?? 0) > 0)
                <tr>
                    <td colspan="4" class="text-right"><b>Diskon Voucher @if($booking->voucher_code)({{ $booking->voucher_code }})@endif</b></td>
                    <td class="text-right" style="color: #28a745;"><b>- Rp {{ number_format($booking->voucher_discount, 0, ',', '.') }}</b></td>
                </tr>
                @endif
                <tr style="background: #4A7C59; color: white;">
                    <td colspan="4" class="text-right" style="padding: 8px;"><b>TOTAL BAYAR</b></td>
                    <td class="text-right" style="padding: 8px;"><b>Rp {{ number_format($grandTotal - ($booking->voucher_discount ?? 0) - ($booking->admin_discount ?? 0), 0, ',', '.') }}</b></td>
                </tr>
                @if($booking->payments && $booking->payments->count() > 0)
                    @foreach($booking->payments as $payment)
                    <tr style="background: #d4edda;">
                        <td colspan="4" class="text-right" style="padding: 5px;">
                            <b>{{ $payment->keterangan ?: 'Pembayaran' }} ({{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }})</b>
                        </td>
                        <td class="text-right" style="padding: 5px; color: #28a745;"><b>Rp {{ number_format($payment->amount, 0, ',', '.') }}</b></td>
                    </tr>
                    @endforeach
                @else
                <tr style="background: #d4edda;">
                    <td colspan="4" class="text-right" style="padding: 5px;"><b>Sudah Dibayar</b></td>
                    <td class="text-right" style="padding: 5px; color: #28a745;"><b>Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</b></td>
                </tr>
                @endif
                <tr style="background: #fff3cd;">
                    <td colspan="4" class="text-right" style="padding: 5px;"><b>Sisa Tagihan</b></td>
                    <td class="text-right" style="padding: 5px; color: #dc3545;"><b>Rp {{ number_format(max(0, $grandTotal - ($booking->voucher_discount ?? 0) - ($booking->admin_discount ?? 0) - $booking->paid_amount), 0, ',', '.') }}</b></td>
                </tr>
            </table>
        </div>

        <!-- Informasi Pembayaran -->
        @if(isset($bankAccounts) && $bankAccounts->count() > 0)
        <div class="bank-info">
            <b>INFORMASI PEMBAYARAN:</b><br>
            Silakan transfer ke salah satu rekening berikut:<br>
            @foreach($bankAccounts->take(3) as $bank)
                <span style="display: block; margin-top: 3px;">
                    <strong>{{ $bank->bank_name }}</strong> - {{ $bank->account_number }} a/n {{ $bank->account_holder }}
                </span>
            @endforeach
        </div>
        @endif

        <!-- Syarat dan Ketentuan -->
        @if(!empty($termsConditions))
        <div class="info-section">
            <div class="section-title">SYARAT DAN KETENTUAN</div>
            <div style="font-size: 9px; line-height: 1.3; white-space: pre-wrap;">{{ $termsConditions }}</div>
        </div>
        @endif

        <!-- Footer dengan Tanda Tangan -->
        <div class="signature-section">
            <table>
                <tr>
                    <!-- Kolom Kiri: Tanda Terima Jamaah -->
                    <td style="width: 33%; text-align: center;">
                        <b>Tanda Terima Jamaah</b><br><br><br><br>
                        ( {{ $booking->jamaah->nama }} )
                    </td>
                    <!-- Kolom Tengah: Pesan -->
                    <td style="width: 34%; text-align: center; font-size: 10px; vertical-align: bottom;">
                        <b>Mohon simpan invoice ini sebagai bukti pembayaran yang sah</b>
                    </td>
                    <!-- Kolom Kanan: Hormat Kami dengan Tanda Tangan & Cap -->
                    <td style="width: 33%; text-align: center;">
                        <b>Hormat Kami</b><br>
                        <div class="signature-box" style="position: relative; display: inline-block;">
                            @if(auth()->user() && auth()->user()->signature_path)
                            <img src="{{ public_path(auth()->user()->signature_path) }}" 
                                 alt="Tanda Tangan" 
                                 class="signature-image">
                            @endif
                            
                            @if(isset($companySettings['company_logo']) && $companySettings['company_logo'])
                            <img src="{{ storage_path('app/public/' . $companySettings['company_logo']) }}" 
                                 alt="Cap" 
                                 class="stamp-image">
                            @endif
                        </div><br>
                        <!-- ( {{ $invoice->user->name ?? auth()->user()->name ?? 'System' }} ) -->
                           ( Muhammad Abdul Aziz, S.E.)
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p style="text-align: center; margin-top: 10px;">
                Preview Invoice - {{ now()->format('d F Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>
