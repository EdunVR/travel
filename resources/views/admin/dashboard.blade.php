{{-- resources/views/admin/dashboard.blade.php --}}
@php
    $user = auth()->user();
    $userOutlets = $user->outlets ?? collect();
    $defaultOutlet = session('outlet_id') ?? $userOutlets->first()->id_outlet ?? null;
    
    // Load menu structure from config and filter by permissions
    $sidebarMenus = config('sidebar_menu');
    $availableModules = [];
    
    foreach ($sidebarMenus as $menuName => $menuData) {
        $hasAccess = false;
        
        // Check if user has access to any item in this module
        foreach ($menuData['items'] as $item) {
            $permissions = $item['permissions'] ?? [];
            
            if ($user->hasRole('super_admin') || empty($permissions)) {
                $hasAccess = true;
                break;
            }
            
            foreach ($permissions as $perm) {
                if ($user->hasPermission($perm)) {
                    $hasAccess = true;
                    break 2;
                }
            }
        }
        
        if ($hasAccess) {
            $availableModules[] = [
                'name' => $menuName,
                'route' => $menuData['route'],
                'icon' => $menuData['icon'],
                'module' => $menuData['module']
            ];
        }
    }
@endphp

<x-layouts.admin-with-tabs :title="'Dashboard'">
    <div x-data="dashboardData({{ json_encode($userOutlets->pluck('id_outlet')->toArray()) }}, {{ $defaultOutlet }})" x-init="init()">
        {{-- Header --}}
        <section class="mb-8">
            <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">
                        Command Center
                    </h1>
                    <p class="text-slate-600 mt-1">Real-time business intelligence & analytics</p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    {{-- Outlet Filter with Checkbox --}}
                    @if($userOutlets->count() > 0)
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-2 bg-white/80 backdrop-blur-sm min-w-[200px] justify-between shadow-sm hover:shadow-md transition-all">
                            <span x-text="getSelectedOutletText()">Pilih Outlet</span>
                            <i class='bx bx-chevron-down transition-transform' :class="{'rotate-180': open}"></i>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition class="absolute top-full left-0 mt-2 w-full bg-white/95 backdrop-blur-sm border border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto">
                            <div class="p-3">
                                @foreach($userOutlets as $outlet)
                                <label class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" 
                                           :checked="selectedOutlets.includes({{ $outlet->id_outlet }})"
                                           @change="toggleOutlet({{ $outlet->id_outlet }})"
                                           class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm">{{ $outlet->nama_outlet }}</span>
                                </label>
                                @endforeach
                            </div>
                            <div class="border-t border-slate-200 p-3">
                                <button @click="selectAllOutlets()" class="text-xs text-blue-600 hover:text-blue-700 mr-4 font-medium">
                                    Pilih Semua
                                </button>
                                <button @click="clearAllOutlets()" class="text-xs text-slate-500 hover:text-slate-700 font-medium">
                                    Hapus Semua
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <button @click="refreshData()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 transition-all flex items-center gap-2 whitespace-nowrap shadow-sm hover:shadow-md">
                        <i class='bx bx-refresh text-lg' :class="{'bx-spin': loading}"></i>
                        <span class="font-medium">Refresh</span>
                    </button>
                </div>
            </div>

            {{-- KPI Cards - Futuristic Design --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm">
                                <i class='bx bx-line-chart text-2xl'></i>
                            </div>
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Penjualan</p>
                                <p class="text-xs text-blue-200">Bulan ini</p>
                            </div>
                        </div>
                        <div class="flex items-end gap-3">
                            <div class="text-2xl font-bold" x-text="formatCurrency(stats.sales.value)">Loading...</div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium" 
                                  :class="stats.sales.growth >= 0 ? 'bg-green-400/20 text-green-100' : 'bg-red-400/20 text-red-100'"
                                  x-text="(stats.sales.growth >= 0 ? '+' : '') + stats.sales.growth + '%'"></span>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 opacity-10">
                        <i class='bx bx-trending-up text-6xl'></i>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm">
                                <i class='bx bx-task text-2xl'></i>
                            </div>
                            <div>
                                <p class="text-emerald-100 text-sm font-medium">Pesanan Diproses</p>
                                <p class="text-xs text-emerald-200">Transaksi aktif</p>
                            </div>
                        </div>
                        <div class="flex items-end gap-3">
                            <div class="text-2xl font-bold" x-text="stats.orders.value">Loading...</div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium" 
                                  :class="stats.orders.growth >= 0 ? 'bg-green-400/20 text-green-100' : 'bg-red-400/20 text-red-100'"
                                  x-text="(stats.orders.growth >= 0 ? '+' : '') + stats.orders.growth + '%'"></span>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 opacity-10">
                        <i class='bx bx-package text-6xl'></i>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm">
                                <i class='bx bx-error-circle text-2xl'></i>
                            </div>
                            <div>
                                <p class="text-amber-100 text-sm font-medium">Piutang Pending</p>
                                <p class="text-xs text-amber-200">Perlu tindak lanjut</p>
                            </div>
                        </div>
                        <div class="flex items-end gap-3">
                            <div class="text-2xl font-bold" x-text="stats.returns.value">Loading...</div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium" 
                                  :class="stats.returns.growth <= 0 ? 'bg-green-400/20 text-green-100' : 'bg-red-400/20 text-red-100'"
                                  x-text="(stats.returns.growth >= 0 ? '+' : '') + stats.returns.growth + '%'"></span>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 opacity-10">
                        <i class='bx bx-time text-6xl'></i>
                    </div>
                </div>
            </div>

            {{-- Analytics Cards - Compact Design --}}
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-2xl bg-white/80 backdrop-blur-sm border border-slate-200 p-6 shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                                <i class='bx bx-trending-up text-xl'></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-900">Tren Penjualan</h3>
                                <p class="text-xs text-slate-500">7 hari terakhir</p>
                            </div>
                        </div>
                    </div>
                    <div class="h-20 flex items-end justify-between px-1">
                        <template x-for="(item, index) in salesTrend" :key="index">
                            <div class="flex flex-col items-center flex-1 group">
                                <div class="text-[10px] text-slate-400 mb-1 group-hover:text-blue-600 transition-colors" x-text="item.day"></div>
                                <div class="w-3/4 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t transition-all duration-300 hover:from-blue-600 hover:to-blue-500" 
                                     :style="`height: ${Math.max(4, (item.value / salesTrendMax) * 50)}px`"></div>
                            </div>
                        </template>
                        <div x-show="salesTrend.length === 0" class="w-full text-center text-xs text-slate-400 py-4">
                            Tidak ada data
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/80 backdrop-blur-sm border border-slate-200 p-6 shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white">
                                <i class='bx bx-bulb text-xl'></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-900">Smart Insights</h3>
                                <p class="text-xs text-slate-500">AI recommendations</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3 max-h-20 overflow-y-auto">
                        <template x-for="insight in insights.slice(0, 2)" :key="insight.title">
                            <div class="p-3 rounded-xl text-xs border-l-4 transition-all hover:shadow-sm" 
                                 :class="{
                                     'bg-green-50 text-green-800 border-green-400': insight.type === 'success',
                                     'bg-blue-50 text-blue-800 border-blue-400': insight.type === 'info',
                                     'bg-amber-50 text-amber-800 border-amber-400': insight.type === 'warning',
                                     'bg-red-50 text-red-800 border-red-400': insight.type === 'danger'
                                 }">
                                <div class="font-semibold mb-1" x-text="insight.title"></div>
                                <div class="text-[10px] opacity-80" x-text="insight.message"></div>
                            </div>
                        </template>
                        <div x-show="insights.length === 0" class="text-xs text-slate-400 text-center py-3">
                            Tidak ada insight saat ini
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Module Grid - Futuristic Design --}}
        <section class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Application Modules</h2>
                    <p class="text-slate-600 text-sm">Integrated business applications</p>
                </div>
                <div class="text-xs text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                    {{ count($availableModules) }} modules available
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($availableModules as $module)
                
                @if($module['module'] === 'inventaris')
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-package')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-blue-600 hover:to-blue-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-indigo-100 border-4 border-indigo-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-package text-4xl text-blue-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-blue-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                
                @elseif($module['module'] === 'crm')
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-group')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-purple-600 hover:to-purple-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-purple-100 border-4 border-purple-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-group text-4xl text-purple-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-purple-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                
                @elseif($module['module'] === 'finance')
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-wallet')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-emerald-600 hover:to-emerald-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-emerald-100 border-4 border-emerald-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-wallet text-4xl text-emerald-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-emerald-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                
                @elseif($module['module'] === 'sales')
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-receipt')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-orange-600 hover:to-orange-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-orange-100 border-4 border-orange-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-receipt text-4xl text-orange-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-orange-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                
                @elseif($module['module'] === 'procurement')
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-truck')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-indigo-600 hover:to-indigo-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-indigo-100 border-4 border-indigo-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-truck text-4xl text-indigo-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-indigo-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                
                @elseif($module['module'] === 'production')
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-cog')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-500 to-red-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-red-600 hover:to-red-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-red-100 border-4 border-red-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-cog text-4xl text-red-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-red-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                
                @elseif($module['module'] === 'supply-chain')
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-network-chart')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-teal-600 hover:to-teal-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-teal-100 border-4 border-teal-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-network-chart text-4xl text-teal-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-teal-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                
                @elseif($module['module'] === 'hrm')
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-id-card')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-500 to-pink-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-pink-600 hover:to-pink-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-pink-100 border-4 border-pink-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-id-card text-4xl text-pink-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-pink-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                
                @elseif($module['module'] === 'service')
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-wrench')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-cyan-600 hover:to-cyan-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-cyan-100 border-4 border-cyan-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-wrench text-4xl text-cyan-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-cyan-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                
                @elseif($module['module'] === 'investor')
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-dollar-circle')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-amber-600 hover:to-amber-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-amber-100 border-4 border-amber-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-dollar-circle text-4xl text-amber-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-amber-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                
                @else
                {{-- Default/Sistem --}}
                <div @click="openModuleInTab('{{ route($module['route']) }}', '{{ $module['name'] }}', 'bx-cog')" 
                   class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-500 to-slate-600 p-6 text-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-[1.05] hover:from-slate-600 hover:to-slate-700 cursor-pointer">
                    
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="7" cy="7" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    
                    {{-- Content --}}
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            {{-- Icon with Background and Border --}}
                            <div class="relative">
                                <div class="p-4 rounded-2xl bg-slate-100 border-4 border-slate-200 group-hover:scale-110 transition-all duration-300 shadow-lg">
                                    <i class='bx bx-cog text-4xl text-slate-600'></i>
                                </div>
                                {{-- Glow effect --}}
                                <div class="absolute inset-0 rounded-2xl bg-slate-100 opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300"></div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class='bx bx-right-arrow-alt text-2xl'></i>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-xl mb-2 group-hover:text-white/90 transition-colors">
                                {{ $module['name'] }}
                            </h3>
                            <p class="text-sm text-white/80 group-hover:text-white/70 transition-colors">
                                Integrated {{ strtolower($module['name']) }} management
                            </p>
                        </div>
                        
                        {{-- Status Indicator --}}
                        <div class="absolute top-4 right-4">
                            <div class="w-3 h-3 rounded-full bg-green-400 animate-pulse shadow-lg"></div>
                        </div>
                    </div>
                    
                    {{-- Hover Effect --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </div>
                @endif
                
                @endforeach
            </div>
        </section>

        {{-- Quick Analytics Section --}}
        <section>
            <h2 class="text-xl font-semibold mb-4 text-slate-900">Quick Analytics</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Inventory Status --}}
                <div class="rounded-2xl bg-white/80 backdrop-blur-sm border border-slate-200 p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                                <i class='bx bx-package text-xl'></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-900">Inventory Status</h3>
                                <p class="text-xs text-slate-500">Real-time stock levels</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="text-center p-3 rounded-xl bg-green-50 border border-green-100">
                            <div class="text-lg font-bold text-green-700" x-text="inventory.stats.safe">0</div>
                            <div class="text-xs text-green-600">Safe</div>
                        </div>
                        <div class="text-center p-3 rounded-xl bg-amber-50 border border-amber-100">
                            <div class="text-lg font-bold text-amber-700" x-text="inventory.stats.low">0</div>
                            <div class="text-xs text-amber-600">Low</div>
                        </div>
                        <div class="text-center p-3 rounded-xl bg-red-50 border border-red-100">
                            <div class="text-lg font-bold text-red-700" x-text="inventory.stats.critical">0</div>
                            <div class="text-xs text-red-600">Critical</div>
                        </div>
                    </div>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        <template x-for="item in inventory.items.slice(0, 5)" :key="item.name">
                            <div class="flex items-center justify-between text-xs">
                                <span class="w-20 truncate font-medium" x-text="item.name"></span>
                                <div class="flex-1 mx-2">
                                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full transition-all" 
                                             :class="{
                                                 'bg-green-500': item.status === 'safe',
                                                 'bg-amber-500': item.status === 'low',
                                                 'bg-red-500': item.status === 'critical'
                                             }"
                                             :style="`width: ${item.percentage}%`"></div>
                                    </div>
                                </div>
                                <span class="w-12 text-right text-slate-600" x-text="item.stock"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Performance Metrics --}}
                <div class="rounded-2xl bg-white/80 backdrop-blur-sm border border-slate-200 p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white">
                                <i class='bx bx-trending-up text-xl'></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-900">Performance</h3>
                                <p class="text-xs text-slate-500">Production & efficiency</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600" x-text="production.target_achievement + '%'">0%</div>
                            <div class="text-xs text-slate-500">Target Achievement</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600" x-text="production.efficiency + '%'">0%</div>
                            <div class="text-xs text-slate-500">Efficiency Rate</div>
                        </div>
                    </div>
                    <div class="space-y-2 max-h-24 overflow-y-auto">
                        <template x-for="emp in employees.slice(0, 3)" :key="emp.name">
                            <div class="flex items-center text-xs">
                                <div class="w-16 truncate font-medium" x-text="emp.name"></div>
                                <div class="flex-1 mx-2">
                                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                                        <div class="bg-blue-500 h-1.5 rounded-full transition-all" :style="`width: ${emp.performance}%`"></div>
                                    </div>
                                </div>
                                <span class="w-8 text-right text-slate-600" x-text="emp.performance + '%'"></span>
                            </div>
                        </template>
                        <div x-show="employees.length === 0" class="text-center text-xs text-slate-400 py-2">
                            No employee data
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        function dashboardData(availableOutlets, defaultOutlet) {
            return {
                loading: false,
                initialized: false,
                availableOutlets: availableOutlets || [],
                selectedOutlets: defaultOutlet ? [defaultOutlet] : [],
                stats: {
                    sales: { value: 0, growth: 0 },
                    orders: { value: 0, growth: 0 },
                    returns: { value: 0, growth: 0 }
                },
                inventory: {
                    items: [],
                    stats: { safe: 0, low: 0, critical: 0 }
                },
                production: {
                    target_achievement: 0,
                    efficiency: 0
                },
                employees: [],
                insights: [],
                salesTrend: [],
                salesTrendMax: 1,

                init() {
                    if (this.initialized) return;
                    this.initialized = true;
                    
                    // Wait for DOM to be ready
                    this.$nextTick(() => {
                        this.loadAllData();
                    });
                },

                getSelectedOutletText() {
                    if (this.selectedOutlets.length === 0) {
                        return 'Pilih Outlet';
                    } else if (this.selectedOutlets.length === 1) {
                        const outlet = @json($userOutlets->keyBy('id_outlet'));
                        return outlet[this.selectedOutlets[0]]?.nama_outlet || 'Outlet';
                    } else if (this.selectedOutlets.length === this.availableOutlets.length) {
                        return 'Semua Outlet';
                    } else {
                        return `${this.selectedOutlets.length} Outlet Dipilih`;
                    }
                },

                toggleOutlet(outletId) {
                    const index = this.selectedOutlets.indexOf(outletId);
                    if (index > -1) {
                        this.selectedOutlets.splice(index, 1);
                    } else {
                        this.selectedOutlets.push(outletId);
                    }
                    this.loadAllData();
                },

                selectAllOutlets() {
                    this.selectedOutlets = [...this.availableOutlets];
                    this.loadAllData();
                },

                clearAllOutlets() {
                    this.selectedOutlets = [];
                    this.loadAllData();
                },

                async loadAllData() {
                    if (this.loading) return;
                    
                    this.loading = true;
                    try {
                        await Promise.all([
                            this.loadOverviewStats(),
                            this.loadSalesTrend(),
                            this.loadInventoryStatus(),
                            this.loadProductionEfficiency(),
                            this.loadEmployeePerformance(),
                            this.loadInsights()
                        ]);
                    } catch (error) {
                        console.error('Error loading dashboard data:', error);
                        alert('Gagal memuat data dashboard. Silakan refresh halaman.');
                    } finally {
                        this.loading = false;
                    }
                },

                async loadOverviewStats() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.overview") }}' + this.getOutletParams());
                        if (!response.ok) throw new Error('Failed to fetch overview');
                        const data = await response.json();
                        this.stats = data;
                    } catch (error) {
                        console.error('Error loading overview:', error);
                    }
                },

                async loadSalesTrend() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.sales-trend") }}' + this.getOutletParams());
                        if (!response.ok) throw new Error('Failed to fetch sales trend');
                        const data = await response.json();
                        this.updateSalesTrend(data);
                    } catch (error) {
                        console.error('Error loading sales trend:', error);
                        this.salesTrend = [];
                    }
                },

                async loadInventoryStatus() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.inventory-status") }}' + this.getOutletParams());
                        if (!response.ok) throw new Error('Failed to fetch inventory');
                        const data = await response.json();
                        this.inventory = data;
                    } catch (error) {
                        console.error('Error loading inventory:', error);
                    }
                },

                async loadProductionEfficiency() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.production-efficiency") }}' + this.getOutletParams());
                        if (!response.ok) throw new Error('Failed to fetch production');
                        const data = await response.json();
                        this.production = data;
                    } catch (error) {
                        console.error('Error loading production:', error);
                    }
                },

                async loadEmployeePerformance() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.employee-performance") }}' + this.getOutletParams());
                        if (!response.ok) throw new Error('Failed to fetch employees');
                        const data = await response.json();
                        this.employees = Array.isArray(data) ? data : [];
                    } catch (error) {
                        console.error('Error loading employees:', error);
                        this.employees = [];
                    }
                },

                async loadInsights() {
                    try {
                        const response = await fetch('{{ route("admin.dashboard.insights") }}' + this.getOutletParams());
                        if (!response.ok) throw new Error('Failed to fetch insights');
                        const data = await response.json();
                        this.insights = Array.isArray(data) ? data : [];
                    } catch (error) {
                        console.error('Error loading insights:', error);
                        this.insights = [];
                    }
                },

                async refreshData() {
                    await this.loadAllData();
                },

                openModuleInTab(url, title, icon) {
                    console.log('📦 Opening module in tab:', { url, title, icon });
                    
                    // Check if TAB_SYSTEM_COMPONENT is available
                    if (window.TAB_SYSTEM_COMPONENT && typeof window.TAB_SYSTEM_COMPONENT.loadInActiveTab === 'function') {
                        try {
                            window.TAB_SYSTEM_COMPONENT.loadInActiveTab(url, title, icon);
                            console.log('✅ Module opened in tab successfully');
                        } catch (error) {
                            console.error('❌ Error opening module in tab:', error);
                            // Fallback to normal navigation
                            window.location.href = url;
                        }
                    } else {
                        console.warn('⚠️ Tab system not available, using normal navigation');
                        window.location.href = url;
                    }
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(value || 0);
                },

                updateSalesTrend(data) {
                    this.salesTrend = Array.isArray(data) ? data : [];
                    this.salesTrendMax = Math.max(...this.salesTrend.map(item => item.value), 1);
                },

                getOutletParams() {
                    if (this.selectedOutlets.length === 0) return '';
                    const params = new URLSearchParams();
                    this.selectedOutlets.forEach(id => params.append('outlet_ids[]', id));
                    return '?' + params.toString();
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin-with-tabs>