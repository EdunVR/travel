<div x-data="workflowManager(<?php echo e($packageId); ?>)" x-init="init()" class="space-y-6">
    <!-- Workflow Progress Timeline -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Progress Workflow</h3>
        
        <!-- Timeline -->
        <div class="relative">
            <template x-for="(item, index) in progress" :key="index">
                <div class="flex items-start mb-6 last:mb-0">
                    <!-- Timeline Dot -->
                    <div class="flex flex-col items-center mr-4">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2"
                             :class="{
                                 'bg-green-100 border-green-500': item.status === 'completed',
                                 'bg-blue-100 border-blue-500': item.status === 'current',
                                 'bg-gray-100 border-gray-300': item.status === 'pending'
                             }">
                            <i class="bx text-xl"
                               :class="{
                                   'bx-check text-green-600': item.status === 'completed',
                                   'bx-loader-circle text-blue-600': item.status === 'current',
                                   'bx-circle text-gray-400': item.status === 'pending'
                               }"></i>
                        </div>
                        <div x-show="index < progress.length - 1" class="w-0.5 h-12 mt-2"
                             :class="{
                                 'bg-green-500': item.status === 'completed',
                                 'bg-gray-300': item.status !== 'completed'
                             }"></div>
                    </div>
                    
                    <!-- Stage Info -->
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold" x-text="item.stage.stage_name"></h4>
                                <p class="text-sm text-gray-600" x-text="item.stage.description"></p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Tim: <span class="font-medium" x-text="item.stage.responsible_team"></span>
                                </p>
                            </div>
                            <div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-green-100 text-green-800': item.status === 'completed',
                                          'bg-blue-100 text-blue-800': item.status === 'current',
                                          'bg-gray-100 text-gray-800': item.status === 'pending'
                                      }"
                                      x-text="getStatusLabel(item.status)"></span>
                            </div>
                        </div>
                        
                        <!-- Transition Button (only for current stage) -->
                        <div x-show="item.status === 'current' && nextStage" class="mt-3">
                            <button @click="openTransitionModal()" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span>Lanjut ke <span x-text="nextStage?.stage_name"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Workflow History -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Riwayat Workflow</h3>
        
        <div class="space-y-3">
            <template x-for="item in history" :key="item.id">
                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <i class="bx bx-history text-xl text-gray-600 mt-1"></i>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-medium" x-text="item.from_stage_details?.stage_name || 'Awal'"></span>
                            <i class="bx bx-right-arrow-alt text-gray-400"></i>
                            <span class="font-medium" x-text="item.to_stage_details?.stage_name"></span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            Oleh <span class="font-medium" x-text="item.user?.name"></span>
                            pada <span x-text="formatDate(item.transitioned_at)"></span>
                        </p>
                        <p x-show="item.notes" class="text-sm text-gray-500 mt-1" x-text="item.notes"></p>
                    </div>
                </div>
            </template>
            
            <div x-show="history.length === 0" class="text-center py-4 text-gray-500">
                Belum ada riwayat workflow
            </div>
        </div>
    </div>

    <!-- Current Stage Tasks -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Tugas Tahap Saat Ini</h3>
        
        <div class="space-y-2">
            <template x-for="task in currentStageTasks" :key="task.id">
                <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg">
                    <input type="checkbox" :checked="task.status === 'completed'" disabled
                           class="w-5 h-5 text-blue-600 rounded">
                    <div class="flex-1">
                        <h4 class="font-medium" x-text="task.task_name"></h4>
                        <p class="text-sm text-gray-600" x-text="task.task_description"></p>
                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                            <span>Deadline: <span x-text="formatDate(task.due_date)"></span></span>
                            <span x-show="task.assigned_user">
                                Ditugaskan ke: <span x-text="task.assigned_user?.name"></span>
                            </span>
                        </div>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                          :class="{
                              'bg-green-100 text-green-800': task.status === 'completed',
                              'bg-yellow-100 text-yellow-800': task.status === 'in_progress',
                              'bg-gray-100 text-gray-800': task.status === 'pending',
                              'bg-red-100 text-red-800': task.status === 'cancelled'
                          }"
                          x-text="getTaskStatusLabel(task.status)"></span>
                </div>
            </template>
            
            <div x-show="currentStageTasks.length === 0" class="text-center py-4 text-gray-500">
                Tidak ada tugas untuk tahap saat ini
            </div>
        </div>
    </div>

    <!-- Transition Modal -->
    <div x-show="showTransitionModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-8 overflow-y-auto"
         style="overflow-y: auto;"
         @keydown.escape.window="showTransitionModal = false">
        <div class="fixed inset-0 bg-black opacity-50" @click="showTransitionModal = false"></div>
        
        <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 my-auto">
            <h3 class="text-lg font-semibold mb-4">Lanjutkan Tahap Workflow</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 mb-2">
                        Transisi dari <span class="font-medium" x-text="currentStage"></span>
                        ke <span class="font-medium" x-text="nextStage?.stage_name"></span>
                    </p>
                </div>
                
                <div x-show="validationErrors.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <p class="text-sm font-medium text-red-800 mb-2">Tidak dapat melanjutkan:</p>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        <template x-for="error in validationErrors" :key="error">
                            <li x-text="error"></li>
                        </template>
                    </ul>
                    
                    <!-- Show pending tasks details -->
                    <div x-show="pendingTasks.length > 0" class="mt-3 pt-3 border-t border-red-200">
                        <p class="text-sm font-medium text-red-800 mb-2">Tugas yang belum selesai:</p>
                        <div class="space-y-2">
                            <template x-for="task in pendingTasks" :key="task.id">
                                <div class="bg-white rounded p-2 text-xs">
                                    <div class="font-medium text-gray-900" x-text="task.task_name"></div>
                                    <div class="text-gray-600 mt-1" x-text="task.task_description"></div>
                                    <div class="flex items-center gap-2 mt-1 text-gray-500">
                                        <span>Deadline: <span x-text="formatDate(task.due_date)"></span></span>
                                        <span x-show="task.assigned_user">
                                            • Ditugaskan ke: <span x-text="task.assigned_user?.name"></span>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (opsional)</label>
                    <textarea x-model="transitionNotes" rows="3"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
                              placeholder="Tambahkan catatan tentang transisi ini..."></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-2 mt-6">
                <button @click="showTransitionModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button @click="transitionWorkflow()"
                        :disabled="validationErrors.length > 0"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Lanjutkan Tahap
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function workflowManager(packageId) {
    return {
        packageId: packageId,
        progress: [],
        history: [],
        tasks: [],
        currentStage: '',
        nextStage: null,
        showTransitionModal: false,
        transitionNotes: '',
        validationErrors: [],
        
        init() {
            this.fetchWorkflowData();
            this.fetchTasks();
        },
        
        async fetchWorkflowData() {
            try {
                const response = await fetch(`<?php echo e(url(route('admin.inventaris.travel.package.workflow.progress', ['id' => '__ID__']))); ?>`.replace('__ID__', this.packageId));
                const data = await response.json();
                
                this.progress = data.progress;
                this.history = data.history;
                this.currentStage = data.current_stage;
                this.nextStage = data.next_stage;
            } catch (error) {
                console.error('Error fetching workflow data:', error);
            }
        },
        
        async fetchTasks() {
            try {
                const response = await fetch(`<?php echo e(url(route('admin.inventaris.travel.package.workflow.tasks', ['id' => '__ID__']))); ?>`.replace('__ID__', this.packageId));
                this.tasks = await response.json();
            } catch (error) {
                console.error('Error fetching tasks:', error);
            }
        },
        
        get currentStageTasks() {
            const currentStageItem = this.progress.find(p => p.status === 'current');
            if (!currentStageItem) return [];
            
            return this.tasks.filter(t => t.id_workflow_stage === currentStageItem.stage.id);
        },
        
        openTransitionModal() {
            this.showTransitionModal = true;
            this.transitionNotes = '';
            this.checkValidation();
        },
        
        get pendingTasks() {
            return this.currentStageTasks.filter(t => t.status !== 'completed');
        },
        
        async checkValidation() {
            // Check if all tasks are completed
            const incompleteTasks = this.pendingTasks;
            this.validationErrors = incompleteTasks.length > 0 
                ? [`${incompleteTasks.length} tugas masih tertunda`]
                : [];
        },
        
        getStatusLabel(status) {
            const labels = {
                'completed': 'SELESAI',
                'current': 'AKTIF',
                'pending': 'MENUNGGU'
            };
            return labels[status] || status.toUpperCase();
        },
        
        getTaskStatusLabel(status) {
            const labels = {
                'completed': 'SELESAI',
                'in_progress': 'SEDANG DIKERJAKAN',
                'pending': 'MENUNGGU',
                'cancelled': 'DIBATALKAN'
            };
            return labels[status] || status.toUpperCase();
        },
        
        async transitionWorkflow() {
            if (!this.nextStage) return;
            
            try {
                const response = await fetch(`<?php echo e(url(route('admin.inventaris.travel.package.workflow.transition', ['id' => '__ID__']))); ?>`.replace('__ID__', this.packageId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        to_stage: this.nextStage.stage_code,
                        notes: this.transitionNotes
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.showTransitionModal = false;
                    await this.fetchWorkflowData();
                    await this.fetchTasks();
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Berhasil!', data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal', data.message || 'Gagal melakukan transisi workflow', 'error');
                    } else {
                        alert(data.message || 'Gagal melakukan transisi workflow');
                    }
                }
            } catch (error) {
                console.error('Error transitioning workflow:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Terjadi kesalahan saat melakukan transisi workflow', 'error');
                } else {
                    alert('Terjadi kesalahan saat melakukan transisi workflow');
                }
            }
        },
        
        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/package/workflow.blade.php ENDPATH**/ ?>