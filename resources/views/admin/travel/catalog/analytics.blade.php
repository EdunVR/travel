<x-layouts.admin :title="'Analytics — ' . $package->package_name">
<div class="space-y-5">

    {{-- ── Header ── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                <a href="{{ route('admin.inventaris.travel.catalog.index') }}" class="hover:text-primary-600">Katalog</a>
                <span>/</span>
                <a href="{{ route('admin.inventaris.travel.catalog.show', $package->id) }}" class="hover:text-primary-600">{{ $package->package_name }}</a>
                <span>/</span>
                <span class="text-slate-700">Analytics</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <i class="bx bx-bar-chart-alt-2 text-primary-600"></i>
                {{ $package->package_name }}
            </h1>
            <p class="text-slate-500 text-sm mt-0.5">{{ $package->package_code }} &bull; {{ ucfirst($package->package_type) }} &bull; {{ $package->duration_days }} hari</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.inventaris.travel.catalog.show', $package->id) }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">
                <i class="bx bx-info-circle"></i> Detail Paket
            </a>
            <a href="{{ route('admin.inventaris.travel.catalog.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-slate-200 text-slate-700 px-4 py-2 text-sm hover:bg-slate-300">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ── GA4 Status Banner ── --}}
    @if($ga4Enabled && $ga4Error)
    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 flex items-start gap-3 text-sm">
        <i class="bx bx-error text-amber-500 text-lg shrink-0 mt-0.5"></i>
        <div>
            <p class="font-semibold text-amber-800">GA4 tidak dapat diakses</p>
            <p class="text-amber-700 text-xs mt-0.5">{{ $ga4Error }}</p>
            <p class="text-amber-600 text-xs mt-1">Data di bawah menggunakan analytics mandiri dari database.</p>
        </div>
    </div>
    @elseif(!$ga4Enabled)
    <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 flex items-start gap-3 text-sm">
        <i class="bx bx-info-circle text-blue-500 text-lg shrink-0 mt-0.5"></i>
        <div>
            <p class="font-semibold text-blue-800">Google Analytics 4 belum dikonfigurasi</p>
            <p class="text-blue-600 text-xs mt-1">
                Tambahkan <code class="bg-blue-100 px-1 rounded">GA4_PROPERTY_ID</code> dan
                <code class="bg-blue-100 px-1 rounded">GA4_CREDENTIALS_PATH</code> ke file <code>.env</code> untuk mengaktifkan data GA4.
                Data saat ini dari tracking mandiri.
            </p>
        </div>
    </div>
    @elseif($ga4Enabled && $ga4Data)
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 flex items-center gap-3 text-sm">
        <i class="bx bx-check-circle text-emerald-600 text-lg shrink-0"></i>
        <p class="text-emerald-800 font-medium">
            Google Analytics 4 aktif &bull; Data {{ $ga4Data['start_date'] }} s/d {{ $ga4Data['end_date'] }}
        </p>
    </div>
    @endif

    {{-- ── Stats Cards ── --}}
    @php
        $ga4Views   = $ga4Data['total_views']   ?? null;
        $ga4Users   = $ga4Data['total_users']   ?? null;
        $ga4Sessions= $ga4Data['total_sessions']?? null;
        $ga4Bounce  = $ga4Data['avg_bounce']    ?? null;
        $ga4Duration= $ga4Data['avg_duration']  ?? null;
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        {{-- Total Views (all time) --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase">Total Views</p>
                <div class="h-8 w-8 rounded-xl bg-primary-50 flex items-center justify-center">
                    <i class="bx bx-show text-primary-600 text-base"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900">{{ number_format($package->view_count) }}</p>
            <p class="text-xs text-slate-400 mt-1">Sejak paket dibuat</p>
        </div>
        {{-- Views 30 hari --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase">30 Hari</p>
                <div class="h-8 w-8 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="bx bx-calendar text-blue-600 text-base"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-blue-700">{{ number_format($totalViews30d) }}</p>
            <p class="text-xs text-slate-400 mt-1">7 hari: {{ number_format($totalViews7d) }}</p>
        </div>
        {{-- Booking --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase">Booking</p>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <i class="bx bx-user-check text-emerald-600 text-base"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-emerald-700">{{ number_format($totalBookings) }}</p>
            <p class="text-xs text-slate-400 mt-1">Dari total {{ $package->capacity }} kapasitas</p>
        </div>
        {{-- Konversi --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase">Konversi</p>
                <div class="h-8 w-8 rounded-xl bg-amber-50 flex items-center justify-center">
                    <i class="bx bx-trending-up text-amber-600 text-base"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-amber-700">{{ $conversionRate }}%</p>
            <p class="text-xs text-slate-400 mt-1">Booking / Views</p>
        </div>
        {{-- Peak day --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-slate-500 uppercase">Hari Terbaik</p>
                <div class="h-8 w-8 rounded-xl bg-rose-50 flex items-center justify-center">
                    <i class="bx bx-star text-rose-600 text-base"></i>
                </div>
            </div>
            @if($peakDay && $peakDay['views'] > 0)
            <p class="text-lg font-black text-slate-900">{{ $peakDay['label'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $peakDay['views'] }} views</p>
            @else
            <p class="text-lg font-black text-slate-400">—</p>
            <p class="text-xs text-slate-400 mt-1">Belum ada data</p>
            @endif
        </div>
    </div>

    {{-- ── GA4 Stats (jika tersedia) ── --}}
    @if($ga4Data)
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-purple-100 bg-purple-50 p-4">
            <p class="text-xs font-medium text-purple-600 uppercase mb-1">GA4 Page Views</p>
            <p class="text-2xl font-black text-purple-800">{{ number_format($ga4Views) }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4">
            <p class="text-xs font-medium text-indigo-600 uppercase mb-1">Unique Users</p>
            <p class="text-2xl font-black text-indigo-800">{{ number_format($ga4Users) }}</p>
        </div>
        <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4">
            <p class="text-xs font-medium text-cyan-600 uppercase mb-1">Sessions</p>
            <p class="text-2xl font-black text-cyan-800">{{ number_format($ga4Sessions) }}</p>
        </div>
        <div class="rounded-2xl border border-teal-100 bg-teal-50 p-4">
            <p class="text-xs font-medium text-teal-600 uppercase mb-1">Avg. Duration</p>
            <p class="text-2xl font-black text-teal-800">
                @php $m = floor($ga4Duration/60); $s = $ga4Duration%60; @endphp
                {{ $m }}m {{ $s }}s
            </p>
        </div>
    </div>
    @endif

    {{-- ── Grafik Views Harian ── --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-slate-900 flex items-center gap-2">
                <i class="bx bx-line-chart text-primary-600"></i>
                Grafik Views Harian ({{ $days }} Hari Terakhir)
            </h2>
            <span class="text-xs text-slate-400">Sumber: tracking mandiri</span>
        </div>
        <div style="height:220px; position:relative;">
            <canvas id="viewsChart"></canvas>
        </div>
    </div>

    {{-- ── GA4 Grafik (jika tersedia) ── --}}
    @if($ga4Data && count($ga4Data['rows']) > 0)
    <div class="rounded-2xl border border-purple-100 bg-white p-5 shadow-card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-slate-900 flex items-center gap-2">
                <i class="bx bx-bar-chart text-purple-600"></i>
                Google Analytics 4 — Page Views per Hari
            </h2>
            <span class="text-xs text-slate-400">30 hari terakhir</span>
        </div>
        <div style="height:220px; position:relative;">
            <canvas id="ga4Chart"></canvas>
        </div>
    </div>
    @endif

    {{-- ── Info Paket Ringkas ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
            <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                <i class="bx bx-package text-primary-600"></i> Info Paket
            </h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2 border-b border-slate-50">
                    <span class="text-slate-500">Harga</span>
                    <span class="font-semibold text-primary-600">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-50">
                    <span class="text-slate-500">Keberangkatan</span>
                    <span class="font-medium">{{ $package->departure_date->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-50">
                    <span class="text-slate-500">Durasi</span>
                    <span class="font-medium">{{ $package->duration_days }} hari</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-50">
                    <span class="text-slate-500">Kapasitas</span>
                    <span class="font-medium">{{ $package->capacity }} jamaah</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-slate-500">Status</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $package->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst($package->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-card">
            <h2 class="font-semibold text-slate-900 mb-4 flex items-center gap-2">
                <i class="bx bx-data text-primary-600"></i> Views per Hari (tabel)
            </h2>
            <div class="max-h-56 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-500 sticky top-0 bg-white">
                        <tr>
                            <th class="text-left py-1 pr-3">Tanggal</th>
                            <th class="text-right py-1">Views</th>
                            <th class="text-right py-1">Bar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $maxViews = max(array_column($dailyViews, 'views') ?: [1]); @endphp
                        @foreach(array_reverse($dailyViews) as $row)
                        <tr class="border-t border-slate-50">
                            <td class="py-1 pr-3 text-slate-600">{{ $row['label'] }}</td>
                            <td class="py-1 text-right font-semibold {{ $row['views'] > 0 ? 'text-primary-700' : 'text-slate-300' }}">
                                {{ $row['views'] }}
                            </td>
                            <td class="py-1 pl-3" style="width:80px;">
                                <div class="bg-slate-100 rounded h-2 overflow-hidden">
                                    @if($row['views'] > 0)
                                    <div class="h-2 rounded bg-primary-400"
                                         style="width:{{ round(($row['views']/$maxViews)*100) }}%"></div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>{{-- end space-y-5 --}}

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Grafik mandiri ────────────────────────────────────────────────────
    const dailyData = @json($dailyViews);
    const labels    = dailyData.map(d => d.label);
    const views     = dailyData.map(d => d.views);

    const ctxMain = document.getElementById('viewsChart');
    if (ctxMain) {
        new Chart(ctxMain, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Views',
                    data: views,
                    backgroundColor: 'rgba(99,102,241,0.6)',
                    borderColor:     'rgba(99,102,241,1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: (items) => dailyData[items[0].dataIndex].date,
                            label:  (item)  => ` ${item.raw} views`,
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 11 } } },
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } } }
                }
            }
        });
    }

    @if($ga4Data && count($ga4Data['rows']) > 0)
    // ── Grafik GA4 ────────────────────────────────────────────────────────
    const ga4Rows   = @json($ga4Data['rows']);
    const ga4Labels = ga4Rows.map(r => {
        const d = r.date; // format YYYYMMDD
        return d.slice(6,8) + '/' + d.slice(4,6);
    });
    const ga4Views  = ga4Rows.map(r => r.views);

    const ctxGa4 = document.getElementById('ga4Chart');
    if (ctxGa4) {
        new Chart(ctxGa4, {
            type: 'line',
            data: {
                labels: ga4Labels,
                datasets: [{
                    label: 'GA4 Page Views',
                    data: ga4Views,
                    borderColor:     'rgba(139,92,246,1)',
                    backgroundColor: 'rgba(139,92,246,0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 11 } } },
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } } }
                }
            }
        });
    }
    @endif
});
</script>
</x-layouts.admin>
