<div x-data="designMaterials(<?php echo e($packageId); ?>)" x-init="init()" class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">Design Materials</h3>
        <div class="text-sm">
            <span class="text-gray-600">Completion:</span>
            <span class="font-semibold" :class="allComplete ? 'text-green-600' : 'text-yellow-600'">
                <span x-text="completedCount"></span> / 4
            </span>
        </div>
    </div>

    <!-- Materials Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Flyer -->
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="bx bx-image text-2xl text-blue-600"></i>
                    <h4 class="font-semibold">Flyer</h4>
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-medium"
                      :class="completionStatus.flyer?.is_complete ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                    <span x-text="completionStatus.flyer?.is_complete ? 'Complete' : 'Incomplete'"></span>
                </span>
            </div>
            
            <template x-if="completionStatus.flyer?.latest_version">
                <div class="space-y-2">
                    <!-- Preview -->
                    <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="height: 200px;">
                        <template x-if="isImage(completionStatus.flyer.latest_version.file_path)">
                            <img :src="getFileUrl(completionStatus.flyer.latest_version.file_path)" 
                                 class="w-full h-full object-cover"
                                 alt="Flyer preview">
                        </template>
                        <template x-if="!isImage(completionStatus.flyer.latest_version.file_path)">
                            <div class="flex items-center justify-center h-full">
                                <i class="bx bx-file text-6xl text-gray-400"></i>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Info -->
                    <div class="text-sm text-gray-600">
                        <p><span class="font-medium">Version:</span> <span x-text="completionStatus.flyer.latest_version.version"></span></p>
                        <p><span class="font-medium">Uploaded:</span> <span x-text="formatDate(completionStatus.flyer.latest_version.uploaded_at)"></span></p>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button @click="downloadMaterial(completionStatus.flyer.latest_version.id)" 
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="bx bx-download"></i> Download
                        </button>
                        <button @click="toggleComplete(completionStatus.flyer.latest_version)" 
                                class="flex-1 px-3 py-2 text-sm rounded-lg"
                                :class="completionStatus.flyer.latest_version.is_complete ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200'">
                            <i :class="completionStatus.flyer.latest_version.is_complete ? 'bx bx-x' : 'bx bx-check'"></i>
                            <span x-text="completionStatus.flyer.latest_version.is_complete ? 'Mark Incomplete' : 'Mark Complete'"></span>
                        </button>
                        <button @click="deleteMaterial(completionStatus.flyer.latest_version.id)" 
                                class="px-3 py-2 text-sm text-red-600 border border-red-300 rounded-lg hover:bg-red-50">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </div>
            </template>
            
            <template x-if="!completionStatus.flyer?.latest_version">
                <div class="text-center py-8">
                    <i class="bx bx-image text-4xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500 mb-3">No flyer uploaded</p>
                    <button @click="console.log('Flyer button clicked'); openUploadModal('flyer')" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 cursor-pointer">
                        <i class="bx bx-upload"></i> Upload Flyer
                    </button>
                </div>
            </template>
        </div>

        <!-- Itinerary -->
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="bx bx-list-ul text-2xl text-green-600"></i>
                    <h4 class="font-semibold">Itinerary</h4>
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-medium"
                      :class="completionStatus.itinerary?.is_complete ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                    <span x-text="completionStatus.itinerary?.is_complete ? 'Complete' : 'Incomplete'"></span>
                </span>
            </div>
            
            <template x-if="completionStatus.itinerary?.latest_version">
                <div class="space-y-2">
                    <!-- Preview -->
                    <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="height: 200px;">
                        <template x-if="isImage(completionStatus.itinerary.latest_version.file_path)">
                            <img :src="getFileUrl(completionStatus.itinerary.latest_version.file_path)" 
                                 class="w-full h-full object-cover"
                                 alt="Itinerary preview">
                        </template>
                        <template x-if="!isImage(completionStatus.itinerary.latest_version.file_path)">
                            <div class="flex items-center justify-center h-full">
                                <i class="bx bx-file text-6xl text-gray-400"></i>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Info -->
                    <div class="text-sm text-gray-600">
                        <p><span class="font-medium">Version:</span> <span x-text="completionStatus.itinerary.latest_version.version"></span></p>
                        <p><span class="font-medium">Uploaded:</span> <span x-text="formatDate(completionStatus.itinerary.latest_version.uploaded_at)"></span></p>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button @click="downloadMaterial(completionStatus.itinerary.latest_version.id)" 
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="bx bx-download"></i> Download
                        </button>
                        <button @click="toggleComplete(completionStatus.itinerary.latest_version)" 
                                class="flex-1 px-3 py-2 text-sm rounded-lg"
                                :class="completionStatus.itinerary.latest_version.is_complete ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200'">
                            <i :class="completionStatus.itinerary.latest_version.is_complete ? 'bx bx-x' : 'bx bx-check'"></i>
                            <span x-text="completionStatus.itinerary.latest_version.is_complete ? 'Mark Incomplete' : 'Mark Complete'"></span>
                        </button>
                        <button @click="deleteMaterial(completionStatus.itinerary.latest_version.id)" 
                                class="px-3 py-2 text-sm text-red-600 border border-red-300 rounded-lg hover:bg-red-50">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </div>
            </template>
            
            <template x-if="!completionStatus.itinerary?.latest_version">
                <div class="text-center py-8">
                    <i class="bx bx-list-ul text-4xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500 mb-3">No itinerary uploaded</p>
                    <button @click="console.log('Itinerary button clicked'); openUploadModal('itinerary')" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 cursor-pointer">
                        <i class="bx bx-upload"></i> Upload Itinerary
                    </button>
                </div>
            </template>
        </div>

        <!-- Promotional Video -->
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="bx bx-video text-2xl text-purple-600"></i>
                    <h4 class="font-semibold">Promotional Video</h4>
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-medium"
                      :class="completionStatus.promotional_video?.is_complete ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                    <span x-text="completionStatus.promotional_video?.is_complete ? 'Complete' : 'Incomplete'"></span>
                </span>
            </div>
            
            <template x-if="completionStatus.promotional_video?.latest_version">
                <div class="space-y-2">
                    <!-- Preview -->
                    <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="height: 200px;">
                        <div class="flex items-center justify-center h-full">
                            <i class="bx bx-video text-6xl text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Info -->
                    <div class="text-sm text-gray-600">
                        <p><span class="font-medium">Version:</span> <span x-text="completionStatus.promotional_video.latest_version.version"></span></p>
                        <p><span class="font-medium">Uploaded:</span> <span x-text="formatDate(completionStatus.promotional_video.latest_version.uploaded_at)"></span></p>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button @click="downloadMaterial(completionStatus.promotional_video.latest_version.id)" 
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="bx bx-download"></i> Download
                        </button>
                        <button @click="toggleComplete(completionStatus.promotional_video.latest_version)" 
                                class="flex-1 px-3 py-2 text-sm rounded-lg"
                                :class="completionStatus.promotional_video.latest_version.is_complete ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200'">
                            <i :class="completionStatus.promotional_video.latest_version.is_complete ? 'bx bx-x' : 'bx bx-check'"></i>
                            <span x-text="completionStatus.promotional_video.latest_version.is_complete ? 'Mark Incomplete' : 'Mark Complete'"></span>
                        </button>
                        <button @click="deleteMaterial(completionStatus.promotional_video.latest_version.id)" 
                                class="px-3 py-2 text-sm text-red-600 border border-red-300 rounded-lg hover:bg-red-50">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </div>
            </template>
            
            <template x-if="!completionStatus.promotional_video?.latest_version">
                <div class="text-center py-8">
                    <i class="bx bx-video text-4xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500 mb-3">No video uploaded</p>
                    <button @click="console.log('Video button clicked'); openUploadModal('promotional_video')" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 cursor-pointer">
                        <i class="bx bx-upload"></i> Upload Video
                    </button>
                </div>
            </template>
        </div>

        <!-- Package Information -->
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="bx bx-info-circle text-2xl text-orange-600"></i>
                    <h4 class="font-semibold">Package Information</h4>
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-medium"
                      :class="completionStatus.package_information?.is_complete ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                    <span x-text="completionStatus.package_information?.is_complete ? 'Complete' : 'Incomplete'"></span>
                </span>
            </div>
            
            <template x-if="completionStatus.package_information?.latest_version">
                <div class="space-y-2">
                    <!-- Preview -->
                    <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="height: 200px;">
                        <template x-if="isImage(completionStatus.package_information.latest_version.file_path)">
                            <img :src="getFileUrl(completionStatus.package_information.latest_version.file_path)" 
                                 class="w-full h-full object-cover"
                                 alt="Package info preview">
                        </template>
                        <template x-if="!isImage(completionStatus.package_information.latest_version.file_path)">
                            <div class="flex items-center justify-center h-full">
                                <i class="bx bx-file text-6xl text-gray-400"></i>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Info -->
                    <div class="text-sm text-gray-600">
                        <p><span class="font-medium">Version:</span> <span x-text="completionStatus.package_information.latest_version.version"></span></p>
                        <p><span class="font-medium">Uploaded:</span> <span x-text="formatDate(completionStatus.package_information.latest_version.uploaded_at)"></span></p>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button @click="downloadMaterial(completionStatus.package_information.latest_version.id)" 
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="bx bx-download"></i> Download
                        </button>
                        <button @click="toggleComplete(completionStatus.package_information.latest_version)" 
                                class="flex-1 px-3 py-2 text-sm rounded-lg"
                                :class="completionStatus.package_information.latest_version.is_complete ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200'">
                            <i :class="completionStatus.package_information.latest_version.is_complete ? 'bx bx-x' : 'bx bx-check'"></i>
                            <span x-text="completionStatus.package_information.latest_version.is_complete ? 'Mark Incomplete' : 'Mark Complete'"></span>
                        </button>
                        <button @click="deleteMaterial(completionStatus.package_information.latest_version.id)" 
                                class="px-3 py-2 text-sm text-red-600 border border-red-300 rounded-lg hover:bg-red-50">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                </div>
            </template>
            
            <template x-if="!completionStatus.package_information?.latest_version">
                <div class="text-center py-8">
                    <i class="bx bx-info-circle text-4xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500 mb-3">No package info uploaded</p>
                    <button @click="console.log('Package Info button clicked'); openUploadModal('package_information')" 
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 cursor-pointer">
                        <i class="bx bx-upload"></i> Upload Package Info
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="showUploadModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-4 pt-8 overflow-y-auto"
         style="z-index: 9999;"
         @click.self="console.log('Modal backdrop clicked'); showUploadModal = false"
         x-transition>
        <div class="bg-white rounded-lg p-6 max-w-md w-full my-auto" @click.stop>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Upload <span x-text="formatMaterialType(uploadMaterialType)"></span></h3>
                <button @click="console.log('Close button clicked'); showUploadModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="bx bx-x text-2xl"></i>
                </button>
            </div>
            
            <form @submit.prevent="console.log('Form submitted'); submitUpload()" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File</label>
                    <input type="file" 
                           @change="console.log('File selected:', $event.target.files[0]); handleFileSelect($event)"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                           required>
                    <p class="mt-1 text-xs text-gray-500" x-text="getFileTypeHint(uploadMaterialType)"></p>
                </div>
                
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" x-model="uploadIsComplete" class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">Mark as complete</span>
                    </label>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                    <textarea x-model="uploadNotes" 
                              rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
                
                <div class="flex gap-2">
                    <button type="button" 
                            @click="console.log('Cancel clicked'); showUploadModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="uploading"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                        <span x-show="!uploading">Upload</span>
                        <span x-show="uploading">Uploading...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function designMaterials(packageId) {
    return {
        packageId: packageId,
        materials: {},
        completionStatus: {},
        allComplete: false,
        completedCount: 0,
        showUploadModal: false,
        uploadMaterialType: '',
        uploadFile: null,
        uploadIsComplete: false,
        uploadNotes: '',
        uploading: false,
        
        async init() {
            console.log('Design Materials component initialized with packageId:', this.packageId);
            await this.fetchMaterials();
        },
        
        async fetchMaterials() {
            try {
                const response = await fetch(`<?php echo e(url(route('admin.inventaris.travel.design-materials.data', ['packageId' => '__ID__']))); ?>`.replace('__ID__', this.packageId));
                const data = await response.json();
                
                if (data.success) {
                    this.materials = data.data.materials;
                    this.completionStatus = data.data.completion_status;
                    this.allComplete = data.data.all_complete;
                    this.completedCount = Object.values(this.completionStatus).filter(s => s.is_complete).length;
                    console.log('Materials loaded:', this.materials);
                    console.log('Completion status:', this.completionStatus);
                }
            } catch (error) {
                console.error('Error fetching materials:', error);
            }
        },
        
        openUploadModal(materialType) {
            console.log('openUploadModal called with type:', materialType);
            this.uploadMaterialType = materialType;
            this.uploadFile = null;
            this.uploadIsComplete = false;
            this.uploadNotes = '';
            this.showUploadModal = true;
            console.log('showUploadModal set to:', this.showUploadModal);
        },
        
        handleFileSelect(event) {
            this.uploadFile = event.target.files[0];
            console.log('File stored in uploadFile:', this.uploadFile);
        },
        
        async submitUpload() {
            console.log('submitUpload called, uploadFile:', this.uploadFile);
            if (!this.uploadFile) {
                alert('Please select a file');
                return;
            }
            
            this.uploading = true;
            
            try {
                const formData = new FormData();
                formData.append('file', this.uploadFile);
                formData.append('material_type', this.uploadMaterialType);
                formData.append('is_complete', this.uploadIsComplete ? '1' : '0');
                if (this.uploadNotes) {
                    formData.append('notes', this.uploadNotes);
                }
                
                console.log('Uploading to:', `<?php echo e(url(route('admin.inventaris.travel.design-materials.upload', ['packageId' => '__ID__']))); ?>`.replace('__ID__', this.packageId));
                
                const response = await fetch(`<?php echo e(url(route('admin.inventaris.travel.design-materials.upload', ['packageId' => '__ID__']))); ?>`.replace('__ID__', this.packageId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const data = await response.json();
                console.log('Upload response:', data);
                
                if (data.success) {
                    this.showUploadModal = false;
                    await this.fetchMaterials();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Berhasil!', 'Material berhasil diupload', 'success');
                    } else {
                        alert('Material berhasil diupload');
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal', 'Gagal upload material: ' + (data.message || 'Unknown error'), 'error');
                    } else {
                        alert('Gagal upload material: ' + (data.message || 'Unknown error'));
                    }
                }
            } catch (error) {
                console.error('Error uploading material:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Gagal upload material', 'error');
                } else {
                    alert('Gagal upload material');
                }
            } finally {
                this.uploading = false;
            }
        },
        
        async toggleComplete(material) {
            try {
                const endpoint = material.is_complete ? 'incomplete' : 'complete';
                const baseUrl = `<?php echo e(url('/admin/inventaris/travel/materials')); ?>`;
                const response = await fetch(`${baseUrl}/${material.id}/${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    await this.fetchMaterials();
                } else {
                    alert('Failed to update material status');
                }
            } catch (error) {
                console.error('Error updating material:', error);
                alert('Failed to update material status');
            }
        },
        
        async deleteMaterial(materialId) {
            if (!confirm('Are you sure you want to delete this material?')) return;
            
            try {
                const response = await fetch(`<?php echo e(url(route('admin.inventaris.travel.design-materials.destroy', ['id' => '__ID__']))); ?>`.replace('__ID__', materialId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    await this.fetchMaterials();
                    alert('Material deleted successfully');
                } else {
                    alert('Failed to delete material');
                }
            } catch (error) {
                console.error('Error deleting material:', error);
                alert('Failed to delete material');
            }
        },
        
        downloadMaterial(materialId) {
            window.location.href = `<?php echo e(url(route('admin.inventaris.travel.design-materials.download', ['id' => '__ID__']))); ?>`.replace('__ID__', materialId);
        },
        
        isImage(filePath) {
            if (!filePath) return false;
            const ext = filePath.split('.').pop().toLowerCase();
            return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
        },
        
        getFileUrl(filePath) {
            return `<?php echo e(url('storage')); ?>/${filePath}`;
        },
        
        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric'
            });
        },
        
        formatMaterialType(type) {
            const types = {
                'flyer': 'Flyer',
                'itinerary': 'Itinerary',
                'promotional_video': 'Promotional Video',
                'package_information': 'Package Information'
            };
            return types[type] || type;
        },
        
        getFileTypeHint(type) {
            const hints = {
                'flyer': 'Accepted formats: JPG, PNG, PDF',
                'itinerary': 'Accepted formats: PDF, DOC, DOCX, JPG, PNG',
                'promotional_video': 'Accepted formats: MP4, AVI, MOV, WMV, FLV, WEBM',
                'package_information': 'Accepted formats: PDF, DOC, DOCX, JPG, PNG'
            };
            return hints[type] || 'Please select a file';
        }
    };
}
</script>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/package/design-materials.blade.php ENDPATH**/ ?>