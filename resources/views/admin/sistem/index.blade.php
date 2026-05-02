<x-layouts.admin>
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sistem & Pengaturan</h1>
                <p class="text-gray-600">Kelola pengaturan sistem dan konfigurasi aplikasi</p>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Outlet</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalOutlets ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total User</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalUsers ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Role</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalRoles ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Konfigurasi</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalConfigs ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Menu Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Pengaturan Perusahaan -->
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Pengaturan Perusahaan</h3>
                            <p class="text-sm text-gray-600">Kelola informasi perusahaan</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Atur nama perusahaan, logo, alamat, kontak, dan informasi legal perusahaan.</p>
                    <div class="flex gap-2">
                        <button onclick="openCompanySettingsModal()" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center text-sm">
                            Kelola Pengaturan
                        </button>
                        <a href="{{ route('admin.sistem.pengaturan.index') }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
                            Lihat
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Management -->
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">User Management</h3>
                            <p class="text-sm text-gray-600">Kelola pengguna sistem</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Atur pengguna, role, permission, dan akses outlet untuk setiap user.</p>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.users.index') }}" 
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center text-sm">
                            Kelola User
                        </a>
                        <a href="{{ route('admin.roles.index') }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
                            Role
                        </a>
                    </div>
                </div>
            </div>

            <!-- Backup & Restore -->
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Backup & Restore</h3>
                            <p class="text-sm text-gray-600">Kelola backup data</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Buat backup database, restore data, dan kelola file backup sistem.</p>
                    <div class="flex gap-2">
                        <button onclick="createBackup()" 
                                class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm">
                            Buat Backup
                        </button>
                        <button onclick="showBackupList()" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
                            Lihat
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">System Information</h3>
                            <p class="text-sm text-gray-600">Informasi sistem</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Lihat informasi server, versi aplikasi, dan status sistem.</p>
                    <div class="flex gap-2">
                        <button onclick="showSystemInfo()" 
                                class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                            Lihat Info
                        </button>
                        <button onclick="clearCache()" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
                            Clear Cache
                        </button>
                    </div>
                </div>
            </div>

            <!-- Database Management -->
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-red-100 rounded-lg">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Database Management</h3>
                            <p class="text-sm text-gray-600">Kelola database</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Jalankan migrasi, seed data, dan optimasi database sistem.</p>
                    <div class="flex gap-2">
                        <button onclick="runMigration()" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                            Migration
                        </button>
                        <button onclick="optimizeDb()" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
                            Optimize
                        </button>
                    </div>
                </div>
            </div>

            <!-- Application Settings -->
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Application Settings</h3>
                            <p class="text-sm text-gray-600">Pengaturan aplikasi</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Atur konfigurasi aplikasi, environment, dan pengaturan global.</p>
                    <div class="flex gap-2">
                        <button onclick="showAppSettings()" 
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                            Pengaturan
                        </button>
                        <button onclick="showEnvEditor()" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
                            ENV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                    <p class="text-sm text-gray-600">Log aktivitas sistem dan perubahan konfigurasi</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">Sistem berhasil diinisialisasi</p>
                                <p class="text-xs text-gray-600">{{ now()->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>Belum ada aktivitas lainnya</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info Modal -->
    <div id="systemInfoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">System Information</h3>
                    <button onclick="closeSystemInfoModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div id="systemInfoContent" class="space-y-4">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Backup List Modal -->
    <div id="backupListModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[80vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Backup Database</h3>
                    <button onclick="closeBackupModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div id="backupListContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button onclick="createBackup()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Buat Backup Baru
                    </button>
                    <button onclick="closeBackupModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Settings Modal -->
    <div id="companySettingsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 p-6 z-10">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Pengaturan Perusahaan</h3>
                        <button onclick="closeCompanySettingsModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <form id="companySettingsForm" class="p-6" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Company Information -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Perusahaan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan *</label>
                                <input type="text" name="company_name" id="company_name" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Perusahaan</label>
                                <input type="text" name="company_code" id="company_code"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                <textarea name="company_address" id="company_address" rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                                <input type="text" name="company_phone" id="company_phone"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="company_email" id="company_email"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <input type="url" name="company_website" id="company_website"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Logo & Favicon -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Logo & Favicon</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Logo Perusahaan</label>
                                <input type="file" name="company_logo" id="company_logo" accept="image/*"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Max 2MB (JPG, PNG, GIF, SVG)</p>
                                <div id="logo_preview" class="mt-2 hidden">
                                    <img src="" alt="Logo Preview" class="h-20 object-contain">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                                <input type="file" name="company_favicon" id="company_favicon" accept="image/*"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Max 1MB (ICO, PNG, JPG, GIF, SVG)</p>
                                <div id="favicon_preview" class="mt-2 hidden">
                                    <img src="" alt="Favicon Preview" class="h-12 object-contain">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Legal Information -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Legal</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                                <input type="text" name="npwp" id="npwp"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIB</label>
                                <input type="text" name="nib" id="nib"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SIUP</label>
                                <input type="text" name="siup" id="siup"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">TDP</label>
                                <input type="text" name="tdp" id="tdp"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Bank Information -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Informasi Bank</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                                <input type="text" name="bank_name" id="bank_name"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                                <input type="text" name="bank_account_number" id="bank_account_number"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama</label>
                                <input type="text" name="bank_account_name" id="bank_account_name"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- System Settings -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Pengaturan Sistem</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Uang *</label>
                                <select name="currency" id="currency" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="IDR">IDR - Indonesian Rupiah</option>
                                    <option value="USD">USD - US Dollar</option>
                                    <option value="EUR">EUR - Euro</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Timezone *</label>
                                <select name="timezone" id="timezone" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Format Tanggal *</label>
                                <select name="date_format" id="date_format" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="d/m/Y">DD/MM/YYYY</option>
                                    <option value="m/d/Y">MM/DD/YYYY</option>
                                    <option value="Y-m-d">YYYY-MM-DD</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Format Waktu *</label>
                                <select name="time_format" id="time_format" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="H:i">24 Jam (HH:MM)</option>
                                    <option value="h:i A">12 Jam (hh:mm AM/PM)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tarif Pajak (%) *</label>
                                <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100" value="11" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeCompanySettingsModal()" 
                                class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // System functions
        function createBackup() {
            if (confirm('Apakah Anda yakin ingin membuat backup database? Proses ini mungkin memakan waktu beberapa menit.')) {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Membuat Backup...';
                button.disabled = true;

                fetch('{{ route("admin.sistem.create-backup") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Backup berhasil dibuat!\nFile: ${data.filename}\nUkuran: ${data.size}`);
                    } else {
                        alert('Error: ' + (data.message || 'Gagal membuat backup'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat membuat backup');
                })
                .finally(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                });
            }
        }

        function showBackupList() {
            fetch('{{ route("admin.sistem.backups") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showBackupModal(data.data);
                    } else {
                        alert('Error: ' + (data.message || 'Gagal mengambil daftar backup'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil daftar backup');
                });
        }

        function showSystemInfo() {
            const modal = document.getElementById('systemInfoModal');
            const content = document.getElementById('systemInfoContent');
            
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Server Information</h4>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">PHP Version:</span>
                                <span class="font-medium">${'{{ PHP_VERSION }}'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Laravel Version:</span>
                                <span class="font-medium">${'{{ app()->version() }}'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Environment:</span>
                                <span class="font-medium">${'{{ app()->environment() }}'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Database Information</h4>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Connection:</span>
                                <span class="font-medium">${'{{ config("database.default") }}'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Database:</span>
                                <span class="font-medium">${'{{ config("database.connections.mysql.database") }}'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function closeSystemInfoModal() {
            document.getElementById('systemInfoModal').classList.add('hidden');
        }

        function clearCache() {
            if (confirm('Apakah Anda yakin ingin menghapus cache aplikasi?')) {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Menghapus...';
                button.disabled = true;

                fetch('{{ route("admin.sistem.clear-cache") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cache berhasil dihapus!');
                    } else {
                        alert('Error: ' + (data.message || 'Gagal menghapus cache'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus cache');
                })
                .finally(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                });
            }
        }

        function runMigration() {
            if (confirm('Apakah Anda yakin ingin menjalankan migrasi database? Pastikan Anda sudah backup data.')) {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Menjalankan...';
                button.disabled = true;

                fetch('{{ route("admin.sistem.run-migration") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Migrasi database berhasil dijalankan!');
                    } else {
                        alert('Error: ' + (data.message || 'Gagal menjalankan migrasi'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menjalankan migrasi');
                })
                .finally(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                });
            }
        }

        function optimizeDb() {
            if (confirm('Apakah Anda yakin ingin mengoptimasi database?')) {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Mengoptimasi...';
                button.disabled = true;

                fetch('{{ route("admin.sistem.optimize-database") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Database berhasil dioptimasi!\nTabel yang dioptimasi: ${data.optimized_tables.length}`);
                    } else {
                        alert('Error: ' + (data.message || 'Gagal mengoptimasi database'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengoptimasi database');
                })
                .finally(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                });
            }
        }

        function showAppSettings() {
            fetch('{{ route("admin.sistem.app-settings") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let settingsHtml = '<div class="space-y-3">';
                        Object.entries(data.data).forEach(([key, value]) => {
                            settingsHtml += `
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">${key.replace(/_/g, ' ').toUpperCase()}:</span>
                                    <span class="text-gray-900">${value}</span>
                                </div>
                            `;
                        });
                        settingsHtml += '</div>';
                        
                        const modal = document.getElementById('systemInfoModal');
                        const content = document.getElementById('systemInfoContent');
                        content.innerHTML = settingsHtml;
                        modal.classList.remove('hidden');
                    } else {
                        alert('Error: ' + (data.message || 'Gagal mengambil pengaturan aplikasi'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil pengaturan aplikasi');
                });
        }

        function showEnvEditor() {
            alert('Fitur editor environment akan segera tersedia untuk keamanan sistem');
        }

        // Backup Modal Functions
        function showBackupModal(backups) {
            const modal = document.getElementById('backupListModal');
            const content = document.getElementById('backupListContent');
            
            if (backups.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <p class="text-gray-500">Belum ada backup yang tersedia</p>
                        <button onclick="createBackup()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Buat Backup Pertama
                        </button>
                    </div>
                `;
            } else {
                let backupHtml = `
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama File</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                `;
                
                backups.forEach(backup => {
                    backupHtml += `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${backup.filename}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${backup.size}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${backup.created_at}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="downloadBackup('${backup.filename}')" class="text-blue-600 hover:text-blue-900">Download</button>
                                <button onclick="restoreBackup('${backup.filename}')" class="text-green-600 hover:text-green-900">Restore</button>
                                <button onclick="deleteBackup('${backup.filename}')" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    `;
                });
                
                backupHtml += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                content.innerHTML = backupHtml;
            }
            
            modal.classList.remove('hidden');
        }

        function closeBackupModal() {
            document.getElementById('backupListModal').classList.add('hidden');
        }

        function downloadBackup(filename) {
            window.location.href = `{{ route('admin.sistem.backups.download', '') }}/${filename}`;
        }

        function restoreBackup(filename) {
            if (confirm(`Apakah Anda yakin ingin restore database dari backup "${filename}"?\n\nPERINGATAN: Ini akan mengganti semua data yang ada dengan data dari backup!`)) {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Restoring...';
                button.disabled = true;

                fetch('{{ route("admin.sistem.restore-backup") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ filename: filename })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Database berhasil direstore dari backup!');
                        closeBackupModal();
                    } else {
                        alert('Error: ' + (data.message || 'Gagal restore database'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat restore database');
                })
                .finally(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                });
            }
        }

        function deleteBackup(filename) {
            if (confirm(`Apakah Anda yakin ingin menghapus backup "${filename}"?`)) {
                fetch(`{{ route('admin.sistem.backups.delete', '') }}/${filename}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Backup berhasil dihapus!');
                        showBackupList(); // Refresh the list
                    } else {
                        alert('Error: ' + (data.message || 'Gagal menghapus backup'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus backup');
                });
            }
        }

        // Company Settings Modal Functions
        function openCompanySettingsModal() {
            // Load current settings
            fetch('{{ route("admin.sistem.pengaturan.settings") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateCompanySettingsForm(data.data);
                    }
                    document.getElementById('companySettingsModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error loading settings:', error);
                    document.getElementById('companySettingsModal').classList.remove('hidden');
                });
        }

        function closeCompanySettingsModal() {
            document.getElementById('companySettingsModal').classList.add('hidden');
        }

        function populateCompanySettingsForm(settings) {
            if (!settings) return;

            // Populate form fields
            const fields = [
                'company_name', 'company_code', 'company_address', 'company_phone', 
                'company_email', 'company_website', 'npwp', 'nib', 'siup', 'tdp',
                'bank_name', 'bank_account_number', 'bank_account_name',
                'currency', 'timezone', 'date_format', 'time_format', 'tax_rate'
            ];

            fields.forEach(field => {
                const element = document.getElementById(field);
                if (element && settings[field]) {
                    element.value = settings[field];
                }
            });

            // Show logo preview if exists
            if (settings.company_logo) {
                const logoPreview = document.getElementById('logo_preview');
                const logoImg = logoPreview.querySelector('img');
                logoImg.src = '{{ asset("storage/") }}/' + settings.company_logo;
                logoPreview.classList.remove('hidden');
            }

            // Show favicon preview if exists
            if (settings.company_favicon) {
                const faviconPreview = document.getElementById('favicon_preview');
                const faviconImg = faviconPreview.querySelector('img');
                faviconImg.src = '{{ asset("storage/") }}/' + settings.company_favicon;
                faviconPreview.classList.remove('hidden');
            }
        }

        // Handle form submission
        document.getElementById('companySettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("admin.sistem.pengaturan.update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pengaturan berhasil disimpan!');
                    closeCompanySettingsModal();
                    // Optionally reload page to reflect changes
                    // location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                    if (data.errors) {
                        console.log('Validation errors:', data.errors);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan pengaturan');
            });
        });

        // Handle file preview
        document.getElementById('company_logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('logo_preview');
                    const img = preview.querySelector('img');
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('company_favicon').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('favicon_preview');
                    const img = preview.querySelector('img');
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-layouts.admin>