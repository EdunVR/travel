<ul class="sub-menu">
    @if(in_array('User', Auth::user()->akses ?? []))
        <li
            class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
            <a href="{{ route('user.index') }}">
                <i data-feather="users"></i> <span>Management User Aplikasi</span>
            </a>
        </li>
    @endif
    <!-- Sub-menu yang belum ada -->
    <li
            class="{{ request()->routeIs('hrm.recruitment.index') ? 'active' : '' }}">
            <a href="{{ route('hrm.recruitment.index') }}">
                <i data-feather="user-plus"></i> <span>Kepegawaian & Rekrutmen</span>
            </a>
        </li>
    <li
            class="{{ request()->routeIs('hrm.payroll.index') ? 'active' : '' }}">
            <a href="{{ route('hrm.payroll.index') }}">
                <i data-feather="dollar-sign"></i> <span>Penggajian / Payroll</span>
            </a>
        </li>
    <li
            class="{{ request()->routeIs('hrm.performance.index') ? 'active' : '' }}">
            <a href="{{ route('hrm.performance.index') }}">
                <i data-feather="trending-up"></i> <span>Manajemen Kinerja</span>
            </a>
        </li>
    <li
            class="{{ request()->routeIs('hrm.training.index') ? 'active' : '' }}">
            <a href="{{ route('hrm.training.index') }}">
                <i data-feather="dollar-sign"></i> <span>Pelatihan dan Pengembangan</span>
            </a>
        </li>
    <li
            class="{{ request()->routeIs('hrm.attendance.index') ? 'active' : '' }}">
            <a href="{{ route('hrm.attendance.index') }}">
                <i data-feather="dollar-sign"></i> <span>Manajemen Absensi dan Waktu Kerja</span>
            </a>
        </li>
    
</ul>
