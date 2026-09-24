<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <li class="nav-item {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('supervisor.dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('supervisor.pesanan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('supervisor.pesanan.index') }}">
                <i class="mdi mdi-clipboard-text-outline menu-icon"></i>
                <span class="menu-title">Monitoring Pesanan</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('supervisor.laporan') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('supervisor.laporan') }}">
                <i class="mdi mdi-chart-line menu-icon"></i>
                <span class="menu-title">Laporan Penjualan</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('supervisor.aktivitas') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('supervisor.aktivitas') }}">
                <i class="mdi mdi-timeline-clock-outline menu-icon"></i>
                <span class="menu-title">Aktivitas Operasional</span>
            </a>
        </li>

    </ul>
</nav>