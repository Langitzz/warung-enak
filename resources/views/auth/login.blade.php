@extends('layouts.auth')

@section('title', 'Login')

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

        .form-control:focus {
            border-color: #008374;
            box-shadow: 0 0 0 .25rem rgba(0, 131, 116, .25);
        }

        .panel-brand {
            background: linear-gradient(135deg, #008374, #00b39f);
            position: relative;
            overflow: hidden;
        }

        .panel-brand::before,
        .panel-brand::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }

        .panel-brand::before {
            width: 320px;
            height: 320px;
            top: -100px;
            left: -100px;
        }

        .panel-brand::after {
            width: 220px;
            height: 220px;
            bottom: -70px;
            right: -50px;
        }

        /* Form muncul halus saat halaman dibuka */
        .panel-form-isi {
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

        /* Getar sebentar kalau login gagal */
        .panel-form-isi.gagal {
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

            .panel-form-isi,
            .panel-form-isi.gagal {
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

    <div class="d-flex" style="min-height: 100vh;">

        {{-- Panel kiri: branding, cuma tampil di layar lebar --}}
        <div class="d-none d-lg-flex col-lg-5 panel-brand text-white flex-column justify-content-center align-items-center p-5 text-center">
            <div style="position: relative; z-index: 1;">
                @if ($logoWarung)
                    <img src="{{ asset('storage/' . $logoWarung) }}" alt="Logo {{ $namaWarung }}"
                        class="rounded-circle border border-4 border-white mb-4"
                        style="width: 110px; height: 110px; object-fit: cover;">
                @else
                    <i class="bi bi-shop d-block mb-3" style="font-size: 4.5rem;"></i>
                @endif
                <h2 class="fw-bold">{{ $namaWarung }}</h2>
                <p class="opacity-75 mb-0" style="max-width: 320px;">
                    Kelola pesanan, menu, dan transaksi dalam satu tempat.
                </p>
            </div>
        </div>

        {{-- Panel kanan: form login --}}
        <div class="col-lg-7 col-12 d-flex align-items-center justify-content-center p-4">
            <div class="w-100 panel-form-isi {{ $errors->any() ? 'gagal' : '' }}" style="max-width: 380px;">

                {{-- Header kecil, cuma muncul di layar sempit (panel kiri disembunyikan) --}}
                <div class="text-center mb-4 d-lg-none">
                    @if ($logoWarung)
                        <img src="{{ asset('storage/' . $logoWarung) }}" alt="Logo {{ $namaWarung }}"
                            class="rounded-circle mb-2" style="width: 64px; height: 64px; object-fit: cover;">
                    @else
                        <i class="bi bi-shop text-primary d-block mb-2" style="font-size: 2.5rem;"></i>
                    @endif
                    <h5 class="fw-bold mb-0">{{ $namaWarung }}</h5>
                </div>

                <h3 class="fw-bold mb-1">Login</h3>
                <p class="text-muted mb-4">Masukkan email dan password Anda.</p>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

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

                <form action="{{ route('login.store') }}" method="POST" id="formLogin">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan Email"
                            value="{{ old('email') }}" autocomplete="username" required
                            @if (!old('email')) autofocus @endif>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Masukkan Password" autocomplete="current-password" required
                                @if (old('email')) autofocus @endif>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword"
                                aria-label="Lihat atau sembunyikan password">
                                <i class="bi bi-eye" id="iconPassword"></i>
                            </button>
                        </div>
                        <div class="form-text text-warning-emphasis d-none" id="peringatanCaps">
                            <i class="bi bi-capslock-fill"></i> Caps Lock sedang aktif
                        </div>
                    </div>
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ingat" id="ingat" value="1">
                            <label class="form-check-label" for="ingat">Ingat Saya</label>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill" id="tombolLogin">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Login
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    @if (Route::has('register'))
                        <div class="mb-2">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Daftar
                                Sekarang</a>
                        </div>
                    @endif
                    <a href="{{ url('/') }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke beranda
                    </a>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const inputPassword = document.getElementById('password');
        const tombolLogin = document.getElementById('tombolLogin');
        const teksTombolAsli = tombolLogin.innerHTML;

        document.getElementById('togglePassword').addEventListener('click', function() {
            const icon = document.getElementById('iconPassword');

            if (inputPassword.type === 'password') {
                inputPassword.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                inputPassword.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        // Status Caps Lock dipantau di level dokumen (bukan cuma pas di kolom password),
        // biar kedeteksi walau nyalanya pas lagi ngetik di kolom lain (misal Email).
        let capsLockAktif = false;

        function perbaruiCapsLock(e) {
            if (e.getModifierState) {
                capsLockAktif = e.getModifierState('CapsLock');
            }
            document.getElementById('peringatanCaps')
                .classList.toggle('d-none', !(capsLockAktif && document.activeElement === inputPassword));
        }

        document.addEventListener('keydown', perbaruiCapsLock);
        document.addEventListener('keyup', perbaruiCapsLock);

        // Begitu kolom password difokus, langsung pakai status Caps Lock yang terakhir
        // diketahui — nggak perlu nunggu keystroke baru di kolom ini.
        inputPassword.addEventListener('focus', function() {
            document.getElementById('peringatanCaps').classList.toggle('d-none', !capsLockAktif);
        });
        inputPassword.addEventListener('blur', function() {
            document.getElementById('peringatanCaps').classList.add('d-none');
        });

        document.getElementById('formLogin').addEventListener('submit', function() {
            tombolLogin.disabled = true;
            tombolLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        });

        window.addEventListener('pageshow', function(e) {
            if (e.persisted) {
                tombolLogin.disabled = false;
                tombolLogin.innerHTML = teksTombolAsli;
            }
        });
    </script>
@endpush