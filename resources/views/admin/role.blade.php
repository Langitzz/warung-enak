@extends('layouts.admin')

@section('title', 'Role/Akses Pengguna')

@section('content')

    @php
        // Warna badge tiap role (pakai class bawaan template)
        $warnaRole = [
            'admin' => 'badge-primary',
            'kasir' => 'badge-info',
            'pelanggan' => 'badge-secondary',
        ];
    @endphp

    <div class="page-header">
        <h3 class="page-title">Role/Akses Pengguna</h3>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Pengguna</li>
                <li class="breadcrumb-item active">Role/Akses</li>
            </ol>
        </nav>
    </div>

    <div class="alert alert-info">
        Halaman ini hanya penjelasan. Role seorang pengguna diatur saat menambah atau mengedit akunnya di
        <a href="{{ route('admin.pengguna.index') }}" class="alert-link">Data Pengguna</a>.
        Hak akses berlabel <strong>Segera</strong> belum berfungsi karena fiturnya belum dibuat.
    </div>

    <div class="row">
        @foreach ($roles as $kode => $role)
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card card-rounded">
                    <div class="card-body">

                        {{-- Nama role + jumlah pengguna --}}
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h4 class="card-title mb-0">{{ $role['nama'] }}</h4>
                            <span class="badge {{ $warnaRole[$kode] ?? 'badge-secondary' }}">{{ $kode }}</span>
                        </div>

                        <p class="text-muted">{{ $role['deskripsi'] }}</p>

                        <div class="d-flex justify-content-between border-top border-bottom py-2 mb-3">
                            <span class="text-muted">Pengguna</span>
                            <span class="fw-bold">
                                {{ $jumlah[$kode] ?? 0 }}
                                <span class="text-muted fw-normal">({{ $jumlahAktif[$kode] ?? 0 }} aktif)</span>
                            </span>
                        </div>

                        {{-- Hak akses --}}
                        <p class="fw-bold mb-2">Hak akses</p>
                        <ul class="list-unstyled mb-3">
                            @foreach ($role['akses'] as $akses)
                                <li class="d-flex align-items-start mb-2">
                                    @if ($akses['siap'])
                                        <i class="mdi mdi-check-circle text-success me-2"></i>
                                        <span>{{ $akses['teks'] }}</span>
                                    @else
                                        <i class="mdi mdi-clock-outline text-muted me-2"></i>
                                        <span class="text-muted">
                                            {{ $akses['teks'] }}
                                            <span class="badge badge-warning ms-1">Segera</span>
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        {{-- Batasan --}}
                        @if (!empty($role['batasan']))
                            <p class="fw-bold mb-2">Batasan</p>
                            <ul class="list-unstyled mb-0">
                                @foreach ($role['batasan'] as $batasan)
                                    <li class="d-flex align-items-start mb-2">
                                        <i class="mdi mdi-lock-outline text-danger me-2"></i>
                                        <span>{{ $batasan }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection
