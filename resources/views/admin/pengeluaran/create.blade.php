@extends('layouts.admin')

@section('title', 'Tambah Pengeluaran')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Tambah Pengeluaran</h3>
        <p class="text-muted mb-0">Catat pengeluaran warung.</p>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-rounded">
                <div class="card-body">
                    <form action="{{ route('admin.pengeluaran.store') }}" method="POST">
                        @csrf
                        @include('admin.pengeluaran._form')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection