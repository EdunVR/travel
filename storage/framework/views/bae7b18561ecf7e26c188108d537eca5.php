<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Permintaan Barang - <?php echo e($permintaan->nomor_permintaan); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table .label {
            width: 150px;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .items-table .number {
            text-align: center;
            width: 30px;
        }
        .items-table .currency {
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-amount {
            font-size: 14px;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-draft { background-color: #f3f4f6; color: #374151; }
        .status-aktif { background-color: #dbeafe; color: #1e40af; }
        .status-disetujui { background-color: #dcfce7; color: #166534; }
        .status-ditolak { background-color: #fee2e2; color: #dc2626; }
        .priority-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .priority-rendah { background-color: #f3f4f6; color: #374151; }
        .priority-normal { background-color: #dbeafe; color: #1e40af; }
        .priority-tinggi { background-color: #fef3c7; color: #d97706; }
        .priority-urgent { background-color: #fee2e2; color: #dc2626; }
        .approval-section {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-box {
            display: inline-block;
            width: 200px;
            text-align: center;
            margin-right: 50px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name"><?php echo e(config('app.name', 'MORRA ERP')); ?></div>
        <div class="document-title">PERMINTAAN BARANG</div>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">Nomor Permintaan:</td>
                <td><?php echo e($permintaan->nomor_permintaan); ?></td>
                <td class="label">Tanggal:</td>
                <td><?php echo e($permintaan->created_at->format('d/m/Y')); ?></td>
            </tr>
            <tr>
                <td class="label">Judul:</td>
                <td><?php echo e($permintaan->judul); ?></td>
                <td class="label">Status:</td>
                <td>
                    <span class="status-badge status-<?php echo e($permintaan->status); ?>">
                        <?php echo e(strtoupper($permintaan->status)); ?>

                    </span>
                </td>
            </tr>
            <tr>
                <td class="label">Outlet:</td>
                <td><?php echo e($permintaan->outlet->nama ?? '-'); ?></td>
                <td class="label">Prioritas:</td>
                <td>
                    <span class="priority-badge priority-<?php echo e($permintaan->prioritas); ?>">
                        <?php echo e(strtoupper($permintaan->prioritas)); ?>

                    </span>
                </td>
            </tr>
            <tr>
                <td class="label">Pemohon:</td>
                <td><?php echo e($permintaan->user->name ?? '-'); ?></td>
                <td class="label">Tanggal Dibutuhkan:</td>
                <td><?php echo e($permintaan->tanggal_dibutuhkan ? $permintaan->tanggal_dibutuhkan->format('d/m/Y') : '-'); ?></td>
            </tr>
            <?php if($permintaan->deskripsi): ?>
            <tr>
                <td class="label">Deskripsi:</td>
                <td colspan="3"><?php echo e($permintaan->deskripsi); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="number">No</th>
                <th>Tipe</th>
                <th>Nama Item</th>
                <th>Spesifikasi</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga Estimasi</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $permintaan->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="number"><?php echo e($index + 1); ?></td>
                <td><?php echo e(strtoupper($item->tipe_item)); ?></td>
                <td>
                    <?php echo e($item->nama_item); ?>

                    <?php if($item->produk): ?>
                        <br><small>SKU: <?php echo e($item->produk->sku); ?></small>
                    <?php endif; ?>
                    <?php if($item->bahan): ?>
                        <br><small>Kode: <?php echo e($item->bahan->kode); ?></small>
                    <?php endif; ?>
                </td>
                <td><?php echo e($item->spesifikasi ?: '-'); ?></td>
                <td class="number"><?php echo e(number_format($item->qty, 2)); ?></td>
                <td><?php echo e($item->satuan); ?></td>
                <td class="currency"><?php echo e(number_format($item->estimasi_harga, 0, ',', '.')); ?></td>
                <td class="currency"><?php echo e(number_format($item->total_estimasi, 0, ',', '.')); ?></td>
            </tr>
            <?php if($item->catatan): ?>
            <tr>
                <td colspan="8" style="font-style: italic; font-size: 10px; background-color: #f9f9f9;">
                    <strong>Catatan:</strong> <?php echo e($item->catatan); ?>

                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="text-align: right; font-weight: bold;">TOTAL ESTIMASI:</td>
                <td class="currency total-amount">Rp <?php echo e(number_format($permintaan->estimasi_budget, 0, ',', '.')); ?></td>
            </tr>
        </tfoot>
    </table>

    <?php if($permintaan->status === 'disetujui' || $permintaan->status === 'ditolak'): ?>
    <div class="approval-section">
        <h4>Informasi Persetujuan</h4>
        <table class="info-table">
            <tr>
                <td class="label"><?php echo e($permintaan->status === 'disetujui' ? 'Disetujui' : 'Ditolak'); ?> Oleh:</td>
                <td><?php echo e($permintaan->approver->name ?? '-'); ?></td>
                <td class="label">Tanggal:</td>
                <td><?php echo e($permintaan->approved_at ? $permintaan->approved_at->format('d/m/Y H:i') : '-'); ?></td>
            </tr>
            <?php if($permintaan->catatan_approval): ?>
            <tr>
                <td class="label">Catatan:</td>
                <td colspan="3"><?php echo e($permintaan->catatan_approval); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($permintaan->alasan_penolakan): ?>
            <tr>
                <td class="label">Alasan Penolakan:</td>
                <td colspan="3"><?php echo e($permintaan->alasan_penolakan); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php endif; ?>

    <div class="signature-section">
        <div class="signature-box">
            <div>Pemohon</div>
            <div class="signature-line"><?php echo e($permintaan->user->name ?? ''); ?></div>
        </div>
        
        <?php if($permintaan->status === 'disetujui' || $permintaan->status === 'ditolak'): ?>
        <div class="signature-box">
            <div><?php echo e($permintaan->status === 'disetujui' ? 'Menyetujui' : 'Menolak'); ?></div>
            <div class="signature-line"><?php echo e($permintaan->approver->name ?? ''); ?></div>
        </div>
        <?php else: ?>
        <div class="signature-box">
            <div>Menyetujui</div>
            <div class="signature-line">(...........................)</div>
        </div>
        <?php endif; ?>
    </div>

    <div style="margin-top: 30px; font-size: 10px; color: #666; text-align: center;">
        Dokumen ini digenerate otomatis pada <?php echo e(now()->format('d/m/Y H:i:s')); ?>

    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\supply-chain\permintaan-barang\pdf.blade.php ENDPATH**/ ?>