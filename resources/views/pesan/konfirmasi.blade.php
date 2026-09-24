@extends('layouts.user')

@section('title', 'Konfirmasi Pesanan')

@section('content')

    @php
        $warnaStatus = [
            'menunggu' => 'text-bg-warning',
            'diproses' => 'text-bg-info',
            'siap' => 'text-bg-primary',
            'selesai' => 'text-bg-success',
            'dibatalkan' => 'text-bg-danger',
        ];
    @endphp

    <section class="section" style="padding-top: 120px;">
        @if (session('success'))
            <div class="container">
                <div class="alert alert-success">{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="container">
                <div class="alert alert-danger">{{ session('error') }}</div>
            </div>
        @endif

        <div class="container text-center mb-4" data-aos="fade-up">
            @if ($baruCheckout && $pesanan->status !== 'dibatalkan')
                <i class="bi bi-check-circle-fill display-3 text-success"></i>
                <h2 class="mt-3">Pesanan Berhasil Dibuat!</h2>
                <p class="text-muted">Simpan atau screenshot kode ini — kamu akan membutuhkannya kalau
                    menghubungi kami soal pesanan ini.</p>
            @else
                <i class="bi bi-receipt display-3 text-primary"></i>
                <h2 class="mt-3">Detail Pesanan</h2>
            @endif
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">{{ $pesanan->kode }}</h4>
                                <span class="badge {{ $warnaStatus[$pesanan->status] ?? 'badge-secondary' }}">
                                    {{ $pesanan->label_status }}
                                </span>
                            </div>
                            <p class="mb-1"><strong>Nama:</strong> {{ $pesanan->nama_pelanggan }}</p>
                            <p class="mb-3"><strong>WhatsApp:</strong> {{ $pesanan->no_whatsapp }}</p>

                            <ul class="list-unstyled mb-3">
                                @foreach ($pesanan->items as $item)
                                    <li class="d-flex justify-content-between mb-2">
                                        <span>{{ $item->nama_menu }} &times; {{ $item->jumlah }}</span>
                                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Pajak</span>
                                <span>Rp {{ number_format($pesanan->pajak, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Biaya Layanan</span>
                                <span>Rp {{ number_format($pesanan->biaya_layanan, 0, ',', '.') }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total</span>
                                <span>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @if (in_array($pesanan->status, ['menunggu', 'diproses'], true))
                        <div class="text-center mt-3">
                            <form action="{{ route('pesanan.batalkan', $pesanan->kode) }}" method="POST"
                                onsubmit="return confirm('Yakin mau membatalkan pesanan ini?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle"></i> Batalkan Pesanan
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="text-center mt-4">
                        <a href="{{ url('/') }}" class="btn-get-started">Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection