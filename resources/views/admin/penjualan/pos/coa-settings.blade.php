{{-- resources/views/admin/penjualan/pos/coa-settings.blade.php --}}
<x-layouts.admin :title="'Setting COA POS'">
<div x-data="coaSettingsApp()" x-init="init()" class="space-y-4">

  {{-- Header --}}
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">Setting COA Point of Sales</h1>
        <p class="text-sm text-slate-600 mt-1">Konfigurasi akun untuk integrasi jurnal otomatis POS</p>
      </div>
      <select x-model="outletId" @change="loadSettings()" class="h-10 rounded-xl border border-slate-200 px-3">
        @foreach($outlets as $outlet)
          <option value="{{ $outlet->id_outlet }}" {{ $outlet->id_outlet == $outletId ? 'selected' : '' }}>
            {{ $outlet->nama_outlet }}
          </option>
        @endforeach
      </select>
    </div>
  </section>

  {{-- Form --}}
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
    <form @submit.prevent="saveSettings()">
      <div class="space-y-4">
        
        {{-- Buku Akuntansi --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Buku Akuntansi X<span class="text-red-500">*</span>
          </label>
          <select x-model="form.accounting_book_id" required
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Buku Akuntansi</option>
            @foreach($books as $book)
              <option value="{{ $book->id }}">{{ $book->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Akun Kas --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun Kas <span class="text-red-500">*</span>
          </label>
          <select x-model="form.akun_kas" required
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun Kas (Asset)</option>
            @foreach($accountsByType['asset'] as $account)
              <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
            @endforeach
          </select>
          <p class="text-xs text-slate-500 mt-1">💵 Untuk pembayaran tunai (Tipe: Asset)</p>
        </div>

        {{-- Akun Bank --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun Bank <span class="text-red-500">*</span>
          </label>
          <select x-model="form.akun_bank" required
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun Bank (Asset)</option>
            @foreach($accountsByType['asset'] as $account)
              <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
            @endforeach
          </select>
          <p class="text-xs text-slate-500 mt-1">🏦 Untuk pembayaran transfer/QRIS (Tipe: Asset)</p>
        </div>

        {{-- Akun Piutang Usaha --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun Piutang Usaha <span class="text-red-500">*</span>
          </label>
          <select x-model="form.akun_piutang_usaha" required
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun Piutang (Asset)</option>
            @foreach($accountsByType['asset'] as $account)
              <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
            @endforeach
          </select>
          <p class="text-xs text-slate-500 mt-1">📋 Untuk transaksi bon/piutang (Tipe: Asset)</p>
        </div>

        {{-- Akun Pendapatan Penjualan --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun Pendapatan Penjualan <span class="text-red-500">*</span>
          </label>
          <select x-model="form.akun_pendapatan_penjualan" required
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun Pendapatan (Revenue)</option>
            @foreach($accountsByType['revenue'] as $account)
              <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
            @endforeach
          </select>
          <p class="text-xs text-slate-500 mt-1">💰 Pendapatan dari penjualan (Tipe: Revenue)</p>
        </div>

        {{-- Akun PPN --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun PPN (Pajak Pertambahan Nilai)
          </label>
          <select x-model="form.akun_ppn"
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun PPN (Liability - Opsional)</option>
            @foreach($accountsByType['liability'] as $account)
              <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
            @endforeach
          </select>
          <p class="text-xs text-slate-500 mt-1">📊 Untuk mencatat PPN 10% (Tipe: Liability)</p>
        </div>

        {{-- Akun HPP --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun HPP (Harga Pokok Penjualan)
          </label>
          <select x-model="form.akun_hpp"
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun HPP (Expense - Opsional)</option>
            @foreach($accountsByType['expense'] as $account)
              <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
            @endforeach
          </select>
          <p class="text-xs text-slate-500 mt-1">📦 Untuk mencatat HPP produk yang terjual (Tipe: Expense)</p>
        </div>

        {{-- Akun Persediaan --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Akun Persediaan
          </label>
          <select x-model="form.akun_persediaan"
                  class="w-full h-10 rounded-xl border border-slate-200 px-3">
            <option value="">Pilih Akun Persediaan (Asset - Opsional)</option>
            @foreach($accountsByType['asset'] as $account)
              <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
            @endforeach
          </select>
          <p class="text-xs text-slate-500 mt-1">📦 Untuk mengurangi nilai persediaan (Tipe: Asset)</p>
        </div>

        {{-- Submit Button --}}
        <div class="flex gap-2 pt-4">
          <button type="submit" 
                  class="px-4 h-10 rounded-xl bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50"
                  :disabled="loading">
            <span x-show="!loading">Simpan Setting</span>
            <span x-show="loading">Menyimpan...</span>
          </button>
          <a href="{{ route('admin.penjualan.pos.index') }}" 
             class="px-4 h-10 rounded-xl border border-slate-200 hover:bg-slate-50 inline-flex items-center">
            Kembali ke POS
          </a>
        </div>

      </div>
    </form>
  </section>

</div>

<script>
function coaSettingsApp(){
  return {
    outletId: {{ $outletId }},
    loading: false,
    form: {
      accounting_book_id: '{{ $setting->accounting_book_id ?? "" }}',
      akun_kas: '{{ $setting->akun_kas ?? "" }}',
      akun_bank: '{{ $setting->akun_bank ?? "" }}',
      akun_piutang_usaha: '{{ $setting->akun_piutang_usaha ?? "" }}',
      akun_pendapatan_penjualan: '{{ $setting->akun_pendapatan_penjualan ?? "" }}',
      akun_hpp: '{{ $setting->akun_hpp ?? "" }}',
      akun_persediaan: '{{ $setting->akun_persediaan ?? "" }}',
      akun_ppn: '{{ $setting->akun_ppn ?? "" }}'
    },

    init(){
      console.log('COA Settings initialized');
    },

    async loadSettings(){
      window.location.href = `{{ route('admin.penjualan.pos.coa.settings') }}?outlet_id=${this.outletId}`;
    },

    async saveSettings(){
      this.loading = true;
      try {
        const response = await fetch('{{ route("admin.penjualan.pos.coa.settings.update") }}?outlet_id=' + this.outletId, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify(this.form)
        });

        const result = await response.json();
        
        if(result.success) {
          alert('Setting COA POS berhasil disimpan');
        } else {
          alert('Gagal menyimpan: ' + (result.message || 'Unknown error'));
        }
      } catch(e) {
        console.error(e);
        alert('Terjadi kesalahan saat menyimpan');
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
</x-layouts.admin>
