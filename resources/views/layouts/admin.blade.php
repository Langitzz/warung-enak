<!DOCTYPE html>
<html lang="id">

<head>
    @include('partials.admin-head')
    @stack('styles')
</head>

<body class="with-welcome-text sidebar-fixed">
    <div class="container-scroller">
        @include('partials.admin-navbar')
        <div class="container-fluid page-body-wrapper">
            @include('partials.' . auth()->user()->role . '-sidebar')
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>
                @include('partials.admin-footer')
            </div>
        </div>
    </div>

    <!-- plugins:js -->
    <script src="{{ asset('admin/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->

    <!-- inject:js -->
    <script src="{{ asset('admin/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('admin/assets/js/template.js') }}"></script>
    <script src="{{ asset('admin/assets/js/hoverable-collapse.js') }}"></script>
    <!-- endinject -->

    @stack('scripts')
</body>

</html>
