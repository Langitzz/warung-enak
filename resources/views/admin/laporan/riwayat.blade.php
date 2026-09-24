@extends('layouts.admin')

@section('title', 'Riwayat Pesanan')

@section('content')

    @php
        // Warna badge untuk tiap status (sama seperti halaman Data Pesanan)
        $warnaStatus = [
            'selesai' => 'badge-success',
            'dibatalkan' => 'badge-danger',
        ];
    @endphp

    {{-- ================= 3 Card ringkasan ================= --}}
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Riwayat</p>
                    <h3 class="fw-bold mb-0">{{ $jumlahSelesai + $jumlahDibatalkan }} pesanan</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Selesai</p>
                    <h3 class="fw-bold mb-0">{{ $jumlahSelesai }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Dibatalkan</p>
                    <h3 class="fw-bold mb-0">{{ $jumlahDibatalkan }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Tabel riwayat ================= --}}
    <div class="card card-rounded">
        <div class="card-body">

            <div class="mb-4">
                <h4 class="card-title mb-1">Riwayat Pesanan</h4>
                <p class="text-muted mb-0">Daftar pesanan yang sudah selesai atau dibatalkan.</p>
            </div>

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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayats as $riwayat)
                            <tr>
                                <td>{{ $loop->iteration + ($riwayats->currentPage() - 1) * $riwayats->perPage() }}</td>
                                <td class="fw-bold">{{ $riwayat->kode }}</td>
                                <td>{{ $riwayat->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                <td>
                                    {{ $riwayat->nama_pelanggan }}
                                    <div class="small text-muted">{{ $riwayat->no_whatsapp }}</div>
                                </td>
                                <td>{{ $riwayat->jumlah_porsi }}</td>
                                <td>Rp {{ number_format($riwayat->total, 0, ',', '.') }}</td>
                                <td>
                                    <span
                                        class="badge {{ $warnaStatus[$riwayat->status] ?? 'badge-secondary' }}">{{ $riwayat->label_status }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.pesanan.show', $riwayat) }}"
                                        class="btn btn-info btn-sm text-white" title="Detail" aria-label="Detail">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada riwayat pesanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $riwayats->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

@endsection
