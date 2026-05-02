<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Roomlist - {{ $keberangkatan->keberangkatan_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 5px 0;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-section table {
            width: 100%;
        }
        .info-section td {
            padding: 3px 5px;
        }
        table.roomlist {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.roomlist th,
        table.roomlist td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        table.roomlist th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .hotel-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .hotel-title {
            background-color: #e8e8e8;
            padding: 8px;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 10px;
            border-left: 4px solid #333;
        }
        .room-group {
            margin-bottom: 15px;
        }
        .room-header {
            background-color: #f5f5f5;
            padding: 5px 8px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .gender-male {
            color: #1976d2;
        }
        .gender-female {
            color: #c2185b;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>ROOMLIST HOTEL</h2>
        <h3>{{ $keberangkatan->keberangkatan_name }}</h3>
        <p>{{ $keberangkatan->travelPackage->package_name }}</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td width="150"><strong>Kode Keberangkatan:</strong></td>
                <td>{{ $keberangkatan->keberangkatan_code }}</td>
                <td width="150"><strong>Tanggal Keberangkatan:</strong></td>
                <td>{{ $keberangkatan->departure_date->format('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Kepulangan:</strong></td>
                <td>{{ $keberangkatan->return_date->format('d F Y') }}</td>
                <td><strong>Total Jamaah:</strong></td>
                <td>{{ $jamaahBookings->count() }} orang</td>
            </tr>
            <tr>
                <td><strong>Dibuat:</strong></td>
                <td colspan="3">{{ now()->format('d F Y H:i') }}</td>
            </tr>
        </table>
    </div>

    @if($hotelBookings->count() > 0)
        {{-- Jika ada hotel bookings dengan room assignments --}}
        @foreach($hotelBookings as $hotelBooking)
        <div class="hotel-section">
            <div class="hotel-title">
                {{ $hotelBooking->hotel->hotel_name ?? 'Hotel' }}
                <span style="font-size: 9pt; font-weight: normal;">
                    ({{ $hotelBooking->check_in_date ? $hotelBooking->check_in_date->format('d/m/Y') : '-' }} - 
                    {{ $hotelBooking->check_out_date ? $hotelBooking->check_out_date->format('d/m/Y') : '-' }})
                </span>
            </div>

            @php
                $roomAssignments = $hotelBooking->roomAssignments->groupBy('room_number');
            @endphp

            @forelse($roomAssignments as $roomNumber => $assignments)
            <div class="room-group">
                <div class="room-header">
                    Kamar {{ $roomNumber }} - {{ $assignments->first()->room_type ?? 'Standard' }}
                    ({{ $assignments->count() }} orang)
                </div>
                <table class="roomlist">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th>Nama Jamaah</th>
                            <th width="80">Jenis Kelamin</th>
                            <th width="120">No. Telepon</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $index => $assignment)
                            @php
                                $jamaah = $assignment->jamaahBooking->jamaah ?? null;
                            @endphp
                            @if($jamaah)
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>{{ $jamaah->nama ?? $jamaah->ktp_nama ?? '-' }}</td>
                                <td style="text-align: center;" class="{{ $jamaah->gender === 'male' ? 'gender-male' : 'gender-female' }}">
                                    {{ $jamaah->gender === 'male' ? 'Laki-laki' : ($jamaah->gender === 'female' ? 'Perempuan' : '-') }}
                                </td>
                                <td>{{ $jamaah->telepon ?? '-' }}</td>
                                <td style="font-size: 9pt;">{{ $assignment->notes ?? '-' }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @empty
            <p style="text-align: center; color: #666; padding: 20px;">Belum ada penempatan kamar untuk hotel ini.</p>
            @endforelse
        </div>
        @endforeach
    @else
        {{-- Tampilkan berdasarkan jamaah hotel bookings (per-jamaah) --}}
        @php
            // Group jamaah by hotel booking
            $byHotel = [];
            foreach($jamaahBookings as $booking) {
                foreach($booking->hotelBookings ?? [] as $hb) {
                    $hotelName = $hb->hotel->hotel_name ?? 'Hotel';
                    $cityType = ucfirst($hb->city_type ?? 'hotel');
                    $key = $hotelName . ' (' . $cityType . ')';
                    if (!isset($byHotel[$key])) $byHotel[$key] = ['hotel' => $hb, 'bookings' => []];
                    $byHotel[$key]['bookings'][] = ['booking' => $booking, 'hb' => $hb];
                }
            }
        @endphp

        @if(count($byHotel) > 0)
            @foreach($byHotel as $hotelKey => $hotelData)
            <div class="hotel-section">
                <div class="hotel-title">
                    {{ $hotelKey }}
                    <span style="font-size: 9pt; font-weight: normal;">
                        ({{ $hotelData['hotel']->check_in_date ? \Carbon\Carbon::parse($hotelData['hotel']->check_in_date)->format('d/m/Y') : '-' }} -
                        {{ $hotelData['hotel']->check_out_date ? \Carbon\Carbon::parse($hotelData['hotel']->check_out_date)->format('d/m/Y') : '-' }},
                        {{ $hotelData['hotel']->nights ?? 0 }} malam)
                    </span>
                </div>
                <table class="roomlist">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th>Nama Jamaah</th>
                            <th width="80">Tipe Kamar</th>
                            <th width="80">Jenis Kelamin</th>
                            <th width="120">No. Telepon</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hotelData['bookings'] as $idx => $item)
                        @php $jamaah = $item['booking']->jamaah; @endphp
                        <tr>
                            <td style="text-align:center;">{{ $idx + 1 }}</td>
                            <td>{{ $jamaah->nama ?? $jamaah->ktp_nama ?? '-' }}</td>
                            <td style="text-align:center;">{{ $item['hb']->room_type ? ucfirst($item['hb']->room_type) : '-' }}</td>
                            <td style="text-align:center;">{{ $jamaah->gender === 'male' ? 'L' : ($jamaah->gender === 'female' ? 'P' : '-') }}</td>
                            <td>{{ $jamaah->telepon ?? '-' }}</td>
                            <td style="font-size:9pt;">{{ $item['hb']->is_charged ? 'Charge' : 'Include' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        @else
        <div class="hotel-section">
            <div class="hotel-title">Daftar Jamaah (Belum Ada Data Hotel)</div>
            <p style="padding: 10px; background-color: #fff3cd; border: 1px solid #ffc107; margin-bottom: 15px; font-size: 9pt;">
                <strong>Catatan:</strong> Belum ada penempatan hotel. Berikut adalah daftar jamaah dengan preferensi kamar mereka.
            </p>

            @php
                // Group by gender for better organization
                $maleJamaah = $jamaahBookings->filter(fn($b) => $b->jamaah && $b->jamaah->gender === 'male');
                $femaleJamaah = $jamaahBookings->filter(fn($b) => $b->jamaah && $b->jamaah->gender === 'female');
            @endphp

            @if($maleJamaah->count() > 0)
            <div class="room-group">
                <div class="room-header">Jamaah Laki-laki ({{ $maleJamaah->count() }} orang)</div>
                <table class="roomlist">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th>Nama Jamaah</th>
                            <th width="100">Preferensi Kamar</th>
                            <th width="120">No. Telepon</th>
                            <th>Permintaan Khusus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($maleJamaah as $index => $booking)
                            @php
                                $jamaah = $booking->jamaah;
                                $roomPref = match($jamaah->room_preference) {
                                    'single' => 'Single',
                                    'double' => 'Double',
                                    'triple' => 'Triple',
                                    'quad' => 'Quad',
                                    default => '-'
                                };
                            @endphp
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>{{ $jamaah->nama ?? $jamaah->ktp_nama ?? '-' }}</td>
                                <td style="text-align: center;">{{ $roomPref }}</td>
                                <td>{{ $jamaah->telepon ?? '-' }}</td>
                                <td style="font-size: 9pt;">{{ $jamaah->special_requests ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @if($femaleJamaah->count() > 0)
            <div class="room-group">
                <div class="room-header">Jamaah Perempuan ({{ $femaleJamaah->count() }} orang)</div>
                <table class="roomlist">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th>Nama Jamaah</th>
                            <th width="100">Preferensi Kamar</th>
                            <th width="120">No. Telepon</th>
                            <th>Permintaan Khusus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($femaleJamaah as $index => $booking)
                            @php
                                $jamaah = $booking->jamaah;
                                $roomPref = match($jamaah->room_preference) {
                                    'single' => 'Single',
                                    'double' => 'Double',
                                    'triple' => 'Triple',
                                    'quad' => 'Quad',
                                    default => '-'
                                };
                            @endphp
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>{{ $jamaah->nama ?? $jamaah->ktp_nama ?? '-' }}</td>
                                <td style="text-align: center;">{{ $roomPref }}</td>
                                <td>{{ $jamaah->telepon ?? '-' }}</td>
                                <td style="font-size: 9pt;">{{ $jamaah->special_requests ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @if($jamaahBookings->count() === 0)
            <div class="no-data">
                Tidak ada data jamaah untuk keberangkatan ini.
            </div>
            @endif
        </div>
    @endif

    <div class="footer">
        <p>_______________________</p>
        <p>Authorized Signature</p>
    </div>
</body>
</html>
