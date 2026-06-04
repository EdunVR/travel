<!-- Detail Modal Content -->
<div class="modal-header bg-blue-600 text-white">
    <h5 class="modal-title">
        <i class="bx bx-file-blank mr-2"></i>
        Detail Kontra Bon - <?php echo e($kontraBon->no_kontra_bon); ?>

    </h5>
</div>

<div class="modal-body">
    <div class="row">
        <!-- Left Column - Info Kontra Bon -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Informasi Kontra Bon</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="font-weight-medium">No. Kontra Bon</td>
                            <td>: <?php echo e($kontraBon->no_kontra_bon); ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium">Tanggal</td>
                            <td>: <?php echo e($kontraBon->created_at->format('d/m/Y H:i')); ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium">Customer</td>
                            <td>: <?php echo e($kontraBon->member->nama ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium">Outlet</td>
                            <td>: <?php echo e($kontraBon->outlet->nama_outlet ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium">Jatuh Tempo</td>
                            <td>: <?php echo e(date('d/m/Y', strtotime($kontraBon->tanggal_jatuh_tempo))); ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium">Status</td>
                            <td>: 
                                <?php if($kontraBon->status == 'selesai'): ?>
                                    <span class="badge badge-success">Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium">Dibuat Oleh</td>
                            <td>: <?php echo e($kontraBon->user->name ?? '-'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column - Summary -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Ringkasan Pembayaran</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="50%" class="font-weight-medium">Total Pembayaran</td>
                            <td class="text-right">: Rp <?php echo e(number_format($kontraBon->total_pembayaran, 0, ',', '.')); ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium">Total Piutang Dibayar</td>
                            <td class="text-right">: Rp <?php echo e(number_format($kontraBon->details->sum('nominal'), 0, ',', '.')); ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-medium">Sisa ke Saldo</td>
                            <td class="text-right">: Rp <?php echo e(number_format($kontraBon->total_pembayaran - $kontraBon->details->sum('nominal'), 0, ',', '.')); ?></td>
                        </tr>
                    </table>
                    
                    <?php if($kontraBon->keterangan): ?>
                    <div class="mt-3">
                        <strong>Keterangan:</strong>
                        <p class="text-muted mb-0"><?php echo e($kontraBon->keterangan); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Piutang yang Dibayar -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 font-weight-bold">Detail Piutang yang Dibayar</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Tanggal Penjualan</th>
                                    <th width="20%">No. Penjualan</th>
                                    <th width="25%">Total Penjualan</th>
                                    <th width="25%">Dibayar</th>
                                    <th width="5%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $kontraBon->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="text-center"><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($detail->penjualan->created_at->format('d/m/Y') ?? '-'); ?></td>
                                    <td><?php echo e($detail->penjualan->kode_penjualan ?? 'TRX00' . $detail->id_penjualan); ?></td>
                                    <td class="text-right">Rp <?php echo e(number_format($detail->penjualan->total ?? 0, 0, ',', '.')); ?></td>
                                    <td class="text-right">Rp <?php echo e(number_format($detail->nominal, 0, ',', '.')); ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-success badge-sm">Dibayar</span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bx bx-info-circle bx-lg mb-2"></i><br>
                                        Tidak ada detail pembayaran
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if($kontraBon->details->count() > 0): ?>
                            <tfoot class="thead-light">
                                <tr>
                                    <th colspan="4" class="text-right">Total Dibayar:</th>
                                    <th class="text-right">Rp <?php echo e(number_format($kontraBon->details->sum('nominal'), 0, ',', '.')); ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="bx bx-x"></i> Tutup
    </button>
    <button type="button" class="btn btn-success" onclick="showPrintModal(<?php echo e($kontraBon->id_kontra_bon); ?>)">
        <i class="bx bx-printer"></i> Print
    </button>
</div><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\kontrabon\modals\detail.blade.php ENDPATH**/ ?>