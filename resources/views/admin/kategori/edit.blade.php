@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Edit Kategori</h3>
        <p class="text-muted mb-0">Ubah data kategori "{{ $kategori->nama }}".</p>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-rounded">
                <div class="card-body">
                    <form action="{{ route('admin.kategori.update', $kategori) }}" method="POST">
                        @csrf
                        {{-- Browser cuma bisa kirim GET/POST, jadi method PUT "dipinjam" lewat baris ini --}}
                        @method('PUT')
                        @include('admin.kategori._form')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
