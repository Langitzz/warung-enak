@extends('layouts.kasir')
@section('title', 'Profil Saya')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">

        {{-- ================= Kartu ringkasan akun ================= --}}
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body text-center">
                    @if ($user->foto)
                        <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto {{ $user->name }}"
                            class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 120px; height: 120px; border-radius: 50%; font-size: 48px;">
                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">Kasir</p>

                    <hr>

                    <div class="text-start">
                        <p>
                            <strong>Email :</strong><br>
                            {{ $user->email }}
                        </p>
                        <p class="mb-0">
                            <strong>WhatsApp :</strong><br>
                            {{ $user->no_whatsapp ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">

            {{-- ================= Informasi profil ================= --}}
            <div class="card card-rounded grid-margin">
                <div class="card-body">
                    <h4 class="card-title mb-4">Informasi Profil</h4>

                    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nama Lengkap</label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" autocomplete="name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" autocomplete="email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="no_whatsapp">Nomor WhatsApp <span class="text-muted">(boleh
                                    dikosongkan)</span></label>
                            <input type="tel" id="no_whatsapp" name="no_whatsapp"
                                class="form-control @error('no_whatsapp') is-invalid @enderror"
                                value="{{ old('no_whatsapp', $user->no_whatsapp) }}"
                                placeholder="Contoh: 0812-3456-7890" autocomplete="tel">
                            @error('no_whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="foto">Foto Profil <span class="text-muted">(boleh dikosongkan, maks 2
                                    MB)</span></label>

                            @if ($user->foto)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto {{ $user->name }}"
                                        style="width: 96px; height: 96px; object-fit: cover; border-radius: 50%;">
                                    <p class="small text-muted mb-0">Foto saat ini. Pilih file baru kalau mau
                                        menggantinya.</p>
                                    <div class="form-check mt-2" style="padding-left: 1.5em;">
                                        <input class="form-check-input" type="checkbox" id="hapus_foto"
                                            name="hapus_foto" value="1">
                                        <label class="form-check-label" for="hapus_foto"
                                            style="margin-left: 0;">Hapus foto ini</label>
                                    </div>
                                </div>
                            @endif

                            <input type="file" id="foto" name="foto" accept="image/*"
                                class="form-control @error('foto') is-invalid @enderror">
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================= Ganti password ================= --}}
            <div class="card card-rounded grid-margin">
                <div class="card-body">
                    <h4 class="card-title mb-4">Ganti Password</h4>
                    @include('partials.profil-password')
                </div>
            </div>

        </div>
    </div>

@endsection