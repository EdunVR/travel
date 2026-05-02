<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Paket - {{ $package->package_name }}</title>
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
            width: 60px;
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
            width: 60px;
            float: left;
            margin-right: 10px;
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
        
        .price-box {
            background: #4A7C59;
            color: white;
            padding: 15px;
            text-align: center;
            margin: 10px 0;
        }
        
        .price-box .label {
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .price-box .amount {
            font-size: 18px;
            font-weight: bold;
        }
        
        .inclusions-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        
        .inclusions-list li {
            padding: 3px 0;
            padding-left: 20px;
            position: relative;
            font-size: 10px;
        }
        
        .inclusions-list li:before {
            content: "v";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
        
        .photo-gallery img {
            width: 32%;
            height: auto;
            margin: 0.5%;
            border: 1px solid #ddd;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-hajj {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-umrah {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <!-- Header dengan Logo -->
    <div class="header">
        <table>
            <tr>
                <!-- Kolom Kiri: Logo -->
                <td style="width: 15%;">
                    @php
                        $companySettings = [];
                        if ($package->outlet) {
                            $companySettings = [
                                'company_logo' => $package->outlet->logo_path ?? null,
                                'company_name' => $package->outlet->nama_outlet ?? 'TRAVEL UMROH & HAJI',
                                'company_address' => $package->outlet->alamat ?? '',
                                'company_phone' => $package->outlet->telepon ?? '',
                                'company_email' => $package->outlet->email ?? ''
                            ];
                        }
                    @endphp
                    
                    @if(isset($companySettings['company_logo']) && $companySettings['company_logo'])
                    <img src="{{ storage_path('app/public/' . $companySettings['company_logo']) }}" 
                         alt="Logo" 
                         class="company-logo">
                    @else
                    <div class="logo-box">
                        {{ strtoupper(substr($companySettings['company_name'] ?? 'TRAVEL', 0, 8)) }}
                    </div>
                    @endif
                </td>
                <!-- Kolom Tengah: Informasi Perusahaan -->
                <td style="width: 70%; text-align: center;">
                    <span style="font-size: 14px; font-weight: bold;">
                        {{ strtoupper($companySettings['company_name'] ?? 'PT. TRAVEL UMROH & HAJI') }}
                    </span><br>
                    <span style="font-size: 10px;">
                        @if($companySettings['company_address'])
                            {{ $companySettings['company_address'] }}
                        @endif
                    </span><br>
                    <span style="font-size: 10px;">
                        @if($companySettings['company_phone'])
                            TELP/WA: {{ $companySettings['company_phone'] }}
                        @endif
                        @if($companySettings['company_email'])
                            | Email: {{ $companySettings['company_email'] }}
                        @endif
                    </span><br>
                    <span style="font-size: 12px; font-weight: bold; margin-top: 5px; display: inline-block;">DETAIL PAKET PERJALANAN</span>
                </td>
                <!-- Kolom Kanan: Badge Jenis Paket -->
                <td style="width: 15%; text-align: right; vertical-align: top;">
                    <span class="badge badge-{{ $package->package_type }}">
                        {{ strtoupper($package->package_type) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Informasi Paket -->
    <div class="body">
        <table>
            <tr>
                <td colspan="2" style="font-size: 14px; font-weight: bold; padding-bottom: 5px;">
                    {{ $package->package_name }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    <table>
                        <tr>
                            <td style="width: 120px;"><strong>Kode Paket</strong></td>
                            <td>: {{ $package->package_code }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis Paket</strong></td>
                            <td>: {{ ucfirst($package->package_type) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Durasi</strong></td>
                            <td>: {{ $package->duration_days }} Hari</td>
                        </tr>
                        <tr>
                            <td><strong>Keberangkatan</strong></td>
                            <td>: {{ $package->departure_date ? $package->departure_date->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kepulangan</strong></td>
                            <td>: {{ $package->return_date ? $package->return_date->format('d M Y') : '-' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table>
                        <tr>
                            <td style="width: 120px;"><strong>Kapasitas</strong></td>
                            <td>: {{ $package->capacity }} Jamaah</td>
                        </tr>
                        <tr>
                            <td><strong>Outlet</strong></td>
                            <td>: {{ $package->outlet ? $package->outlet->nama_outlet : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>: {{ strtoupper($package->status) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Harga Paket -->
    @php
        $pricePackages = $package->price_packages;
        if (is_string($pricePackages)) $pricePackages = json_decode($pricePackages, true);
        if (!is_array($pricePackages)) $pricePackages = [];
    @endphp

    @if(count($pricePackages) > 0)
    <div class="info-section">
        <div class="section-title">💰 HARGA PER JAMAAH</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Paket Harga</th>
                    <th class="text-center">Quad (4 Orang)</th>
                    <th class="text-center">Triple (3 Orang)</th>
                    <th class="text-center">Double (2 Orang)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pricePackages as $pkg)
                <tr>
                    <td><strong>{{ $pkg['name'] ?? '-' }}</strong></td>
                    @php
                        $variants = collect($pkg['variants'] ?? []);
                        $quad   = $variants->firstWhere('type', 'quad');
                        $triple = $variants->firstWhere('type', 'triple');
                        $double = $variants->firstWhere('type', 'double');
                    @endphp
                    <td class="text-center">{{ $quad   ? 'Rp ' . number_format($quad['price'],   0, ',', '.') : '-' }}</td>
                    <td class="text-center">{{ $triple ? 'Rp ' . number_format($triple['price'], 0, ',', '.') : '-' }}</td>
                    <td class="text-center">{{ $double ? 'Rp ' . number_format($double['price'], 0, ',', '.') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="price-box">
        <div class="label">HARGA PER JAMAAH</div>
        <div class="amount">Rp {{ number_format($package->price, 0, ',', '.') }}</div>
    </div>
    @endif

    <!-- Gambar Utama Paket -->
    @if($package->image_path)
    <div style="text-align: center; margin: 10px 0;">
        <img src="{{ public_path('storage/' . $package->image_path) }}" 
             alt="{{ $package->package_name }}"
             style="max-width: 100%; max-height: 200px; border: 1px solid #ddd;">
    </div>
    @endif

    <!-- Deskripsi -->
    @if($package->description)
    <div class="info-section">
        <div class="section-title">DESKRIPSI PAKET</div>
        <p style="text-align: justify; font-size: 10px; margin: 0;">{{ $package->description }}</p>
    </div>
    @endif

    <!-- Informasi Penerbangan -->
    <div class="info-section">
        <div class="section-title">INFORMASI PENERBANGAN</div>
        <table class="data">
            <thead>
                <tr>
                    <th width="15%">Jenis</th>
                    <th width="20%">Maskapai</th>
                    <th width="15%">No. Penerbangan</th>
                    <th width="25%">Rute & Waktu</th>
                    <th width="25%">Host Seller</th>
                </tr>
            </thead>
            <tbody>
                @if($package->flightDeparture)
                <tr>
                    <td><strong>Keberangkatan</strong></td>
                    <td>{{ $package->flightDeparture->airline_name }}</td>
                    <td>{{ $package->flightDeparture->flight_number }}</td>
                    <td>
                        {{ $package->flightDeparture->departure_airport }} - {{ $package->flightDeparture->arrival_airport }}
                        @if($package->departure_datetime)
                        <br><small>{{ $package->departure_datetime->format('d/m/Y H:i') }} WIB</small>
                        @endif
                    </td>
                    <td>
                        @if($package->flightDeparture->seller_name)
                            {{ $package->flightDeparture->seller_name }}
                            @if($package->flightDeparture->seller_phone)
                                <br><small>{{ $package->flightDeparture->seller_phone }}</small>
                            @endif
                        @else
                            <span style="color:#999;">-</span>
                        @endif
                    </td>
                </tr>
                @else
                <tr>
                    <td><strong>Keberangkatan</strong></td>
                    <td colspan="4" class="text-center" style="color: #999;">Belum ditentukan</td>
                </tr>
                @endif
                
                @if($package->flightReturn)
                <tr>
                    <td><strong>Kepulangan</strong></td>
                    <td>{{ $package->flightReturn->airline_name }}</td>
                    <td>{{ $package->flightReturn->flight_number }}</td>
                    <td>
                        {{ $package->flightReturn->departure_airport }} - {{ $package->flightReturn->arrival_airport }}
                        @if($package->return_datetime)
                        <br><small>{{ $package->return_datetime->format('d/m/Y H:i') }} WIB</small>
                        @endif
                    </td>
                    <td>
                        @if($package->flightReturn->seller_name)
                            {{ $package->flightReturn->seller_name }}
                            @if($package->flightReturn->seller_phone)
                                <br><small>{{ $package->flightReturn->seller_phone }}</small>
                            @endif
                        @else
                            <span style="color:#999;">-</span>
                        @endif
                    </td>
                </tr>
                @else
                <tr>
                    <td><strong>Kepulangan</strong></td>
                    <td colspan="4" class="text-center" style="color: #999;">Belum ditentukan</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Informasi Hotel -->
    <div class="info-section">
        <div class="section-title">INFORMASI AKOMODASI</div>
        <table class="data">
            <thead>
                <tr>
                    <th width="12%">Lokasi</th>
                    <th width="22%">Nama Hotel</th>
                    <th width="8%">Bintang</th>
                    <th width="15%">Tipe Kamar</th>
                    <th width="20%">Check-in / Check-out</th>
                    <th width="23%">Host Seller</th>
                </tr>
            </thead>
            <tbody>
                @if($package->hotelMakkah)
                <tr>
                    <td><strong>Mekkah</strong></td>
                    <td>{{ $package->hotelMakkah->hotel_name }}</td>
                    <td class="text-center">{{ $package->hotelMakkah->star_rating }} bintang</td>
                    <td>{{ $package->hotelRoomTypeMakkah ? $package->hotelRoomTypeMakkah->room_type_name : '-' }}</td>
                    <td>
                        @if($package->makkah_check_in && $package->makkah_check_out)
                            {{ $package->makkah_check_in->format('d/m/Y') }} - {{ $package->makkah_check_out->format('d/m/Y') }}
                            <br><small>({{ $package->makkah_check_in->diffInDays($package->makkah_check_out) }} malam)</small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($package->hotelMakkah->seller_name)
                            {{ $package->hotelMakkah->seller_name }}
                            @if($package->hotelMakkah->seller_phone)
                                <br><small>{{ $package->hotelMakkah->seller_phone }}</small>
                            @endif
                        @else
                            <span style="color:#999;">-</span>
                        @endif
                    </td>
                </tr>
                @else
                <tr>
                    <td><strong>Mekkah</strong></td>
                    <td colspan="5" class="text-center" style="color: #999;">Belum ditentukan</td>
                </tr>
                @endif
                
                @if($package->hotelMadinah)
                <tr>
                    <td><strong>Madinah</strong></td>
                    <td>{{ $package->hotelMadinah->hotel_name }}</td>
                    <td class="text-center">{{ $package->hotelMadinah->star_rating }} bintang</td>
                    <td>{{ $package->hotelRoomTypeMadinah ? $package->hotelRoomTypeMadinah->room_type_name : '-' }}</td>
                    <td>
                        @if($package->madinah_check_in && $package->madinah_check_out)
                            {{ $package->madinah_check_in->format('d/m/Y') }} - {{ $package->madinah_check_out->format('d/m/Y') }}
                            <br><small>({{ $package->madinah_check_in->diffInDays($package->madinah_check_out) }} malam)</small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($package->hotelMadinah->seller_name)
                            {{ $package->hotelMadinah->seller_name }}
                            @if($package->hotelMadinah->seller_phone)
                                <br><small>{{ $package->hotelMadinah->seller_phone }}</small>
                            @endif
                        @else
                            <span style="color:#999;">-</span>
                        @endif
                    </td>
                </tr>
                @else
                <tr>
                    <td><strong>Madinah</strong></td>
                    <td colspan="5" class="text-center" style="color: #999;">Belum ditentukan</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Fasilitas yang Termasuk -->
    @if($package->inclusions)
    <div class="info-section">
        <div class="section-title">FASILITAS YANG TERMASUK</div>
        <ul class="inclusions-list">
            @php
                $inclusions = [];
                if (is_string($package->inclusions)) {
                    $inclusions = explode("\n", $package->inclusions);
                } elseif (is_array($package->inclusions)) {
                    $inclusions = $package->inclusions;
                } elseif (method_exists($package, 'getInclusionsArray')) {
                    $inclusions = $package->getInclusionsArray();
                }
            @endphp
            @foreach($inclusions as $inclusion)
                @if(trim($inclusion))
                <li>{{ trim($inclusion) }}</li>
                @endif
            @endforeach
        </ul>
    </div>
    @endif

    <!-- HPP Calculation -->
    @if($package->hppCalculation)
    @php
        $hpp = $package->hppCalculation;
        $hppPerOrang = ($hpp->flight_cost ?? 0) + ($hpp->hotel_cost ?? 0) + ($hpp->transportation_cost ?? 0)
                     + ($hpp->meal_cost ?? 0) + ($hpp->visa_cost ?? 0) + ($hpp->guide_cost ?? 0)
                     + ($hpp->insurance_cost ?? 0) + ($hpp->operational_overhead ?? 0) + ($hpp->contingency ?? 0);
    @endphp
    <div class="info-section">
        <div class="section-title">📊 HPP (HARGA POKOK PENJUALAN)</div>
        <table class="data">
            <thead>
                <tr>
                    <th width="50%">Komponen Biaya</th>
                    <th width="25%" class="text-right">Per Orang</th>
                    <th width="25%" class="text-right">Total ({{ $package->capacity }} Jamaah)</th>
                </tr>
            </thead>
            <tbody>
                @if($hpp->flight_cost > 0)
                <tr>
                    <td>Biaya Penerbangan</td>
                    <td class="text-right">Rp {{ number_format($hpp->flight_cost, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hpp->flight_cost * $package->capacity, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($hpp->hotel_cost > 0)
                <tr>
                    <td>Biaya Hotel</td>
                    <td class="text-right">Rp {{ number_format($hpp->hotel_cost, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hpp->hotel_cost * $package->capacity, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($hpp->transportation_cost > 0)
                <tr>
                    <td>Biaya Transportasi</td>
                    <td class="text-right">Rp {{ number_format($hpp->transportation_cost, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hpp->transportation_cost * $package->capacity, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($hpp->meal_cost > 0)
                <tr>
                    <td>Biaya Makan</td>
                    <td class="text-right">Rp {{ number_format($hpp->meal_cost, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hpp->meal_cost * $package->capacity, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($hpp->visa_cost > 0)
                <tr>
                    <td>Biaya Visa</td>
                    <td class="text-right">Rp {{ number_format($hpp->visa_cost, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hpp->visa_cost * $package->capacity, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($hpp->guide_cost > 0)
                <tr>
                    <td>Biaya Pembimbing</td>
                    <td class="text-right">Rp {{ number_format($hpp->guide_cost, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hpp->guide_cost * $package->capacity, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($hpp->insurance_cost > 0)
                <tr>
                    <td>Biaya Asuransi</td>
                    <td class="text-right">Rp {{ number_format($hpp->insurance_cost, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hpp->insurance_cost * $package->capacity, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($hpp->operational_overhead > 0)
                <tr>
                    <td>Biaya Operasional</td>
                    <td class="text-right">Rp {{ number_format($hpp->operational_overhead, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hpp->operational_overhead * $package->capacity, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($hpp->contingency > 0)
                <tr>
                    <td>Biaya Kontingensi</td>
                    <td class="text-right">Rp {{ number_format($hpp->contingency, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hpp->contingency * $package->capacity, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr style="background:#f0f4ff; font-weight:bold;">
                    <td>TOTAL HPP</td>
                    <td class="text-right">Rp {{ number_format($hppPerOrang, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hppPerOrang * $package->capacity, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Simulasi profit per price package --}}
        @if(count($pricePackages) > 0)
        <table class="data" style="margin-top:6px;">
            <thead>
                <tr>
                    <th>Paket Harga</th>
                    <th class="text-center">Varian</th>
                    <th class="text-right">Harga Jual</th>
                    <th class="text-right">HPP/Orang</th>
                    <th class="text-right">Profit/Orang</th>
                    <th class="text-right">Margin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pricePackages as $pkg)
                    @foreach($pkg['variants'] ?? [] as $v)
                    @php
                        $harga  = (float)($v['price'] ?? 0);
                        $profit = $harga - $hppPerOrang;
                        $margin = $harga > 0 ? ($profit / $harga * 100) : 0;
                    @endphp
                    <tr>
                        <td>{{ $pkg['name'] ?? '-' }}</td>
                        <td class="text-center" style="text-transform:capitalize;">{{ $v['type'] ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($hppPerOrang, 0, ',', '.') }}</td>
                        <td class="text-right" style="color:{{ $profit >= 0 ? '#155724' : '#721c24' }};">
                            Rp {{ number_format($profit, 0, ',', '.') }}
                        </td>
                        <td class="text-right" style="color:{{ $margin >= 0 ? '#155724' : '#721c24' }};">
                            {{ number_format($margin, 1) }}%
                        </td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    <!-- Galeri Foto -->
    @php
        $photos = [];
        if ($package->package_photos) {
            if (is_string($package->package_photos)) {
                $photos = json_decode($package->package_photos, true) ?: [];
            } elseif (is_array($package->package_photos)) {
                $photos = $package->package_photos;
            }
        }
    @endphp
    @if(count($photos) > 0)
    <div class="info-section">
        <div class="section-title">GALERI FOTO PAKET</div>
        <div class="photo-gallery">
            @foreach($photos as $photo)
                @if(file_exists(public_path('storage/' . $photo)))
                <img src="{{ public_path('storage/' . $photo) }}" alt="Package Photo">
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis pada {{ now()->format('d F Y H:i:s') }} WIB</p>
        <p style="margin-top: 5px;">{{ $package->outlet ? $package->outlet->nama_outlet : '' }}</p>
        <p style="margin-top: 5px; font-size: 8px;">Untuk informasi lebih lanjut, hubungi kami di {{ $companySettings['company_phone'] ?? '' }}</p>
    </div>
</body>
</html>
