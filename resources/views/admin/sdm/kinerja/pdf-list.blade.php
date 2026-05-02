<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f3f4f6;
            border-left: 4px solid #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .grade-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        .grade-a { background-color: #10b981; color: white; }
        .grade-b { background-color: #3b82f6; color: white; }
        .grade-c { background-color: #f59e0b; color: white; }
        .grade-d { background-color: #ef4444; color: white; }
        .grade-e { background-color: #6b7280; color: white; }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
        }
        .status-final { background-color: #10b981; color: white; }
        .status-draft { background-color: #f59e0b; color: white; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        @if($period)
        <p>Periode: {{ $period }}</p>
        @endif
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    @if($appraisals->count() > 0)
    <div class="summary">
        <strong>Ringkasan:</strong><br>
        Total Penilaian: {{ $appraisals->count() }}<br>
        Rata-rata Skor: {{ number_format($appraisals->avg('average_score'), 2) }}<br>
        Grade A: {{ $appraisals->where('grade', 'A')->count() }} | 
        Grade B: {{ $appraisals->where('grade', 'B')->count() }} | 
        Grade C: {{ $appraisals->where('grade', 'C')->count() }} | 
        Grade D: {{ $appraisals->where('grade', 'D')->count() }} | 
        Grade E: {{ $appraisals->where('grade', 'E')->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Outlet</th>
                <th style="width: 20%;">Karyawan</th>
                <th style="width: 15%;">Jabatan</th>
                <th style="width: 10%;">Periode</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 8%;">Skor</th>
                <th style="width: 10%;">Grade</th>
                <th style="width: 7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appraisals as $index => $appraisal)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $appraisal->outlet ? $appraisal->outlet->nama_outlet : '-' }}</td>
                <td>{{ $appraisal->employee_name }}</td>
                <td>{{ $appraisal->employee ? $appraisal->employee->position : '-' }}</td>
                <td>{{ $appraisal->period }}</td>
                <td>{{ $appraisal->appraisal_date->format('d/m/Y') }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($appraisal->average_score, 2) }}</td>
                <td style="text-align: center;">
                    @php
                        $gradeInfo = $appraisal->getGradeLabel();
                        $gradeClass = 'grade-' . strtolower($appraisal->grade);
                    @endphp
                    <span class="grade-badge {{ $gradeClass }}">{{ $appraisal->grade }}</span>
                </td>
                <td style="text-align: center;">
                    <span class="status-badge status-{{ $appraisal->status }}">
                        {{ $appraisal->status === 'final' ? 'Final' : 'Draft' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; padding: 20px; color: #666;">Tidak ada data penilaian kinerja</p>
    @endif

    <div class="footer">
        <p>Laporan Penilaian Kinerja - {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>
