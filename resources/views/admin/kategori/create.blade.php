@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Tambah Kategori</h3>
        <p class="text-muted mb-0">Buat kategori baru untuk mengelompokkan menu.</p>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-rounded">
                <div class="card-body">
                    <form action="{{ route('admin.kategori.store') }}" method="POST">
                        @csrf
                        @include('admin.kategori._form')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
