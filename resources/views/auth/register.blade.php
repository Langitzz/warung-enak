@extends('layouts.auth')

@section('title', 'Daftar')

@push('styles')
    <style>
        html, body {
            height: 100%;
        }

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

        .form-control:focus {
            border-color: #008374;
            box-shadow: 0 0 0 .25rem rgba(0, 131, 116, .25);
        }

        .halaman-daftar {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
            background: #f7faf9;
        }

        .halaman-daftar::before,
        .halaman-daftar::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(0, 131, 116, .08);
        }

        .halaman-daftar::before {
            width: 360px;
            height: 360px;
            top: -120px;
            right: -100px;
        }

        .halaman-daftar::after {
            width: 260px;
            height: 260px;
            bottom: -90px;
            left: -70px;
        }

        .kartu-daftar {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 560px;
            animation: masuk .45s ease both;
        }

        @keyframes masuk {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .kartu-daftar.gagal {
            animation: getar .4s ease;
        }

        @keyframes getar {

            0%,
            100% {
                transform: translateX(0);
            }

            20% {
                transform: translateX(-8px);
            }

            40% {
                transform: translateX(8px);
            }

            60% {
                transform: translateX(-5px);
            }

            80% {
                transform: translateX(5px);
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .kartu-daftar,
            .kartu-daftar.gagal {
                animation: none;
            }
        }
    </style>
@endpush

@section('content')

    @php
        $logoWarung = \App\Models\Pengaturan::ambil()->logo;
        $namaWarung = \App\Models\Pengaturan::namaWarung();
    @endphp

    <div class="halaman-daftar">
        <div class="kartu-daftar {{ $errors->any() ? 'gagal' : '' }}">
            <div class="card border-0 shadow rounded-4">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        @if ($logoWarung)
                            <img src="{{ asset('storage/' . $logoWarung) }}" alt="Logo {{ $namaWarung }}"
                                class="rounded-circle mb-2" style="width: 64px; height: 64px; object-fit: cover;">
                        @else
                            <i class="bi bi-person-plus-fill text-primary d-block mb-2" style="font-size: 2.75rem;"></i>
                        @endif
                        <h3 class="fw-bold mb-1">Daftar Akun {{ $namaWarung }}</h3>
                        <p class="text-muted mb-0">Nomor WhatsApp bisa dilengkapi nanti di halaman profil.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('register.store') }}" method="POST" id="formDaftar">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan Nama"
                                    value="{{ old('name') }}" autocomplete="name" required autofocus>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan Email"
                                    value="{{ old('email') }}" autocomplete="username" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                                    <button type="button" class="btn btn-outline-secondary" data-toggle-password="password"
                                        aria-label="Lihat atau sembunyikan password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text text-warning-emphasis d-none" data-caps-untuk="password">
                                    <i class="bi bi-capslock-fill"></i> Caps Lock aktif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        placeholder="Ulangi Password" autocomplete="new-password" required>
                                    <button type="button" class="btn btn-outline-secondary"
                                        data-toggle-password="password_confirmation"
                                        aria-label="Lihat atau sembunyikan password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text text-warning-emphasis d-none" data-caps-untuk="password_confirmation">
                                    <i class="bi bi-capslock-fill"></i> Caps Lock aktif
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill" id="tombolDaftar">
                                <i class="bi bi-person-plus me-2"></i>
                                Daftar
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <div class="mb-2">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Login</a>
                        </div>
                        <a href="{{ url('/') }}" class="text-decoration-none text-muted small">
                            <i class="bi bi-arrow-left me-1"></i>Kembali ke beranda
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Lihat / sembunyikan password — satu handler buat kedua kolom
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

        // Status Caps Lock dipantau di level dokumen, dipakai bareng oleh kedua kolom
        // password — jadi nggak ilang kalau kamu pindah dari Password ke Konfirmasi Password.
        let capsLockAktif = false;

        function tampilkanPeringatanCaps() {
            const idFokus = document.activeElement.id;
            document.querySelectorAll('[data-caps-untuk]').forEach(function(el) {
                el.classList.toggle('d-none', !(capsLockAktif && el.dataset.capsUntuk === idFokus));
            });
        }

        function perbaruiCapsLock(e) {
            if (e.getModifierState) {
                capsLockAktif = e.getModifierState('CapsLock');
            }
            tampilkanPeringatanCaps();
        }

        document.addEventListener('keydown', perbaruiCapsLock);
        document.addEventListener('keyup', perbaruiCapsLock);

        document.querySelectorAll('#password, #password_confirmation').forEach(function(input) {
            // Begitu salah satu kolom password difokus, langsung pakai status Caps Lock
            // yang terakhir diketahui — nggak perlu nunggu keystroke baru di kolom ini.
            input.addEventListener('focus', tampilkanPeringatanCaps);
            input.addEventListener('blur', function() {
                document.querySelectorAll('[data-caps-untuk]').forEach((el) => el.classList.add('d-none'));
            });
        });

        // Cegah dobel-klik saat submit
        const tombolDaftar = document.getElementById('tombolDaftar');
        const teksTombolAsli = tombolDaftar.innerHTML;

        document.getElementById('formDaftar').addEventListener('submit', function() {
            tombolDaftar.disabled = true;
            tombolDaftar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        });

        window.addEventListener('pageshow', function(e) {
            if (e.persisted) {
                tombolDaftar.disabled = false;
                tombolDaftar.innerHTML = teksTombolAsli;
            }
        });
    </script>
@endpush