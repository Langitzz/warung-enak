@extends('layouts.admin')

@section('title', 'Tambah Pengguna')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Tambah Pengguna</h3>
        <p class="text-muted mb-0">Buat akun baru untuk admin, kasir, atau pelanggan.</p>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-rounded">
                <div class="card-body">
                    <form action="{{ route('admin.pengguna.store') }}" method="POST">
                        @csrf
                        @include('admin.pengguna._form')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
