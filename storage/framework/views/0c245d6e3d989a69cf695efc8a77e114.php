<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Kontra Bon PDF</title>
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
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 3px;
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
            width: 60px;
            height: 20px;
        }
        .body {
            margin: 10px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }
        .section-title {
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .logo-box {
            border: 2px solid #000;
            padding: 5px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <!-- Logo -->
                <td style="width: 20%;">
                    <div class="logo-box">
                        <?php echo e(strtoupper($setting->nama_perusahaan)); ?>

                    </div>
                </td>
                <!-- Nama Perusahaan -->
                <td style="width: 60%; text-align: center;">
                    <div class="nama-perusahaan"><?php echo e($setting->nama_perusahaan); ?></div>
                    <?php echo e($setting->alamat); ?><br>
                    Telp: <?php echo e($setting->telepon); ?>

                </td>
                <!-- Barcode -->
                <td style="width: 20%; text-align: right;">
                    <?php
                        $barcode = 'KB00' . $kontraBon->id_kontra_bon;
                        $barcodeOptions = ['height' => 20, 'width' => 1.2];
                    ?>
                    <img src="data:image/png;base64,<?php echo e(DNS1D::getBarcodePNG($barcode, 'C39', $barcodeOptions['width'], $barcodeOptions['height'])); ?>" alt="barcode">
                </td>
            </tr>
        </table>
    </div>

    <!-- Body -->
    <div class="body">
        <!-- Info Kontra Bon -->
        <table>
            <tr>
                <td style="width: 50%;">
                    <table>
                        <tr><td>Kode Kontra Bon</td><td>: <?php echo e($kontraBon->kode_kontra_bon); ?></td></tr>
                        <tr><td>Tanggal</td><td>: <?php echo e(tanggal_indonesia($kontraBon->tanggal)); ?></td></tr>
                        <tr><td>Jatuh Tempo</td><td>: <?php echo e(tanggal_indonesia($kontraBon->tanggal_jatuh_tempo)); ?></td></tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table>
                        <tr><td>Customer</td><td>: <?php echo e($kontraBon->member->nama); ?></td></tr>
                        <tr><td>Pembayaran</td><td>: <?php echo e(format_uang($kontraBon->total_pembayaran)); ?></td></tr>
                        <tr><td>Saldo</td><td>: <?php echo e(format_uang($kontraBon->member->saldo)); ?></td></tr>
                        <tr><td>Total Hutang</td><td>: <?php echo e(format_uang($totalHutang)); ?></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Tabel Hutang Belum Dibayar -->
        <?php if($startDate && $endDate): ?>
        <div class="section-title">Data Hutang yang Ditagihkan ( <?php echo e(tanggal_indonesia($startDate)); ?> s/d <?php echo e(tanggal_indonesia($endDate)); ?> )</div>
        <?php else: ?>
        <div class="section-title">Data Hutang yang Ditagihkan</div>
        <?php endif; ?>
        <table class="data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>TrxID</th>
                    <th class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $piutangBelumLunas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $piutang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center"><?php echo e($key + 1); ?></td>
                    <td><?php echo e(tanggal_indonesia($piutang->penjualan->created_at)); ?></td>
                    <td>TRX00<?php echo e($piutang->id_penjualan); ?></td>
                    <td class="text-right"><?php echo e(format_uang($piutang->sisa_piutang)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data hutang yang belum dibayar.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Tabel Hutang Sudah Dilunasi -->
        <div class="section-title">Data Hutang yang Sudah Dilunasi</div>
        <table class="data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>TrxID</th>
                    <th class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $kontraBon->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php if($detail->penjualan): ?>
                    <tr>
                        <td class="text-center"><?php echo e($key + 1); ?></td>
                        <td><?php echo e(tanggal_indonesia($detail->penjualan->created_at)); ?></td>
                        <td>TRX00<?php echo e($detail->penjualan->id_penjualan); ?></td>
                        <td class="text-right"><?php echo e(format_uang($detail->nominal)); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data hutang yang sudah dilunasi</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <table>
            <tr>
                <td style="width: 33%;">
                    <b>Pengirim</b><br><br>
                    ( <?php echo e($kontraBon->member->nama); ?> )
                </td>
                <td style="width: 34%;" class="text-center">
                    <b>Tanda Terima Kontra Bon</b>
                </td>
                <td style="width: 33%;" class="text-right">
                    <b>Penerima</b><br><br>
                    ( <?php echo e(auth()->user()->name); ?> )
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\kontra_bon\nota_besar_kontrabon.blade.php ENDPATH**/ ?>