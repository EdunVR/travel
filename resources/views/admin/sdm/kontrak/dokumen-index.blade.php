<x-layouts.admin>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dokumen HR Resmi</h1>
                <p class="text-gray-600 mt-1">SK Jabatan, Surat Tugas, dan dokumen lainnya</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('sdm.kontrak.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="{{ route('sdm.kontrak.dokumen.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Dokumen
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan</label>
                    <select name="recruitment_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">Semua Karyawan</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('recruitment_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }} - {{ $emp->position }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen</label>
                    <input type="text" name="jenis_dokumen" value="{{ request('jenis_dokumen') }}" class="w-full border-gray-300 rounded-lg" placeholder="Cari jenis...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Habis</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Terbit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Masa Berlaku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dokumen as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nomor_dokumen }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">{{ $item->jenis_dokumen }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $item->judul_dokumen }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->recruitment->name ?? 'Umum' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tanggal_terbit->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($item->memiliki_masa_berlaku)
                                    {{ $item->tanggal_berlaku ? $item->tanggal_berlaku->format('d/m/Y') : '-' }} - 
                                    {{ $item->tanggal_berakhir ? $item->tanggal_berakhir->format('d/m/Y') : '-' }}
                                @else
                                    <span class="text-gray-500">Tidak Terbatas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item->status === 'aktif')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Aktif</span>
                                @elseif($item->status === 'habis')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Habis</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex gap-2 items-center">
                                    <!-- Print PDF Button -->
                                    <button onclick="openPrintModal({{ $item->id }}, '{{ addslashes($item->nomor_dokumen) }}')" 
                                            class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition text-xs" 
                                            title="Generate & Print PDF Dokumen">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    
                                    @if($item->file_path)
                                        <a href="{{ Storage::url($item->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="Lihat File"><i class="fas fa-file-pdf"></i></a>
                                    @endif
                                    <a href="{{ route('sdm.kontrak.dokumen.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('sdm.kontrak.dokumen.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data dokumen HR</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $dokumen->links() }}</div>
    </div>

    <!-- Modal Print PDF -->
    <div id="printModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Preview Dokumen HR - <span id="modalDokumenNumber"></span></h3>
                <button onclick="closePrintModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="mb-4 flex gap-2">
                <a id="downloadPdfBtn" href="#" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                <button onclick="printPdf()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
            
            <div class="border rounded-lg overflow-hidden" style="height: 70vh;">
                <iframe id="pdfFrame" src="" class="w-full h-full" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    <script>
        function openPrintModal(dokumenId, nomorDokumen) {
            const modal = document.getElementById('printModal');
            const iframe = document.getElementById('pdfFrame');
            const downloadBtn = document.getElementById('downloadPdfBtn');
            const modalNumber = document.getElementById('modalDokumenNumber');
            
            const pdfUrl = '{{ route("sdm.kontrak.dokumen.print", ":id") }}'.replace(':id', dokumenId);
            
            iframe.src = pdfUrl;
            downloadBtn.href = pdfUrl;
            modalNumber.textContent = nomorDokumen;
            modal.classList.remove('hidden');
        }

        function closePrintModal() {
            const modal = document.getElementById('printModal');
            const iframe = document.getElementById('pdfFrame');
            
            modal.classList.add('hidden');
            iframe.src = '';
        }

        function printPdf() {
            const iframe = document.getElementById('pdfFrame');
            iframe.contentWindow.print();
        }

        // Close modal when clicking outside
        document.getElementById('printModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePrintModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePrintModal();
            }
        });
    </script>
</x-layouts.admin>
