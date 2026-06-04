<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan Sederhana</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        .header h3 {
            margin: 3px 0;
            font-size: 14px;
        }
        .info {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f9f9f9;
            border-radius: 3px;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 9px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
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
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .total-row {
            font-weight: bold;
            background-color: #e8f4ff;
        }
        .summary-table {
            margin-top: 15px;
            width: 100%;
        }
        .summary-table td {
            border: none;
            padding: 3px;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #999;
            padding: 20px;
        }
        .status-lunas {
            color: green;
            font-weight: bold;
        }
        .status-kredit {
            color: red;
            font-weight: bold;
        }
        
        /* Page break handling */
        @media print {
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PENJUALAN SEDERHANA</h2>
        <h3><?php echo e($setting->nama_perusahaan ?? 'Toko'); ?></h3>
        <p><?php echo e($setting->alamat ?? ''); ?> | <?php echo e($setting->telepon ?? ''); ?></p>
    </div>

    <div class="info">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 15%;"><strong>Periode:</strong></td>
                <td style="border: none; width: 35%;"><?php echo e(\Carbon\Carbon::parse($start_date)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($end_date)->format('d/m/Y')); ?></td>
                <td style="border: none; width: 15%;"><strong>Outlet:</strong></td>
                <td style="border: none; width: 35%;"><?php echo e($outlet->nama_outlet ?? 'Semua Outlet'); ?></td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Tanggal Cetak:</strong></td>
                <td style="border: none;"><?php echo e(date('d/m/Y H:i:s')); ?></td>
                <td style="border: none;"><strong>Jumlah Transaksi:</strong></td>
                <td style="border: none;"><?php echo e($penjualan->count()); ?> transaksi</td>
            </tr>
        </table>
    </div>

    <?php if($penjualan->count() > 0): ?>
        <?php
            $grand_total_item = 0;
            $grand_total_harga = 0;
            $grand_total_bayar = 0;
            $grand_total_diterima = 0;
        ?>

        <table>
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="8%">No. Transaksi</th>
                    <th width="10%">Tanggal</th>
                    <th width="12%">Outlet</th>
                    <th width="12%">Customer</th>
                    <th width="6%">Total Item</th>
                    <th width="10%">Total Harga</th>
                    <th width="6%">Diskon</th>
                    <th width="10%">Total Bayar</th>
                    <th width="10%">Diterima</th>
                    <th width="8%">Kembali</th>
                    <th width="8%">Status</th>
                    <th width="6%">Kasir</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $penjualan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $grand_total_item += $item->total_item;
                    $grand_total_harga += $item->total_harga;
                    $grand_total_bayar += $item->bayar;
                    $grand_total_diterima += $item->diterima;
                ?>
                <tr>
                    <td class="text-center"><?php echo e($key + 1); ?></td>
                    <td class="text-center">#<?php echo e($item->id_penjualan); ?></td>
                    <td class="text-center"><?php echo e(\Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')); ?></td>
                    <td><?php echo e($item->outlet->nama_outlet ?? '-'); ?></td>
                    <td><?php echo e($item->member->nama ?? 'Customer Umum'); ?></td>
                    <td class="text-center"><?php echo e($item->total_item); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($item->total_harga, 0, ',', '.')); ?></td>
                    <td class="text-center"><?php echo e($item->diskon); ?>%</td>
                    <td class="text-right">Rp <?php echo e(number_format($item->bayar, 0, ',', '.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($item->diterima, 0, ',', '.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($item->diterima - $item->bayar, 0, ',', '.')); ?></td>
                    <td class="text-center">
                        <?php if($item->diterima >= $item->bayar): ?>
                            <span class="status-lunas">LUNAS</span>
                        <?php else: ?>
                            <span class="status-kredit">KREDIT</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($item->user->name ?? '-'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- TOTAL SUMMARY -->
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>TOTAL KESELURUHAN</strong></td>
                    <td class="text-center"><strong><?php echo e($grand_total_item); ?></strong></td>
                    <td class="text-right"><strong>Rp <?php echo e(number_format($grand_total_harga, 0, ',', '.')); ?></strong></td>
                    <td></td>
                    <td class="text-right"><strong>Rp <?php echo e(number_format($grand_total_bayar, 0, ',', '.')); ?></strong></td>
                    <td class="text-right"><strong>Rp <?php echo e(number_format($grand_total_diterima, 0, ',', '.')); ?></strong></td>
                    <td class="text-right"><strong>Rp <?php echo e(number_format($grand_total_diterima - $grand_total_bayar, 0, ',', '.')); ?></strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

    <?php else: ?>
        <div class="no-data">
            <p>Tidak ada data penjualan untuk periode yang dipilih.</p>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Dicetak oleh: <?php echo e(auth()->user()->name); ?> | <?php echo e(date('d F Y H:i:s')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\penjualan\cetak_sederhana.blade.php ENDPATH**/ ?>