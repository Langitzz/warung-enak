@extends('layouts.admin')

@section('title', 'Profil Warung')

@section('content')

    @include('partials.admin-alert')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Profil Warung</h3>
        <p class="text-muted mb-0">Informasi dasar tentang warung kamu.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-rounded">
                <div class="card-body">

                    {{-- enctype="multipart/form-data" wajib supaya file logo bisa terkirim --}}
                    <form action="{{ route('admin.pengaturan.profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- Browser cuma bisa kirim GET/POST, jadi method PUT "dipinjam" lewat baris ini --}}
                        @method('PUT')

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="nama_warung" class="form-label">Nama Warung</label>
                            <input type="text" id="nama_warung" name="nama_warung"
                                class="form-control @error('nama_warung') is-invalid @enderror"
                                value="{{ old('nama_warung', $pengaturan->nama_warung) }}">
                            @error('nama_warung')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Telepon --}}
                        <div class="mb-3">
                            <label for="telepon" class="form-label">Nomor Telepon / WhatsApp <span
                                    class="text-muted">(boleh dikosongkan)</span></label>
                            <input type="text" id="telepon" name="telepon"
                                class="form-control @error('telepon') is-invalid @enderror"
                                value="{{ old('telepon', $pengaturan->telepon) }}" placeholder="Contoh: 0812-3456-7890">
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat <span class="text-muted">(boleh
                                    dikosongkan)</span></label>
                            <textarea id="alamat" name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror"
                                placeholder="Contoh: Jl. Contoh No. 12, Kota Contoh">{{ old('alamat', $pengaturan->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jam operasional --}}
                        {{-- Di database jam tersimpan "08:00:00", sedangkan input time butuh "08:00", jadi dipotong 5 karakter --}}
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="jam_buka" class="form-label">Jam Buka</label>
                                <input type="time" id="jam_buka" name="jam_buka"
                                    class="form-control @error('jam_buka') is-invalid @enderror"
                                    value="{{ old('jam_buka', $pengaturan->jam_buka ? substr($pengaturan->jam_buka, 0, 5) : '') }}">
                                @error('jam_buka')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="jam_tutup" class="form-label">Jam Tutup</label>
                                <input type="time" id="jam_tutup" name="jam_tutup"
                                    class="form-control @error('jam_tutup') is-invalid @enderror"
                                    value="{{ old('jam_tutup', $pengaturan->jam_tutup ? substr($pengaturan->jam_tutup, 0, 5) : '') }}">
                                @error('jam_tutup')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Singkat <span class="text-muted">(boleh
                                    dikosongkan)</span></label>
                            <textarea id="deskripsi" name="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror"
                                placeholder="Contoh: Warung makan sederhana dengan menu nasi goreng, mie, dan kwetiau.">{{ old('deskripsi', $pengaturan->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Logo --}}
                        <div class="mb-4">
                            <label for="logo" class="form-label">Logo Warung <span class="text-muted">(boleh
                                    dikosongkan, maks 2 MB)</span></label>

                            {{-- Tampilkan logo yang sekarang kalau ada --}}
                            @if ($pengaturan->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $pengaturan->logo) }}"
                                        alt="Logo {{ $pengaturan->nama_warung }}"
                                        style="width: 96px; height: 96px; object-fit: cover; border-radius: 8px;">
                                    <p class="small text-muted mb-0">Logo saat ini. Pilih file baru kalau mau menggantinya.
                                    </p>

                                    {{-- style inline di bawah untuk memperbaiki bentrok CSS template pada checkbox --}}
                                    <div class="form-check mt-2" style="padding-left: 1.5em;">
                                        <input class="form-check-input" type="checkbox" id="hapus_logo" name="hapus_logo"
                                            value="1">
                                        <label class="form-check-label" for="hapus_logo" style="margin-left: 0;">Hapus logo
                                            ini</label>
                                    </div>
                                    </p>
                                </div>
                            @endif

                            <input type="file" id="logo" name="logo" accept="image/*"
                                class="form-control @error('logo') is-invalid @enderror">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
