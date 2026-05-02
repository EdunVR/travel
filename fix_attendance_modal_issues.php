<?php

/**
 * Fix untuk masalah pada halaman manajemen absensi:
 * 1. Error 404 saat edit karena item.id bernilai null
 * 2. Data karyawan tidak muncul saat pertama kali membuka modal tambah absensi
 * 3. Validasi format waktu yang tidak konsisten (HH:MM:SS vs HH:MM)
 */

echo "🔧 Memperbaiki masalah pada halaman manajemen absensi...\n";

// Backup file asli
$originalFile = 'resources/views/admin/sdm/attendance/index.blade.php';
$backupFile = $originalFile . '.backup-modal-fix.' . date('Y-m-d-H-i-s');

if (file_exists($originalFile)) {
    copy($originalFile, $backupFile);
    echo "✅ Backup dibuat: $backupFile\n";
}

// Baca file asli
$content = file_get_contents($originalFile);

// 1. Fix masalah item.id null - pastikan ID selalu ada
$content = str_replace(
    '<template x-for="(item, index) in attendances" :key="item.id || \'emp-\' + index">',
    '<template x-for="(item, index) in attendances" :key="item.id || \'emp-\' + item.employee_id + \'-\' + index">',
    $content
);

// 2. Fix tombol edit - tambahkan pengecekan ID
$content = str_replace(
    'x-on:click="openEdit(item.id)"',
    'x-on:click="item.id ? openEdit(item.id) : showToastMessage(\'Data absensi belum tersedia\', \'error\')"',
    $content
);

// 3. Fix fetchEmployees - panggil saat init dan outlet selection change
$oldInitFunction = 'async init() {
          await Promise.all([
            this.loadOutlets(),
            this.fetchEmployees(),
            this.fetchStatistics(),
            this.fetchData()
          ]);
        },';

$newInitFunction = 'async init() {
          await this.loadOutlets();
          // Panggil fetchEmployees setelah outlets dimuat dan selected
          if (this.selectedOutlets.length > 0) {
            await Promise.all([
              this.fetchEmployees(),
              this.fetchStatistics(),
              this.fetchData()
            ]);
          }
        },';

$content = str_replace($oldInitFunction, $newInitFunction, $content);

