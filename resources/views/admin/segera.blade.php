@extends('layouts.admin')

@section('title', $judul)

@section('content')

    {{-- HALAMAN PENGGANTI: dipakai semua menu sidebar yang fiturnya belum dibuat.
       BELUM ADA BACKEND: hapus route pengganti dan buat halaman aslinya kalau fiturnya sudah dikerjakan.
       Variabel $judul dikirim dari route (lihat routes/web.php). --}}

    <div class="row">
        <div class="col-12">
            <div class="card card-rounded">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-wrench text-muted" style="font-size: 64px;"></i>
                    <h3 class="fw-bold mt-3">{{ $judul }}</h3>
                    <p class="text-muted mb-0">Halaman ini belum tersedia. Fiturnya akan dibuat nanti.</p>
                </div>
            </div>
        </div>
    </div>

@endsection
