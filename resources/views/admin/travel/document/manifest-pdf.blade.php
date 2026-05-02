<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manifest - {{ $keberangkatan->keberangkatan_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
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
        table.manifest {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.manifest th,
        table.manifest td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        table.manifest th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .status-complete {
            color: green;
            font-weight: bold;
        }
        .status-incomplete {
            color: red;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>MANIFEST JAMAAH</h2>
        <h3>{{ $keberangkatan->keberangkatan_name }}</h3>
        <p>{{ $keberangkatan->travelPackage->package_name }}</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td width="150"><strong>Departure Code:</strong></td>
                <td>{{ $keberangkatan->keberangkatan_code }}</td>
                <td width="150"><strong>Departure Date:</strong></td>
                <td>{{ $keberangkatan->departure_date->format('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Return Date:</strong></td>
                <td>{{ $keberangkatan->return_date->format('d F Y') }}</td>
                <td><strong>Total Jamaah:</strong></td>
                <td>{{ $keberangkatan->jamaahBookings->count() }}</td>
            </tr>
            <tr>
                <td><strong>Generated:</strong></td>
                <td colspan="3">{{ now()->format('d F Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table class="manifest">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Nama</th>
                <th>Hubungan</th>
                <th>KTP/NIK</th>
                <th>No. Passport</th>
                <th>Exp. Passport</th>
                <th>Status Dokumen</th>
            </tr>
        </thead>
        <tbody>
            @php $rowNo = 1; @endphp
            @forelse($keberangkatan->jamaahBookings as $booking)
                @php
                    $jamaah = $booking->jamaah;
                    $passportDoc = $booking->documents->where('document_type', 'passport')->first();
                    $hasPassport = ($passportDoc && $passportDoc->status === 'approved') || !empty($jamaah->passport_nomor);
                    $hasVisa = $booking->documents->where('document_type', 'visa')->where('status', 'approved')->first() !== null;
                    $hasTicket = $booking->documents->where('document_type', 'ticket')->where('status', 'approved')->first() !== null;
                    $hasInsurance = $booking->documents->where('document_type', 'insurance')->where('status', 'approved')->first() !== null;
                    $hasHealthCert = $booking->documents->where('document_type', 'health_certificate')->where('status', 'approved')->first() !== null;
                    $approvedCount = ($hasPassport?1:0)+($hasVisa?1:0)+($hasTicket?1:0)+($hasInsurance?1:0)+($hasHealthCert?1:0);
                    $isComplete = $approvedCount === 5;

                    // Anggota keluarga
                    $familyMembers = $jamaah->family_members ?? [];
                    if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true);
                    if (!is_array($familyMembers)) $familyMembers = [];
                @endphp
                <!-- Jamaah Utama -->
                <tr>
                    <td>{{ $rowNo++ }}</td>
                    <td><strong>{{ $jamaah->nama ?? $jamaah->ktp_nama ?? '-' }}</strong></td>
                    <td>Jamaah Utama</td>
                    <td>{{ $jamaah->ktp_nik ?? '-' }}</td>
                    <td>{{ $passportDoc ? $passportDoc->document_number : ($jamaah->passport_nomor ?? '-') }}</td>
                    <td>{{ $passportDoc && $passportDoc->expiry_date ? $passportDoc->expiry_date->format('d M Y') : ($jamaah->passport_tanggal_kadaluarsa ? \Carbon\Carbon::parse($jamaah->passport_tanggal_kadaluarsa)->format('d M Y') : '-') }}</td>
                    <td class="{{ $isComplete ? 'status-complete' : 'status-incomplete' }}">
                        {{ $approvedCount }}/5 {{ $isComplete ? 'Lengkap' : 'Belum' }}
                    </td>
                </tr>
                <!-- Anggota Keluarga -->
                @foreach($familyMembers as $fm)
                <tr style="background:#f9f9f9;">
                    <td>{{ $rowNo++ }}</td>
                    <td style="padding-left:15px;">{{ $fm['nama'] ?? '-' }}</td>
                    <td>{{ $fm['hubungan'] ?? 'Keluarga' }}</td>
                    <td>{{ $fm['nik'] ?? '-' }}</td>
                    <td>{{ $fm['passport_nomor'] ?? '-' }}</td>
                    <td>{{ isset($fm['passport_exp']) ? \Carbon\Carbon::parse($fm['passport_exp'])->format('d M Y') : '-' }}</td>
                    <td>-</td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #666;">
                        Tidak ada data jamaah.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:10px; font-size:9pt; color:#666;">
        Total: {{ $keberangkatan->jamaahBookings->count() }} booking,
        {{ $keberangkatan->jamaahBookings->sum(function($b) {
            $fm = $b->jamaah->family_members ?? [];
            if (is_string($fm)) $fm = json_decode($fm, true);
            return 1 + (is_array($fm) ? count($fm) : 0);
        }) }} jiwa
    </div>

    <div class="footer">
        <p>_______________________</p>
        <p>Authorized Signature</p>
    </div>
</body>
</html>
