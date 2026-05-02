<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Roomlist - {{ $keberangkatan->keberangkatan_name }}</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        body { font-family: Arial, sans-serif; font-size: 8pt; line-height: 1.3; margin: 0; }
        .header { text-align: center; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 3px solid #4472C4; }
        .header h2 { margin: 2px 0; color: #4472C4; font-size: 12pt; }
        .header .sub { font-size: 8pt; color: #333; }
        table.roomlist { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.roomlist th, table.roomlist td { border: 1px solid #000; padding: 3px 4px; text-align: left; vertical-align: middle; }
        table.roomlist th { background-color: #4472C4; color: white; font-weight: bold; text-align: center; font-size: 7pt; }
        table.roomlist td { font-size: 7pt; }
        .hotel-madinah-header { background-color: #92D050 !important; color: #000 !important; }
        .hotel-makkah-header  { background-color: #F4B084 !important; color: #000 !important; }
        .hotel-madinah-cell   { background-color: #E2EFDA; }
        .hotel-makkah-cell    { background-color: #FCE4D6; }
        .room-type-double { background-color: #5B9BD5; color: white; }
        .room-type-triple { background-color: #FFFF00; color: #333; }
        .room-type-quad   { background-color: #00B050; color: white; }
        .room-position-cell { background-color: #F4B084; font-weight: bold; text-align: center; font-size: 6.5pt; }
        .room-no-cell { text-align: center; font-weight: bold; background-color: #E2EFDA; font-size: 6.5pt; }
        .family-row td { background-color: #FFF2CC !important; font-style: italic; }
        .text-center { text-align: center; }
        .footer-section { margin-top: 8px; font-size: 7pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ROOM LIST - {{ strtoupper($keberangkatan->keberangkatan_name) }}</h2>
        <div class="sub">
            Keberangkatan: {{ $keberangkatan->departure_date->format('d F Y') }}
            @if($keberangkatan->return_date) &nbsp;|&nbsp; Kepulangan: {{ $keberangkatan->return_date->format('d F Y') }} @endif
            &nbsp;|&nbsp; Paket: {{ $keberangkatan->travelPackage->package_name ?? '-' }}
        </div>
    </div>

    @php
        $roomAssignments = \App\Models\RoomAssignment::with(['jamaahBooking.hotelBookings.hotel'])
            ->where('id_keberangkatan', $keberangkatan->id)
            ->orderBy('city_type')->orderBy('room_number')->orderBy('sort_order')
            ->get();

        $useRoomAssignments = $roomAssignments->isNotEmpty();
        $rows = [];
        $no   = 1;

        if ($useRoomAssignments) {
            // Build map: booking_id → {madinah: {room_number, room_type, room_position}, makkah: {...}}
            $bookingRoomMap = [];
            foreach ($roomAssignments as $ra) {
                $bid = $ra->id_jamaah_booking;
                $ct  = $ra->city_type;
                if (!isset($bookingRoomMap[$bid][$ct])) {
                    $bookingRoomMap[$bid][$ct] = [
                        'room_number'   => $ra->room_number,
                        'room_type'     => $ra->room_type,
                        'room_position' => $ra->room_position ?? '',
                    ];
                }
            }

            // One row per unique person (deduplicate by booking+person_type+family_index)
            $seen = [];
            foreach ($roomAssignments->sortBy('sort_order') as $ra) {
                $pk = $ra->id_jamaah_booking . '_' . $ra->person_type . '_' . $ra->family_index;
                if (isset($seen[$pk])) continue;
                $seen[$pk] = true;

                $bk        = $ra->jamaahBooking;
                $hbMadinah = $bk ? $bk->hotelBookings->where('city_type', 'madinah')->first() : null;
                $hbMakkah  = $bk ? $bk->hotelBookings->where('city_type', 'makkah')->first()  : null;

                $madInfo = $bookingRoomMap[$ra->id_jamaah_booking]['madinah'] ?? null;
                $makInfo = $bookingRoomMap[$ra->id_jamaah_booking]['makkah']  ?? null;

                $rows[] = [
                    'type'           => $ra->person_type,
                    'no'             => $ra->person_type === 'jamaah' ? $no++ : '',
                    'name'           => $ra->person_name,
                    'booking_code'   => $bk->booking_code ?? '-',
                    'status'         => $ra->person_type === 'family' ? 'Keluarga' : ucfirst($bk->status ?? 'confirmed'),
                    'room_type'      => strtolower($ra->room_type ?? 'double'),
                    'room_position'  => $ra->room_position ?? '',
                    'room_no_madinah'=> $madInfo ? $madInfo['room_number'] : '-',
                    'room_no_makkah' => $makInfo ? $makInfo['room_number'] : '-',
                    'madinah_hotel'  => $hbMadinah ? ($hbMadinah->hotel->hotel_name ?? '-') : ($keberangkatan->travelPackage->hotelMadinah->hotel_name ?? '-'),
                    'madinah_ci'     => $hbMadinah && $hbMadinah->check_in_date  ? $hbMadinah->check_in_date->format('d/m')  : '-',
                    'madinah_co'     => $hbMadinah && $hbMadinah->check_out_date ? $hbMadinah->check_out_date->format('d/m') : '-',
                    'makkah_hotel'   => $hbMakkah  ? ($hbMakkah->hotel->hotel_name ?? '-')  : ($keberangkatan->travelPackage->hotelMakkah->hotel_name ?? '-'),
                    'makkah_ci'      => $hbMakkah  && $hbMakkah->check_in_date   ? $hbMakkah->check_in_date->format('d/m')   : '-',
                    'makkah_co'      => $hbMakkah  && $hbMakkah->check_out_date  ? $hbMakkah->check_out_date->format('d/m')  : '-',
                ];
            }
        } else {
            // Fallback: from jamaah_hotel_bookings
            foreach ($jamaahBookings as $booking) {
                $jamaah = $booking->jamaah;
                if (!$jamaah) continue;
                $fm = $jamaah->family_members ?? [];
                if (is_string($fm)) $fm = json_decode($fm, true);
                if (!is_array($fm)) $fm = [];

                $hbMadinah = $booking->hotelBookings->where('city_type', 'madinah')->first();
                $hbMakkah  = $booking->hotelBookings->where('city_type', 'makkah')->first();
                $roomType  = strtolower($hbMakkah->room_type ?? $hbMadinah->room_type ?? $booking->room_type ?? 'double');
                $roomPos   = $hbMakkah->notes ?? $hbMadinah->notes ?? '';

                $baseRow = [
                    'type'           => 'jamaah',
                    'no'             => $no++,
                    'name'           => $jamaah->nama ?? '-',
                    'booking_code'   => $booking->booking_code,
                    'status'         => ucfirst($booking->status),
                    'room_type'      => $roomType,
                    'room_position'  => $roomPos,
                    'room_no_madinah'=> '-',
                    'room_no_makkah' => '-',
                    'madinah_hotel'  => $hbMadinah ? ($hbMadinah->hotel->hotel_name ?? '-') : ($keberangkatan->travelPackage->hotelMadinah->hotel_name ?? '-'),
                    'madinah_ci'     => $hbMadinah && $hbMadinah->check_in_date  ? $hbMadinah->check_in_date->format('d/m')  : '-',
                    'madinah_co'     => $hbMadinah && $hbMadinah->check_out_date ? $hbMadinah->check_out_date->format('d/m') : '-',
                    'makkah_hotel'   => $hbMakkah  ? ($hbMakkah->hotel->hotel_name ?? '-')  : ($keberangkatan->travelPackage->hotelMakkah->hotel_name ?? '-'),
                    'makkah_ci'      => $hbMakkah  && $hbMakkah->check_in_date   ? $hbMakkah->check_in_date->format('d/m')   : '-',
                    'makkah_co'      => $hbMakkah  && $hbMakkah->check_out_date  ? $hbMakkah->check_out_date->format('d/m')  : '-',
                ];
                $rows[] = $baseRow;
                foreach ($fm as $member) {
                    $rows[] = array_merge($baseRow, [
                        'type'   => 'family',
                        'no'     => '',
                        'name'   => ($member['nama'] ?? '-') . ' (' . ($member['hubungan'] ?? 'Keluarga') . ')',
                        'status' => 'Keluarga',
                    ]);
                }
            }
        }

        // Build rowspan map: consecutive rows with same room_no_madinah+room_no_makkah+room_type+madinah_hotel+makkah_hotel get merged
        $rowspanMap = [];
        $i = 0;
        while ($i < count($rows)) {
            $key  = $rows[$i]['room_no_madinah'] . '|' . $rows[$i]['room_no_makkah'] . '|' . $rows[$i]['room_type']
                  . '|' . $rows[$i]['madinah_hotel'] . '|' . $rows[$i]['makkah_hotel'];
            $span = 1;
            $j    = $i + 1;
            while ($j < count($rows)) {
                $keyNext = $rows[$j]['room_no_madinah'] . '|' . $rows[$j]['room_no_makkah'] . '|' . $rows[$j]['room_type']
                         . '|' . $rows[$j]['madinah_hotel'] . '|' . $rows[$j]['makkah_hotel'];
                if ($keyNext === $key) { $span++; $j++; } else break;
            }
            $rowspanMap[$i] = $span;
            $i = $j;
        }

        // Per-hotel summary
        $summaryByHotel = ['madinah' => [], 'makkah' => []];
        if ($useRoomAssignments) {
            foreach ($roomAssignments->groupBy('city_type') as $ct => $ctA) {
                $ck = in_array($ct, ['madinah','makkah']) ? $ct : 'makkah';
                foreach ($ctA->groupBy('room_number') as $rn => $rp) {
                    $rt = strtolower($rp->first()->room_type ?? 'double');
                    $sampleBk = $rp->first()->jamaahBooking;
                    $hb = $sampleBk ? $sampleBk->hotelBookings->where('city_type', $ct)->first() : null;
                    $hn = $hb ? ($hb->hotel->hotel_name ?? 'Hotel') : ($ct === 'makkah' ? ($keberangkatan->travelPackage->hotelMakkah->hotel_name ?? 'Hotel Makkah') : ($keberangkatan->travelPackage->hotelMadinah->hotel_name ?? 'Hotel Madinah'));
                    if (!isset($summaryByHotel[$ck][$hn][$rt])) $summaryByHotel[$ck][$hn][$rt] = ['rooms'=>0,'persons'=>0];
                    $summaryByHotel[$ck][$hn][$rt]['rooms']++;
                    $summaryByHotel[$ck][$hn][$rt]['persons'] += $rp->count();
                }
            }
        } else {
            foreach ($jamaahBookings as $bk) {
                $j = $bk->jamaah; if (!$j) continue;
                $fm = $j->family_members ?? []; if (is_string($fm)) $fm = json_decode($fm, true);
                $total = 1 + (is_array($fm) ? count($fm) : 0);
                foreach (['makkah','madinah'] as $ct) {
                    $hb = $bk->hotelBookings->where('city_type', $ct)->first();
                    $rt = strtolower($hb->room_type ?? $bk->room_type ?? 'double');
                    $hn = $hb ? ($hb->hotel->hotel_name ?? 'Hotel') : ($ct === 'makkah' ? ($keberangkatan->travelPackage->hotelMakkah->hotel_name ?? 'Hotel Makkah') : ($keberangkatan->travelPackage->hotelMadinah->hotel_name ?? 'Hotel Madinah'));
                    if (!isset($summaryByHotel[$ct][$hn][$rt])) $summaryByHotel[$ct][$hn][$rt] = ['rooms'=>0,'persons'=>0];
                    $summaryByHotel[$ct][$hn][$rt]['persons'] += $total;
                }
            }
        }

        $totalJamaah = $jamaahBookings->count();
        $totalJiwa   = count($rows);
    @endphp

    {{-- SUMMARY: per hotel side by side --}}
    <table style="width:100%; border-collapse:collapse; margin-top:8px; font-size:7.5pt;">
        <tr>
            @foreach(['madinah' => ['label'=>'RINGKASAN HOTEL MADINAH','bg'=>'#92D050'], 'makkah' => ['label'=>'RINGKASAN HOTEL MAKKAH','bg'=>'#F4B084']] as $ct => $cfg)
            <td style="width:50%; {{ $ct === 'madinah' ? 'padding-right:4px;' : '' }} vertical-align:top;">
                <div style="border:1px solid #999;">
                    <div style="background:{{ $cfg['bg'] }};color:#000;font-weight:bold;padding:3px 6px;text-align:center;">{{ $cfg['label'] }}</div>
                    @if(!empty($summaryByHotel[$ct]))
                        @foreach($summaryByHotel[$ct] as $hotelName => $typeData)
                        <div style="background:#f8f8f8;padding:2px 5px;font-weight:bold;font-size:7pt;border-bottom:1px solid #ddd;">{{ $hotelName }}</div>
                        <table style="width:100%;border-collapse:collapse;font-size:7pt;">
                            <tr>
                                <th style="border:1px solid #ddd;padding:2px 4px;background:#eee;">Tipe</th>
                                <th style="border:1px solid #ddd;padding:2px 4px;background:#eee;">Kap.</th>
                                <th style="border:1px solid #ddd;padding:2px 4px;background:#eee;">Kamar</th>
                                <th style="border:1px solid #ddd;padding:2px 4px;background:#eee;">Jiwa</th>
                            </tr>
                            @foreach($typeData as $type => $info)
                            @php $cap=match($type){'single'=>1,'double'=>2,'triple'=>3,'quad'=>4,default=>2}; $bgS=match($type){'double'=>'background:#5B9BD5;color:white;','triple'=>'background:#FFFF00;color:#333;','quad'=>'background:#00B050;color:white;',default=>''}; @endphp
                            <tr>
                                <td style="border:1px solid #ddd;padding:2px 4px;{{ $bgS }}font-weight:bold;text-align:center;">{{ strtoupper($type) }}</td>
                                <td style="border:1px solid #ddd;padding:2px 4px;text-align:center;">{{ $cap }}</td>
                                <td style="border:1px solid #ddd;padding:2px 4px;text-align:center;">{{ $info['rooms'] ?? '-' }}</td>
                                <td style="border:1px solid #ddd;padding:2px 4px;text-align:center;">{{ $info['persons'] }}</td>
                            </tr>
                            @endforeach
                            <tr style="background:#f0f4ff;font-weight:bold;">
                                <td style="border:1px solid #ddd;padding:2px 4px;" colspan="2">Sub Total</td>
                                <td style="border:1px solid #ddd;padding:2px 4px;text-align:center;">{{ array_sum(array_column($typeData,'rooms')) }}</td>
                                <td style="border:1px solid #ddd;padding:2px 4px;text-align:center;">{{ array_sum(array_column($typeData,'persons')) }}</td>
                            </tr>
                        </table>
                        @endforeach
                        @php
                            $ctTotalRooms   = array_sum(array_map(fn($h)=>array_sum(array_column($h,'rooms')), $summaryByHotel[$ct]));
                            $ctTotalPersons = array_sum(array_map(fn($h)=>array_sum(array_column($h,'persons')), $summaryByHotel[$ct]));
                        @endphp
                        <div style="background:#4472C4;color:white;font-weight:bold;padding:2px 5px;font-size:7pt;text-align:right;">
                            TOTAL: {{ $ctTotalRooms }} kamar &nbsp;|&nbsp; {{ $ctTotalPersons }} jiwa
                        </div>
                    @else
                        <div style="padding:5px;text-align:center;color:#888;font-size:7pt;">-</div>
                    @endif
                </div>
            </td>
            @endforeach
        </tr>
    </table>

    {{-- MAIN TABLE: single unified table with room_no_madinah + room_no_makkah columns, merged rows --}}
    @if(!empty($rows))
    @php
        // Pre-compute: for each row, is it the first in its merge group?
        $firstInGroup = [];
        $groupSpan    = [];
        $i = 0;
        while ($i < count($rows)) {
            $span = $rowspanMap[$i] ?? 1;
            $firstInGroup[$i] = true;
            $groupSpan[$i]    = $span;
            for ($k = $i + 1; $k < $i + $span; $k++) {
                $firstInGroup[$k] = false;
                $groupSpan[$k]    = 0;
            }
            $i += $span;
        }
    @endphp
    <table class="roomlist" style="margin-top:10px;">
        <thead>
            <tr>
                <th rowspan="2" style="width:70px;">ROOM POSITION</th>
                <th rowspan="2" style="width:22px;">NO</th>
                <th rowspan="2" style="width:130px;">FULL NAME</th>
                <th rowspan="2" style="width:60px;">KODE BOOKING</th>
                <th rowspan="2" style="width:45px;">STATUS</th>
                <th rowspan="2" style="width:48px;">ROOM TYPE</th>
                <th colspan="4" class="hotel-madinah-header">HOTEL MADINAH</th>
                <th colspan="4" class="hotel-makkah-header">HOTEL MAKKAH</th>
            </tr>
            <tr>
                <th class="hotel-madinah-header" style="width:38px;">NO. KMR</th>
                <th class="hotel-madinah-header" style="width:75px;">NAMA HOTEL</th>
                <th class="hotel-madinah-header" style="width:35px;">CHECK IN</th>
                <th class="hotel-madinah-header" style="width:35px;">CHECK OUT</th>
                <th class="hotel-makkah-header" style="width:38px;">NO. KMR</th>
                <th class="hotel-makkah-header" style="width:75px;">NAMA HOTEL</th>
                <th class="hotel-makkah-header" style="width:35px;">CHECK IN</th>
                <th class="hotel-makkah-header" style="width:35px;">CHECK OUT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $idx => $row)
            @php $isFirst = $firstInGroup[$idx] ?? true; $span = $groupSpan[$idx] ?? 1; @endphp
            <tr @if($row['type'] === 'family') class="family-row" @endif>
                {{-- Merged: room_position --}}
                @if($isFirst)
                <td class="room-position-cell" @if($span > 1) rowspan="{{ $span }}" @endif>{{ $row['room_position'] }}</td>
                @endif

                <td class="text-center">{{ $row['no'] }}</td>
                <td>
                    @if($row['type'] === 'family')
                        &nbsp;&nbsp;<em>+ {{ $row['name'] }}</em>
                    @else
                        <strong>{{ $row['name'] }}</strong>
                    @endif
                </td>
                <td class="text-center" style="font-size:6pt;">{{ $row['booking_code'] }}</td>
                <td class="text-center">{{ $row['status'] }}</td>
                <td class="text-center
                    @if(str_contains($row['room_type'],'double')) room-type-double
                    @elseif(str_contains($row['room_type'],'triple')) room-type-triple
                    @elseif(str_contains($row['room_type'],'quad')) room-type-quad
                    @endif">
                    {{ strtoupper($row['room_type']) }}
                </td>

                {{-- Merged: hotel info --}}
                @if($isFirst)
                <td class="hotel-madinah-cell room-no-cell" @if($span > 1) rowspan="{{ $span }}" @endif>{{ $row['room_no_madinah'] }}</td>
                <td class="hotel-madinah-cell text-center" @if($span > 1) rowspan="{{ $span }}" @endif>{{ $row['madinah_hotel'] }}</td>
                <td class="hotel-madinah-cell text-center" @if($span > 1) rowspan="{{ $span }}" @endif>{{ $row['madinah_ci'] }}</td>
                <td class="hotel-madinah-cell text-center" @if($span > 1) rowspan="{{ $span }}" @endif>{{ $row['madinah_co'] }}</td>
                <td class="hotel-makkah-cell room-no-cell" @if($span > 1) rowspan="{{ $span }}" @endif>{{ $row['room_no_makkah'] }}</td>
                <td class="hotel-makkah-cell text-center" @if($span > 1) rowspan="{{ $span }}" @endif>{{ $row['makkah_hotel'] }}</td>
                <td class="hotel-makkah-cell text-center" @if($span > 1) rowspan="{{ $span }}" @endif>{{ $row['makkah_ci'] }}</td>
                <td class="hotel-makkah-cell text-center" @if($span > 1) rowspan="{{ $span }}" @endif>{{ $row['makkah_co'] }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align:center;padding:15px;color:#666;margin-top:10px;border:1px solid #ccc;">Tidak ada data jamaah</div>
    @endif

    <div class="footer-section">
        <table style="width:100%;">
            <tr>
                <td style="width:60%;">
                    <strong>Keterangan:</strong>
                    <span style="background:#5B9BD5;color:white;padding:1px 4px;">DOUBLE</span> = 2 org &nbsp;
                    <span style="background:#FFFF00;color:#333;padding:1px 4px;">TRIPLE</span> = 3 org &nbsp;
                    <span style="background:#00B050;color:white;padding:1px 4px;">QUAD</span> = 4 org &nbsp;
                    <span style="background:#FFF2CC;color:#333;padding:1px 4px;border:1px solid #ccc;">Kuning</span> = Anggota Keluarga
                </td>
                <td style="width:40%; text-align:right; vertical-align:bottom;">
                    <small>Dicetak: {{ now()->format('d/m/Y H:i') }}</small>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
