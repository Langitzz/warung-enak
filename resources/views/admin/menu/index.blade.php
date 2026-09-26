@extends('layouts.admin')

@section('title', 'Data Menu')

@section('content')

    @include('partials.admin-alert')

    <div class="card card-rounded">
        <div class="card-body">

            {{-- ================= Judul + tombol tambah ================= --}}
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="card-title mb-1">Data Menu</h4>
                    <p class="text-muted mb-0">Kelola daftar menu yang dijual di {{ \App\Models\Pengaturan::namaWarung() }}.</p>
                </div>
                <div class="mt-3 mt-sm-0">
                    <a href="{{ route('admin.menu.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Tambah Menu
                    </a>
                </div>
            </div>

            {{-- ================= Cari & filter ================= --}}
            <form action="{{ route('admin.menu.index') }}" method="GET" class="row g-2 mb-4">
                <div class="col-md-5">
                    <input type="text" name="cari" value="{{ request('cari') }}" class="form-control"
                        placeholder="Cari nama menu...">
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}"
                                {{ (string) request('kategori') === (string) $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia
                        </option>
                        <option value="tidak" {{ request('status') === 'tidak' ? 'selected' : '' }}>Tidak Tersedia
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="mdi mdi-magnify"></i> Cari
                    </button>
                    @if (request('cari') || request('kategori') || request('status'))
                        <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-secondary"
                            title="Reset filter">
                            <i class="mdi mdi-close"></i>
                        </a>
                    @endif
                </div>
            </form>

            {{-- ================= Tabel daftar menu ================= --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menus as $menu)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($menu->foto)
                                        <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama }}"
                                            style="width: 56px; height: 56px; object-fit: cover; border-radius: 8px;">
                                    @else
                                        {{-- Kalau belum ada foto, tampilkan kotak abu-abu berisi icon --}}
                                        <div class="bg-light text-muted d-flex align-items-center justify-content-center"
                                            style="width: 56px; height: 56px; border-radius: 8px;">
                                            <i class="mdi mdi-food" style="font-size: 24px;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $menu->nama }}</td>
                                <td>{{ $menu->kategori->nama ?? '-' }}</td>
                                <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td>
                                    @if ($menu->tersedia)
                                        <span class="badge badge-success">Tersedia</span>
                                    @else
                                        <span class="badge badge-danger">Tidak tersedia</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.menu.edit', $menu) }}" class="btn btn-warning btn-sm"
                                            title="Edit" aria-label="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>

                                        {{-- Hapus harus lewat form dengan method DELETE, bukan link biasa --}}
                                        <form action="{{ route('admin.menu.destroy', $menu) }}" method="POST"
                                            onsubmit="return confirm('Yakin mau menghapus menu ini?')">
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    @if (request('cari') || request('kategori') || request('status'))
                                        Tidak ada menu yang cocok dengan pencarian/filter ini.
                                    @else
                                        Belum ada menu. Klik "Tambah Menu" untuk mulai.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
