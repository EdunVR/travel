<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Information Package - {{ $keberangkatan->keberangkatan_name }}</title>
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
    @php
        $package = $keberangkatan->travelPackage;
        $companyName = 'HM TOUR & TRAVEL';
        if ($package && $package->outlet) {
            $companyName = strtoupper($package->outlet->nama_outlet ?? 'HM TOUR & TRAVEL');
        }
        
        $departureDate = $keberangkatan->departure_date;
        $returnDate = $keberangkatan->return_date;
        
        // Rawdah date = H+1 dari tanggal keberangkatan
        $rawdahDate = $departureDate ? $departureDate->copy()->addDay() : null;
        
        // Get jamaah count including family members
        $totalJamaah = $keberangkatan->total_jamaah ?? 0;
        $bookings = $keberangkatan->jamaahBookings ?? collect();
        
        // Count total pax including family members from each booking
        $adultCount = 0;
        $childCount = 0;
        $infantCount = 0;
        
        foreach ($bookings as $booking) {
            // Main jamaah = 1 adult
            $adultCount++;
            
            // Family members from booking
            $familyMembers = $booking->family_members_booking;
            if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true);
            if (!is_array($familyMembers)) $familyMembers = [];
            
            foreach ($familyMembers as $fm) {
                if (!empty($fm['tanggal_lahir'])) {
                    $age = \Carbon\Carbon::parse($fm['tanggal_lahir'])->age;
                    if ($age < 2) {
                        $infantCount++;
                    } elseif ($age <= 12) {
                        $childCount++;
                    } else {
                        $adultCount++;
                    }
                } else {
                    // No birth date, assume adult
                    $adultCount++;
                }
            }
        }
        
        // Fallback if no bookings
        if ($bookings->count() == 0 && $totalJamaah > 0) {
            $adultCount = $totalJamaah;
        }
        
        // Tour Leader
        $tourLeader = $package->ustadz_name ?? '-';
        
        // Flight info
        $flightDeparture = $package ? $package->flightDeparture : null;
        $flightReturn = $package ? $package->flightReturn : null;
        
        // Hotel info
        $hotelMadinah = $package ? $package->hotelMadinah : null;
        $hotelMakkah = $package ? $package->hotelMakkah : null;
        
        // Hotel bookings for this keberangkatan
        $hotelBookings = $keberangkatan->hotelBookings ?? collect();
        
        // Room type counts from hotel bookings or package data
        // We'll calculate room types from bookings
        $madinahRooms = $hotelBookings->filter(function($hb) use ($hotelMadinah) {
            return $hotelMadinah && $hb->id_hotel == $hotelMadinah->id;
        });
        $makkahRooms = $hotelBookings->filter(function($hb) use ($hotelMakkah) {
            return $hotelMakkah && $hb->id_hotel == $hotelMakkah->id;
        });
        
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
        
        // Tour Plans
        $tourPlans = collect();
        if ($package) {
            $tourPlans = $package->tourPlans()->with('activities')->orderBy('day_number')->get();
        }
        
        // Calculate nights for hotels
        $madinahCheckIn = $package ? $package->madinah_check_in : null;
        $madinahCheckOut = $package ? $package->madinah_check_out : null;
        $makkahCheckIn = $package ? $package->makkah_check_in : null;
        $makkahCheckOut = $package ? $package->makkah_check_out : null;
        
        $madinahNights = ($madinahCheckIn && $madinahCheckOut) ? $madinahCheckIn->diffInDays($madinahCheckOut) : 0;
        $makkahNights = ($makkahCheckIn && $makkahCheckOut) ? $makkahCheckIn->diffInDays($makkahCheckOut) : 0;
        
        // Room count calculation from hotel bookings
        $madinahRoomCounts = ['sgl' => 0, 'dbl' => 0, 'trpl' => 0, 'quad' => 0, 'quint' => 0, 'total' => 0];
        $makkahRoomCounts = ['sgl' => 0, 'dbl' => 0, 'trpl' => 0, 'quad' => 0, 'quint' => 0, 'total' => 0];
        
        foreach ($madinahRooms as $room) {
            $type = strtolower($room->room_type ?? '');
            $count = $room->room_count ?? 0;
            if (str_contains($type, 'single') || str_contains($type, 'sgl')) $madinahRoomCounts['sgl'] += $count;
            elseif (str_contains($type, 'double') || str_contains($type, 'dbl')) $madinahRoomCounts['dbl'] += $count;
            elseif (str_contains($type, 'triple') || str_contains($type, 'trpl')) $madinahRoomCounts['trpl'] += $count;
            elseif (str_contains($type, 'quad')) $madinahRoomCounts['quad'] += $count;
            elseif (str_contains($type, 'quint')) $madinahRoomCounts['quint'] += $count;
            $madinahRoomCounts['total'] += $count;
        }
        
        foreach ($makkahRooms as $room) {
            $type = strtolower($room->room_type ?? '');
            $count = $room->room_count ?? 0;
            if (str_contains($type, 'single') || str_contains($type, 'sgl')) $makkahRoomCounts['sgl'] += $count;
            elseif (str_contains($type, 'double') || str_contains($type, 'dbl')) $makkahRoomCounts['dbl'] += $count;
            elseif (str_contains($type, 'triple') || str_contains($type, 'trpl')) $makkahRoomCounts['trpl'] += $count;
            elseif (str_contains($type, 'quad')) $makkahRoomCounts['quad'] += $count;
            elseif (str_contains($type, 'quint')) $makkahRoomCounts['quint'] += $count;
            $makkahRoomCounts['total'] += $count;
        }
    @endphp

    <!-- TITLE -->
    <h2>INFORMATION PACKAGE {{ $companyName }}</h2>
    <h3>GROUP {{ $departureDate ? $departureDate->format('d') : '' }} {{ $departureDate ? strtoupper($departureDate->translatedFormat('F')) : '' }} {{ $departureDate ? $departureDate->format('Y') : '' }}</h3>

    <!-- HEADER INFO -->
    <table class="data" style="margin-bottom: 15px;">
        <tr>
            <td style="width: 15%;"><strong>NAME GROUP</strong></td>
            <td style="width: 35%;">{{ $departureDate ? $departureDate->format('d') . ' ' . strtoupper($departureDate->translatedFormat('F')) . ' ' . $departureDate->format('Y') : '-' }}</td>
            <td style="width: 15%;"><strong>NO. OF PAX</strong></td>
            <td style="width: 35%;"><strong>TOUR LEADER NAME</strong></td>
        </tr>
        <tr>
            <td>{{ $departureDate ? $departureDate->format('d') . ' ' . strtoupper($departureDate->translatedFormat('F')) . ' ' . $departureDate->format('Y') : '-' }}</td>
            <td><strong>ADULT</strong> {{ $adultCount }} &nbsp;&nbsp;&nbsp; <strong>CHILD</strong> {{ $childCount }} &nbsp;&nbsp;&nbsp; <strong>INFANT</strong> {{ $infantCount }}</td>
            <td colspan="2">{{ $tourLeader }}</td>
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
            @if($flightDeparture)
            <tr>
                <td class="text-center">{{ $flightDeparture->flight_number }}</td>
                <td class="text-center">{{ $flightDeparture->departure_airport }}</td>
                <td class="text-center">{{ $flightDeparture->arrival_airport }}</td>
                <td class="text-center">
                    @if($package->departure_datetime)
                        {{ $package->departure_datetime->format('d M Y') }}
                    @elseif($departureDate)
                        {{ $departureDate->format('d M Y') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($package->departure_datetime)
                        {{ $package->departure_datetime->format('H.i') }} WIB
                    @elseif($flightDeparture->departure_time)
                        {{ $flightDeparture->departure_time->format('H.i') }} WIB
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($flightDeparture->arrival_time)
                        {{ $flightDeparture->arrival_time->format('H.i') }} WAS
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($flightDeparture->arrival_time)
                        Landing {{ $flightDeparture->arrival_time->format('H.i') }} WAS
                    @endif
                </td>
            </tr>
            @endif
            @if($flightReturn)
            <tr>
                <td class="text-center">{{ $flightReturn->flight_number }}</td>
                <td class="text-center">{{ $flightReturn->departure_airport }}</td>
                <td class="text-center">{{ $flightReturn->arrival_airport }}</td>
                <td class="text-center">
                    @if($package->return_datetime)
                        {{ $package->return_datetime->format('d M Y') }}
                    @elseif($returnDate)
                        {{ $returnDate->format('d M Y') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($package->return_datetime)
                        {{ $package->return_datetime->format('H.i') }} WAS
                    @elseif($flightReturn->departure_time)
                        {{ $flightReturn->departure_time->format('H.i') }} WAS
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($flightReturn->arrival_time)
                        {{ $flightReturn->arrival_time->format('H.i') }} WIB
                    @else
                        -
                    @endif
                </td>
                <td class="text-center">
                    @if($flightReturn->arrival_time)
                        Landing {{ $flightReturn->arrival_time->format('H.i') }} WIB
                    @endif
                </td>
            </tr>
            @endif
            @if(!$flightDeparture && !$flightReturn)
            <tr>
                <td colspan="7" class="text-center" style="color: #999; padding: 10px;">Belum ada data penerbangan</td>
            </tr>
            @endif
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
            @if($hotelMadinah)
            <tr>
                <td class="text-center">MED</td>
                <td>{{ strtoupper($hotelMadinah->hotel_name) }}</td>
                <td class="text-center">{{ $madinahCheckIn ? $madinahCheckIn->format('d M') : '-' }}</td>
                <td class="text-center">{{ $madinahCheckOut ? $madinahCheckOut->format('d M') : '-' }}</td>
                <td class="text-center">{{ $madinahNights ?: '' }}</td>
                <td class="text-center">{{ $madinahRoomCounts['sgl'] ?: '' }}</td>
                <td class="text-center">{{ $madinahRoomCounts['dbl'] ?: '' }}</td>
                <td class="text-center">{{ $madinahRoomCounts['trpl'] ?: '' }}</td>
                <td class="text-center">{{ $madinahRoomCounts['quad'] ?: '' }}</td>
                <td class="text-center">{{ $madinahRoomCounts['quint'] ?: '' }}</td>
                <td class="text-center">{{ $madinahRoomCounts['total'] ?: '' }}</td>
            </tr>
            @endif
            @if($hotelMakkah)
            <tr>
                <td class="text-center">MEK</td>
                <td>{{ strtoupper($hotelMakkah->hotel_name) }}</td>
                <td class="text-center">{{ $makkahCheckIn ? $makkahCheckIn->format('d M') : '-' }}</td>
                <td class="text-center">{{ $makkahCheckOut ? $makkahCheckOut->format('d M') : '-' }}</td>
                <td class="text-center">{{ $makkahNights ?: '' }}</td>
                <td class="text-center">{{ $makkahRoomCounts['sgl'] ?: '' }}</td>
                <td class="text-center">{{ $makkahRoomCounts['dbl'] ?: '' }}</td>
                <td class="text-center">{{ $makkahRoomCounts['trpl'] ?: '' }}</td>
                <td class="text-center">{{ $makkahRoomCounts['quad'] ?: '' }}</td>
                <td class="text-center">{{ $makkahRoomCounts['quint'] ?: '' }}</td>
                <td class="text-center">{{ $makkahRoomCounts['total'] ?: '' }}</td>
            </tr>
            @endif
            @foreach($otherHotels as $otherHotel)
            <tr>
                <td class="text-center">{{ strtoupper(substr($otherHotel['city'] ?? '-', 0, 3)) }}</td>
                <td>{{ strtoupper($otherHotel['hotel_name'] ?? '-') }}</td>
                <td class="text-center">{{ isset($otherHotel['check_in']) ? \Carbon\Carbon::parse($otherHotel['check_in'])->format('d M') : '-' }}</td>
                <td class="text-center">{{ isset($otherHotel['check_out']) ? \Carbon\Carbon::parse($otherHotel['check_out'])->format('d M') : '-' }}</td>
                <td class="text-center">
                    @if(isset($otherHotel['check_in']) && isset($otherHotel['check_out']))
                        {{ \Carbon\Carbon::parse($otherHotel['check_in'])->diffInDays(\Carbon\Carbon::parse($otherHotel['check_out'])) }}
                    @endif
                </td>
                <td class="text-center"></td>
                <td class="text-center">{{ $otherHotel['dbl'] ?? '' }}</td>
                <td class="text-center">{{ $otherHotel['trpl'] ?? '' }}</td>
                <td class="text-center">{{ $otherHotel['quad'] ?? '' }}</td>
                <td class="text-center">{{ $otherHotel['quint'] ?? '' }}</td>
                <td class="text-center">{{ $otherHotel['total_rooms'] ?? '' }}</td>
            </tr>
            @endforeach
            @if(!$hotelMadinah && !$hotelMakkah && count($otherHotels) == 0)
            <tr>
                <td colspan="11" class="text-center" style="color: #999; padding: 10px;">Belum ada data akomodasi</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- RAWDAH SCHEDULE -->
    <div class="section-title">RAWDAH & UMRAH SCHEDULE</div>
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
            <tr>
                <td class="text-center">1</td>
                <td>RAWDAH FOR WOMEN</td>
                <td class="text-center">{{ $rawdahDate ? $rawdahDate->format('d') . ' ' . strtoupper($rawdahDate->translatedFormat('F')) . ' ' . $rawdahDate->format('Y') : '-' }}</td>
                <td class="text-center">07.00 WAS</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>RAWDAH FOR MEN</td>
                <td class="text-center">{{ $rawdahDate ? $rawdahDate->format('d') . ' ' . strtoupper($rawdahDate->translatedFormat('F')) . ' ' . $rawdahDate->format('Y') : '-' }}</td>
                <td class="text-center">17.00 WAS</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>UMRAH</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
            </tr>
        </tbody>
    </table>

    <!-- TOUR PLAN / ITINERARY -->
    <div class="section-title">TOUR PLAN (ITINERARY)</div>
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
            @if($tourPlans->count() > 0)
                @php $itineraryNo = 1; @endphp
                @foreach($tourPlans as $plan)
                    @php
                        // Parse day_title for FROM/TO logic
                        $dayTitle = $plan->day_title ?? '';
                        $hasDash = str_contains($dayTitle, ' - ');
                        $fromText = '';
                        $toText = '';
                        
                        if ($hasDash) {
                            $parts = explode(' - ', $dayTitle, 2);
                            $fromText = strtoupper(trim($parts[0]));
                            $toText = strtoupper(trim($parts[1]));
                        } else {
                            // No dash: merge FROM & TO, prefix with "CITY TOUR "
                            $mergedText = 'CITY TOUR ' . strtoupper(trim($dayTitle));
                        }
                    @endphp
                    @if($plan->activities && $plan->activities->count() > 0)
                        @foreach($plan->activities as $actIdx => $activity)
                        <tr>
                            <td class="text-center">{{ $itineraryNo }}</td>
                            @if($hasDash)
                                <td>{{ $actIdx === 0 ? $fromText : '' }}</td>
                                <td>{{ $actIdx === 0 ? $toText : ($activity->activity_title ?? '') }}</td>
                            @else
                                <td colspan="2" class="text-center">{{ $actIdx === 0 ? $mergedText : ($activity->activity_title ?? '') }}</td>
                            @endif
                            <td class="text-center">
                                @if($plan->day_date)
                                    {{ \Carbon\Carbon::parse($plan->day_date)->format('d') }} {{ strtoupper(\Carbon\Carbon::parse($plan->day_date)->translatedFormat('F')) }}{{ \Carbon\Carbon::parse($plan->day_date)->format('Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($activity->activity_time)
                                    {{ $activity->activity_time }} WAS
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $activity->activity_description ?? '' }}</td>
                        </tr>
                        @php $itineraryNo++; @endphp
                        @endforeach
                    @else
                    <tr>
                        <td class="text-center">{{ $itineraryNo }}</td>
                        @if($hasDash)
                            <td>{{ $fromText }}</td>
                            <td>{{ $toText }}</td>
                        @else
                            <td colspan="2" class="text-center">{{ $mergedText }}</td>
                        @endif
                        <td class="text-center">
                            @if($plan->day_date)
                                {{ \Carbon\Carbon::parse($plan->day_date)->format('d') }} {{ strtoupper(\Carbon\Carbon::parse($plan->day_date)->translatedFormat('F')) }}{{ \Carbon\Carbon::parse($plan->day_date)->format('Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">-</td>
                        <td>{{ $plan->description ?? '' }}</td>
                    </tr>
                    @php $itineraryNo++; @endphp
                    @endif
                @endforeach
            @else
                {{-- Generate basic itinerary from flight and hotel data --}}
                @php $itineraryNo = 1; @endphp
                
                {{-- Day 1: Arrival --}}
                @if($flightDeparture)
                <tr>
                    <td class="text-center">{{ $itineraryNo }}</td>
                    <td>{{ $flightDeparture->arrival_airport }} AIRPORT</td>
                    <td>{{ $hotelMadinah ? strtoupper($hotelMadinah->hotel_name) : 'HOTEL MADINAH' }}</td>
                    <td class="text-center">
                        @if($departureDate)
                            {{ $departureDate->format('d') }} {{ strtoupper($departureDate->translatedFormat('F')) }} {{ $departureDate->format('Y') }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if($flightDeparture->arrival_time)
                            {{ $flightDeparture->arrival_time->format('H.i') }} WAS
                        @endif
                    </td>
                    <td>Landing {{ $flightDeparture->arrival_time ? $flightDeparture->arrival_time->format('H.i') : '' }} BAWA KOPER</td>
                </tr>
                @php $itineraryNo++; @endphp
                @endif
                
                @if(!$flightDeparture && !$flightReturn)
                <tr>
                    <td colspan="6" class="text-center" style="color: #999; padding: 10px;">
                        Belum ada data tour plan. Silakan tambahkan tour plan di tab Tour Plan pada halaman detail paket.
                    </td>
                </tr>
                @endif
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <div style="margin-top: 20px; text-align: center; font-size: 8px; color: #666; border-top: 1px solid #ccc; padding-top: 8px;">
        <p>Dokumen ini digenerate secara otomatis pada {{ now()->format('d F Y H:i:s') }} WIB</p>
        <p>{{ $companyName }}</p>
    </div>
</body>
</html>
