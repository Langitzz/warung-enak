@extends('layouts.admin')

@section('title', 'Performa Menu')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Performa Menu</h3>
        <p class="text-muted mb-0">30 hari terakhir, dari pesanan yang sudah selesai.</p>
    </div>

    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <h4 class="card-title mb-3">Menu Paling Laris</h4>
                    @forelse ($terlaris as $menu)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <p class="mb-0 fw-semibold">{{ $menu->nama }}</p>
                                <p class="small text-muted mb-0">{{ $menu->kategori->nama ?? '-' }}</p>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 fw-bold">{{ $menu->terjual }} terjual</p>
                                <p class="small text-muted mb-0">Rp {{ number_format($menu->pendapatan, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card card-rounded">
                <div class="card-body">
                    <h4 class="card-title mb-3">Menu Paling Sedikit Dipesan</h4>
                    @forelse ($kurangLaris as $menu)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <p class="mb-0 fw-semibold">{{ $menu->nama }}</p>
                                <p class="small text-muted mb-0">{{ $menu->kategori->nama ?? '-' }}</p>
                            </div>
                            <p class="mb-0 fw-bold">{{ $menu->terjual }} terjual</p>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card card-rounded">
        <div class="card-body">
            <h4 class="card-title mb-3">Kategori Paling Diminati</h4>
            @forelse ($kategoriFavorit as $nama => $terjual)
                <div class="d-flex justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <span>{{ $nama }}</span>
                    <span class="fw-bold">{{ $terjual }} porsi</span>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada data.</p>
            @endforelse
        </div>
    </div>

@endsection