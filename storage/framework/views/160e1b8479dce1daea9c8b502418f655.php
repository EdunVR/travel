
<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Dashboard Produksi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Dashboard Produksi')]); ?>
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight">Dashboard Produksi</h1>
        <p class="text-slate-600">Monitor & analisis performa lini produksi secara real-time</p>
    </div>

    
    <section class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                            <i class='bx bx-target-lock text-primary-600'></i>
                            <span>Target Produksi</span>
                        </div>
                        <div class="text-2xl font-bold">15.000</div>
                        <div class="text-xs text-slate-500">unit/bulan</div>
                    </div>
                    <div class="text-right">
                        <div class="inline-flex items-center px-2 py-1 rounded-full bg-green-50 text-green-700 text-xs">
                            <i class='bx bx-trending-up mr-1'></i>
                            +8%
                        </div>
                    </div>
                </div>
                <div class="mt-3 w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
                </div>
                <div class="mt-2 text-xs text-slate-500 flex justify-between">
                    <span>12.750/15.000</span>
                    <span>85%</span>
                </div>
            </div>

            
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                            <i class='bx bx-line-chart text-primary-600'></i>
                            <span>Efisiensi</span>
                        </div>
                        <div class="text-2xl font-bold">88.5%</div>
                        <div class="text-xs text-slate-500">Overall Equipment</div>
                    </div>
                    <div class="text-right">
                        <div class="inline-flex items-center px-2 py-1 rounded-full bg-amber-50 text-amber-700 text-xs">
                            <i class='bx bx-minus mr-1'></i>
                            -2%
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-center">
                    <div class="relative w-16 h-16">
                        <svg class="w-full h-full" viewBox="0 0 36 36">
                            <path d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none" stroke="#e2e8f0" stroke-width="3"/>
                            <path d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none" stroke="#3b82f6" stroke-width="3" stroke-dasharray="88.5, 100"/>
                            <text x="18" y="20.5" class="text-[10px] font-bold" text-anchor="middle" fill="#1f2937">88%</text>
                        </svg>
                    </div>
                </div>
            </div>

            
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                            <i class='bx bx-time-five text-primary-600'></i>
                            <span>Downtime</span>
                        </div>
                        <div class="text-2xl font-bold">4.2%</div>
                        <div class="text-xs text-slate-500">Rata-rata mesin</div>
                    </div>
                    <div class="text-right">
                        <div class="inline-flex items-center px-2 py-1 rounded-full bg-red-50 text-red-700 text-xs">
                            <i class='bx bx-trending-up mr-1'></i>
                            +1.5%
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <div class="text-lg font-semibold text-slate-700">156 jam</div>
                    <div class="text-xs text-slate-500">total bulan ini</div>
                </div>
            </div>

            
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                            <i class='bx bx-award text-primary-600'></i>
                            <span>Tingkat Kualitas</span>
                        </div>
                        <div class="text-2xl font-bold">96.8%</div>
                        <div class="text-xs text-slate-500">First Pass Yield</div>
                    </div>
                    <div class="text-right">
                        <div class="inline-flex items-center px-2 py-1 rounded-full bg-green-50 text-green-700 text-xs">
                            <i class='bx bx-trending-up mr-1'></i>
                            +0.8%
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <div class="flex-1 bg-slate-100 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 96.8%"></div>
                    </div>
                    <span class="text-xs font-medium text-slate-700">A</span>
                </div>
            </div>
        </div>
    </section>

    
    <section class="mb-6">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            <div class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex items-center justify-between mb-6">
                    <div class="font-semibold flex items-center gap-2">
                        <i class='bx bx-bar-chart-alt-2 text-primary-600'></i>
                        <span>Output Produksi Harian</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <select class="text-sm border border-slate-200 rounded-lg px-3 py-1">
                            <option>7 Hari Terakhir</option>
                            <option>30 Hari Terakhir</option>
                            <option>Bulan Ini</option>
                        </select>
                    </div>
                </div>
                <div class="h-80">
                    <?php
                        $dailyData = [
                            ['day' => 'Sen', 'target' => 500, 'actual' => 480, 'efficiency' => 96],
                            ['day' => 'Sel', 'target' => 500, 'actual' => 520, 'efficiency' => 104],
                            ['day' => 'Rab', 'target' => 500, 'actual' => 460, 'efficiency' => 92],
                            ['day' => 'Kam', 'target' => 500, 'actual' => 510, 'efficiency' => 102],
                            ['day' => 'Jum', 'target' => 500, 'actual' => 490, 'efficiency' => 98],
                            ['day' => 'Sab', 'target' => 300, 'actual' => 280, 'efficiency' => 93],
                            ['day' => 'Min', 'target' => 0, 'actual' => 0, 'efficiency' => 0],
                        ];
                        $maxOutput = 600;
                    ?>
                    <div class="h-full flex items-end justify-between pb-8">
                        <?php $__currentLoopData = $dailyData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex flex-col items-center flex-1">
                            <div class="text-xs text-slate-500 mb-2"><?php echo e($day['day']); ?></div>
                            <div class="w-full flex flex-col items-center justify-end h-56 gap-1">
                                <?php if($day['target'] > 0): ?>
                                <div class="text-xs text-slate-400"><?php echo e($day['actual']); ?>/<?php echo e($day['target']); ?></div>
                                <div class="w-3/4 bg-slate-100 rounded-t relative" style="height: <?php echo e(($day['target'] / $maxOutput) * 200); ?>px">
                                    <div class="absolute bottom-0 w-full bg-primary-500 rounded-t" style="height: <?php echo e(($day['actual'] / $day['target']) * 100); ?>%"></div>
                                </div>
                                <div class="text-xs font-medium <?php echo e($day['efficiency'] >= 100 ? 'text-green-600' : ($day['efficiency'] >= 95 ? 'text-amber-600' : 'text-red-600')); ?>">
                                    <?php echo e($day['efficiency']); ?>%
                                </div>
                                <?php else: ?>
                                <div class="text-xs text-slate-400">Libur</div>
                                <div class="w-3/4 bg-slate-100 rounded-t" style="height: 10px"></div>
                                <div class="text-xs text-slate-400">-</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="font-semibold mb-6 flex items-center gap-2">
                    <i class='bx bx-cog text-primary-600'></i>
                    <span>Status Mesin Produksi</span>
                </div>
                <div class="space-y-4">
                    <?php
                        $machines = [
                            ['name' => 'Injection Molding A', 'status' => 'running', 'uptime' => 95, 'output' => 2450],
                            ['name' => 'Injection Molding B', 'status' => 'idle', 'uptime' => 88, 'output' => 2180],
                            ['name' => 'CNC Router 1', 'status' => 'maintenance', 'uptime' => 92, 'output' => 1950],
                            ['name' => 'CNC Router 2', 'status' => 'running', 'uptime' => 97, 'output' => 2620],
                            ['name' => 'Assembly Line 1', 'status' => 'running', 'uptime' => 94, 'output' => 2780],
                            ['name' => 'Packaging Line', 'status' => 'idle', 'uptime' => 89, 'output' => 2310],
                        ];
                    ?>
                    <?php $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full 
                                <?php if($machine['status'] == 'running'): ?> bg-green-500
                                <?php elseif($machine['status'] == 'idle'): ?> bg-amber-500
                                <?php else: ?> bg-red-500 <?php endif; ?>">
                            </div>
                            <div>
                                <div class="font-medium text-sm"><?php echo e($machine['name']); ?></div>
                                <div class="text-xs text-slate-500 capitalize"><?php echo e($machine['status']); ?></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold"><?php echo e($machine['uptime']); ?>%</div>
                            <div class="text-xs text-slate-500"><?php echo e($machine['output']); ?> unit</div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="mt-6 p-3 bg-slate-50 rounded-lg">
                    <div class="flex justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span>Running: 3 mesin</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                            <span>Idle: 2 mesin</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            <span>Maintenance: 1 mesin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex items-center justify-between mb-6">
                    <div class="font-semibold flex items-center gap-2">
                        <i class='bx bx-error-circle text-primary-600'></i>
                        <span>Tingkat Reject per Lini</span>
                    </div>
                    <div class="text-sm text-slate-500">Bulan Ini</div>
                </div>
                <div class="h-64">
                    <?php
                        $rejectData = [
                            ['line' => 'Lini A', 'total' => 12500, 'reject' => 380, 'rate' => 3.04],
                            ['line' => 'Lini B', 'total' => 11800, 'reject' => 420, 'rate' => 3.56],
                            ['line' => 'Lini C', 'total' => 13200, 'reject' => 290, 'rate' => 2.20],
                            ['line' => 'Lini D', 'total' => 9800, 'reject' => 510, 'rate' => 5.20],
                        ];
                        $maxReject = 6.0;
                    ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $rejectData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center">
                            <div class="w-20 text-sm font-medium"><?php echo e($line['line']); ?></div>
                            <div class="flex-1 mx-3">
                                <div class="flex justify-between text-xs mb-1">
                                    <span><?php echo e($line['reject']); ?> unit reject</span>
                                    <span class="<?php echo e($line['rate'] <= 3 ? 'text-green-600' : ($line['rate'] <= 5 ? 'text-amber-600' : 'text-red-600')); ?> font-medium">
                                        <?php echo e($line['rate']); ?>%
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-3">
                                    <div class="h-3 rounded-full <?php echo e($line['rate'] <= 3 ? 'bg-green-500' : ($line['rate'] <= 5 ? 'bg-amber-500' : 'bg-red-500')); ?>" 
                                         style="width: <?php echo e(($line['rate'] / $maxReject) * 100); ?>%">
                                    </div>
                                </div>
                            </div>
                            <div class="w-16 text-right text-xs text-slate-500">
                                <?php echo e($line['total']); ?> total
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                        <div class="p-3 rounded-lg bg-green-50 border border-green-100">
                            <div class="text-lg font-bold text-green-700">2.20%</div>
                            <div class="text-xs text-green-600">Terbaik (Lini C)</div>
                        </div>
                        <div class="p-3 rounded-lg bg-amber-50 border border-amber-100">
                            <div class="text-lg font-bold text-amber-700">3.56%</div>
                            <div class="text-xs text-amber-600">Rata-rata</div>
                        </div>
                        <div class="p-3 rounded-lg bg-red-50 border border-red-100">
                            <div class="text-lg font-bold text-red-700">5.20%</div>
                            <div class="text-xs text-red-600">Terburuk (Lini D)</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex items-center justify-between mb-6">
                    <div class="font-semibold flex items-center gap-2">
                        <i class='bx bx-timer text-primary-600'></i>
                        <span>Waktu Siklus Produksi</span>
                    </div>
                    <div class="text-sm text-slate-500">Detik/unit</div>
                </div>
                <div class="h-64">
                    <?php
                        $cycleData = [
                            ['process' => 'Preparasi Material', 'target' => 45, 'actual' => 48, 'variance' => '+3'],
                            ['process' => 'Injection Molding', 'target' => 120, 'actual' => 118, 'variance' => '-2'],
                            ['process' => 'Pendinginan', 'target' => 60, 'actual' => 65, 'variance' => '+5'],
                            ['process' => 'Quality Check', 'target' => 30, 'actual' => 28, 'variance' => '-2'],
                            ['process' => 'Assembly', 'target' => 180, 'actual' => 175, 'variance' => '-5'],
                            ['process' => 'Packaging', 'target' => 45, 'actual' => 50, 'variance' => '+5'],
                        ];
                    ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $cycleData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $process): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center">
                            <div class="w-32 text-sm"><?php echo e($process['process']); ?></div>
                            <div class="flex-1 mx-3">
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Target: <?php echo e($process['target']); ?>s</span>
                                    <span class="<?php echo e($process['variance'][0] == '+' ? 'text-red-600' : 'text-green-600'); ?> font-medium">
                                        <?php echo e($process['variance']); ?>s
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="h-2 rounded-full <?php echo e($process['variance'][0] == '+' ? 'bg-red-500' : 'bg-green-500'); ?>" 
                                         style="width: <?php echo e(min(100, ($process['actual'] / $process['target']) * 100)); ?>%">
                                    </div>
                                </div>
                            </div>
                            <div class="w-12 text-right text-sm font-medium">
                                <?php echo e($process['actual']); ?>s
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-6 p-3 bg-slate-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm font-medium">Total Waktu Siklus</div>
                                <div class="text-xs text-slate-500">Per unit produk</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-primary-600">484s</div>
                                <div class="text-xs text-green-600">-4s dari target</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex items-center justify-between mb-6">
                    <div class="font-semibold flex items-center gap-2">
                        <i class='bx bx-package text-primary-600'></i>
                        <span>Level Persediaan Material</span>
                    </div>
                    <div class="text-sm text-slate-500">Status Real-time</div>
                </div>
                <div class="h-64">
                    <?php
                        $inventory = [
                            ['material' => 'Plastic ABS', 'current' => 1250, 'min' => 500, 'max' => 2000, 'status' => 'aman'],
                            ['material' => 'Plastic PP', 'current' => 420, 'min' => 500, 'max' => 2000, 'status' => 'warning'],
                            ['material' => 'Steel Sheet', 'current' => 850, 'min' => 300, 'max' => 1500, 'status' => 'aman'],
                            ['material' => 'Electronic Parts', 'current' => 180, 'min' => 200, 'max' => 800, 'status' => 'danger'],
                            ['material' => 'Packaging Box', 'current' => 3200, 'min' => 1000, 'max' => 5000, 'status' => 'aman'],
                            ['material' => 'Fasteners', 'current' => 45000, 'min' => 25000, 'max' => 100000, 'status' => 'aman'],
                        ];
                    ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $inventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-3 h-3 rounded-full 
                                    <?php if($item['status'] == 'aman'): ?> bg-green-500
                                    <?php elseif($item['status'] == 'warning'): ?> bg-amber-500
                                    <?php else: ?> bg-red-500 <?php endif; ?>">
                                </div>
                                <span class="text-sm w-28"><?php echo e($item['material']); ?></span>
                                <div class="flex-1 mx-2">
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        <div class="h-2 rounded-full 
                                            <?php if($item['status'] == 'aman'): ?> bg-green-500
                                            <?php elseif($item['status'] == 'warning'): ?> bg-amber-500
                                            <?php else: ?> bg-red-500 <?php endif; ?>" 
                                             style="width: <?php echo e(min(100, ($item['current'] / $item['max']) * 100)); ?>%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium"><?php echo e(number_format($item['current'])); ?></div>
                                <div class="text-xs text-slate-500">min: <?php echo e(number_format($item['min'])); ?></div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
                <div class="font-semibold mb-6 flex items-center gap-2">
                    <i class='bx bx-clipboard text-primary-600'></i>
                    <span>Permintaan Material Mendatang</span>
                </div>
                <div class="h-64">
                    <?php
                        $materialRequests = [
                            ['material' => 'Plastic ABS', 'quantity' => 1500, 'date' => '2024-01-15', 'priority' => 'high'],
                            ['material' => 'Electronic Parts', 'quantity' => 500, 'date' => '2024-01-12', 'priority' => 'critical'],
                            ['material' => 'Steel Sheet', 'quantity' => 800, 'date' => '2024-01-18', 'priority' => 'medium'],
                            ['material' => 'Packaging Box', 'quantity' => 2500, 'date' => '2024-01-20', 'priority' => 'low'],
                            ['material' => 'Fasteners', 'quantity' => 30000, 'date' => '2024-01-22', 'priority' => 'medium'],
                        ];
                    ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $materialRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full 
                                    <?php if($request['priority'] == 'critical'): ?> bg-red-500
                                    <?php elseif($request['priority'] == 'high'): ?> bg-amber-500
                                    <?php elseif($request['priority'] == 'medium'): ?> bg-blue-500
                                    <?php else: ?> bg-green-500 <?php endif; ?>">
                                </div>
                                <div>
                                    <div class="font-medium text-sm"><?php echo e($request['material']); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo e(\Carbon\Carbon::parse($request['date'])->format('d M Y')); ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold"><?php echo e(number_format($request['quantity'])); ?></div>
                                <div class="text-xs text-slate-500 capitalize"><?php echo e($request['priority']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-4 p-3 bg-primary-50 rounded-lg border border-primary-100">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-primary-900">Total Permintaan</div>
                            <div class="text-lg font-bold text-primary-700">35,300 unit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
            <div class="flex items-center justify-between mb-6">
                <div class="font-semibold flex items-center gap-2">
                    <i class='bx bx-rocket text-primary-600'></i>
                    <span>Tindakan Cepat & Alert</span>
                </div>
                <div class="text-sm text-slate-500">Prioritas Tinggi</div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-red-50 border border-red-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                            <i class='bx bx-error text-red-600 text-xl'></i>
                        </div>
                        <div>
                            <div class="font-semibold text-red-900">Material Kritis</div>
                            <div class="text-sm text-red-700">2 item perlu segera</div>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i class='bx bx-time-five text-amber-600 text-xl'></i>
                        </div>
                        <div>
                            <div class="font-semibold text-amber-900">Maintenance</div>
                            <div class="text-sm text-amber-700">1 mesin butuh service</div>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <i class='bx bx-trending-down text-blue-600 text-xl'></i>
                        </div>
                        <div>
                            <div class="font-semibold text-blue-900">Efisiensi Turun</div>
                            <div class="text-sm text-blue-700">Lini B perlu optimasi</div>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 rounded-xl bg-green-50 border border-green-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <i class='bx bx-check-circle text-green-600 text-xl'></i>
                        </div>
                        <div>
                            <div class="font-semibold text-green-900">Target Tercapai</div>
                            <div class="text-sm text-green-700">85% progres bulanan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\produksi\index.blade.php ENDPATH**/ ?>