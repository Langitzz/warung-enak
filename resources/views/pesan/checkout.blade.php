@extends('layouts.user')

@section('title', 'Checkout')

@section('content')

    <section class="section" style="padding-top: 120px;">
        <div class="container section-title" data-aos="fade-up">
            <h2>Checkout</h2>
            <p>Lengkapi data buat menyelesaikan pesanan</p>
        </div>

        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row gy-4">
                <div class="col-lg-7">
                    <form action="{{ route('checkout.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama_pelanggan" class="form-control"
                                value="{{ old('nama_pelanggan', auth()->user()->name ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor WhatsApp</label>
                            <input type="text" name="no_whatsapp" class="form-control"
                                value="{{ old('no_whatsapp', auth()->user()->no_whatsapp ?? '') }}"
                                placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
                        </div>
                        <button type="submit" class="btn-get-started border-0 w-100">
                            Buat Pesanan
                        </button>
                    </form>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Ringkasan Pesanan</h5>
                            <ul class="list-unstyled mb-3">
                                @foreach ($items as $item)
                                    <li class="d-flex justify-content-between mb-2">
                                        <span>{{ $item['menu']->nama }} &times; {{ $item['jumlah'] }}</span>
                                        <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Pajak</span>
                                <span>Rp {{ number_format($pajak, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Biaya Layanan</span>
                                <span>Rp {{ number_format($biayaLayanan, 0, ',', '.') }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total</span>
                                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('keranjang.index') }}" class="d-block text-center mt-3">
                        <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
