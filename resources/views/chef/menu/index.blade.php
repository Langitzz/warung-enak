@extends('layouts.admin')

@section('title', 'Daftar Menu')

@section('content')

    <div class="card card-rounded">
        <div class="card-body">
            <h4 class="card-title mb-1">Daftar Menu</h4>
            <p class="text-muted mb-4">Referensi menu yang bisa dipesan (hanya lihat).</p>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menus as $menu)
                            <tr>
                                <td class="fw-semibold">{{ $menu->nama }}</td>
                                <td>{{ $menu->kategori->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $menu->tersedia ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $menu->tersedia ? 'Tersedia' : 'Tidak Tersedia' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Belum ada menu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection