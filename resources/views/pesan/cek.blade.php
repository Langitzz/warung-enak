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
                            <form action="{{ route('pesanan.cek.proses') }}" method="POST" id="formCekPesanan">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Kode Pesanan</label>
                                    <input type="text" name="kode" class="form-control" value="{{ old('kode') }}"
                                        placeholder="Contoh: PSN-0008" required autocomplete="off">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Nomor WhatsApp</label>
                                    <input type="text" name="no_whatsapp" class="form-control"
                                        value="{{ old('no_whatsapp') }}" placeholder="Nomor yang dipakai saat checkout"
                                        required autocomplete="tel">
                                </div>
                                <button type="submit" class="btn-get-started border-0 w-100" id="tombolCekPesanan">Cek
                                    Pesanan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (function() {
                const form = document.getElementById('formCekPesanan');
                const tombol = document.getElementById('tombolCekPesanan');
                const teksAsli = tombol.innerHTML;

                form.addEventListener('submit', function() {
                    tombol.disabled = true;
                    tombol.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
                });

                window.addEventListener('pageshow', function(e) {
                    if (e.persisted) {
                        tombol.disabled = false;
                        tombol.innerHTML = teksAsli;
                    }
                });
            })();
        </script>
    @endpush
@endsection
