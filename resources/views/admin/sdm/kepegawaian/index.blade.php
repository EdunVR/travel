<x-layouts.admin title="Kepegawaian & Rekrutmen">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Kepegawaian & Rekrutmen</h1>
                <p class="text-sm text-slate-600 mt-1">Kelola data karyawan dan rekrutmen</p>
            </div>
            <div class="flex gap-2">
                <button onclick="openAnnouncementModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2">
                    <i class='bx bx-volume-full'></i>
                    <span>Pengumuman Mesin</span>
                </button>
                @hasPermission('hrm.karyawan.create')
                <button onclick="openAddModal()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center gap-2">
                    <i class='bx bx-plus'></i>
                    <span>Tambah Karyawan</span>
                </button>
                @endhasPermission
            </div>
        </div>

        {{-- Announcement Modal --}}
        <div id="announcementModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <i class='bx bx-volume-full text-xl text-purple-600'></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Kirim Pengumuman ke Mesin Absensi</h3>
                        <p class="text-xs text-slate-500">Teks akan diucapkan oleh speaker mesin</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Teks Pengumuman</label>
                    <textarea id="announcementText" rows="3"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        placeholder="Contoh: Halo, Selamat Pagi semua karyawan..."></textarea>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-slate-500 mb-2">Teks cepat:</p>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="setAnnouncementText('Halo, Selamat Pagi')"
                            class="px-2 py-1 text-xs bg-slate-100 rounded hover:bg-slate-200">Selamat Pagi</button>
                        <button onclick="setAnnouncementText('Selamat Siang, jangan lupa istirahat')"
                            class="px-2 py-1 text-xs bg-slate-100 rounded hover:bg-slate-200">Selamat Siang</button>
                        <button onclick="setAnnouncementText('Perhatian! Rapat akan dimulai 15 menit lagi')"
                            class="px-2 py-1 text-xs bg-slate-100 rounded hover:bg-slate-200">Pengingat Rapat</button>
                        <button onclick="setAnnouncementText('Waktu pulang telah tiba, jangan lupa absen keluar')"
                            class="px-2 py-1 text-xs bg-slate-100 rounded hover:bg-slate-200">Waktu Pulang</button>
                    </div>
                </div>

                <div id="announcementStatus" class="hidden mb-4 p-3 rounded-lg text-sm"></div>

                <div class="flex gap-3">
                    <button onclick="closeAnnouncementModal()" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button onclick="sendAnnouncement()" id="sendAnnouncementBtn"
                        class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center justify-center gap-2">
                        <i class='bx bx-send'></i>
                        <span>Kirim & Putar</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4" id="statsCards">
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class='bx bx-user-check text-2xl text-green-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Karyawan Aktif</p>
                        <p class="text-2xl font-bold text-slate-900" id="activeCount">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <i class='bx bx-user-minus text-2xl text-yellow-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Tidak Aktif</p>
                        <p class="text-2xl font-bold text-slate-900" id="inactiveCount">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class='bx bx-user-x text-2xl text-red-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Resign</p>
                        <p class="text-2xl font-bold text-slate-900" id="resignedCount">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class='bx bx-group text-2xl text-blue-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Total Karyawan</p>
                        <p class="text-2xl font-bold text-slate-900" id="totalCount">0</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Outlet</label>
                    <select id="outletFilter" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="all">Semua Outlet</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="all">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                        <option value="resigned">Resign</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Departemen</label>
                    <select id="departmentFilter" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="all">Semua Departemen</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Cari</label>
                    <input type="text" id="searchInput" placeholder="Cari nama, posisi, telepon..." class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div class="flex items-end gap-2">
                    <button onclick="loadData()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex-1">
                        <i class='bx bx-search'></i> Filter
                    </button>
                    @can('hrm.karyawan.export')
                    <button onclick="exportPdf()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class='bx bxs-file-pdf'></i>
                    </button>
                    <button onclick="exportExcel()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class='bx bxs-file'></i>
                    </button>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Data Grid --}}
        <div class="bg-white rounded-xl shadow-card border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="employeeTable">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Outlet</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Nama</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Posisi</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Departemen</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Telepon</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">
                                <div class="flex items-center gap-1">
                                    Gaji
                                    <button onclick="toggleAllSalary(this)" title="Tampilkan/sembunyikan semua gaji"
                                            class="text-slate-400 hover:text-slate-600 transition-colors ml-1"
                                            data-visible="false">
                                        <i class='bx bx-hide text-base' id="globalSalaryEyeIcon"></i>
                                    </button>
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Tgl Bergabung</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody">
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                <i class='bx bx-loader-alt bx-spin text-3xl'></i>
                                <p class="mt-2">Memuat data...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal fade" id="employeeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-xl border-0 shadow-xl">
                <div class="modal-header border-b border-slate-200 bg-slate-50">
                    <h5 class="modal-title font-semibold" id="modalTitle">Tambah Karyawan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="employeeForm">
                    <div class="modal-body p-6">
                        <input type="hidden" id="employeeId">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Outlet <span class="text-red-500">*</span></label>
                                <select id="outlet_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                    <option value="">Pilih Outlet</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="name" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Posisi <span class="text-red-500">*</span></label>
                                <input type="text" id="position" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Departemen</label>
                                <input type="text" id="department" class="w-full px-3 py-2 border border-slate-300 rounded-lg" list="departmentList">
                                <datalist id="departmentList"></datalist>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                                <select id="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                    <option value="resigned">Resign</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Telepon</label>
                                <input type="text" id="phone" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                                <input type="email" id="email" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Gaji (Rp)</label>
                                <div class="relative">
                                    <input type="password" id="salary"
                                           class="w-full px-3 py-2 pr-10 border border-slate-300 rounded-lg"
                                           min="0" placeholder="Masukkan nominal gaji"
                                           style="-webkit-text-security: disc;">
                                    <button type="button" onclick="toggleSalaryVisibility()"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                                            title="Tampilkan/sembunyikan gaji">
                                        <i class='bx bx-hide text-lg' id="salaryEyeIcon"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tarif Per Jam (Rp)</label>
                                <input type="number" id="hourly_rate" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Bergabung</label>
                                <input type="date" id="join_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">UID Kartu</label>
                                <div class="flex gap-2">
                                    <input type="text" id="rfid_uid" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg" placeholder="UID kartu RFID" readonly>
                                    <button type="button" id="startDetectionBtn" onclick="startRfidDetection()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                                        <i class='bx bx-radar'></i>
                                        <span>Mulai Deteksi</span>
                                    </button>
                                </div>
                                <small class="text-slate-500 mt-1">Tekan "Mulai Deteksi" lalu tempelkan kartu RFID ke ESP32 CAM</small>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Alamat</label>
                                <textarea id="address" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Job Description</label>
                                <div id="jobdeskContainer" class="space-y-2">
                                    <div class="flex gap-2">
                                        <input type="text" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg jobdesk-item" placeholder="Tugas dan tanggung jawab...">
                                        <button type="button" onclick="addJobdesk()" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                            <i class='bx bx-plus'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- ── Buat Akun User Akses ─────────────────────────────────── --}}
                            <div class="md:col-span-2" id="createUserSection">
                                <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
                                    <label class="flex items-center gap-3 cursor-pointer select-none">
                                        <input type="checkbox" id="create_user" onchange="toggleUserFields(this.checked)"
                                               class="w-4 h-4 text-primary-600 rounded border-slate-300">
                                        <span class="font-medium text-slate-700">Buat juga akun user untuk akses aplikasi</span>
                                    </label>

                                    <div id="userAccessFields" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                                Role / Hak Akses <span class="text-red-500">*</span>
                                            </label>
                                            <select id="user_role_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                                <option value="">— Pilih Role —</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                                Password <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="password" id="user_password"
                                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg pr-10"
                                                       placeholder="Min. 6 karakter">
                                                <button type="button" onclick="togglePasswordVisibility()"
                                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                                    <i class='bx bx-hide' id="passwordEyeIcon"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="md:col-span-2">
                                            <p class="text-xs text-slate-500 flex items-start gap-1.5">
                                                <i class='bx bx-info-circle text-blue-400 text-sm mt-0.5'></i>
                                                Username login menggunakan <strong>Email</strong> karyawan. Pastikan email sudah diisi dan belum terdaftar.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer border-t border-slate-200 bg-slate-50">
                        <button type="button" class="px-4 py-2 text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50" data-dismiss="modal">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let employees = [];

        $(document).ready(function() {
            loadData();
            loadDepartments();

            $('#outletFilter, #statusFilter, #departmentFilter').on('change', loadData);
            $('#searchInput').on('keyup', debounce(loadData, 500));
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        async function loadData() {
            try {
                const params = new URLSearchParams({
                    outlet_filter: $('#outletFilter').val(),
                    status_filter: $('#statusFilter').val(),
                    department_filter: $('#departmentFilter').val(),
                    search: $('#searchInput').val()
                });

                const response = await fetch(`{{ route('sdm.kepegawaian.data') }}?${params}`);
                const result = await response.json();

                if (result.success) {
                    employees = result.data;
                    renderTable();
                    updateStats();
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Gagal memuat data');
            }
        }

        async function loadDepartments() {
            try {
                const response = await fetch(`{{ route('sdm.kepegawaian.departments') }}`);
                const result = await response.json();

                if (result.success) {
                    const select = $('#departmentFilter');
                    const datalist = $('#departmentList');
                    
                    result.data.forEach(dept => {
                        select.append(`<option value="${dept}">${dept}</option>`);
                        datalist.append(`<option value="${dept}">`);
                    });
                }
            } catch (error) {
                console.error('Error loading departments:', error);
            }
        }

        function renderTable() {
            const tbody = $('#employeeTableBody');
            
            if (employees.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                            <i class='bx bx-info-circle text-3xl'></i>
                            <p class="mt-2">Tidak ada data karyawan</p>
                        </td>
                    </tr>
                `);
                return;
            }

            const rows = employees.map(emp => {
                const statusColors = {
                    active: 'bg-green-100 text-green-700',
                    inactive: 'bg-yellow-100 text-yellow-700',
                    resigned: 'bg-red-100 text-red-700'
                };

                return `
                    <tr class="border-b border-slate-200 hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-700">${emp.outlet_name}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900">${emp.name}</div>
                            <div class="text-sm text-slate-500">${emp.email}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-700">${emp.position}</td>
                        <td class="px-4 py-3 text-slate-700">${emp.department}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${statusColors[emp.status]}">
                                ${emp.status_label}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700">${emp.phone}</td>
                        <td class="px-4 py-3 text-slate-700">
                            <div class="flex items-center gap-1.5">
                                <span class="salary-value" data-real="${emp.salary_formatted}">
                                    <span class="salary-display">••••••</span>
                                </span>
                                <button onclick="toggleRowSalary(this)" title="Tampilkan gaji"
                                        class="text-slate-400 hover:text-slate-600 transition-colors shrink-0">
                                    <i class='bx bx-hide text-sm salary-eye-icon'></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-700">${emp.join_date}</td>
                        <td class="px-4 py-3 text-center">
                            @hasPermission('hrm.karyawan.edit')
                            <button onclick="editEmployee(${emp.id})" class="px-2 py-1 text-blue-600 hover:bg-blue-50 rounded">
                                <i class='bx bx-edit'></i>
                            </button>
                            @endhasPermission
                            @hasPermission('hrm.karyawan.delete')
                            <button onclick="deleteEmployee(${emp.id})" class="px-2 py-1 text-red-600 hover:bg-red-50 rounded">
                                <i class='bx bx-trash'></i>
                            </button>
                            @endhasPermission
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.html(rows);
        }

        function updateStats() {
            const stats = {
                active: employees.filter(e => e.status === 'active').length,
                inactive: employees.filter(e => e.status === 'inactive').length,
                resigned: employees.filter(e => e.status === 'resigned').length,
                total: employees.length
            };

            $('#activeCount').text(stats.active);
            $('#inactiveCount').text(stats.inactive);
            $('#resignedCount').text(stats.resigned);
            $('#totalCount').text(stats.total);
        }

        function openAddModal() {
            $('#modalTitle').text('Tambah Karyawan');
            $('#employeeForm')[0].reset();
            $('#employeeId').val('');
            resetJobdesk();
            // Reset user access fields
            $('#create_user').prop('checked', false);
            $('#userAccessFields').addClass('hidden');
            $('#createUserSection').show();
            // Reset salary field ke mode tersembunyi
            const salInput = document.getElementById('salary');
            if (salInput) { salInput.type = 'password'; }
            const salIcon = document.getElementById('salaryEyeIcon');
            if (salIcon) { salIcon.className = 'bx bx-hide text-lg'; }
            $('#employeeModal').modal('show');
        }

        async function editEmployee(id) {
            try {
                const response = await fetch(`{{ route('sdm.kepegawaian.index') }}/${id}`);
                const result = await response.json();

                if (result.success) {
                    const emp = result.data;
                    
                    $('#modalTitle').text('Edit Karyawan');
                    $('#employeeId').val(emp.id);
                    $('#outlet_id').val(emp.outlet_id);
                    $('#name').val(emp.name);
                    $('#position').val(emp.position);
                    $('#department').val(emp.department);
                    $('#status').val(emp.status);
                    $('#phone').val(emp.phone);
                    $('#email').val(emp.email);
                    $('#address').val(emp.address);
                    $('#salary').val(emp.salary);
                    $('#hourly_rate').val(emp.hourly_rate);
                    $('#join_date').val(emp.join_date);
                    $('#fingerprint_id').val(emp.fingerprint_id);
                    $('#rfid_uid').val(emp.rfid_uid);
                    
                    loadJobdesk(emp.jobdesk);
                    
                    // Sembunyikan section buat user saat edit
                    $('#create_user').prop('checked', false);
                    $('#userAccessFields').addClass('hidden');
                    $('#createUserSection').hide();

                    $('#employeeModal').modal('show');
                }
            } catch (error) {
                console.error('Error loading employee:', error);
                alert('Gagal memuat data karyawan');
            }
        }

        $('#employeeForm').on('submit', async function(e) {
            e.preventDefault();

            const id = $('#employeeId').val();
            const jobdesk = [];
            $('.jobdesk-item').each(function() {
                const val = $(this).val().trim();
                if (val) jobdesk.push(val);
            });

            const createUser = !id && $('#create_user').is(':checked');

            // Validasi client-side untuk field user akses
            if (createUser) {
                if (!$('#email').val().trim()) {
                    alert('Email wajib diisi untuk membuat akun user.');
                    $('#email').focus();
                    return;
                }
                if (!$('#user_role_id').val()) {
                    alert('Pilih Role untuk akun user.');
                    $('#user_role_id').focus();
                    return;
                }
                if (!$('#user_password').val() || $('#user_password').val().length < 6) {
                    alert('Password minimal 6 karakter.');
                    $('#user_password').focus();
                    return;
                }
            }

            const data = {
                outlet_id:     $('#outlet_id').val(),
                name:          $('#name').val(),
                position:      $('#position').val(),
                department:    $('#department').val(),
                status:        $('#status').val(),
                phone:         $('#phone').val(),
                email:         $('#email').val(),
                address:       $('#address').val(),
                salary:        $('#salary').val(),
                hourly_rate:   $('#hourly_rate').val(),
                join_date:     $('#join_date').val(),
                fingerprint_id:$('#fingerprint_id').val(),
                rfid_uid:      $('#rfid_uid').val(),
                jobdesk:       jobdesk,
                // User akses fields
                create_user:   createUser,
                user_role_id:  createUser ? $('#user_role_id').val() : null,
                user_password: createUser ? $('#user_password').val() : null,
                _token:        '{{ csrf_token() }}'
            };

            try {
                const url = id 
                    ? `{{ route('sdm.kepegawaian.index') }}/${id}`
                    : `{{ route('sdm.kepegawaian.store') }}`;
                
                const method = id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    $('#employeeModal').modal('hide');
                    loadData();
                    alert(result.message);
                } else {
                    alert(result.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error saving employee:', error);
                alert('Gagal menyimpan data');
            }
        });

        async function deleteEmployee(id) {
            if (!confirm('Yakin ingin menghapus karyawan ini?')) return;

            try {
                const response = await fetch(`{{ route('sdm.kepegawaian.index') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    loadData();
                    alert(result.message);
                } else {
                    alert(result.message || 'Gagal menghapus data');
                }
            } catch (error) {
                console.error('Error deleting employee:', error);
                alert('Gagal menghapus data');
            }
        }

        function addJobdesk() {
            const container = $('#jobdeskContainer');
            const newItem = `
                <div class="flex gap-2">
                    <input type="text" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg jobdesk-item" placeholder="Tugas dan tanggung jawab...">
                    <button type="button" onclick="$(this).parent().remove()" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class='bx bx-minus'></i>
                    </button>
                </div>
            `;
            container.append(newItem);
        }

        function resetJobdesk() {
            $('#jobdeskContainer').html(`
                <div class="flex gap-2">
                    <input type="text" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg jobdesk-item" placeholder="Tugas dan tanggung jawab...">
                    <button type="button" onclick="addJobdesk()" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class='bx bx-plus'></i>
                    </button>
                </div>
            `);
        }

        function loadJobdesk(jobdesk) {
            const container = $('#jobdeskContainer');
            container.empty();

            if (!jobdesk || jobdesk.length === 0) {
                resetJobdesk();
                return;
            }

            jobdesk.forEach((item, index) => {
                const html = `
                    <div class="flex gap-2">
                        <input type="text" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg jobdesk-item" value="${item}" placeholder="Tugas dan tanggung jawab...">
                        <button type="button" onclick="${index === 0 ? 'addJobdesk()' : '$(this).parent().remove()'}" class="px-3 py-2 ${index === 0 ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'} text-white rounded-lg">
                            <i class='bx ${index === 0 ? 'bx-plus' : 'bx-minus'}'></i>
                        </button>
                    </div>
                `;
                container.append(html);
            });
        }

        function exportPdf() {
            const params = new URLSearchParams({
                outlet_filter: $('#outletFilter').val(),
                status_filter: $('#statusFilter').val(),
                department_filter: $('#departmentFilter').val()
            });
            window.open(`{{ route('sdm.kepegawaian.export.pdf') }}?${params}`, '_blank');
        }

        function exportExcel() {
            const params = new URLSearchParams({
                outlet_filter: $('#outletFilter').val(),
                status_filter: $('#statusFilter').val(),
                department_filter: $('#departmentFilter').val()
            });
            window.location.href = `{{ route('sdm.kepegawaian.export.excel') }}?${params}`;
        }

        // ── Salary Visibility Helpers ────────────────────────────────────────────

        // Toggle salary di input modal
        function toggleSalaryVisibility() {
            const input = document.getElementById('salary');
            const icon  = document.getElementById('salaryEyeIcon');
            if (!input || !icon) return;
            if (input.type === 'password') {
                input.type = 'number';
                icon.className = 'bx bx-show text-lg';
            } else {
                input.type = 'password';
                icon.className = 'bx bx-hide text-lg';
            }
        }

        // Toggle salary pada satu baris tabel
        function toggleRowSalary(btn) {
            const cell  = btn.closest('td');
            const span  = cell.querySelector('.salary-display');
            const icon  = btn.querySelector('.salary-eye-icon');
            const real  = cell.querySelector('.salary-value').getAttribute('data-real');
            const isHidden = span.textContent.includes('•');
            if (isHidden) {
                span.textContent = real;
                icon.className = 'bx bx-show text-sm salary-eye-icon';
            } else {
                span.textContent = '••••••';
                icon.className = 'bx bx-hide text-sm salary-eye-icon';
            }
        }

        // Toggle semua salary di tabel sekaligus (dari header)
        function toggleAllSalary(btn) {
            const isVisible = btn.getAttribute('data-visible') === 'true';
            const icon = document.getElementById('globalSalaryEyeIcon');

            document.querySelectorAll('.salary-value').forEach(function(el) {
                const display = el.querySelector('.salary-display');
                const rowBtn  = el.closest('td').querySelector('[onclick="toggleRowSalary(this)"]');
                const rowIcon = rowBtn ? rowBtn.querySelector('.salary-eye-icon') : null;
                const real    = el.getAttribute('data-real');
                if (!isVisible) {
                    display.textContent = real;
                    if (rowIcon) rowIcon.className = 'bx bx-show text-sm salary-eye-icon';
                } else {
                    display.textContent = '••••••';
                    if (rowIcon) rowIcon.className = 'bx bx-hide text-sm salary-eye-icon';
                }
            });

            btn.setAttribute('data-visible', (!isVisible).toString());
            icon.className = isVisible ? 'bx bx-hide text-base' : 'bx bx-show text-base';
        }

        // ── User Access Helper Functions ─────────────────────────────────────────
        function toggleUserFields(checked) {
                // Jika email sudah diisi, fokus ke role
                if ($('#email').val().trim()) {
                    $('#user_role_id').focus();
                } else {
                    $('#email').focus();
                }
            } else {
                $('#userAccessFields').addClass('hidden');
                $('#user_role_id').val('');
                $('#user_password').val('');
            }
        }

        function togglePasswordVisibility() {
            const input = document.getElementById('user_password');
            const icon  = document.getElementById('passwordEyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bx bx-show';
            } else {
                input.type = 'password';
                icon.className = 'bx bx-hide';
            }
        }

        // RFID Detection Functions
        let detectionInterval = null;
        let isDetecting = false;
        let detectionAttempts = 0;
        const MAX_DETECTION_ATTEMPTS = 60; // 60 detik

        async function startRfidDetection() {
            if (isDetecting) {
                stopRfidDetection();
                return;
            }

            try {
                // Set ESP32 to register mode
                const response = await fetch('{{ url("/api/morra/api/rfid/mode") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ mode: 'register' })
                });

                const result = await response.json();
                
                if (result.success) {
                    isDetecting = true;
                    detectionAttempts = 0;
                    updateDetectionButton(true);
                    
                    // Start polling for detected card - lebih cepat (300ms)
                    detectionInterval = setInterval(checkForDetectedCard, 300);
                    
                    // Auto stop after 60 seconds
                    setTimeout(() => {
                        if (isDetecting) {
                            stopRfidDetection();
                            alert('Deteksi dihentikan otomatis setelah 60 detik. Silakan coba lagi.');
                        }
                    }, 60000);
                    
                    // Show visual feedback
                    showDetectionStatus('Menunggu kartu RFID...', 'info');
                } else {
                    alert('Gagal mengaktifkan mode deteksi: ' + result.message);
                }
            } catch (error) {
                console.error('Error starting detection:', error);
                alert('Gagal menghubungi mesin absensi. Pastikan perangkat terhubung.');
            }
        }

        async function stopRfidDetection() {
            isDetecting = false;
            updateDetectionButton(false);
            
            if (detectionInterval) {
                clearInterval(detectionInterval);
                detectionInterval = null;
            }
            
            // Reset mode to attendance
            try {
                await fetch('{{ url("/api/morra/api/rfid/mode") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ mode: 'attendance' })
                });
            } catch (error) {
                console.error('Error resetting mode:', error);
            }
            
            hideDetectionStatus();
        }

        function updateDetectionButton(detecting) {
            const btn = $('#startDetectionBtn');
            const icon = btn.find('i');
            const text = btn.find('span');
            
            if (detecting) {
                btn.removeClass('bg-blue-600 hover:bg-blue-700').addClass('bg-red-600 hover:bg-red-700');
                icon.removeClass('bx-radar').addClass('bx-stop');
                text.text('Hentikan Deteksi');
            } else {
                btn.removeClass('bg-red-600 hover:bg-red-700').addClass('bg-blue-600 hover:bg-blue-700');
                icon.removeClass('bx-stop').addClass('bx-radar');
                text.text('Mulai Deteksi');
            }
        }

        async function checkForDetectedCard() {
            if (!isDetecting) return;
            
            detectionAttempts++;
            
            // Update status setiap 3 detik
            if (detectionAttempts % 10 === 0) {
                showDetectionStatus(`Menunggu kartu... (${Math.floor(detectionAttempts / 10)}s)`, 'info');
            }
            
            try {
                const response = await fetch('{{ url("/api/detected-rfid-uid") }}');
                const result = await response.json();
                
                if (result.success && result.uid) {
                    // Card detected, fill the form
                    $('#rfid_uid').val(result.uid);
                    stopRfidDetection();
                    
                    // Show success message with clear instruction to save
                    showDetectionStatus('✅ Kartu terdeteksi! UID: ' + result.uid + ' — Klik SIMPAN untuk menyimpan.', 'success');
                    
                    // Play success sound
                    playSuccessSound();
                    
                    // Don't auto-hide - keep showing until user saves
                }
            } catch (error) {
                console.error('Error checking for detected card:', error);
            }
        }
        
        function showDetectionStatus(message, type) {
            let statusDiv = $('#rfidDetectionStatus');
            
            if (statusDiv.length === 0) {
                statusDiv = $('<div id="rfidDetectionStatus" class="mt-2 p-3 rounded-lg text-sm"></div>');
                $('#rfid_uid').parent().append(statusDiv);
            }
            
            statusDiv.removeClass('bg-blue-100 text-blue-700 bg-green-100 text-green-700 bg-yellow-100 text-yellow-700');
            
            if (type === 'success') {
                statusDiv.addClass('bg-green-100 text-green-700');
            } else if (type === 'info') {
                statusDiv.addClass('bg-blue-100 text-blue-700');
            } else {
                statusDiv.addClass('bg-yellow-100 text-yellow-700');
            }
            
            statusDiv.html('<i class="bx bx-info-circle"></i> ' + message).show();
        }
        
        function hideDetectionStatus() {
            $('#rfidDetectionStatus').fadeOut();
        }
        
        function playSuccessSound() {
            // Create audio context for beep sound
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.2);
            } catch (e) {
                console.log('Audio not supported');
            }
        }

        // Clean up on modal close
        $('#employeeModal').on('hidden.bs.modal', function() {
            if (isDetecting) {
                stopRfidDetection();
            }
        });

        // =============================================
        // ANNOUNCEMENT FUNCTIONS
        // =============================================
        function openAnnouncementModal() {
            document.getElementById('announcementModal').classList.remove('hidden');
            document.getElementById('announcementStatus').classList.add('hidden');
            document.getElementById('announcementText').value = '';
        }

        function closeAnnouncementModal() {
            document.getElementById('announcementModal').classList.add('hidden');
        }

        function setAnnouncementText(text) {
            document.getElementById('announcementText').value = text;
        }

        async function sendAnnouncement() {
            const text = document.getElementById('announcementText').value.trim();
            if (!text) {
                showAnnouncementStatus('Teks tidak boleh kosong', 'error');
                return;
            }

            const btn = document.getElementById('sendAnnouncementBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> <span>Memproses...</span>';

            showAnnouncementStatus('Sedang generate audio TTS...', 'info');

            try {
                const response = await fetch('{{ url("/api/morra/api/rfid/announce") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ text: text })
                });

                const result = await response.json();

                if (result.success) {
                    showAnnouncementStatus(
                        '✅ Berhasil! Audio dikirim ke mesin absensi. Mesin akan memainkan dalam max 10 detik.',
                        'success'
                    );
                    // Auto close setelah 3 detik
                    setTimeout(() => closeAnnouncementModal(), 3000);
                } else {
                    showAnnouncementStatus('❌ Gagal: ' + (result.message || 'Error tidak diketahui'), 'error');
                }
            } catch (error) {
                showAnnouncementStatus('❌ Error koneksi: ' + error.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-send"></i> <span>Kirim & Putar</span>';
            }
        }

        function showAnnouncementStatus(message, type) {
            const el = document.getElementById('announcementStatus');
            el.className = 'mb-4 p-3 rounded-lg text-sm';
            if (type === 'success') el.classList.add('bg-green-100', 'text-green-700');
            else if (type === 'error') el.classList.add('bg-red-100', 'text-red-700');
            else el.classList.add('bg-blue-100', 'text-blue-700');
            el.textContent = message;
            el.classList.remove('hidden');
        }

        // Tutup modal saat klik backdrop
        document.getElementById('announcementModal').addEventListener('click', function(e) {
            if (e.target === this) closeAnnouncementModal();
        });
    </script>
    @endpush
</x-layouts.admin>
