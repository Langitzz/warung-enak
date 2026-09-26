<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir')</title>

    <link rel="stylesheet" href="{{ asset('admin/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.png') }}">

    @stack('styles')
</head>

<body>

    @php
        $akun = auth()->user();
        $fotoAkun = $akun->foto ? asset('storage/' . $akun->foto) : null;
        $hurufAkun = strtoupper(mb_substr($akun->name, 0, 1));
    @endphp

    <nav class="navbar navbar-expand d-flex align-items-center justify-content-between px-3"
        style="height: 64px; background: #fff; border-bottom: 1px solid #e9ecef;">
        <span class="fw-bold fs-5">
            {{ \App\Models\Pengaturan::namaWarung() }} &mdash; Kasir
        </span>

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                data-bs-toggle="dropdown" aria-expanded="false" style="gap: 8px; color: #212529;">
                @if ($fotoAkun)
                    <img src="{{ $fotoAkun }}" alt="{{ $akun->name }}" class="rounded-circle"
                        style="width: 36px; height: 36px; object-fit: cover;">
                @else
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px; background: #4d55c4; color: #fff; font-weight: 700;">
                        {{ $hurufAkun }}
                    </span>
                @endif
                <span>{{ $akun->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
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
    </nav>

    <div class="p-3" style="height: calc(100vh - 64px); overflow-y: auto;">
        @yield('content')
    </div>

    <script src="{{ asset('admin/assets/vendors/js/vendor.bundle.base.js') }}"></script>

    @stack('scripts')

</body>

</html>