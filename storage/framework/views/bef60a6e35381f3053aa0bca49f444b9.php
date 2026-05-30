<?php
    use Illuminate\Support\Facades\Route;
    $current = Route::currentRouteName();
    $user = auth()->user();
    
    // Redirect to login if user is not authenticated
    if (!$user) {
        header('Location: ' . route('login'));
        exit;
    }

    // Load menu structure from config
    $sidebarMenus = config('sidebar_menu');

    // Filter menus based on user permissions
    $menus = [];
    $modules = [];
    
    foreach ($sidebarMenus as $menuName => $menuData) {
        $filteredItems = [];
        foreach ($menuData['items'] as $item) {
            $permissions = $item['permissions'] ?? [];
            $routeUrl = $item['route'] === '#' ? '#' : route($item['route']);
            
            // Check if user has any of the required permissions
            $hasPermission = $user->hasRole('super_admin') || empty($permissions);
            if (!$hasPermission) {
                foreach ($permissions as $perm) {
                    if ($user->hasPermission($perm)) {
                        $hasPermission = true;
                        break;
                    }
                }
            }
            
            if ($hasPermission) {
                $menuItem = [$item['name'], $routeUrl];
                
                // Handle submenu if exists
                if (isset($item['submenu'])) {
                    $filteredSubmenu = [];
                    foreach ($item['submenu'] as $subItem) {
                        $subPermissions = $subItem['permissions'] ?? [];
                        $subRouteUrl = $subItem['route'] === '#' ? '#' : route($subItem['route']);
                        
                        $hasSubPermission = $user->hasRole('super_admin') || empty($subPermissions);
                        if (!$hasSubPermission) {
                            foreach ($subPermissions as $perm) {
                                if ($user->hasPermission($perm)) {
                                    $hasSubPermission = true;
                                    break;
                                }
                            }
                        }
                        
                        if ($hasSubPermission) {
                            $filteredSubmenu[] = [$subItem['name'], $subRouteUrl];
                        }
                    }
                    
                    if (!empty($filteredSubmenu)) {
                        $menuItem[] = $filteredSubmenu; // Add submenu as third element
                    }
                }
                
                $filteredItems[] = $menuItem;
            }
        }
        
        // Only add menu if it has accessible items
        if (!empty($filteredItems)) {
            $menus[$menuName] = $filteredItems;
            $modules[] = [
                'name' => $menuName,
                'route' => $menuData['route'],
                'icon' => $menuData['icon'],
                'module' => $menuData['module']
            ];
        }
    }
?>

<aside
    class="fixed inset-y-0 left-0 z-40 w-80 bg-white/90 backdrop-blur border-r border-slate-200 shadow-sm lg:translate-x-0 transform transition-transform"
    :class="{'-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen}"
    x-on:keydown.escape.window="sidebarOpen=false"
