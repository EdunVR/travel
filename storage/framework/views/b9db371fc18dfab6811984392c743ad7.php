<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> Role Management <?php $__env->endSlot(); ?>

    <div x-data="roleManagement()" x-init="init()" class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Role & Permission Management</h1>
                <p class="text-slate-600 text-sm">Kelola role dan permission sistem</p>
            </div>

            <button @click="openCreateModal()" 
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 h-10 hover:bg-primary-700">
                <i class='bx bx-plus'></i> Tambah Role
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="role in roles" :key="role.id">
                <div class="bg-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-slate-900" x-text="role.display_name"></h3>
                            <p class="text-sm text-slate-500 mt-1" x-text="role.description || '-'"></p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="editRole(role)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                <i class='bx bx-edit'></i>
                            </button>
                            <template x-if="!isProtectedRole(role.name)">
                                <button @click="deleteRole(role)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-200">
                        <div class="flex items-center gap-2">
                            <i class='bx bx-user text-slate-400'></i>
                            <span class="text-sm text-slate-600">
                                <span class="font-semibold" x-text="role.users_count"></span> users
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class='bx bx-shield text-slate-400'></i>
                            <span class="text-sm text-slate-600">
                                <span class="font-semibold" x-text="role.permissions?.length || 0"></span> permissions
                            </span>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-slate-600 uppercase mb-2">Permissions</p>
                        <div class="flex flex-wrap gap-1">
                            <template x-for="(perms, group) in groupPermissions(role.permissions)" :key="group">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-slate-100 text-slate-700"
                                      x-text="group + ' (' + perms.length + ')'"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <?php echo $__env->make('admin.user-management.roles.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Set global data for roles.js
        window.rolesData = <?php echo json_encode($roles, 15, 512) ?>;
        window.permissionsData = <?php echo json_encode($permissions, 15, 512) ?>;
        window.baseUrl = '<?php echo e(url('')); ?>';
    </script>
    
    <!-- Load roles.js synchronously BEFORE Alpine.js -->
    <script src="<?php echo e(asset('js/roles.js')); ?>?v=<?php echo e(time()); ?>"></script>
    
    <!-- Verify roleManagement function is available -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof roleManagement === 'undefined') {
                console.error('❌ roleManagement function not found in roles.js');
                
                // Create a minimal fallback function
                window.roleManagement = function() {
                    return {
                        roles: window.rolesData || [],
                        permissions: window.permissionsData || [],
                        init() {
                            console.log('⚠️ Using fallback roleManagement - please check roles.js');
                        },
                        isProtectedRole() { return false; },
                        groupPermissions() { return {}; },
                        openCreateModal() { console.log('Modal function not available'); },
                        editRole() { console.log('Edit function not available'); },
                        deleteRole() { console.log('Delete function not available'); }
                    }
                };
                console.log('✅ Fallback roleManagement function created');
            } else {
                console.log('✅ roleManagement function found in roles.js');
            }
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\user-management\roles\index.blade.php ENDPATH**/ ?>