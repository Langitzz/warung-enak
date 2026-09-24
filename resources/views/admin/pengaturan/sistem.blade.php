@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')

    @include('partials.admin-alert')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Pengaturan Sistem</h3>
        <p class="text-muted mb-0">Atur cara kerja sistem Warung Enak.</p>
    </div>

    <div class="alert alert-info">
        Pengaturan di halaman ini sudah tersimpan, tapi baru berpengaruh setelah fitur pesanan dan kasir dibuat.
    </div>

    <div class="row">
        <div class="col-lg-8">

            <form action="{{ route('admin.pengaturan.sistem.update') }}" method="POST">
                @csrf
                {{-- Browser cuma bisa kirim GET/POST, jadi method PUT "dipinjam" lewat baris ini --}}
                @method('PUT')

                {{-- ----- Umum ----- --}}
                <div class="card card-rounded mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Umum</h4>

                        <div class="mb-3">
                            <label for="zona_waktu" class="form-label">Zona Waktu</label>
                            <select id="zona_waktu" name="zona_waktu"
                                class="form-select @error('zona_waktu') is-invalid @enderror">
                                @foreach ($zonaWaktu as $kode => $nama)
                                    <option value="{{ $kode }}" @selected(old('zona_waktu', $pengaturan->zona_waktu) === $kode)>{{ $nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('zona_waktu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="mata_uang" class="form-label">Mata Uang</label>
                            {{-- Dikunci di Rupiah: tidak dikirim dari form dan tidak diproses controller --}}
                            <input type="text" id="mata_uang" class="form-control" value="Rupiah (Rp)" disabled>
                            <div class="form-text">Untuk sekarang mata uang tetap Rupiah.</div>
                        </div>
                    </div>
                </div>

                {{-- ----- Transaksi ----- --}}
                <div class="card card-rounded mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Transaksi</h4>

                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="pajak" class="form-label">Pajak (%)</label>
                                <input type="number" id="pajak" name="pajak" min="0" max="100"
                                    step="1" class="form-control @error('pajak') is-invalid @enderror"
                                    value="{{ old('pajak', $pengaturan->pajak) }}">
                                @error('pajak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="biaya_layanan" class="form-label">Biaya Layanan (%)</label>
                                <input type="number" id="biaya_layanan" name="biaya_layanan" min="0" max="100"
                                    step="1" class="form-control @error('biaya_layanan') is-invalid @enderror"
                                    value="{{ old('biaya_layanan', $pengaturan->biaya_layanan) }}">
                                @error('biaya_layanan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ----- Pesanan ----- --}}
                <div class="card card-rounded mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Pesanan</h4>

                        {{-- Input tersembunyi "0" memastikan nilainya tetap terkirim saat switch dimatikan.
                 style inline di bawah untuk memperbaiki bentrok CSS template pada switch. --}}
                        <input type="hidden" name="terima_pesanan" value="0">
                        <div class="form-check form-switch mb-3" style="padding-left: 2.5em;">
                            <input class="form-check-input" type="checkbox" role="switch" id="terima_pesanan"
                                name="terima_pesanan" value="1" @checked((bool) old('terima_pesanan', $pengaturan->terima_pesanan))>
                            <label class="form-check-label" for="terima_pesanan" style="margin-left: 0;">Terima
                                pesanan</label>
                        </div>

                        <input type="hidden" name="notif_pesanan" value="0">
                        <div class="form-check form-switch mb-0" style="padding-left: 2.5em;">
                            <input class="form-check-input" type="checkbox" role="switch" id="notif_pesanan"
                                name="notif_pesanan" value="1" @checked((bool) old('notif_pesanan', $pengaturan->notif_pesanan))>
                            <label class="form-check-label" for="notif_pesanan" style="margin-left: 0;">Notifikasi pesanan
                                baru</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>

            </form>

        </div>
    </div>

@endsection
