<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manifest - <?php echo e($keberangkatan->keberangkatan_name); ?></title>
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
        <h3><?php echo e($keberangkatan->keberangkatan_name); ?></h3>
        <p><?php echo e($keberangkatan->travelPackage->package_name); ?></p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td width="150"><strong>Departure Code:</strong></td>
                <td><?php echo e($keberangkatan->keberangkatan_code); ?></td>
                <td width="150"><strong>Departure Date:</strong></td>
                <td><?php echo e($keberangkatan->departure_date->format('d F Y')); ?></td>
            </tr>
            <tr>
                <td><strong>Return Date:</strong></td>
                <td><?php echo e($keberangkatan->return_date->format('d F Y')); ?></td>
                <td><strong>Total Jamaah:</strong></td>
                <td><?php echo e($keberangkatan->jamaahBookings->count()); ?></td>
            </tr>
            <tr>
                <td><strong>Generated:</strong></td>
                <td colspan="3"><?php echo e(now()->format('d F Y H:i')); ?></td>
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
                <th width="30">Age</th>
            </tr>
        </thead>
        <tbody>
            <?php $rowNo = 1; ?>
            <?php $__empty_1 = true; $__currentLoopData = $keberangkatan->jamaahBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
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
                ?>
                <!-- Jamaah Utama -->
                <tr>
                    <td><?php echo e($rowNo++); ?></td>
                    <td><strong><?php echo e($jamaah->nama ?? $jamaah->ktp_nama ?? '-'); ?></strong></td>
                    <td>Jamaah Utama</td>
                    <td><?php echo e($jamaah->ktp_nik ?? '-'); ?></td>
                    <td><?php echo e($passportDoc ? $passportDoc->document_number : ($jamaah->passport_nomor ?? '-')); ?></td>
                    <td><?php echo e($passportDoc && $passportDoc->expiry_date ? $passportDoc->expiry_date->format('d M Y') : ($jamaah->passport_tanggal_kadaluarsa ? \Carbon\Carbon::parse($jamaah->passport_tanggal_kadaluarsa)->format('d M Y') : '-')); ?></td>
                    <td class="<?php echo e($isComplete ? 'status-complete' : 'status-incomplete'); ?>">
                        <?php echo e($approvedCount); ?>/5 <?php echo e($isComplete ? 'Lengkap' : 'Belum'); ?>

                    </td>
                    <td style="text-align:center;"><?php echo e($jamaah->ktp_tanggal_lahir ? \Carbon\Carbon::parse($jamaah->ktp_tanggal_lahir)->age : '-'); ?></td>
                </tr>
                <!-- Anggota Keluarga -->
                <?php $__currentLoopData = $familyMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="background:#f9f9f9;">
                    <td><?php echo e($rowNo++); ?></td>
                    <td style="padding-left:15px;"><?php echo e($fm['nama'] ?? '-'); ?></td>
                    <td><?php echo e($fm['hubungan'] ?? 'Keluarga'); ?></td>
                    <td><?php echo e($fm['nik'] ?? '-'); ?></td>
                    <td><?php echo e($fm['passport_nomor'] ?? '-'); ?></td>
                    <td><?php echo e(isset($fm['passport_exp']) ? \Carbon\Carbon::parse($fm['passport_exp'])->format('d M Y') : '-'); ?></td>
                    <td>-</td>
                    <td style="text-align:center;"><?php echo e(!empty($fm['tanggal_lahir']) ? \Carbon\Carbon::parse($fm['tanggal_lahir'])->age : '-'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #666;">
                        Tidak ada data jamaah.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:10px; font-size:9pt; color:#666;">
        Total: <?php echo e($keberangkatan->jamaahBookings->count()); ?> booking,
        <?php echo e($keberangkatan->jamaahBookings->sum(function($b) {
            $fm = $b->jamaah->family_members ?? [];
            if (is_string($fm)) $fm = json_decode($fm, true);
            return 1 + (is_array($fm) ? count($fm) : 0);
        })); ?> jiwa
    </div>

    <div class="footer">
        <p>_______________________</p>
        <p>Authorized Signature</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\document\manifest-pdf.blade.php ENDPATH**/ ?>