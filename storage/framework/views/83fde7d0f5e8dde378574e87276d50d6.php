<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 16px;
        }
        .info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 6px;
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
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
        .total-row {
            font-weight: bold;
            background-color: #e8f4ff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PENJUALAN</h2>
        <h3><?php echo e($setting->nama_perusahaan ?? 'Toko'); ?></h3>
        <p><?php echo e($setting->alamat ?? ''); ?></p>
    </div>

    <div class="info">
        <p><strong>Periode:</strong> <?php echo e(\Carbon\Carbon::parse($start_date)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($end_date)->format('d/m/Y')); ?></p>
        <?php if($outlet): ?>
        <p><strong>Outlet:</strong> <?php echo e($outlet->nama_outlet); ?></p>
        <?php else: ?>
        <p><strong>Outlet:</strong> Semua Outlet</p>
        <?php endif; ?>
        <p><strong>Tanggal Cetak:</strong> <?php echo e(date('d/m/Y H:i:s')); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="15%">Outlet</th>
                <th width="15%">Customer</th>
                <th width="8%">Total Item</th>
                <th width="12%">Total Harga</th>
                <th width="8%">Diskon</th>
                <th width="12%">Total Bayar</th>
                <th width="10%">Kasir</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $total_item = 0;
                $total_harga = 0;
                $total_bayar = 0;
            ?>
            
            <?php $__currentLoopData = $penjualan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $total_item += $item->total_item;
                $total_harga += $item->total_harga;
                $total_bayar += $item->bayar;
            ?>
            <tr>
                <td class="text-center"><?php echo e($key + 1); ?></td>
                <td><?php echo e(\Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')); ?></td>
                <td><?php echo e($item->outlet->nama_outlet ?? '-'); ?></td>
                <td><?php echo e($item->member->nama ?? 'Customer Umum'); ?></td>
                <td class="text-center"><?php echo e($item->total_item); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($item->total_harga, 0, ',', '.')); ?></td>
                <td class="text-center"><?php echo e($item->diskon); ?>%</td>
                <td class="text-right">Rp <?php echo e(number_format($item->bayar, 0, ',', '.')); ?></td>
                <td><?php echo e($item->user->name ?? ''); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <?php if($penjualan->count() > 0): ?>
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-center"><strong><?php echo e($total_item); ?></strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($total_harga, 0, ',', '.')); ?></strong></td>
                <td></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($total_bayar, 0, ',', '.')); ?></strong></td>
                <td></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($penjualan->count() === 0): ?>
    <p class="text-center" style="margin-top: 20px; font-style: italic; color: #999;">
        Tidak ada data penjualan untuk periode yang dipilih.
    </p>
    <?php endif; ?>

    <div class="footer">
        <p>Dicetak oleh: <?php echo e(auth()->user()->name); ?></p>
        <p><?php echo e(date('d F Y H:i:s')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\penjualan\cetak.blade.php ENDPATH**/ ?>