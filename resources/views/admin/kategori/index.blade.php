@extends('layouts.admin')

@section('title', 'Kategori Menu')

@section('content')

    @include('partials.admin-alert')

    <div class="card card-rounded">
        <div class="card-body">

            {{-- ================= Judul + tombol tambah ================= --}}
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="card-title mb-1">Kategori Menu</h4>
                    <p class="text-muted mb-0">Kelompokkan menu berdasarkan jenisnya.</p>
                </div>
                <div class="mt-3 mt-sm-0">
                    <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Tambah Kategori
                    </a>
                </div>
            </div>

            {{-- ================= Cari & filter ================= --}}
            <form action="{{ route('admin.kategori.index') }}" method="GET" class="row g-2 mb-4">
                <div class="col-md-6">
                    <input type="text" name="cari" value="{{ request('cari') }}" class="form-control"
                        placeholder="Cari nama kategori...">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="mdi mdi-magnify"></i> Cari
                    </button>
                    @if (request('cari') || request('status'))
                        <a href="{{ route('admin.kategori.index') }}" class="btn btn-outline-secondary"
                            title="Reset filter">
                            <i class="mdi mdi-close"></i>
                        </a>
                    @endif
                </div>
            </form>

            {{-- ================= Tabel kategori ================= --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Kategori</th>
                            <th>Jumlah Menu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategoris as $kategori)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $kategori->nama }}</td>
                                <td>{{ $kategori->menus_count }} menu</td>
                                <td>
                                    @if ($kategori->aktif)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-warning">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.kategori.edit', $kategori) }}"
                                            class="btn btn-warning btn-sm" title="Edit" aria-label="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>

                                        {{-- Hapus harus lewat form dengan method DELETE, bukan link biasa --}}
                                        <form action="{{ route('admin.kategori.destroy', $kategori) }}" method="POST"
                                            onsubmit="return confirm('Yakin mau menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')

                                            {{-- Kategori yang masih dipakai menu tidak bisa dihapus (controller juga menolaknya) --}}
                                            @if ($kategori->menus_count > 0)
                                                {{-- Dibungkus span supaya tooltip tetap muncul walau tombolnya disabled --}}
                                                <span title="Masih dipakai oleh {{ $kategori->menus_count }} menu">
                                                    <button type="button" class="btn btn-danger btn-sm text-white" disabled
                                                        aria-label="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </span>
                                            @else
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                    aria-label="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada kategori. Klik "Tambah Kategori" untuk mulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
