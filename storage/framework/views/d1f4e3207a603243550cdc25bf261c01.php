<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SPT Tahunan - <?php echo e($report->taxpayer_name); ?> - <?php echo e($report->report_year); ?></title>
    <style>
        /* Gaya Resmi DJP */
        body { 
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
        }
        .subtitle {
            font-size: 11pt;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.bordered, table.bordered th, table.bordered td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            vertical-align: top;
        }
        th {
            text-align: center;
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .signature-area {
            margin-top: 50px;
            width: 100%;
        }
        .signature-line {
            border-top: 1px solid black;
            width: 300px;
            margin: 30px auto 10px;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            font-size: 8pt;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Halaman 1: Identitas dan Penghasilan -->
    <div class="header">
        <div class="title">SURAT PEMBERITAHUAN TAHUNAN PAJAK PENGHASILAN</div>
        <div class="subtitle">TAHUN PAJAK <?php echo e($report->report_year); ?></div>
        <div>FORMULIR 1770<?php echo e(in_array('specific_gross_turnover', $tax_objects) ? 'S' : ''); ?></div>
    </div>

    <!-- Identitas WP -->
    <table class="bordered">
        <tr>
            <th colspan="4">A. IDENTITAS WAJIB PAJAK</th>
        </tr>
        <tr>
            <td width="25%">1. NPWP</td>
            <td width="25%"><?php echo e($report->npwp); ?></td>
            <td width="25%">2. Nama Wajib Pajak</td>
            <td width="25%"><?php echo e($report->taxpayer_name); ?></td>
        </tr>
        <tr>
            <td>3. Status PTKP</td>
            <td><?php echo e($ptkp_label); ?></td>
            <td>4. Status Perpajakan</td>
            <td><?php echo e($marital_status_label); ?></td>
        </tr>
        <tr>
            <td>5. Alamat</td>
            <td colspan="3"><?php echo e($report->head_office_country ?? '-'); ?></td>
        </tr>
        <tr>
            <td>6. Bidang Usaha</td>
            <td><?php echo e($report->business_field); ?></td>
            <td>7. KLU</td>
            <td><?php echo e($report->klu_code); ?></td>
        </tr>
        <tr>
            <td>8. Jenis Usaha</td>
            <td colspan="3"><?php echo e($report->business_type_label); ?></td>
        </tr>
    </table>

    <!-- Di bagian Objek Pajak -->
    <table class="bordered">
        <tr>
            <th colspan="2">B. OBJEK PAJAK</th>
        </tr>
        <?php if(isset($tax_objects) && is_array($tax_objects)): ?>
            <?php $__currentLoopData = $tax_objects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $object): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td width="5%"><?php echo e($loop->iteration); ?>.</td>
                <td width="95%">
                    <?php switch($object):
                        case ('final'): ?> PPh bersifat final <?php break; ?>
                        <?php case ('specific_gross_turnover'): ?> Peredaran bruto tertentu <?php break; ?>
                        <?php case ('general_article17'): ?> Tarif umum pasal 17 UU PPh <?php break; ?>
                        <?php default: ?> <?php echo e($object); ?>

                    <?php endswitch; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <tr>
                <td colspan="2">Tidak ada objek pajak yang dipilih</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Lampiran Penghasilan -->
    <table class="bordered">
        <tr>
            <th colspan="5">C. PENGHASILAN DAN BIAYA TAHUN <?php echo e($report->report_year); ?></th>
        </tr>
        <tr>
            <th width="5%">No</th>
            <th width="45%">Uraian</th>
            <th width="15%">Jumlah (Rp)</th>
            <th width="15%">PPh (Rp)</th>
            <th width="20%">Keterangan</th>
        </tr>
        <tr>
            <td class="text-center">1</td>
            <td>Penghasilan Usaha</td>
            <td class="text-right"><?php echo e(formatRupiah($report->gross_income ?? 0)); ?></td>
            <td class="text-right"><?php echo e(formatRupiah($report->tax_withheld ?? 0)); ?></td>
            <td>-</td>
        </tr>
        <!-- Tambahkan baris lainnya sesuai kebutuhan -->
    </table>

    <!-- Halaman 2: Perhitungan PPh -->
    <div class="page-break"></div>
    
    <div class="header">
        <div class="title">PERHITUNGAN PAJAK PENGHASILAN</div>
        <div class="subtitle">TAHUN PAJAK <?php echo e($report->report_year); ?></div>
    </div>

    <!-- Perhitungan PKP -->
    <table class="bordered">
        <tr>
            <th colspan="3">1. PENGHITUNGAN PENGHASILAN KENA PAJAK</th>
        </tr>
        <tr>
            <td width="60%">a. Penghasilan Neto</td>
            <td width="20%" class="text-right">Rp</td>
            <td width="20%" class="text-right"><?php echo e(formatRupiah($report->net_income ?? 0)); ?></td>
        </tr>
        <tr>
            <td>b. PTKP (<?php echo e($ptkp_label); ?>)</td>
            <td class="text-right">Rp</td>
            <td class="text-right">(<?php echo e(formatRupiah($report->ptkp_value ?? 0)); ?>)</td>
        </tr>
        <tr class="text-bold">
            <td>c. Penghasilan Kena Pajak</td>
            <td class="text-right">Rp</td>
            <td class="text-right"><?php echo e(formatRupiah(max(0, ($report->net_income ?? 0) - ($report->ptkp_value ?? 0)))); ?></td>
        </tr>
    </table>

    <!-- Tarif Pajak -->
    <table class="bordered">
        <tr>
            <th colspan="4">2. PENGHITUNGAN PAJAK TERUTANG</th>
        </tr>
        <tr>
            <th width="40%">Lapisan Penghasilan</th>
            <th width="15%">Tarif</th>
            <th width="25%">Pajak (Rp)</th>
            <th width="20%">Keterangan</th>
        </tr>
        <?php
            $pkp = max(0, ($report->net_income ?? 0) - ($report->ptkp_value ?? 0));
            $totalTax = 0;
        ?>
        
        <?php $__currentLoopData = $tax_rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $lower = $rate['lower_limit'];
            $upper = $rate['upper_limit'] ?? PHP_FLOAT_MAX;
            $ratePercent = $rate['rate'];
            
            if ($pkp <= 0) {
                $taxAmount = 0;
            } elseif ($pkp <= $lower) {
                $taxAmount = 0;
            } else {
                $taxable = min($pkp, $upper) - $lower;
                $taxAmount = $taxable * ($ratePercent / 100);
                $totalTax += $taxAmount;
            }
        ?>
        <tr>
            <td>
                <?php if($rate['upper_limit']): ?>
                    <?php echo e(formatRupiah($rate['lower_limit'])); ?> - <?php echo e(formatRupiah($rate['upper_limit'])); ?>

                <?php else: ?>
                    > <?php echo e(formatRupiah($rate['lower_limit'])); ?>

                <?php endif; ?>
            </td>
            <td class="text-center"><?php echo e($rate['rate']); ?>%</td>
            <td class="text-right"><?php echo e(formatRupiah($taxAmount)); ?></td>
            <td class="text-center">-</td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr class="text-bold">
            <td colspan="2">Total Pajak Terutang</td>
            <td class="text-right"><?php echo e(formatRupiah($totalTax)); ?></td>
            <td></td>
        </tr>
    </table>

    <!-- Informasi Lain -->
    <table class="bordered">
        <tr>
            <th colspan="2">3. INFORMASI LAIN</th>
        </tr>
        <tr>
            <td width="30%">Pembukuan Diaudit</td>
            <td width="70%"><?php echo e($report->is_audited ? 'Ya' : 'Tidak'); ?></td>
        </tr>
        <?php if($report->is_audited): ?>
        <tr>
            <td>Opini Audit</td>
            <td>
                <?php switch($report->audit_opinion):
                    case ('unqualified'): ?> Wajar Tanpa Pengecualian <?php break; ?>
                    <?php case ('qualified'): ?> Wajar Dengan Pengecualian <?php break; ?>
                    <?php case ('adverse'): ?> Tidak Wajar <?php break; ?>
                    <?php case ('no_opinion'): ?> Tidak Ada Opini <?php break; ?>
                <?php endswitch; ?>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td>Menggunakan Konsultan Pajak</td>
            <td><?php echo e($report->uses_tax_consultant ? 'Ya' : 'Tidak'); ?></td>
        </tr>
    </table>

    <!-- Tanda Tangan -->
    <div class="signature-area">
        <div style="text-align: center; width: 60%; margin: 0 auto;">
            <div><?php echo e($report->taxpayer_name); ?></div>
            <div class="signature-line"></div>
            <div>Nama Jelas & Tanda Tangan</div>
            <div style="margin-top: 20px;">Tanggal: <?php echo e($current_date); ?></div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Formulir ini merupakan bagian yang tidak terpisahkan dari SPT Tahunan PPh Tahun <?php echo e($report->report_year); ?>

    </div>

    <!-- Helper Function -->
    <?php
        function formatRupiah($value) {
            return number_format($value ?? 0, 0, ',', '.');
        }
    ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\annual-tax-report\pdf_template.blade.php ENDPATH**/ ?>