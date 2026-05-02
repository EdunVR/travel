<ul class="sub-menu">
        <li
            class="{{ request()->routeIs('rab_template.index') ? 'active' : '' }}">
            <a href="{{ route('rab_template.index') }}">
                <i data-feather="book"></i> <span>Manajemen RAB</span>
            </a>
        </li>
    @if(in_array('Pengeluaran', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('pengeluaran.index') ? 'active' : '' }}">
            <a href="{{ route('pengeluaran.index') }}">
                <i data-feather="dollar-sign"></i> <span>Pengeluaran</span>
            </a>
        </li>
    @endif
    @if(in_array('Hutang', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('hutang.index') ? 'active' : '' }}">
            <a href="{{ route('hutang.index') }}">
                <i data-feather="dollar-sign"></i> <span>Hutang ke Supplier</span>
            </a>
        </li>
    @endif
    @if(in_array('Piutang', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('piutang.index') ? 'active' : '' }}">
            <a href="{{ route('piutang.index') }}">
                <i data-feather="dollar-sign"></i> <span>Piutang dari Customer</span>
            </a>
        </li>
    @endif

        <li
            class="{{ request()->routeIs('financial.book.list') ? 'active' : '' }}">
            <a href="{{ route('financial.book.list') }}">
                <i data-feather="book"></i> <span>Akun, Buku & Saldo Awal</span>
            </a>
        </li>
        <li
            class="{{ request()->routeIs('financial.journal.index') ? 'active' : '' }}">
            <a href="{{ route('financial.journal.index') }}">
                <i data-feather="book"></i> <span>Jurnal Transaksi</span>
            </a>
        </li>
        <li
            class="{{ request()->routeIs('financial.fixed-asset.index') ? 'active' : '' }}">
            <a href="{{ route('financial.fixed-asset.index') }}">
                <i data-feather="book"></i> <span>Aktiva Tetap</span>
            </a>
        </li>
        <li
            class="{{ request()->routeIs('financial.annual-tax-report.index') ? 'active' : '' }}">
            <a href="{{ route('financial.annual-tax-report.index') }}">
                <i data-feather="book"></i> <span>SPT Tahunan</span>
            </a>
        </li>
        <li
            class="{{ request()->routeIs('financial.ledger.index') ? 'active' : '' }}">
            <a href="{{ route('financial.ledger.index') }}">
                <i data-feather="book"></i> <span>Buku Besar</span>
            </a>
        </li>
        <li
            class="{{ request()->routeIs('financial.worksheet.index') ? 'active' : '' }}">
            <a href="{{ route('financial.worksheet.index') }}">
                <i data-feather="book"></i> <span>Neraca Lajur</span>
            </a>
        </li>
        <li
            class="{{ request()->routeIs('financial.profit-loss.index') ? 'active' : '' }}">
            <a href="{{ route('financial.profit-loss.index') }}">
                <i data-feather="book"></i> <span>Laba Rugi</span>
            </a>
        </li>
        <li
            class="{{ request()->routeIs('financial.equity-change.index') ? 'active' : '' }}">
            <a href="{{ route('financial.equity-change.index') }}">
                <i data-feather="book"></i> <span>Perubahan Modal</span>
            </a>
        </li>
        <li
            class="{{ request()->routeIs('financial.balance-sheet.index') ? 'active' : '' }}">
            <a href="{{ route('financial.balance-sheet.index') }}">
                <i data-feather="book"></i> <span>Neraca</span>
            </a>
        </li>
        <li
            class="{{ request()->routeIs('financial.cash-flow.index') ? 'active' : '' }}">
            <a href="{{ route('financial.cash-flow.index') }}">
                <i data-feather="book"></i> <span>Arus Kas</span>
            </a>
        </li>
    
    <!-- Sub-menu yang belum ada -->
    <li class="unavailable">
        <a href="#">
            <i data-feather="file-text"></i> <span>Pelaporan Keuangan</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="credit-card"></i> <span>Manajemen Kas dan Bank</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="pie-chart"></i> <span>Anggaran dan Perencanaan Keuangan</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
    <li class="unavailable">
        <a href="#">
            <i data-feather="file"></i> <span>Pajak dan Compliance</span>
            <i data-feather="lock" class="unavailable-icon" title="Akses Terbatas"></i>
        </a>
    </li>
</ul>
