@extends('layouts.user')

@section('title', 'Cek Pesanan')

@section('content')

    <section class="section" style="padding-top: 140px;">
        <div class="container section-title" data-aos="fade-up">
            <h2>Cek Pesanan</h2>
            <p>Kehilangan halaman konfirmasi? Cek lagi di sini</p>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form action="{{ route('pesanan.cek.proses') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Kode Pesanan</label>
                                    <input type="text" name="kode" class="form-control" value="{{ old('kode') }}"
                                        placeholder="Contoh: PSN-0008" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Nomor WhatsApp</label>
                                    <input type="text" name="no_whatsapp" class="form-control"
                                        value="{{ old('no_whatsapp') }}" placeholder="Nomor yang dipakai saat checkout"
                                        required>
                                </div>
                                <button type="submit" class="btn-get-started border-0 w-100">Cek Pesanan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
