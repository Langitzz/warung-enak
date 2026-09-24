@php
    // Foto akun yang sedang login (kalau belum ada, tampil huruf pertama nama)
    $akun = auth()->user();
    $fotoAkun = $akun->foto ? asset('storage/' . $akun->foto) : null;
    $hurufAkun = strtoupper(mb_substr($akun->name, 0, 1));

    // Nama warung dari Profil Warung, buat logo navbar
    $namaWarung = \App\Models\Pengaturan::ambil()->nama_warung ?: 'Warung Enak';
    $kataWarung = explode(' ', trim($namaWarung));
    $inisialWarung = strtoupper(mb_substr($kataWarung[0], 0, 1) . mb_substr($kataWarung[1] ?? '', 0, 1));
@endphp

<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
        </div>
        <div>
            <a class="navbar-brand brand-logo" href="{{ route('admin.dashboard') }}">
                <span style="font-size: 1.25rem; font-weight: 700; color: #1F3BB3;">{{ $namaWarung }}</span>
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{ route('admin.dashboard') }}">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; background: #1F3BB3; color: #fff; font-weight: 700; font-size: 1rem;">
                    {{ $inisialWarung }}
                </span>
            </a>
        </div>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-top">
        <ul class="navbar-nav">
            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
                <h1 class="welcome-text">Halo, <span class="text-black fw-bold">{{ auth()->user()->name }}</span></h1>
                <h3 class="welcome-sub-text">Kelola menu dan pesanan Warung Enak</h3>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown user-dropdown">
                <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    @if ($fotoAkun)
                        <img class="rounded-circle" src="{{ $fotoAkun }}" alt="Foto {{ $akun->name }}"
                            style="width: 32px; height: 32px; object-fit: cover;">
                    @else
                        <span
                            class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-semibold"
                            style="width: 32px; height: 32px; font-size: 14px;">{{ $hurufAkun }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <div class="dropdown-header text-center">
                        @if ($fotoAkun)
                            <img class="rounded-circle" src="{{ $fotoAkun }}" alt="Foto {{ $akun->name }}"
                                style="width: 56px; height: 56px; object-fit: cover;">
                        @else
                            <span
                                class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-semibold"
                                style="width: 56px; height: 56px; font-size: 22px;">{{ $hurufAkun }}</span>
                        @endif
                        <p class="mb-1 mt-3 fw-semibold">{{ auth()->user()->name }}</p>
                        <p class="fw-light text-muted mb-0">{{ auth()->user()->email }}</p>
                    </div>
                    <a class="dropdown-item" href="{{ route('profil.edit') }}">
                        <i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>Profil Saya
                    </a>

                    {{-- Logout harus lewat form POST (bukan link biasa), supaya tidak terpicu tanpa sengaja --}}
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
