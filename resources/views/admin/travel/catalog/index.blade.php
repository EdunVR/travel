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

    <!-- Leaderboard Views Terbanyak -->
    @if($leaderboard->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <h2 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
            <i class="bx bx-trophy text-amber-500"></i>
            Top 5 Paket Terbanyak Dilihat
        </h2>
        <div class="space-y-2">
            @foreach($leaderboard as $i => $pkg)
            @php
                $medals = ['🥇','🥈','🥉','4️⃣','5️⃣'];
                $maxViews = $leaderboard->first()->view_count ?: 1;
                $barPct = round(($pkg->view_count / $maxViews) * 100);
            @endphp
            <a href="{{ route('admin.inventaris.travel.catalog.analytics', $pkg->id) }}"
               class="flex items-center gap-3 rounded-xl border border-slate-100 p-2.5 hover:bg-slate-50 transition-colors group">
                <span class="text-lg w-6 text-center shrink-0">{{ $medals[$i] ?? ($i+1) }}</span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <p class="text-sm font-medium text-slate-900 truncate group-hover:text-primary-600">{{ $pkg->package_name }}</p>
                        <span class="text-xs font-bold text-slate-700 shrink-0 flex items-center gap-1">
                            <i class="bx bx-show text-slate-400"></i>{{ number_format($pkg->view_count) }}
                        </span>
                    </div>
                    <div class="bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full bg-amber-400 transition-all"
                             style="width: {{ $barPct }}%"></div>
                    </div>
                    <div class="flex items-center gap-3 mt-1 text-xs text-slate-400">
                        <span class="capitalize">{{ $pkg->package_type }}</span>
                        <span><i class="bx bx-user text-xs"></i> {{ $pkg->booking_count }} booking</span>
                        @if($pkg->departure_date)
                        <span><i class="bx bx-calendar text-xs"></i> {{ $pkg->departure_date->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
                <i class="bx bx-bar-chart-alt-2 text-slate-300 group-hover:text-primary-400 shrink-0"></i>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Package Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($packages as $package)
        {{-- Wrap card dengan link ke halaman analytics --}}
        <div class="relative">
            <a href="{{ route('admin.inventaris.travel.catalog.analytics', $package->id) }}"
               class="absolute inset-0 z-10" title="Lihat Analytics {{ $package->package_name }}"></a>
            <x-package-card :package="$package" />
            {{-- Badge view count --}}
            <div class="absolute top-2 left-2 z-20 pointer-events-none">
                <span class="inline-flex items-center gap-1 rounded-lg bg-black/60 backdrop-blur-sm text-white text-xs font-semibold px-2 py-1">
                    <i class="bx bx-show text-xs"></i> {{ number_format($package->view_count) }}
                </span>
            </div>
        </div>
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
