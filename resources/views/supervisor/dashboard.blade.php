@extends('layouts.admin')

@section('title', 'Dashboard Supervisor')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Dashboard Operasional</h3>
        <p class="text-muted mb-0">Bagaimana kondisi Warung Enak hari ini?</p>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Pesanan Hari Ini</p>
                    <h3 class="fw-bold mb-0">{{ $pesananHariIni }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Sedang Diproses</p>
                    <h3 class="fw-bold mb-0">{{ $pesananDiproses }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Pesanan Selesai</p>
                    <h3 class="fw-bold mb-0">{{ $pesananSelesai }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Pendapatan Hari Ini</p>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-rounded">
        <div class="card-body">
            <h4 class="card-title mb-3">Status Operasional</h4>
            <div class="row text-center">
                <div class="col-3">
                    <p class="text-muted small mb-1">Baru Masuk</p>
                    <h4 class="fw-bold">{{ $statusOperasional['menunggu'] }}</h4>
                </div>
                <div class="col-3">
                    <p class="text-muted small mb-1">Sedang Dibuat</p>
                    <h4 class="fw-bold">{{ $statusOperasional['diproses'] }}</h4>
                </div>
                <div class="col-3">
                    <p class="text-muted small mb-1">Siap</p>
                    <h4 class="fw-bold">{{ $statusOperasional['siap'] }}</h4>
                </div>
                <div class="col-3">
                    <p class="text-muted small mb-1">Selesai</p>
                    <h4 class="fw-bold">{{ $statusOperasional['selesai'] }}</h4>
                </div>
            </div>
            <p class="text-muted small mt-3 mb-0">
                Ringkasan aktivitas per Kasir/Chef belum bisa ditampilkan — sistem belum mencatat siapa yang
                menangani tiap pesanan.
            </p>
        </div>
    </div>

@endsection