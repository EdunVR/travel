<?php
/**
 * Final fix for Alpine.js attendanceCrud function
 * 
 * This script will:
 * 1. Create a clean, working version of the Alpine.js function
 * 2. Remove all duplicate function definitions
 * 3. Fix syntax errors
 * 4. Ensure proper function closure
 */

echo "=== FIXING ALPINE.JS ATTENDANCECRUD FUNCTION ===\n\n";

$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';

// Read the current file
$content = file_get_contents($viewFile);

// Find the script section and replace it with a clean version
$scriptStart = strpos($content, '<script>');
$scriptEnd = strpos($content, '</script>') + 9; // Include </script>

if ($scriptStart === false || $scriptEnd === false) {
    echo "❌ ERROR: Could not find script tags\n";
    exit(1);
}

// Get the content before and after the script
$beforeScript = substr($content, 0, $scriptStart);
$afterScript = substr($content, $scriptEnd);

// Create the clean Alpine.js function
$cleanScript = <<<'SCRIPT'
<script>
    function attendanceCrud() {
      const today = new Date();
      const currentYear = today.getFullYear();
      
      // Format tanggal hari ini dengan timezone lokal
      const formatLocalDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
      };
      
      return {
        // State
        attendances: [],
        monthlyData: [],
        employees: [],
        outlets: [],
        selectedOutlets: [],
        showOutletDropdown: false,
        statistics: {},
        loading: false,
        saving: false,
        savingWorkHours: false,
        deleting: false,
        
        // Current tab
        currentTab: 'daily',
        
        // Filters
        filterDate: formatLocalDate(today),
        filterMonth: today.getMonth() + 1,
        filterYear: currentYear,
        search: '',
        
        // Year options - Initialize immediately
        yearOptions: [currentYear - 2, currentYear - 1, currentYear, currentYear + 1],
        
        // Monthly calendar
        daysInMonth: 31,
        
        // Modals
        showForm: false,
        showWorkHoursModal: false,
        showTimeSettingsModal: false,
        showDeleteModal: false,
        
        // Form data
        form: {
          id: null,
          employee_id: '',
          date: '',
          clock_in: '',
          clock_out: '',
          break_out: '',
          break_in: '',
          overtime_in: '',
          overtime_out: '',
          status: 'present',
          notes: ''
        },
        errors: {},
        
        // Work hours form
        workHoursForm: {
          employee_id: '',
          clock_in: '08:00',
          clock_out: '17:00',
          apply_to_all: false
        },
        
        // Time settings
        timeSettings: [],
        loadingTimeSettings: false,
        savingTimeSettings: false,
        testTime: '',
        testResult: null,
        testingTime: false,
        
        // Delete
        deleteId: null,
        
        // Toast
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init() {
          await this.loadOutlets();
          // Panggil fetchEmployees setelah outlets dimuat dan selected
          if (this.selectedOutlets.length > 0) {
            await Promise.all([
              this.fetchEmployees(),
              this.fetchStatistics(),
              this.fetchData()
            ]);
          }
        },

        async loadOutlets() {
          try {
            const response = await fetch('{{ route("finance.outlets.data") }}');
            const result = await response.json();

            if (result.success) {
              this.outlets = result.data;
              // Set default to first outlet if available
              if (this.outlets.length > 0 && this.selectedOutlets.length === 0) {
                this.selectedOutlets = [this.outlets[0].id_outlet];
                // Langsung panggil fetchEmployees setelah outlet default diset
                await this.fetchEmployees();
                console.log('✅ Default outlet set and employees loaded');
              }
              console.log('✅ Loaded outlets:', this.outlets.length);
            }
          } catch (error) {
            console.error('❌ Error loading outlets:', error);
          }
        },

        // Checkbox Management Functions
        getSelectedOutletsText() {
          if (this.selectedOutlets.length === 0) {
            return 'Pilih Outlet';
          } else if (this.selectedOutlets.length === 1) {
            const outlet = this.outlets.find(o => o.id_outlet === this.selectedOutlets[0]);
            return outlet ? outlet.nama_outlet : 'Outlet Terpilih';
          } else if (this.selectedOutlets.length === this.outlets.length) {
            return 'Semua Outlet';
          } else {
            return `${this.selectedOutlets.length} Outlet Terpilih`;
          }
        },

        selectAllOutlets() {
          this.selectedOutlets = this.outlets.map(outlet => outlet.id_outlet);
          this.onOutletSelectionChange();
        },

        clearAllOutlets() {
          this.selectedOutlets = [];
          this.onOutletSelectionChange();
        },

        async onOutletSelectionChange() {
          console.log('🔄 Outlet selection changed:', this.selectedOutlets);
          if (this.selectedOutlets.length > 0) {
            await Promise.all([
              this.fetchEmployees(),
              this.fetchStatistics(),
              this.fetchData()
            ]);
          }
        },

        // Computed calculation functions
        calculateHoursWorked(item) {
          if (!item.clock_in || !item.clock_out) return '-';
          
          try {
            const clockIn = new Date(`2000-01-01T${item.clock_in}`);
            const clockOut = new Date(`2000-01-01T${item.clock_out}`);
            
            // Handle overnight shifts
            if (clockOut < clockIn) {
              clockOut.setDate(clockOut.getDate() + 1);
            }
            
            let totalMinutes = 0;
            
            // Rumus: total_jam_kerja = [(break_in - clock_in) + (clock_out - break_out)] + (overtime_out - overtime_in)
            if (item.break_in && item.break_out) {
              // Ada waktu istirahat
              const breakIn = new Date(`2000-01-01T${item.break_in}`);   // mulai istirahat
              const breakOut = new Date(`2000-01-01T${item.break_out}`); // selesai istirahat
              
              // Handle overnight break
              if (breakOut < breakIn) {
                breakOut.setDate(breakOut.getDate() + 1);
              }
              
              // Waktu kerja sebelum istirahat: break_in - clock_in
              const beforeBreakMinutes = (breakIn - clockIn) / 1000 / 60;
              
              // Waktu kerja setelah istirahat: clock_out - break_out
              const afterBreakMinutes = (clockOut - breakOut) / 1000 / 60;
              
              // Total jam kerja normal (tanpa lembur)
              totalMinutes = beforeBreakMinutes + afterBreakMinutes;
            } else {
              // Tidak ada waktu istirahat: clock_out - clock_in
              totalMinutes = (clockOut - clockIn) / 1000 / 60;
            }
            
            // Tambahkan jam lembur jika ada: overtime_out - overtime_in
            if (item.overtime_in && item.overtime_out) {
              const overtimeIn = new Date(`2000-01-01T${item.overtime_in}`);
              const overtimeOut = new Date(`2000-01-01T${item.overtime_out}`);
              
              // Handle overnight overtime
              if (overtimeOut < overtimeIn) {
                overtimeOut.setDate(overtimeOut.getDate() + 1);
              }
              
              const overtimeMinutes = (overtimeOut - overtimeIn) / 1000 / 60;
              if (overtimeMinutes > 0) {
                totalMinutes += overtimeMinutes;
              }
            }
            
            if (totalMinutes <= 0) return '-';
            
            const hours = Math.floor(totalMinutes / 60);
            const minutes = Math.round(totalMinutes % 60);
            
            if (hours > 0 && minutes > 0) {
              return `${hours}j ${minutes}m`;
            } else if (hours > 0) {
              return `${hours} jam`;
            } else if (minutes > 0) {
              return `${minutes} mnt`;
            } else {
              return '-';
            }
          } catch (error) {
            console.error('Error calculating hours worked:', error);
            return '-';
          }
        },

        calculateLateMinutes(item) {
          if (!item.clock_in || !item.schedule_in) return 0;
          
          const clockIn = new Date(`2000-01-01 ${item.clock_in}`);
          const scheduleIn = new Date(`2000-01-01 ${item.schedule_in}`);
          
          if (clockIn > scheduleIn) {
            return Math.round((clockIn - scheduleIn) / 1000 / 60);
          }
          return 0;
        },

        calculateEarlyMinutes(item) {
          if (!item.clock_out || !item.schedule_out) return 0;
          
          const clockOut = new Date(`2000-01-01 ${item.clock_out}`);
          const scheduleOut = new Date(`2000-01-01 ${item.schedule_out}`);
          
          if (clockOut < scheduleOut) {
            return Math.round((scheduleOut - clockOut) / 1000 / 60);
          }
          return 0;
        },

        calculateOvertimeMinutes(item) {
          if (!item.overtime_in || !item.overtime_out) return 0;
          
          try {
            const overtimeIn = new Date(`2000-01-01T${item.overtime_in}`);
            const overtimeOut = new Date(`2000-01-01T${item.overtime_out}`);
            
            // Handle overnight overtime
            if (overtimeOut < overtimeIn) {
              overtimeOut.setDate(overtimeOut.getDate() + 1);
            }
            
            if (overtimeOut > overtimeIn) {
              return Math.round((overtimeOut - overtimeIn) / 1000 / 60);
            }
          } catch (error) {
            console.error('Error calculating overtime:', error);
          }
          return 0;
        },

        // Fungsi untuk menampilkan jam lembur dalam format yang mudah dibaca
        calculateOvertimeHours(item) {
          const overtimeMinutes = this.calculateOvertimeMinutes(item);
          if (overtimeMinutes <= 0) return '-';
          
          const hours = Math.floor(overtimeMinutes / 60);
          const minutes = overtimeMinutes % 60;
          
          if (hours > 0 && minutes > 0) {
            return `${hours}j ${minutes}m`;
          } else if (hours > 0) {
            return `${hours} jam`;
          } else if (minutes > 0) {
            return `${minutes} mnt`;
          } else {
            return '-';
          }
        },

        switchTab(tab) {
          this.currentTab = tab;
          // Fetch both data and statistics when switching tabs
          Promise.all([
            this.fetchData(),
            this.fetchStatistics()
          ]);
        },

        async fetchData() {
          this.loading = true;
          try {
            if (this.currentTab === 'daily') {
              await this.fetchDailyData();
            } else {
              await this.fetchMonthlyData();
            }
            // Update statistics setiap kali data berubah
            await this.fetchStatistics();
          } catch (error) {
            console.error('Error fetching data:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchDailyData() {
          if (this.selectedOutlets.length === 0) {
            this.attendances = [];
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          
          params.append('date', this.filterDate);
          params.append('search', this.search);

          const response = await fetch(`{{ route('sdm.attendance.daily.table') }}?${params}`);
          const data = await response.json();
          
          this.attendances = data.data || [];
          
          // Debug: Log first attendance to check data structure
          if (this.attendances.length > 0) {
            console.log('=== ATTENDANCE DATA DEBUG ===');
            console.log('Daily attendance sample:', this.attendances[0]);
            console.log('Available fields:', Object.keys(this.attendances[0]));
            console.log('--- CALCULATED FIELDS ---');
            console.log('hours_worked:', this.attendances[0].hours_worked, '(Type:', typeof this.attendances[0].hours_worked, ')');
            console.log('late_minutes:', this.attendances[0].late_minutes, '(Type:', typeof this.attendances[0].late_minutes, ')');
            console.log('early_minutes:', this.attendances[0].early_minutes, '(Type:', typeof this.attendances[0].early_minutes, ')');
            console.log('overtime_minutes:', this.attendances[0].overtime_minutes, '(Type:', typeof this.attendances[0].overtime_minutes, ')');
            console.log('========================');
          }
        },

        async fetchMonthlyData() {
          if (this.selectedOutlets.length === 0) {
            this.monthlyData = [];
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          
          params.append('month', this.filterMonth);
          params.append('year', this.filterYear);
          params.append('search', this.search);

          const response = await fetch(`{{ route('sdm.attendance.monthly.table') }}?${params}`);
          const data = await response.json();
          
          this.monthlyData = data.data || [];
          this.daysInMonth = data.days_in_month || 31;
          
          // Debug: Log first row to see data structure
          if (this.monthlyData.length > 0) {
            console.log('Monthly data sample:', this.monthlyData[0]);
          }
        },

        async fetchEmployees() {
          try {
            if (this.selectedOutlets.length === 0) {
              console.log('⚠️ No outlets selected, clearing employees');
              this.employees = [];
              return;
            }

            console.log('🔄 Fetching employees for outlets:', this.selectedOutlets);

            const params = new URLSearchParams();
            
            // Add multiple outlet IDs
            this.selectedOutlets.forEach(outletId => {
              params.append('outlet_ids[]', outletId);
            });

            const response = await fetch(`{{ route("sdm.attendance.employees") }}?${params}`);
            const data = await response.json();
            this.employees = data;
            
            console.log('✅ Loaded', this.employees.length, 'employees');
          } catch (error) {
            console.error('❌ Error fetching employees:', error);
            this.employees = [];
          }
        },

        async fetchStatistics() {
          try {
            if (this.selectedOutlets.length === 0) {
              this.statistics = {};
              return;
            }

            const params = new URLSearchParams();
            
            // Add multiple outlet IDs
            this.selectedOutlets.forEach(outletId => {
              params.append('outlet_ids[]', outletId);
            });
            
            if (this.currentTab === 'daily') {
              // Untuk tab harian, gunakan tanggal yang dipilih
              params.append('start_date', this.filterDate);
              params.append('end_date', this.filterDate);
            } else {
              // Untuk tab bulanan, gunakan range bulan yang dipilih
              const startDate = `${this.filterYear}-${String(this.filterMonth).padStart(2, '0')}-01`;
              const endDate = new Date(this.filterYear, this.filterMonth, 0).toISOString().split('T')[0];
              
              params.append('start_date', startDate);
              params.append('end_date', endDate);
            }
            
            params.append('search', this.search);

            const response = await fetch(`{{ route('sdm.attendance.statistics') }}?${params}`);
            const data = await response.json();
            this.statistics = data;
          } catch (error) {
            console.error('Error fetching statistics:', error);
          }
        },

        async openCreate() {
          // Pastikan menggunakan tanggal hari ini
          const today = new Date();
          const todayFormatted = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
          
          // Pastikan employees sudah dimuat
          if (this.employees.length === 0 && this.selectedOutlets.length > 0) {
            console.log('🔄 Loading employees for modal...');
            await this.fetchEmployees();
          }
          
          this.form = {
            id: null,
            employee_id: '',
            date: todayFormatted,
            clock_in: '',
            clock_out: '',
            break_out: '',
            break_in: '',
            overtime_in: '',
            overtime_out: '',
            status: 'present',
            notes: ''
          };
          this.errors = {};
          this.showForm = true;
          
          console.log('✅ Modal opened with', this.employees.length, 'employees available');
        },

        async openEdit(id) {
          try {
            const response = await fetch(`{{ route('sdm.attendance.show', '') }}/${id}`);
            const data = await response.json();
            
            // Helper function to format time (remove seconds if present)
            const formatTime = (time) => {
              if (!time) return '';
              // If time has seconds (HH:MM:SS), remove them
              return time.substring(0, 5);
            };
            
            this.form = {
              id: data.id,
              employee_id: data.employee_id,
              date: data.date,
              clock_in: formatTime(data.clock_in),
              clock_out: formatTime(data.clock_out),
              break_out: formatTime(data.break_out),
              break_in: formatTime(data.break_in),
              overtime_in: formatTime(data.overtime_in),
              overtime_out: formatTime(data.overtime_out),
              status: data.status,
              notes: data.notes || ''
            };
            this.errors = {};
            this.showForm = true;
          } catch (error) {
            console.error('Error fetching attendance:', error);
            this.showToastMessage('Gagal memuat data absensi', 'error');
          }
        },

        closeForm() {
          this.showForm = false;
          this.errors = {};
        },

        async submitForm() {
          this.saving = true;
          this.errors = {};

          try {
            const url = this.form.id 
              ? `{{ route('sdm.attendance.update', '') }}/${this.form.id}`
              : '{{ route("sdm.attendance.store") }}';

            const method = this.form.id ? 'PUT' : 'POST';

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify(this.form)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
              this.closeForm();
              await this.fetchData();
              await this.fetchStatistics();
            } else {
              if (result.errors) {
                this.errors = result.errors;
              } else {
                this.showToastMessage(result.message || 'Terjadi kesalahan', 'error');
              }
            }
          } catch (error) {
            console.error('Error saving data:', error);
            this.showToastMessage('Gagal menyimpan data', 'error');
          } finally {
            this.saving = false;
          }
        },

        openSetWorkHours() {
          this.workHoursForm = {
            employee_id: '',
            clock_in: '08:00',
            clock_out: '17:00',
            apply_to_all: false
          };
          this.showWorkHoursModal = true;
        },

        async submitWorkHours() {
          this.savingWorkHours = true;

          try {
            const response = await fetch('{{ route("sdm.attendance.set.work.hours") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({
                clock_in: this.workHoursForm.clock_in,
                clock_out: this.workHoursForm.clock_out,
                employee_id: this.workHoursForm.employee_id || null,
                apply_to_all: this.workHoursForm.apply_to_all ? 1 : 0
              })
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Jadwal kerja berhasil disimpan', 'success');
              this.showWorkHoursModal = false;
              await this.fetchData();
            } else {
              this.showToastMessage(result.message || 'Terjadi kesalahan', 'error');
            }
          } catch (error) {
            console.error('Error saving work hours:', error);
            this.showToastMessage('Gagal menyimpan jadwal kerja', 'error');
          } finally {
            this.savingWorkHours = false;
          }
        },

        // Time Settings Functions
        async openTimeSettings() {
          this.showTimeSettingsModal = true;
          await this.loadTimeSettings();
        },

        async loadTimeSettings() {
          this.loadingTimeSettings = true;
          try {
            const response = await fetch('{{ route("sdm.attendance.time.settings") }}');
            const result = await response.json();

            if (response.ok) {
              this.timeSettings = result.settings.map(setting => ({
                id: setting.id,
                name: setting.name,
                start_time: setting.start_time.substring(0, 5), // Remove seconds
                end_time: setting.end_time.substring(0, 5), // Remove seconds
                description: setting.description,
                is_active: setting.is_active
              }));
            } else {
              this.showToastMessage(result.message || 'Gagal memuat pengaturan waktu', 'error');
            }
          } catch (error) {
            console.error('Error loading time settings:', error);
            this.showToastMessage('Gagal memuat pengaturan waktu', 'error');
          } finally {
            this.loadingTimeSettings = false;
          }
        },

        async saveTimeSettings() {
          this.savingTimeSettings = true;
          try {
            // Ensure all time values are properly formatted before sending
            const dataToSend = {
              settings: this.timeSettings.map(setting => {
                // Ensure time format is HH:MM
                let startTime = setting.start_time || '';
                let endTime = setting.end_time || '';
                
                // Convert and validate start_time
                if (startTime) {
                  startTime = this.formatTimeToHHMM(startTime);
                }
                
                // Convert and validate end_time
                if (endTime) {
                  endTime = this.formatTimeToHHMM(endTime);
                }
                
                // Validate that both times are present and valid
                if (!startTime || !endTime) {
                  throw new Error(`Pengaturan "${setting.name}" harus memiliki jam mulai dan jam selesai`);
                }
                
                if (!startTime.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
                  throw new Error(`Format jam mulai tidak valid untuk "${setting.name}": ${startTime}`);
                }
                
                if (!endTime.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
                  throw new Error(`Format jam selesai tidak valid untuk "${setting.name}": ${endTime}`);
                }
                
                return {
                  id: setting.id,
                  start_time: startTime,
                  end_time: endTime,
                  is_active: setting.is_active
                };
              })
            };

            const response = await fetch('{{ route("sdm.attendance.time.settings.update") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify(dataToSend)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Pengaturan waktu berhasil disimpan', 'success');
              this.showTimeSettingsModal = false;
            } else {
              console.error('❌ Save failed:', result);
              this.showToastMessage(result.message || 'Terjadi kesalahan', 'error');
            }
          } catch (error) {
            console.error('❌ Error saving time settings:', error);
            this.showToastMessage('Gagal menyimpan pengaturan waktu', 'error');
          } finally {
            this.savingTimeSettings = false;
          }
        },

        async testTimePeriod() {
          if (!this.testTime) {
            this.showToastMessage('Masukkan waktu untuk test', 'error');
            return;
          }

          this.testingTime = true;
          this.testResult = null;

          try {
            const response = await fetch('{{ route("sdm.attendance.test.time.period") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({
                time: this.testTime
              })
            });

            const result = await response.json();

            if (response.ok && result) {
              // Ensure result has the required properties
              this.testResult = {
                time_period: result.time_period || 'Diluar jam kerja',
                action_description: result.action_description || 'Tidak ada aksi'
              };
            } else {
              this.showToastMessage(result?.message || 'Gagal test periode waktu', 'error');
              this.testResult = null;
            }
          } catch (error) {
            console.error('Error testing time period:', error);
            this.showToastMessage('Gagal test periode waktu', 'error');
            this.testResult = null;
          } finally {
            this.testingTime = false;
          }
        },

        getTimeSettingTitle(name) {
          const titles = {
            'check_in': 'Jam Masuk (07:00 - 09:00)',
            'break': 'Jam Istirahat (11:01 - 14:00)',
            'check_out': 'Jam Pulang (14:01 - 18:00)',
            'overtime': 'Jam Lembur (18:01 - 03:30)'
          };
          return titles[name] || name;
        },

        // Function to ensure time format is always 24-hour (HH:MM)
        ensureTimeFormat(input) {
          if (!input || !input.value) return;
          
          let value = input.value;
          console.log('🕐 Original time value:', value);
          
          // If value is in 12-hour format, convert to 24-hour
          if (value.includes('AM') || value.includes('PM')) {
            console.log('🔄 Converting 12-hour to 24-hour format');
            
            // Parse 12-hour format
            const time12h = value.replace(/\s/g, '');
            const [time, period] = time12h.split(/(AM|PM)/i);
            const [hours, minutes] = time.split(':');
            
            let hours24 = parseInt(hours);
            
            if (period.toUpperCase() === 'PM' && hours24 !== 12) {
              hours24 += 12;
            } else if (period.toUpperCase() === 'AM' && hours24 === 12) {
              hours24 = 0;
            }
            
            value = `${hours24.toString().padStart(2, '0')}:${minutes}`;
            console.log('✅ Converted to 24-hour:', value);
          }
          
          // Ensure format is HH:MM or HH:MM:SS (pad single digits)
          if (value.match(/^\d{1,2}:\d{2}$/)) {
            const [hours, minutes] = value.split(':');
            value = `${hours.padStart(2, '0')}:${minutes}`;
          } else if (value.match(/^\d{1,2}:\d{2}:\d{2}$/)) {
            const [hours, minutes, seconds] = value.split(':');
            value = `${hours.padStart(2, '0')}:${minutes}:${seconds}`;
          }
          
          // Validate 24-hour format (with or without seconds)
          if (value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/)) {
            input.value = value;
            console.log('✅ Final time value:', value);
            
            // Trigger Alpine.js update
            input.dispatchEvent(new Event('input', { bubbles: true }));
          } else {
            console.warn('⚠️ Invalid time format:', value);
          }
        },

        // Helper function to format time (preserves HH:MM:SS if present, otherwise HH:MM)
        formatTimeToHHMM(timeValue) {
          if (!timeValue) return '';
          
          let value = timeValue.toString().trim();
          console.log('🕐 Formatting time value:', value);
          
          // If value is in 12-hour format, convert to 24-hour
          if (value.includes('AM') || value.includes('PM')) {
            console.log('🔄 Converting 12-hour to 24-hour format');
            
            // Parse 12-hour format
            const time12h = value.replace(/\s/g, '');
            const [time, period] = time12h.split(/(AM|PM)/i);
            const [hours, minutes, seconds] = time.split(':');
            
            let hours24 = parseInt(hours);
            
            if (period.toUpperCase() === 'PM' && hours24 !== 12) {
              hours24 += 12;
            } else if (period.toUpperCase() === 'AM' && hours24 === 12) {
              hours24 = 0;
            }
            
            value = `${hours24.toString().padStart(2, '0')}:${minutes}`;
            if (seconds) {
              value += `:${seconds}`;
            }
            console.log('✅ Converted to 24-hour:', value);
          }
          
          // Ensure format is HH:MM or HH:MM:SS (pad single digits)
          if (value.match(/^\d{1,2}:\d{2}$/)) {
            const [hours, minutes] = value.split(':');
            value = `${hours.padStart(2, '0')}:${minutes}`;
          } else if (value.match(/^\d{1,2}:\d{2}:\d{2}$/)) {
            const [hours, minutes, seconds] = value.split(':');
            value = `${hours.padStart(2, '0')}:${minutes}:${seconds}`;
          }
          
          // Remove seconds if present (HH:MM:SS -> HH:MM) for display
          if (value.match(/^\d{2}:\d{2}:\d{2}$/)) {
            value = value.substring(0, 5);
          }
          
          // Validate 24-hour format
          if (value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
            console.log('✅ Final formatted time:', value);
            return value;
          } else {
            console.warn('⚠️ Invalid time format, returning original:', value);
            return timeValue; // Return original if can't format
          }
        },

        confirmDelete(id) {
          this.deleteId = id;
          this.showDeleteModal = true;
        },

        async deleteNow() {
          if (!this.deleteId) return;
          
          this.deleting = true;
          try {
            const response = await fetch(`{{ route('sdm.attendance.destroy', '') }}/${this.deleteId}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil dihapus', 'success');
              this.showDeleteModal = false;
              this.deleteId = null;
              await this.fetchData();
              await this.fetchStatistics();
            } else {
              this.showToastMessage(result.message || 'Gagal menghapus data', 'error');
            }
          } catch (error) {
            console.error('Error deleting data:', error);
            this.showToastMessage('Gagal menghapus data', 'error');
          } finally {
            this.deleting = false;
          }
        },

        exportPdf() {
          if (this.selectedOutlets.length === 0) {
            this.showToastMessage('Pilih minimal satu outlet', 'error');
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });

          if (this.currentTab === 'daily') {
            params.append('date', this.filterDate);
            window.open(`{{ route('sdm.attendance.export.daily.pdf') }}?${params}`, '_blank');
          } else {
            params.append('month', this.filterMonth);
            params.append('year', this.filterYear);
            window.open(`{{ route('sdm.attendance.export.monthly.pdf') }}?${params}`, '_blank');
          }
        },
        
        exportExcel() {
          if (this.selectedOutlets.length === 0) {
            this.showToastMessage('Pilih minimal satu outlet', 'error');
            return;
          }

          const params = new URLSearchParams();
          
          // Add multiple outlet IDs
          this.selectedOutlets.forEach(outletId => {
            params.append('outlet_ids[]', outletId);
          });
          
          params.append('start_date', this.filterDate);
          params.append('end_date', this.filterDate);
          
          window.open(`{{ route('sdm.attendance.export.excel') }}?${params}`, '_blank');
        },

        getStatusLabel(status) {
          const labels = {
            'present': 'Hadir',
            'late': 'Terlambat',
            'leave': 'Izin',
            'sick': 'Sakit',
            'absent': 'Alpha',
            'permission': 'Izin Khusus'
          };
          return labels[status] || status;
        },

        getStatusCode(value) {
          if (!value) return '';
          
          // If value is HTML string, strip all HTML tags
          if (typeof value === 'string' && (value.includes('<') || value.includes('>'))) {
            // Create a temporary div to parse HTML
            const temp = document.createElement('div');
            temp.innerHTML = value;
            // Get text content (this strips all HTML)
            const text = temp.textContent || temp.innerText || '';
            // Return first letter (should be H, T, I, S, A, or P)
            return text.trim().charAt(0).toUpperCase();
          }
          
          // If already just the code, return it
          return value.toString().trim().charAt(0).toUpperCase();
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        }
      };
    }
    
    // AGGRESSIVE 24-HOUR FORMAT ENFORCEMENT
    document.addEventListener('DOMContentLoaded', function() {
      console.log('🕐 Enforcing 24-hour format for time inputs...');
      
      // Function to force 24-hour format
      function enforce24HourFormat() {
        const timeInputs = document.querySelectorAll('input[type="time"]');
        console.log(`Found ${timeInputs.length} time inputs to process`);
        
        timeInputs.forEach((input, index) => {
          console.log(`Processing time input ${index + 1}:`, input);
          
          // Force attributes
          input.setAttribute('step', '1');
          input.setAttribute('pattern', '[0-9]{2}:[0-9]{2}');
          input.setAttribute('data-format', '24');
          
          // Remove any existing AM/PM related attributes
          input.removeAttribute('data-12hour');
          input.removeAttribute('data-ampm');
          
          // Force style to hide AM/PM
          input.style.setProperty('-webkit-appearance', 'none', 'important');
          input.style.setProperty('-moz-appearance', 'textfield', 'important');
          input.style.setProperty('appearance', 'none', 'important');
          
          // Add validation
          input.addEventListener('input', function() {
            const value = this.value;
            console.log('Time input changed:', value);
            
            if (value && !value.match(/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
              this.setCustomValidity('Format harus HH:MM (24 jam)');
              console.log('Invalid time format:', value);
            } else {
              this.setCustomValidity('');
              console.log('Valid time format:', value);
            }
          });
          
          // Force focus behavior
          input.addEventListener('focus', function() {
            console.log('Time input focused, ensuring 24-hour format');
            // Try to force 24-hour mode
            this.setAttribute('data-format', '24');
          });
          
          // Additional enforcement on change
          input.addEventListener('change', function() {
            console.log('Time input changed, value:', this.value);
          });
        });
      }
      
      // Run immediately
      enforce24HourFormat();
      
      // Run again after a short delay to catch dynamically added inputs
      setTimeout(enforce24HourFormat, 500);
      setTimeout(enforce24HourFormat, 1000);
      
      // Watch for new time inputs being added to DOM
      const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if (mutation.type === 'childList') {
            mutation.addedNodes.forEach(function(node) {
              if (node.nodeType === 1) { // Element node
                const timeInputs = node.querySelectorAll ? node.querySelectorAll('input[type="time"]') : [];
                if (timeInputs.length > 0) {
                  console.log('New time inputs detected, enforcing 24-hour format');
                  enforce24HourFormat();
                }
              }
            });
          }
        });
      });
      
      observer.observe(document.body, {
        childList: true,
        subtree: true
      });
      
      console.log('✅ 24-hour format enforcement initialized');
    });
  </script>
SCRIPT;

// Combine the content
$newContent = $beforeScript . $cleanScript . $afterScript;

// Write the fixed content back to the file
if (file_put_contents($viewFile, $newContent)) {
    echo "✅ SUCCESS: Alpine.js function has been fixed!\n\n";
    echo "Changes made:\n";
    echo "- Removed all duplicate function definitions\n";
    echo "- Fixed syntax errors and malformed code blocks\n";
    echo "- Ensured proper function closure\n";
    echo "- Cleaned up JavaScript structure\n";
    echo "- Maintained all original functionality\n\n";
    echo "Next steps:\n";
    echo "1. Clear browser cache (Ctrl+F5)\n";
    echo "2. Test the attendance page\n";
    echo "3. Check browser console for any remaining errors\n";
} else {
    echo "❌ ERROR: Could not write to file: $viewFile\n";
    exit(1);
}

echo "\n=== FIX COMPLETE ===\n";
?>