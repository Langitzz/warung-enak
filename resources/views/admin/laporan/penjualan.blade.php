@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')

    {{-- ================= Judul halaman + pilihan rentang ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-1">Laporan Penjualan</h3>
            <p class="text-muted mb-0">Ringkasan penjualan (pesanan yang sudah selesai).</p>
        </div>
        <div class="btn-group mt-3 mt-sm-0" role="group">
            <a href="{{ route('admin.laporan.penjualan', ['rentang' => 'minggu']) }}"
                class="btn btn-sm {{ $rentang === 'minggu' ? 'btn-primary' : 'btn-outline-primary' }}">Minggu</a>
            <a href="{{ route('admin.laporan.penjualan', ['rentang' => 'bulan']) }}"
                class="btn btn-sm {{ $rentang === 'bulan' ? 'btn-primary' : 'btn-outline-primary' }}">Bulan</a>
            <a href="{{ route('admin.laporan.penjualan', ['rentang' => 'tahun']) }}"
                class="btn btn-sm {{ $rentang === 'tahun' ? 'btn-primary' : 'btn-outline-primary' }}">Tahun</a>
        </div>
    </div>

    {{-- ================= 3 Card ringkasan ================= --}}
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pendapatan</p>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pesanan</p>
                    <h3 class="fw-bold mb-0">{{ $totalPesanan }} pesanan</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Rata-rata per Pesanan</p>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($rataRata, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Pengeluaran & Laba Bersih ================= --}}
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pengeluaran</p>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                    <a href="{{ route('admin.pengeluaran.index') }}" class="small">Kelola Pengeluaran &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Laba Bersih</p>
                    <h3 class="fw-bold mb-0 {{ $labaBersih >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($labaBersih, 0, ',', '.') }}
                    </h3>
                    <p class="small text-muted mb-0">Pendapatan &minus; Pengeluaran, 7 hari terakhir</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Grafik ================= --}}
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <h4 class="card-title mb-3">Pendapatan per {{ $rentang === 'tahun' ? 'Bulan' : 'Hari' }}</h4>
                    <div style="position: relative; height: 300px;">
                        <canvas id="grafikLaporan"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= Tabel rincian per hari ================= --}}
    <div class="card card-rounded">
        <div class="card-body">
            <h4 class="card-title mb-3">Rincian per {{ $rentang === 'tahun' ? 'Bulan' : 'Hari' }}</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>{{ $rentang === 'tahun' ? 'Bulan' : 'Tanggal' }}</th>
                            <th>Jumlah Pesanan</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualanPerPeriode as $periode)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $periode['label_panjang'] }}</td>
                                <td>{{ $periode['pesanan'] }}</td>
                                <td>Rp {{ number_format($periode['pendapatan'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Chart.js bawaan template --}}
    <script src="{{ asset('admin/assets/vendors/chart.js/chart.umd.js') }}"></script>
    <script>
        const labels = @json(collect($penjualanPerPeriode)->pluck('label_singkat'));
        const data = @json(collect($penjualanPerPeriode)->pluck('pendapatan'));

        new Chart(document.getElementById('grafikLaporan'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: data,
                    borderColor: '#1F3BB3',
                    backgroundColor: 'rgba(31, 59, 179, 0.1)',
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (item) => 'Rp ' + item.raw.toLocaleString('id-ID'),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Rp ' + (value / 1000) + 'rb',
                        },
                    },
                },
            },
        });
    </script>
@endpush
