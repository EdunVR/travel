<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Jamaah - <?php echo e($invoice->no_invoice); ?></title>
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
            margin-top: 15px;
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
            margin: 0;
            padding: 0;
            height: 0;
            line-height: 0;
            font-size: 0;
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
    
    <div class="page-main">
        <!-- Header / Kop -->
        <div class="header">
            <table>
                <tr>
                    <!-- Kolom Kiri: Logo -->
                    <td style="width: 20%; vertical-align: middle;">
                        <?php
                            $logoPathInvH = $companySettings->company_logo ?? null;
                            $logoAbsInvH = null;
                            if ($logoPathInvH) {
                                $logoPathInvH = preg_replace('#^(https?://[^/]+)?/?(storage/)?#', '', $logoPathInvH);
                                $logoPathInvH = ltrim($logoPathInvH, '/');
                                $logoAbsInvH = storage_path('app/public/' . $logoPathInvH);
                            }
                        ?>
                        <?php if($logoAbsInvH && file_exists($logoAbsInvH)): ?>
                        <img src="<?php echo e($logoAbsInvH); ?>" 
                             alt="Logo" 
                             class="company-logo">
                        <?php else: ?>
                        <div class="logo-box">
                            <?php echo e(strtoupper(substr($companySettings->company_name ?? 'TRAVEL', 0, 8))); ?>

                        </div>
                        <?php endif; ?>
                    </td>
                    <!-- Kolom Tengah: Informasi Perusahaan -->
                    <td style="width: 55%; text-align: center; vertical-align: middle; padding: 0 8px;">
                        <div style="font-size: 15px; font-weight: bold; margin-bottom: 3px;">
                            <?php echo e(strtoupper($companySettings->company_name ?? 'PT. TRAVEL UMROH & HAJI')); ?>

                        </div>
                        <?php if($companySettings && $companySettings->company_address): ?>
                        <div style="font-size: 9px; line-height: 1.4; margin-bottom: 2px; word-wrap: break-word;">
                            <?php echo $companySettings->formatted_address ?? $companySettings->company_address; ?>

                        </div>
                        <?php endif; ?>
                        <div style="font-size: 9px;">
                            <?php if($companySettings && $companySettings->company_phone): ?>
                                TELP/WA: <?php echo e($companySettings->formatted_phone ?? $companySettings->company_phone); ?>

                            <?php endif; ?>
                            <?php if($companySettings && $companySettings->company_email): ?>
                                | <?php echo e($companySettings->company_email); ?>

                            <?php endif; ?>
                        </div>
                    </td>
                    <!-- Kolom Kanan: QR Code -->
                    <td style="width: 25%; text-align: right; vertical-align: middle;">
                        <?php
                            $invoiceToken = hash('sha256', $booking->id . $booking->id_invoice . config('app.key'));
                            $invoiceUrl = url('doc/invoice/' . $booking->id . '/' . $invoiceToken);
                        ?>
                        <?php if(class_exists('Milon\Barcode\Facades\DNS2DFacade')): ?>
                        <img src="data:image/png;base64,<?php echo e(DNS2D::getBarcodePNG($invoiceUrl, 'QRCODE', 3, 3)); ?>"
                             alt="QR Invoice" style="width: 70px; height: 70px;">
                        <div style="font-size: 8px; color: #666; margin-top: 2px; text-align: center;">Scan untuk invoice digital</div>
                        <?php endif; ?>
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
                                <td>: <?php echo e($invoice->no_invoice); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Invoice</strong></td>
                                <td>: <?php echo e($invoice->tanggal->format('d/m/Y H:i')); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Jatuh Tempo</strong></td>
                                <td>: <?php echo e($invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Kode Booking</strong></td>
                                <td>: <?php echo e($booking->booking_code); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status Invoice</strong></td>
                                <td>: <?php echo e(strtoupper(str_replace('_', ' ', $invoice->status))); ?></td>
                            </tr>
                        </table>
                    </td>
                    <!-- Kolom Kanan: Info Jamaah -->
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td style="width: 120px;"><strong>Nama Jamaah</strong></td>
                                <td>: <?php echo e($booking->jamaah->nama); ?></td>
                            </tr>
                            <tr>
                                <td><strong>No. KTP</strong></td>
                                <td>: <?php echo e($booking->jamaah->ktp_nik ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>No. Passport</strong></td>
                                <td>: <?php echo e($booking->jamaah->passport_nomor ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Telepon/WA</strong></td>
                                <td>: <?php echo e($booking->jamaah->telepon ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Kota Asal</strong></td>
                                <td>: <?php echo e($booking->jamaah->kota ?? $booking->jamaah->alamat ?? '-'); ?></td>
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
                        <?php
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
                        ?>
                    </td>
                    <td style="width: 33%;">
                        <strong>Seller/Closing:</strong>
                        <?php echo e($booking->seller_name ?? ($booking->closedBy->name ?? '-')); ?>

                    </td>
                    <td style="width: 34%;">
                        <strong>Sumber Closing:</strong>
                        <?php echo e($booking->closing_source ? ucfirst(str_replace('_', ' ', $booking->closing_source)) : '-'); ?>

                    </td>
                </tr>
                <?php
                    // Ambil family members HANYA dari booking, BUKAN dari member
                    // Ini memastikan hanya family members yang ditambahkan saat booking yang terhitung
                    $familyMembersH1 = $booking->family_members_booking;
                    if (is_string($familyMembersH1)) {
                        $familyMembersH1 = json_decode($familyMembersH1, true);
                    }
                    if (!is_array($familyMembersH1)) {
                        $familyMembersH1 = [];
                    }
                ?>
                <?php if(!empty($familyMembersH1) && is_array($familyMembersH1)): ?>
                <tr>
                    <td colspan="4" style="padding-top: 5px; border-top: 1px solid #ddd;">
                        <strong>Anggota Keluarga:</strong>
                        <?php echo e(implode(', ', array_map(fn($fm) => ($fm['nama'] ?? '') . (!empty($fm['hubungan']) ? ' (' . $fm['hubungan'] . ')' : ''), $familyMembersH1))); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Detail Paket -->
        <div class="info-section">
            <div class="section-title">DETAIL PAKET PERJALANAN</div>
            <table>
                <tr>
                    <td colspan="4" style="font-size: 12px; font-weight: bold; padding-bottom: 5px;">
                        <?php echo e($booking->travelPackage->package_name); ?>

                    </td>
                </tr>
                <tr>
                    <td style="width: 25%;">
                        <strong>Jenis Paket:</strong> <?php echo e(ucfirst($booking->travelPackage->package_type)); ?>

                    </td>
                    <td style="width: 25%;">
                        <strong>Durasi:</strong> <?php echo e($booking->travelPackage->duration_days); ?> Hari
                    </td>
                    <td style="width: 25%;">
                        <strong>Keberangkatan:</strong> <?php echo e($booking->travelPackage->departure_date->format('d/m/Y')); ?>

                    </td>
                    <td style="width: 25%;">
                        <strong>Kepulangan:</strong> <?php echo e($booking->travelPackage->return_date->format('d/m/Y')); ?>

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Maskapai Keberangkatan:</strong> 
                        <?php echo e($booking->travelPackage->flightDeparture->airline_name ?? $booking->travelPackage->airline ?? '-'); ?>

                        <?php if($booking->travelPackage->flightDeparture): ?>
                            (<?php echo e($booking->travelPackage->flightDeparture->flight_number); ?>)
                            â€” <?php echo e($booking->travelPackage->flightDeparture->departure_airport ?? ''); ?> - <?php echo e($booking->travelPackage->flightDeparture->arrival_airport ?? ''); ?>

                        <?php endif; ?>
                    </td>
                    <td colspan="2">
                        <strong>Maskapai Kepulangan:</strong> 
                        <?php echo e($booking->travelPackage->flightReturn->airline_name ?? $booking->travelPackage->airline ?? '-'); ?>

                        <?php if($booking->travelPackage->flightReturn): ?>
                            (<?php echo e($booking->travelPackage->flightReturn->flight_number); ?>)
                            â€” <?php echo e($booking->travelPackage->flightReturn->departure_airport ?? ''); ?> - <?php echo e($booking->travelPackage->flightReturn->arrival_airport ?? ''); ?>

                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Hotel Mekkah:</strong>
                        <?php
                            $hotelMakkahJamaah = $booking->hotelBookings ? $booking->hotelBookings->where('city_type', 'makkah')->first() : null;
                        ?>
                        <?php if($hotelMakkahJamaah): ?>
                            <?php echo e($hotelMakkahJamaah->hotel->hotel_name ?? '-'); ?>

                            <?php if($hotelMakkahJamaah->room_type): ?> (<?php echo e(ucfirst($hotelMakkahJamaah->room_type)); ?>)<?php endif; ?>
                            <?php if(!$hotelMakkahJamaah->is_charged): ?> <small style="color:#28a745;">Include Paket</small><?php else: ?> <small style="color:#dc3545;">Charge</small><?php endif; ?>
                        <?php elseif($booking->travelPackage->hotelMakkah): ?>
                            <?php echo e($booking->travelPackage->hotelMakkah->hotel_name); ?>

                            <?php if($booking->travelPackage->hotelMakkah->star_rating): ?> (<?php echo e($booking->travelPackage->hotelMakkah->star_rating); ?> bintang)<?php endif; ?>
                            <small style="color:#28a745;">Include Paket</small>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td colspan="2">
                        <strong>Hotel Madinah:</strong>
                        <?php
                            $hotelMadinahJamaah = $booking->hotelBookings ? $booking->hotelBookings->where('city_type', 'madinah')->first() : null;
                        ?>
                        <?php if($hotelMadinahJamaah): ?>
                            <?php echo e($hotelMadinahJamaah->hotel->hotel_name ?? '-'); ?>

                            <?php if($hotelMadinahJamaah->room_type): ?> (<?php echo e(ucfirst($hotelMadinahJamaah->room_type)); ?>)<?php endif; ?>
                            <?php if(!$hotelMadinahJamaah->is_charged): ?> <small style="color:#28a745;">Include Paket</small><?php else: ?> <small style="color:#dc3545;">Charge</small><?php endif; ?>
                        <?php elseif($booking->travelPackage->hotelMadinah): ?>
                            <?php echo e($booking->travelPackage->hotelMadinah->hotel_name); ?>

                            <?php if($booking->travelPackage->hotelMadinah->star_rating): ?> (<?php echo e($booking->travelPackage->hotelMadinah->star_rating); ?> bintang)<?php endif; ?>
                            <small style="color:#28a745;">Include Paket</small>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabel Detail Item -->
        <?php
            $itemNo = 1;
            $basePrice = $booking->total_price - ($booking->equipment_cost ?? 0) - ($booking->upgrade_cost ?? 0) + $booking->discount_amount;
            
            // Hitung harga dasar dari price_packages
            $selectedRoomType2 = $booking->price_variant ?? $booking->room_type ?? 'double';
            $selectedPkgName2 = $booking->price_package_name ?? null;
            $pricePackages2 = $booking->travelPackage->price_packages ?? [];
            if (is_string($pricePackages2)) $pricePackages2 = json_decode($pricePackages2, true);
            
            $unitPrice = 0;
            if (!empty($pricePackages2) && is_array($pricePackages2)) {
                $targetPkg2 = null;
                if ($selectedPkgName2) {
                    foreach ($pricePackages2 as $pp) {
                        if (strtolower($pp['name'] ?? '') === strtolower($selectedPkgName2)) { $targetPkg2 = $pp; break; }
                    }
                }
                if (!$targetPkg2) $targetPkg2 = $pricePackages2[0] ?? null;
                if ($targetPkg2) {
                    foreach ($targetPkg2['variants'] ?? [] as $v) {
                        if (strtolower($v['type'] ?? '') === strtolower($selectedRoomType2)) { $unitPrice = (float)($v['price'] ?? 0); break; }
                    }
                    if ($unitPrice == 0) {
                        foreach ($targetPkg2['variants'] ?? [] as $v) {
                            if (strtolower($v['type'] ?? '') === 'double') { $unitPrice = (float)($v['price'] ?? 0); break; }
                        }
                    }
                }
            }
            if ($unitPrice == 0) $unitPrice = (float)$basePrice;
            
            // Proses anggota keluarga - ambil HANYA dari booking, BUKAN dari member
            $familyMembers2 = $booking->family_members_booking;
            if (is_string($familyMembers2)) $familyMembers2 = json_decode($familyMembers2, true);
            if (!is_array($familyMembers2)) $familyMembers2 = [];
            
            // Pisahkan anggota: dewasa/tanpa tgl lahir vs diskon
            $familyNormal = []; // dewasa atau tanpa tgl lahir
            $familyDiscount = []; // infant atau anak
            foreach ($familyMembers2 as $fm) {
                if (empty($fm['tanggal_lahir'])) {
                    $familyNormal[] = array_merge($fm, ['price' => $unitPrice, 'kategori' => 'Dewasa', 'diskon' => '-']);
                } else {
                    $fmAge = \Carbon\Carbon::parse($fm['tanggal_lahir'])->age;
                    if ($fmAge < 2) {
                        $familyDiscount[] = array_merge($fm, ['price' => 18000000, 'kategori' => 'Infant (0-2th)', 'diskon' => 'Flat Rp 18jt', 'age' => $fmAge]);
                    } elseif ($fmAge <= 8) {
                        $familyDiscount[] = array_merge($fm, ['price' => $unitPrice * 0.85, 'kategori' => 'Anak (2-8th)', 'diskon' => 'Diskon 15%', 'age' => $fmAge]);
                    } else {
                        $familyNormal[] = array_merge($fm, ['price' => $unitPrice, 'kategori' => 'Dewasa', 'diskon' => '-', 'age' => $fmAge]);
                    }
                }
            }
            
            // Pax utama = 1 (jamaah) + jumlah anggota normal
            $mainPax = 1 + count($familyNormal);
            $mainSubtotal = $unitPrice * $mainPax;
        ?>
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
                    <td class="text-center"><?php echo e($itemNo++); ?></td>
                    <td>
                        <strong>Paket <?php echo e($booking->travelPackage->package_name); ?></strong><br>
                        <small><?php echo e(ucfirst($booking->travelPackage->package_type)); ?> - <?php echo e($booking->travelPackage->duration_days); ?> Hari</small><br>
                        <?php if($booking->price_package_name): ?>
                        <small>Paket Harga: <strong><?php echo e($booking->price_package_name); ?></strong><?php if($booking->price_variant): ?> - <?php echo e(ucfirst($booking->price_variant)); ?><?php endif; ?></small><br>
                        <?php endif; ?>
                        <small>Jenis Kamar: <?php echo e($booking->room_type ? ucfirst($booking->room_type) : ($booking->price_variant ? ucfirst($booking->price_variant) : 'Standard')); ?></small>
                        <?php if(count($familyNormal) > 0): ?>
                        <br><small style="color:#555;">Termasuk: <?php echo e($booking->jamaah->nama); ?>

                            <?php $__currentLoopData = $familyNormal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>, <?php echo e($fn['nama']); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($mainPax); ?> Pax</td>
                    <td class="text-right">Rp <?php echo e(number_format($unitPrice, 0, ',', '.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($mainSubtotal, 0, ',', '.')); ?></td>
                </tr>

                <!-- Anggota keluarga dengan diskon (infant/anak) -->
                <?php $__currentLoopData = $familyDiscount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($itemNo++); ?></td>
                    <td>
                        <strong><?php echo e($fd['nama']); ?></strong> - <?php echo e($fd['kategori']); ?><br>
                        <small><?php echo e($fd['diskon']); ?> | Usia: <?php echo e($fd['age'] ?? '-'); ?> th</small>
                    </td>
                    <td class="text-center">1 Pax</td>
                    <td class="text-right">Rp <?php echo e(number_format($fd['price'], 0, ',', '.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($fd['price'], 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Equipment/Perlengkapan (legacy field - only show if no BookingAddon records) -->
                <?php if(($booking->equipment_cost ?? 0) > 0 && (!$booking->addons || $booking->addons->count() === 0)): ?>
                <tr>
                    <td class="text-center"><?php echo e($itemNo++); ?></td>
                    <td>
                        <strong>Perlengkapan Tambahan</strong>
                        <?php if($booking->equipment_notes): ?><br><small><?php echo e($booking->equipment_notes); ?></small><?php endif; ?>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp <?php echo e(number_format($booking->equipment_cost, 0, ',', '.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($booking->equipment_cost, 0, ',', '.')); ?></td>
                </tr>
                <?php endif; ?>

                <!-- Upgrade -->
                <?php if(($booking->upgrade_cost ?? 0) > 0): ?>
                <tr>
                    <td class="text-center"><?php echo e($itemNo++); ?></td>
                    <td>
                        <strong>Upgrade</strong>
                        <?php if($booking->upgrade_notes): ?><br><small><?php echo e($booking->upgrade_notes); ?></small><?php endif; ?>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp <?php echo e(number_format($booking->upgrade_cost, 0, ',', '.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($booking->upgrade_cost, 0, ',', '.')); ?></td>
                </tr>
                <?php endif; ?>

                <!-- Discount -->
                <?php if($booking->discount_amount > 0): ?>
                <tr>
                    <td class="text-center">-</td>
                    <td colspan="3"><strong>Diskon</strong></td>
                    <td class="text-right" style="color: #dc3545;">- Rp <?php echo e(number_format($booking->discount_amount, 0, ',', '.')); ?></td>
                </tr>
                <?php endif; ?>

                <!-- Hotel Booking yang di-charge -->
                <?php
                    $chargedHotels = $booking->hotelBookings ? $booking->hotelBookings->where('is_charged', true) : collect();
                    $hotelChargeTotal = 0;
                ?>
                <?php $__currentLoopData = $chargedHotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $hotelChargeTotal += $hb->total_cost; ?>
                <tr>
                    <td class="text-center"><?php echo e($itemNo++); ?></td>
                    <td>
                        <strong>Hotel <?php echo e(ucfirst($hb->city_type)); ?> - <?php echo e($hb->hotel->hotel_name ?? '-'); ?></strong><br>
                        <small><?php echo e($hb->room_type ? ucfirst($hb->room_type) : '-'); ?> | <?php echo e($hb->check_in_date?->format('d/m/Y')); ?> - <?php echo e($hb->check_out_date?->format('d/m/Y')); ?> (<?php echo e($hb->nights); ?> malam)</small>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp <?php echo e(number_format($hb->price_per_night, 0, ',', '.')); ?>/mlm</td>
                    <td class="text-right">Rp <?php echo e(number_format($hb->total_cost, 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Add-ons -->
                <?php if($booking->addons && $booking->addons->count() > 0): ?>
                <?php $__currentLoopData = $booking->addons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($itemNo++); ?></td>
                    <td>
                        <strong><?php echo e($addon->nama); ?></strong>
                        <?php if($addon->keterangan): ?><br><small><?php echo e($addon->keterangan); ?></small><?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($addon->qty); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($addon->harga, 0, ',', '.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($addon->harga * $addon->qty, 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Total Section -->
        <?php
            $familyDiscountTotal = array_sum(array_column($familyDiscount, 'price'));
            $chargedHotelsTotal = $booking->hotelBookings ? $booking->hotelBookings->where('is_charged', true)->sum('total_cost') : 0;
            $addonsTotal = $booking->addons ? $booking->addons->sum(fn($a) => $a->harga * $a->qty) : 0;
            
            // Add handling fee if enabled
            $handlingFee = 0;
            if ($booking->travelPackage && $booking->travelPackage->include_handling_lounge_fee && $booking->travelPackage->handling_lounge_fee_amount > 0) {
                $handlingFee = $booking->travelPackage->handling_lounge_fee_amount;
            }
            
            $grandTotal = $mainSubtotal + $familyDiscountTotal + ($booking->equipment_cost ?? 0) + ($booking->upgrade_cost ?? 0) + $chargedHotelsTotal + $addonsTotal + $handlingFee - $booking->discount_amount;
        ?>
        <div class="total-section">
            <table>
                <?php if(count($familyDiscount) > 0): ?>
                <tr>
                    <td colspan="4" class="text-right">Paket Utama (<?php echo e($mainPax); ?> Pax × Rp <?php echo e(number_format($unitPrice, 0, ',', '.')); ?>)</td>
                    <td class="text-right" style="width: 150px;">Rp <?php echo e(number_format($mainSubtotal, 0, ',', '.')); ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">Anggota Keluarga (Diskon)</td>
                    <td class="text-right">Rp <?php echo e(number_format($familyDiscountTotal, 0, ',', '.')); ?></td>
                </tr>
                <?php endif; ?>
                <?php if($handlingFee > 0): ?>
                <tr>
                    <td colspan="4" class="text-right"><?php echo e($booking->travelPackage->handling_lounge_fee_description ?? 'Handling & Lounge Fee Wajib'); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($handlingFee, 0, ',', '.')); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="4" class="text-right"><b>Total Harga</b></td>
                    <td class="text-right" style="width: 150px;"><b>Rp <?php echo e(number_format($grandTotal + $booking->discount_amount, 0, ',', '.')); ?></b></td>
                </tr>
                <?php if($booking->discount_amount > 0): ?>
                <tr>
                    <td colspan="4" class="text-right"><b>Diskon</b></td>
                    <td class="text-right" style="color: #dc3545;"><b>- Rp <?php echo e(number_format($booking->discount_amount, 0, ',', '.')); ?></b></td>
                </tr>
                <?php endif; ?>
                <?php if(($booking->admin_discount ?? 0) > 0): ?>
                <tr>
                    <td colspan="4" class="text-right"><b>Diskon Admin</b></td>
                    <td class="text-right" style="color: #007bff;"><b>- Rp <?php echo e(number_format($booking->admin_discount, 0, ',', '.')); ?></b></td>
                </tr>
                <?php endif; ?>
                <?php if(($booking->voucher_discount ?? 0) > 0): ?>
                <tr>
                    <td colspan="4" class="text-right"><b>Diskon Voucher <?php if($booking->voucher_code): ?>(<?php echo e($booking->voucher_code); ?>)<?php endif; ?></b></td>
                    <td class="text-right" style="color: #28a745;"><b>- Rp <?php echo e(number_format($booking->voucher_discount, 0, ',', '.')); ?></b></td>
                </tr>
                <?php endif; ?>
                <tr style="background: #4A7C59; color: white;">
                    <td colspan="4" class="text-right" style="padding: 8px;"><b>TOTAL BAYAR</b></td>
                    <td class="text-right" style="padding: 8px;"><b>Rp <?php echo e(number_format($grandTotal - ($booking->voucher_discount ?? 0) - ($booking->admin_discount ?? 0), 0, ',', '.')); ?></b></td>
                </tr>
                <?php if($booking->payments && $booking->payments->count() > 0): ?>
                    <?php $__currentLoopData = $booking->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="background: #d4edda;">
                        <td colspan="4" class="text-right" style="padding: 5px;">
                            <b><?php echo e($payment->keterangan ?: 'Pembayaran'); ?> (<?php echo e(\Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y')); ?>)</b>
                        </td>
                        <td class="text-right" style="padding: 5px; color: #28a745;"><b>Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></b></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                <tr style="background: #d4edda;">
                    <td colspan="4" class="text-right" style="padding: 5px;"><b>Sudah Dibayar</b></td>
                    <td class="text-right" style="padding: 5px; color: #28a745;"><b>Rp <?php echo e(number_format($booking->paid_amount, 0, ',', '.')); ?></b></td>
                </tr>
                <?php endif; ?>
                <tr style="background: #fff3cd;">
                    <td colspan="4" class="text-right" style="padding: 5px;"><b>Sisa Tagihan</b></td>
                    <td class="text-right" style="padding: 5px; color: #dc3545;"><b>Rp <?php echo e(number_format(max(0, $grandTotal - ($booking->voucher_discount ?? 0) - ($booking->admin_discount ?? 0) - $booking->paid_amount), 0, ',', '.')); ?></b></td>
                </tr>
            </table>
        </div>

        <!-- Informasi Pembayaran -->
        <?php if(isset($bankAccounts) && $bankAccounts->count() > 0): ?>
        <div class="bank-info">
            <b>INFORMASI PEMBAYARAN:</b><br>
            Silakan transfer ke salah satu rekening berikut:<br>
            <?php $__currentLoopData = $bankAccounts->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span style="display: block; margin-top: 3px;">
                    <strong><?php echo e($bank->bank_name); ?></strong> - <?php echo e($bank->account_number); ?> a/n <?php echo e($bank->account_holder); ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <?php if($invoice->keterangan): ?>
        <div style="margin-top: 8px; padding: 8px; background: #f8f9fa; border-left: 3px solid #4A7C59; font-size: 10px;">
            <b>Catatan:</b> <?php echo e($invoice->keterangan); ?>

        </div>
        <?php endif; ?>

        <!-- Footer dengan Tanda Tangan -->
        <div class="signature-section">
            <table>
                <tr>
                    <!-- Kolom Kiri: Tanda Terima Jamaah -->
                    <td style="width: 33%; text-align: center;">
                        <b>Tanda Terima Jamaah</b><br><br><br><br>
                        ( <?php echo e($booking->jamaah->nama); ?> )
                    </td>
                    <!-- Kolom Tengah: Pesan -->
                    <td style="width: 34%; text-align: center; font-size: 10px; vertical-align: bottom;">
                        <b>Mohon simpan invoice ini sebagai bukti pembayaran yang sah</b>
                    </td>
                    <!-- Kolom Kanan: Hormat Kami dengan Tanda Tangan & Cap -->
                    <td style="width: 33%; text-align: center;">
                        <b>Hormat Kami</b><br>
                        <div class="signature-box" style="position: relative; display: inline-block;">
                            <?php
                                // Tanda tangan: dari closedBy, atau fallback ke user pertama yang punya signature
                                $sigUserInv = ($booking->closedBy && $booking->closedBy->signature_path)
                                    ? $booking->closedBy
                                    : \App\Models\User::whereNotNull('signature_path')->where('signature_path','!=','')->first();
                                // Cap/stamp logo
                                $stampPathInv = $companySettings->company_logo ?? null;
                                $stampAbsInv = null;
                                if ($stampPathInv) {
                                    $stampPathInv = preg_replace('#^(https?://[^/]+)?/?(storage/)?#', '', $stampPathInv);
                                    $stampPathInv = ltrim($stampPathInv, '/');
                                    $stampAbsInv = storage_path('app/public/' . $stampPathInv);
                                }
                            ?>
                            <?php if($sigUserInv && $sigUserInv->signature_path && file_exists(public_path($sigUserInv->signature_path))): ?>
                            <img src="<?php echo e(public_path($sigUserInv->signature_path)); ?>" 
                                 alt="Tanda Tangan" 
                                 class="signature-image">
                            <?php endif; ?>
                            
                            <?php if($stampAbsInv && file_exists($stampAbsInv)): ?>
                            <img src="<?php echo e($stampAbsInv); ?>" 
                                 alt="Cap" 
                                 class="stamp-image">
                            <?php endif; ?>
                        </div><br>
                        ( Muhammad Abdul Aziz, S.E.)
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p style="text-align: center; margin-top: 10px;">
                Dicetak pada: <?php echo e(now()->format('d F Y H:i:s')); ?> | Dokumen ini sah tanpa tanda tangan basah
            </p>
        </div>
    </div>

    
    <div class="page-break"></div>
    <div class="page-additional">
        <!-- Header Halaman 2 -->
        <div class="header">
            <table>
                <tr>
                    <td style="width: 15%; vertical-align: middle;">
                        <?php
                            $logoPathInvH2 = $companySettings->company_logo ?? null;
                            $logoAbsInvH2 = null;
                            if ($logoPathInvH2) {
                                $logoPathInvH2 = preg_replace('#^(https?://[^/]+)?/?(storage/)?#', '', $logoPathInvH2);
                                $logoPathInvH2 = ltrim($logoPathInvH2, '/');
                                $logoAbsInvH2 = storage_path('app/public/' . $logoPathInvH2);
                            }
                        ?>
                        <?php if($logoAbsInvH2 && file_exists($logoAbsInvH2)): ?>
                        <img src="<?php echo e($logoAbsInvH2); ?>" 
                             alt="Logo" 
                             class="company-logo">
                        <?php else: ?>
                        <div class="logo-box">
                            <?php echo e(strtoupper(substr($companySettings->company_name ?? 'TRAVEL', 0, 8))); ?>

                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="width: 85%; text-align: center; vertical-align: middle;">
                        <span style="font-size: 14px; font-weight: bold;">
                            LAMPIRAN INVOICE - <?php echo e($invoice->no_invoice); ?>

                        </span><br>
                        <span style="font-size: 11px;">
                            Kelengkapan Informasi Booking Jamaah
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Informasi Lengkap Jamaah -->
        <div class="info-section">
            <div class="section-title">DATA LENGKAP JAMAAH</div>
            <table style="font-size: 10px;">
                <tr>
                    <td style="width: 25%;"><strong>Nama Lengkap</strong></td>
                    <td style="width: 25%;">: <?php echo e($booking->jamaah->nama); ?></td>
                    <td style="width: 25%;"><strong>Jenis Kelamin</strong></td>
                    <td style="width: 25%;">: <?php echo e($booking->jamaah->jenis_kelamin ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Tempat Lahir</strong></td>
                    <td>: <?php echo e($booking->jamaah->ktp_tempat_lahir ?? $booking->jamaah->passport_tempat_lahir ?? '-'); ?></td>
                    <td><strong>Tanggal Lahir</strong></td>
                    <td>: <?php echo e($booking->jamaah->ktp_tanggal_lahir ? \Carbon\Carbon::parse($booking->jamaah->ktp_tanggal_lahir)->format('d/m/Y') : ($booking->jamaah->passport_tanggal_lahir ? \Carbon\Carbon::parse($booking->jamaah->passport_tanggal_lahir)->format('d/m/Y') : '-')); ?></td>
                </tr>
                <tr>
                    <td><strong>No. KTP</strong></td>
                    <td>: <?php echo e($booking->jamaah->ktp_nik ?? '-'); ?></td>
                    <td><strong>No. Passport</strong></td>
                    <td>: <?php echo e($booking->jamaah->passport_nomor ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Kadaluarsa Passport</strong></td>
                    <td>: <?php echo e($booking->jamaah->passport_tanggal_kadaluarsa ? \Carbon\Carbon::parse($booking->jamaah->passport_tanggal_kadaluarsa)->format('d/m/Y') : '-'); ?></td>
                    <td><strong>Telepon/WA</strong></td>
                    <td>: <?php echo e($booking->jamaah->telepon ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>: <?php echo e($booking->jamaah->email ?? '-'); ?></td>
                    <td><strong>Pekerjaan</strong></td>
                    <td>: <?php echo e($booking->jamaah->pekerjaan ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Alamat Lengkap</strong></td>
                    <td colspan="3">: <?php echo e($booking->jamaah->alamat ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Kota/Domisili</strong></td>
                    <td>: <?php echo e($booking->jamaah->kota ?? '-'); ?></td>
                    <td><strong>Provinsi</strong></td>
                    <td>: <?php echo e($booking->jamaah->provinsi ?? '-'); ?></td>
                </tr>
            </table>
        </div>

        <!-- Informasi Mahram & Kontak Darurat -->
        <div class="info-section">
            <div class="section-title">MAHRAM &amp; KONTAK DARURAT</div>
            <table style="font-size: 10px;">
                <tr>
                    <td style="width: 25%;"><strong>Nama Mahram</strong></td>
                    <td style="width: 25%;">: <?php echo e($booking->jamaah->mahram_name ?? '-'); ?></td>
                    <td style="width: 25%;"><strong>Hubungan Mahram</strong></td>
                    <td style="width: 25%;">: <?php echo e($booking->jamaah->mahram_relationship ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Kontak Darurat</strong></td>
                    <td>: <?php echo e($booking->jamaah->emergency_contact_name ?? '-'); ?></td>
                    <td><strong>No. Telp Darurat</strong></td>
                    <td>: <?php echo e($booking->jamaah->emergency_contact_phone ?? '-'); ?></td>
                </tr>
                <tr>
                    <td><strong>Hubungan</strong></td>
                    <td colspan="3">: <?php echo e($booking->jamaah->emergency_contact_relationship ?? '-'); ?></td>
                </tr>
            </table>
        </div>

        <!-- Anggota Keluarga -->
        <?php
            // Ambil family members HANYA dari booking, BUKAN dari member
            $familyMembersH2 = $booking->family_members_booking;
            if (is_string($familyMembersH2)) $familyMembersH2 = json_decode($familyMembersH2, true);
            if (!is_array($familyMembersH2)) $familyMembersH2 = [];
            $basePriceH2 = $unitPrice > 0 ? $unitPrice : (float)$booking->travelPackage->price;
            $familyTotalH2 = 0;
        ?>
        <?php if(!empty($familyMembersH2) && is_array($familyMembersH2)): ?>
        <div class="info-section">
            <div class="section-title">ANGGOTA KELUARGA</div>
            <table class="data" style="font-size: 10px;">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="25%">Nama</th>
                        <th width="12%">Hubungan</th>
                        <th width="12%">Tgl Lahir</th>
                        <th width="8%">Usia</th>
                        <th width="12%">Kategori</th>
                        <th width="27%" class="text-right">Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $familyMembersH2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $fm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $fmAge2 = null; $fmCat2 = 'Dewasa'; $fmPrice2 = $basePriceH2;
                        if (!empty($fm['tanggal_lahir'])) {
                            $fmAge2 = \Carbon\Carbon::parse($fm['tanggal_lahir'])->age;
                            if ($fmAge2 < 2) { $fmCat2 = 'Infant (0-2th)'; $fmPrice2 = 18000000; }
                            elseif ($fmAge2 <= 8) { $fmCat2 = 'Anak (2-8th)'; $fmPrice2 = $basePriceH2 * 0.85; }
                        }
                        $familyTotalH2 += $fmPrice2;
                    ?>
                    <tr>
                        <td class="text-center"><?php echo e($i + 1); ?></td>
                        <td><?php echo e($fm['nama'] ?? '-'); ?></td>
                        <td><?php echo e($fm['hubungan'] ?? '-'); ?></td>
                        <td><?php echo e(!empty($fm['tanggal_lahir']) ? \Carbon\Carbon::parse($fm['tanggal_lahir'])->format('d/m/Y') : '-'); ?></td>
                        <td class="text-center"><?php echo e($fmAge2 !== null ? $fmAge2 . ' th' : '-'); ?></td>
                        <td><?php echo e($fmCat2); ?></td>
                        <td class="text-right"><strong>Rp <?php echo e(number_format($fmPrice2, 0, ',', '.')); ?></strong></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr style="background:#f0f4ff;">
                        <td colspan="6" class="text-right"><strong>Total</strong></td>
                        <td class="text-right"><strong>Rp <?php echo e(number_format($familyTotalH2, 0, ',', '.')); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Riwayat Pembayaran -->
        <?php if($booking->payments && $booking->payments->count() > 0): ?>
        <div class="info-section">
            <div class="section-title">RIWAYAT PEMBAYARAN</div>
            <table class="data" style="font-size: 10px;">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="18%">Tanggal</th>
                        <th width="20%">Metode</th>
                        <th width="22%">Referensi</th>
                        <th width="20%" class="text-right">Jumlah</th>
                        <th width="15%">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $booking->payments->sortBy('payment_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-center"><?php echo e($index + 1); ?></td>
                        <td><?php echo e($payment->payment_date->format('d/m/Y')); ?></td>
                        <td><?php echo e($payment->formatted_payment_method ?? ucfirst(str_replace('_', ' ', $payment->payment_method))); ?></td>
                        <td><?php echo e($payment->reference_number ?? '-'); ?></td>
                        <td class="text-right">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></td>
                        <td><?php echo e($payment->recordedBy->name ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Syarat dan Ketentuan -->
        <div class="info-section">
            <div class="section-title">SYARAT DAN KETENTUAN</div>
            <div style="font-size: 9px; line-height: 1.5;">
                <?php if(!empty($booking->terms_conditions)): ?>
                    <div style="white-space: pre-wrap;"><?php echo e($booking->terms_conditions); ?></div>
                <?php else: ?>
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 10px;">Ketentuan Pembelian Paket Umrah:</strong>
                    <ol style="margin: 5px 0; padding-left: 18px;">
                        <li>Pembayaran DP untuk Booking seat sebesar 10 juta/pax di transfer ke rekening Perusahaan.</li>
                        <li>Pembayaran 50% harga paket dilakukan maksimal H-40 dari tanggal keberangkatan.</li>
                        <li>Pelunasan dilakukan paling lambat H-30 dari tanggal keberangkatan.</li>
                        <li>Ketentuan Pembatalan:
                            <ol style="list-style-type: lower-alpha; padding-left: 15px; margin: 3px 0;">
                                <li>Pembatalan dikenakan biaya 3 juta/Jemaah Non Refundable.</li>
                                <li>Pembatalan Setelah H-40 dikenakan biaya 5 juta/Jemaah Non Refundable.</li>
                                <li>Pembatalan setelah H-30 dikenakan biaya seharga tiket pesawat.</li>
                                <li>Pembatalan/perubahan paket H-20 Non Refundable.</li>
                            </ol>
                        </li>
                        <li>Tiket pesawat kelas ekonomi. Untuk upgrade ke bisnis, hubungi kami untuk ketersediaan kursi dan biaya tambahan.</li>
                        <li>Jika hingga H-16 keberangkatan kuota kamar paket QUAD belum terpenuhi, pembeli harus upgrade sesuai ketersediaan kamar.</li>
                        <li>Jika terdapat Force Majure maka tidak dapat dibebankan kepada travel.</li>
                    </ol>
                </div>
                
                <div style="margin-bottom: 10px;">
                    <strong style="font-size: 10px;">Ketentuan Pendaftaran:</strong>
                    <ol style="margin: 5px 0; padding-left: 18px;">
                        <li>Biaya Paket dan jadwal penerbangan dapat berubah sewaktu-waktu apabila terdapat perubahan Kebijakan dari Negara-negara yang berkaitan seperti kebijakan Persyaratan Kesehatan, Tiket Pesawat, Hotel & Visa. Harga Paket juga menyesuaikan apabila terdapat perubahan Kurs yang signifikan.</li>
                        <li>Program perjalanan sewaktu-waktu bisa berubah mengikuti situasi dan kondisi terupdate.</li>
                        <li>Jadwal Penerbangan Travel yang telah terbooking dapat berubah sewaktu-waktu mengikuti kebijakan dan kondisi terbaru maskapai.</li>
                        <li>Perihal Hotel yang telah tertera diflyer, ataupun jika terdapat perubahan ke hotel yang setaraf merupakan wewenang dari pihak HM Tour & Travel sepenuhnya.</li>
                        <li>Jika terdapat kondisi Force Majure karna (bencana alam, kerusuhan, peperangan, huru-hara keributan, blokade, perselisihan, perburuan, pemogokan, wabah penyakit, kebijakan pemerintah setempat, dll) rencana perjalanan dapat dirubah baik susunan maupun tanggal keberangkatanya baik dengan pemberitahuan atau tanpa pemberitahuan terlebih dahulu, hal ini demi kepentingan dan keamanan seluruh rombongan dan HM Tour & Travel tidak bertanggung jawab dalam pengembalian biaya atau uang service yang sudah dibayarkan namun tidak digunakan dikarnakan Force Majure.</li>
                        <li>Terkait dengan hotel di Indonesia serta paket City Tour Al Ula dan Thoif, perlu kami informasikan bahwa biaya tersebut tidak dapat diuangkan kembali karena statusnya sudah bersifat kolektif.</li>
                    </ol>
                </div>
                
                <div>
                    <strong style="font-size: 10px;">Ketentuan Umum:</strong>
                    <p style="margin: 5px 0;">
                        Dengan melakukan DP (Down Payment) ke Pihak HM Tour & Travel, maka Jemaah dianggap memahami, mengetahui, menyetujui dan bersedia mengikuti semua Ketentuan tertulis yang ada di HM Tour & Travel dalam keadaan sadar dan tanpa tekanan dari pihak manapun.
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            <p style="text-align: center; font-size: 9px;">
                Halaman 2 dari 2 - Lampiran Invoice <?php echo e($invoice->no_invoice); ?>

            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/payment/jamaah-invoice-pdf.blade.php ENDPATH**/ ?>