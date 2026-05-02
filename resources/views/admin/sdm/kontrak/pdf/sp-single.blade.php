<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Peringatan - {{ $sp->nomor_sp }}</title>
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
            border-left: 4px solid #dc2626;
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
        .badge-sp1 { background-color: #fef3c7; color: #92400e; }
        .badge-sp2 { background-color: #fed7aa; color: #9a3412; }
        .badge-sp3 { background-color: #fecaca; color: #991b1b; }
        .badge-aktif { background-color: #d4edda; color: #155724; }
        .badge-selesai { background-color: #e2e8f0; color: #475569; }
        .badge-dibatalkan { background-color: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .warning-box {
            background-color: #fef2f2;
            border: 2px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning-box h3 {
            margin: 0 0 10px 0;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Surat Peringatan {{ $sp->jenis_sp }}</h1>
        <p>{{ $sp->outlet->nama_outlet ?? 'PT. NAMA PERUSAHAAN' }}</p>
        <p>{{ $sp->outlet->alamat ?? 'Alamat Perusahaan' }}</p>
    </div>

    <!-- Informasi SP -->
    <div class="section">
        <table class="info-table">
            <tr>
                <td>Nomor SP</td>
                <td>: {{ $sp->nomor_sp }}</td>
            </tr>
            <tr>
                <td>Jenis SP</td>
                <td>: 
                    @if($sp->jenis_sp === 'SP1')
                        <span class="badge badge-sp1">SP1 - Peringatan Pertama</span>
                    @elseif($sp->jenis_sp === 'SP2')
                        <span class="badge badge-sp2">SP2 - Peringatan Kedua</span>
                    @else
                        <span class="badge badge-sp3">SP3 - Peringatan Ketiga</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Tanggal SP</td>
                <td>: {{ $sp->tanggal_sp->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Masa Berlaku</td>
                <td>: {{ $sp->tanggal_berlaku->format('d F Y') }} s/d {{ $sp->tanggal_berakhir ? $sp->tanggal_berakhir->format('d F Y') : 'Tidak Terbatas' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: 
                    @if($sp->status === 'aktif')
                        <span class="badge badge-aktif">AKTIF</span>
                    @elseif($sp->status === 'selesai')
                        <span class="badge badge-selesai">SELESAI</span>
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
                <td>: {{ $sp->recruitment->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Posisi/Jabatan</td>
                <td>: {{ $sp->recruitment->position ?? '-' }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>: {{ $sp->recruitment->department ?? '-' }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>: {{ $sp->recruitment->email ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Alasan Peringatan -->
    <div class="section">
        <div class="section-title">ALASAN PERINGATAN</div>
        <div class="content-box">
            <p style="margin: 0; text-align: justify;">{{ $sp->alasan }}</p>
        </div>
    </div>

    <!-- Tindakan yang Diharapkan -->
    @if($sp->tindakan_perbaikan)
    <div class="section">
        <div class="section-title">TINDAKAN PERBAIKAN YANG DIHARAPKAN</div>
        <div class="content-box">
            <p style="margin: 0; text-align: justify;">{{ $sp->tindakan_perbaikan }}</p>
        </div>
    </div>
    @endif

    <!-- Konsekuensi -->
    <div class="warning-box">
        <h3>⚠️ KONSEKUENSI</h3>
        <p style="margin: 0; text-align: justify;">
            @if($sp->jenis_sp === 'SP1')
                Apabila pelanggaran yang sama terulang kembali, maka akan diberikan Surat Peringatan Kedua (SP2).
            @elseif($sp->jenis_sp === 'SP2')
                Apabila pelanggaran yang sama terulang kembali, maka akan diberikan Surat Peringatan Ketiga (SP3).
            @else
                Apabila pelanggaran yang sama terulang kembali, maka perusahaan berhak melakukan pemutusan hubungan kerja (PHK) sesuai dengan peraturan yang berlaku.
            @endif
        </p>
    </div>

    <!-- Catatan -->
    @if($sp->catatan)
    <div class="section">
        <div class="section-title">CATATAN</div>
        <div class="content-box">
            <p style="margin: 0; text-align: justify;">{{ $sp->catatan }}</p>
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
                <p style="font-size: 10px;">{{ $sp->recruitment->name ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak pada {{ now()->format('d F Y H:i') }}</p>
        <p>{{ $sp->outlet->nama_outlet ?? 'PT. NAMA PERUSAHAAN' }}</p>
        <p style="margin-top: 10px; font-style: italic;">
            Surat peringatan ini merupakan dokumen resmi perusahaan dan harus disimpan dengan baik.
        </p>
    </div>
</body>
</html>
