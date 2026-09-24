@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $pesanan->kode)

@section('content')

    @include('partials.admin-alert')

    @php
        // Warna badge untuk tiap status (pakai class bawaan template)
        $warnaStatus = [
            'menunggu' => 'badge-warning',
            'diproses' => 'badge-info',
            'siap' => 'badge-primary',
            'selesai' => 'badge-success',
            'dibatalkan' => 'badge-danger',
        ];

        // Tombol untuk tiap perpindahan status: [teks tombol, class tombol]
        $tombolStatus = [
            'diproses' => ['Proses pesanan', 'btn-info text-white'],
            'siap' => ['Tandai siap', 'btn-primary'],
            'selesai' => ['Tandai selesai', 'btn-success'],
            'dibatalkan' => ['Batalkan pesanan', 'btn-danger'],
        ];

        // Tautan WhatsApp: nomor dibersihkan dari spasi dan tanda, awalan 0 diganti 62
        $nomorWa = preg_replace('/\D+/', '', $pesanan->no_whatsapp);
        if (str_starts_with($nomorWa, '0')) {
            $nomorWa = '62' . substr($nomorWa, 1);
        }
    @endphp

    <div class="page-header">
        <h3 class="page-title">
            Pesanan {{ $pesanan->kode }}
            <span
                class="badge {{ $warnaStatus[$pesanan->status] ?? 'badge-secondary' }} ms-2">{{ $pesanan->label_status }}</span>
        </h3>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.pesanan.index') }}">Data Pesanan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>

    <div class="row">

        {{-- ================= Isi pesanan ================= --}}
        <div class="col-lg-8 grid-margin">
            <div class="card card-rounded">
                <div class="card-body">
                    <h4 class="card-title mb-4">Isi Pesanan</h4>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th>Menu</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pesanan->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $item->nama_menu }}</span>
                                            @if ($item->menu_id === null)
                                                <div class="small text-muted">Menu ini sudah dihapus dari daftar menu</div>
                                            @endif
                                        </td>
                                        <td class="text-end">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $item->jumlah }}</td>
                                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Rincian total --}}
                    <div class="ms-auto mt-3" style="max-width: 320px;">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Subtotal</span>
                            <span>Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Pajak</span>
                            <span>Rp {{ number_format($pesanan->pajak, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Biaya layanan</span>
                            <span>Rp {{ number_format($pesanan->biaya_layanan, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mt-1 fw-bold fs-5">
                            <span>Total</span>
                            <span>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-4">

            {{-- ================= Info pelanggan ================= --}}
            <div class="card card-rounded grid-margin">
                <div class="card-body">
                    <h4 class="card-title mb-3">Info Pelanggan</h4>

                    <p class="mb-2">
                        <strong>Nama :</strong><br>
                        {{ $pesanan->nama_pelanggan }}
                    </p>

                    <p class="mb-2">
                        <strong>WhatsApp :</strong><br>
                        {{ $pesanan->no_whatsapp }}
                        <a href="https://wa.me/{{ $nomorWa }}" target="_blank" rel="noopener"
                            class="btn btn-success btn-sm text-white ms-1">
                            <i class="mdi mdi-whatsapp"></i> Hubungi
                        </a>
                    </p>

                    <p class="mb-2">
                        <strong>Pemesan :</strong><br>
                        @if ($pesanan->user)
                            Akun {{ $pesanan->user->name }}
                        @else
                            Tamu (tanpa akun)
                        @endif
                    </p>

                    <p class="mb-2">
                        <strong>Waktu pesan :</strong><br>
                        {{ $pesanan->created_at->locale('id')->translatedFormat('l, d F Y, H:i') }} WIB
                    </p>

                    <p class="mb-0">
                        <strong>Catatan :</strong><br>
                        {{ $pesanan->catatan ?: '-' }}
                    </p>
                </div>
            </div>

            {{-- ================= Ubah status ================= --}}
            <div class="card card-rounded grid-margin">
                <div class="card-body">
                    <h4 class="card-title mb-3">Ubah Status</h4>

                    @forelse ($pesanan->statusBerikutnya() as $statusBaru)
                        <form action="{{ route('admin.pesanan.status', $pesanan) }}" method="POST" class="mb-2"
                            @if ($statusBaru === 'dibatalkan') onsubmit="return confirm('Yakin mau membatalkan pesanan {{ $pesanan->kode }}?')" @endif>
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $statusBaru }}">
                            <div class="d-grid">
                                <button type="submit" class="btn {{ $tombolStatus[$statusBaru][1] }}">
                                    {{ $tombolStatus[$statusBaru][0] }}
                                </button>
                            </div>
                        </form>
                    @empty
                        <p class="text-muted mb-0">
                            Pesanan ini sudah {{ strtolower($pesanan->label_status) }}, jadi statusnya tidak bisa diubah
                            lagi.
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- ================= Tombol bawah ================= --}}
            <div class="d-flex gap-2">
                <a href="{{ route('admin.pesanan.index') }}" class="btn btn-light">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>

                {{-- Hapus hanya untuk pesanan yang dibatalkan (controller juga menolaknya) --}}
                @if ($pesanan->bolehDihapus())
                    <form action="{{ route('admin.pesanan.destroy', $pesanan) }}" method="POST"
                        onsubmit="return confirm('Yakin mau menghapus pesanan {{ $pesanan->kode }}? Ini tidak bisa dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="mdi mdi-delete"></i> Hapus Pesanan
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>

@endsection
