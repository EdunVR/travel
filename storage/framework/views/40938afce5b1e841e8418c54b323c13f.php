<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Siskopatuh Report - <?php echo e($keberangkatan->keberangkatan_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
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
        table.siskopatuh {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }
        table.siskopatuh th,
        table.siskopatuh td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }
        table.siskopatuh th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
        }
        .signature-section {
            display: inline-block;
            width: 45%;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN SISKOPATUH</h2>
        <h3>SISTEM KOMPUTERISASI PENYELENGGARAAN IBADAH HAJI DAN UMRAH</h3>
        <p><?php echo e($keberangkatan->keberangkatan_name); ?></p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td width="200"><strong>Kode Keberangkatan:</strong></td>
                <td><?php echo e($keberangkatan->keberangkatan_code); ?></td>
            </tr>
            <tr>
                <td><strong>Nama Paket:</strong></td>
                <td><?php echo e($keberangkatan->travelPackage->package_name); ?></td>
            </tr>
            <tr>
                <td><strong>Jenis Perjalanan:</strong></td>
                <td><?php echo e(strtoupper($keberangkatan->travelPackage->package_type)); ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal Keberangkatan:</strong></td>
                <td><?php echo e($keberangkatan->departure_date->format('d F Y')); ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal Kepulangan:</strong></td>
                <td><?php echo e($keberangkatan->return_date->format('d F Y')); ?></td>
            </tr>
            <tr>
                <td><strong>Jumlah Jamaah:</strong></td>
                <td><?php echo e($keberangkatan->jamaahBookings->count()); ?> orang</td>
            </tr>
            <tr>
                <td><strong>Tanggal Laporan:</strong></td>
                <td><?php echo e(now()->format('d F Y')); ?></td>
            </tr>
        </table>
    </div>

    <table class="siskopatuh">
        <thead>
            <tr>
                <th rowspan="2" width="25">No</th>
                <th rowspan="2">Nama Lengkap</th>
                <th rowspan="2">NIK KTP</th>
                <th rowspan="2">Tempat Lahir</th>
                <th rowspan="2">Tanggal Lahir</th>
                <th rowspan="2">Jenis Kelamin</th>
                <th colspan="3">Passport</th>
                <th colspan="2">Visa</th>
            </tr>
            <tr>
                <th>Nomor</th>
                <th>Tanggal Terbit</th>
                <th>Tanggal Kadaluarsa</th>
                <th>Nomor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $keberangkatan->jamaahBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $jamaah = $booking->jamaah;
                    $passportDoc = $booking->documents->where('document_type', 'passport')->where('status', 'approved')->first();
                    $visaDoc = $booking->documents->where('document_type', 'visa')->where('status', 'approved')->first();
                    
                    // Fallback untuk tanggal lahir passport - gunakan dari KTP jika tidak ada di passport
                    $tanggalLahir = $jamaah->passport_tanggal_lahir ?? $jamaah->ktp_tanggal_lahir;
                ?>
                <tr>
                    <td style="text-align: center;"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($jamaah->nama ?? $jamaah->ktp_nama ?? $jamaah->passport_nama ?? '-'); ?></td>
                    <td><?php echo e($jamaah->ktp_nik ?? '-'); ?></td>
                    <td><?php echo e($jamaah->ktp_tempat_lahir ?? '-'); ?></td>
                    <td><?php echo e($tanggalLahir ? \Carbon\Carbon::parse($tanggalLahir)->format('d/m/Y') : '-'); ?></td>
                    <td style="text-align: center;"><?php echo e($jamaah->gender ?? '-'); ?></td>
                    <td><?php echo e($passportDoc ? $passportDoc->document_number : ($jamaah->passport_nomor ?? '-')); ?></td>
                    <td>
                        <?php if($passportDoc && $passportDoc->issue_date): ?>
                            <?php echo e($passportDoc->issue_date->format('d/m/Y')); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($passportDoc && $passportDoc->expiry_date ? $passportDoc->expiry_date->format('d/m/Y') : ($jamaah->passport_tanggal_kadaluarsa ? \Carbon\Carbon::parse($jamaah->passport_tanggal_kadaluarsa)->format('d/m/Y') : '-')); ?></td>
                    <td><?php echo e($visaDoc ? $visaDoc->document_number : '-'); ?></td>
                    <td style="text-align: center;"><?php echo e($visaDoc ? strtoupper($visaDoc->status) : '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="11" style="text-align: center; padding: 20px; color: #666;">
                        Tidak ada data jamaah yang terdaftar untuk keberangkatan ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-section" style="float: left;">
            <p>Mengetahui,</p>
            <p>Kepala Kantor</p>
            <br><br><br>
            <p>_______________________</p>
            <p>NIP.</p>
        </div>
        <div class="signature-section" style="float: right;">
            <p>Penanggung Jawab</p>
            <br><br><br><br>
            <p>_______________________</p>
            <p>Nama & Jabatan</p>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\document\siskopatuh-pdf.blade.php ENDPATH**/ ?>