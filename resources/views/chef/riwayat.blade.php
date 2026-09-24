@extends('layouts.admin')

@section('title', 'Riwayat Masakan')

@section('content')

    <div class="card card-rounded">
        <div class="card-body">
            <h4 class="card-title mb-1">Riwayat Masakan</h4>
            <p class="text-muted mb-4">
                Pesanan yang sudah selesai ditangani dapur.
                <span class="d-block small">(Belum ada pencatatan siapa yang mengerjakan tiap pesanan, jadi ini
                    riwayat dapur secara umum.)</span>
            </p>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Selesai</th>
                            <th>Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesanans as $pesanan)
                            <tr>
                                <td class="fw-semibold">{{ $pesanan->kode }}</td>
                                <td>{{ $pesanan->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                <td>{{ $pesanan->updated_at->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                <td class="small">
                                    @foreach ($pesanan->items as $item)
                                        {{ $item->nama_menu }} &times;{{ $item->jumlah }}@if (!$loop->last), @endif
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada pesanan yang selesai.</td>
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