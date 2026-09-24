@extends('layouts.user')

@section('title', 'Sedang Tutup')

@section('content')

    @php
        $nomorWa = preg_replace('/\D+/', '', $pengaturan->telepon ?? '');
        if (str_starts_with($nomorWa, '0')) {
            $nomorWa = '62' . substr($nomorWa, 1);
        }
    @endphp

    <section class="section" style="padding-top: 160px;">
        <div class="container text-center">
            <i class="bi bi-door-closed display-1 text-muted"></i>
            <h2 class="mt-3">Sedang Tidak Menerima Pesanan Online</h2>
            <p class="text-muted">
                {{ $pengaturan->nama_warung }} sementara belum menerima pesanan lewat website.
                Silakan coba lagi nanti, atau hubungi kami langsung.
            </p>
            @if ($nomorWa)
                <a href="https://wa.me/{{ $nomorWa }}" target="_blank" rel="noopener" class="btn-get-started">
                    <i class="bi bi-whatsapp"></i> Hubungi via WhatsApp
                </a>
            @endif
            <div class="mt-3">
                <a href="{{ url('/') }}">Kembali ke Beranda</a>
            </div>
        </div>
    </section>

@endsection
