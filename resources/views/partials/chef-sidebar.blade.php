<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <li class="nav-item {{ request()->routeIs('chef.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('chef.dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('chef.pesanan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('chef.pesanan.index') }}">
                <i class="mdi mdi-silverware-fork-knife menu-icon"></i>
                <span class="menu-title">Pesanan Masuk</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('chef.menu.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('chef.menu.index') }}">
                <i class="mdi mdi-food-outline menu-icon"></i>
                <span class="menu-title">Daftar Menu</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('chef.riwayat') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('chef.riwayat') }}">
                <i class="mdi mdi-history menu-icon"></i>
                <span class="menu-title">Riwayat Masakan</span>
            </a>
        </li>

    </ul>
</nav>
