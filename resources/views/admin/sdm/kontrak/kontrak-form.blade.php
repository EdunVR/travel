<x-layouts.admin>
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ isset($kontrak) ? 'Edit' : 'Tambah' }} Kontrak Kerja</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ isset($kontrak) ? route('sdm.kontrak.kontrak.update', $kontrak->id) : route('sdm.kontrak.kontrak.store') }}" 
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($kontrak))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan *</label>
                        <select name="recruitment_id" required class="w-full border-gray-300 rounded-lg">
                            <option value="">Pilih Karyawan ({{ count($employees) }} tersedia)</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ (isset($kontrak) && $kontrak->recruitment_id == $emp->id) ? 'selected' : '' }}>
                                    {{ $emp->name }} - {{ $emp->position }}
                                </option>
                            @endforeach
                        </select>
                        @if(count($employees) == 0)
                            <p class="text-xs text-red-600 mt-1">⚠️ Tidak ada karyawan aktif. Periksa data recruitment.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kontrak *</label>
                        <input type="text" name="nomor_kontrak" value="{{ $kontrak->nomor_kontrak ?? '' }}" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kontrak *</label>
                        <select name="jenis_kontrak" required class="w-full border-gray-300 rounded-lg">
                            <option value="PKWT" {{ (isset($kontrak) && $kontrak->jenis_kontrak == 'PKWT') ? 'selected' : '' }}>PKWT</option>
                            <option value="PKWTT" {{ (isset($kontrak) && $kontrak->jenis_kontrak == 'PKWTT') ? 'selected' : '' }}>PKWTT</option>
                            <option value="Freelance" {{ (isset($kontrak) && $kontrak->jenis_kontrak == 'Freelance') ? 'selected' : '' }}>Freelance</option>
                            <option value="Magang" {{ (isset($kontrak) && $kontrak->jenis_kontrak == 'Magang') ? 'selected' : '' }}>Magang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan *</label>
                        <input type="text" name="jabatan" value="{{ $kontrak->jabatan ?? '' }}" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja *</label>
                        <input type="text" name="unit_kerja" value="{{ $kontrak->unit_kerja ?? '' }}" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai *</label>
                        <input type="date" name="tanggal_mulai" value="{{ isset($kontrak) ? $kontrak->tanggal_mulai->format('Y-m-d') : '' }}" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="{{ isset($kontrak) && $kontrak->tanggal_selesai ? $kontrak->tanggal_selesai->format('Y-m-d') : '' }}" class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gaji Pokok</label>
                        <input type="number" name="gaji_pokok" value="{{ $kontrak->gaji_pokok ?? '' }}" 
                               class="w-full border-gray-300 rounded-lg"
                               onchange="showCurrencyFormat(this)">
                        <p class="text-xs text-blue-600 mt-1 currency-display" style="display: none;"></p>
                    </div>

                    @if(isset($kontrak))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" required class="w-full border-gray-300 rounded-lg">
                            <option value="aktif" {{ $kontrak->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="habis" {{ $kontrak->status == 'habis' ? 'selected' : '' }}>Habis</option>
                            <option value="diperpanjang" {{ $kontrak->status == 'diperpanjang' ? 'selected' : '' }}>Diperpanjang</option>
                            <option value="dibatalkan" {{ $kontrak->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen (PDF/IMG, Max 5MB)</label>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border-gray-300 rounded-lg">
                        @if(isset($kontrak) && $kontrak->file_path)
                            <p class="text-sm text-gray-500 mt-1">File saat ini: <a href="{{ Storage::url($kontrak->file_path) }}" target="_blank" class="text-blue-600">Lihat</a></p>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full border-gray-300 rounded-lg">{{ $kontrak->deskripsi ?? '' }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="{{ route('sdm.kontrak.kontrak.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showCurrencyFormat(input) {
            const value = parseFloat(input.value);
            const displayElement = input.parentNode.querySelector('.currency-display');
            
            if (value > 0) {
                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
                
                displayElement.textContent = '≈ ' + formatted;
                displayElement.style.display = 'block';
            } else {
                displayElement.style.display = 'none';
            }
        }
    </script>
</x-layouts.admin>