>
    
    <div class="h-20 px-2 border-b border-slate-200 flex items-center">
        <div class="relative w-full">
            
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="block w-fit mx-auto" data-logo-link="true" onclick="handleLogoClick(event)">
                <img
                    src="<?php echo e(url(asset('img/logo_xx.png'))); ?>"
                    alt="MORRA"
                    class="h-16 w-auto object-contain select-none"
                    style="max-height: clamp(3rem, 4vw, 4rem);"
                    loading="lazy"
                />
                <span class="sr-only">Beranda</span>
            </a>
            
            <button class="absolute right-0 top-1/2 -translate-y-1/2 p-2 -mr-1 rounded hover:bg-slate-100 lg:hidden"
                    @click="sidebarOpen=false" aria-label="Tutup Sidebar">
                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'menu','class' => 'w-5 h-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'menu','class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
            </button>
        </div>
    </div>

    
    
    <nav class="px-0 py-0 space-y-0 overflow-y-auto h-[calc(100vh-80px)]" x-data="sidebarState">
        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                $active = $current === $m['route'];
                $menuId = str_replace(['/', ' ', '(', ')'], ['_', '_', '', ''], $m['name']);
            ?>

            <div data-menu-parent="<?php echo e($menuId); ?>"
                 class="border border-slate-200 rounded-xl shadow-card overflow-hidden <?php echo e($active ? 'ring-1 ring-primary-200 bg-primary-50/50' : 'bg-white'); ?>">
                
                
                <a href="<?php echo e(route($m['route'])); ?>"
                   class="flex items-center gap-2 px-0 py-2 hover:bg-slate-50 <?php echo e($active ? 'text-primary-900' : 'text-ink-900'); ?>">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $m['icon'],'class' => 'w-4 h-4 flex-shrink-0 '.e($active ? 'text-primary-700' : 'text-primary-600').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($m['icon']),'class' => 'w-4 h-4 flex-shrink-0 '.e($active ? 'text-primary-700' : 'text-primary-600').'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    <div class="flex-1 min-w-0 flex items-center justify-between gap-2">
                        <span class="font-medium text-sm truncate"><?php echo e($m['name']); ?></span>
                        <span class="text-slate-400 text-xs flex-shrink-0">Dashboard</span>
                    </div>
                </a>

                
                <button
                    @click="toggleMenu('<?php echo e($menuId); ?>')"
                    class="w-full flex items-center justify-between px-0 py-1.5 text-left text-xs border-t border-slate-200 hover:bg-slate-50 <?php echo e($active ? 'text-primary-800' : 'text-slate-600'); ?>"
                >
                    <span>Submenu</span>
                    <span :class="isExpanded('<?php echo e($menuId); ?>') ? 'rotate-90' : ''" class="transition-transform">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-right','class' => 'w-3 h-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'w-3 h-3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    </span>
                </button>

                
                <div x-show="isExpanded('<?php echo e($menuId); ?>')" x-collapse>
                    <ul class="py-1 px-2">
                        <?php $__currentLoopData = ($menus[$m['name']] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isDemo = $item[1] === '#';
                                $hasSubmenu = isset($item[2]) && is_array($item[2]);
                                $submenuId = $menuId . '_' . str_replace([' ', '&', '/', '(', ')'], ['_', '', '_', '', ''], $item[0]);
                            ?>

                            <li>
                                <?php if($hasSubmenu): ?>
                                    
                                    <div>
                                        <button 
                                            @click="toggleSubmenu('<?php echo e($submenuId); ?>')"
                                            class="flex items-center justify-between w-full px-2 py-1.5 rounded-lg text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 font-medium"
                                        >
                                            <span class="truncate"><?php echo e($item[0]); ?></span>
                                            <span :class="isSubmenuExpanded('<?php echo e($submenuId); ?>') ? 'rotate-90' : ''" class="transition-transform flex-shrink-0 ml-1">
                                                <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-right','class' => 'w-3 h-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'w-3 h-3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                                            </span>
                                        </button>
                                        
                                        
                                        <div x-show="isSubmenuExpanded('<?php echo e($submenuId); ?>')" x-collapse class="ml-3 mt-1">
                                            <ul class="space-y-0.5">
                                                <?php $__currentLoopData = $item[2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $isSubDemo = $subItem[1] === '#';
                                                    ?>
                                                    <li>
                                                        <?php if($isSubDemo): ?>
                                                            <button 
                                                                type="button"
                                                                class="block w-full px-2 py-1 rounded text-sm text-red-500 hover:bg-red-50 hover:text-red-600 text-left cursor-pointer truncate"
                                                                onclick="showDemoModal('<?php echo e($subItem[0]); ?>')"
                                                            >
                                                                <?php echo e($subItem[0]); ?>

                                                            </button>
                                                        <?php else: ?>
                                                            <a href="<?php echo e($subItem[1]); ?>"
                                                               class="block px-2 py-1 rounded text-sm text-slate-600 hover:bg-primary-50 hover:text-primary-700 truncate">
                                                                <?php echo e($subItem[0]); ?>

                                                            </a>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    
                                    <?php if($isDemo): ?>
                                        <button 
                                            type="button"
                                            class="block px-2 py-1.5 rounded-lg text-sm text-red-500 hover:bg-red-50 hover:text-red-600 w-full text-left cursor-pointer font-medium truncate"
                                            onclick="showDemoModal('<?php echo e($item[0]); ?>')"
                                        >
                                            <?php echo e($item[0]); ?>

                                        </button>
                                    <?php else: ?>
                                        <a href="<?php echo e($item[1]); ?>"
                                        class="block px-2 py-1.5 rounded-lg text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 font-medium truncate">
                                            <?php echo e($item[0]); ?>

                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

</aside>

<!-- Modal DEMO
<div 
    x-data
    x-show="$store.demoModal.open"
    x-transition.opacity.duration.200ms
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
    @click.self="$store.demoModal.hide()"
>
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md text-center transform transition-all scale-100"
         x-transition.scale.origin.center>
        <h2 class="text-lg font-semibold mb-3 text-slate-800" x-text="$store.demoModal.title"></h2>
        <p class="text-sm text-slate-600 mb-5 leading-relaxed">
            Fitur ini tidak ditampilkan karena halaman ini versi <span class="font-semibold text-red-600">DEMO</span>.<br>
            Silahkan hubungi developer untuk akses penuh.
        </p>
        <div class="flex justify-center gap-3">
            <button 
                @click="$store.demoModal.hide()" 
                class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium">
                Tutup
            </button>
            <a :href="'https://wa.me/6285795483498?text=' + encodeURIComponent('Halo developer, saya ingin mengakses fitur ' + $store.demoModal.title)"
               target="_blank"
               class="px-4 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white font-medium">
                Hubungi via WhatsApp
            </a>
        </div>
    </div>
</div> -->

<script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('demoModal', {
                open: false,
                title: '',
                show(title) {
                    this.title = title;
                    this.open = true;
                },
                hide() {
                    this.open = false;
                }
            });

            // Sidebar state management component
            Alpine.data('sidebarState', () => ({
                expandedMenus: [],
                expandedSubmenus: [],

                init() {
                    // Load saved state from localStorage
                    const saved = localStorage.getItem('sidebar_expanded_menus');
                    if (saved) {
                        try {
                            this.expandedMenus = JSON.parse(saved);
                        } catch (e) {
                            this.expandedMenus = [];
                        }
                    }

                    const savedSubmenus = localStorage.getItem('sidebar_expanded_submenus');
                    if (savedSubmenus) {
                        try {
                            this.expandedSubmenus = JSON.parse(savedSubmenus);
                        } catch (e) {
                            this.expandedSubmenus = [];
                        }
                    }

                    // Auto-expand menu containing active route
                    this.expandActiveMenu();
                },

                expandActiveMenu() {
                    const currentPath = window.location.pathname;
                    const activeMenuItem = document.querySelector(`a[href="${currentPath}"]`);

                    if (activeMenuItem) {
                        const parentMenu = activeMenuItem.closest('[data-menu-parent]');
                        if (parentMenu) {
                            const menuId = parentMenu.dataset.menuParent;
                            if (!this.expandedMenus.includes(menuId)) {
                                this.expandedMenus.push(menuId);
                                this.saveState();
                            }
                        }
                    }
                },

                toggleMenu(menuId) {
                    const index = this.expandedMenus.indexOf(menuId);
                    if (index > -1) {
                        this.expandedMenus.splice(index, 1);
                    } else {
                        this.expandedMenus.push(menuId);
                    }
                    this.saveState();
                },

                toggleSubmenu(submenuId) {
                    const index = this.expandedSubmenus.indexOf(submenuId);
                    if (index > -1) {
                        this.expandedSubmenus.splice(index, 1);
                    } else {
                        this.expandedSubmenus.push(submenuId);
                    }
                    this.saveSubmenuState();
                },

                isExpanded(menuId) {
                    return this.expandedMenus.includes(menuId);
                },

                isSubmenuExpanded(submenuId) {
                    return this.expandedSubmenus.includes(submenuId);
                },

                saveState() {
                    localStorage.setItem('sidebar_expanded_menus', JSON.stringify(this.expandedMenus));
                },

                saveSubmenuState() {
                    localStorage.setItem('sidebar_expanded_submenus', JSON.stringify(this.expandedSubmenus));
                }
            }));
        });

        function showDemoModal(title) {
            Alpine.store('demoModal').show(title);
        }
    </script>

<?php /**PATH C:\xampp\htdocs\hm\resources\views/components/sidebar.blade.php ENDPATH**/ ?>