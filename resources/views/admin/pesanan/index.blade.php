@extends('layouts.admin')

@section('title', 'Data Pesanan')

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
    @endphp

    {{-- ================= Ringkasan jumlah per status (dihitung asli dari database) ================= --}}
    <div class="row">
        @foreach ($statusList as $kode => $label)
            <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
                <div class="card card-rounded">
                    <div class="card-body">
                        <p class="text-muted mb-1">{{ $label }}</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="fw-bold mb-0">{{ $hitungStatus[$kode] ?? 0 }}</h3>
                            <span class="badge {{ $warnaStatus[$kode] }}">pesanan</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ================= Tabel pesanan ================= --}}
    <div class="card card-rounded">
        <div class="card-body">

            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="card-title mb-1">Data Pesanan</h4>
                    <p class="text-muted mb-0">Pantau dan kelola semua pesanan yang masuk.</p>
                </div>
                <div class="mt-3 mt-sm-0">
                    <a href="{{ route('admin.pesanan.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Tambah Pesanan
                    </a>
                </div>
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
                        @forelse ($pesanans as $pesanan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $pesanan->kode }}</td>
                                <td>{{ $pesanan->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                <td>
                                    {{ $pesanan->nama_pelanggan }}
                                    <div class="small text-muted">{{ $pesanan->no_whatsapp }}</div>
                                </td>
                                <td>{{ $pesanan->jumlah_porsi }}</td>
                                <td>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                <td>
                                    <span
                                        class="badge {{ $warnaStatus[$pesanan->status] ?? 'badge-secondary' }}">{{ $pesanan->label_status }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.pesanan.show', $pesanan) }}"
                                            class="btn btn-info btn-sm text-white" title="Detail" aria-label="Detail">
                                            <i class="mdi mdi-eye"></i>
                                        </a>

                                        {{-- Hapus harus lewat form dengan method DELETE, bukan link biasa --}}
                                        <form action="{{ route('admin.pesanan.destroy', $pesanan) }}" method="POST"
                                            onsubmit="return confirm('Yakin mau menghapus pesanan {{ $pesanan->kode }}?')">
                                            @csrf
                                            @method('DELETE')

                                            {{-- Hanya pesanan yang dibatalkan yang boleh dihapus (controller juga menolaknya) --}}
                                            @if ($pesanan->bolehDihapus())
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                    aria-label="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            @else
                                                <span title="Hanya pesanan yang dibatalkan yang bisa dihapus">
                                                    <button type="button" class="btn btn-danger btn-sm text-white" disabled
                                                        aria-label="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </span>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada pesanan. Klik "Tambah Pesanan" untuk membuat pesanan manual.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
