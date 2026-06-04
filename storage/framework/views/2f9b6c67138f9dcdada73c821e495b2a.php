<!DOCTYPE html>
<html>
<head>
    <title>INVOICE SERVICE - <?php echo e($invoice->no_invoice); ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .logo {
            width: 60px;
            height: auto;
            float: left;
            margin-right: 15px;
        }
        
        .company-info {
            overflow: hidden;
        }
        
        .company-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 3px;
        }
        
        .company-address {
            font-size: 11px;
            line-height: 1.3;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        .invoice-detail {
            font-size: 11px;
            line-height: 1.4;
        }
        
        .customer-info {
            margin: 15px 0;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
            font-size: 11px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 8px 6px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-left {
            text-align: left;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .bank-info {
            border: 1px solid #000;
            padding: 10px;
            background-color: #f8f9fa;
            font-size: 11px;
        }
        
        .signature {
            display: table;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 20px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin: 30px 0 5px 0;
            width: 80%;
            display: inline-block;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        .keep-together {
            page-break-inside: avoid;
        }
        
        .avoid-break {
            page-break-before: avoid;
        }

        /* Style untuk garansi */
        .garansi-badge {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        /* Style untuk strikethrough */
        del {
            color: #6c757d;
            text-decoration: line-through;
        }

        /* Style untuk total garansi */
        .total-garansi {
            color: #28a745;
            font-weight: bold;
        }
        /* Style untuk item dengan diskon */
tr.has-discount {
    background-color: #f8fff8 !important;
}

tr.has-discount td {
    border-left: 3px solid #28a745 !important;
}

/* Style untuk text diskon */
.discount-text {
    color: #dc3545;
    font-weight: bold;
}

/* Style untuk harga coret */
.strikethrough {
    text-decoration: line-through;
    color: #6c757d;
}
    </style>
</head>
<body>
    <div class="header keep-together">
        <div class="header-left">
            <img src="<?php echo e(public_path('img/logo-ghava.png')); ?>" class="logo" alt="Logo">
            <div class="company-info">
                <div class="company-name">PT. GHAVA SHANKARA NUSANTARA</div>
                <div class="company-address">
                    <?php echo e($setting->alamat ?? 'Komplek BPI Logam Blok B2, Kec. Panyileukan, Kota Bandung'); ?><br>
                    Telp: <?php echo e($setting->telepon ?? '0812-220-033'); ?> | 
                    Email: <?php echo e($setting->email ?? 'marketing@dahana-boiler.com'); ?>

                </div>
            </div>
        </div>
        
        <div class="header-right">
            <div class="invoice-info">
                <div class="invoice-title">INVOICE SERVICE</div>
                <div class="invoice-detail">
                    <strong>No:</strong> <?php echo e($invoice->no_invoice); ?><br>
                    <strong>Tanggal:</strong> <?php echo e(\Carbon\Carbon::parse($invoice->tanggal)->format('d/m/Y')); ?>

                </div>
            </div>
        </div>
    </div>

    <div class="customer-info keep-together">
        <div><strong>Kepada:</strong></div>
        <div>
            <strong>
                <?php if(isset($invoice->member->kode_member) && $invoice->member->kode_member): ?>
                    <?php echo e($invoice->member->nama); ?> (<?php echo e($invoice->getMemberCodeWithPrefix()); ?>)
                <?php else: ?>
                    <?php echo e($invoice->member->nama); ?>

                <?php endif; ?>
            </strong>
        </div>
        <div><?php echo e($invoice->member->alamat); ?></div>
        <!-- <div>Telp: <?php echo e($invoice->member->telepon); ?></div> -->
    </div>

    <div class="service-info keep-together">
        <div><strong>Keperluan Service:</strong> 
            <?php echo e($invoice->jenis_service); ?>

            <?php if($invoice->service_lanjutan_ke > 0): ?>
                <strong>(Service lanjutan ke-<?php echo e($invoice->service_lanjutan_ke); ?>)</strong>
            <?php endif; ?>
        </div>
        <?php if($invoice->id_invoice_sebelumnya && $invoice->invoiceSebelumnya): ?>
            <div><strong>Invoice Sebelumnya:</strong> 
                <?php echo e($invoice->invoiceSebelumnya->no_invoice); ?> 
                (<?php echo e(\Carbon\Carbon::parse($invoice->invoiceSebelumnya->tanggal)->format('d/m/Y')); ?>)
            </div>
        <?php endif; ?>
        <?php if($invoice->keterangan_service): ?>
            <div><strong>Keterangan:</strong> <?php echo e($invoice->keterangan_service); ?></div>
        <?php endif; ?>
        <?php if($invoice->tanggal_mulai_service && $invoice->tanggal_selesai_service): ?>
        <div><strong>Periode Service:</strong> 
            <?php echo e(\Carbon\Carbon::parse($invoice->tanggal_mulai_service)->format('d/m/Y')); ?> - 
            <?php echo e(\Carbon\Carbon::parse($invoice->tanggal_selesai_service)->format('d/m/Y')); ?>

        </div>
        <?php endif; ?>
        <?php if(isset($invoice->mesinCustomer) && $invoice->mesinCustomer->produk->count() > 0): ?>
            <div><strong>Service Mesin:</strong> 
                <?php echo e($invoice->mesinCustomer->produk->pluck('nama_produk')->implode(', ')); ?>

            </div>
        <?php endif; ?>
        <?php if($invoice->is_preview ?? false): ?>
        <div style="color: #e74c3c; font-weight: bold; margin-top: 5px;">
            ⓘ Dokumen ini adalah preview. Invoice akan resmi setelah disimpan.
        </div>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="30%">Deskripsi</th>
                    <th width="20%">Keterangan</th>
                    <th width="8%" class="text-center">Qty</th>
                    <th width="10%" class="text-center">Satuan</th>
                    <th width="12%" class="text-right">Harga (Rp)</th>
                    <th width="12%" class="text-right">Diskon (Rp)</th>
                    <th width="15%" class="text-right">Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="avoid-break">
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item->deskripsi); ?></td>
                    <td>
                        <?php if(isset($item->jenis_kendaraan) && $item->jenis_kendaraan && $item->tipe == 'ongkir'): ?>
                            Menggunakan <?php echo e($item->jenis_kendaraan); ?>

                        <?php else: ?>
                            <?php echo e($item->keterangan); ?>

                        <?php endif; ?>
                        <?php if(isset($item->is_sparepart) && $item->is_sparepart && isset($item->kode_sparepart)): ?>
                            <br><small class="text-muted">Kode: <?php echo e($item->kode_sparepart); ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($item->kuantitas); ?></td>
                    <td class="text-center"><?php echo e($item->satuan); ?></td>
                    <td class="text-right">
                        <?php if($item->diskon > 0): ?>
                            <small style="text-decoration: line-through; color: #999;">
                                <?php echo e(number_format($item->harga, 0, ',', '.')); ?>

                            </small><br>
                            <?php echo e(number_format($item->harga_setelah_diskon, 0, ',', '.')); ?>

                        <?php else: ?>
                            <?php echo e(number_format($item->harga, 0, ',', '.')); ?>

                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php if($item->diskon > 0): ?>
                            -<?php echo e(number_format($item->diskon, 0, ',', '.')); ?>

                        <?php else: ?>
                            0
                        <?php endif; ?>
                    </td>
                    <td class="text-right"><?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <?php if($invoice->diskon > 0): ?>
                <tr>
                    <td colspan="7" class="text-right"><strong>Total Sebelum Diskon</strong></td>
                    <td class="text-right">
                        <del><?php echo e(number_format($invoice->total + $invoice->diskon, 0, ',', '.')); ?></del>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="text-right"><strong>Diskon</strong></td>
                    <td class="text-right"><?php echo e(number_format($invoice->diskon, 0, ',', '.')); ?></td>
                </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td colspan="7" class="text-right">
                        <strong>
                            <?php if($invoice->is_garansi): ?>
                            TOTAL (GARANSI)
                            <?php else: ?>
                            TOTAL
                            <?php endif; ?>
                        </strong>
                    </td>
                    <td class="text-right">
                        <strong>
                            <?php if($invoice->is_garansi): ?>
                            <span style="color: #28a745;">
                                <del><?php echo e(number_format($invoice->total, 0, ',', '.')); ?></del>
                            </span>
                            <?php else: ?>
                            <?php echo e(number_format($invoice->total, 0, ',', '.')); ?>

                            <?php endif; ?>
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer keep-together">
        <?php if(!$invoice->is_garansi): ?>
        <div class="payment-deadline" style="margin-bottom: 15px; padding: 8px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
            <small style="font-size: 10px;">
                <strong>* Catatan:</strong> Batas terakhir pembayaran adalah 7 hari setelah selesai service. 
                Invoice ini harus dilunasi paling lambat tanggal 
                <strong><?php echo e(\Carbon\Carbon::parse($invoice->tanggal_selesai_service)->addDays(7)->format('d/m/Y')); ?></strong>.
            </small>
        </div>
        <?php endif; ?>
        <?php if($invoice->tanggal_service_berikutnya): ?>
        <div class="service-berikutnya-info" style="margin-top: 10px; padding: 8px; background-color: #e8f4fd; border: 1px solid #b8daff; border-radius: 4px;">
            <strong>Service Berikutnya Dijadwalkan:</strong> 
            <?php echo e(\Carbon\Carbon::parse($invoice->tanggal_service_berikutnya)->format('d F Y')); ?>

            <?php if($invoice->keterangan_service_berikutnya): ?>
            <br><small><?php echo e($invoice->keterangan_service_berikutnya); ?></small>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="signature">
            <div class="signature-box">
                <div>Hormat Kami</div>
                <div style="margin-bottom: 0px;">admin</div>
                <div style="margin-bottom: 0px;">
                    <img src="<?php echo e(public_path('img/tiktik.png')); ?>" alt="Tanda Tangan" style="height: 40px; width: auto;">
                </div>
                <div>Tiktik Atikasari</div>
            </div>
        </div>
        <?php if(!$invoice->is_garansi): ?>
        <div class="bank-info">
            <strong>TRANSFER REKENING KE:</strong><br>
            <strong>Atas nama:</strong> PT. Ghava Shankara Nusantara<br>
            <strong>Bank:</strong> BCA<br>
            <strong>No Rekening:</strong> 6395860988
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\service_management\invoice\print.blade.php ENDPATH**/ ?>