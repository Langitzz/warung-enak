@extends('layouts.admin')

@section('title', 'Data Pengeluaran')

@section('content')

    @include('partials.admin-alert')

    <div class="card card-rounded">
        <div class="card-body">

            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="card-title mb-1">Data Pengeluaran</h4>
                    <p class="text-muted mb-0">Catatan belanja dan biaya operasional warung.</p>
                </div>
                <div class="mt-3 mt-sm-0">
                    <a href="{{ route('admin.pengeluaran.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Tambah Pengeluaran
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengeluarans as $pengeluaran)
                            <tr>
                                <td>{{ $loop->iteration + ($pengeluarans->currentPage() - 1) * $pengeluarans->perPage() }}</td>
                                <td>{{ $pengeluaran->tanggal->locale('id')->translatedFormat('d M Y') }}</td>
                                <td>{{ $pengeluaran->keterangan }}</td>
                                <td>Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.pengeluaran.edit', $pengeluaran) }}"
                                            class="btn btn-warning btn-sm" title="Edit" aria-label="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.pengeluaran.destroy', $pengeluaran) }}"
                                            method="POST" onsubmit="return confirm('Yakin mau menghapus pengeluaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                aria-label="Hapus">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada pengeluaran. Klik "Tambah Pengeluaran" untuk mulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pengeluarans->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

@endsection