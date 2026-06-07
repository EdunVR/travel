<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kinerja - {{ $user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }
        /* ── Header ── */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
        }
        .report-header h1 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .report-header .subtitle {
            font-size: 11px;
            color: #555;
        }
        /* ── Info Section ── */
        .info-section {
            margin-bottom: 18px;
        }
        .info-section h2 {
            font-size: 12px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 5px 8px;
            margin-bottom: 8px;
            border-left: 3px solid #333;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .info-table td.label {
            width: 140px;
            font-weight: bold;
            color: #555;
        }
        .info-table td.colon {
            width: 10px;
        }
        /* ── Data Tables ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .data-table thead tr th {
            background-color: #333;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }
        .data-table tbody tr td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            vertical-align: top;
        }
        .data-table tbody tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        /* ── Progress Bar ── */
        .progress-bar-bg {
            background-color: #e0e0e0;
            border-radius: 3px;
            width: 100%;
            height: 10px;
            position: relative;
            overflow: hidden;
        }
        .progress-bar-fill {
            background-color: #4caf50;
            height: 10px;
            border-radius: 3px;
        }
        /* ── Attendance ── */
        .attendance-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .attendance-grid td {
            width: 33.33%;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .attendance-grid .att-label {
            font-size: 10px;
            color: #777;
            margin-bottom: 4px;
        }
        .attendance-grid .att-value {
            font-size: 20px;
            font-weight: bold;
        }
        .att-present  { color: #2e7d32; }
        .att-absent   { color: #c62828; }
        .att-late     { color: #f57c00; }
        /* ── Grade ── */
        .grade-box {
            border: 2px solid #333;
            padding: 14px;
            text-align: center;
            margin-bottom: 18px;
        }
        .grade-box .grade-letter {
            font-size: 40px;
            font-weight: bold;
            line-height: 1;
        }
        .grade-box .grade-label {
            font-size: 14px;
            margin-top: 4px;
        }
        .grade-box .grade-percent {
            font-size: 11px;
            color: #555;
            margin-top: 4px;
        }
        /* ── Overdue badge ── */
        .badge-overdue {
            background-color: #ffcccc;
            color: #c62828;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        /* ── Footer ── */
        .report-footer {
            margin-top: 24px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            font-size: 9px;
            color: #777;
            text-align: right;
        }
    </style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="report-header">
        <h1>Laporan Kinerja Karyawan</h1>
        <div class="subtitle">
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
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="colon">:</td>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <td class="label">Periode Laporan</td>
                <td class="colon">:</td>
                <td>
                    @if($period)
                        {{ $period }}
                    @else
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Tanggal Cetak</td>
                <td class="colon">:</td>
                <td>{{ \Carbon\Carbon::now()->format('d M Y H:i') }}</td>
            </tr>
        </table>
    </div>

    {{-- ── DAFTAR JOB TARGETS ── --}}
    <div class="info-section">
        <h2>Daftar Job Target</h2>
        @if($targets->isEmpty())
            <p style="font-size: 11px; color: #777; padding: 8px;">Belum ada job target untuk karyawan ini.</p>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 28%;">Judul</th>
                        <th style="width: 22%;">Deskripsi</th>
                        <th style="width: 14%;">Due Date</th>
                        <th style="width: 10%;" class="text-center">Target (%)</th>
                        <th style="width: 10%;" class="text-center">Realisasi (%)</th>
                        <th style="width: 12%;">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($targets as $index => $target)
                        @php
                            $progress    = $target->getProgressPercent();
                            $dueDate     = $target->due_date;
                            $today       = \Carbon\Carbon::today();
                            $isOverdue   = $dueDate !== null
                                            && $dueDate->lt($today)
                                            && $target->realisasi_percent < $target->target_percent;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $target->title }}
                                @if($isOverdue)
                                    <br><span class="badge-overdue">TERLAMBAT</span>
                                @endif
                            </td>
                            <td>{{ $target->description ?? '-' }}</td>
                            <td>
                                @if($dueDate)
                                    {{ $dueDate->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($target->target_percent, 1) }}</td>
                            <td class="text-center">{{ number_format($target->realisasi_percent, 1) }}</td>
                            <td>
                                <div style="margin-bottom: 2px; font-size: 10px;">{{ number_format($progress, 1) }}%</div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width: {{ min($progress, 100) }}%;"></div>
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
        Dokumen ini dicetak secara otomatis oleh sistem &bull; {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}
    </div>

</body>
</html>
