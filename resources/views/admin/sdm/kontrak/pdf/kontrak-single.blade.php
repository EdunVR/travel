<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kontrak Kerja - {{ $kontrak->nomor_kontrak }}</title>
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
            border-left: 4px solid #333;
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
        .badge-habis { background-color: #f8d7da; color: #721c24; }
        .badge-diperpanjang { background-color: #d1ecf1; color: #0c5460; }
        .badge-dibatalkan { background-color: #f8f9fa; color: #6c757d; }
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
        <h1>Surat Kontrak Kerja</h1>
        <p>{{ $kontrak->outlet->nama_outlet ?? 'PT. NAMA PERUSAHAAN' }}</p>
        <p>{{ $kontrak->outlet->alamat ?? 'Alamat Perusahaan' }}</p>
    </div>

    <!-- Nomor Kontrak -->
    <div class="section">
        <table class="info-table">
            <tr>
                <td>Nomor Kontrak</td>
                <td>: <strong>{{ $kontrak->nomor_kontrak }}</strong></td>
            </tr>
            <tr>
                <td>Jenis Kontrak</td>
                <td>: {{ $kontrak->jenis_kontrak }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: 
                    @if($kontrak->status === 'aktif')
                        <span class="badge badge-aktif">AKTIF</span>
                    @elseif($kontrak->status === 'habis')
                        <span class="badge badge-habis">HABIS</span>
                    @elseif($kontrak->status === 'diperpanjang')
                        <span class="badge badge-diperpanjang">DIPERPANJANG</span>
                    @else
                        <span class="badge badge-dibatalkan">DIBATALKAN</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Karyawan -->
    <div class="section">
        <div class="section-title">DATA KARYAWAN</div>
        <table class="info-table">
            <tr>
                <td>Nama Lengkap</td>
                <td>: {{ $kontrak->recruitment->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Posisi/Jabatan</td>
                <td>: {{ $kontrak->jabatan }}</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>: {{ $kontrak->unit_kerja }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>: {{ $kontrak->recruitment->department ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Periode Kontrak -->
    <div class="section">
        <div class="section-title">PERIODE KONTRAK</div>
        <table class="info-table">
            <tr>
                <td>Tanggal Mulai</td>
                <td>: {{ $kontrak->tanggal_mulai->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Selesai</td>
                <td>: {{ $kontrak->tanggal_selesai ? $kontrak->tanggal_selesai->format('d F Y') : 'Tidak Terbatas' }}</td>
            </tr>
            <tr>
                <td>Durasi</td>
                <td>: {{ $kontrak->durasi_bulan ?? $kontrak->durasi ?? '-' }} bulan</td>
            </tr>
        </table>
    </div>

    <!-- Kompensasi -->
    @if($kontrak->gaji_pokok)
    <div class="section">
        <div class="section-title">KOMPENSASI</div>
        <table class="info-table">
            <tr>
                <td>Gaji Pokok</td>
                <td>: Rp {{ number_format($kontrak->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Deskripsi -->
    @if($kontrak->deskripsi)
    <div class="section">
        <div class="section-title">DESKRIPSI PEKERJAAN</div>
        <p style="padding: 10px; text-align: justify;">{{ $kontrak->deskripsi }}</p>
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
        <div class="signature-box">
            <p><strong>Pihak Karyawan</strong></p>
            <div class="signature-line">
                <p>(_____________________)</p>
                <p style="font-size: 10px;">{{ $kontrak->recruitment->name ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak pada {{ now()->format('d F Y H:i') }}</p>
        <p>{{ $kontrak->outlet->nama_outlet ?? 'PT. NAMA PERUSAHAAN' }}</p>
    </div>
</body>
</html>
