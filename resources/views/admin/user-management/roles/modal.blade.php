<div class="modal fade" id="roleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content rounded-xl border-0 shadow-xl">
            <div class="modal-header border-b border-slate-200 bg-slate-50">
                <h5 class="modal-title font-semibold" id="modalTitle">Tambah Role</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="roleForm">
                @csrf
                <input type="hidden" id="roleId" name="id">
                
                <div class="modal-body p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Nama Role <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="roleName" 
                                   name="name" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Display Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="roleDisplayName" 
                                   name="display_name" 
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                   required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea id="roleDescription" 
                                  name="description" 
                                  rows="2" 
                                  class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">
                            Permissions (Modul > Submenu > CRUD)
                        </label>
                        <div class="border border-slate-200 rounded-lg p-4 max-h-96 overflow-y-auto">
                            @php
                                // Load menu structure from config (same as sidebar)
                                $sidebarMenus = config('sidebar_menu');
                                
                                // Group permissions by module and menu for easy lookup
                                $permissionsByModuleMenu = [];
                                foreach ($permissions as $perm) {
                                    $module = $perm->module;
                                    $menu = $perm->menu;
                                    if (!isset($permissionsByModuleMenu[$module])) {
                                        $permissionsByModuleMenu[$module] = [];
                                    }
                                    if (!isset($permissionsByModuleMenu[$module][$menu])) {
                                        $permissionsByModuleMenu[$module][$menu] = [];
                                    }
                                    $permissionsByModuleMenu[$module][$menu][] = $perm;
                                }
                            @endphp
                            
                            @foreach($sidebarMenus as $menuName => $menuData)
                            @php
                                $module = $menuData['module'];
                                $hasPermissions = isset($permissionsByModuleMenu[$module]);
                            @endphp
                            
                            @if($hasPermissions)
                            <div class="mb-4 pb-4 border-b border-slate-200 last:border-0">
                                {{-- Module Header --}}
                                <div class="flex items-center mb-3 bg-slate-50 p-2 rounded-lg">
                                    <input type="checkbox" 
                                           class="select-module mr-2" 
                                           data-module="{{ $module }}" 
                                           id="module_{{ $loop->index }}">
                                    <label for="module_{{ $loop->index }}" class="font-bold text-slate-900 text-sm">
                                        📦 {{ $menuName }}
                                    </label>
                                </div>
                                
                                {{-- Submenus from sidebar config --}}
                                @foreach($menuData['items'] as $item)
                                @php
                                    // Extract menu identifier from route or name
                                    $menuIdentifier = str_replace(['.index', 'admin.', 'finance.', 'sdm.', 'pembelian.', 'admin.penjualan.', 'admin.crm.', 'admin.inventaris.', 'admin.service.', 'admin.investor.', 'admin.produksi.produksi.'], '', $item['route']);
                                    $menuIdentifier = str_replace('.', '-', $menuIdentifier);
                                    
                                    // Special handling for specific routes
                                    if ($item['route'] === 'admin.penjualan.pos.index') {
                                        $menuIdentifier = 'pos';
                                    }
                                    
                                    // SDM special mappings (route name -> database menu name)
                                    if ($item['route'] === 'sdm.kepegawaian.index') {
                                        $menuIdentifier = 'karyawan';
                                    }
                                    if ($item['route'] === 'sdm.attendance.index') {
                                        $menuIdentifier = 'absensi';
                                    }
                                    
                                    // Service Management special mappings - FIX: Remove service prefix
                                    if ($item['route'] === 'admin.service.invoice.index') {
                                        $menuIdentifier = 'invoice';
                                    }
                                    if ($item['route'] === 'admin.service.history.index') {
                                        $menuIdentifier = 'history';
                                    }
                                    if ($item['route'] === 'admin.service.ongkir.index') {
                                        $menuIdentifier = 'ongkir';
                                    }
                                    if ($item['route'] === 'admin.service.mesin.index') {
                                        $menuIdentifier = 'mesin';
                                    }
                                    
                                    // Additional service route handling for any missed routes
                                    if (str_starts_with($item['route'], 'admin.service.')) {
                                        $parts = explode('.', $item['route']);
                                        if (count($parts) >= 3) {
                                            $menuIdentifier = $parts[2]; // Get the third part (invoice, history, ongkir, mesin)
                                        }
                                    }
                                    
                                    // Find permissions for this submenu
                                    $submenuPerms = [];
                                    if (isset($permissionsByModuleMenu[$module])) {
                                        // Try exact match first
                                        if (isset($permissionsByModuleMenu[$module][$menuIdentifier])) {
                                            $submenuPerms = $permissionsByModuleMenu[$module][$menuIdentifier];
                                        } else {
                                            // Try fuzzy match
                                            foreach ($permissionsByModuleMenu[$module] as $menuKey => $perms) {
                                                // Normalize both strings for comparison
                                                $normalizedMenuKey = str_replace(['-', '_', '.'], '', strtolower($menuKey));
                                                $normalizedIdentifier = str_replace(['-', '_', '.'], '', strtolower($menuIdentifier));
                                                
                                                if ($normalizedMenuKey === $normalizedIdentifier || 
                                                    str_contains($normalizedMenuKey, $normalizedIdentifier) || 
                                                    str_contains($normalizedIdentifier, $normalizedMenuKey)) {
                                                    $submenuPerms = $perms;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                
                                @if(count($submenuPerms) > 0)
                                <div class="ml-6 mb-3">
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" 
                                               class="select-menu mr-2" 
                                               data-module="{{ $module }}"
                                               data-menu="{{ $menuIdentifier }}" 
                                               id="menu_{{ $module }}_{{ $loop->parent->index }}_{{ $loop->index }}">
                                        <label for="menu_{{ $module }}_{{ $loop->parent->index }}_{{ $loop->index }}" class="font-semibold text-slate-700 text-sm">
                                            📄 {{ $item['name'] }}
                                        </label>
                                    </div>
                                    
                                    {{-- CRUD Actions --}}
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 ml-6">
                                        @foreach($submenuPerms as $permission)
                                        <div class="flex items-start">
                                            <input type="checkbox" 
                                                   class="permission-checkbox mt-0.5 mr-2" 
                                                   id="perm_{{ $permission->id }}" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   data-module="{{ $module }}"
                                                   data-menu="{{ $menuIdentifier }}">
                                            <label for="perm_{{ $permission->id }}" class="text-xs text-slate-600">
                                                {{ ucfirst($permission->action) }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            @endif
                            @endforeach
                        </div>
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
