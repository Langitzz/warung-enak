@extends('layouts.admin')

@section('title', 'Edit Pengguna')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Edit Pengguna</h3>
        <p class="text-muted mb-0">Ubah data akun "{{ $pengguna->name }}".</p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-rounded">
                <div class="card-body">
                    <form action="{{ route('admin.pengguna.update', $pengguna) }}" method="POST">
                        @csrf
                        {{-- Browser cuma bisa kirim GET/POST, jadi method PUT "dipinjam" lewat baris ini --}}
                        @method('PUT')
                        @include('admin.pengguna._form')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
