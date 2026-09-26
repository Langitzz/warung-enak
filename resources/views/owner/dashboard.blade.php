@extends('layouts.admin')

@section('title', 'Dashboard Owner')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Performa {{ \App\Models\Pengaturan::namaWarung() }}</h3>
        <p class="text-muted mb-0">Bagaimana performa bisnis sejauh ini?</p>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Pendapatan Hari Ini</p>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Pendapatan Bulan Ini</p>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Transaksi</p>
                    <h3 class="fw-bold mb-0">{{ $totalTransaksi }}</h3>
                    <p class="small text-muted mb-0">Sepanjang waktu</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pesanan Selesai</p>
                    <h3 class="fw-bold mb-0">{{ $totalSelesai }}</h3>
                    <p class="small text-muted mb-0">Sepanjang waktu</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <h4 class="card-title mb-3">Pendapatan 7 Hari Terakhir</h4>
                    <div style="position: relative; height: 280px;">
                        <canvas id="grafikOwner"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <h4 class="card-title mb-3">Menu Terlaris</h4>
                    <p class="text-muted small mb-3">30 hari terakhir</p>
                    @forelse ($menuTerlaris as $item)
                        <div class="d-flex justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <span>{{ $item->menu->nama ?? 'Menu dihapus' }}</span>
                            <span class="fw-bold">{{ $item->total_terjual }}</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Belum ada penjualan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('admin/assets/vendors/chart.js/chart.umd.js') }}"></script>
    <script>
        new Chart(document.getElementById('grafikOwner'), {
            type: 'bar',
            data: {
                labels: @json($grafikLabels),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($grafikData),
                    backgroundColor: '#4d55c4',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } },
            },
        });
    </script>
@endpush