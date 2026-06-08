<x-layouts.admin :title="'Katalog Paket Travel'">
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold">Katalog Paket Travel</h1>
            <p class="text-slate-600 text-sm">Jelajahi paket Hajj dan Umrah yang tersedia</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.inventaris.travel.catalog.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Destinasi</label>
                    <select name="destination" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                        <option value="">Semua</option>
                        <option value="hajj" {{ request('destination') == 'hajj' ? 'selected' : '' }}>Hajj</option>
                        <option value="umrah" {{ request('destination') == 'umrah' ? 'selected' : '' }}>Umrah</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Bulan Keberangkatan</label>
                    <select name="month" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Durasi (Hari)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="duration_min" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Min" value="{{ request('duration_min') }}">
                        <input type="number" name="duration_max" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Max" value="{{ request('duration_max') }}">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Harga (Rp)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="price_min" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Min" value="{{ request('price_min') }}">
                        <input type="number" name="price_max" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Max" value="{{ request('price_max') }}">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Urutkan</label>
                    <select name="sort_by" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                        <option value="departure_date" {{ request('sort_by') == 'departure_date' ? 'selected' : '' }}>Tanggal Keberangkatan</option>
                        <option value="price_low" {{ request('sort_by') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_high" {{ request('sort_by') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="popular" {{ request('sort_by') == 'popular' ? 'selected' : '' }}>Paling Populer</option>
                        <option value="duration" {{ request('sort_by') == 'duration' ? 'selected' : '' }}>Durasi</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
                    <i class='bx bx-search'></i> Filter
                </button>
                <a href="{{ route('admin.inventaris.travel.catalog.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-200 text-slate-700 px-4 py-2 hover:bg-slate-300">
                    <i class='bx bx-reset'></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Package Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($packages as $package)
        <x-package-card :package="$package" :href="route('admin.inventaris.travel.catalog.analytics', $package->id)" />
        @empty
        <div class="col-span-full">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                <i class='bx bx-info-circle text-3xl text-blue-600 mb-2'></i>
                <p class="text-blue-800">Tidak ada paket yang tersedia saat ini.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($packages->hasPages())
    <div class="flex justify-center">
        {{ $packages->links() }}
    </div>
    @endif
</div>
</x-layouts.admin>

<script>
function copyPackageLink(packageId, packageName) {
    // Generate public link dengan url() helper Laravel
    const publicUrl = '{{ url("/paket") }}/' + packageId;
    
    // Copy to clipboard
    navigator.clipboard.writeText(publicUrl).then(() => {
        // Show success notification
        Swal.fire({
            icon: 'success',
            title: 'Link Disalin!',
            text: `Link paket "${packageName}" telah disalin ke clipboard`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }).catch(err => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = publicUrl;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            Swal.fire({
                icon: 'success',
                title: 'Link Disalin!',
                text: `Link paket "${packageName}" telah disalin ke clipboard`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } catch (e) {
            document.body.removeChild(textArea);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menyalin link. Silakan copy manual: ' + publicUrl,
                showConfirmButton: true
            });
        }
    });
}
</script>
