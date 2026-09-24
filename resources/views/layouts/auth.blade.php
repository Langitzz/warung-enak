<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Masuk') - Warung Enak</title>

    <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.png') }}">

    {{-- Font template Impact (kalau offline, otomatis pakai font cadangan sistem) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600&display=swap"
        rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="{{ asset('user/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="{{ asset('user/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    {{-- CSS utama template Impact --}}
    <link href="{{ asset('user/assets/css/main.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>

    {{-- Isi Login / Register --}}
    @yield('content')

    {{-- Bootstrap JS (path sekarang memakai user/ seperti CSS-nya) --}}
    <script src="{{ asset('user/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    @stack('scripts')

</body>

</html>
