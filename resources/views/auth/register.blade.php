@extends('layouts.auth')

@section('title', 'Daftar')

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
                Bergabung Yuk
            </span>
            <h1 class="display-4 fw-bold">
                Daftar Akun <span class="text-primary">{{ \App\Models\Pengaturan::namaWarung() }}</span>
            </h1>
            <p class="lead text-muted mx-auto" style="max-width:700px;">
                Daftar untuk menyimpan data pesanan dan melihat riwayat pembelian.
            </p>
        </div>
    </section>

    <!-- Register -->
    <section class="section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="card border-0 shadow rounded-4">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <i class="bi bi-person-plus-fill display-1 text-primary"></i>
                                <h3 class="fw-bold mt-3">
                                    Buat Akun
                                </h3>
                                <p class="text-muted">
                                    Nomor WhatsApp bisa dilengkapi nanti di halaman profil.
                                </p>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('register.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Nama
                                    </label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Masukkan Nama" value="{{ old('name') }}" autocomplete="name" required
                                        autofocus>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        Email
                                    </label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="Masukkan Email" value="{{ old('email') }}" autocomplete="username"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-toggle-password="password" aria-label="Lihat atau sembunyikan password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">
                                        Konfirmasi Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-control" placeholder="Ulangi Password" autocomplete="new-password"
                                            required>
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-toggle-password="password_confirmation"
                                            aria-label="Lihat atau sembunyikan password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                        <i class="bi bi-person-plus me-2"></i>
                                        Daftar
                                    </button>
                                </div>
                            </form>
                            <hr class="my-4">
                            <div class="text-center">
                                Sudah punya akun?
                                <a href="{{ route('login') }}" class="fw-bold text-decoration-none">
                                    Login
                                </a>
                                <br>
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
        document.addEventListener('click', function(e) {
            const tombol = e.target.closest('[data-toggle-password]');
            if (!tombol) return;

            const input = document.getElementById(tombol.dataset.togglePassword);
            const ikon = tombol.querySelector('i');
            const tampil = input.type === 'password';

            input.type = tampil ? 'text' : 'password';
            ikon.classList.toggle('bi-eye', !tampil);
            ikon.classList.toggle('bi-eye-slash', tampil);
        });
    </script>
@endpush
