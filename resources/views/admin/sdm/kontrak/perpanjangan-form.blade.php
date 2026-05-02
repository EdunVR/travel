<x-layouts.admin>
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Perpanjang Kontrak</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('sdm.kontrak.perpanjangan.store') }}" method="POST" enctype="multipart/form-data" x-data="perpanjanganForm()">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kontrak yang Akan Diperpanjang *</label>
                        <select name="kontrak_lama_id" required class="w-full border-gray-300 rounded-lg" x-model="kontrakLamaId" @change="loadKontrakData()">
                            <option value="">Pilih Kontrak</option>
                            @foreach($kontrakAktif as $k)
                                <option value="{{ $k->id }}" 
                                        data-nomor="{{ $k->nomor_kontrak }}"
                                        data-employee="{{ $k->recruitment->name }}"
                                        data-jabatan="{{ $k->jabatan }}"
                                        data-unit="{{ $k->unit_kerja }}"
                                        data-jenis="{{ $k->jenis_kontrak }}"
                                        data-gaji="{{ $k->gaji_pokok }}">
                                    {{ $k->nomor_kontrak }} - {{ $k->recruitment->name }} ({{ $k->jabatan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Info Kontrak Lama -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg" x-show="kontrakLamaId">
                        <h3 class="font-semibold text-gray-700 mb-2">Informasi Kontrak Lama:</h3>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-gray-600">Karyawan:</span> <span x-text="employeeName"></span></div>
                            <div><span class="text-gray-600">Jabatan:</span> <span x-text="jabatan"></span></div>
                            <div><span class="text-gray-600">Unit Kerja:</span> <span x-text="unitKerja"></span></div>
                            <div><span class="text-gray-600">Jenis:</span> <span x-text="jenisKontrak"></span></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kontrak Baru *</label>
                        <input type="text" name="nomor_kontrak_baru" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai Baru *</label>
                        <input type="date" name="tanggal_mulai_baru" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai Baru *</label>
                        <input type="date" name="tanggal_selesai_baru" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen Perpanjangan (Opsional)</label>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Perpanjangan</label>
                        <textarea name="alasan" rows="3" class="w-full border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="{{ route('sdm.kontrak.perpanjangan.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Simpan Perpanjangan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function perpanjanganForm() {
            return {
                kontrakLamaId: '',
                employeeName: '',
                jabatan: '',
                unitKerja: '',
                jenisKontrak: '',
                loadKontrakData() {
                    const select = document.querySelector('select[name="kontrak_lama_id"]');
                    const option = select.options[select.selectedIndex];
                    if (option.value) {
                        this.employeeName = option.dataset.employee;
                        this.jabatan = option.dataset.jabatan;
                        this.unitKerja = option.dataset.unit;
                        this.jenisKontrak = option.dataset.jenis;
                    }
                }
            }
        }
    </script>
</x-layouts.admin>
