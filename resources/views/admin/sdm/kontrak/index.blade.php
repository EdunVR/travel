<x-layouts.admin>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Kontrak & Dokumen HR</h1>
                    <p class="text-gray-600 mt-1">Kelola kontrak kerja, perpanjangan, surat peringatan, dan dokumen HR</p>
                </div>
                
                <!-- Outlet Filter -->
                @if($outlets->count() > 1)
                <div class="w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Outlet</label>
                    <select id="outletFilter" class="w-full border-gray-300 rounded-lg" onchange="changeOutlet(this.value)">
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id_outlet }}" {{ $selectedOutletId == $outlet->id_outlet ? 'selected' : '' }}>
                                {{ $outlet->nama_outlet }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>
        
        <script>
        function changeOutlet(outletId) {
            window.location.href = '{{ route("sdm.kontrak.index") }}?outlet_id=' + outletId;
        }
        </script>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Kontrak Aktif</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_kontrak_aktif'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Akan Habis</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['kontrak_akan_habis'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">SP Aktif</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['total_sp_aktif'] }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Dokumen</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['total_dokumen'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Kontrak Kerja -->
            <a href="{{ route('sdm.kontrak.kontrak.index') }}" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg group-hover:bg-blue-200 transition">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-blue-600 group-hover:translate-x-1 transition">→</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Kontrak Kerja</h3>
                <p class="text-sm text-gray-600">Kelola kontrak kerja karyawan (PKWT, PKWTT, dll)</p>
            </a>

            <!-- Perpanjangan Kontrak -->
            <a href="{{ route('sdm.kontrak.perpanjangan.index') }}" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-lg group-hover:bg-green-200 transition">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <span class="text-green-600 group-hover:translate-x-1 transition">→</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Perpanjangan Kontrak</h3>
                <p class="text-sm text-gray-600">Proses perpanjangan kontrak kerja karyawan</p>
            </a>

            <!-- Monitoring Masa Berlaku -->
            <a href="{{ route('sdm.kontrak.monitoring') }}" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-yellow-100 p-3 rounded-lg group-hover:bg-yellow-200 transition">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <span class="text-yellow-600 group-hover:translate-x-1 transition">→</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Monitoring Masa Berlaku</h3>
                <p class="text-sm text-gray-600">Pantau dokumen yang akan/sudah habis masa berlaku</p>
            </a>

            <!-- Surat Peringatan -->
            <a href="{{ route('sdm.kontrak.sp.index') }}" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-red-100 p-3 rounded-lg group-hover:bg-red-200 transition">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <span class="text-red-600 group-hover:translate-x-1 transition">→</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Surat Peringatan</h3>
                <p class="text-sm text-gray-600">Kelola SP1, SP2, SP3 karyawan</p>
            </a>

            <!-- Dokumen HR -->
            <a href="{{ route('sdm.kontrak.dokumen.index') }}" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg group-hover:bg-purple-200 transition">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-purple-600 group-hover:translate-x-1 transition">→</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Dokumen HR Resmi</h3>
                <p class="text-sm text-gray-600">SK Jabatan, Surat Tugas, dan dokumen lainnya</p>
            </a>
        </div>
    </div>
</x-layouts.admin>
