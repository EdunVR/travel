<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Verifikasi Pembayaran']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Verifikasi Pembayaran']); ?>
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="history.back()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm transition-colors">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Kembali
                </button>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Verifikasi Pembayaran</h1>
                    <p class="text-gray-600 mt-1">Verifikasi pembayaran yang masuk dari jamaah</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-amber-100 text-amber-800 px-4 py-2 rounded-lg font-semibold">
                    <i class="fas fa-clock mr-2"></i>
                    <?php echo e($pendingCount); ?> Menunggu Verifikasi
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Upload
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Booking
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jamaah
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Paket
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bukti Transfer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($payment->created_at->format('d M Y H:i')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <?php echo e($payment->booking->booking_code); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <?php echo e($payment->booking->jamaah->full_name); ?>

                            </div>
                            <div class="text-sm text-gray-500">
                                <?php echo e($payment->booking->jamaah->phone_number); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?php echo e($payment->booking->travelPackage->package_name); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">
                                Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?>

                            </div>
                            <div class="text-xs text-gray-500">
                                <?php echo e($payment->payment_method); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($payment->bukti_transfer): ?>
                            <button onclick="viewProof('<?php echo e(url(Storage::url($payment->bukti_transfer))); ?>')" 
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fas fa-image mr-1"></i>
                                Lihat Bukti
                            </button>
                            <?php else: ?>
                            <span class="text-gray-400 text-sm">Tidak ada</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($payment->verification_status === 'pending_verification'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                <i class="fas fa-clock mr-1"></i>
                                Menunggu
                            </span>
                            <?php elseif($payment->verification_status === 'verified'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Terverifikasi
                            </span>
                            <?php elseif($payment->verification_status === 'rejected'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Ditolak
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if($payment->verification_status === 'pending_verification'): ?>
                            <div class="flex items-center gap-2">
                                <button onclick="verifyPayment(<?php echo e($payment->id); ?>)" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-medium transition">
                                    <i class="fas fa-check mr-1"></i>
                                    Verifikasi
                                </button>
                                <button onclick="rejectPayment(<?php echo e($payment->id); ?>)" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-medium transition">
                                    <i class="fas fa-times mr-1"></i>
                                    Tolak
                                </button>
                            </div>
                            <?php else: ?>
                            <div class="text-xs text-gray-500">
                                <?php if($payment->verified_by): ?>
                                Oleh: <?php echo e($payment->verifiedBy->name ?? 'Admin'); ?><br>
                                <?php endif; ?>
                                <?php echo e($payment->verified_at ? $payment->verified_at->format('d M Y H:i') : ''); ?>

                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-lg font-medium">Tidak ada pembayaran yang perlu diverifikasi</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($payments->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($payments->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal untuk melihat bukti transfer — posisi top-center -->
<div id="proofModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-start justify-center p-4 pt-16 overflow-y-auto">
    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[80vh] overflow-auto">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
            <h3 class="text-lg font-semibold text-gray-900">Bukti Transfer</h3>
            <button onclick="closeProofModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4">
            <img id="proofImage" src="" alt="Bukti Transfer" class="w-full h-auto">
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Default Swal config — semua dialog muncul di top-center
const SwalTop = Swal.mixin({
    position: 'top',
    customClass: {
        popup: 'swal2-top-popup',
    }
});

function viewProof(imageUrl) {
    document.getElementById('proofImage').src = imageUrl;
    document.getElementById('proofModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeProofModal() {
    document.getElementById('proofModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function verifyPayment(paymentId) {
    SwalTop.fire({
        title: 'Verifikasi Pembayaran?',
        text: 'Pembayaran akan diverifikasi dan jamaah akan menerima notifikasi',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Verifikasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            SwalTop.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`<?php echo e(url('admin/inventaris/travel/payment')); ?>/${paymentId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    SwalTop.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pembayaran telah diverifikasi',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => window.location.reload());
                } else {
                    SwalTop.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan' });
                }
            })
            .catch(() => {
                SwalTop.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat memproses' });
            });
        }
    });
}

function rejectPayment(paymentId) {
    SwalTop.fire({
        title: 'Tolak Pembayaran?',
        text: 'Jamaah akan diminta untuk upload ulang bukti transfer',
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Alasan Penolakan',
        inputPlaceholder: 'Masukkan alasan penolakan...',
        inputAttributes: { 'aria-label': 'Alasan penolakan' },
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Tolak',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) return 'Alasan penolakan harus diisi!';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            SwalTop.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`<?php echo e(url('admin/inventaris/travel/payment')); ?>/${paymentId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ rejection_reason: result.value })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    SwalTop.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pembayaran telah ditolak',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => window.location.reload());
                } else {
                    SwalTop.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan' });
                }
            })
            .catch(() => {
                SwalTop.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat memproses' });
            });
        }
    });
}

// Close proof modal when clicking backdrop
document.getElementById('proofModal').addEventListener('click', function(e) {
    if (e.target === this) closeProofModal();
});
</script>
<?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\payment\verify-payments.blade.php ENDPATH**/ ?>