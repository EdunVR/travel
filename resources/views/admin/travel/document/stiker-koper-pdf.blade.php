<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stiker Koper - {{ $keberangkatan->keberangkatan_name }}</title>
    <style>
        @page { margin: 14mm 8mm 8mm 8mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 8pt; background: #fff; }

        /* Grid 2 kolom */
        .grid { display: table; width: 100%; border-collapse: separate; border-spacing: 5px 0; }
        .grid-row { display: table-row; }
        .grid-cell { display: table-cell; width: 50%; vertical-align: top; padding: 0 2px; }

        /* ===== STIKER ===== */
        .stiker {
            border: 2px solid #1a5276;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 5px;
            page-break-inside: avoid;
            background: #fff;
        }

        /* --- KOP: logo kiri, nama travel kanan --- */
        .kop {
            background: #1a5276;
            padding: 4px 6px;
            overflow: hidden;
        }
        .kop-logo {
            float: left;
            width: 24px;
            height: 24px;
            margin-right: 5px;
            background: #fff;
            border-radius: 3px;
            padding: 1px;
        }
        .kop-logo img { width: 22px; height: 22px; object-fit: contain; }
        .kop-text { overflow: hidden; padding-top: 1px; }
        .kop-nama { font-size: 8.5pt; font-weight: bold; color: #fff; letter-spacing: 0.5px; }
        .kop-paket { font-size: 5.5pt; color: #aed6f1; }
        .clearfix { clear: both; }

        /* --- BODY UTAMA: bendera besar kiri, info kanan --- */
        .body-main {
            padding: 5px 6px;
            overflow: hidden;
            border-bottom: 1px solid #d5e8f5;
        }
        .col-flag {
            float: left;
            width: 50px;
            margin-right: 8px;
            text-align: center;
        }
        .flag-img {
            width: 50px;
            height: 33px;
            border: 1.5px solid #999;
            border-radius: 2px;
            display: block;
        }
        .flag-negara {
            font-size: 5.5pt;
            font-weight: bold;
            color: #1a5276;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }
        .col-info { overflow: hidden; }

        /* Nama jamaah — besar & mencolok */
        .nama-jamaah {
            font-size: 10pt;
            font-weight: bold;
            color: #1a5276;
            border-bottom: 1.5px solid #1a5276;
            padding-bottom: 2px;
            margin-bottom: 3px;
            line-height: 1.2;
        }
        .info-grid { overflow: hidden; }
        .info-row-2 { display: table; width: 100%; margin-bottom: 2px; }
        .info-cell { display: table-cell; width: 50%; vertical-align: top; padding-right: 3px; }
        .info-item { margin-bottom: 2px; }
        .info-item.full { width: 100%; }
        .lbl { font-size: 5pt; color: #888; text-transform: uppercase; letter-spacing: 0.3px; }
        .val { font-size: 7pt; font-weight: bold; color: #222; }

        /* --- FOOTER: tanggal & badge --- */
        .footer-stiker {
            background: #eaf4fb;
            padding: 3px 6px;
            overflow: hidden;
        }
        .footer-left { float: left; }
        .footer-right { float: right; text-align: right; }
        .footer-lbl { font-size: 5pt; color: #666; }
        .footer-val { font-size: 6.5pt; font-weight: bold; color: #1a5276; }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            color: #fff;
            letter-spacing: 0.5px;
        }
        .badge-hajj  { background: #7d6608; }
        .badge-umrah { background: #1a5276; }
        .room-badge {
            display: inline-block;
            border: 1px solid #1a5276;
            color: #1a5276;
            font-size: 5.5pt;
            font-weight: bold;
            padding: 1px 3px;
            border-radius: 2px;
            margin-top: 2px;
        }
        .no-urut { font-size: 5pt; color: #aaa; margin-top: 1px; }

        /* Garis gunting */
        .cut-line { text-align: center; font-size: 6pt; color: #bbb; margin: 2px 0; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
@php
    $allJamaah = [];
    $no = 1;
    foreach ($jamaahBookings as $booking) {
        $jamaah = $booking->jamaah;
        if (!$jamaah) continue;

        $allJamaah[] = [
            'no'          => $no++,
            'nama'        => strtoupper($jamaah->passport_nama ?: ($jamaah->ktp_nama ?: $jamaah->nama)),
            'passport_no' => $jamaah->passport_nomor ?: '-',
            'tgl_lahir'   => $jamaah->passport_tanggal_lahir
                               ? \Carbon\Carbon::parse($jamaah->passport_tanggal_lahir)->format('d/m/Y')
                               : ($jamaah->ktp_tanggal_lahir ? \Carbon\Carbon::parse($jamaah->ktp_tanggal_lahir)->format('d/m/Y') : '-'),
            'gender'      => $jamaah->gender === 'female' ? 'Perempuan' : 'Laki-laki',
            'room_type'   => strtoupper($booking->room_type ?? 'DOUBLE'),
            'booking_code'=> $booking->booking_code ?? '-',
        ];

        $familyMembers = $jamaah->family_members ?? [];
        if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true);
        if (!is_array($familyMembers)) $familyMembers = [];
        foreach ($familyMembers as $fm) {
            $allJamaah[] = [
                'no'          => $no++,
                'nama'        => strtoupper($fm['nama'] ?? '-'),
                'passport_no' => $fm['passport_nomor'] ?? '-',
                'tgl_lahir'   => isset($fm['tanggal_lahir']) && $fm['tanggal_lahir']
                                   ? \Carbon\Carbon::parse($fm['tanggal_lahir'])->format('d/m/Y') : '-',
                'gender'      => ($fm['jenis_kelamin'] ?? '') === 'perempuan' ? 'Perempuan' : 'Laki-laki',
                'room_type'   => strtoupper($booking->room_type ?? 'DOUBLE'),
                'booking_code'=> $booking->booking_code ?? '-',
            ];
        }
    }

    $packageType = strtolower($keberangkatan->travelPackage->package_type ?? 'umrah');
    $packageName = $keberangkatan->travelPackage->package_name ?? $keberangkatan->keberangkatan_name;
    $departure   = $keberangkatan->departure_date ? \Carbon\Carbon::parse($keberangkatan->departure_date)->format('d M Y') : '-';
    $return      = $keberangkatan->return_date ? \Carbon\Carbon::parse($keberangkatan->return_date)->format('d M Y') : '-';
    $kode        = $keberangkatan->keberangkatan_code ?? '';

    $chunks = array_chunk($allJamaah, 8);
@endphp

@foreach($chunks as $chunkIdx => $chunk)
@php $rows = array_chunk($chunk, 2); @endphp

<div class="grid">
@foreach($rows as $rowIdx => $row)
    <div class="grid-row">
        @foreach($row as $j)
        <div class="grid-cell">
            <div class="stiker">

                {{-- KOP --}}
                <div class="kop">
                    @if(!empty($logoBase64))
                    <div class="kop-logo">
                        <img src="{{ $logoBase64 }}" alt="logo">
                    </div>
                    @endif
                    <div class="kop-text">
                        <div class="kop-nama">{{ strtoupper($companyName) }}</div>
                        <div class="kop-paket">{{ $packageName }}</div>
                    </div>
                    <div class="clearfix"></div>
                </div>

                {{-- BODY UTAMA --}}
                <div class="body-main">
                    {{-- Bendera besar di kiri --}}
                    <div class="col-flag">
                        <img src="{{ $flagBase64 }}" class="flag-img" width="50" height="33" alt="ID">
                        <div class="flag-negara">INDONESIA</div>
                    </div>

                    {{-- Info jamaah --}}
                    <div class="col-info">
                        <div class="nama-jamaah">{{ $j['nama'] }}</div>
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                            <tr>
                                <td width="50%" style="vertical-align:top; padding-right:3px; padding-bottom:2px;">
                                    <div class="lbl">No. Passport</div>
                                    <div class="val">{{ $j['passport_no'] }}</div>
                                </td>
                                <td width="50%" style="vertical-align:top; padding-bottom:2px;">
                                    <div class="lbl">Tgl. Lahir</div>
                                    <div class="val">{{ $j['tgl_lahir'] }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%" style="vertical-align:top; padding-right:3px;">
                                    <div class="lbl">Jenis Kelamin</div>
                                    <div class="val">{{ $j['gender'] }}</div>
                                </td>
                                <td width="50%" style="vertical-align:top;">
                                    <div class="lbl">Kode Booking</div>
                                    <div class="val">{{ $j['booking_code'] }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>

                {{-- FOOTER --}}
                <div class="footer-stiker">
                    <div class="footer-left">
                        <div class="footer-lbl">Keberangkatan</div>
                        <div class="footer-val">{{ $departure }}</div>
                        <div class="footer-lbl" style="margin-top:1px;">Kembali</div>
                        <div class="footer-val">{{ $return }}</div>
                    </div>
                    <div class="footer-right">
                        <span class="badge badge-{{ $packageType }}">{{ strtoupper($packageType) }}</span><br>
                        <span class="room-badge">{{ $j['room_type'] }}</span><br>
                        <div class="no-urut">No. {{ $j['no'] }} &nbsp;|&nbsp; {{ $kode }}</div>
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div>
        </div>
        @endforeach
        @if(count($row) === 1)
        <div class="grid-cell"></div>
        @endif
    </div>
    @if(!$loop->last)
    <div class="grid-row">
        <div class="grid-cell" colspan="2">
            <div class="cut-line">✂ ─────────────────────────────────────────────────────────────────────────────────────</div>
        </div>
    </div>
    @endif
@endforeach
</div>

@if(!$loop->last)
<div class="page-break"></div>
@endif
@endforeach

</body>
</html>
