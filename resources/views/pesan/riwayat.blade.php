@extends('layouts.user')

@section('title', 'Pesanan Saya')

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
        <div class="container section-title" data-aos="fade-up">
            <h2>Pesanan Saya</h2>
            <p>Riwayat pesanan yang pernah kamu buat</p>
        </div>

        <div class="container">
            @if ($pesanans->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">Kamu belum pernah membuat pesanan.</p>
                    <a href="{{ route('menu.index') }}" class="btn-get-started">Lihat Menu</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Waktu</th>
                                <th>Porsi</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pesanans as $pesanan)
                                <tr>
                                    <td class="fw-semibold">{{ $pesanan->kode }}</td>
                                    <td>{{ $pesanan->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                    <td>{{ $pesanan->jumlah_porsi }}</td>
                                    <td>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $warnaStatus[$pesanan->status] ?? 'badge-secondary' }}">
                                            {{ $pesanan->label_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('pesanan.konfirmasi', $pesanan->kode) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $pesanans->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>

@endsection
