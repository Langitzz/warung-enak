@extends('layouts.admin')

@section('title', 'Monitoring Pesanan')

@section('content')

    @php
        $warnaStatus = [
            'menunggu' => 'badge-warning',
            'diproses' => 'badge-info',
            'siap' => 'badge-primary',
            'selesai' => 'badge-success',
            'dibatalkan' => 'badge-danger',
        ];
    @endphp

    <div class="card card-rounded">
        <div class="card-body">
            <h4 class="card-title mb-1">Monitoring Pesanan</h4>
            <p class="text-muted mb-4">Pantau seluruh pesanan (tampilan lihat saja).</p>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>ID Pesanan</th>
                            <th>Waktu</th>
                            <th>Pelanggan</th>
                            <th>Porsi</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesanans as $pesanan)
                            <tr>
                                <td>{{ $loop->iteration + ($pesanans->currentPage() - 1) * $pesanans->perPage() }}</td>
                                <td class="fw-bold">{{ $pesanan->kode }}</td>
                                <td>{{ $pesanan->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                <td>{{ $pesanan->nama_pelanggan }}</td>
                                <td>{{ $pesanan->jumlah_porsi }}</td>
                                <td>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $warnaStatus[$pesanan->status] ?? 'badge-secondary' }}">
                                        {{ $pesanan->label_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada pesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pesanans->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

@endsection