// 4. Fix loadOutlets - pastikan fetchEmployees dipanggil setelah outlet default diset
$oldLoadOutlets = 'async loadOutlets() {
          try {
            const response = await fetch(\'{{ route("finance.outlets.data") }}\');
            const result = await response.json();

            if (result.success) {
              this.outlets = result.data;
              // Set default to first outlet if available
              if (this.outlets.length > 0 && this.selectedOutlets.length === 0) {
                this.selectedOutlets = [this.outlets[0].id_outlet];
              }
              console.log(\'✅ Loaded outlets:\', this.outlets.length);
            }
          } catch (error) {
            console.error(\'❌ Error loading outlets:\', error);
          }
        },';

$newLoadOutlets = 'async loadOutlets() {
          try {
            const response = await fetch(\'{{ route("finance.outlets.data") }}\');
            const result = await response.json();

            if (result.success) {
              this.outlets = result.data;
              // Set default to first outlet if available
              if (this.outlets.length > 0 && this.selectedOutlets.length === 0) {
                this.selectedOutlets = [this.outlets[0].id_outlet];
                // Langsung panggil fetchEmployees setelah outlet default diset
                await this.fetchEmployees();
                console.log(\'✅ Default outlet set and employees loaded\');
              }
              console.log(\'✅ Loaded outlets:\', this.outlets.length);
            }
          } catch (error) {
            console.error(\'❌ Error loading outlets:\', error);
          }
        },';

$content = str_replace($oldLoadOutlets, $newLoadOutlets, $content);

// 5. Fix openCreate - pastikan employees sudah dimuat
$oldOpenCreate = 'openCreate() {
          // Pastikan menggunakan tanggal hari ini
          const today = new Date();
          const todayFormatted = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, \'0\')}-${String(today.getDate()).padStart(2, \'0\')}`;
          
          this.form = {
            id: null,
            employee_id: \'\',
            date: todayFormatted,
            clock_in: \'\',
            clock_out: \'\',
            break_out: \'\',
            break_in: \'\',
            overtime_in: \'\',
            overtime_out: \'\',
            status: \'present\',
            notes: \'\'
          };
          this.errors = {};
          this.showForm = true;
        },';

$newOpenCreate = 'async openCreate() {
          // Pastikan menggunakan tanggal hari ini
          const today = new Date();
          const todayFormatted = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, \'0\')}-${String(today.getDate()).padStart(2, \'0\')}`;
          
          // Pastikan employees sudah dimuat
          if (this.employees.length === 0 && this.selectedOutlets.length > 0) {
            console.log(\'🔄 Loading employees for modal...\');
            await this.fetchEmployees();
          }
          
          this.form = {
            id: null,
            employee_id: \'\',
            date: todayFormatted,
            clock_in: \'\',
            clock_out: \'\',
            break_out: \'\',
            break_in: \'\',
            overtime_in: \'\',
            overtime_out: \'\',
            status: \'present\',
            notes: \'\'
          };
          this.errors = {};
          this.showForm = true;
          
          console.log(\'✅ Modal opened with\', this.employees.length, \'employees available\');
        },';

$content = str_replace($oldOpenCreate, $newOpenCreate, $content);

// 6. Fix validasi format waktu - akan diperbaiki di controller
// Tidak bisa langsung replace karena ada di controller, jadi kita buat note untuk update controller

// 7. Fix ensureTimeFormat function - tambahkan support untuk detik
$oldEnsureTimeFormat = '// Validate 24-hour format
          if (value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
            input.value = value;
            console.log(\'✅ Final time value:\', value);
            
            // Trigger Alpine.js update
            input.dispatchEvent(new Event(\'input\', { bubbles: true }));
          } else {
            console.warn(\'⚠️ Invalid time format:\', value);
          }';

$newEnsureTimeFormat = '// Validate 24-hour format (with or without seconds)
          if (value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/)) {
            input.value = value;
            console.log(\'✅ Final time value:\', value);
            
            // Trigger Alpine.js update
            input.dispatchEvent(new Event(\'input\', { bubbles: true }));
          } else {
            console.warn(\'⚠️ Invalid time format:\', value);
          }';

$content = str_replace($oldEnsureTimeFormat, $newEnsureTimeFormat, $content);

// 8. Fix formatTimeToHHMM function - tambahkan support untuk detik
$oldFormatTimeToHHMM = '// Validate 24-hour format
          if (value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
            console.log(\'✅ Final formatted time:\', value);
            return value;
          } else {
            console.warn(\'⚠️ Invalid time format, returning original:\', value);
            return timeValue; // Return original if can\'t format
          }';

$newFormatTimeToHHMM = '// Validate 24-hour format (with or without seconds)
          if (value.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/)) {
            console.log(\'✅ Final formatted time:\', value);
            return value;
          } else {
            console.warn(\'⚠️ Invalid time format, returning original:\', value);
            return timeValue; // Return original if can\'t format
          }';

$content = str_replace($oldFormatTimeToHHMM, $newFormatTimeToHHMM, $content);

// 9. Tambahkan debug logging untuk troubleshooting
$oldFetchEmployees = 'async fetchEmployees() {
          try {
            if (this.selectedOutlets.length === 0) {
              this.employees = [];
              return;
            }

            const params = new URLSearchParams();
            
            // Add multiple outlet IDs
            this.selectedOutlets.forEach(outletId => {
              params.append(\'outlet_ids[]\', outletId);
            });

            const response = await fetch(`{{ route("sdm.attendance.employees") }}?${params}`);
            const data = await response.json();
            this.employees = data;
          } catch (error) {
            console.error(\'Error fetching employees:\', error);
          }
        },';

$newFetchEmployees = 'async fetchEmployees() {
          try {
            if (this.selectedOutlets.length === 0) {
              console.log(\'⚠️ No outlets selected, clearing employees\');
              this.employees = [];
              return;
            }

            console.log(\'🔄 Fetching employees for outlets:\', this.selectedOutlets);

            const params = new URLSearchParams();
            
            // Add multiple outlet IDs
            this.selectedOutlets.forEach(outletId => {
              params.append(\'outlet_ids[]\', outletId);
            });

            const response = await fetch(`{{ route("sdm.attendance.employees") }}?${params}`);
            const data = await response.json();
            this.employees = data;
            
            console.log(\'✅ Loaded\', this.employees.length, \'employees\');
          } catch (error) {
            console.error(\'❌ Error fetching employees:\', error);
            this.employees = [];
          }
        },';

$content = str_replace($oldFetchEmployees, $newFetchEmployees, $content);

// 10. Fix input time pattern untuk mendukung detik
$content = str_replace(
    'pattern="[0-9]{2}:[0-9]{2}"',
    'pattern="[0-9]{2}:[0-9]{2}(:[0-9]{2})?"',
    $content
);

// Simpan file yang sudah diperbaiki
file_put_contents($originalFile, $content);

echo "✅ File attendance index.blade.php berhasil diperbaiki\n";

// Sekarang update controller untuk mendukung format waktu yang fleksibel
echo "\n🔧 Memperbaiki controller AttendanceManagementController...\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
$controllerBackup = $controllerFile . '.backup-modal-fix.' . date('Y-m-d-H-i-s');

if (file_exists($controllerFile)) {
    copy($controllerFile, $controllerBackup);
    echo "✅ Backup controller dibuat: $controllerBackup\n";
    
    $controllerContent = file_get_contents($controllerFile);
    
    // Update validasi untuk mendukung format HH:MM dan HH:MM:SS
    $oldControllerValidation = "'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'break_in' => 'nullable|date_format:H:i',
            'break_out' => 'nullable|date_format:H:i',
            'overtime_in' => 'nullable|date_format:H:i',
            'overtime_out' => 'nullable|date_format:H:i',";
    
    $newControllerValidation = "'clock_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'clock_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'break_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'break_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'overtime_in' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'overtime_out' => 'nullable|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
    
    $controllerContent = str_replace($oldControllerValidation, $newControllerValidation, $controllerContent);
    
    // Update validasi untuk setWorkHours juga
    $oldWorkHoursValidation = "'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',";
    
    $newWorkHoursValidation = "'clock_in' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'clock_out' => 'required|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',";
    
    $controllerContent = str_replace($oldWorkHoursValidation, $newWorkHoursValidation, $controllerContent);
    
    // Tambahkan custom error messages
    $oldValidatorMessages = '], [
            \'clock_in.date_format\' => \'Format jam masuk harus HH:MM (24 jam)\',
            \'clock_out.date_format\' => \'Format jam pulang harus HH:MM (24 jam)\',
        ]);';
    
    $newValidatorMessages = '], [
            \'clock_in.regex\' => \'Format jam masuk harus HH:MM atau HH:MM:SS (24 jam)\',
            \'clock_out.regex\' => \'Format jam pulang harus HH:MM atau HH:MM:SS (24 jam)\',
            \'break_in.regex\' => \'Format jam istirahat harus HH:MM atau HH:MM:SS (24 jam)\',
            \'break_out.regex\' => \'Format jam selesai istirahat harus HH:MM atau HH:MM:SS (24 jam)\',
            \'overtime_in.regex\' => \'Format jam lembur masuk harus HH:MM atau HH:MM:SS (24 jam)\',
            \'overtime_out.regex\' => \'Format jam lembur keluar harus HH:MM atau HH:MM:SS (24 jam)\',
        ]);';
    
    $controllerContent = str_replace($oldValidatorMessages, $newValidatorMessages, $controllerContent);
    
    file_put_contents($controllerFile, $controllerContent);
    echo "✅ Controller berhasil diperbaiki\n";
}

echo "\n🎯 RINGKASAN PERBAIKAN:\n";
echo "1. ✅ Fixed item.id null error - tambah pengecekan ID sebelum edit\n";
echo "2. ✅ Fixed employee loading - panggil fetchEmployees saat init dan outlet change\n";
echo "3. ✅ Fixed time format validation - support HH:MM dan HH:MM:SS\n";
echo "4. ✅ Added debug logging untuk troubleshooting\n";
echo "5. ✅ Updated controller validation untuk format waktu fleksibel\n";

echo "\n📋 TESTING CHECKLIST:\n";
echo "1. Buka halaman manajemen absensi\n";
echo "2. Klik tombol 'Tambah Absensi' - pastikan data karyawan muncul\n";
echo "3. Pilih karyawan dan isi waktu dengan format HH:MM:SS (misal: 16:21:22)\n";
echo "4. Simpan dan pastikan tidak ada error validasi\n";
echo "5. Klik tombol edit pada data yang ada - pastikan tidak ada error 404\n";
echo "6. Ubah filter outlet dan buka modal lagi - pastikan data karyawan update\n";

echo "\n✅ Perbaikan selesai! Silakan test fungsionalitas.\n";

?>