<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota POS - <?php echo e($posSale->no_transaksi); ?></title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            font-size: 14px;
            padding: 2px;
            vertical-align: top;
        }
        table.data td,
        table.data th {
            border: 1px solid #000;
            padding: 3px;
        }
        table.data {
            border-collapse: collapse;
            width: 100%;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .nama-perusahaan {
            font-size: 18px;
            font-weight: bold;
        }
        .header {
            margin-bottom: 10px;
        }
        .header .barcode img {
            width: 50px;
            height: 20px;
        }
        .body {
            margin: 10px 0;
        }
        .footer {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }
        .logo-box {
            border: 2px solid #000;
            padding: 5px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }
        .total-section {
            margin-top: 10px;
        }
        .date-trxid {
            margin-top: 10px;
        }
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 14px;
                margin: 0;
                padding: 10px;
            }
            th, td {
                padding: 2px 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <!-- Kolom Kiri: Logo Box -->
                <td style="width: 20%;">
                    <div class="logo-box">
                        <?php echo e(strtoupper($companySettings['company_name'] ?? 'PERUSAHAAN')); ?>

                    </div>
                </td>
                <!-- Kolom Tengah: Informasi Perusahaan -->
                <td style="width: 60%; text-align: center;">
                    <span style="font-size: 16px; font-weight: bold;">TELP: <?php echo e($companySettings['company_phone'] ?? '-'); ?></span><br>
                    <span><?php echo e($posSale->is_bon ? 'BON PENJUALAN' : 'NOTA PEMBELIAN'); ?></span>
                </td>
                <!-- Kolom Kanan: Barcode -->
                <td style="width: 20%; text-align: right;">
                    <?php
                        $barcode = 'POS' . str_pad($posSale->id, 6, '0', STR_PAD_LEFT);
                        $barcodeOptions = [
                            'height' => 15,
                            'width' => 1,
                        ];
                    ?>
                    <img src="data:image/png;base64,<?php echo e(DNS1D::getBarcodePNG($barcode, 'C39', $barcodeOptions['width'], $barcodeOptions['height'])); ?>" alt="barcode">
                </td>
            </tr>
        </table>
    </div>

    <!-- Body -->
    <div class="body">
        <table>
            <tr>
                <!-- Kolom Kiri -->
                <td style="width: 50%;">
                    <table class="date-trxid">
                        <tr>
                            <td>Tanggal</td>
                            <td>: <?php echo e(\Carbon\Carbon::parse($posSale->tanggal)->format('d/m/Y H:i')); ?></td>
                        </tr>
                        <tr>
                            <td>Nama Customer</td>
                            <td>: <?php echo e($posSale->member->nama ?? 'Pelanggan Umum'); ?></td>
                        </tr>
                        <?php if($posSale->is_bon && isset($piutang)): ?>
                        <tr>
                            <td>Tempo</td>
                            <td>: <?php echo e(\Carbon\Carbon::parse($piutang->tanggal_jatuh_tempo)->format('d/m/Y')); ?></td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <td>Tempo</td>
                            <td>: -</td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </td>
                <!-- Kolom Kanan -->
                <td style="width: 50%;">
                    <table class="date-trxid">
                        <tr>
                            <td>TrxID</td>
                            <td>: <?php echo e($posSale->no_transaksi); ?></td>
                        </tr>
                        <tr>
                            <td>Operator</td>
                            <td>: <?php echo e($posSale->user->name ?? 'System'); ?></td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>: <?php echo e($posSale->is_bon ? 'BON' : 'LUNAS'); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabel Detail Penjualan -->
    <table class="data" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Jumlah</th>
                <th>Nama</th>
                <th>Harga Satuan</th>
                <th>Diskon</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $posSale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($key+1); ?></td>
                    <td class="text-right"><?php echo e($item->kuantitas); ?></td>
                    <td><?php echo e($item->nama_produk); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($item->harga, 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e($posSale->diskon_persen > 0 ? $posSale->diskon_persen . '%' : '-'); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <!-- Total Section -->
    <div class="total-section">
        <table>
            <tr>
                <td colspan="4" class="text-right"><b>Total Harga</b></td>
                <td class="text-right"><b>Rp <?php echo e(number_format($posSale->subtotal, 0, ',', '.')); ?></b></td>
            </tr>
            <?php if($posSale->is_bon && $posSale->member && isset($piutang)): ?>
            <tr>
                <td colspan="4" class="text-right"><b>Hutang Customer</b></td>
                <td class="text-right"><b>Rp <?php echo e(number_format($piutang->sisa_piutang ?? 0, 0, ',', '.')); ?></b></td>
            </tr>
            <?php endif; ?>
            <?php if($posSale->total_diskon > 0): ?>
            <tr>
                <td colspan="4" class="text-right"><b>Diskon</b></td>
                <td class="text-right"><b>Rp <?php echo e(number_format($posSale->total_diskon, 0, ',', '.')); ?></b></td>
            </tr>
            <?php endif; ?>
            <?php if($posSale->ppn > 0): ?>
            <tr>
                <td colspan="4" class="text-right"><b>PPN 10%</b></td>
                <td class="text-right"><b>Rp <?php echo e(number_format($posSale->ppn, 0, ',', '.')); ?></b></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td colspan="4" class="text-right"><b>Total Bayar</b></td>
                <td class="text-right"><b>Rp <?php echo e(number_format($posSale->total, 0, ',', '.')); ?></b></td>
            </tr>
            <?php if(!$posSale->is_bon): ?>
            <tr>
                <td colspan="4" class="text-right"><b>Diterima</b></td>
                <td class="text-right"><b>Rp <?php echo e(number_format($posSale->jumlah_bayar, 0, ',', '.')); ?></b></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><b>Kembali</b></td>
                <td class="text-right"><b>Rp <?php echo e(number_format($posSale->kembalian, 0, ',', '.')); ?></b></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <?php if($posSale->catatan): ?>
    <div style="margin-top: 10px;">
        <b>Catatan:</b> <?php echo e($posSale->catatan); ?>

    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <table>
            <tr>
                <!-- Kolom Kiri: Tanda Terima -->
                <td style="width: 33%;">
                    <b>Tanda Terima</b><br>
                    ( <?php echo e($posSale->member->nama ?? 'Pelanggan Umum'); ?> )
                </td>
                <!-- Kolom Tengah: Pesan -->
                <td style="width: 34%; text-align: center;">
                    <b>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</b>
                </td>
                <!-- Kolom Kanan: Hormat Kami -->
                <td style="width: 33%; text-align: right;">
                    <b>Hormat Kami</b><br>
                    ( <?php echo e($posSale->user->name ?? 'System'); ?> )
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.onload = function() {
            // Auto print only if autoprint parameter is present
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                window.print();
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\pos\nota_besar.blade.php ENDPATH**/ ?>