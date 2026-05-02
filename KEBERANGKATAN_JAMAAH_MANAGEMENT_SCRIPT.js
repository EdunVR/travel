// Add these to the keberangkatanDetail() Alpine component in show.blade.php

// Data properties to add:
showJamaahModal: false,
jamaahTab: 'registered',
availableJamaah: [],
selectedJamaahIds: [],
selectAllJamaah: false,
jamaahSearch: '',
loadingAvailableJamaah: false,
addingJamaah: false,
removingJamaah: false,
showRemoveConfirm: false,
jamaahToRemove: null,
costData: null,
loadingCost: false,

// Methods to add:

openJamaahModal() {
  this.showJamaahModal = true;
  this.jamaahTab = 'registered';
  this.selectedJamaahIds = [];
  this.selectAllJamaah = false;
},

closeJamaahModal() {
  this.showJamaahModal = false;
  this.jamaahTab = 'registered';
  this.availableJamaah = [];
  this.selectedJamaahIds = [];
  this.jamaahSearch = '';
},

async loadAvailableJamaah() {
  this.loadingAvailableJamaah = true;
  try {
    const params = new URLSearchParams({
      search: this.jamaahSearch
    });
    
    const response = await fetch(`{{ route('admin.inventaris.travel.keberangkatan.available-jamaah', '') }}/${this.keberangkatanId}?${params}`);
    
    if (response.ok) {
      this.availableJamaah = await response.json();
    }
  } catch (error) {
    console.error('Error loading available jamaah:', error);
    this.showToastMessage('Gagal memuat data jamaah', 'error');
  } finally {
    this.loadingAvailableJamaah = false;
  }
},

toggleSelectAll() {
  if (this.selectAllJamaah) {
    this.selectedJamaahIds = this.availableJamaah.map(j => j.booking_id);
  } else {
    this.selectedJamaahIds = [];
  }
},

async addSelectedJamaah() {
  if (this.selectedJamaahIds.length === 0) {
    this.showToastMessage('Pilih minimal 1 jamaah', 'error');
    return;
  }

  // Check capacity
  if (this.selectedJamaahIds.length > this.keberangkatan.available_capacity) {
    this.showToastMessage(`Kapasitas tidak cukup. Tersedia: ${this.keberangkatan.available_capacity}`, 'error');
    return;
  }

  this.addingJamaah = true;
  try {
    const response = await fetch(`{{ route('admin.inventaris.travel.keberangkatan.add-jamaah', '') }}/${this.keberangkatanId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        booking_ids: this.selectedJamaahIds
      })
    });

    const data = await response.json();

    if (response.ok && data.success) {
      this.showToastMessage(data.message, 'success');
      
      // Refresh data
      await this.fetchKeberangkatanData();
      
      // Reset selection
      this.selectedJamaahIds = [];
      this.selectAllJamaah = false;
      
      // Switch to registered tab
      this.jamaahTab = 'registered';
    } else {
      this.showToastMessage(data.message || 'Gagal menambahkan jamaah', 'error');
    }
  } catch (error) {
    console.error('Error adding jamaah:', error);
    this.showToastMessage('Gagal menambahkan jamaah', 'error');
  } finally {
    this.addingJamaah = false;
  }
},

confirmRemoveJamaah(jamaah) {
  this.jamaahToRemove = jamaah;
  this.showRemoveConfirm = true;
},

async removeJamaahNow() {
  if (!this.jamaahToRemove) return;

  this.removingJamaah = true;
  try {
    const response = await fetch(`{{ route('admin.inventaris.travel.keberangkatan.remove-jamaah', '') }}/${this.keberangkatanId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        booking_id: this.jamaahToRemove.booking_id
      })
    });

    const data = await response.json();

    if (response.ok && data.success) {
      this.showToastMessage(data.message, 'success');
      
      // Refresh data
      await this.fetchKeberangkatanData();
      
      // Close confirm modal
      this.showRemoveConfirm = false;
      this.jamaahToRemove = null;
    } else {
      this.showToastMessage(data.message || 'Gagal menghapus jamaah', 'error');
    }
  } catch (error) {
    console.error('Error removing jamaah:', error);
    this.showToastMessage('Gagal menghapus jamaah', 'error');
  } finally {
    this.removingJamaah = false;
  }
},

async loadCostCalculation() {
  this.loadingCost = true;
  try {
    const response = await fetch(`{{ route('admin.inventaris.travel.keberangkatan.total-cost', '') }}/${this.keberangkatanId}`);
    
    if (response.ok) {
      const result = await response.json();
      if (result.success) {
        this.costData = result.data;
      }
    }
  } catch (error) {
    console.error('Error loading cost calculation:', error);
    this.showToastMessage('Gagal memuat perhitungan biaya', 'error');
  } finally {
    this.loadingCost = false;
  }
},

// Watch jamaahTab to load cost when switching to cost tab
$watch('jamaahTab', function(value) {
  if (value === 'cost') {
    this.loadCostCalculation();
  }
}),
