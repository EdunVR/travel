<x-layouts.admin :title="'Detail Paket - ' . $package->package_name">
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold">{{ $package->package_name }}</h1>
            <p class="text-slate-600 text-sm">{{ $package->package_code }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.inventaris.travel.catalog.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-200 text-slate-700 px-4 py-2 hover:bg-slate-300">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
            @if($availableSeats > 0)
            <a href="{{ route('admin.inventaris.booking.index', ['package_id' => $package->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
                <i class='bx bx-cart'></i> Booking Sekarang
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Package Image -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden cursor-pointer" onclick="openImageModal('{{ $package->image_path ? asset('storage/' . $package->image_path) : '' }}')">
            @if($package->image_path)
            <img src="{{ asset('storage/' . $package->image_path) }}" class="w-full h-96 object-cover" alt="{{ $package->package_name }}">
            @else
            <div class="w-full h-96 bg-slate-200 flex items-center justify-center">
                <i class='bx bx-image text-8xl text-slate-400'></i>
            </div>
            @endif
        </div>

        <!-- info paket -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Informasi Paket</h2>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Kode Paket</span>
                    <span class="font-medium text-slate-900">{{ $package->package_code }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Jenis</span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full {{ $package->package_type == 'hajj' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }} text-xs font-medium">
                        {{ ucfirst($package->package_type) }}
                    </span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Durasi</span>
                    <span class="font-medium text-slate-900">{{ $package->duration_days }} Hari</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Keberangkatan</span>
                    <span class="font-medium text-slate-900">{{ $package->departure_date->format('d F Y') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Kepulangan</span>
                    <span class="font-medium text-slate-900">{{ $package->return_date->format('d F Y') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Harga</span>
                    <span class="text-2xl font-bold text-primary-600">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Kapasitas</span>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-slate-900">{{ $package->capacity }} Jamaah</span>
                        @if($availableSeats > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-medium">
                            <i class='bx bx-check-circle'></i> {{ $availableSeats }} Tersedia
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-medium">
                            <i class='bx bx-x-circle'></i> Penuh
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-slate-600">Popularitas</span>
                    <div class="flex items-center gap-3 text-sm text-slate-600">
                        <span class="flex items-center gap-1">
                            <i class='bx bx-show'></i> {{ $package->view_count }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class='bx bx-user'></i> {{ $package->booking_count }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Deskripsi Paket</h2>
        <p class="text-slate-700 leading-relaxed">{{ $package->description ?: 'Tidak ada deskripsi.' }}</p>
    </div>

    <!-- Inclusions -->
    @if(count($inclusions) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Fasilitas yang Termasuk</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($inclusions as $inclusion)
            <div class="flex items-start gap-2">
                <i class='bx bx-check-circle text-green-600 text-xl mt-0.5'></i>
                <span class="text-slate-700">{{ $inclusion }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Departure Batches -->
    @if($package->keberangkatan->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Jadwal Keberangkatan</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Kode</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Nama Keberangkatan</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Tanggal</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Jumlah Jamaah</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($package->keberangkatan as $keberangkatan)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="py-3 px-4 text-sm text-slate-900">{{ $keberangkatan->keberangkatan_code }}</td>
                        <td class="py-3 px-4 text-sm text-slate-900">{{ $keberangkatan->keberangkatan_name }}</td>
                        <td class="py-3 px-4 text-sm text-slate-900">{{ $keberangkatan->departure_date->format('d M Y') }}</td>
                        <td class="py-3 px-4 text-sm text-slate-900">{{ $keberangkatan->total_jamaah }}</td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $keberangkatan->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($keberangkatan->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tour Plan -->
    @if($package->tourPlans->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
            <i class='bx bx-calendar-event text-primary-600'></i>
            Rencana Perjalanan
        </h2>
        <div class="space-y-4">
            @foreach($package->tourPlans as $day)
            <div class="border border-slate-200 rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-4 py-3 border-b border-blue-200">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold">
                            {{ $day->day_number }}
                        </span>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $day->day_title }}</h3>
                            <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($day->day_date)->format('d F Y') }}</p>
                        </div>
                    </div>
                    @if($day->description)
                    <p class="text-sm text-gray-600 mt-2">{{ $day->description }}</p>
                    @endif
                </div>
                @if($day->activities->count() > 0)
                <div class="p-4 space-y-3">
                    @foreach($day->activities as $activity)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-16 text-center">
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">
                                {{ \Carbon\Carbon::parse($activity->activity_time)->format('H:i') }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $activity->activity_title }}</h4>
                            @if($activity->activity_description)
                            <p class="text-sm text-gray-600 mt-1">{{ $activity->activity_description }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
</x-layouts.admin>

{{-- Image Modal --}}
<div id="imageModal" class="fixed inset-0 z-[100] hidden bg-black bg-opacity-90 flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-7xl max-h-full">
        <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-4xl font-bold">&times;</button>
        <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
    </div>
</div>

<script>
function openImageModal(src) {
    if (!src) return;
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
