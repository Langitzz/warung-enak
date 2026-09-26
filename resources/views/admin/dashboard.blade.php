@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

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

    {{-- ================= Judul halaman ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-1">Selamat Datang, Admin!</h3>
            <p class="text-muted mb-0">Pantau aktivitas {{ \App\Models\Pengaturan::namaWarung() }} hari ini.</p>
        </div>
        <div class="mt-3 mt-sm-0 text-muted">
            <i class="mdi mdi-calendar-today me-1"></i>
            {{ now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}
        </div>
    </div>

    {{-- ================= 4 Card statistik ================= --}}
    <div class="row">
        @foreach ($stats as $stat)
            <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
                <div class="card card-rounded">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1">{{ $stat['label'] }}</p>
                                <h3 class="fw-bold mb-0">{{ $stat['value'] }}</h3>
                            </div>
                            <div class="rounded-circle bg-{{ $stat['color'] }} text-white d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="mdi {{ $stat['icon'] }}" style="font-size: 24px;"></i>
                            </div>
                        </div>
                        <p class="small {{ $stat['note_color'] }} mt-3 mb-0">{{ $stat['note'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ================= Grafik penjualan ================= --}}
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <h4 class="card-title mb-0">Penjualan 7 Hari Terakhir</h4>
                    <p class="text-muted small mb-3">Total pendapatan per hari (pesanan selesai)</p>
                    <div style="position: relative; height: 300px;">
                        <canvas id="grafikPenjualan"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= Kolom kanan: Ringkasan + Quick Action ================= --}}
        <div class="col-lg-4 grid-margin">

            {{-- ----- Ringkasan Hari Ini ----- --}}
            <div class="card card-rounded mb-3">
                <div class="card-body">
                    <h4 class="card-title mb-3">Ringkasan Hari Ini</h4>
                    @foreach ($ringkasan as $item)
                        <div class="d-flex justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <span class="text-muted">{{ $item['label'] }}</span>
                            <span class="fw-bold">{{ $item['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ----- Quick Action ----- --}}
            {{-- Tambah Kategori masih mengarah ke daftar kategori, karena halaman tambah kategori belum dibuat --}}
            <div class="card card-rounded">
                <div class="card-body">
                    <h4 class="card-title mb-3">Quick Action</h4>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.menu.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Menu
                        </a>
                        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-clipboard-list-outline me-1"></i> Lihat Pesanan
                        </a>
                        <a href="{{ route('admin.kategori.index') }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-tag-outline me-1"></i> Tambah Kategori
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        {{-- Chart.js bawaan template --}}
        <script src="{{ asset('admin/assets/vendors/chart.js/chart.umd.js') }}"></script>
        <script>
            const grafikLabels = @json($grafikLabels);
            const grafikData = @json($grafikData);

            new Chart(document.getElementById('grafikPenjualan'), {
                type: 'bar',
                data: {
                    labels: grafikLabels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: grafikData,
                        backgroundColor: '#1F3BB3',
                        borderRadius: 6,
                        maxBarThickness: 40,
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

    {{-- ================= Pesanan Terbaru + Menu Terlaris ================= --}}
    <div class="row">

        {{-- ----- Pesanan Terbaru ----- --}}
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="card-title mb-0">Pesanan Terbaru</h4>
                        <a href="{{ route('admin.pesanan.index') }}" class="small">Lihat semua</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th>ID Pesanan</th>
                                    <th>Pelanggan</th>
                                    <th>Porsi</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pesananTerbaru as $pesanan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-bold">{{ $pesanan->kode }}</td>
                                        <td>{{ $pesanan->nama_pelanggan }}</td>
                                        <td>{{ $pesanan->jumlah_porsi }}</td>
                                        <td>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $warnaStatus[$pesanan->status] ?? 'badge-secondary' }}">{{ $pesanan->label_status }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pesanan.show', $pesanan) }}"
                                                class="btn btn-info btn-sm text-white" title="Detail"
                                                aria-label="Detail">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Belum ada pesanan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ----- Menu Terlaris ----- --}}
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <h4 class="card-title mb-3">Menu Terlaris</h4>
                    <p class="text-muted small mb-3">30 hari terakhir</p>

                    @forelse ($menuTerlaris as $item)
                        <div
                            class="d-flex align-items-center justify-content-between py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <p class="fw-bold mb-0">{{ $item->menu->nama }}</p>
                                <p class="small text-muted mb-0">
                                    {{ $item->menu->kategori->nama ?? '-' }} &bull; Rp
                                    {{ number_format($item->menu->harga, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-end">
                                <p class="fw-bold mb-0">{{ $item->total_terjual }}</p>
                                <p class="small text-muted mb-0">terjual</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Belum ada penjualan dalam 30 hari terakhir.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

@endsection