@extends('layouts.admin')

@section('title', 'Edit Pengeluaran')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Edit Pengeluaran</h3>
        <p class="text-muted mb-0">Ubah data pengeluaran "{{ $pengeluaran->keterangan }}".</p>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-rounded">
                <div class="card-body">
                    <form action="{{ route('admin.pengeluaran.update', $pengeluaran) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.pengeluaran._form')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection