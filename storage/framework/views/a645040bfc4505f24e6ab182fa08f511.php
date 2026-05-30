<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Information Package - <?php echo e($keberangkatan->keberangkatan_name); ?></title>
    <style>
        @page {
            size: A4;
            margin: 12mm 10mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #000;
        }
        
        h2 {
            text-align: center;
            font-size: 14px;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }
        
        h3 {
            text-align: center;
            font-size: 12px;
            margin: 0 0 15px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            margin: 15px 0 5px 0;
            padding: 4px;
            background: #e0e0e0;
            border: 1px solid #000;
        }
        
        table.data {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
        }
        
        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 10px;
            vertical-align: middle;
        }
        
        table.data th {
            background-color: #d9d9d9;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .header-table td {
            padding: 2px 5px;
            font-size: 10px;
            border: 1px solid #000;
        }
        
        .header-table {
            margin-bottom: 15px;
        }
        
        .no-border td {
            border: none;
            padding: 2px 5px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <?php
        $package = $keberangkatan->travelPackage;
        $companyName = 'HM TOUR & TRAVEL';
        if ($package && $package->outlet) {
            $companyName = strtoupper($package->outlet->nama_outlet ?? 'HM TOUR & TRAVEL');
        }
        
        $departureDate = $keberangkatan->departure_date;
        $returnDate = $keberangkatan->return_date;
        
        // Use saved infoPaketData if available
        $hasInfoPaketData = isset($infoPaketData) && $infoPaketData;
        
        if ($hasInfoPaketData) {
            $adultCount = $infoPaketData->adult_count ?? 0;
            $childCount = $infoPaketData->child_count ?? 0;
            $infantCount = $infoPaketData->infant_count ?? 0;
            $tourLeader = $infoPaketData->tour_leader_name ?? '-';
            $groupName = $infoPaketData->group_name ?? '';
            $rawdahRows = $infoPaketData->rawdah_rows ?? [];
            $itineraryRows = $infoPaketData->itinerary_rows ?? [];
        } else {
            // Fallback: compute from data
            $rawdahDate = $departureDate ? $departureDate->copy()->addDay() : null;
            
            // Get jamaah count including family members
            $totalJamaah = $keberangkatan->total_jamaah ?? 0;
            $bookings = $keberangkatan->jamaahBookings ?? collect();
            $adultCount = 0; $childCount = 0; $infantCount = 0;
            foreach ($bookings as $booking) {
                $adultCount++;
                $familyMembers = $booking->family_members_booking;
                if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true);
                if (!is_array($familyMembers)) $familyMembers = [];
                foreach ($familyMembers as $fm) {
                    if (!empty($fm['tanggal_lahir'])) {
                        $age = \Carbon\Carbon::parse($fm['tanggal_lahir'])->age;
                        if ($age < 2) $infantCount++;
                        elseif ($age <= 12) $childCount++;
                        else $adultCount++;
                    } else { $adultCount++; }
                }
            }
            if ($bookings->count() == 0 && $totalJamaah > 0) $adultCount = $totalJamaah;
            
            $tourLeader = $package->ustadz_name ?? '-';
            $groupName = $departureDate ? $departureDate->format('d') . ' ' . strtoupper($departureDate->translatedFormat('F')) . ' ' . $departureDate->format('Y') : '-';
            
            // Default rawdah
            $rawdahDateStr = $rawdahDate ? $rawdahDate->format('d') . ' ' . strtoupper($rawdahDate->translatedFormat('F')) . ' ' . $rawdahDate->format('Y') : '';
            $rawdahRows = [
                ['activity' => 'RAWDAH FOR WOMEN', 'date' => $rawdahDateStr, 'time' => '07.00 WAS'],
                ['activity' => 'RAWDAH FOR MEN', 'date' => $rawdahDateStr, 'time' => '17.00 WAS'],
                ['activity' => 'UMRAH', 'date' => '', 'time' => ''],
            ];
            
            // Default itinerary
            $itineraryRows = [];
            $flightDep = $package ? $package->flightDeparture : null;
            if ($flightDep) {
                $itineraryRows[] = [
                    'from' => strtoupper($flightDep->arrival_airport ?? '') . ' AIRPORT',
                    'to' => ($package->hotelMadinah ? strtoupper($package->hotelMadinah->hotel_name) : 'HOTEL MADINAH'),
                    'date' => $departureDate ? $departureDate->format('d') . ' ' . strtoupper($departureDate->translatedFormat('F')) . ' ' . $departureDate->format('Y') : '',
                    'time' => $flightDep->arrival_time ? $flightDep->arrival_time->format('H.i') . ' WAS' : '',
                    'remark' => 'Landing ' . ($flightDep->arrival_time ? $flightDep->arrival_time->format('H.i') : '') . ' BAWA KOPER',
                ];
            }
        }
        
        // Flight info
        $flightDeparture = $package ? $package->flightDeparture : null;
        $flightReturn = $package ? $package->flightReturn : null;
        
        // Hotel info
        $hotelMadinah = $package ? $package->hotelMadinah : null;
        $hotelMakkah = $package ? $package->hotelMakkah : null;
        
        // Get other hotels from package hotels JSON
        $otherHotels = [];
        if ($package && $package->hotels) {
            $hotelsData = is_string($package->hotels) ? json_decode($package->hotels, true) : $package->hotels;
            if (is_array($hotelsData)) {
                foreach ($hotelsData as $h) {
                    $otherHotels[] = $h;
                }
            }
        }
        
        // Calculate nights for hotels
        $madinahCheckIn = $package ? $package->madinah_check_in : null;
        $madinahCheckOut = $package ? $package->madinah_check_out : null;
        $makkahCheckIn = $package ? $package->makkah_check_in : null;
        $makkahCheckOut = $package ? $package->makkah_check_out : null;
        
        $madinahNights = ($madinahCheckIn && $madinahCheckOut) ? $madinahCheckIn->diffInDays($madinahCheckOut) : 0;
        $makkahNights = ($makkahCheckIn && $makkahCheckOut) ? $makkahCheckIn->diffInDays($makkahCheckOut) : 0;
        
        // Room count calculation from jamaah bookings (price_variant = quad/triple/double)
        // Each booking = 1 room of that type (shared by the group in that room)
        // We count how many rooms of each type are needed
        $roomCounts = ['sgl' => 0, 'dbl' => 0, 'trpl' => 0, 'quad' => 0, 'quint' => 0, 'total' => 0];
        $bookingsForRooms = $keberangkatan->jamaahBookings ?? collect();
        
        foreach ($bookingsForRooms as $booking) {
            $variant = strtolower($booking->price_variant ?? $booking->room_type ?? 'double');
            if (str_contains($variant, 'single') || str_contains($variant, 'sgl')) $roomCounts['sgl']++;
            elseif (str_contains($variant, 'double') || str_contains($variant, 'dbl')) $roomCounts['dbl']++;
            elseif (str_contains($variant, 'triple') || str_contains($variant, 'trpl')) $roomCounts['trpl']++;
            elseif (str_contains($variant, 'quad')) $roomCounts['quad']++;
            elseif (str_contains($variant, 'quint')) $roomCounts['quint']++;
            else $roomCounts['dbl']++; // default to double
        }
        
        // Calculate actual room count (group jamaah by room type capacity)
        // quad = 4 per room, triple = 3 per room, double = 2 per room, single = 1 per room
        $madinahRoomCounts = [
            'sgl' => $roomCounts['sgl'],
            'dbl' => (int) ceil($roomCounts['dbl'] / 2),
            'trpl' => (int) ceil($roomCounts['trpl'] / 3),
            'quad' => (int) ceil($roomCounts['quad'] / 4),
            'quint' => (int) ceil($roomCounts['quint'] / 5),
            'total' => 0
        ];
        $madinahRoomCounts['total'] = $madinahRoomCounts['sgl'] + $madinahRoomCounts['dbl'] + $madinahRoomCounts['trpl'] + $madinahRoomCounts['quad'] + $madinahRoomCounts['quint'];
        
        // Same room allocation applies to Makkah (same jamaah, same room types)
        $makkahRoomCounts = $madinahRoomCounts;
    ?>

    <!-- TITLE -->
    <h2>INFORMATION PACKAGE <?php echo e($companyName); ?></h2>
    <h3>GROUP <?php echo e($departureDate ? $departureDate->format('d') : ''); ?> <?php echo e($departureDate ? strtoupper($departureDate->translatedFormat('F')) : ''); ?> <?php echo e($departureDate ? $departureDate->format('Y') : ''); ?></h3>

    <!-- HEADER INFO -->
    <table class="data" style="margin-bottom: 15px;">
        <tr>
            <td style="width: 15%;"><strong>NAME GROUP</strong></td>
            <td style="width: 35%;"><?php echo e($groupName); ?></td>
            <td style="width: 15%;"><strong>NO. OF PAX</strong></td>
            <td style="width: 35%;"><strong>TOUR LEADER NAME</strong></td>
        </tr>
        <tr>
            <td><?php echo e($groupName); ?></td>
            <td><strong>ADULT</strong> <?php echo e($adultCount); ?> &nbsp;&nbsp;&nbsp; <strong>CHILD</strong> <?php echo e($childCount); ?> &nbsp;&nbsp;&nbsp; <strong>INFANT</strong> <?php echo e($infantCount); ?></td>
            <td colspan="2"><?php echo e($tourLeader); ?></td>
        </tr>
    </table>

    <!-- FLIGHT SCHEDULE -->
    <div class="section-title">FLIGHT SCHEDULE</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 14%;">CARRIER</th>
                <th style="width: 10%;">FROM</th>
                <th style="width: 10%;">TO</th>
                <th style="width: 18%;">DATE</th>
                <th style="width: 16%;">ETD</th>
                <th style="width: 16%;">ETA</th>
                <th style="width: 16%;">REMARK</th>
            </tr>
        </thead>
        <tbody>
            <?php if($flightDeparture): ?>
            <tr>
                <td class="text-center"><?php echo e($flightDeparture->flight_number); ?></td>
                <td class="text-center"><?php echo e($flightDeparture->departure_airport); ?></td>
                <td class="text-center"><?php echo e($flightDeparture->arrival_airport); ?></td>
                <td class="text-center">
                    <?php if($package->departure_datetime): ?>
                        <?php echo e($package->departure_datetime->format('d M Y')); ?>

                    <?php elseif($departureDate): ?>
                        <?php echo e($departureDate->format('d M Y')); ?>

                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($package->departure_datetime): ?>
                        <?php echo e($package->departure_datetime->format('H.i')); ?> WIB
                    <?php elseif($flightDeparture->departure_time): ?>
                        <?php echo e($flightDeparture->departure_time->format('H.i')); ?> WIB
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($flightDeparture->arrival_time): ?>
                        <?php echo e($flightDeparture->arrival_time->format('H.i')); ?> WAS
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($flightDeparture->arrival_time): ?>
                        Landing <?php echo e($flightDeparture->arrival_time->format('H.i')); ?> WAS
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php if($flightReturn): ?>
            <tr>
                <td class="text-center"><?php echo e($flightReturn->flight_number); ?></td>
                <td class="text-center"><?php echo e($flightReturn->departure_airport); ?></td>
                <td class="text-center"><?php echo e($flightReturn->arrival_airport); ?></td>
                <td class="text-center">
                    <?php if($package->return_datetime): ?>
                        <?php echo e($package->return_datetime->format('d M Y')); ?>

                    <?php elseif($returnDate): ?>
                        <?php echo e($returnDate->format('d M Y')); ?>

                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($package->return_datetime): ?>
                        <?php echo e($package->return_datetime->format('H.i')); ?> WAS
                    <?php elseif($flightReturn->departure_time): ?>
                        <?php echo e($flightReturn->departure_time->format('H.i')); ?> WAS
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($flightReturn->arrival_time): ?>
                        <?php echo e($flightReturn->arrival_time->format('H.i')); ?> WIB
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($flightReturn->arrival_time): ?>
                        Landing <?php echo e($flightReturn->arrival_time->format('H.i')); ?> WIB
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php if(!$flightDeparture && !$flightReturn): ?>
            <tr>
                <td colspan="7" class="text-center" style="color: #999; padding: 10px;">Belum ada data penerbangan</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- ACCOMMODATION -->
    <div class="section-title">ACCOMMODATION</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 7%;">CITY</th>
                <th style="width: 20%;">HOTEL</th>
                <th style="width: 10%;">CHECK IN</th>
                <th style="width: 10%;">CHECK OUT</th>
                <th style="width: 8%;">TOTAL NIGHTS</th>
                <th colspan="5" style="text-align: center;">ROOM TYPE</th>
                <th style="width: 8%;">TOTAL ROOM</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="width: 7%;">SGL</th>
                <th style="width: 7%;">DBL</th>
                <th style="width: 7%;">TRPL</th>
                <th style="width: 7%;">QUAD</th>
                <th style="width: 7%;">QUINT</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if($hotelMadinah): ?>
            <tr>
                <td class="text-center">MED</td>
                <td><?php echo e(strtoupper($hotelMadinah->hotel_name)); ?></td>
                <td class="text-center"><?php echo e($madinahCheckIn ? $madinahCheckIn->format('d M') : '-'); ?></td>
                <td class="text-center"><?php echo e($madinahCheckOut ? $madinahCheckOut->format('d M') : '-'); ?></td>
                <td class="text-center"><?php echo e($madinahNights ?: ''); ?></td>
                <td class="text-center"><?php echo e($madinahRoomCounts['sgl'] ?: ''); ?></td>
                <td class="text-center"><?php echo e($madinahRoomCounts['dbl'] ?: ''); ?></td>
                <td class="text-center"><?php echo e($madinahRoomCounts['trpl'] ?: ''); ?></td>
                <td class="text-center"><?php echo e($madinahRoomCounts['quad'] ?: ''); ?></td>
                <td class="text-center"><?php echo e($madinahRoomCounts['quint'] ?: ''); ?></td>
                <td class="text-center"><?php echo e($madinahRoomCounts['total'] ?: ''); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($hotelMakkah): ?>
            <tr>
                <td class="text-center">MEK</td>
                <td><?php echo e(strtoupper($hotelMakkah->hotel_name)); ?></td>
                <td class="text-center"><?php echo e($makkahCheckIn ? $makkahCheckIn->format('d M') : '-'); ?></td>
                <td class="text-center"><?php echo e($makkahCheckOut ? $makkahCheckOut->format('d M') : '-'); ?></td>
                <td class="text-center"><?php echo e($makkahNights ?: ''); ?></td>
                <td class="text-center"><?php echo e($makkahRoomCounts['sgl'] ?: ''); ?></td>
                <td class="text-center"><?php echo e($makkahRoomCounts['dbl'] ?: ''); ?></td>
                <td class="text-center"><?php echo e($makkahRoomCounts['trpl'] ?: ''); ?></td>
                <td class="text-center"><?php echo e($makkahRoomCounts['quad'] ?: ''); ?></td>
                <td class="text-center"><?php echo e($makkahRoomCounts['quint'] ?: ''); ?></td>
                <td class="text-center"><?php echo e($makkahRoomCounts['total'] ?: ''); ?></td>
            </tr>
            <?php endif; ?>
            <?php $__currentLoopData = $otherHotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $otherHotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e(strtoupper(substr($otherHotel['city'] ?? '-', 0, 3))); ?></td>
                <td><?php echo e(strtoupper($otherHotel['hotel_name'] ?? '-')); ?></td>
                <td class="text-center"><?php echo e(isset($otherHotel['check_in']) ? \Carbon\Carbon::parse($otherHotel['check_in'])->format('d M') : '-'); ?></td>
                <td class="text-center"><?php echo e(isset($otherHotel['check_out']) ? \Carbon\Carbon::parse($otherHotel['check_out'])->format('d M') : '-'); ?></td>
                <td class="text-center">
                    <?php if(isset($otherHotel['check_in']) && isset($otherHotel['check_out'])): ?>
                        <?php echo e(\Carbon\Carbon::parse($otherHotel['check_in'])->diffInDays(\Carbon\Carbon::parse($otherHotel['check_out']))); ?>

                    <?php endif; ?>
                </td>
                <td class="text-center"></td>
                <td class="text-center"><?php echo e($otherHotel['dbl'] ?? ''); ?></td>
                <td class="text-center"><?php echo e($otherHotel['trpl'] ?? ''); ?></td>
                <td class="text-center"><?php echo e($otherHotel['quad'] ?? ''); ?></td>
                <td class="text-center"><?php echo e($otherHotel['quint'] ?? ''); ?></td>
                <td class="text-center"><?php echo e($otherHotel['total_rooms'] ?? ''); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(!$hotelMadinah && !$hotelMakkah && count($otherHotels) == 0): ?>
            <tr>
                <td colspan="11" class="text-center" style="color: #999; padding: 10px;">Belum ada data akomodasi</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- RAWDAH & UMRAH SCHEDULE -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">NO</th>
                <th style="width: 37%;">ACTIVITY</th>
                <th style="width: 30%;">DATE</th>
                <th style="width: 25%;">TIME</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $rawdahRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($idx + 1); ?></td>
                <td><?php echo e($row['activity'] ?? ''); ?></td>
                <td class="text-center"><?php echo e($row['date'] ?? ''); ?></td>
                <td class="text-center"><?php echo e($row['time'] ?? ''); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <!-- ITINERARY -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 6%;">NO.</th>
                <th style="width: 18%;">FROM</th>
                <th style="width: 20%;">TO</th>
                <th style="width: 18%;">DATE</th>
                <th style="width: 13%;">TIME</th>
                <th style="width: 25%;">REMARK</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($itineraryRows) > 0): ?>
                <?php $__currentLoopData = $itineraryRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $from = $row['from'] ?? '';
                    $to = $row['to'] ?? '';
                    $isMerged = (empty($from) && !empty($to)) || (!empty($from) && empty($to) && str_contains(strtoupper($from), 'CITY TOUR'));
                ?>
                <tr>
                    <td class="text-center"><?php echo e($idx + 1); ?></td>
                    <?php if(empty($from) && empty($to)): ?>
                        <td colspan="2" class="text-center">-</td>
                    <?php elseif(empty($from) && !empty($to)): ?>
                        <td colspan="2" class="text-center"><?php echo e(strtoupper($to)); ?></td>
                    <?php else: ?>
                        <td><?php echo e(strtoupper($from)); ?></td>
                        <td><?php echo e(strtoupper($to)); ?></td>
                    <?php endif; ?>
                    <td class="text-center"><?php echo e($row['date'] ?? ''); ?></td>
                    <td class="text-center"><?php echo e($row['time'] ?? ''); ?></td>
                    <td><?php echo e($row['remark'] ?? ''); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center" style="color: #999; padding: 10px;">
                        Belum ada data itinerary. Silakan isi melalui form penyesuaian info paket.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Footer -->
    <div style="margin-top: 20px; text-align: center; font-size: 8px; color: #666; border-top: 1px solid #ccc; padding-top: 8px;">
        <p>Dokumen ini digenerate secara otomatis pada <?php echo e(now()->format('d F Y H:i:s')); ?> WIB</p>
        <p><?php echo e($companyName); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/package/info-paket-pdf.blade.php ENDPATH**/ ?>