<div class="space-y-6">
    <!-- Header Info -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Informasi Pre Order</h4>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kode:</span>
                        <span class="font-medium"><?php echo e($preorder->kode_preorder); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal:</span>
                        <span><?php echo e($preorder->tanggal->format('d/m/Y')); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs rounded-full <?php echo e($preorder->status_badge); ?>">
                            <?php echo e(ucfirst($preorder->status)); ?>

                        </span>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Informasi Customer</h4>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama:</span>
                        <span><?php echo e($preorder->customer->nama ?? '-'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Telepon:</span>
                        <span><?php echo e($preorder->customer->telepon ?? '-'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span><?php echo e($preorder->customer->email ?? '-'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div>
        <h4 class="font-medium text-gray-900 mb-3">Item Pre Order</h4>
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $preorder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-4 py-2">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($item->deskripsi); ?></div>
                            <?php if($item->product): ?>
                            <div class="text-xs text-gray-500"><?php echo e($item->product->nama_produk); ?></div>
                            <?php endif; ?>
                            
                            <!-- Additional Costs Display -->
                            <?php if($item->material_instalasi_biaya > 0 || $item->pemasangan_pelatihan_biaya > 0 || $item->ongkos_kirim_biaya > 0): ?>
                            <div class="mt-2 space-y-1">
                                <div class="text-xs font-medium text-blue-600">Biaya Tambahan:</div>
                                
                                <?php if($item->material_instalasi_biaya > 0): ?>
                                <div class="text-xs text-gray-600 pl-2">
                                    • Material Instalasi: Rp <?php echo e(number_format($item->material_instalasi_biaya, 0, ',', '.')); ?> / <?php echo e($item->material_instalasi_satuan); ?>

                                    <?php if($item->material_instalasi_keterangan): ?>
                                    <br><span class="text-gray-500 italic"><?php echo e($item->material_instalasi_keterangan); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($item->pemasangan_pelatihan_biaya > 0): ?>
                                <div class="text-xs text-gray-600 pl-2">
                                    • Pemasangan & Pelatihan: Rp <?php echo e(number_format($item->pemasangan_pelatihan_biaya, 0, ',', '.')); ?> / <?php echo e($item->pemasangan_pelatihan_satuan); ?>

                                    <?php if($item->pemasangan_pelatihan_keterangan): ?>
                                    <br><span class="text-gray-500 italic"><?php echo e($item->pemasangan_pelatihan_keterangan); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($item->ongkos_kirim_biaya > 0): ?>
                                <div class="text-xs text-gray-600 pl-2">
                                    • Ongkos Kirim: Rp <?php echo e(number_format($item->ongkos_kirim_biaya, 0, ',', '.')); ?> / <?php echo e($item->ongkos_kirim_satuan); ?>

                                    <?php if($item->ongkos_kirim_komponen && count($item->ongkos_kirim_komponen) > 0): ?>
                                    <br><span class="text-gray-500">Komponen: 
                                        <?php $__currentLoopData = $item->formatted_ongkos_kirim_komponen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $komponen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($komponen['nama']); ?> (<?php echo e($komponen['formatted_biaya']); ?>)<?php echo e(!$loop->last ? ', ' : ''); ?>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="text-xs font-medium text-blue-700 pl-2">
                                    Total Biaya Tambahan: Rp <?php echo e(number_format($item->calculateTotalBiayaTambahan(), 0, ',', '.')); ?>

                                </div>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-900"><?php echo e($item->qty); ?></td>
                        <td class="px-4 py-2 text-sm text-gray-900">Rp <?php echo e(number_format($item->harga, 0, ',', '.')); ?></td>
                        <td class="px-4 py-2">
                            <div class="text-sm font-medium text-gray-900">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></div>
                            <?php if($item->calculateTotalBiayaTambahan() > 0): ?>
                            <div class="text-xs text-blue-600">+ Rp <?php echo e(number_format($item->calculateTotalBiayaTambahan(), 0, ',', '.')); ?></div>
                            <div class="text-xs font-medium text-green-600">= Rp <?php echo e(number_format($item->total_with_additional_costs, 0, ',', '.')); ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal Produk:</span>
                <span>Rp <?php echo e(number_format($preorder->subtotal, 0, ',', '.')); ?></span>
            </div>
            <?php if($preorder->total_additional_costs > 0): ?>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Total Biaya Tambahan:</span>
                <span class="text-blue-600">Rp <?php echo e(number_format($preorder->total_additional_costs, 0, ',', '.')); ?></span>
            </div>
            <div class="flex justify-between text-sm font-medium">
                <span class="text-gray-600">Subtotal:</span>
                <span>Rp <?php echo e(number_format($preorder->subtotal_with_additional_costs, 0, ',', '.')); ?></span>
            </div>
            <?php else: ?>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal:</span>
                <span>Rp <?php echo e(number_format($preorder->subtotal, 0, ',', '.')); ?></span>
            </div>
            <?php endif; ?>
            <?php if($preorder->diskon > 0): ?>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Diskon:</span>
                <span class="text-red-600">- Rp <?php echo e(number_format($preorder->diskon, 0, ',', '.')); ?></span>
            </div>
            <?php endif; ?>
            <?php if($preorder->pajak > 0): ?>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Pajak:</span>
                <span>Rp <?php echo e(number_format($preorder->pajak, 0, ',', '.')); ?></span>
            </div>
            <?php endif; ?>
            <hr class="border-gray-300">
            <div class="flex justify-between font-medium">
                <span>Total:</span>
                <span class="text-lg">Rp <?php echo e(number_format($preorder->grand_total_with_additional_costs, 0, ',', '.')); ?></span>
            </div>
            <?php if($preorder->dp_amount > 0): ?>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">DP Dibayar:</span>
                <span class="text-green-600">Rp <?php echo e(number_format($preorder->dp_amount, 0, ',', '.')); ?></span>
            </div>
            <div class="flex justify-between text-sm font-medium">
                <span class="text-gray-600">Sisa Pembayaran:</span>
                <span class="text-orange-600">Rp <?php echo e(number_format($preorder->grand_total_with_additional_costs - $preorder->dp_amount, 0, ',', '.')); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Notes -->
    <?php if($preorder->catatan): ?>
    <div>
        <h4 class="font-medium text-gray-900 mb-2">Catatan</h4>
        <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700">
            <?php echo e($preorder->catatan); ?>

        </div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="flex gap-3 pt-4 border-t border-gray-200">
        <?php if($preorder->status === 'penawaran'): ?>
        <button onclick="updateStatus(<?php echo e($preorder->id); ?>, 'invoice')" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
            Ubah ke Invoice
        </button>
        <?php elseif($preorder->status === 'invoice'): ?>
        <button onclick="updateStatus(<?php echo e($preorder->id); ?>, 'lunas')" 
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
            Tandai Lunas
        </button>
        <?php endif; ?>
        
        <div class="flex gap-2">
            <button onclick="printDocument('penawaran', <?php echo e($preorder->id); ?>)" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                Print Penawaran
            </button>
            <?php if($preorder->status !== 'penawaran'): ?>
            <button onclick="printDocument('invoice', <?php echo e($preorder->id); ?>)" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                Print Invoice
            </button>
            <?php endif; ?>
            <?php if($preorder->status === 'lunas'): ?>
            <button onclick="printDocument('kwitansi', <?php echo e($preorder->id); ?>)" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                Print Kwitansi
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function printDocument(type, preOrderId) {
    const url = `/admin/penjualan/preorders/${preOrderId}/print/${type}`;
    window.open(url, '_blank');
}
</script><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\pre-orders\partials\detail.blade.php ENDPATH**/ ?>