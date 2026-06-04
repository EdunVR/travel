<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manifest <?php echo e($departureDate ?? ''); ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm 15mm 20mm 15mm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 8pt; color: #1a1a1a; line-height: 1.3; padding: 20px 25px; }

        /* Header / Kop Surat */
        .header { width: 100%; margin-bottom: 8px; border-bottom: 2px solid #2E7D32; padding-bottom: 8px; }
        .header-table { width: 100%; }
        .header-logo { width: 60px; vertical-align: middle; }
        .header-logo img { max-height: 50px; max-width: 55px; }
        .header-info { vertical-align: middle; padding-left: 10px; }
        .header-info .company-name { font-size: 13pt; font-weight: bold; color: #1b5e20; margin: 0; }
        .header-info .company-tagline { font-size: 7.5pt; color: #555; margin: 2px 0 0 0; }
        .header-info .company-address { font-size: 7pt; color: #777; margin: 1px 0 0 0; }
        .header-right { text-align: right; vertical-align: middle; }
        .header-right .doc-label { font-size: 7pt; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        .header-right .doc-code { font-size: 8pt; font-weight: bold; color: #333; font-family: 'Courier New', monospace; }

        /* Title */
        .title { text-align: center; font-size: 13pt; font-weight: bold; margin: 10px 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; color: #1b5e20; }
        .subtitle { text-align: center; font-size: 8.5pt; color: #555; margin-bottom: 10px; }

        /* Table */
        .manifest-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; margin-top: 4px; }
        .manifest-table th, .manifest-table td { border: 0.5px solid #4a4a4a; padding: 3px 4px; vertical-align: middle; }
        .manifest-table thead th {
            background-color: #1b5e20; color: #ffffff; font-weight: bold;
            text-align: center; font-size: 7pt; text-transform: uppercase;
            padding: 5px 3px; white-space: nowrap;
        }
        .manifest-table tbody tr:nth-child(even) { background-color: #e8f5e9; }
        .manifest-table tbody tr:nth-child(odd) { background-color: #ffffff; }
        .manifest-table td.center { text-align: center; }
        .manifest-table td.no-col { text-align: center; font-weight: bold; width: 22px; }
        .manifest-table td.name-col { white-space: nowrap; font-weight: 500; }
        .manifest-table td.passport-col { font-family: 'Courier New', monospace; text-align: center; font-size: 7pt; white-space: nowrap; }
        .manifest-table td.date-col { text-align: center; font-size: 6.5pt; white-space: nowrap; }
        .manifest-table td.nat-col { text-align: center; width: 22px; font-size: 7pt; }

        /* Berdekatan group */
        .berdekatan-cell {
            background-color: #a5d6a7 !important; text-align: center; font-weight: bold;
            font-size: 6.5pt; color: #1b5e20; writing-mode: vertical-rl;
            text-orientation: mixed; letter-spacing: 1px; padding: 3px 2px; width: 16px;
        }
        .group-indicator { width: 16px; min-width: 16px; text-align: center; font-size: 5pt; color: #ccc; }

        /* Footer */
        .footer { margin-top: 10px; padding-top: 6px; border-top: 1px solid #ddd; }
        .footer-table { width: 100%; font-size: 7pt; color: #666; }
        .footer-left { text-align: left; }
        .footer-right { text-align: right; }
    </style>
</head>
<body>
    
    <div class="header">
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <?php if($logoBase64): ?>
                <td class="header-logo">
                    <img src="<?php echo e($logoBase64); ?>" alt="Logo">
                </td>
                <?php endif; ?>
                <td class="header-info">
                    <p class="company-name"><?php echo e($companySettings->company_name ?? 'HM Tour & Travel'); ?></p>
                    <p class="company-tagline">Berizin Kemenag RI — Penyelenggara Perjalanan Ibadah Umrah & Haji</p>
                    <p class="company-address"><?php echo e($companySettings->company_address ?? ''); ?> <?php echo e($companySettings->company_phone ? '| Telp: ' . $companySettings->company_phone : ''); ?></p>
                </td>
                <td class="header-right">
                    <p class="doc-label">Kode Keberangkatan</p>
                    <p class="doc-code"><?php echo e($keberangkatan->keberangkatan_code ?? '-'); ?></p>
                    <p class="doc-label" style="margin-top: 4px;">Paket</p>
                    <p class="doc-code" style="font-size: 7pt;"><?php echo e($keberangkatan->travelPackage->package_name ?? '-'); ?></p>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="title">MANIFEST <?php echo e($departureDate); ?></div>
    <div class="subtitle">
        Tanggal Keberangkatan: <?php echo e($keberangkatan->departure_date ? \Carbon\Carbon::parse($keberangkatan->departure_date)->format('d/m/Y') : '-'); ?>

        &nbsp;&bull;&nbsp;
        Tanggal Kepulangan: <?php echo e($keberangkatan->return_date ? \Carbon\Carbon::parse($keberangkatan->return_date)->format('d/m/Y') : '-'); ?>

        &nbsp;&bull;&nbsp;
        Total Jamaah: <?php echo e(count($manifestRows)); ?> orang
    </div>

    <?php
        function fmtDate($date) {
            if (empty($date)) return '-';
            try { return \Carbon\Carbon::parse($date)->format('d-M-y'); } catch (\Exception $e) { return $date; }
        }
        function fmtGender($g) {
            $gl = strtolower($g ?? '');
            if (in_array($gl, ['male', 'l', 'laki-laki'])) return 'L/M';
            if (in_array($gl, ['female', 'p', 'perempuan'])) return 'P/F';
            return $gl;
        }

        // Build group map for BERDEKATAN rowspan
        $groups = []; $cg = null;
        foreach ($manifestRows as $idx => $row) {
            if (($row['group_label'] ?? '') === 'BERDEKATAN') {
                if ($cg === null) { $cg = ['start' => $idx, 'end' => $idx]; } else { $cg['end'] = $idx; }
            } else {
                if ($cg !== null) { $groups[] = $cg; $cg = null; }
            }
        }
        if ($cg !== null) $groups[] = $cg;

        $groupMap = [];
        foreach ($groups as $g) {
            $span = $g['end'] - $g['start'] + 1;
            for ($i = $g['start']; $i <= $g['end']; $i++) {
                $groupMap[$i] = ['is_first' => ($i === $g['start']), 'span' => $span];
            }
        }
    ?>

    
    <table class="manifest-table">
        <thead>
            <tr>
                <th style="width:16px;"></th>
                <th style="width:22px;">NO</th>
                <th style="width:32px;">TITLE</th>
                <th>FULL NAME</th>
                <th style="width:28px;">GENDER</th>
                <th style="width:68px;">NO PASSPORT</th>
                <th style="width:52px;">ISSUED DATE</th>
                <th style="width:52px;">EXPIRE DATE</th>
                <th style="width:22px;">NAT</th>
                <th style="width:52px;">DATE OF BIRTH</th>
                <th style="width:68px;">OFFICE ISSUED</th>
                <th style="width:55px;">BIRTH CITY</th>
                <th>RELATION</th>
                <th style="width:22px;">AGE</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $manifestRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <?php if(isset($groupMap[$idx])): ?>
                        <?php if($groupMap[$idx]['is_first']): ?>
                            <td class="berdekatan-cell" rowspan="<?php echo e($groupMap[$idx]['span']); ?>">BERDEKATAN</td>
                        <?php endif; ?>
                    <?php else: ?>
                        <td class="group-indicator">—</td>
                    <?php endif; ?>
                    <td class="no-col"><?php echo e($idx + 1); ?></td>
                    <td class="center"><?php echo e($row['title'] ?? ''); ?></td>
                    <td class="name-col"><?php echo e(strtoupper($row['full_name'] ?? '')); ?></td>
                    <td class="center"><?php echo e(fmtGender($row['gender'] ?? '')); ?></td>
                    <td class="passport-col"><?php echo e($row['passport_no'] ?? '-'); ?></td>
                    <td class="date-col"><?php echo e(fmtDate($row['issued_date'] ?? '')); ?></td>
                    <td class="date-col"><?php echo e(fmtDate($row['expire_date'] ?? '')); ?></td>
                    <td class="nat-col"><?php echo e($row['nationality'] ?? 'IDN'); ?></td>
                    <td class="date-col"><?php echo e(fmtDate($row['date_of_birth'] ?? '')); ?></td>
                    <td class="center" style="font-size:6.5pt;"><?php echo e($row['office_issued'] ?? '-'); ?></td>
                    <td class="center" style="font-size:6.5pt;"><?php echo e($row['birth_city'] ?? '-'); ?></td>
                    <td style="font-size:6.5pt;">
                        <?php if(($row['type'] ?? '') === 'main'): ?>
                            <span style="color:#888;font-style:italic;">—</span>
                        <?php else: ?>
                            <?php echo e($row['relation'] ?? ''); ?>

                        <?php endif; ?>
                    </td>
                    <td class="center"><?php echo e($row['age'] ?? ''); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    
    <div class="footer">
        <table class="footer-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="footer-left">
                    <?php echo e($companySettings->company_name ?? 'HM Tour & Travel'); ?> — Manifest Keberangkatan
                </td>
                <td class="footer-right">
                    Dicetak: <?php echo e(\Carbon\Carbon::now()->format('d-m-Y H:i')); ?> | Halaman 1
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\keberangkatan\manifest-table-pdf.blade.php ENDPATH**/ ?>