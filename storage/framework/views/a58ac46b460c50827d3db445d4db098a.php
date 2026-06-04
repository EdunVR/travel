<div class="modal fade" id="userModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded-xl border-0 shadow-xl">
            <div class="modal-header border-b border-slate-200 bg-slate-50">
                <h5 class="modal-title font-semibold" id="modalTitle">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="userForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="userId" name="id">
                
                <div class="modal-body p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                   required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                No. Telepon
                            </label>
                            <input type="text" 
                                   id="phone" 
                                   name="phone" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Password <span id="passwordNote" class="text-slate-500 text-xs">(Kosongkan jika tidak diubah)</span>
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select id="role_id" 
                                    name="role_id" 
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                    required>
                                <option value="">Pilih Role</option>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role->id); ?>"><?php echo e($role->display_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Status
                            </label>
                            <div class="flex items-center h-10">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           checked 
                                           class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                                    <span class="ms-3 text-sm font-medium text-slate-700">User Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanda Tangan
                        </label>
                        <div class="border border-slate-300 rounded-lg p-4">
                            <input type="file" 
                                   id="signature" 
                                   name="signature" 
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <p class="text-xs text-slate-500 mt-1">Upload gambar tanda tangan (JPG, PNG, GIF, max 2MB)</p>
                            
                            <!-- Preview Container -->
                            <div id="signaturePreview" class="mt-3 hidden">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Preview:</label>
                                <div class="border border-slate-200 rounded-lg p-2 bg-slate-50">
                                    <img id="signatureImage" src="" alt="Signature Preview" class="max-h-32 max-w-full object-contain">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Akses Outlet
                        </label>
                        <div class="border border-slate-300 rounded-lg p-3 max-h-48 overflow-y-auto">
                            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center mb-2">
                                <input type="checkbox" 
                                       id="outlet_<?php echo e($outlet->id_outlet); ?>" 
                                       name="outlet_ids[]" 
                                       value="<?php echo e($outlet->id_outlet); ?>"
                                       class="outlet-checkbox w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                                <label for="outlet_<?php echo e($outlet->id_outlet); ?>" class="ml-2 text-sm text-slate-700">
                                    <?php echo e($outlet->nama_outlet); ?>

                                </label>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Pilih outlet yang dapat diakses user ini</p>
                    </div>
                </div>
                
                <div class="modal-footer border-t border-slate-200 bg-slate-50">
                    <button type="button" 
                            class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50" 
                            data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\user-management\users\modal.blade.php ENDPATH**/ ?>