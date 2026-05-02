<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perpanjangan Kontrak - {{ $perpanjangan->kontrakBaru->nomor_kontrak }}</title>
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
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .comparison-table th,
        .comparison-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .comparison-table th {
            background-color: #f8f9fa;
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
        .badge-berhasil { background-color: #d4edda; color: #155724; }
        .badge-pending { background-color: #fff3cd; color: #856404; }
        .badge-dibatalkan { background-color: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .highlight {
            background-color: #e3f2fd;
            padding: 10px;
            border-left: 4px solid #2196f3;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Surat Perpanjangan Kontrak Kerja</h1>
        <p>{{ $perpanjangan->kontrakLama->outlet->nama_outlet ?? 'PT. NAMA PERUSAHAAN' }}</p>
        <p>{{ $perpanjangan->kontrakLama->outlet->alamat ?? 'Alamat Perusahaan' }}</p>
    </div>

    <!-- Informasi Perpanjangan -->
    <div class="section">
        <table class="info-table">
            <tr>
                <td>Tanggal Perpanjangan</td>
                <td>: {{ $perpanjangan->tanggal_perpanjangan->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: 
                    @if($perpanjangan->status === 'berhasil')
                        <span class="badge badge-berhasil">BERHASIL</span>
                    @elseif($perpanjangan->status === 'pending')
                        <span class="badge badge-pending">PENDING</span>
                    @else
                        <span class="badge badge-dibatalkan">DIBATALKAN</span>
                    @endif
                </td>
            </tr>
            @if($perpanjangan->alasan)
            <tr>
                <td>Alasan Perpanjangan</td>
                <td>: {{ $perpanjangan->alasan }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Data Karyawan -->
    <div class="section">
        <div class="section-title">DATA KARYAWAN</div>
        <table class="info-table">
            <tr>
                <td>Nama Lengkap</td>
                <td>: {{ $perpanjangan->kontrakLama->recruitment->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Posisi/Jabatan</td>
                <td>: {{ $perpanjangan->kontrakLama->jabatan }}</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>: {{ $perpanjangan->kontrakLama->unit_kerja }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>: {{ $perpanjangan->kontrakLama->recruitment->department ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Perbandingan Kontrak -->
    <div class="section">
        <div class="section-title">PERBANDINGAN KONTRAK LAMA & BARU</div>
        <table class="comparison-table">
            <thead>
                <tr>
                    <th>Aspek</th>
                    <th>Kontrak Lama</th>
                    <th>Kontrak Baru</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Nomor Kontrak</strong></td>
                    <td>{{ $perpanjangan->kontrakLama->nomor_kontrak }}</td>
                    <td>{{ $perpanjangan->kontrakBaru->nomor_kontrak }}</td>
                </tr>
                <tr>
                    <td><strong>Jenis Kontrak</strong></td>
                    <td>{{ $perpanjangan->kontrakLama->jenis_kontrak }}</td>
                    <td>{{ $perpanjangan->kontrakBaru->jenis_kontrak }}</td>
                </tr>
                <tr>
                    <td><strong>Periode Mulai</strong></td>
                    <td>{{ $perpanjangan->kontrakLama->tanggal_mulai->format('d F Y') }}</td>
                    <td>{{ $perpanjangan->kontrakBaru->tanggal_mulai->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Periode Selesai</strong></td>
                    <td>{{ $perpanjangan->kontrakLama->tanggal_selesai ? $perpanjangan->kontrakLama->tanggal_selesai->format('d F Y') : 'Tidak Terbatas' }}</td>
                    <td>{{ $perpanjangan->kontrakBaru->tanggal_selesai ? $perpanjangan->kontrakBaru->tanggal_selesai->format('d F Y') : 'Tidak Terbatas' }}</td>
                </tr>
                @if($perpanjangan->kontrakLama->gaji_pokok || $perpanjangan->kontrakBaru->gaji_pokok)
                <tr>
                    <td><strong>Gaji Pokok</strong></td>
                    <td>{{ $perpanjangan->kontrakLama->gaji_pokok ? 'Rp ' . number_format($perpanjangan->kontrakLama->gaji_pokok, 0, ',', '.') : '-' }}</td>
                    <td>{{ $perpanjangan->kontrakBaru->gaji_pokok ? 'Rp ' . number_format($perpanjangan->kontrakBaru->gaji_pokok, 0, ',', '.') : '-' }}</td>
                </tr>
                @endif
                <tr>
                    <td><strong>Status</strong></td>
                    <td>{{ ucfirst($perpanjangan->kontrakLama->status) }}</td>
                    <td>{{ ucfirst($perpanjangan->kontrakBaru->status) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Catatan -->
    @if($perpanjangan->catatan)
    <div class="section">
        <div class="section-title">CATATAN</div>
        <div class="highlight">
            <p style="margin: 0; text-align: justify;">{{ $perpanjangan->catatan }}</p>
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
        <div class="signature-box">
            <p><strong>Pihak Karyawan</strong></p>
            <div class="signature-line">
                <p>(_____________________)</p>
                <p style="font-size: 10px;">{{ $perpanjangan->kontrakLama->recruitment->name ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak pada {{ now()->format('d F Y H:i') }}</p>
        <p>{{ $perpanjangan->kontrakLama->outlet->nama_outlet ?? 'PT. NAMA PERUSAHAAN' }}</p>
    </div>
</body>
</html>
