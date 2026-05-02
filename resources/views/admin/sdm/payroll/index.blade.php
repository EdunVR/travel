<x-layouts.admin title="Penggajian / Payroll">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Penggajian / Payroll</h1>
                <p class="text-sm text-slate-600 mt-1">Kelola penggajian karyawan</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('sdm.payroll.coa.index') }}" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 flex items-center gap-2">
                    <i class='bx bx-cog'></i>
                    <span>Setting COA</span>
                </a>
                @hasPermission('hrm.payroll.create')
                <button onclick="openAddModal()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center gap-2">
                    <i class='bx bx-plus'></i>
                    <span>Tambah Payroll</span>
                </button>
                @endhasPermission
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4" id="statsCards">
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class='bx bx-money text-2xl text-blue-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Total Payroll</p>
                        <p class="text-2xl font-bold text-slate-900" id="totalPayroll">Rp 0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <i class='bx bx-time text-2xl text-yellow-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Draft</p>
                        <p class="text-2xl font-bold text-slate-900" id="draftCount">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class='bx bx-check-circle text-2xl text-green-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Approved</p>
                        <p class="text-2xl font-bold text-slate-900" id="approvedCount">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-card p-4 border border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class='bx bx-check-double text-2xl text-purple-600'></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600">Dibayar</p>
                        <p class="text-2xl font-bold text-slate-900" id="paidCount">0</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
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
                    <label class="block text-sm font-medium text-slate-700 mb-2">Periode</label>
                    <input type="month" id="periodFilter" value="{{ date('Y-m') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="all">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="approved">Approved</option>
                        <option value="paid">Dibayar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Cari</label>
                    <input type="text" id="searchInput" placeholder="Cari karyawan..." class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div class="flex items-end gap-2">
                    <button onclick="loadData()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex-1">
                        <i class='bx bx-search'></i> Filter
                    </button>
                    <button onclick="exportPdf()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class='bx bxs-file-pdf'></i>
                    </button>
                    <button onclick="exportExcel()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class='bx bxs-file'></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="bg-white rounded-xl shadow-card border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="payrollTable">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Outlet</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Karyawan</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Periode</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Tgl Bayar</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Gaji Pokok</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Gaji Bersih</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Status</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="payrollTableBody">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">
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
    <div class="modal fade" id="payrollModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content rounded-xl border-0 shadow-xl">
                <div class="modal-header border-b border-slate-200 bg-slate-50">
                    <h5 class="modal-title font-semibold" id="modalTitle">Tambah Payroll</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="payrollForm">
                    <div class="modal-body p-6">
                        <input type="hidden" id="payrollId">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Basic Info --}}
                            <div class="md:col-span-2 bg-slate-50 p-4 rounded-lg">
                                <h6 class="font-semibold text-slate-900 mb-3">Informasi Dasar</h6>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Outlet <span class="text-red-500">*</span></label>
                                        <select id="outlet_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required onchange="loadEmployees()">
                                            <option value="">Pilih Outlet</option>
                                            @foreach($outlets as $outlet)
                                                <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Karyawan <span class="text-red-500">*</span></label>
                                        <select id="recruitment_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required onchange="loadEmployeeData()">
                                            <option value="">Pilih Karyawan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Periode <span class="text-red-500">*</span></label>
                                        <input type="month" id="period" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Salary & Attendance --}}
                            <div class="md:col-span-2 bg-blue-50 p-4 rounded-lg">
                                <h6 class="font-semibold text-slate-900 mb-3">Gaji & Kehadiran</h6>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Gaji Pokok <span class="text-red-500">*</span></label>
                                        <input type="number" id="basic_salary" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required min="0" onchange="calculateSalary()">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Hari Kerja <span class="text-red-500">*</span></label>
                                        <input type="number" id="working_days" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required min="0">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Hari Hadir <span class="text-red-500">*</span></label>
                                        <input type="number" id="present_days" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required min="0">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Bayar <span class="text-red-500">*</span></label>
                                        <input type="date" id="payment_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Additions --}}
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h6 class="font-semibold text-slate-900 mb-3">Penambahan</h6>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Jam Lembur</label>
                                        <input type="number" id="overtime_hours" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0" step="0.5" onchange="calculateOvertimePay()">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Upah Lembur (Rp)</label>
                                        <input type="number" id="overtime_pay" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0" onchange="calculateSalary()">
                                        <small class="text-slate-500 text-xs mt-1 block">Otomatis dihitung: Jam Lembur × Tarif per Jam</small>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Bonus (Rp)</label>
                                        <input type="number" id="bonus" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0" onchange="calculateSalary()">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Tunjangan (Rp)</label>
                                        <input type="number" id="allowance" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0" onchange="calculateSalary()">
                                    </div>
                                </div>
                            </div>

                            {{-- Deductions --}}
                            <div class="bg-red-50 p-4 rounded-lg">
                                <h6 class="font-semibold text-slate-900 mb-3">Potongan</h6>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Hari Tidak Hadir</label>
                                        <input type="number" id="absent_days" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Denda Absen (Rp)</label>
                                        <input type="number" id="absent_penalty" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0" onchange="calculateSalary()">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Hari Terlambat</label>
                                        <input type="number" id="late_days" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Denda Telat (Rp)</label>
                                        <input type="number" id="late_penalty" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0" onchange="calculateSalary()">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Potongan Pinjaman (Rp)</label>
                                        <input type="number" id="loan_deduction" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0" onchange="calculateSalary()">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Potongan Lain (Rp)</label>
                                        <input type="number" id="deduction" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0" onchange="calculateSalary()">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Pajak (Rp)</label>
                                        <input type="number" id="tax" class="w-full px-3 py-2 border border-slate-300 rounded-lg" min="0" onchange="calculateSalary()">
                                    </div>
                                </div>
                            </div>

                            {{-- Summary --}}
                            <div class="md:col-span-2 bg-purple-50 p-4 rounded-lg">
                                <h6 class="font-semibold text-slate-900 mb-3">Ringkasan</h6>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-white p-3 rounded-lg">
                                        <p class="text-sm text-slate-600">Gaji Kotor</p>
                                        <p class="text-xl font-bold text-slate-900" id="gross_salary_display">Rp 0</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg">
                                        <p class="text-sm text-slate-600">Gaji Bersih</p>
                                        <p class="text-xl font-bold text-green-600" id="net_salary_display">Rp 0</p>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Catatan</label>
                                <textarea id="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
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
        let payrolls = [];
        let employees = [];

        $(document).ready(function() {
            loadData();
            $('#outletFilter, #periodFilter, #statusFilter').on('change', loadData);
            $('#searchInput').on('keyup', debounce(loadData, 500));
            
            // Set default period to current month
            $('#period').val('{{ date("Y-m") }}');
            $('#payment_date').val('{{ date("Y-m-d") }}');
            
            // Add event listener for period change in modal
            $('#period').on('change', function() {
                loadAttendanceData();
            });
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
                    period_filter: $('#periodFilter').val(),
                    status_filter: $('#statusFilter').val(),
                    search: $('#searchInput').val()
                });

                const response = await fetch(`{{ route('sdm.payroll.data') }}?${params}`);
                const result = await response.json();

                if (result.success) {
                    payrolls = result.data;
                    renderTable();
                    updateStats();
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Gagal memuat data');
            }
        }

        async function loadEmployees() {
            const outletId = $('#outlet_id').val();
            if (!outletId) {
                $('#recruitment_id').html('<option value="">Pilih Karyawan</option>');
                return;
            }

            try {
                const response = await fetch(`{{ route('sdm.payroll.employees') }}?outlet_id=${outletId}`);
                const result = await response.json();

                if (result.success) {
                    employees = result.data;
                    let options = '<option value="">Pilih Karyawan</option>';
                    result.data.forEach(emp => {
                        options += `<option value="${emp.id}" data-salary="${emp.salary}" data-hourly="${emp.hourly_rate}">${emp.name} - ${emp.position}</option>`;
                    });
                    $('#recruitment_id').html(options);
                }
            } catch (error) {
                console.error('Error loading employees:', error);
            }
        }

        function loadEmployeeData() {
            const selected = $('#recruitment_id option:selected');
            const salary = selected.data('salary') || 0;
            const hourlyRate = selected.data('hourly') || 0;
            
            $('#basic_salary').val(salary);
            
            // Store hourly rate for overtime calculation
            $('#recruitment_id').attr('data-current-hourly-rate', hourlyRate);
            
            // Calculate overtime pay if overtime hours already filled
            calculateOvertimePay();
            
            // Load attendance data if employee and period are selected
            loadAttendanceData();
            
            calculateSalary();
        }

        function loadAttendanceData() {
            const employeeId = $('#recruitment_id').val();
            const period = $('#period').val();
            
            if (!employeeId || !period) {
                return;
            }

            // Show loading indicator
            $('#overtime_hours, #absent_days, #late_days').prop('disabled', true).val('Loading...');

            $.ajax({
                url: '{{ route("sdm.payroll.attendance.summary") }}',
                method: 'GET',
                data: {
                    employee_id: employeeId,
                    period: period
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Auto-fill attendance data
                        $('#working_days').val(data.working_days);
                        $('#present_days').val(data.total_present);
                        $('#overtime_hours').val(data.overtime_hours);
                        $('#absent_days').val(data.total_absent);
                        $('#late_days').val(data.late_days);
                        
                        // Calculate overtime pay automatically
                        calculateOvertimePay();
                        
                        // Show info message
                        showToast('Data absensi berhasil dimuat', 'success');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading attendance data:', xhr);
                    showToast('Gagal memuat data absensi', 'error');
                },
                complete: function() {
                    // Re-enable fields
                    $('#overtime_hours, #absent_days, #late_days').prop('disabled', false);
                }
            });
        }

        function calculateOvertimePay() {
            const overtimeHours = parseFloat($('#overtime_hours').val()) || 0;
            const hourlyRate = parseFloat($('#recruitment_id').attr('data-current-hourly-rate')) || 0;
            
            if (overtimeHours > 0 && hourlyRate > 0) {
                const overtimePay = overtimeHours * hourlyRate;
                $('#overtime_pay').val(overtimePay);
                
                // Show calculation info
                const info = `${overtimeHours} jam × Rp ${hourlyRate.toLocaleString('id-ID')} = Rp ${overtimePay.toLocaleString('id-ID')}`;
                $('#overtime_pay').attr('title', info);
                
                // Show toast notification
                showToast(`Upah lembur dihitung otomatis: ${info}`, 'info');
            }
            
            calculateSalary();
        }

        function calculateSalary() {
            const basicSalary = parseFloat($('#basic_salary').val()) || 0;
            const overtimePay = parseFloat($('#overtime_pay').val()) || 0;
            const bonus = parseFloat($('#bonus').val()) || 0;
            const allowance = parseFloat($('#allowance').val()) || 0;
            
            const deduction = parseFloat($('#deduction').val()) || 0;
            const latePenalty = parseFloat($('#late_penalty').val()) || 0;
            const absentPenalty = parseFloat($('#absent_penalty').val()) || 0;
            const loanDeduction = parseFloat($('#loan_deduction').val()) || 0;
            const tax = parseFloat($('#tax').val()) || 0;

            const grossSalary = basicSalary + overtimePay + bonus + allowance;
            const totalDeductions = deduction + latePenalty + absentPenalty + loanDeduction + tax;
            const netSalary = grossSalary - totalDeductions;

            $('#gross_salary_display').text('Rp ' + grossSalary.toLocaleString('id-ID'));
            $('#net_salary_display').text('Rp ' + netSalary.toLocaleString('id-ID'));
        }

        function renderTable() {
            const tbody = $('#payrollTableBody');
            
            if (payrolls.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-500">
                            <i class='bx bx-info-circle text-3xl'></i>
                            <p class="mt-2">Tidak ada data payroll</p>
                        </td>
                    </tr>
                `);
                return;
            }

            const rows = payrolls.map(p => {
                const statusColors = {
                    draft: 'bg-yellow-100 text-yellow-700',
                    approved: 'bg-green-100 text-green-700',
                    paid: 'bg-purple-100 text-purple-700'
                };

                let actions = '';
                if (p.status === 'draft') {
                    actions = `
                        @hasPermission('hrm.payroll.edit')
                        <button onclick="editPayroll(${p.id})" class="px-2 py-1 text-blue-600 hover:bg-blue-50 rounded" title="Edit">
                            <i class='bx bx-edit'></i>
                        </button>
                        @endhasPermission
                        @hasPermission('hrm.payroll.approve')
                        <button onclick="approvePayroll(${p.id})" class="px-2 py-1 text-green-600 hover:bg-green-50 rounded" title="Approve">
                            <i class='bx bx-check'></i>
                        </button>
                        @endhasPermission
                        @hasPermission('hrm.payroll.delete')
                        <button onclick="deletePayroll(${p.id})" class="px-2 py-1 text-red-600 hover:bg-red-50 rounded" title="Hapus">
                            <i class='bx bx-trash'></i>
                        </button>
                        @endhasPermission
                    `;
                } else if (p.status === 'approved') {
                    actions = `
                        <button onclick="payPayroll(${p.id})" class="px-2 py-1 text-purple-600 hover:bg-purple-50 rounded" title="Bayar">
                            <i class='bx bx-money'></i>
                        </button>
                        <button onclick="printSlip(${p.id})" class="px-2 py-1 text-blue-600 hover:bg-blue-50 rounded" title="Print Slip">
                            <i class='bx bx-printer'></i>
                        </button>
                    `;
                } else {
                    actions = `
                        <button onclick="printSlip(${p.id})" class="px-2 py-1 text-blue-600 hover:bg-blue-50 rounded" title="Print Slip">
                            <i class='bx bx-printer'></i>
                        </button>
                    `;
                }

                return `
                    <tr class="border-b border-slate-200 hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-700">${p.outlet_name}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900">${p.employee_name}</div>
                            <div class="text-sm text-slate-500">${p.employee_position}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-700">${p.period_formatted}</td>
                        <td class="px-4 py-3 text-slate-700">${p.payment_date}</td>
                        <td class="px-4 py-3 text-slate-700">${p.basic_salary_formatted}</td>
                        <td class="px-4 py-3 text-slate-700 font-semibold">${p.net_salary_formatted}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${statusColors[p.status]}">
                                ${p.status_label}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            ${actions}
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.html(rows);
        }

        function updateStats() {
            const draft = payrolls.filter(p => p.status === 'draft').length;
            const approved = payrolls.filter(p => p.status === 'approved').length;
            const paid = payrolls.filter(p => p.status === 'paid').length;
            const total = payrolls.reduce((sum, p) => sum + parseFloat(p.net_salary || 0), 0);

            $('#draftCount').text(draft);
            $('#approvedCount').text(approved);
            $('#paidCount').text(paid);
            $('#totalPayroll').text('Rp ' + Math.round(total).toLocaleString('id-ID'));
        }

        function openAddModal() {
            $('#modalTitle').text('Tambah Payroll');
            $('#payrollForm')[0].reset();
            $('#payrollId').val('');
            $('#period').val('{{ date("Y-m") }}');
            $('#payment_date').val('{{ date("Y-m-d") }}');
            calculateSalary();
            $('#payrollModal').modal('show');
        }

        async function editPayroll(id) {
            try {
                const response = await fetch(`{{ route('sdm.payroll.index') }}/${id}`);
                const result = await response.json();

                if (result.success) {
                    const p = result.data;
                    
                    $('#modalTitle').text('Edit Payroll');
                    $('#payrollId').val(p.id);
                    $('#outlet_id').val(p.outlet_id);
                    await loadEmployees();
                    $('#recruitment_id').val(p.recruitment_id);
                    
                    // Set hourly rate for overtime calculation
                    const selectedOption = $('#recruitment_id option:selected');
                    const hourlyRate = selectedOption.data('hourly') || 0;
                    $('#recruitment_id').attr('data-current-hourly-rate', hourlyRate);
                    
                    $('#period').val(p.period);
                    $('#payment_date').val(p.payment_date);
                    $('#basic_salary').val(p.basic_salary);
                    $('#working_days').val(p.working_days);
                    $('#present_days').val(p.present_days);
                    $('#absent_days').val(p.absent_days);
                    $('#late_days').val(p.late_days);
                    $('#overtime_hours').val(p.overtime_hours);
                    $('#overtime_pay').val(p.overtime_pay);
                    $('#bonus').val(p.bonus);
                    $('#allowance').val(p.allowance);
                    $('#deduction').val(p.deduction);
                    $('#late_penalty').val(p.late_penalty);
                    $('#absent_penalty').val(p.absent_penalty);
                    $('#loan_deduction').val(p.loan_deduction);
                    $('#tax').val(p.tax);
                    $('#notes').val(p.notes);
                    
                    calculateSalary();
                    $('#payrollModal').modal('show');
                }
            } catch (error) {
                console.error('Error loading payroll:', error);
                alert('Gagal memuat data payroll');
            }
        }

        $('#payrollForm').on('submit', async function(e) {
            e.preventDefault();

            const id = $('#payrollId').val();
            const data = {
                outlet_id: $('#outlet_id').val(),
                recruitment_id: $('#recruitment_id').val(),
                period: $('#period').val(),
                payment_date: $('#payment_date').val(),
                basic_salary: $('#basic_salary').val(),
                working_days: $('#working_days').val(),
                present_days: $('#present_days').val(),
                absent_days: $('#absent_days').val() || 0,
                late_days: $('#late_days').val() || 0,
                overtime_hours: $('#overtime_hours').val() || 0,
                overtime_pay: $('#overtime_pay').val() || 0,
                bonus: $('#bonus').val() || 0,
                allowance: $('#allowance').val() || 0,
                deduction: $('#deduction').val() || 0,
                late_penalty: $('#late_penalty').val() || 0,
                absent_penalty: $('#absent_penalty').val() || 0,
                loan_deduction: $('#loan_deduction').val() || 0,
                tax: $('#tax').val() || 0,
                notes: $('#notes').val(),
                _token: '{{ csrf_token() }}'
            };

            try {
                const url = id 
                    ? `{{ route('sdm.payroll.index') }}/${id}`
                    : `{{ route('sdm.payroll.store') }}`;
                
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
                    $('#payrollModal').modal('hide');
                    loadData();
                    alert(result.message);
                } else {
                    alert(result.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error saving payroll:', error);
                alert('Gagal menyimpan data');
            }
        });

        async function deletePayroll(id) {
            if (!confirm('Yakin ingin menghapus payroll ini?')) return;

            try {
                const response = await fetch(`{{ route('sdm.payroll.index') }}/${id}`, {
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
                console.error('Error deleting payroll:', error);
                alert('Gagal menghapus data');
            }
        }

        async function approvePayroll(id) {
            if (!confirm('Approve payroll ini?')) return;

            try {
                const response = await fetch(`{{ route('sdm.payroll.index') }}/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    loadData();
                    alert(result.message);
                } else {
                    alert(result.message || 'Gagal approve');
                }
            } catch (error) {
                console.error('Error approving payroll:', error);
                alert('Gagal approve');
            }
        }

        async function payPayroll(id) {
            if (!confirm('Tandai payroll ini sebagai sudah dibayar?')) return;

            try {
                const response = await fetch(`{{ route('sdm.payroll.index') }}/${id}/pay`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    loadData();
                    alert(result.message);
                } else {
                    alert(result.message || 'Gagal');
                }
            } catch (error) {
                console.error('Error paying payroll:', error);
                alert('Gagal');
            }
        }

        function printSlip(id) {
            window.open(`{{ route('sdm.payroll.index') }}/${id}/slip`, '_blank');
        }

        function exportPdf() {
            const params = new URLSearchParams({
                outlet_filter: $('#outletFilter').val(),
                period_filter: $('#periodFilter').val(),
                status_filter: $('#statusFilter').val()
            });
            window.open(`{{ route('sdm.payroll.export.pdf') }}?${params}`, '_blank');
        }

        function exportExcel() {
            const params = new URLSearchParams({
                outlet_filter: $('#outletFilter').val(),
                period_filter: $('#periodFilter').val(),
                status_filter: $('#statusFilter').val()
            });
            window.location.href = `{{ route('sdm.payroll.export.excel') }}?${params}`;
        }

        function showToast(message, type = 'info') {
            // Create toast element if not exists
            if (!$('#toast-container').length) {
                $('body').append(`
                    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
                `);
            }

            const toastId = 'toast-' + Date.now();
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500',
                warning: 'bg-yellow-500'
            };

            const toast = $(`
                <div id="${toastId}" class="transform translate-x-full transition-transform duration-300 ${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg max-w-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">${message}</span>
                        <button onclick="$('#${toastId}').addClass('translate-x-full').delay(300).fadeOut()" class="ml-2 text-white hover:text-gray-200">
                            <i class='bx bx-x'></i>
                        </button>
                    </div>
                </div>
            `);

            $('#toast-container').append(toast);
            
            // Show toast
            setTimeout(() => {
                toast.removeClass('translate-x-full');
            }, 100);

            // Auto hide after 5 seconds
            setTimeout(() => {
                toast.addClass('translate-x-full');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 5000);
        }
    </script>
    @endpush
</x-layouts.admin>
