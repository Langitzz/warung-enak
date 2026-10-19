@push('styles')
    <style>
        @keyframes munculBadge {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .badge.rounded-pill.bg-danger {
                animation: none !important;
            }
        }
    </style>
@endpush

@php
    $namaWarung = \App\Models\Pengaturan::namaWarung();
    $jumlahKeranjang = array_sum(session('keranjang', []));

    // Foto akun yang sedang login (kalau belum ada, tampil huruf pertama nama) — sama pola kayak admin-navbar
    $akun = auth()->user();
    $fotoAkun = $akun && $akun->foto ? asset('storage/' . $akun->foto) : null;
    $hurufAkun = $akun ? strtoupper(mb_substr($akun->name, 0, 1)) : '';

    // Highlight menu aktif buat link yang ngarah ke HALAMAN LAIN (bukan section di landing page).
    // Scrollspy bawaan template cuma jalan buat link berhash (#tentang, #kontak, dst), jadi
    // "Menu" dan "Cek Pesanan" perlu ditandai manual dari sisi server di sini.
    $diBeranda = request()->is('/');
    $diMenu = request()->routeIs('menu.index', 'keranjang.*', 'checkout.*');
    $diCekPesanan = request()->routeIs('pesanan.cek', 'pesanan.cek.proses');
@endphp

<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

        <a href="{{ url('/') }}" class="logo d-flex align-items-center">
            <h1 class="sitename">{{ $namaWarung }}</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="{{ url('/') }}" class="{{ $diBeranda ? 'active' : '' }}">Beranda</a></li>
                <li><a href="{{ url('/') }}#about">Tentang</a></li>
                <li><a href="{{ route('menu.index') }}" class="{{ $diMenu ? 'active' : '' }}">Menu</a></li>
                <li><a href="{{ url('/') }}#cara-pesan">Cara Pesan</a></li>
                <li><a href="{{ route('pesanan.cek') }}" class="{{ $diCekPesanan ? 'active' : '' }}">Cek Pesanan</a>
                </li>
                <li><a href="{{ url('/') }}#contact">Kontak</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a href="{{ route('keranjang.index') }}" class="position-relative d-inline-flex align-items-center me-3"
            style="color: var(--nav-color); transition: transform .15s ease;"
            onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <i class="bi bi-cart3 fs-5"></i>
            <span id="badgeKeranjang"
                class="badge rounded-pill bg-danger position-absolute {{ $jumlahKeranjang > 0 ? '' : 'd-none' }}"
                style="top: -8px; right: -10px; font-size: 0.65rem; animation: munculBadge .25s ease;">
                {{ $jumlahKeranjang }}
            </span>
        </a>

        @auth
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--nav-color); gap: 8px;">
                    @if ($fotoAkun)
                        <img src="{{ $fotoAkun }}" alt="{{ $akun->name }}" class="rounded-circle"
                            style="width: 36px; height: 36px; object-fit: cover;">
                    @else
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px; background: var(--accent-color); color: var(--contrast-color); font-weight: 700;">
                            {{ $hurufAkun }}
                        </span>
                    @endif
                    <span>{{ $akun->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('pesanan.saya') }}">Pesanan Saya</a></li>
                    <li><a class="dropdown-item" href="{{ route('profil.edit') }}">Profil Saya</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="px-3">
                            @csrf
                            <button type="submit" class="dropdown-item px-0">Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn-getstarted">Masuk</a>
        @endauth

    </div>
</header>
