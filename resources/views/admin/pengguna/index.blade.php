@extends('layouts.admin')

@section('title', 'Data Pengguna')

@section('content')

    @include('partials.admin-alert')

    @php
        // Warna badge tiap role (pakai class bawaan template)
        $warnaRole = [
            'admin' => 'badge-primary',
            'kasir' => 'badge-info',
            'pelanggan' => 'badge-secondary',
        ];
    @endphp

    <div class="card card-rounded">
        <div class="card-body">

            {{-- ================= Judul + tombol tambah ================= --}}
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="card-title mb-1">Data Pengguna</h4>
                    <p class="text-muted mb-0">Kelola akun admin, kasir, dan pelanggan.</p>
                </div>
                <div class="mt-3 mt-sm-0">
                    <a href="{{ route('admin.pengguna.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Tambah Pengguna
                    </a>
                </div>
            </div>

            {{-- ================= Tabel pengguna ================= --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Pengguna</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penggunas as $pengguna)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{-- Foto profil; kalau belum ada, tampilkan huruf pertama nama --}}
                                        @if ($pengguna->foto)
                                            <img src="{{ asset('storage/' . $pengguna->foto) }}"
                                                alt="Foto {{ $pengguna->name }}" class="rounded-circle me-2"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary text-white d-flex align-items-center justify-content-center rounded-circle me-2 fw-semibold"
                                                style="width: 40px; height: 40px;">
                                                {{ strtoupper(mb_substr($pengguna->name, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div>
                                            <span class="fw-bold">{{ $pengguna->name }}</span>
                                            @if ($pengguna->is(auth()->user()))
                                                <span class="badge badge-outline-primary ms-1">Kamu</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $pengguna->email }}</td>
                                <td>{{ $pengguna->no_whatsapp ?: '-' }}</td>
                                <td>
                                    <span
                                        class="badge {{ $warnaRole[$pengguna->role] ?? 'badge-secondary' }}">{{ ucfirst($pengguna->role) }}</span>
                                </td>
                                <td>
                                    @if ($pengguna->aktif)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.pengguna.edit', $pengguna) }}"
                                            class="btn btn-warning btn-sm" title="Edit" aria-label="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>

                                        {{-- Hapus harus lewat form dengan method DELETE, bukan link biasa --}}
                                        <form action="{{ route('admin.pengguna.destroy', $pengguna) }}" method="POST"
                                            onsubmit="return confirm('Yakin mau menghapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')

                                            {{-- Akun sendiri tidak boleh dihapus (controller juga menolaknya) --}}
                                            @if ($pengguna->is(auth()->user()))
                                                <span title="Kamu tidak bisa menghapus akunmu sendiri">
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada pengguna.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
