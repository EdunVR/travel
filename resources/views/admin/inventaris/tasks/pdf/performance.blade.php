<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kinerja - {{ $user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #333; padding: 20px; }

        /* ── Kop Surat ── */
        .kop { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .kop td { vertical-align: middle; padding: 4px 0; }
        .kop .logo-cell { width: 80px; text-align: center; }
        .kop .logo-cell img { max-height: 60px; max-width: 75px; object-fit: contain; }
        .kop .info-cell { padding-left: 12px; }
        .kop .company-name { font-size: 15px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop .company-sub  { font-size: 10px; color: #555; margin-top: 2px; line-height: 1.5; }
        .kop-divider { border: none; border-top: 2.5px solid #333; margin: 8px 0 4px; }
        .kop-divider2 { border: none; border-top: 1px solid #333; margin: 2px 0 14px; }

        /* ── Doc title ── */
        .doc-title { text-align: center; margin-bottom: 14px; }
        .doc-title h1 { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .doc-title .period { font-size: 10px; color: #666; margin-top: 3px; }

        /* ── Info Section ── */
        .info-section { margin-bottom: 14px; }
        .info-section h2 {
            font-size: 11px; font-weight: bold;
            background-color: #e8e8e8; padding: 4px 8px;
            margin-bottom: 6px; border-left: 3px solid #333;
        }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 8px; vertical-align: top; }
        .info-table td.label { width: 130px; font-weight: bold; color: #555; }
        .info-table td.colon { width: 10px; }

        /* ── Data Tables ── */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .data-table thead tr th { background-color: #374151; color: #fff; padding: 5px 7px; text-align: left; font-size: 10px; }
        .data-table tbody tr td { border: 1px solid #ddd; padding: 5px 7px; vertical-align: top; }
        .data-table tbody tr:nth-child(even) td { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        /* ── Progress Bar ── */
        .progress-bar-bg  { background-color: #e0e0e0; border-radius: 3px; width: 100%; height: 8px; overflow: hidden; }
        .progress-bar-fill { background-color: #2563eb; height: 8px; border-radius: 3px; }

        /* ── Attendance ── */
        .attendance-grid { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .attendance-grid td { width: 33.33%; border: 1px solid #ddd; padding: 8px; text-align: center; }
        .att-label { font-size: 10px; color: #777; margin-bottom: 3px; }
        .att-value { font-size: 18px; font-weight: bold; }
        .att-present { color: #2e7d32; }
        .att-absent  { color: #c62828; }
        .att-late    { color: #f57c00; }

        /* ── Grade ── */
        .grade-box { border: 2px solid #374151; padding: 12px; text-align: center; margin-bottom: 14px; border-radius: 4px; }
        .grade-letter  { font-size: 36px; font-weight: bold; line-height: 1; }
        .grade-label   { font-size: 13px; margin-top: 4px; }
        .grade-percent { font-size: 10px; color: #555; margin-top: 3px; }

        /* ── Overdue badge ── */
        .badge-overdue { background-color: #fee2e2; color: #c62828; padding: 1px 5px; border-radius: 3px; font-size: 9px; font-weight: bold; }

        /* ── Footer ── */
        .report-footer { margin-top: 20px; border-top: 1px solid #ccc; padding-top: 6px; font-size: 9px; color: #888; text-align: right; }
    </style>
</head>
<body>

    {{-- ── KOP SURAT ── --}}
    <table class="kop">
        <tr>
            @if(!empty($logoBase64))
            <td class="logo-cell">
                <img src="{{ $logoBase64 }}" alt="Logo">
            </td>
            @endif
            <td class="info-cell">
                <div class="company-name">{{ $company->company_name ?? config('app.name') }}</div>
                <div class="company-sub">
                    @if($company->company_address){{ $company->company_address }}@endif
                    @if($company->company_phone) &bull; Telp: {{ $company->company_phone }}@endif
                    @if($company->company_email) &bull; {{ $company->company_email }}@endif
                    @if($company->company_website) &bull; {{ $company->company_website }}@endif
                </div>
            </td>
        </tr>
    </table>
    <hr class="kop-divider">
    <hr class="kop-divider2">

    {{-- ── JUDUL DOKUMEN ── --}}
    <div class="doc-title">
        <h1>Laporan Kinerja Karyawan</h1>
        <div class="period">
            Periode:
            @if($period)
                {{ $period }}
            @else
                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            @endif
        </div>
    </div>

    {{-- ── INFO KARYAWAN ── --}}
    <div class="info-section">
        <h2>Informasi Karyawan</h2>
        <table class="info-table">
            <tr><td class="label">Nama</td><td class="colon">:</td><td>{{ $user->name }}</td></tr>
            <tr><td class="label">Email</td><td class="colon">:</td><td>{{ $user->email }}</td></tr>
            <tr>
                <td class="label">Periode Laporan</td>
                <td class="colon">:</td>
                <td>@if($period){{ $period }}@else{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}@endif</td>
            </tr>
            <tr><td class="label">Tanggal Cetak</td><td class="colon">:</td><td>{{ \Carbon\Carbon::now()->format('d M Y H:i') }}</td></tr>
        </table>
    </div>

    {{-- ── DAFTAR JOB TARGETS ── --}}
    <div class="info-section">
        <h2>Daftar Job Target</h2>
        @if($targets->isEmpty())
            <p style="font-size:11px; color:#777; padding:6px;">Belum ada job target untuk karyawan ini.</p>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:4%;">#</th>
                        <th style="width:30%;">Judul</th>
                        <th style="width:22%;">Deskripsi</th>
                        <th style="width:12%;">Due Date</th>
                        <th class="text-center" style="width:10%;">Target %</th>
                        <th class="text-center" style="width:10%;">Realisasi %</th>
                        <th style="width:12%;">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($targets as $index => $target)
                        @php
                            $progress  = $target->getProgressPercent();
                            $dueDate   = $target->due_date;
                            $today     = \Carbon\Carbon::today();
                            $isOverdue = $dueDate && $dueDate->lt($today) && $target->realisasi_percent < $target->target_percent;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $target->title }}
                                @if($isOverdue)<br><span class="badge-overdue">TERLAMBAT</span>@endif
                            </td>
                            <td>{{ $target->description ?? '-' }}</td>
                            <td>{{ $dueDate ? $dueDate->format('d M Y') : '-' }}</td>
                            <td class="text-center">{{ number_format($target->target_percent, 1) }}</td>
                            <td class="text-center">{{ number_format($target->realisasi_percent, 1) }}</td>
                            <td>
                                <div style="font-size:10px; margin-bottom:2px;">{{ number_format($progress, 1) }}%</div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width:{{ min($progress, 100) }}%;"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ── RINGKASAN KEHADIRAN ── --}}
    <div class="info-section">
        <h2>Ringkasan Kehadiran</h2>
        <table class="attendance-grid">
            <tr>
                <td>
                    <div class="att-label">Hadir</div>
                    <div class="att-value att-present">{{ $attendanceSummary['present'] }}</div>
                    <div class="att-label">hari</div>
                </td>
                <td>
                    <div class="att-label">Absen</div>
                    <div class="att-value att-absent">{{ $attendanceSummary['absent'] }}</div>
                    <div class="att-label">hari</div>
                </td>
                <td>
                    <div class="att-label">Terlambat</div>
                    <div class="att-value att-late">{{ $attendanceSummary['late'] }}</div>
                    <div class="att-label">hari</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── GRADE OVERALL ── --}}
    <div class="info-section">
        <h2>Grade Kinerja Keseluruhan</h2>
        <div class="grade-box">
            <div class="grade-letter">{{ $grade?->grade ?? '-' }}</div>
            <div class="grade-label">{{ $grade?->label ?? 'Tidak ada pengaturan grade' }}</div>
            <div class="grade-percent">Overall Progress: {{ number_format($overall, 1) }}%</div>
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="report-footer">
        {{ $company->company_name ?? config('app.name') }} &bull;
        Dicetak {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}
    </div>

</body>
</html>
