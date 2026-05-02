<x-layouts.admin>
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ isset($sp) ? 'Edit' : 'Tambah' }} Surat Peringatan</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ isset($sp) ? route('sdm.kontrak.sp.update', $sp->id) : route('sdm.kontrak.sp.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($sp)) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan *</label>
                        <select name="recruitment_id" required class="w-full border-gray-300 rounded-lg">
                            <option value="">Pilih Karyawan</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ (isset($sp) && $sp->recruitment_id == $emp->id) ? 'selected' : '' }}>{{ $emp->name }} - {{ $emp->position }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor SP *</label>
                        <input type="text" name="nomor_sp" value="{{ $sp->nomor_sp ?? '' }}" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis SP *</label>
                        <select name="jenis_sp" required class="w-full border-gray-300 rounded-lg">
                            <option value="SP1" {{ (isset($sp) && $sp->jenis_sp == 'SP1') ? 'selected' : '' }}>SP1</option>
                            <option value="SP2" {{ (isset($sp) && $sp->jenis_sp == 'SP2') ? 'selected' : '' }}>SP2</option>
                            <option value="SP3" {{ (isset($sp) && $sp->jenis_sp == 'SP3') ? 'selected' : '' }}>SP3</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal SP *</label>
                        <input type="date" name="tanggal_sp" value="{{ isset($sp) ? $sp->tanggal_sp->format('Y-m-d') : '' }}" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berlaku *</label>
                        <input type="date" name="tanggal_berlaku" value="{{ isset($sp) ? $sp->tanggal_berlaku->format('Y-m-d') : '' }}" required class="w-full border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" value="{{ isset($sp) && $sp->tanggal_berakhir ? $sp->tanggal_berakhir->format('Y-m-d') : '' }}" class="w-full border-gray-300 rounded-lg">
                    </div>

                    @if(isset($sp))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" required class="w-full border-gray-300 rounded-lg">
                            <option value="aktif" {{ $sp->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ $sp->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ $sp->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Dokumen SP</label>
                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border-gray-300 rounded-lg">
                        @if(isset($sp) && $sp->file_path)
                            <p class="text-sm text-gray-500 mt-1">File saat ini: <a href="{{ Storage::url($sp->file_path) }}" target="_blank" class="text-blue-600">Lihat</a></p>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan *</label>
                        <textarea name="alasan" rows="3" required class="w-full border-gray-300 rounded-lg">{{ $sp->alasan ?? '' }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="catatan" rows="2" class="w-full border-gray-300 rounded-lg">{{ $sp->catatan ?? '' }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="{{ route('sdm.kontrak.sp.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
