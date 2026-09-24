@php
    // Penanda menu aktif: dipakai untuk membuka grup dan menyalakan menu
    // kalau kamu sedang berada di salah satu halamannya.
    $kelolaDataAktif = request()->routeIs('admin.menu.*', 'admin.kategori.*', 'admin.pesanan.*', 'admin.pengeluaran.*');
    $laporanAktif = request()->routeIs('admin.laporan.*');
    $penggunaAktif = request()->routeIs('admin.pengguna.*');
    $pengaturanAktif = request()->routeIs('admin.pengaturan.*');
@endphp

<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        {{-- ========== Dashboard ========== --}}
        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        {{-- ========== Kelola Data ========== --}}
        {{-- Data Menu sudah punya fitur. Kategori dan Pesanan masih halaman pengganti (admin/segera) --}}
        <li class="nav-item {{ $kelolaDataAktif ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#menu-kelola-data"
                aria-expanded="{{ $kelolaDataAktif ? 'true' : 'false' }}" aria-controls="menu-kelola-data">
                <i class="mdi mdi-database-outline menu-icon"></i>
                <span class="menu-title">Kelola Data</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ $kelolaDataAktif ? 'show' : '' }}" id="menu-kelola-data">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}"
                            href="{{ route('admin.menu.index') }}">Data Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}"
                            href="{{ route('admin.kategori.index') }}">Kategori Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}"
                            href="{{ route('admin.pesanan.index') }}">Data Pesanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.pengeluaran.*') ? 'active' : '' }}"
                            href="{{ route('admin.pengeluaran.index') }}">Data Pengeluaran</a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- ========== Laporan ========== --}}
        {{-- Masih halaman pengganti (admin/segera) --}}
        <li class="nav-item {{ $laporanAktif ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#menu-laporan"
                aria-expanded="{{ $laporanAktif ? 'true' : 'false' }}" aria-controls="menu-laporan">
                <i class="mdi mdi-chart-line menu-icon"></i>
                <span class="menu-title">Laporan</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ $laporanAktif ? 'show' : '' }}" id="menu-laporan">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan.penjualan') ? 'active' : '' }}"
                            href="{{ route('admin.laporan.penjualan') }}">Laporan Penjualan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan.riwayat') ? 'active' : '' }}"
                            href="{{ route('admin.laporan.riwayat') }}">Riwayat Pesanan</a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- ========== Pengguna ========== --}}
        {{-- Masih halaman pengganti (admin/segera). Fitur aslinya butuh sistem login dan role dulu --}}
        <li class="nav-item {{ $penggunaAktif ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#menu-pengguna"
                aria-expanded="{{ $penggunaAktif ? 'true' : 'false' }}" aria-controls="menu-pengguna">
                <i class="mdi mdi-account-group-outline menu-icon"></i>
                <span class="menu-title">Pengguna</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ $penggunaAktif ? 'show' : '' }}" id="menu-pengguna">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.pengguna.index', 'admin.pengguna.create', 'admin.pengguna.edit') ? 'active' : '' }}"
                            href="{{ route('admin.pengguna.index') }}">Data Pengguna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.pengguna.role') ? 'active' : '' }}"
                            href="{{ route('admin.pengguna.role') }}">Role/Akses Pengguna</a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- ========== Pengaturan ========== --}}
        {{-- Masih halaman pengganti (admin/segera) --}}
        <li class="nav-item {{ $pengaturanAktif ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#menu-pengaturan"
                aria-expanded="{{ $pengaturanAktif ? 'true' : 'false' }}" aria-controls="menu-pengaturan">
                <i class="mdi mdi-cog-outline menu-icon"></i>
                <span class="menu-title">Pengaturan</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ $pengaturanAktif ? 'show' : '' }}" id="menu-pengaturan">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.pengaturan.profil') ? 'active' : '' }}"
                            href="{{ route('admin.pengaturan.profil') }}">Profil Warung</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.pengaturan.sistem') ? 'active' : '' }}"
                            href="{{ route('admin.pengaturan.sistem') }}">Pengaturan Sistem</a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>
</nav>
