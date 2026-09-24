@extends('layouts.admin')

@section('title', 'Dashboard Chef')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Dashboard Dapur</h3>
        <p class="text-muted mb-0">Pesanan yang perlu dikerjakan hari ini.</p>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Pesanan Baru</p>
                    <h3 class="fw-bold mb-0">{{ $jumlah['menunggu'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Sedang Diproses</p>
                    <h3 class="fw-bold mb-0">{{ $jumlah['diproses'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Siap Disajikan</p>
                    <h3 class="fw-bold mb-0">{{ $jumlah['siap'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <p class="text-muted mb-1">Pesanan Selesai</p>
                    <h3 class="fw-bold mb-0">{{ $jumlah['selesai'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-rounded">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="card-title mb-0">Pesanan Terbaru</h4>
                <a href="{{ route('chef.pesanan.index') }}" class="small">Lihat semua</a>
            </div>

            @forelse ($pesananTerbaru as $pesanan)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">{{ $pesanan->kode }}</span>
                        <span class="text-muted small">{{ $pesanan->created_at->locale('id')->translatedFormat('H:i') }}</span>
                    </div>
                    <div class="small text-muted">{{ $pesanan->nama_pelanggan }}</div>
                    <div class="small">
                        @foreach ($pesanan->items as $item)
                            {{ $item->nama_menu }} &times;{{ $item->jumlah }}@if (!$loop->last), @endif
                        @endforeach
                    </div>
                    @if ($pesanan->catatan)
                        <div class="small fst-italic">Catatan: {{ $pesanan->catatan }}</div>
                    @endif
                </div>
            @empty
                <p class="text-muted mb-0">Tidak ada pesanan yang perlu dikerjakan saat ini.</p>
            @endforelse
        </div>
    </div>

@endsection