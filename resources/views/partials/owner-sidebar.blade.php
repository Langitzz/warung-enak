<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <li class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('owner.dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('owner.laporan') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('owner.laporan') }}">
                <i class="mdi mdi-chart-line menu-icon"></i>
                <span class="menu-title">Ringkasan Penjualan</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('owner.performa-menu') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('owner.performa-menu') }}">
                <i class="mdi mdi-star-outline menu-icon"></i>
                <span class="menu-title">Performa Menu</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('owner.laporan-bisnis') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('owner.laporan-bisnis') }}">
                <i class="mdi mdi-briefcase-outline menu-icon"></i>
                <span class="menu-title">Laporan Bisnis</span>
            </a>
        </li>

    </ul>
</nav>