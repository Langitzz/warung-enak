@extends('layouts.admin')

@section('title', 'Laporan Bisnis')

@push('styles')
    <style>
        @media print {

            .sidebar,
            .navbar,
            .footer,
            #tombolCetak {
                display: none !important;
            }

            .main-panel {
                margin: 0 !important;
                width: 100% !important;
            }
        }
    </style>
@endpush

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-1">Laporan Bisnis</h3>
            <p class="text-muted mb-0">Ringkasan performa Warung Enak sepanjang waktu.</p>
        </div>
        <button type="button" id="tombolCetak" class="btn btn-outline-primary mt-3 mt-sm-0"
            onclick="window.print()">
            <i class="mdi mdi-printer"></i> Cetak Laporan
        </button>
    </div>

    <div class="row">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pendapatan</p>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Transaksi</p>
                    <h3 class="fw-bold mb-0">{{ $totalTransaksi }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Pesanan Selesai</p>
                    <h3 class="fw-bold mb-0">{{ $totalSelesai }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Jumlah Menu</p>
                    <h3 class="fw-bold mb-0">{{ $jumlahMenu }}</h3>
                    <p class="small text-muted mb-0">{{ $menuTersedia }} tersedia</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-rounded">
        <div class="card-body">
            <h4 class="card-title mb-3">Menu Terlaris Sepanjang Waktu</h4>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Menu</th>
                        <th>Total Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menuTerlaris as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->menu->nama ?? 'Menu dihapus' }}</td>
                            <td>{{ $item->total_terjual }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection