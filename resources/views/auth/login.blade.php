@extends('layouts.auth')

@section('title', 'Login')

{{-- Template Impact tidak mengubah warna "primary" bawaan Bootstrap (biru), jadi kita
     samakan dengan warna aksen template (teal). Nanti dipindah ke layout kalau halaman
     lain sudah memakainya. --}}
@push('styles')
    <style>
        .btn-primary {
            --bs-btn-bg: #008374;
            --bs-btn-border-color: #008374;
            --bs-btn-hover-bg: #006b5f;
            --bs-btn-hover-border-color: #006b5f;
            --bs-btn-active-bg: #006b5f;
            --bs-btn-active-border-color: #006b5f;
            --bs-btn-disabled-bg: #008374;
            --bs-btn-disabled-border-color: #008374;
        }

        .text-primary {
            color: #008374 !important;
        }

        .badge.bg-primary {
            background-color: #008374 !important;
        }

        .form-control:focus {
            border-color: #008374;
            box-shadow: 0 0 0 .25rem rgba(0, 131, 116, .25);
        }
    </style>
@endpush

@section('content')

    <!-- Hero -->
    <section class="section light-background py-5">
        <div class="container text-center">
            <span class="badge bg-primary mb-3">
                Selamat Datang
            </span>
            <h1 class="display-4 fw-bold">
                Login <span class="text-primary">Warung Enak</span>
            </h1>
            <p class="lead text-muted mx-auto" style="max-width:700px;">
                Masuk ke akun Anda untuk melanjutkan.
            </p>
        </div>
    </section>

    <!-- Login -->
    <section class="section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="card border-0 shadow rounded-4">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <i class="bi bi-person-circle display-1 text-primary"></i>
                                <h3 class="fw-bold mt-3">
                                    Login Akun
                                </h3>
                                <p class="text-muted">
                                    Silakan masukkan email dan password Anda.
                                </p>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('login.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        Email
                                    </label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="Masukkan Email" value="{{ old('email') }}" autocomplete="username"
                                        required autofocus>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="Masukkan Password" autocomplete="current-password" required>
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword"
                                            aria-label="Lihat atau sembunyikan password">
                                            <i class="bi bi-eye" id="iconPassword"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="ingat" id="ingat"
                                            value="1">
                                        <label class="form-check-label" for="ingat">
                                            Ingat Saya
                                        </label>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Login
                                    </button>
                                </div>
                            </form>
                            <hr class="my-4">
                            <div class="text-center">
                                {{-- Link Daftar baru muncul kalau route 'register' sudah dibuat --}}
                                @if (Route::has('register'))
                                    Belum punya akun?
                                    <a href="{{ route('register') }}" class="fw-bold text-decoration-none">
                                        Daftar Sekarang
                                    </a>
                                    <br>
                                @endif
                                <a href="{{ url('/') }}" class="text-decoration-none text-muted">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali ke beranda
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = document.getElementById('iconPassword');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    </script>
@endpush
