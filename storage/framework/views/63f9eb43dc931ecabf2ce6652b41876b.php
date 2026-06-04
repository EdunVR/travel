<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Setting Resolusi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Setting Resolusi']); ?>
    <div class="container mx-auto px-4 py-6" x-data="resolutionSettings()">
        
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">
                <i class='bx bx-desktop text-primary-600'></i>
                Setting Resolusi Tampilan
            </h1>
            <p class="text-slate-600">
                Sesuaikan tampilan aplikasi dengan resolusi layar Anda untuk pengalaman yang lebih baik
            </p>
        </div>

        
        <div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden">
            
            <div class="bg-gradient-to-br from-slate-50 to-white p-6 border-b border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-900">
                        <i class='bx bx-show text-primary-600'></i>
                        Preview Tampilan
                    </h2>
                    <div class="text-sm text-slate-600">
                        Resolusi Layar: <span class="font-semibold" x-text="screenResolution"></span>
                    </div>
                </div>

                
                <div class="bg-white rounded-xl border-2 border-slate-300 p-4 overflow-hidden"
                     :style="`transform: scale(${settings.scale / 100}); transform-origin: top left;`">
                    <div class="flex gap-4">
                        
                        <div class="bg-slate-800 rounded-lg p-3 transition-all duration-300"
                             :class="{
                                 'w-48': settings.sidebar_width === 'compact',
                                 'w-64': settings.sidebar_width === 'normal',
                                 'w-80': settings.sidebar_width === 'wide'
                             }">
                            <div class="text-white space-y-2">
                                <div class="font-bold mb-3" 
                                     :class="{
                                         'text-xs': settings.font_size === 'small',
                                         'text-sm': settings.font_size === 'normal',
                                         'text-base': settings.font_size === 'large'
                                     }">
                                    MORRA ERP
                                </div>
                                <div class="space-y-1"
                                     :class="{
                                         'space-y-0.5': settings.spacing === 'compact',
                                         'space-y-1': settings.spacing === 'normal',
                                         'space-y-2': settings.spacing === 'comfortable'
                                     }">
                                    <div class="bg-slate-700 rounded px-2 py-1"
                                         :class="{
                                             'text-xs py-0.5': settings.font_size === 'small',
                                             'text-sm py-1': settings.font_size === 'normal',
                                             'text-base py-1.5': settings.font_size === 'large'
                                         }">
                                        Dashboard
                                    </div>
                                    <div class="bg-slate-700 rounded px-2 py-1"
                                         :class="{
                                             'text-xs py-0.5': settings.font_size === 'small',
                                             'text-sm py-1': settings.font_size === 'normal',
                                             'text-base py-1.5': settings.font_size === 'large'
                                         }">
                                        Inventaris
                                    </div>
                                    <div class="bg-slate-700 rounded px-2 py-1"
                                         :class="{
                                             'text-xs py-0.5': settings.font_size === 'small',
                                             'text-sm py-1': settings.font_size === 'normal',
                                             'text-base py-1.5': settings.font_size === 'large'
                                         }">
                                        Penjualan
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="flex-1 space-y-2"
                             :class="{
                                 'space-y-1': settings.spacing === 'compact',
                                 'space-y-2': settings.spacing === 'normal',
                                 'space-y-3': settings.spacing === 'comfortable'
                             }">
                            <div class="bg-slate-100 rounded h-8"
                                 :class="{
                                     'h-6': settings.font_size === 'small',
                                     'h-8': settings.font_size === 'normal',
                                     'h-10': settings.font_size === 'large'
                                 }"></div>
                            <div class="bg-slate-100 rounded h-20"></div>
                            <div class="bg-slate-100 rounded h-16"></div>
                        </div>
                    </div>
                </div>

                
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-start gap-2">
                    <i class='bx bx-info-circle text-blue-600 text-xl flex-shrink-0'></i>
                    <div class="text-sm text-blue-800">
                        <strong>Tips:</strong> Untuk layar kecil (laptop 13-14 inch), gunakan scale 80-90% dengan sidebar compact. 
                        Untuk layar besar (desktop 24 inch+), gunakan scale 100-110% dengan sidebar wide.
                    </div>
                </div>
            </div>

            
            <div class="p-6 space-y-6">
                
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-3">
                        <i class='bx bx-zoom-in text-primary-600'></i>
                        Skala Tampilan: <span class="text-primary-600" x-text="settings.scale + '%'"></span>
                    </label>
                    <div class="flex items-center gap-4">
                        <span class="text-xs text-slate-500 w-12">50%</span>
                        <input type="range" 
                               x-model="settings.scale" 
                               min="50" 
                               max="150" 
                               step="5"
                               class="flex-1 h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary-600">
                        <span class="text-xs text-slate-500 w-12 text-right">150%</span>
                    </div>
                    <div class="mt-2 flex gap-2">
                        <button @click="settings.scale = 80" class="px-3 py-1 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">80%</button>
                        <button @click="settings.scale = 90" class="px-3 py-1 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">90%</button>
                        <button @click="settings.scale = 100" class="px-3 py-1 text-xs bg-primary-100 hover:bg-primary-200 text-primary-700 rounded-lg transition-colors">100% (Default)</button>
                        <button @click="settings.scale = 110" class="px-3 py-1 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">110%</button>
                        <button @click="settings.scale = 120" class="px-3 py-1 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">120%</button>
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-3">
                        <i class='bx bx-sidebar text-primary-600'></i>
                        Lebar Sidebar
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <button @click="settings.sidebar_width = 'compact'"
                                :class="settings.sidebar_width === 'compact' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-300 hover:border-primary-400'"
                                class="px-4 py-3 rounded-xl border-2 transition-all font-medium">
                            <i class='bx bx-collapse-horizontal text-xl block mb-1'></i>
                            Compact
                        </button>
                        <button @click="settings.sidebar_width = 'normal'"
                                :class="settings.sidebar_width === 'normal' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-300 hover:border-primary-400'"
                                class="px-4 py-3 rounded-xl border-2 transition-all font-medium">
                            <i class='bx bx-sidebar text-xl block mb-1'></i>
                            Normal
                        </button>
                        <button @click="settings.sidebar_width = 'wide'"
                                :class="settings.sidebar_width === 'wide' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-300 hover:border-primary-400'"
                                class="px-4 py-3 rounded-xl border-2 transition-all font-medium">
                            <i class='bx bx-expand-horizontal text-xl block mb-1'></i>
                            Wide
                        </button>
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-3">
                        <i class='bx bx-font-size text-primary-600'></i>
                        Ukuran Font
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <button @click="settings.font_size = 'small'"
                                :class="settings.font_size === 'small' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-300 hover:border-primary-400'"
                                class="px-4 py-3 rounded-xl border-2 transition-all font-medium">
                            <i class='bx bx-font text-lg block mb-1'></i>
                            Kecil
                        </button>
                        <button @click="settings.font_size = 'normal'"
                                :class="settings.font_size === 'normal' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-300 hover:border-primary-400'"
                                class="px-4 py-3 rounded-xl border-2 transition-all font-medium">
                            <i class='bx bx-font text-xl block mb-1'></i>
                            Normal
                        </button>
                        <button @click="settings.font_size = 'large'"
                                :class="settings.font_size === 'large' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-300 hover:border-primary-400'"
                                class="px-4 py-3 rounded-xl border-2 transition-all font-medium">
                            <i class='bx bx-font text-2xl block mb-1'></i>
                            Besar
                        </button>
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-3">
                        <i class='bx bx-space-bar text-primary-600'></i>
                        Jarak Antar Elemen
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <button @click="settings.spacing = 'compact'"
                                :class="settings.spacing === 'compact' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-300 hover:border-primary-400'"
                                class="px-4 py-3 rounded-xl border-2 transition-all font-medium">
                            <i class='bx bx-align-justify text-xl block mb-1'></i>
                            Rapat
                        </button>
                        <button @click="settings.spacing = 'normal'"
                                :class="settings.spacing === 'normal' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-300 hover:border-primary-400'"
                                class="px-4 py-3 rounded-xl border-2 transition-all font-medium">
                            <i class='bx bx-align-middle text-xl block mb-1'></i>
                            Normal
                        </button>
                        <button @click="settings.spacing = 'comfortable'"
                                :class="settings.spacing === 'comfortable' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-300 hover:border-primary-400'"
                                class="px-4 py-3 rounded-xl border-2 transition-all font-medium">
                            <i class='bx bx-align-left text-xl block mb-1'></i>
                            Longgar
                        </button>
                    </div>
                </div>

                
                <div class="border-t border-slate-200 pt-6">
                    <label class="block text-sm font-semibold text-slate-900 mb-3">
                        <i class='bx bx-magic-wand text-primary-600'></i>
                        Preset Cepat
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <button @click="applyPreset('small-screen')"
                                class="px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all font-medium shadow-sm hover:shadow-md">
                            <i class='bx bx-laptop text-xl block mb-1'></i>
                            Layar Kecil (13-14")
                        </button>
                        <button @click="applyPreset('medium-screen')"
                                class="px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-all font-medium shadow-sm hover:shadow-md">
                            <i class='bx bx-desktop text-xl block mb-1'></i>
                            Layar Sedang (15-17")
                        </button>
                        <button @click="applyPreset('large-screen')"
                                class="px-4 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all font-medium shadow-sm hover:shadow-md">
                            <i class='bx bx-tv text-xl block mb-1'></i>
                            Layar Besar (24"+)
                        </button>
                    </div>
                </div>

                
                <div class="flex gap-3 pt-6 border-t border-slate-200">
                    <button @click="saveSettings()" 
                            :disabled="saving"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class='bx bx-save text-xl' :class="{'bx-spin': saving}"></i>
                        <span x-text="saving ? 'Menyimpan...' : 'Simpan Pengaturan'"></span>
                    </button>
                    <button @click="resetSettings()"
                            :disabled="saving"
                            class="px-6 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-all font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class='bx bx-reset'></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class='bx bx-info-circle text-blue-600 text-2xl flex-shrink-0'></i>
                    <div>
                        <h3 class="font-semibold text-blue-900 mb-1">Informasi</h3>
                        <p class="text-sm text-blue-800">
                            Pengaturan ini akan disimpan di browser Anda dan berlaku untuk semua halaman aplikasi. 
                            Anda dapat mengubahnya kapan saja sesuai kebutuhan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class='bx bx-bulb text-amber-600 text-2xl flex-shrink-0'></i>
                    <div>
                        <h3 class="font-semibold text-amber-900 mb-1">Rekomendasi</h3>
                        <p class="text-sm text-amber-800">
                            Jika tampilan terlihat terlalu kecil atau terlalu besar, coba gunakan preset cepat 
                            sesuai ukuran layar Anda untuk hasil optimal.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function resolutionSettings() {
        return {
            settings: {
                scale: 100,
                sidebar_width: 'normal',
                font_size: 'normal',
                spacing: 'normal'
            },
            saving: false,
            screenResolution: '',

            init() {
                // Get screen resolution
                this.screenResolution = `${window.screen.width} x ${window.screen.height}`;
                
                // Load saved settings
                this.loadSettings();
            },

            async loadSettings() {
                try {
                    const response = await fetch('<?php echo e(route('admin.sistem.resolusi.get')); ?>');
                    const data = await response.json();
                    
                    if (data.success && data.settings) {
                        this.settings = data.settings;
                    }
                } catch (error) {
                    console.error('Error loading settings:', error);
                }
            },

            async saveSettings() {
                this.saving = true;

                try {
                    const response = await fetch('<?php echo e(route('admin.sistem.resolusi.store')); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                        },
                        body: JSON.stringify(this.settings)
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Apply settings immediately
                        this.applySettingsToPage();
                        
                        // Show success message
                        alert('✅ ' + data.message + '\n\nHalaman akan dimuat ulang untuk menerapkan pengaturan.');
                        
                        // Reload page to apply settings
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        alert('❌ Gagal menyimpan pengaturan!');
                    }
                } catch (error) {
                    console.error('Error saving settings:', error);
                    alert('❌ Terjadi kesalahan saat menyimpan pengaturan!');
                } finally {
                    this.saving = false;
                }
            },

            async resetSettings() {
                if (!confirm('Reset pengaturan ke default?')) {
                    return;
                }

                this.saving = true;

                try {
                    const response = await fetch('<?php echo e(route('admin.sistem.resolusi.reset')); ?>', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('✅ ' + data.message + '\n\nHalaman akan dimuat ulang.');
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } catch (error) {
                    console.error('Error resetting settings:', error);
                    alert('❌ Terjadi kesalahan saat reset pengaturan!');
                } finally {
                    this.saving = false;
                }
            },

            applyPreset(preset) {
                switch(preset) {
                    case 'small-screen':
                        this.settings = {
                            scale: 85,
                            sidebar_width: 'compact',
                            font_size: 'small',
                            spacing: 'compact'
                        };
                        break;
                    case 'medium-screen':
                        this.settings = {
                            scale: 100,
                            sidebar_width: 'normal',
                            font_size: 'normal',
                            spacing: 'normal'
                        };
                        break;
                    case 'large-screen':
                        this.settings = {
                            scale: 110,
                            sidebar_width: 'wide',
                            font_size: 'large',
                            spacing: 'comfortable'
                        };
                        break;
                }
            },

            applySettingsToPage() {
                // This will be handled by the global resolution script
                console.log('Settings applied:', this.settings);
            }
        }
    }
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\sistem\resolusi\index.blade.php ENDPATH**/ ?>