<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perpanjangan Kontrak - <?php echo e($perpanjangan->kontrakBaru->nomor_kontrak); ?></title>
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
        <p><?php echo e($perpanjangan->kontrakLama->outlet->nama_outlet ?? 'PT. NAMA PERUSAHAAN'); ?></p>
        <p><?php echo e($perpanjangan->kontrakLama->outlet->alamat ?? 'Alamat Perusahaan'); ?></p>
    </div>

    <!-- Informasi Perpanjangan -->
    <div class="section">
        <table class="info-table">
            <tr>
                <td>Tanggal Perpanjangan</td>
                <td>: <?php echo e($perpanjangan->tanggal_perpanjangan->format('d F Y')); ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: 
                    <?php if($perpanjangan->status === 'berhasil'): ?>
                        <span class="badge badge-berhasil">BERHASIL</span>
                    <?php elseif($perpanjangan->status === 'pending'): ?>
                        <span class="badge badge-pending">PENDING</span>
                    <?php else: ?>
                        <span class="badge badge-dibatalkan">DIBATALKAN</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if($perpanjangan->alasan): ?>
            <tr>
                <td>Alasan Perpanjangan</td>
                <td>: <?php echo e($perpanjangan->alasan); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Data Karyawan -->
    <div class="section">
        <div class="section-title">DATA KARYAWAN</div>
        <table class="info-table">
            <tr>
                <td>Nama Lengkap</td>
                <td>: <?php echo e($perpanjangan->kontrakLama->recruitment->name ?? '-'); ?></td>
            </tr>
            <tr>
                <td>Posisi/Jabatan</td>
                <td>: <?php echo e($perpanjangan->kontrakLama->jabatan); ?></td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>: <?php echo e($perpanjangan->kontrakLama->unit_kerja); ?></td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>: <?php echo e($perpanjangan->kontrakLama->recruitment->department ?? '-'); ?></td>
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
                    <td><?php echo e($perpanjangan->kontrakLama->nomor_kontrak); ?></td>
                    <td><?php echo e($perpanjangan->kontrakBaru->nomor_kontrak); ?></td>
                </tr>
                <tr>
                    <td><strong>Jenis Kontrak</strong></td>
                    <td><?php echo e($perpanjangan->kontrakLama->jenis_kontrak); ?></td>
                    <td><?php echo e($perpanjangan->kontrakBaru->jenis_kontrak); ?></td>
                </tr>
                <tr>
                    <td><strong>Periode Mulai</strong></td>
                    <td><?php echo e($perpanjangan->kontrakLama->tanggal_mulai->format('d F Y')); ?></td>
                    <td><?php echo e($perpanjangan->kontrakBaru->tanggal_mulai->format('d F Y')); ?></td>
                </tr>
                <tr>
                    <td><strong>Periode Selesai</strong></td>
                    <td><?php echo e($perpanjangan->kontrakLama->tanggal_selesai ? $perpanjangan->kontrakLama->tanggal_selesai->format('d F Y') : 'Tidak Terbatas'); ?></td>
                    <td><?php echo e($perpanjangan->kontrakBaru->tanggal_selesai ? $perpanjangan->kontrakBaru->tanggal_selesai->format('d F Y') : 'Tidak Terbatas'); ?></td>
                </tr>
                <?php if($perpanjangan->kontrakLama->gaji_pokok || $perpanjangan->kontrakBaru->gaji_pokok): ?>
                <tr>
                    <td><strong>Gaji Pokok</strong></td>
                    <td><?php echo e($perpanjangan->kontrakLama->gaji_pokok ? 'Rp ' . number_format($perpanjangan->kontrakLama->gaji_pokok, 0, ',', '.') : '-'); ?></td>
                    <td><?php echo e($perpanjangan->kontrakBaru->gaji_pokok ? 'Rp ' . number_format($perpanjangan->kontrakBaru->gaji_pokok, 0, ',', '.') : '-'); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td><strong>Status</strong></td>
                    <td><?php echo e(ucfirst($perpanjangan->kontrakLama->status)); ?></td>
                    <td><?php echo e(ucfirst($perpanjangan->kontrakBaru->status)); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Catatan -->
    <?php if($perpanjangan->catatan): ?>
    <div class="section">
        <div class="section-title">CATATAN</div>
        <div class="highlight">
            <p style="margin: 0; text-align: justify;"><?php echo e($perpanjangan->catatan); ?></p>
        </div>
    </div>
    <?php endif; ?>

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
                <p style="font-size: 10px;"><?php echo e($perpanjangan->kontrakLama->recruitment->name ?? '-'); ?></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak pada <?php echo e(now()->format('d F Y H:i')); ?></p>
        <p><?php echo e($perpanjangan->kontrakLama->outlet->nama_outlet ?? 'PT. NAMA PERUSAHAAN'); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sdm\kontrak\pdf\perpanjangan-single.blade.php ENDPATH**/ ?>