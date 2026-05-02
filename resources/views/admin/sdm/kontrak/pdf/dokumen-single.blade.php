<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dokumen HR - {{ $dokumen->nomor_dokumen }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            padding: 5px;
            background-color: #f0f0f0;
            border-left: 4px solid #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
        }
        .content-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px 0;
            background-color: #f9f9f9;
        }
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-aktif { background-color: #d4edda; color: #155724; }
        .badge-expired { background-color: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Dokumen HR Resmi</h1>
        <p>{{ $dokumen->outlet->nama_outlet ?? 'PT. NAMA PERUSAHAAN' }}</p>
        <p>{{ $dokumen->outlet->alamat ?? 'Alamat Perusahaan' }}</p>
    </div>

    <!-- Informasi Dokumen -->
    <div class="section">
        <table class="info-table">
            <tr>
                <td>Nomor Dokumen</td>
                <td>: {{ $dokumen->nomor_dokumen }}</td>
            </tr>
            <tr>
                <td>Jenis Dokumen</td>
                <td>: {{ $dokumen->jenis_dokumen }}</td>
            </tr>
            <tr>
                <td>Judul Dokumen</td>
                <td>: {{ $dokumen->judul_dokumen }}</td>
            </tr>
            <tr>
                <td>Tanggal Terbit</td>
                <td>: {{ $dokumen->tanggal_terbit->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Berakhir</td>
                <td>: {{ $dokumen->tanggal_berakhir ? $dokumen->tanggal_berakhir->format('d F Y') : 'Tidak Terbatas' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: 
                    @if($dokumen->tanggal_berakhir && $dokumen->tanggal_berakhir->isPast())
                        <span class="badge badge-expired">EXPIRED</span>
                    @else
                        <span class="badge badge-aktif">AKTIF</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Karyawan -->
    @if($dokumen->recruitment_id)
    <div class="section">
        <div class="section-title">DATA KARYAWAN</div>
        <table class="info-table">
            <tr>
                <td>Nama Lengkap</td>
                <td>: {{ $dokumen->recruitment->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Posisi/Jabatan</td>
                <td>: {{ $dokumen->recruitment->position ?? '-' }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>: {{ $dokumen->recruitment->department ?? '-' }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>: {{ $dokumen->recruitment->email ?? '-' }}</td>
            </tr>
        </table>
    </div>
    @else
    <div class="section">
        <div class="section-title">JENIS DOKUMEN</div>
        <div class="content-box">
            <p style="margin: 0; text-align: center; font-weight: bold; color: #2563eb;">DOKUMEN UMUM PERUSAHAAN</p>
        </div>
    </div>
    @endif

    <!-- Deskripsi Dokumen -->
    @if($dokumen->deskripsi)
    <div class="section">
        <div class="section-title">DESKRIPSI DOKUMEN</div>
        <div class="content-box">
            <p style="margin: 0; text-align: justify;">{{ $dokumen->deskripsi }}</p>
        </div>
    </div>
    @endif

    <!-- Catatan -->
    @if($dokumen->catatan)
    <div class="section">
        <div class="section-title">CATATAN</div>
        <div class="content-box">
            <p style="margin: 0; text-align: justify;">{{ $dokumen->catatan }}</p>
        </div>
    </div>
    @endif

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Pihak Perusahaan</strong></p>
            <div class="signature-line">
                <p>(_____________________)</p>
                <p style="font-size: 10px;">Direktur/HRD</p>
            </div>
        </div>
        @if($dokumen->recruitment_id)
        <div class="signature-box">
            <p><strong>Pihak Karyawan</strong></p>
            <div class="signature-line">
                <p>(_____________________)</p>
                <p style="font-size: 10px;">{{ $dokumen->recruitment->name ?? '-' }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak pada {{ now()->format('d F Y H:i') }}</p>
        <p>{{ $dokumen->outlet->nama_outlet ?? 'PT. NAMA PERUSAHAAN' }}</p>
        <p style="margin-top: 10px; font-style: italic;">
            Dokumen HR resmi perusahaan - Simpan dengan baik.
        </p>
    </div>
</body>
</html>
