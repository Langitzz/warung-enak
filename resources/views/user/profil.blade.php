@extends('layouts.user')

@section('title', 'Profil Saya')

@section('content')

    <section class="section" style="padding-top: 120px;">
        <div class="container section-title" data-aos="fade-up">
            <h2>Profil Saya</h2>
            <p>Kelola data akun dan lihat riwayat pesananmu</p>
        </div>

        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row gy-4">

                {{-- ================= Kartu ringkasan akun ================= --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm text-center mb-4">
                        <div class="card-body">
                            @if ($user->foto)
                                <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto {{ $user->name }}"
                                    class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
                                    style="width: 100px; height: 100px; background: var(--accent-color); color: #fff; font-size: 40px;">
                                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Riwayat Pesanan</h6>
                            <p class="mb-3">Lihat semua pesanan yang pernah kamu buat.</p>
                            <a href="{{ route('pesanan.saya') }}" class="btn-get-started">
                                <i class="bi bi-receipt"></i> Pesanan Saya
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">

                    {{-- ================= Informasi profil ================= --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Informasi Profil</h5>

                            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nama Lengkap</label>
                                        <input type="text" id="name" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $user->name) }}" autocomplete="name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $user->email) }}" autocomplete="email">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="no_whatsapp" class="form-label">Nomor WhatsApp</label>
                                        <input type="tel" id="no_whatsapp" name="no_whatsapp"
                                            class="form-control @error('no_whatsapp') is-invalid @enderror"
                                            value="{{ old('no_whatsapp', $user->no_whatsapp) }}"
                                            placeholder="Contoh: 0812-3456-7890" autocomplete="tel">
                                        @error('no_whatsapp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Dipakai supaya nggak perlu ngetik ulang tiap checkout.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="foto" class="form-label">Foto Profil</label>
                                        <input type="file" id="foto" name="foto" accept="image/*"
                                            class="form-control @error('foto') is-invalid @enderror">
                                        @error('foto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if ($user->foto)
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="hapus_foto"
                                            name="hapus_foto" value="1">
                                        <label class="form-check-label" for="hapus_foto">Hapus foto profil ini</label>
                                    </div>
                                @endif

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn-get-started border-0">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- ================= Ganti password ================= --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Ganti Password</h5>
                            @include('partials.profil-password')
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection